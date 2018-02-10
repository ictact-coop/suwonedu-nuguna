/**
 * @file   modules/board/js/sms_admin.js
 * @author 조철한(in perbiz) (cch6721@perbiz.com)
 * @brief  board 모듈의 관리자용 javascript
 **/

var popWindow = {
	init: function(module_srl,winTop,winLeft,view,check,bcate,sear_type,sear_string) {
		var winHeight  = document.body.clientHeight;

		if(this.initcheck == true) {
			alert(js_already_addrset);
			return;
		} else this.initcheck = true;


		this.width   = 600;
		this.height	 = 500;
		this.maxHeight = 900;
		if(check == 'button') {
			this.check = 'button';
			this.buttonId = module_srl;

		} else if(check == 'bcate') {

			this.check = 'bcate';
			this.buttonId = bcate;
			this.buttonPre = module_srl;

		} else if(check == 'searchId') {

			this.check = 'searchId';
			this.buttonId = 'searchId';
			this.buttonPre = module_srl;
		} else {
			this.check = '';
			this.buttonId = module_srl;
		}


		var tact = 'dispSmsAdminMemberAddrSetting';
		if(module_srl == 'direct')module_srl = '&admin_send=direct';
		if(!bcate)bcate = 'defaultaddr';

		if(sear_type) {
			bcate += '&search='+sear_type+'&string='+sear_string;
		}

		if(check == 'searchId') {
			// FIXME
			this.win = new Window({className: "dialog", title: "",
					  top:winHeight-winTop, left:parseInt(200)+parseInt(winLeft), width:this.width, height:this.height,
					  url:'./?module=sms&act='+tact+'&module_srl='+module_srl+'&bcate='+bcate, showEffectOptions: {duration:0.1}})
		} else {
			// FIXME
			this.win = new Window({className: "alphacube", title: "",
					  top:winHeight-winTop, left:parseInt(200)+parseInt(winLeft), width:this.width, height:this.height,
					  url:'./?module=sms&act='+tact+'&module_srl='+module_srl+'&bcate='+bcate, showEffectOptions: {duration:0.1}})
		}



		this.win.show();
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
	payPoint: function(user_id,auth_key,url) {
		var winWidth  = document.body.clientWidth;
		this.payWin = new WindowNew({className: "alphacube", title: "",
					  top:100, left:0, width:100, height:100,
					  url: "http://sms.perbiz.co.kr/?user_id="+user_id+"&auth_key="+auth_key, showEffectOptions: {duration:0.1}})


		this.payWin.show();
		this.payWin.setSize(winWidth-15,900,0.3);
		this.url = url;
	},
	winRecover: function() {
		var winSize = this.getWinSize();
		if(winSize.height > this.height)this.win.setSize(this.width,this.height,0.3);

	},
	winCloseReload: function() {
		location.href=this.url;
	},
	winClose: function() {
		this.controller
		this.initcheck = null;
		return this.win.close(this.controller,'click');
	},
	memberAdd: function(num,check) {

		if(this.check == 'button') {
			$('hiddenMemberTotal_'+this.buttonId).value = num;
			var total = $('hiddenMemberTotal_'+this.buttonId).value;
			$('memberTotal_'+this.buttonId).value = 'Member Total : '+total;

		} else if(this.check == 'bcate') {
			$('hiddenMemberTotal_'+this.buttonPre+'_'+this.buttonId).value = num;
			var total = $('hiddenMemberTotal_'+this.buttonPre+'_'+this.buttonId).value;
			$('memberTotal_'+this.buttonPre+'_'+this.buttonId).innerHTML = total;

		} else if(this.check == 'searchId') {
			$('hiddenMemberTotal_'+this.buttonPre+'_searchId').value = num;
			var total = $('hiddenMemberTotal_'+this.buttonPre+'_searchId').value;
			$('memberTotal_'+this.buttonPre+'_searchId').innerHTML = total;

		} else {
			$('hiddenMemberTotal_'+this.buttonId).value = num;
			var total = $('hiddenMemberTotal_'+this.buttonId).value;
			$('memberTotal_'+this.buttonId).innerHTML = total;
		}
	},
	memberDel: function(num,check) {

		if(this.check == 'button') {
			$('hiddenMemberTotal_'+this.buttonId).value = num;
			var total = $('hiddenMemberTotal_'+this.buttonId).value;
			$('memberTotal_'+this.buttonId).value = 'Member Total : '+total;

		} else if(this.check == 'bcate') {
			$('hiddenMemberTotal_'+this.buttonPre+'_'+this.buttonId).value = num;
			var total = $('hiddenMemberTotal_'+this.buttonPre+'_'+this.buttonId).value;
			$('memberTotal_'+this.buttonPre+'_'+this.buttonId).innerHTML = total;

		} else if(this.check == 'searchId') {
			$('hiddenMemberTotal_'+this.buttonPre+'_searchId').value = num;
			var total = $('hiddenMemberTotal_'+this.buttonPre+'_searchId').value;
			$('memberTotal_'+this.buttonPre+'_searchId').innerHTML = total;

		} else {
			$('hiddenMemberTotal_'+this.buttonId).value = num;
			var total = $('hiddenMemberTotal_'+this.buttonId).value;
			$('memberTotal_'+this.buttonId).innerHTML = total;
		}
	},
	memberAddUpd: function(num,check) {

		if(this.check == 'button') {
			var layer = $('hiddenMemberTotal_'+this.buttonId);
			layer.value = parseInt(layer.value) + parseInt(num);

			var total = 'Member Total : '+layer.value;
			$('memberTotal_'+this.buttonId).value=total;

		} else if(this.check == 'bcate' || this.check == 'searchId') {
			var layer = document.getElementById('hiddenMemberTotal_'+this.buttonPre+'_'+this.buttonId);
			var layerText = document.getElementById('memberTotal_'+this.buttonPre+'_'+this.buttonId);

			layer.value = parseInt(layer.value) + parseInt(num);
			var total = layer.value;
			layerText.innerHTML = total;
		} else {
			var layer = document.getElementById('hiddenMemberTotal_'+this.buttonId);
			var layerText = document.getElementById('memberTotal_'+this.buttonId);
			layer.value = parseInt(layer.value) + parseInt(num);
			var total = layer.value;
			layerText.innerHTML = total;
		}

	},
	memberDelUpd: function(num,check) {

		if(this.check == 'button') {
			var layer = $('hiddenMemberTotal_'+this.buttonId);
			layer.value = parseInt(layer.value) - parseInt(num);
			var total = 'Member Total : '+layer.value;
			$('memberTotal_'+this.buttonId).value= total;

		} else if(this.check == 'bcate' || this.check == 'searchId') {
			var layer = document.getElementById('hiddenMemberTotal_'+this.buttonPre+'_'+this.buttonId);
			var layerText = document.getElementById('memberTotal_'+this.buttonPre+'_'+this.buttonId);
			layer.value = parseInt(layer.value) - parseInt(num);
			var total = layer.value;
			layerText.innerHTML = total;
		} else {
			var layer = document.getElementById('hiddenMemberTotal_'+this.buttonId);
			var layerText = document.getElementById('memberTotal_'+this.buttonId);
			layer.value = parseInt(layer.value) - parseInt(num);
			var total = layer.value;
			layerText.innerHTML = total;
		}





	}


}

function alinkGo(act,srl,aTagName) {
	// FIXME
	location.href='./?module=admin&act='+act+'&module_srl='+srl+'&atag='+aTagName;
}

/* 모듈 생성 후 */
function completeInsertSmsBoard(ret_obj) {
	var data = ret_obj['message'].evalJSON();
	alert(data.msg);
	if(data.code == 'fail') {
		return false;
	} else if(data.code == 'failsystem') {
		location.reload();
	} else  {
		// FIXME
		location.href='./?module=admin&act=dispSmsAdminContents';
	}

}


function check_set(id, view) {
	var layer = jQuery('#'+id+'_layer');

	if(!view) view = 'show';

	if(view == 'show') {
		layer.show();
	} else {
		layer.hide();
	}

	if(id == 'breply_sms' || id == 'bwrite_sms') {
		var modify_layer = jQuery('#'+id+'_modify_layer');

		if(view == 'show') {
			modify_layer.show();
		} else {
			modify_layer.hide();
		}
	}
}

function sms_check_submit(f,module_srl) {

	if($('buse').value == 'yes') {

		if($('bhead_layer').style.display != 'none') {
			if(!$F('bhead')) {
				alert(lang_set_direct_msg+lang_set_tail_input_msg);
				$('bhead').focus();

				return false;
			}
		}

		// FIXME
		if($('bwrite_sms_layer').style.display != 'none') {
			if($('bwrite_layer').style.display != 'none') {
				if(!$F('bwrite')) {
					alert(lang_set_direct_msg+lang_set_tail_input_msg);
					f.bwrite.focus();

					return false;
				}
			}
		}

		// FIXME
		if($('breply_sms_layer').style.display != 'none') {
			if($('breply_layer').style.display != 'none') {
				if(!$F('breply')) {
					alert(lang_set_direct_msg+lang_set_tail_input_msg);
					$('breply').focus();
					return false;
				}
			}
		}

		// FIXME
		if($('return_layer').style.display == 'block') {
			if(!$F('bpcs1') || !$F('bpcs2') || !$F('bpcs3')) {
				alert(lang_set_pcs_msg+lang_set_tail_input_msg);
				if(!$F('bpcs1'))$('bpcs1').focus();
				else if(!$F('bpcs2'))$('bpcs2').focus();
				else if(!$F('bpcs3'))$('bpcs3').focus();

				return false;
			}
		}

		if($('searchid_layer').style.display == 'block') {

			if($F('hiddenMemberTotal_'+module_srl+'_searchId')<1) {
				alert(js_searchId_input_msg);

				return false;
			}

			if(!$F('sear_pcs1') || !$F('sear_pcs2') || !$F('sear_pcs3')) {
				alert(lang_set_searchId_msg+lang_set_tail_input_msg);

				if(!$F('sear_pcs1'))$('sear_pcs1').focus();
				else if(!$F('sear_pcs2'))$('sear_pcs2').focus();
				else if(!$F('sear_pcs3'))$('sear_pcs3').focus();

				return false;
			}


		}

	}

	procFilter(f,insert_sms_board);

	return false;
}

function completeSmsDirect(ret_obj) {

	var msg = ret_obj['message'];

	if(msg == 'c7' || msg == 'c3') {
		msg  = eval('lang_'+msg+'_msg');
		var url = current_url;
	}

	alert(msg);

	if(url) location.href = url;

	return false;
}

function completeServerSync(ret_obj) {
	var data = ret_obj['message'].evalJSON();

	alert(data.msg);

	if(data.code == '210') {
		$('view_sms_point').innerHTML = data.point;

	}

	return;
}


function addrCheck(sort) {

	if(sort == 'yes') {
		check_set('cateset','show');
		check_set('addr','hide');

	} else if(sort == 'no') {
		check_set('cateset','hide');
		check_set('addr','show');
	}
}

// FIXME
function extraCheck() {

	if($('user_use').checked == true) $('extra_layer').show();
	else  $('extra_layer').hide();
}

// FIXME
function boardUseCheck(dis) {
	$('boardUseLayer').style.display = dis;
}



;(function($) {

/* DOM READY */
jQuery(function($) {
	var $widgetContainer = $('div.smsxe');
	var $widgetForm = $('form#fo_sms_widget', $widgetContainer);
	var $widgetSerchForm = $('form#fo_sms_widget_search', $widgetContainer);
	var $searchArea = $('.search_area', $widgetContainer);
	var $sendPcsList = $('.send_pcs_list', $widgetContainer);
	var $searchInputbox = $('.input_box', $searchArea);
	var $statusArea = $('.status', $widgetContainer);

	$widgetForm.submit(function() {
		if(!$('[name=send_content]', this).val()) {
			alert(lang_require_sms_text);
			$('[name=send_content]', this).focus();
			return false;
		}

		if($('[name=appointment]', this).is(':checked')) {
			var app_date = $('[name=appointment_day]', $widgetForm).val();
			var year  = 	app_date.substring(0,4);
			var month = 	app_date.substring(4,6) - 1;
			var day   = 	app_date.substring(6,8);
			var hour = $('[name=appointment_hour]').val();
			var min	 = $('[name=appointment_min]').val();

			var to_day = new Date().getTime(); // 오늘의 날짜와 시간을 구함
			var appoint_date = new Date(year, month, day, hour, min, 59).getTime();

			if(to_day > appoint_date) {
				alert(lang_set_wrong_appoint_msg);
				return false;
			}
			$('[name=send_appointment_time]', $widgetForm).val(year+'-'+(++month)+'-'+day+' '+hour+':'+min+':'+59);
		}

		procFilter(this, send_sms_direct);
		return false;

	});

	$widgetSerchForm.submit(function() {
		var bcode = $('[name=bcode]', this).val();
		var search = $('[name=search]', this).val();
		var title = $('[name=search]', this).attr('title');
		if(title == search || !search) {
			alert("검색어를 입력해주세요.\n검색항목 : 아이디, 이름, 닉네임");
			$('[name=search]', this).focus();
			return false;
		}
		popWindow.init('direct',820,70,null,null,null,'userName',search);
		return false;
	});

	$('textarea', $widgetForm).keyup(function() {
		var get_length = PerbizSMS.getByteLength(this);
		if(!get_length) return;

		$('.status_byte', $statusArea).text(get_length.length);
		$('.limit_byte', $statusArea).text(get_length.limit_byte);
		$('.status_count', $statusArea).text(get_length.sms_count);
	});

	$('textarea', $widgetForm).triggerHandler('keyup');
	$('textarea', $widgetForm).removeAttr('disabled');
	$('.btn_submit', $widgetForm).removeAttr('disabled');

	$('input', $sendPcsList).keypress(function(e) {
		if(e.which == 13) {
			var $nextInput = $(this).parent().next().children('input');

			if($nextInput.length) {
				$nextInput.focus().select();
			} else {
				$('.return_pcs input', $widgetContainer).focus().select();
			}
			return false;
		}
	});

	$('.bth_addressbook a', $widgetContainer).click(function() {
		popWindow.init('direct',820,70);
		return false;
	});

	if(typeof(jQuery.fn.watermark) == 'function') $searchInputbox.watermark($searchInputbox.attr('title'));

	/**
	 * @berif 목록에서 번호 삭제
	 **/
	$('.btn_delete', $sendPcsList).click(function() {
		var $inputAll = $('input', $sendPcsList);
		$(this).prev('input').val('');
		var $nextInput = $(this).parent().nextAll().children('input');
		var values = [];

		$inputAll.each(function(idx) {
			if(this.value && this.value != 'undefined') values.push(this.value);
		});

		$inputAll.val('');
		$inputAll.each(function(idx) {
			if(values[idx]) {
				this.value = values[idx];
			} else {
				this.value = '';
			}

		});
	});


	$('[name=appointment]', $widgetForm).click(function() {
		if($(this).is(':checked')) {
			$('#appointment_div', $widgetForm).show();
		} else {
			$('#appointment_div', $widgetForm).hide();
		}
	});


	$('.btn_appoint', $widgetForm).click(function() {
		$('.appoint', $widgetForm).toggle();
	});

	$('.close', $widgetForm).click(function() {
		$('.appoint', $widgetForm).hide();
	});

	var option = { gotoCurrent: false,yearRange:'-100:+10', onSelect:function(dateText) {
		$('[name=appointment_day]', $widgetForm).val(dateText.replace(/-/g, ''));
	}};
	if(typeof(jQuery.fn.datepicker) == 'function') $('.inputDate', $widgetForm).datepicker(option);

	$('[name=bhead_use]').click(function() {
		if(window.site_domain) {
			var $content = $('[name=send_content]', $widgetForm);

			var a_text = $content.val().replace(site_domain, '');
			$content.val(a_text);

			var chk = new RegExp("/^"+site_domain+"/g");
			if(this.value == 'yes' && !$content.val().match(chk)) {
				var a_text = site_domain + $content.val();
				$content.val(a_text);
			}
		}
	});
});

}) (jQuery);




function server_sync() {
    jQuery.exec_json('sms.updateSmsAdminPointSync', {}, completeServerSync);
}