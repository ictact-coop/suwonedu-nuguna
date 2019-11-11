<?php
$mid = $_GET['mid'];
$logged_info = Context::get('logged_info');
$oDB = &DB::getInstance();
$query = $oDB->_query("select extra_vars from xe_member where member_srl = " . $logged_info->member_srl);
$result = $oDB->_fetch($query);
$extra_vars = unserialize($result->extra_vars);

$regdate = $logged_info->regdate;
if(!empty($logged_info) && empty($extra_vars->agree20181YN) && $regdate < 20180430235959 && $mid != 'agree_201804') {
	// 2018년 4월 30일 이전 가입자 수: 1367
	// 그 중 동의하지 않은 회원: 1217
	// extra_vars not contain agree20181YN => 1217
	echo '<script>location.href="/agree_201804"</script>';
}
?>
