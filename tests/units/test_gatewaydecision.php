<!DOCTYPE HTML>
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Test Gateway Decision</title>
    </head>
<body><div id="main_panel">&nbsp;</div>
<script type="text/javascript" src="../../js/payment_handler.js"></script>
<script type="text/javascript">
	
	function setMainPanel(strText) {
		document.getElementById('main_panel').innerHTML = strText;
	}

	function testGateway() {
	//document.getElementById('main_panel').innerHTML = 	'Result for AMEX card with USD currency = '+resultAMEXUSD+'<br/>'+'Result for AMEX card with EUR currency = '+resultAMEXEUR+'<br/>'+'Result for VISA card with SGD currency = '+resultAMEXEUR+'<br/>'+'Result for MASTER card with AUD currency = '+resultAMEXEUR+'<br/>';
		var resultAMEXUSD = gateway_decision('AMEX','USD');
    	var resultAMEXEUR = gateway_decision('AMEX','EUR');		
    	var resultVISASGD = gateway_decision('VISA','SGD');
    	var resultMASTERAUD = gateway_decision('MASTER','AUD');
		setMainPanel('Result for AMEX card with USD currency = '+resultAMEXUSD+'<br/>'+'Result for AMEX card with EUR currency = '+resultAMEXEUR+'<br/>'+'Result for VISA card with SGD currency = '+resultVISASGD+'<br/>'+'Result for MASTER card with AUD currency = '+resultMASTERAUD+'<br/>');
	}
	testGateway();
</script>
</body>
</html>