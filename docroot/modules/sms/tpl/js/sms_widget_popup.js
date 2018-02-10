

if(top.window.popWindow) {
	var winSize = top.window.popWindow.getWinSize();
	if(winSize.height > top.window.popWindow.height) {
		top.window.popWindow.winRecover();
	}
}


var addrview = {
	groupNavi: function(page) {
		var f = document.smsMemberForm;
		f.uid.value = this.gid; // 그룹코드
		f.page.value = page;
		var f = document.smsMemberForm;
		//////// 그룹에 속한 회원정보를 가져온다 ////////
		procFilter(f,select_group_member_na);


		document.getElementById('groupMemberLayer').style.display='block';
	},
	groupUser: function(idx,check) {
		var f = document.smsMemberForm;
		this.gid    = idx;  // 그룹코드
		this.buttonUse = check;
		f.uid.value = idx;

		//////// 그룹에 속한 회원정보를 가져온다 ////////
		procFilter(f,select_group_member_na);
		document.getElementById('groupMemberLayer').style.display='block';
	},
	groupClose: function() {
		document.getElementById('groupMemberLayer').style.display='none';
		top.window.popWindow.winRecover();
	},
	memberDel: function(idx,uid) {
		var f = document.smsMemberForm;
		if(f.bcate.value == 'searchId')var lang = lang_sms_searchId;
		else var lang = lang_sms_set;

		this.userid = uid;
		this.ret_seq = idx;
		f.uid.value = idx;
		f.bsort.value   = 'user';
		procFilter(f,delete_group_member_na);
	},
	memberInsert: function(idx,uid) {
		var f = document.smsMemberForm;
		if(f.bcate.value == 'searchId')var lang = lang_sms_searchId;
		else var lang = lang_sms_set;

		this.userid = uid;
		this.ret_seq = idx;

		f.bsort.value = 'user';
		f.uid.value =   idx;
		procFilter(f,insert_sms_address_na);
	},
	memberAll: function() {
		var f = document.smsMemberForm;
		if(f.bcate.value == 'searchId')var lang = lang_sms_all_searchId;
		else var lang = lang_sms_all_set;

		if(confirm(lang+lang_sms_do_send+lang_sms_msg)) {

			f.bsort.value = 'alluser';
			procFilter(f,insert_all_address_na);
		}
	},
	memberAllOut: function() {
		var f = document.smsMemberForm;
		if(f.bcate.value == 'searchId')var lang = lang_sms_all_searchId;
		else var lang = lang_sms_all_set;

		if(confirm(lang+lang_sms_donot_send+lang_sms_msg)) {

			f.bsort.value = 'alluser';
			procFilter(f,delete_all_address_na);
		}
	},

	groupDel: function(idx) {
		var f = document.smsMemberForm;
		if(f.bcate.value == 'searchId')var lang = lang_sms_group_searchId;
		else var lang = lang_sms_group_set;

		if(confirm(lang+lang_sms_donot_send+lang_sms_msg)) {
			this.userid = idx;
			this.ret_seq = idx;
			f.uid.value = idx;
			f.bsort.value   = 'group';
			procFilter(f,delete_group_member_na);
		}
	},
	groupInsert: function(idx) {
		var f = document.smsMemberForm;
		if(f.bcate.value == 'searchId')var lang = lang_sms_group_searchId;
		else var lang = lang_sms_group_set;

		if(confirm(lang+lang_sms_do_send+lang_sms_msg)) {

			this.userid = idx;
			this.ret_seq = idx;

			f.bsort.value = 'group';
			f.uid.value =   idx;
			procFilter(f,insert_sms_address_na);
		}
	},
	memberUse: function(userId,check,gid) {
		if(check == 'yes') var msg = lang_sms_do_send;
		else var msg = lang_sms_donot_send;

		var f = document.smsSendForm;
		if(f.bcate.value == 'searchId')var lang = lang_sms_searchId;
		else var lang = lang_sms_set;

		if(confirm(lang+msg+lang_sms_msg)) {
			f.userid.value = userId;
			f.gcode.value = gid;
			f.mode.value = check;
			//////// 그룹에 속한 회원정보를 가져온다 ////////
			procFilter(f,update_group_member_na);
		}
	},
	goUrl: function(actname,msrl,csrl) {
		if(msrl == 'direct')msrl = '&admin_send='+msrl;
		location.href='./?module=sms&act='+actname+'&module_srl='+msrl+'&bcate='+csrl;
	}
}


var smsAddress = {
	init: function() {
		this.check = 'user';
	},

	sms_layer_set: function(sort) {
		jQuery('#hidden_check').val(sort);

		if(sort == 'group') {
			jQuery('#groupLayer').show();
			jQuery('#userLayer').hide();
		} else {
			jQuery('#groupLayer').hide();
			jQuery('#userLayer').show();
		}
	},

	allUserCheck: function() {

		$('bsort').value = 'alluser';

		if($('alluser').checked == true) {
			procFilter(document.smsSetForm,insert_sms_address);
			$('smsSetForm').disable();
		} else {
			$('smsSetForm').enable();
			procFilter(document.smsSetForm,insert_sms_address);
		}

	}
}


/**
 * @berif 주소록에서 회원을 삭제한 후
 * FIXME
 **/
function completeDelMember(ret_obj) {
	var uid = addrview.userid;
	var useq = addrview.ret_seq;
	var data = ret_obj['message'].evalJSON();
	if(data.code == 'fail') {
		alert(data.msg);
		return;
	} else {
		if($('bsort').value == 'group') {
			var tag = "<a href=\"javascript:addrview.groupInsert('"+useq+"');\" class='buttonSet buttonDisable'><span></span></a>";
			var moreTag = "<span style='cursor:pointer;' onclick=\"addrview.groupUser('"+useq+"','useNot');\">"+lang_sms_more_text+"</span>";
			$(uid+'_more_span').innerHTML = moreTag;

			$('groupMemberLayer').hide();

		} else {
			var tag = "<a href=\"javascript:addrview.memberInsert('"+useq+"','"+uid+"');\" class='buttonSet buttonDisable'><span></span></a>";

		}
		$(uid+'_span').innerHTML = tag;
	}
}


/**
 * @berif 회원전체선택 상태에서 변경시
 * FIXME
 **/
function completeUpdGroupMember() {
	var f = document.smsSendForm;
	var uid = f.userid.value;

	if(f.mode.value == 'yes') {
		var tag = "<a href=\"javascript:addrview.memberUse('"+uid+"','no','"+addrview.gid+"');\" class='buttonSet buttonActive'><span></span></a>";
	} else {
		var tag = "<a href=\"javascript:addrview.memberUse('"+uid+"','yes','"+addrview.gid+"');\" class='buttonSet buttonDisable'><span></span></a>";
	}
	$(uid+'_span').innerHTML = tag;


}


/**
 * @berif 회원/그룹 선택시
 * FIXME
 **/
function completeSmsAddress(ret_obj) {
	var data = ret_obj['message'].evalJSON();

	if(data.code == 'yes' || data.code == 'success' || data.code == 'f227') {

		if(data.code == 'success' || data.code == 'f227') {
			alert(data.msg);
		}
		var uid = addrview.userid;
		var useq = addrview.ret_seq;

		if($('bsort').value == 'group') {
			var more_tag = "<span onclick=\"addrview.groupUser('"+useq+"');\" style='cursor:pointer;'>"+lang_sms_more_text+"</span>&nbsp;</td>";
			$(uid+'_more_span').innerHTML = more_tag;
			var tag = "<a href=\"javascript:addrview.groupDel('"+useq+"');\" class='buttonSet buttonActive'><span></span></a>";

			$('groupMemberLayer').hide();

		} else {
			var tag = "<a href=\"javascript:addrview.memberDel('"+useq+"','"+uid+"');\" class='buttonSet buttonActive'><span></span></a>";

		}

		$(uid+'_span').innerHTML = tag;

	} else {
		alert(data.msg);
	}
}


/**
 * @berif 개별 회원/그룹 해제시
 * FIXME
 **/
function completeSmsDelAddress(ret_obj) {
	var data = ret_obj['message'].evalJSON();
	if(data.code == 'fail') {
		alert(data.msg);
		return;
	} else if(data.code == 'succ') {
		for(var k=0; k<$('member_sms_select').options.length; k++) {
			if($('member_sms_select').options[parseInt(k)].value = $F('member_sms_select')) {
				$('member_sms_select').options[parseInt(k)] = null;
				break;
			}
		}
		if($('shot_sub_layer')) {
			if($F('shot_sub_layer') == 'no')return;
		}

	}
}


/**
 * @berif 전체 회원 선택/해제시
 * FIXME
 **/
function completeSmsAllAddress(ret_obj) {
	var data = ret_obj['message'].evalJSON();
	if(data.code == 'fail') {
			alert(data.msg);
			return;
	} else if(data.code == 'alldel') {
		location.reload();
	} else {
		var f = document.smsMemberForm;
		location.href = './?module=sms&act=dispSmsMemberAddrSetting&module_srl='+f.bcode.value+'&bcate='+f.bcate.value+'&admin_send=direct';
	}

}


// FIXME
function checkMember(msrl) {
	if($('addr_use').checked == true)var addruse = $F('addr_use');
	else var addruse = '';

	location.href='./?module=sms&act=dispSmsMemberAddrSetting&module_srl='+msrl+'&addr_use='+addruse;
}


/**
 * @berif
 * 사용안하는 듯?
 **/
function completeGroupMember(ret_obj) {
	var result = eval('('+ret_obj['message']+')');
	var list = result.mlist;

	var tag = "<table width=590 cellpadding=0 cellspacing=0 border=0  class='rowTable'>";
	tag+= "<thead><tr>";
	tag+= "<Th>"+cmd_sms_number+"</th>";
	tag+= "<Th>"+cmd_sms_userName+"</th>";
	tag+= "<Th>"+cmd_sms_userId+"</th>";
	tag+= "<Th>"+cmd_sms_pcs+"</th>";
	tag+= "<Th></th>";
	tag+= "</tr></thead>";
	tag+= "<tbody>";

	if(list.length <1) {
		tag+="<tr><td colspan=5 align='center' width='590'>";
		tag+="<font color='#006699'>"+NotResult+"</font>";
		tag+="</td></tr></tbody></table>";

		$('navi_page_info').hide();
		$('groupMemberRequest').show();
		$('groupMemberRequest').innerHTML = tag;
		$('groupMemberRequestNavi').hide();
		return;
	}
	var uname = '';
	var uid = '';
	var upcs = '';
	var num = result.minusNum;
	for(var i=0; i<list.length; i++) {
		if(list[i].user_name)uname = list[i].user_name
		else uname = '';
		if(list[i].user_id)uid = list[i].user_id;
		else uid = '';
		if(list[i].upcs_return_val) upcs = list[i].upcs_return_val;
		else upcs = '';

		tag+= "<tr>";
		tag+= "<Td align=center>"+num+"&nbsp;</td>";
		tag+= "<Td align=center>"+uname+"&nbsp;</td>";
		tag+= "<td align=center>"+uid+"&nbsp;</td>";
		tag+= "<Td align=center>"+upcs+"&nbsp;</td>";
		tag+= "<td><span id='"+uid+"_span'>";

		if(addrview.buttonUse != 'useNot') {

			if(list[i].use_check == 'yes') {
				tag+= "<a href=\"javascript:addrview.memberUse('"+uid+"','no','"+addrview.gid+"');\" class='buttonSet buttonActive'><span></span></a>"
			} else {
				tag+= "<a href=\"javascript:addrview.memberUse('"+uid+"','yes','"+addrview.gid+"');\" class='buttonSet buttonDisable'><span></span></a>";
			}
		}
		tag+= "</span>&nbsp;</td></tr>";
	num--;
	}
	tag+= "</tbody></table>";
	$('groupMemberRequest').show();
	//$('groupMemberRequest').innerHTML = tag;
	/// 페이지 네비게이션 ////

	$('navi_page_info').show();
	$('total_count_view').innerHTML = result.total_count;
	//$('page_view').innerHTML = result.page;
	//$('total_page_view').innerHTML = result.total_page;


	var inNaviTag = "<div class='pagination a1' align=center>";
	inNaviTag+= "<a href=\"javascript:addrview.groupNavi('"+result.navi.first_page+"');\" class='prevEnd'>first page</a>";
	for(var k=1; k<=result.navi.page_count; k++) {
		if(result.page == k)inNaviTag+= "<strong>"+k+"</strong>";
		else inNaviTag+= "<a href=\"javascript:addrview.groupNavi('"+k+"');\">"+k+"</a>";
	}
	inNaviTag+= "<a href=\"javascript:addrview.groupNavi('"+result.navi.last_page+"');\"  class='nextEnd'>last page</a>";


	var naviTag = "<table  width=590 cellpadding=0 cellspacing=0 border=0>";
	naviTag+= "<tr><td align=center>"+inNaviTag+"</td></tr>";
	naviTag+= "</table>";

	top.window.popWindow.winResize(i);

	$('groupMemberRequestNavi').show();
	$('groupMemberRequestNavi').innerHTML = naviTag;
}

