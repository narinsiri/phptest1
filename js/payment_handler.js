function gateway_decision(strCardType, strCurrency) {
		var adyenCurrency = new Array("USD", "EUR", "AUD"); 			// Currency for Adyen
        
        if((strCardType == "AMEX") && (strCurrency != "USD")) {			// Not accept AMEX if it is not for USD
            return false;
        }
        if((strCardType == "AMEX")
           || (adyenCurrency.indexOf(strCurrency) != -1)) {
            return "adyen";
        }
		return "braintree";
}

function adyen_encript_form(objForm) {
	var resultForm = objForm;
	var public_key = "10001|C2A67A55E3782ED252EDD32B0825057FC15742E3A0AADA2F269"
		 +"842D3DD1B6D31EA8E9A865B2DEB914EDC060992788A5F1882CA2AECBB"
		 +"9288CDC03A9228079C859F00E7FA95144044CAA9BB318454A56D2094C"
		 +"6E39B46602CDF5C1E7E1D7427167F2428ED2DA713E61587F4633599B2"
		 +"B77597794B0AF2E0176A19F94025E62D3A7D744A0EB0D41750D59F7B2"
		 +"66476583A463B8D72B4E7A835764B45C0EB7BA205DE5370C24DB39549"
		 +"4B4C83A23539DDBE64ACA3389A161D3F304258E5E4930738CD04DBAD9"
		 +"1FD501A547300C0F7D926A22B6183E28A6519B4E5DC9493789C7CAEFC"
		 +"3F64D9905FFE389C9B08E6B46FDF510EBCF9E73C75FB5B8EEDDD0D50F6B795";
	adyen.encrypt.createEncryptedForm( resultForm, public_key, {});
	return resultForm;
}

function validate(formObj)
{
    var booErrorFound = false;
    
    for (var i = 0; i<formObj.elements.length; i++)
    {
    	var ele=formObj.elements[i];
    	if(ele.getAttribute("validate"))
    	{
    	    var arrValidation = validationType = ele.getAttribute("validate").split(",");
    		validationMethod = arrValidation[0];
    		validationParam1 = arrValidation[1];
    		validationParam2 = arrValidation[2];						
    		    
    		switch (validationMethod)
    		{
                case "date":
                    arrDateValue = ele.value.split('-');
					var month = parseInt(arrDateValue[1],10).toString();
                    if(ele.value=='' || arrDateValue[0]=='YYYY' || arrDateValue[0]=='0000' || arrDateValue[0]==''
                       || arrDateValue[1]=='MM' || arrDateValue[1]=='00' || arrDateValue[1]==''  || month=="NaN"
                       || arrDateValue[2]=='DD' || arrDateValue[2]=='00' || arrDateValue[2]=='') 
                    { 
                        booErrorFound = true; 
                        if (document.getElementById('err'+ele.getAttribute("name"))) { 
                            document.getElementById('err'+ele.getAttribute("name")).style.display='block';
                        }
                    } else if (document.getElementById('err'+ele.getAttribute("name"))) {
					    document.getElementById('err'+ele.getAttribute("name")).style.display='none';
					}
                    break;
                
                case "equals":
					//alert("Come to equal");
                    if (validationParam1 == "element")
                    {
                        var ele2 = formObj[validationParam2];
                        if (ele.value != ele2.value)
                        {
							//alert("Not equal: "+ele.value+' <> '+ele2.value);
                            ele.className="textboxerror";
                            booErrorFound = true;
							if (document.getElementById('err'+ele.getAttribute("name"))) { 
                         	   document.getElementById('err'+ele.getAttribute("name")).style.display='block';
                        	}
                        } else {
							if (document.getElementById('err'+ele.getAttribute("name"))) { 
								document.getElementById('err'+ele.getAttribute("name")).style.display='none';
							}
						}
                    }
                    else if (validationParam1 == "value")
                    {
                        if (ele.value != validationParam2)
                        {
                            ele.className="textboxerror";
                            booErrorFound = true;
                        }
                    }
                    break;
                case "length":
                    if (ele.value.length<validationParam1 || ele.value.length>validationParam2)
                    {
                        ele.className="textboxerror";
                        booErrorFound = true;
                    }
                    break;
                case "mobile":
					//alert("Validate Mobile");
					if (!isValidMobile(ele.value)) 
					{
                        ele.className="textboxerror";
                        booErrorFound = true;
                        if (document.getElementById('err'+ele.getAttribute("name"))) { 
                            document.getElementById('err'+ele.getAttribute("name")).style.display='block' 
                        }
                    } else if (document.getElementById('err'+ele.getAttribute("name"))) {
					    document.getElementById('err'+ele.getAttribute("name")).style.display='none'
					}	
					break;

				case "populated":
					//alert(ele.getAttribute("name")+"/Length = "+ele.value.length);
				    if (ele.value.length<1 || ele.value==' ' || ele.value=='  ' || ele.value=='   ')
					{
						ele.className="textboxerror";
						if (document.getElementById('err'+ele.getAttribute("name"))) {document.getElementById('err'+ele.getAttribute("name")).style.display='block'}
						booErrorFound = true;
					}
					else {
					    if (typeof validationParam1 != "undefined") {
					        if (charactersCheck(ele.value,validationParam1)) {
					        }
					        else {
					            booErrorFound = true;
					        }
					    }
					    ele.className="";
					    if (document.getElementById('err'+ele.getAttribute("name"))) {document.getElementById('err'+ele.getAttribute("name")).style.display='none'}
					}
					break; 
					
				case "number":
					//alert(ele.getAttribute("name")+"/Length = "+ele.value.length);
				    if (ele.value.length<1 || ele.value==' ' || ele.value=='  ' || ele.value=='   ' || !isNumeric(ele.value))
					{
						ele.className="textboxerror";
						if (document.getElementById('err'+ele.getAttribute("name"))) {document.getElementById('err'+ele.getAttribute("name")).style.display='block'}
						booErrorFound = true;
					}
					else {
					    if (typeof validationParam1 != "undefined") {
					        if (charactersCheck(ele.value,validationParam1)) {
					        }
					        else {
					            booErrorFound = true;
					        }
					    }
					    ele.className="";
					    if (document.getElementById('err'+ele.getAttribute("name"))) {document.getElementById('err'+ele.getAttribute("name")).style.display='none'}
					}
					break; 	
					
                case "radio":
                    var radioSelected = -1;
                    var radioGroup    = ele.name;
                    var radioButtons  = formObj.elements[radioGroup];
                    for (j=0;j<radioButtons.length; j++) {
                        if (radioButtons[j].checked) {
                            radioSelected = j;
                        }
                    }
                    if (radioSelected == -1) {
                        ele.className="textboxerror";
                        booErrorFound = true;
                        if (document.getElementById('err'+ele.getAttribute("name"))) { 
                            document.getElementById('err'+ele.getAttribute("name")).style.display='block' 
                        }
                    } else if (document.getElementById('err'+ele.getAttribute("name"))) {
					    document.getElementById('err'+ele.getAttribute("name")).style.display='none'
					}
                    break; 
    		}
    		
        }
    }
    
    if (booErrorFound == true)
    {
        alert("You have not filled in all required fields correctly.");
        return false;
    }
    else {
        return true;
    }
}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}