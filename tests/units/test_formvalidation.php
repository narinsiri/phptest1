<!--  Test the form validation function -->
<link rel="stylesheet" href="../../css/styles.css?v=1.0">
<script type="text/javascript" src="../../js/payment_handler.js"></script>
<form action="" id="form_test_1" onsubmit="return false">
<table>
	<tr><td>Populated Text Box</td><td> <input type="text" id="inputText" name="inputText" validate="populated" value="Test Text" /><div id="errinputText" class="errortext">*</div></td></tr>
	<tr><td>Number Text Box </td><td><input type="text" id="inputNumber" name="inputNumber" validate="number" value="1234" /><div id="errinputNumber" class="errortext">*</div></td></tr>
	<tr><td>Populated Dropdown</td><td><select id="inputDropdown" name="inputDropdown" validate="populated">
					<option value="">--</option>
					<option value="1">1</option>
					<option value="2">2</option>
					</select><div id="errinputDropdown" class="errortext">*</div> </td></tr>
	<tr><td colspan="2"><input type="button" onclick="if(validate(document.getElementById('form_test_1')) !== false) alert('Success');" value="test" />	</td></tr>
</table>			
</form>