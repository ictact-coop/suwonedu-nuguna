function completeBannermgmInsert(ret_obj) {
	alert(ret_obj['message']);
	location.href=current_url.setQuery('act','dispBannermgmAdminContent');
}

function doDeleteBannermgm(bannermgm_srl) {
	var fo_obj = jQuery("#fo_delete")[0];
	if(!fo_obj) return;
    fo_obj.bannermgm_srl.value = bannermgm_srl;
    procFilter(fo_obj, banner_delete);
}

function completeDeleteBannermgm(ret_obj) {
	alert(ret_obj['message']);
	location.href=current_url.setQuery('act','dispBannermgmAdminContent');
}

/* 각 메뉴의 버튼 이미지 등록 */
function doUploadBannerImage(obj) {
    // 이미지인지 체크
    if(!/\.(gif|jpg|jpeg|png|swf)$/i.test(obj.value)) return alert("이미지 또는 플래시 파일이 아닙니다.");
    
    var fo_obj = jQuery("#fo_banner")[0];
    fo_obj.act.value = "procBannermgmAdminUploadBanner";
    fo_obj.target.value = obj.name;
    fo_obj.submit();
    fo_obj.act.value = "";
    fo_obj.target.value = "";
}

/* 메뉴 이미지 업로드 후처리 */
function completeUploadBannerImage(filename) {
    var fo_obj = jQuery('#fo_banner')[0];
    var img_obj = jQuery('#banner_file_img');
    var zone_obj = jQuery('#banner_file_zone');

    fo_obj["image_path"].value = filename;

    var img = new Image();
    img.src = filename;
    img_obj.attr('src', img.src);
    zone_obj.show();
}