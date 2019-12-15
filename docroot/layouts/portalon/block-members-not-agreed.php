<?php
$mid = $_GET['mid'];
$logged_info = Context::get('logged_info');
if(!empty($logged_info)) {
	// 동의하지 않은 경우 재동의 화면에 고정
	if(empty($logged_info->agree201912yn)) {
		if($mid != 'reconfirm_privacy_terms') {
			echo '<script>location.href="/reconfirm_privacy_terms"</script>';
		}
	} else {
		// 동의했지만 성별/생년월일/주소 없는 경우 개인정보 추가 화면에 고정
		if(empty($logged_info->gender) || empty($logged_info->birthday) || empty($logged_info->address)) {
			if($_GET['act'] != 'dispMemberModifyInfo') {
				echo '<script>location.href="/index.php?act=dispMemberModifyInfo&type=add_additional_info"</script>';
			}
		}
	}
}
?>
