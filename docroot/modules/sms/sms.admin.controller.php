<?php
/**
 * @class  smsAdminController
 * @author Perbiz (http://perbiz.co.kr)
 * @brief  sms 모듈의 admin controller class
 **/
class smsAdminController extends sms {
	var $jsonObject = null;

	function init() {
		///////// json_encode 함수가없는경우 php5.0 이하인경우 json 클래스를 삽입한다 //
		if($this->jsonObject==null) {
			include 'json.util.php';
			$this->jsonObject = new Services_JSON();
		}
	}


	/**
	 * @brief 게시판별 sms 환경 설정
	 **/
	function insertSmsAdminBoard() {
		$obj = Context::getRequestVars();

		if($obj->bcode<1) {
			$result = array('code'=>'failsystem','msg'=>Context::getLang('js_fail_system_msg'));
			$result = $this->jsonObject->encode($result);
			$this->setMessage($result);
			return;
			exit;
		}

		$oSmsModel = &getModel('sms');
		$smsInfo = $oSmsModel->getSmsBoardInfo($obj->bcode);

		$nowtime = mktime();
		$obj->searchid_pcs =  $obj->sear_pcs1.'-'.$obj->sear_pcs2.'-'.$obj->sear_pcs3;
		$obj->bpcs =  $obj->bpcs1.'-'.$obj->bpcs2.'-'.$obj->bpcs3;
		$obj->bregdate	= $nowtime;

		if(!$smsInfo->sms_srl) {
			$obj->sms_srl = getNextSequence();

			$ret = executeQuery('sms.insertSmsBoardSet', $obj);

		} else executeQuery('sms.updateSmsBoardSet', $obj);

		$result = array('code'=>'succ', 'msg'=>Context::getLang('js_set_ok_msg'));
		$result = $this->jsonObject->encode($result);

		$this->setMessage($result);

	}


	/**
	 * @brief 전체회원선택
	 **/
	function insertSmsAdminAllMemberSet() {

		$obj = Context::getRequestVars();
		if(!$obj->bcate)$obj->bcate = 'defaultaddr';
		// 먼저 모든회원의 그룹및 회원정보를 삭제한다
		$delobj->bcode = $obj->bcode;
		$userResult = executeQuery("sms.deleteSmsAdminAddress",$delobj);

		// 그룹정보중에서 차단시킨 회원정보를 모두삭제
		if($userResult->message='success')$groupResult = executeQuery('sms.deleteSmsAdminGmGroupList', $delobj);
		// 회원의 그룹정보삭제
		if($groupResult->message='success')$inUserResult = executeQuery('sms.deleteSmsAdminUserGroup', $delobj);

		$obj->sms_srl = getNextSequence();
		if($inUserResult->message='success')$lastResult=executeQuery('sms.insertSmsAdminAddress', $obj);

		if($lastResult->message='success') {
			$code = 'yes';

		} else {
			$code = 'fail';
			$msg = getLang('js_fail_msg');
		}
		$oSmsModel = &getModel('sms');
		$memberTotal =  count($oSmsModel->getSmsAddr($obj->bcode,$obj->bcate));

		$result = array('addMember'=>$memberTotal,'code'=>$code,'msg'=>$msg);
		$result = $this->jsonObject->encode($result);
		$this->setMessage($result);

	}


	/**
	 * @brief 전체회원선택해제
	 **/
	function deleteSmsAdminAllMemberSet() {
		$obj = Context::getRequestVars();
		if(!$obj->bcate)$obj->bcate = 'defaultaddr';

		$delobj->bcode = $obj->bcode;
		$lastResult = executeQuery('sms.deleteSmsAdminAddress', $delobj);

		if($lastResult->message='success') {
			$code = 'alldel';
		} else {
			$code = 'fail';
			$msg = getLang('js_fail_msg');
		}

		$oSmsModel = &getModel('sms');
		$memberTotal =  count($oSmsModel->getSmsAddr($obj->bcode,$obj->bcate));


		$result = array('delMember'=>$memberTotal, 'code'=>$code, 'msg'=>$msg);
		$result = $this->jsonObject->encode($result);
		$this->setMessage($result);
	}

	/**
	 * @brief 주소저장
	 **/
	function insertSmsAdminAddress() {
		$obj = Context::getRequestVars();
		$oSmsModel = &getModel('sms');

		if(!$obj->bcate)$obj->bcate = 'defaultaddr';

		if($obj->bsort == 'user') $query_addr = 'sms.getSmsAdminAddrUserCheck';
		else if($obj->bsort == 'group') $query_addr = 'sms.getSmsAdminAddrGroupCheck';

		////////// 그룹및 회원검사 //////////////

		$addrlist_data = executeQuery($query_addr,$obj);
		$row = $addrlist_data->data->srl;

		if($row) {
			if($obj->bsort == 'group')$result = array('code'=>'f206','msg'=>Context::getLang('js_f206_msg'));
			else if($obj->bsort == 'user')$result = array('code'=>'f207','msg'=>Context::getLang('js_f207_msg'));
			$result = $this->jsonObject->encode($result);
			$this->setMessage($result);
			return;
		}

		/////////// 회원일때 ////////////////
		if($obj->bsort == 'user') {
			$oMemberModel = &getModel('member');

			$userInfo  = $oMemberModel->getMemberInfoByMemberSrl($obj->uid,0); // 회원정보를 가져옴
			$extraInfo = $oMemberModel->arrangeMemberInfo($userInfo,0); // 추가정보를 가져옴


			$group_srl = '';
			foreach($extraInfo->group_list as $key=>$val) {
				$group_srl[] = $key;
				unset($checkobj);
				$checkobj->bcode    = $obj->bcode;
				$checkobj->bcate = $obj->bcate;
				$checkobj->bsort	= 'group';
				$checkobj->uid		= $key;

				$checkGroupData = executeQuery("sms.getSmsAdminAddrGroupCheck",$checkobj);
				$gRow = $checkGroupData->data->srl;


				if($gRow) {
					$result = array('code'=>'f213','msg'=>Context::getLang('js_f213_msg'));
					$result = $this->jsonObject->encode($result);
					$this->setMessage($result);
					return;
				}
			}
			$userPcs = trim($this->pcsfield);

			if(@$extraInfo->$userPcs) {
				$upcs='';
				if(is_array($extraInfo->$userPcs)) {
					foreach($extraInfo->$userPcs as $val) {
						$upcs.= $val.'-';
					}
					$upcs = substr($upcs,'0','-1');
				} else $upcs = $extraInfo->$userPcs;
			}

			$useq		= $obj->uid;
			$obj->upcs   = $upcs;
			$obj->uid   = $userInfo->user_id;
			$obj->useq  = $useq;

			if(count($group_srl)>0) {
				for($inum=0; $inum<count($group_srl); $inum++) {
					unset($group_obj);
					$group_obj->sms_srl = getNextSequence();
					$group_obj->useq  = $obj->useq;
					$group_obj->bcode = $obj->bcode;
					$group_obj->bcate = $obj->bcate;
					$group_obj->group_seq = $group_srl[$inum];
					executeQuery("sms.insertSmsAdminUserGroup",$group_obj);
				}
			}
			$obj->gseq  = '';
			$obj->uname = $userInfo->user_name;

		/////////// 그룹일때::기존에 회원정보가 추가되는 그룹에 포함되어있을때 기존회원정보삭제함 ////////
		} else if($obj->bsort == 'group') {
			unset($checkobj);
			$checkobj->bcode	 = $obj->bcode;
			$checkobj->group_seq = $obj->uid;
			$checkobj->bcate  = $obj->bcate;

			$checkGroupData = executeQueryArray("sms.getSmsAdminUserGroup",$checkobj);
			if($checkGroupData->data) {
				foreach($checkGroupData->data as $cval) {
					unset($delobj);
					$delobj->bcode	  = $obj->bcode;
					$delobj->bcate = $obj->bcate;
					$delobj->bsort	  = 'user';
					$delobj->useq	  =  $cval->useq;
					executeQuery("sms.deleteSmsAdminAddrUserCheck",$delobj);
				}
			}
			$groupInfoData = $oSmsModel->getSmsGroupInfo($obj->uid);
			$uName      = $groupInfoData->title;
			$obj->gseq  = $obj->uid;
			$obj->uid   = '';
			$obj->uname = trim($uName);

		}

		$obj->sms_srl = getNextSequence();
		executeQuery("sms.insertSmsAdminAddress",$obj);
		//if($uflag > 0){ //// 기존 회원이 새로추가되는 그룹속에 포함된겅우임
		//	$result = array('addMember'=>$addMember,'code'=>'f227','msg'=>Context::getLang('js_f227_msg'));
		//} else { // 성공값

		$memberTotal = count($oSmsModel->getSmsAddr($obj->bcode,$obj->bcate));
		$result = array('addMember'=>$memberTotal,'code'=>'yes','msg'=>'');
		//}

		$result = $this->jsonObject->encode($result);

		$this->setMessage($result);

	}


	/**
	* @brief 그룹회원중에서 sms송신여부 업데이트
	**/
	function updateSmsAdminGroupMember() {
		$obj = Context::getRequestVars();
		if(!$obj->bcate)$obj->bcate = 'defaultaddr';


		if($obj->mode == 'yes') {
			$result = executeQuery("sms.deleteSmsAdminGmList",$obj);
		} else if($obj->mode == 'no') {
			$obj->sms_srl = getNextSequence();
			$result = executeQuery("sms.insertSmsAdminGmList",$obj);
		}
		if($result->message='success')$code = 'succ';
		else $code = 'fail';

		$result_data = array('code'=>$code,'msg'=>Context::getLang('js_'.$code.'_msg'));
		//print_r($result_data);
		$result_data = $this->jsonObject->encode($result_data);
		$this->setMessage($result_data);
	}

	/**
	 * @brief 주소록삭제중 그룹및 일반회원삭제
	 **/
	function deleteSmsAdminGroupMember() {

		$obj = Context::getRequestVars();
		if(!$obj->bcate)$obj->bcate = 'defaultaddr';

		$result = executeQuery("sms.deleteSmsAdminAddress",$obj);
		$oSmsModel = &getModel('sms');

		// 그룹정보중에서 차단시킨 회원정보가 있는지 검사한다
		if($obj->bsort == 'group') {
			$obj->gcode = $obj->uid;
			$groupMember = $oSmsModel->getGroupMemberUse($obj->bcode,$obj->gcode,'',$obj->bcate);
			if($groupMember)executeQuery("sms.deleteSmsAdminUserGroup",$obj);
		}

		if($result->message='success')$code = 'succ';
		else $code = 'fail';

		$memberTotal =  count($oSmsModel->getSmsAddr($obj->bcode,$obj->gcode));
		$result_data = array('delMember'=>$memberTotal,'code'=>$code,'msg'=>Context::getLang('js_'.$code.'_msg'));
		$result_data = $this->jsonObject->encode($result_data);

		$this->setMessage($result_data);
	}


	/**
	 * @brief 주소록삭제
	 **/
	function deleteSmsAdminAddress() {

		$obj = Context::getRequestVars();
		if(!$obj->bcate)$obj->bcate = 'defaultaddr';

		$oSmsModel = &getModel('sms');
		if($obj->bsort == 'group') {
			$delobj->bsort	 = 'group';
			$delobj->gseq    = $obj->uid;
			$delobj->bcode   = $obj->bcode;
			$delobj->bcate= $obj->bcate;

			// 그룹정보중에서 차단시킨 회원정보가 있는지 검사한다
			$oSmsModel = &getModel('sms');
			$obj->gcode = $obj->uid;
			$groupMember = $oSmsModel->getGroupMemberUse($obj->bcode,$obj->gcode,'',$obj->bcate);
			if($groupMember)executeQuery("sms.deleteSmsAdminGmGroupList",$obj);

		} else if($obj->bsort == 'user') {
			$delobj->bsort = 'user';
			$delobj->useq  = $obj->uid;
			$delobj->bcode = $obj->bcode;
			$delobj->bcate = $obj->bcate;

			// 회원의 그룹정보삭제
			executeQuery("sms.deleteSmsAdminUserGroup",$delobj);
		}

		$result = executeQuery("sms.deleteSmsAdminAddress",$delobj);

		if($result->message='success')$code = 'succ';
		else $code = 'fail';

		$memberTotal =  count($oSmsModel->getSmsAddr($obj->bcode,$obj->gcode));
		$result_data = array('delMember'=>$memberTotal,'code'=>$code,'msg'=>Context::getLang('js_'.$code.'_msg'));
		$result_data = $this->jsonObject->encode($result_data);

		$this->setMessage($result_data);
	}


	/**
	 * @brief 퍼비즈 회원가입
	 **/
	function sendperbizSmsAdminResult() {

		$obj = Context::getRequestVars();

		$site_domain= $this->sendEncode($obj->site_domain);
		$user_name  = $obj->user_name;
		$user_id	= $this->sendEncode($obj->user_id);
		$email		= $this->sendEncode($obj->email);
		$password	= $this->sendEncode($obj->password1);
		$homepage	= $this->sendEncode($obj->homepage);
		$remain_sms	= $this->sendEncode($obj->remain_sms1.'-'.$obj->remain_sms2.'-'.$obj->remain_sms3);
		$phone      = $this->sendEncode($obj->phone1.$obj->phone2.$obj->phone3);

		$phoneVar   = $obj->phone1.'-'.$obj->phone2.'-'.$obj->phone3;


		$sendVar = '&user_name='.$user_name;
		$sendVar.= '&user_id='.$user_id;
		$sendVar.= '&email='.$email;
		$sendVar.= '&password='.$password;
		$sendVar.= '&homepage='.$homepage;
		$sendVar.= '&remain_sms='.$remain_sms;
		$sendVar.= '&phone='.$phone;
		$sendVar.= '&checkTime='.rand(0,mktime());

		$target = 'mode='.$this->sendEncode('userInsert').'&site='.$site_domain.$sendVar;
		$result = $this->sendPerbiz($target);
		$returnVal = unserialize($result);


		// 정상적인 경우
		if($returnVal['code'] == '7') {

			$now = mktime();

			$obj->sms_srl		= getNextSequence();
			$obj->site_id		= $obj->user_id;
			$obj->authKey		= base64_decode($returnVal['key']);
			$obj->pcs			= $obj->remain_sms1."-".$obj->remain_sms2."-".$obj->remain_sms3;
			$obj->user_pcs		= $phoneVar;
			$obj->regdate		= $now;
			executeQuery("sms.insertSmsMemberSet",$obj);

			$result = array('code'=>'f237','auth_key'=>base64_decode($returnVal['key']),
							'msg'=>Context::getLang('js_f237_msg'));
			$result =$this->jsonObject->encode($result);
			$this->setMessage($result);

		} else {

			if($returnVal['code'] == '1')$code = 'f273';
			else $code = 'f274';

			$msg = Context::getLang('js_'.$code.'_msg');

			$result = array('code'=>$code,'msg'=>$msg);
			$result =$this->jsonObject->encode($result);

			$this->setMessage($result);
		}

	}


	/**
	 * @brief 초기 설정정보 수정
	 **/
	function updateSmsAdminSetting() {

		$obj = Context::getRequestVars();
		$remain_sms	= $obj->user_pcs1.'-'.$obj->user_pcs2.'-'.$obj->user_pcs3;

		$now = mktime();
		$obj->site_id		= $this->auth_id;
		$obj->authKey		= $this->auth_key;
		$obj->pcs			= $remain_sms;

		$r =executeQuery("sms.updateSmsMemberSet",$obj);
		if($r->message == 'success')$code = 'f247';
		else $code = 'f246';

		$this->setMessage($code);

	}


	/**
	 * @brief 아이디 중복체크
	 **/
	function checkIdSmsAdminResult() {
		$obj = Context::getRequestVars();

		$sendVar = '&user_id='.$this->sendEncode($obj->user_id);
		$sendVar.= '&checkTime='.rand(0,mktime());

		$target = 'mode='.$this->sendEncode('userId_check').$sendVar;
		$result = $this->sendPerbiz($target);

		$returnVal = unserialize($result);

		if($returnVal['code'] == '1') {
			$code = 'f273';
			$msg = Context::getLang('js_'.$code.'_msg');

		} else $code = 'f1';

		$result = array('code'=>$code,'msg'=>$msg);
		$result =$this->jsonObject->encode($result);
		$this->setMessage($result);

	}


	/**
	 * @brief 인증키재발급
	 **/
	function getSmsAdminReauthkey() {

		$domain = $this->sendEncode($this->domain);
		$user_id	= $this->sendEncode($this->auth_id);
		$auth_key	= $this->sendEncode($this->auth_key);

		$sendVar = 'mode='.$this->sendEncode('reauthkey');
		$sendVar.= '&user_id='.$user_id;
		$sendVar.= '&auth_key='.$auth_key;
		$sendVar.= '&site='.$domain;
		$sendVar.= '&checkTime='.rand(0,mktime());

		$result = $this->sendPerbiz($sendVar);
		$returnVal = unserialize($result);


		// 정상적인 경우
		if($returnVal['code'] == '207') {

			$obj->user_id		= $this->auth_id;
			$obj->authKey	    = $returnVal['auth_key'];
			executeQuery("sms.updateSmsAdminSite",$obj);
			$code = 'succ';

		} else $code = 'fail';

		$result_data = array('code'=>$code,'msg'=>Context::getLang('js_reauthkey_'.$code.'_msg'),'auth_key'=>$returnVal['auth_key']);
		$result_data = $this->jsonObject->encode($result_data);
		$this->setMessage($result_data);

	}


	/**
	 * @brief 도메인추가
	 **/
	function insertSmsAdminAddDomain() {

		$obj = Context::getRequestVars();

		$domain = str_replace(',','.',$obj->domain);
		$domain = str_replace('\"','',$domain);
		$domain = str_replace('\'','',$domain);
		$domain = str_replace('WWW.','',$domain);
		$domain = str_replace('www.','',$domain);
		$domain = str_replace('/','',$domain);
		$domain = str_replace(':','',$domain);
		$domain = str_replace('http','',$domain);


		$add_domain = $this->sendEncode($domain);
		$user_id	= $this->sendEncode($this->auth_id);
		$auth_key	= $this->sendEncode($this->auth_key);

		$sendVar = 'mode='.$this->sendEncode('add_domain');
		$sendVar.= '&user_id='.$user_id;
		$sendVar.= '&auth_key='.$auth_key;
		$sendVar.= '&add_domain='.$add_domain;
		$sendVar.= '&checkTime='.rand(0,mktime());

		$result = $this->sendPerbiz($sendVar);
		$returnVal = unserialize($result);


		// 정상적인 경우
		if($returnVal['code'] == '207') {

			$obj->sms_srl		= getNextSequence();
			$obj->user_id		= $this->auth_id;
			$obj->add_domain	= $domain;
			$obj->regdate		= date('Y-m-d');
			executeQuery("sms.insertSmsAdminAddDomain",$obj);
			$code = 'f247';

		} else $code = 'f246';

		$result_data = array('code'=>$code,'msg'=>Context::getLang('js_'.$code.'_msg'));
		//print_r($result_data);
		$result_data = $this->jsonObject->encode($result_data);
		$this->setMessage($result_data);

	}


	/**
	 * @brief 도메인삭제
	 **/
	function deleteSmsAdminAddDomain() {

		$del_domain = $this->sendEncode(Context::get('del_domain'));
		$user_id	= $this->sendEncode($this->auth_id);
		$auth_key	= $this->sendEncode($this->auth_key);

		$sendVar = 'mode='.$this->sendEncode('del_domain');
		$sendVar.= '&user_id='.$user_id;
		$sendVar.= '&auth_key='.$auth_key;
		$sendVar.= '&del_domain='.$del_domain;
		$sendVar.= '&checkTime='.rand(0,mktime());

		$result = $this->sendPerbiz($sendVar);
		$returnVal = unserialize($result);


		// 정상
		if($returnVal['code'] == '207') {
			$obj->user_id		= $this->auth_id;
			$obj->add_domain	= Context::get('del_domain');
			$r = executeQuery("sms.deleteSmsAdminAddDomain",$obj);

			$this->setMessage('f247');
		} else {
			$this->setMessage('f246');
		}


	}


	/**
	 * @brief 게시판별 환경설정 복사
	 **/
	function updateSmsAdminCopySetting() {

		$obj = Context::getRequestVars();

		//환경설정은 크게 다음 테이블의 내용에 따른다
		//sms_board -> 게시판환경설정
		//sms_userlist -> 주소록설정
		//sms_user_group
		//sms_gm_use   -> 주소록중 그룹회원

		$user_args->module_srl = $obj->bcode;
		$group_args->bcode = $obj->bcode;
		$oSmsModel = &getModel('sms');


		for($i=0; $i<count($obj->module_code); $i++) {
			// 먼저 선택된 게시판의 모든설정을 다 지운다


			$target_args->bcode = $obj->module_code[$i];

			$del_gmlist = executeQuery("sms.deleteSmsAdminGmList",$target_args);
			$del_grouplist = executeQuery("sms.deleteSmsAdminUserGroup",$target_args);
			$del_userlist = executeQuery("sms.deleteSmsAdminAddress",$target_args);

			//대상 게시판의 설정정보를 하나씩 받아온다 //
			//복사할대상의 게시판설정정보
			$oriInfo = $oSmsModel->getSmsBoardInfo($obj->bcode);
			foreach($oriInfo as $key=>$val) {
				if($key != bcode && $key != sms_srl) {
					$ori_args->$key = $val;
				}
			}
			$ori_args->bcode = $obj->module_code[$i];

			$smsInfo = $oSmsModel->getSmsBoardInfo($obj->module_code[$i]);

			if(!$smsInfo->sms_srl) {
				$ori_args->sms_srl = getNextSequence();
				executeQuery("sms.insertSmsBoardSet",$ori_args);
			} else executeQuery("sms.updateSmsBoardSet",$ori_args);
			unset($ori_args);


			//1. 회원정보를 받아서 저장한다
			$userInfo = executeQueryArray("sms.getSmsAddrList",$user_args);
			if($userInfo->data) {
				foreach($userInfo->data as $val) {
					foreach($val as $sekey=>$seval) {
						if($sekey != 'bcode' && $sekey != 'sms_srl' && $sekey != 'bcate') {
							$ins_args->$sekey = $seval;
						} else if($sekey == 'bcate') {
							$ins_args->bcate = $seval;
						}
					}
					$ins_args->bcode = $obj->module_code[$i];
					$ins_args->sms_srl = getNextSequence();

					$var = executeQuery("sms.insertSmsAdminAddress",$ins_args);
					unset($ins_args);
				}
			}

			//2. 그룹정보중 차단된 그룹내회원정보를 받아서 저장한다
			$groupInfo = executeQueryArray("sms.getSmsAdminUserGroup",$group_args);
			if($groupInfo->data) {
				foreach($groupInfo->data as $val) {
					foreach($val as $sekey=>$seval) {
						if($sekey != bcode && $sekey != sms_srl) {
							$ins_args->$sekey = $seval;
						} else if($sekey == 'bcate') {
							$ins_args->bcate = $seval;
						}
					}
					$ins_args->bcode = $obj->module_code[$i];
					$ins_args->sms_srl = getNextSequence();
					executeQuery("sms.insertSmsAdminUserGroup",$ins_args);
					unset($ins_args);
				}
			}

			//3. 회원정보중 차단된 회원정보를 받아서 저장한다
			$delUserInfo = executeQueryArray("getSmsAdminGmSetList",$group_args);
			if($delUserInfo->data) {
				foreach($delUserInfo->data as $val) {
					foreach($val as $sekey=>$seval) {
						if($sekey != bcode && $sekey != sms_srl) {
							$ins_args->$sekey = $seval;
						} else if($sekey == 'bcate') {
							$ins_args->bcate = $seval;
						}
					}
					$ins_args->bcode = $obj->module_code[$i];
					$ins_args->sms_srl = getNextSequence();
					executeQuery("sms.insertSmsAdminGmList",$ins_args);
					unset($ins_args);
				}
			}
		}

		echo "<script>";
		echo "opener.location.reload();";
		echo "self.close();";
		echo "</script>";
	}


	function checkSmsAdminLogin() {
		$user_id = Context::get('user_id');
		$user_pw = Context::get('password');

		$target = 'user_id='.$this->sendEncode($user_id);
		$target.= '&user_pw='.$this->sendEncode($user_pw);
		$target.= '&mode='.$this->sendEncode('login');

		$result = $this->sendPerbiz($target);
		$returnVal = unserialize(trim($result));


		// 정상적인 경우
		if($returnVal['code'] == '777') {
			$now = mktime();
			$domain		= $_SERVER['HTTP_HOST'];
			$domain		= str_replace('www.','',$domain);
			$domain		= str_replace('WWW.','',$domain);
			$obj->site_domain   = $domain;
			$obj->sms_srl		= getNextSequence();
			$obj->site_id		= $user_id;
			$obj->authKey		= base64_decode($returnVal['auth_key']);
			$obj->pcs			= Context::get('remain_sms1')."-".Context::get('remain_sms2')."-".Context::get('remain_sms3');
			$obj->user_pcs		= $returnVal['phone'];
			$obj->pcsfield		= Context::get('pcsfield');
			$obj->regdate		= $now;
			executeQuery("sms.insertSmsMemberSet",$obj);

			// 잔여건수 싱크
			$this->auth_id = $obj->site_id;
			$this->auth_key = $obj->authKey;
			$this->updateSmsAdminPointSync();
		}

		$result_data = array('code'=>$returnVal['code'],'msg'=>Context::getLang('js_login_'.$returnVal['code'].'_msg'));
		$result_data = $this->jsonObject->encode($result_data);
		$this->setMessage($result_data);
	}


	function updateSmsAdminPointSync() {
		$oSmsModel = getModel('sms');

		$target = 'site_id='.$this->sendEncode($this->auth_id);
		$target.= '&authkey='.$this->sendEncode($this->auth_key);
		$target.= '&mode='.$this->sendEncode('sms_sync');
		$result = $this->sendPerbiz($target);
		$returnVal = unserialize(trim($result));

		if($returnVal['code'] == '210') {
			$oSmsModel->pointUpdate("",$returnVal['remain_sms']);
		}

		if(!$returnVal['code']) $returnVal['code'] = '11';

		$result_data = array('code'=>$returnVal['code'],'msg'=>Context::getLang('js_point_'.$returnVal['code'].'_msg'),'point'=>$returnVal['remain_sms']);
		$result_data = $this->jsonObject->encode($result_data);
		$this->setMessage($result_data);
	}


	function procSmsAdminCommonSetup() {
		$config = Context::getRequestVars();

		$oModuleController = &getController('module');
		$output = $oModuleController->insertModuleConfig('sms', $config);
		$this->setMessage('success_registed');
	}

}
