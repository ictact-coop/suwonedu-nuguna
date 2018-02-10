
function makeList() {
    var fo_obj = xGetElementById("mobilemessage_fo");
    var mobilemessage_srl = new Array();

    if(typeof(fo_obj.cart.length)=='undefined') {
        if(fo_obj.cart.checked) mobilemessage_srl[mobilemessage_srl.length] = fo_obj.cart.value;
    } else {
        var length = fo_obj.cart.length;
        for(var i=0;i<length;i++) {
            if(fo_obj.cart[i].checked) mobilemessage_srl[mobilemessage_srl.length] = fo_obj.cart[i].value;
        }
    }

    return mobilemessage_srl;
}

/* 일괄 취소 */
function cancelMobilemessage() {
    var message_id = makeList();

    if(message_id.length<1) return;

    var url = './?module=textmessage&act=dispTextmessageAdminCancelReserv&message_id='+message_id.join(',');
    winopen(url, 'delete_log','scrollbars=no,width=400,height=500,toolbars=no');
}

/* 일괄 취소 후 */
function completeMobilemessageCancel(ret_obj) {
    alert(ret_obj['message']);
    opener.location.href = opener.current_url;
    window.close();
}

/* 일괄 취소(그룹) */
function cancelGroupMessages() {
    var group_ids = makeList();

    if(group_ids.length<1) return;

    var url = './?module=textmessage&act=dispTextmessageAdminCancelGroup&group_ids='+group_ids.join(',');
    winopen(url, 'delete_log','scrollbars=no,width=400,height=500,toolbars=no');
}

/* 일괄 취소 후(그룹) */
function completeCancelGroupMessages(ret_obj) {
    alert(ret_obj['message']);
    opener.location.href = opener.current_url;
    window.close();
}


function completeUpdateStatus(ret) {
	updateStatus.progress_count++;
	if (!getUnfinishedMessages.list.length) {
		setTimeout("jQuery('div.jGrowl .updateStatus').find('.jGrowl-close').trigger('jGrowl.close');", 2000);
	}
	jQuery('#updateCounter').text(updateStatus.progress_count); 
	setTimeout("updateStatus()", 1000);
}

function updateStatus() {
	if (typeof(updateStatus.progress_count)=='undefined') {
		updateStatus.progress_count = 0;
		jQuery.jGrowl("<div id='statusUpdader' style='color:white;'>전송결과 가져오는 중...<br /><span id='updateCounter'>0</span> / " + getUnfinishedMessages.total_count + "</div>", { sticky: true, theme:'updateStatus', themeState:'' });
	}

	if (getUnfinishedMessages.list.length > 0) {
		var message_id = getUnfinishedMessages.list.pop()
		exec_xml('textmessage'
				,'procTextmessageAdminUpdateStatus'
				, {message_id : message_id}
				, completeUpdateStatus
				, ['error','message']);
	} else {
		setTimeout("updateStatus()", 3000);
	}
}

function completeGetUnfinishedMessages(ret) {
	if (!ret['data'] || ret['total_count']==0) {
		/*
		setTimeout("jQuery('div.jGrowl').find('.jGrowl-close').trigger('jGrowl.close');", 2000);
		delete getUnfinishedMessages.total_count;
		getUnfinishedMessages.progress_count = 0;
		*/
		return;
	}
	var data = ret['data']['item'];
	if (!jQuery.isArray(data)) {
		data = new Array(data);
	}
	if (typeof(getUnfinishedMessages.total_count)=='undefined') {
		getUnfinishedMessages.total_count = ret['total_count'];
		jQuery.jGrowl("<div id='statusUpdader' style='color:white;'>목록 가져오는 중...<br /><span id='listCounter'>0</span> / " + getUnfinishedMessages.total_count + "</div>", { sticky: true, theme:'getList', themeState:'' });
	}

	for (var i = 0; i < data.length; i++) {
		getUnfinishedMessages.list[getUnfinishedMessages.list.length] = data[i].message_id;
		getUnfinishedMessages.progress_count++;
		jQuery('#listCounter').text(getUnfinishedMessages.progress_count); 

	}

	if (parseInt(ret['page']) < parseInt(ret['total_page'])) {
		setTimeout("getUnfinishedMessages()", 1000);
	} else {
		setTimeout("jQuery('div.jGrowl .getList').find('.jGrowl-close').trigger('jGrowl.close');", 2000);

		if (typeof(updateStatus.progress_count)=='undefined') {
			updateStatus();
		}
	}
	getUnfinishedMessages.page++;


}

function getUnfinishedMessages() {
	if (typeof(getUnfinishedMessages.list)=='undefined') {
		getUnfinishedMessages.list = new Array();
	}
	if (typeof(getUnfinishedMessages.page)=='undefined') {
		getUnfinishedMessages.page = 1;
	}
	if (typeof(getUnfinishedMessages.progress_count)=='undefined') {
		getUnfinishedMessages.progress_count = 0;
	}

	exec_xml(
		'textmessage',
		'getTextmessageAdminUnfinishedMessages',
		{page:getUnfinishedMessages.page},
		completeGetUnfinishedMessages,
		['error','message','data','total_count','total_page','page']
	);
}


jQuery(function($) {
	$('a.modalAnchor.cancelReserv').bind('before-open.mw', function(event){
		var message_id = makeList();
		if(message_id.length<1) return;

		exec_xml(
			'textmessage',
			'getTextmessageAdminCancelReserv',
			{message_id:message_id},
			function(ret){
				var tpl = ret.tpl.replace(/<enter>/g, '\n');
				$('#cancelForm').html(tpl);

				//if (checked)$('#extendForm #radio_'+checked).attr('checked', 'checked');
			},
			['error','message','tpl']
		);

	});
	$('a.modalAnchor.cancelReserv').bind('before-open.mw', function(event){
		var message_id = makeList();
		if(message_id.length<1) return;

		exec_xml(
			'textmessage',
			'getTextmessageAdminCancelReserv',
			{message_id:message_id},
			function(ret){
				var tpl = ret.tpl.replace(/<enter>/g, '\n');
				$('#cancelForm').html(tpl);

				//if (checked)$('#extendForm #radio_'+checked).attr('checked', 'checked');
			},
			['error','message','tpl']
		);

	});
	$('a.modalAnchor.deleteGroup').bind('before-open.mw', function(event){
		var group_ids = makeList();
		if(group_ids.length<1) return;

		exec_xml(
			'textmessage',
			'getTextmessageAdminDeleteGroup',
			{group_ids:group_ids},
			function(ret){
				var tpl = ret.tpl.replace(/<enter>/g, '\n');
				$('#deleteForm').html(tpl);

				//if (checked)$('#extendForm #radio_'+checked).attr('checked', 'checked');
			},
			['error','message','tpl']
		);

	});
	$('a.modalAnchor.deleteMessages').bind('before-open.mw', function(event){
		var message_ids = makeList();
		if(message_ids.length<1) return;

		exec_xml(
			'textmessage',
			'getTextmessageAdminDeleteMessages',
			{message_ids:message_ids},
			function(ret){
				var tpl = ret.tpl.replace(/<enter>/g, '\n');
				$('#deleteForm').html(tpl);

				//if (checked)$('#extendForm #radio_'+checked).attr('checked', 'checked');
			},
			['error','message','tpl']
		);
	});

	getUnfinishedMessages();
});
