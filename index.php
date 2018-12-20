<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PHP Round 1</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="author" content="Narin Siritaranukul">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">	
    <link rel="stylesheet" href="css/styles.css?v=1.0">
    <script type="text/javascript" src="js/adyen.js"></script>
    <script type="text/javascript" src="js/payment_handler.js"></script>
</head>

<body>
    <form id="form_main" name="form_main">
		<table>
				<tr>
					<td colspan="2"><legend>Card Details</legend></td></tr>
					<tr>
						<td><label for="price">Price</label></td>
						<td><input type="text" id="price" value="11" size="20" autocomplete="off" name="price" validate="number" /><div id="errprice" class="errortext">*</div>
							<select id="currency" name="currency" validate="populated">
								<option value="">-- Select --</option>
								<option value="USD">USD</option>
								<option value="EUR">EUR</option>
								<option value="THB">THB</option>
								<option value="HKD">HKD</option>
								<option value="SGD">SGD</option>
								<option value="AUD">AUD</option>
							</select><div id="errcurrency" class="errortext">*</div>			
						</td>
					</tr>
					<tr>
						<td><label for="customer-name">Customer Name</label></td>
						<td><input type="text" id="customer-name" value="Jane Doe" size="20" autocomplete="off" name="customerName" validate="populated" /><div id="errcustomerName" class="errortext">*</div></td>
					</tr>
                    <tr>
						<td><label for="card-type">Card type</label></td>
						<td><select id="card-type" name="card-type" validate="populated">
								<option value="">-- Select --</option>
								<option value="AMEX">AMEX</option>
								<option value="VISA">VISA</option>
								<option value="MASTER">MASTER</option>
								<option value="JCB">JCB</option>
								<option value="Discovery">Discovery</option>
							</select><div id="errcard-type" class="errortext">*</div></td>
					</tr>
					<tr>
						<td><label for="form-number">Card Number</label></td>
						<td><input type="text" id="form-number" value="5555444433331111" size="20" autocomplete="off" name="number" validate="number" /><div id="errnumber" class="errortext">*</div></td>
					</tr>
                    <tr>
                    	<td><label for="form-holder-name">Card Holder Name</label></td>
                    	<td><input type="text" id="form-holder-name" value="John Doe" size="20" autocomplete="off" name="holderName" validate="populated" /><div id="errholderName" class="errortext">*</div></td>
                    </tr>
					<tr>
						<td><label for="form-cvc">CVC</label></td>
						<td><input type="text" id="form-cvc" value="737" size="4" autocomplete="off" name="cvc" validate="number" /><div id="errcvc" class="errortext">*</div></td>
					</tr>
					<tr>
						<td><label for="form-expiry-month">Expiration Month (MM / YYYY)</label></td>
						<td><select id="form-expiry-month" name="expiryMonth" validate="populated">
                                <option value="">--</option>
                                <?php for($i=1;$i<=12;$i++) {?>
                                    <option value="<?php echo str_pad($i,2,'0',STR_PAD_LEFT);?>"><?php echo str_pad($i,2,'0',STR_PAD_LEFT);?></option>
                                <?php }?>
                            </select><div id="errexpiryMonth" class="errortext">*</div> /
							<select id="form-expiry-year" name="expiryYear" validate="populated">
                                 <option value="">----</option>
                                <?php for($i=intval(date('Y'));$i<=(intval(date('Y'))+10);$i++) {?>
                                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                <?php }?>
                            </select><div id="errexpiryYear" class="errortext">*</div></td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="button" value="Create payment" onclick="payment_handler();" /></td>
					</tr>
		</table>
        </form>
 		<form method="POST" action="payment-handler.php" id="paymentform">
			
				<input type="hidden" id="encrypted-form-number" 		value="" data-encrypted-name="number" />
				<input type="hidden" id="encrypted-form-holder-name" 	value="" data-encrypted-name="holderName" />
				<input type="hidden" id="encrypted-form-cvc" 			value="" data-encrypted-name="cvc" />
				<input type="hidden" id="encrypted-form-expiry-month" value="" data-encrypted-name="expiryMonth" />
				<input type="hidden" id="encrypted-form-expiry-year" 	value="" data-encrypted-name="expiryYear" />	
				<input type="hidden" id="encrypted-form-expiry-generationtime" value="<?php date_default_timezone_set('Europe/Amsterdam'); echo date("c")?>" data-encrypted-name="generationtime" />
				<input type="hidden" id="encrypted-form-gateway" 		value="" name="gateway" />
				<input type="hidden" id="encrypted-customer-name" 	value="" name="customer-name" />
                <input type="hidden" id="encrypted-card-type" 		value="" name="card-type" />
				<input type="hidden" id="encrypted-price" 			value="" name="price" />
				<input type="hidden" id="encrypted-currency" 			value="" name="currency" />
                <input type="hidden" id="encrypted-for-db-number" 	value="" name="for-db-number" />
                <input type="hidden" id="encrypted-for-db-holderName" 		value="" name="for-db-holderName" />
				<input type="hidden" id="encrypted-for-db-cvc" 			value="" name="for-db-cvc" />
				<input type="hidden" id="encrypted-for-db-expiryMonth" 	value="" name="for-db-expiryMonth" />
                <input type="hidden" id="encrypted-for-db-expiryYear" 	value="" name="for-db-expiryYear" />
				<input type="submit" id="paymentsubmit" value="Create Payment" style="display:none;" />
			
		</form>
		
		<script type="text/javascript">
			function payment_handler() {
               // Validate the form
                if(validate(document.getElementById('form_main')) == false) {
                   return false;
                }
				// Based on the card type and currecy, get which gateway are we using 
                var gateway = gateway_decision(document.getElementById('card-type').value,document.getElementById('currency').value);
                if(gateway === false) {
                    return alert("We only accept AMEX in USD");
                }
                if(gateway == 'adyen'){ // Use Adyen
                    return adyen_payment_submission();
                }
                // User Braintree
				return braintree_payment_submission();
			}

			

            function hiddenFormAssignments() {
                // Assign value to adyen form //

                document.getElementById("encrypted-form-number").value = document.getElementById("form-number").value;
                document.getElementById("encrypted-form-holder-name").value = document.getElementById("form-holder-name").value;
                document.getElementById("encrypted-form-cvc").value = document.getElementById("form-cvc").value;
                document.getElementById("encrypted-form-expiry-month").value = document.getElementById("form-expiry-month").value;
                document.getElementById("encrypted-form-expiry-year").value = document.getElementById("form-expiry-year").value; 
                /* Non-encripted data */
                document.getElementById("encrypted-card-type").value = document.getElementById("card-type").value; 
                document.getElementById("encrypted-customer-name").value = document.getElementById("customer-name").value; 
                document.getElementById("encrypted-price").value = document.getElementById("price").value; 
                document.getElementById("encrypted-currency").value = document.getElementById("currency").value; 
                document.getElementById("encrypted-for-db-number").value = document.getElementById("form-number").value; 
                document.getElementById("encrypted-for-db-holderName").value = document.getElementById("form-holder-name").value; 
                document.getElementById("encrypted-for-db-cvc").value = document.getElementById("form-cvc").value; 
                document.getElementById("encrypted-for-db-expiryMonth").value = document.getElementById("form-expiry-month").value;
                document.getElementById("encrypted-for-db-expiryYear").value = document.getElementById("form-expiry-year").value;
            }
            
			function adyen_payment_submission() {
                hiddenFormAssignments();
                document.getElementById("encrypted-form-gateway").value = "adyen";
                
                var thisForm    = document.getElementById('paymentform');
                thisForm = adyen_encript_form(thisForm);

                document.getElementById('paymentsubmit').click();
            }
			
            function braintree_payment_submission() {
                hiddenFormAssignments();
                document.getElementById("encrypted-form-gateway").value = "braintree";
                
                document.getElementById('paymentsubmit').click();
            }
		</script>	
</body>
</html>