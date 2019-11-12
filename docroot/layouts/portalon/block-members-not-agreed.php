<?php
$mid = $_GET['mid'];
$logged_info = Context::get('logged_info');
$oDB = &DB::getInstance();
$query = $oDB->_query("select extra_vars from xe_member where member_srl = " . $logged_info->member_srl);
$result = $oDB->_fetch($query);
$extra_vars = unserialize($result->extra_vars);

$regdate = $logged_info->regdate;
if(!empty($logged_info) && empty($extra_vars->agree20181YN) && $regdate < 20180430235959 && $mid != 'agree_201804') {
	echo '<script>location.href="/agree_201804"</script>';
}
?>
