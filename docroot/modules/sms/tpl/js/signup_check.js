var userId_check_sort = 'off';

function send_check(f) {
	if(!f.pcsfield) {
			alert(lang_sms_add_pcs);
			// FIXME
			location.href = './?module=admin&act=dispMemberAdminInsertJoinForm';
		return false;
	}

	if(userId_check_sort == 'off') {
		alert(js_userId_check_msg);
		f.user_id.focus();

		return false;
	}

	if(f.serviceInfo.checked == false) {
		alert(js_sms_service_agree);
		f.serviceInfo.focus();

		return false;
	}
	if(f.userInfo.checked == false) {
		alert(js_sms_user_agree);
		f.userInfo.focus();

		return false;
	}


	procFilter(f,send_perbiz);

	return false;
}

function userIdCheck() {
	var f = document.perbiz_member;
	if(f.user_id.value) {
		// 여기서 아이디 중복체크히든박스를 아웃시킨다
		userId_check_sort = 'off';
		procFilter(f,check_id);
	}
}

function completeCheckId(ret_obj) {
	var r_obj = ret_obj['message'].evalJSON();
	var code     = r_obj.code;

	if(code == 'f273') {
		var msg = r_obj.msg;
		alert(msg);
		document.perbiz_member.user_id.vaule = '';
	} else {
		userId_check_sort = 'on';
	}

}

function completeSendperbiz(ret_obj) {

	var r_obj = ret_obj['message'].evalJSON();
	if(r_obj.auth_key)var auth_key = r_obj.auth_key;
	var code     = r_obj.code;
	var msg      = r_obj.msg;

	alert(msg);

	if(code == 'f237') {
		$('sms_input_layer').hide();
		$('sms_authkey_layer').show();
		$('authkeytext').innerText = auth_key;
	}
}


var statis = {
	search: function(date) {
		var f = document.adminSearch;
		f.mode.value = date;
		f.submit();
	},
	search_year: function(date,check) {
		var f = document.adminSearch;

		if(check == 'pre') var val = parseInt(date)-1;
		else if(check == 'next') var val = parseInt(date)+1;
		f.choice_year.value = val;
		f.submit();
	},
	search_month: function(date,check) {
		var f = document.adminSearch;

		if(check == 'pre') var val = parseInt(date)-1;
		else if(check == 'next') var val = parseInt(date)+1;
		f.choice_month.value = val;
		f.submit();
	},
	search_day: function(date,check) {
		var f = document.adminSearch;

		if(check == 'pre') var val = parseInt(date)-1;
		else if(check == 'next') var val = parseInt(date)+1;
		f.choice_day.value = val;
		f.submit();
	}
}


function completeInsertSmsAddDomain(ret_obj) {

	var data =  ret_obj['message'].evalJSON();

	if(data.code == 'f247') {
		alert(data.msg);
		var url = current_url;
		location.href = url;
	} else alert(data.msg);

}


function completeSmsSetting(ret_obj) {
	alert(lang_set_ok_msg);
	 var url = current_url;
	 location.href = url;
}

function sms_domain_check(f) {
	if(!f.domain.value) {
		alert(lang_sms_domain_require);
		f.domain.focus();

		return false;
	}

	var pattern = /^[_a-zA-Z가-힝0-9-]+\.[a-zA-Z가-힝0-9-\.]+[a-zA-Z]+$/;

	if(!pattern.test(f.domain.value)) {
		alert(lang_sms_domain_wrong);
		f.domain.focus();

		return false;
	}

	procFilter(f,insert_add_domain);

	return false;
}

function completeReauthkey(ret_obj) {
	var data =  ret_obj['message'].evalJSON();

	if(data.code == 'succ') {
		alert(data.msg);
		$('auth_key_text').innerText = data.auth_key;

	} else alert(data.msg);


}
function reauthkey_send(f) {
	procFilter(f,submit_reauthKey);
}




var infoView = {
	view: function(page) {
		this.width   = 970;
		this.height  = 550;
		this.maxHeight = 900;

		this.win = new Window({className: "alphacube", title: "",
					  top:150, left:30, width:this.width, height:this.height,
					  url:'http://sms.perbiz.co.kr/tpls/'+page+'.utf8.html', showEffectOptions: {duration:0.1}})
		this.win.show();

		$('frameBoderLayer').style.overflow = 'auto';
		$('frameBoderLayer').style.height   = '500px';
		$('frameBoderLayer').style.width   = '950px';
		$(this.win.frameId).style.width = '930px';

		if(page == 'privacy') {
			$(this.win.frameId).style.height = '7200px';
			this.privacy_check = 'yes';
		} else {
			$(this.win.frameId).style.height = '10600px';
			this.agreement_check = 'yes';
		}
	},
	privacyCheck: function() {
		if(this.privacy_check != 'yes') {
			alert(js_member_sms_user_info+js_member_sms_tail_text);
			$('userInfo').checked = false;
			$('privacyButton').focus();

			return;
		}
	},
	agreementCheck: function() {
		if(this.agreement_check != 'yes') {
			alert(js_member_sms_service+js_member_sms_tail_text);
			$('agreementButton').focus();
			$('serviceInfo').checked = false;
			return;
		}
	},
	getWinSize: function() {
		if(this.win)return this.win.getSize();
		else return false;
	},
	winResize: function(inum) {
		var winSize = this.getWinSize();
		if(winSize){
			var bigHeight = parseInt(this.height) + (inum * 25) + parseInt(40);
			if(winSize.height <= this.height)this.win.setSize(this.width,bigHeight,0.3);
		}
	},

	winRecover: function() {
		var winSize = this.getWinSize();
		if(winSize.height > this.height)this.win.setSize(this.width,this.height,0.3);

	}

}


function completeSmsLogin(ret_obj) {
	var data =  ret_obj['message'].evalJSON();

	if(data.code == '777') {
		alert(data.msg);
		var url = current_url;
		location.href = url;
	} else alert(data.msg);

}

function completeCommonSetup(ret_obj) {
	alert(ret_obj['message']);
}


function checkCommonSetup(fo) {
	var $form = jQuery(fo);
	var join_sms_admin = jQuery('input[name=join_sms_admin]:checked', $form).val();
	var join_sms_member = jQuery('input[name=join_sms_member]:checked', $form).val();

	if(join_sms_admin == 'Y') {
		var join_sms_admin_text = jQuery('input[name=join_sms_admin_text]', $form).val();
		if(!join_sms_admin_text) {
			alert('메시지 입력해주세요.');// FIXME
			jQuery('input[name=join_sms_admin_text]', $form).focus();
		}
	}
	if(join_sms_member == 'Y') {
		var join_sms_member_text = jQuery('input[name=join_sms_member_text]', $form).val();
		if(!join_sms_member_text) {
			alert('메시지 입력해주세요.');// FIXME
			jQuery('input[name=join_sms_member_text]', $form).focus();
		}
	}

	return procFilter(fo, common_setup);
}


jQuery(function($) {

	var $common_setup = $('#common_setup');
	var $admin_tr = $('#insertmember_sms_admin');
	var $member_tr = $('#insertmember_sms_member');
	var $head_tr = $('#insertmember_sms_head');

	$('input[name=join_sms_admin]', $common_setup).click(function() {
		var chk = $(this).is(':checked');

		if(chk == true) {
			$admin_tr.show();
			$head_tr.show();
		} else {
			$admin_tr.hide();
			if(!$('input[name=join_sms_member]', $common_setup).is(':checked')) $head_tr.hide();
		}
	});

	$('input[name=join_sms_member]', $common_setup).click(function() {
		var chk = $(this).is(':checked');
		if(chk == true) {
			$member_tr.show();
			$head_tr.show();
		} else {
			$member_tr.hide();
			if(!$('input[name=join_sms_admin]', $common_setup).is(':checked')) $head_tr.hide();
		}
	});

	$('input[name=join_head_use]', $common_setup).click(function() {
		var $target_admin = $('input[name=join_sms_admin_text]', $common_setup);
		var $target_member = $('input[name=join_sms_member_text]', $common_setup);

		var a_text = $target_admin.val().replace(site_domain, '');
		var m_text = $target_member.val().replace(site_domain, '');
		$target_admin.val(a_text);
		$target_member.val(m_text);

		var chk = new RegExp("/^"+site_domain+"/g");
		if(this.value == 'Y' && !$target_admin.val().match(chk)) {
			var a_text = site_domain + $target_admin.val();
			var m_text = site_domain + $target_member.val();
			$target_admin.val(a_text);
			$target_member.val(m_text);
		}
	});

	$('input[name=sms_remain_alert_use]', $common_setup).click(function() {
		var $target = $('#area_sms_remain_alert', $common_setup);

		if(this.checked) {
			$target.show();
		} else {
			$target.hide();
		}
	});

	$('input[name=use_sms_idle]', $common_setup).click(function() {
		var $target = $('#area_sms_idle', $common_setup);

		if(this.checked) {
			$target.show();
		} else {
			$target.hide();
		}
	});
});