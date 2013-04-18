function isUsrname(elem, helperMsg){ //1
	var uInput = elem.value;
	var alphaExp = /^[0-9a-zA-Z]+$/;
	if(!elem.value.match(/^\s*$/) && elem.value.match(alphaExp) && uInput.length > 3 && uInput.length < 31 ){
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}
}
function isName(elem, helperMsg){ //3,4
	var uInput = elem.value; //^[\S]*$ //^\s*$
	var alphaExp = /^[0-9a-zA-Z\S-]+$/;
	if(!elem.value.match(/^\s*$/) && uInput.length > 1 && uInput.length < 51 ){
		elem.setStyle('border-color', '#0097e9');
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}
}
function isMobile(elem, helperMsg){ //6
	var validMobile = /^[0-9\S()-]+$/;
	if(!elem.value.match(/^\s*$/) && elem.value.match(validMobile) && elem.value.length>9 && elem.value.length<16){
		elem.setStyle('border-color', '#0097e9');
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}
}
function isLandline(elem, helperMsg){ //7
	var validLandline = /^[0-9 ()-]+$/;
	if(!elem.value.match(/^\s*$/) && elem.value.match(validLandline)&&elem.value.length>7&&elem.value.length<16){
		elem.setStyle('border-color', '#0097e9');
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}
}
function emailValidator(elem, helperMsg){ //8
	var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	var uInput = elem.value;
	if(elem.value.match(emailExp) && uInput.length < 101){
		elem.setStyle('border-color', '#0097e9');
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}
}
function tbregochkPassword(elem, helperMsg){ //11
	var validPass = /^(?!.*(.)\1{3})((?=.*[\d])(?=.*[A-Za-z])|(?=.*[^\w\d\s])(?=.*[A-Za-z])).{8,30}$/;
	if(elem.value.match(validPass)){
		elem.setStyle('border-color', '#0097e9');
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}
}
function isPcode(elem, helperMsg){ //17
	var numericExpression = /^[0-9]+$/;
	var uInput = elem.value;
	if(elem.value.match(numericExpression) && uInput.length > 3 && uInput.length < 5){
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		return false;
	}
}
function tbregochkPromo(elem, min, max, helperMsg){ //18
	var uInput = elem.value;
	var alphaExp = /^[0-9a-zA-Z]+$/;
	if(elem.value.match(alphaExp) && uInput.length > min && uInput.length < max){
		return true;
	}else if(!uInput.length){ //allow NULL as not required
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		return false;
	}
}
function tbregochkDetails(elem, length, helperMsg){ //20
	var uInput = elem.value;
	var alphaExp = /^[0-9a-zA-Z\W]+$/;
	if(elem.value.match(alphaExp) && uInput.length == length || uInput.length < length){
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		return false;
	}
}
function isAddress(elem, min, length, helperMsg){ //13,14
	var uInput = elem.value;
	var alphaExp = /^[0-9a-zA-Z\W]+$/;
	if(!elem.value.match(/^\s*$/) && elem.value.match(alphaExp) && uInput.length > min && uInput.length < length){
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		return false;
	}
}


/* ####################################### */
function notDifferent(elem, elem2chk, helperMsg){ //9,12
	if(elem.value != elem2chk.value){
		$('formMsg').setText(helperMsg);
		elem.focus(); // set the focus to this input
		elem.setStyle('border-color', '#B50311');
		return false;
	}
	elem.setStyle('border-color', '#0097e9');
	return true;
}
function notEmpty(elem, helperMsg){ //2,5a-c,13,14,15
	if(elem.value.length == 0){
		$('formMsg').setText(helperMsg);
		elem.focus(); // set the focus to this input
		elem.setStyle('border-color', '#B50311');
		return false;
	}
	elem.setStyle('border-color', '#0097e9');
	return true;
}
function isChecked(elem, outerElem, helperMsg){ //23,24
	if(elem.checked){
		outerElem.setStyle('border-color', '#0097e9');
		outerElem.setStyle('background', 'none');
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		outerElem.setStyle('border-color', '#B50311');
		outerElem.setStyle('background', '#FAEFF1');
		return false;
	}
}



/* ####################################### */





function isNumeric(elem, helperMsg){
	var numericExpression = /^[0-9]+$/;
	if(elem.value.match(numericExpression)){
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		return false;
	}
}



function isPassword(elem, helperMsg){
	var validPass = /^\S[\S ]{2,98}\S$/;
	if(elem.value.match(validPass)){
		elem.setStyle('border-color', '#0097e9');
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}
}

function isAlphabet(elem, helperMsg){
	var alphaExp = /^[a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		return false;
	}
}

function isAlphanumeric(elem, helperMsg){
	var alphaExp = /^[0-9a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}else{
		$('formMsg').setText(helperMsg);
		elem.focus();
		return false;
	}
}

function lengthRestriction(elem, min, max){
	var uInput = elem.value;
	if(uInput.length >= min && uInput.length <= max){
		return true;
	}else{
		alert("Please enter between " +min+ " and " +max+ " characters");
		elem.focus();
		return false;
	}
}

function madeSelection(elem, helperMsg){
	if(elem.value == "Please Choose"){
		$('formMsg').setText(helperMsg);
		elem.focus();
		elem.setStyle('border-color', '#B50311');
		return false;
	}else{
		elem.setStyle('border-color', '#0097e9');
		return true;
	}
}




