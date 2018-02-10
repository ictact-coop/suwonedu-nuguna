function checkAll() {
		
	var obj = document.getElementsByName('module_code[]');
	
	for(var i=0; i<obj.length; i++) {
		if(obj[i].checked == true)obj[i].checked = false;
		else obj[i].checked = true;
	}

}

function check_copy(f) {
	var obj = document.getElementsByName('module_code[]');
	var flag = 0;
	for(var i=0; i<obj.length; i++) {
		if(obj[i].checked == true) {
			flag = 1;
			break;
		}	
	}

	if(!flag) {
		alert(js_lang_copy_choice);
		return false;
	}

}

function completeCopySet(ret_obj) {
	
    var msg_code = ret_obj['message'];

}