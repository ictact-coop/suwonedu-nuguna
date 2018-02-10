<?php
/**
 * @class  sms
 * @author Perbiz (http://perbiz.co.kr)
 * @brief  sms 모듈의 controller class
 **/
class smsController extends sms {

	function init() {
		///////// json_encode 함수가없는경우 php5.0 이하인경우 json 클래스를 삽입한다 //
		if($this->jsonObject==null) {
			include 'json.util.php';
			$this->jsonObject = new Services_JSON();
		}
	}


	/**
	 * @brief 전화번호 parse
	 **/
	function phoneNumber($number) {
		if(!$number) return false;

		$number = str_replace(array('\'', '"', ' ', '-', '.', ',', '/', ')'), '', $number);

		return $number;
	}

	/**
	 * @brief 전화번호 정보 parse 2
	 **/
	function getPcsNumberByUserId($memberInfo) {
		$telfield = $this->pcsfield;
		$writePcs = $memberInfo->$telfield;

		if(is_array($writePcs)) $writePcs = implode('', $writePcs);
		$writePcs = $this->phoneNumber($writePcs);

		return $writePcs;
	}


	/**
	 * @brief 주소록 정보를 전송형태로 변환한다
	 * FIXME 중복제거
	 **/
	function getSendUser($bcode, $obj='', $smsBoardSet='', $check='') {
		if(!$bcode) return false;

		$oSmsModel = &getModel('sms');
		$is_check_pcs = array(); // 전화번호 중복체크

		$oMemberModel = &getModel('member');

		//인증을위한 정보셋팅 /////
		$target = '&site='.$this->sendEncode($this->domain);
		$target .= '&site_id='.$this->sendEncode($this->auth_id);
		$target .= '&authkey='.$this->sendEncode($this->auth_key);

		if($check == 'direct') {
			// 관리자 직접발송일때
			//주소록 정보를 가져온다
			$addrList = $oSmsModel->getSmsAddr($bcode);
			if(!is_array($addrList))$addrList = array();

			foreach($addrList as $val) {
				$phone = $this->phoneNumber($val['upcs']);
				if($phone && !$is_check_pcs[$phone]) {
					$is_check_pcs[$phone] = 'yes';

					$target .= '&send_pcs[]='.$phone;
					$target .= '&send_name[]='.$val['uname'];
					$target .= '&send_id[]='.$val['uid'];
				}
			}
			$return['target'] = $target;
			$return['total'] = count($addrList);

			return $return;

		} else { // 게시판에서 전송되는 경우
			/*********************************************************/
			//		새로 추가된 로직 적용 2009-07-23
			//		추적아이디 검사
			/*********************************************************/

			if($smsBoardSet->searchid_use == 'yes') {

				$searchId = $oSmsModel->getSearchId($obj->module_srl,$obj->user_id);
				if($searchId) {

					// 전화번호를 가져온다
					$target .= '&send_pcs[]='.$this->phoneNumber($smsBoardSet->searchid_pcs);
					$target .= '&send_name[]='.$obj->user_name;
					$target .= '&send_id[]='.$obj->user_id;
				}
			}


			/*********************************************************/
			//		새로 추가된 로직 적용 2009-07-23
			//		관리자 sms 사용 , 사용자 sms 사용
			/*********************************************************/
			/// 사용자 사용일때 sms 정보를 가져오는 부분

			if($smsBoardSet->user_use == 'yes') {

				if($check == 'reply') {

					$selobj->bcode = $obj->module_srl;
					$selobj->dcode = $obj->document_srl;

					$useInfo = executeQuery('sms.getSmsUserUse', $selobj);

					if($useInfo->data->smsuse == 'yes') {
						$target.= '&send_pcs[]='.$useInfo->data->user_pcs;
						$target.= '&send_name[]='.$obj->user_name;
						$target.= '&send_id[]='.$obj->user_id;
					}

				} else {

					$memberInfo = $oMemberModel->getMemberInfoByUserID($obj->user_id);
					// 전화번호를 가져온다
					$writePcsNum = $this->getPcsNumberByUserId($memberInfo);

					$extraField = 'extra_vars'.$smsBoardSet->extraid;

					if($obj->$extraField)$smsuse = 'yes'; // 확장변수에 체크햇을경우 ,사용일경우
					else $smsuse = 'no';

					$selobj->bcode = $obj->module_srl;
					$selobj->dcode = $obj->document_srl;

					$useInfo = executeQuery('sms.getSmsUserUse', $selobj);

					$inobj->bcode = $obj->module_srl;
					$inobj->bcate = $obj->category_srl;
					$inobj->dcode = $obj->document_srl;
					$inobj->user_id = $obj->user_id;
					$inobj->user_name = $obj->user_name;
					$inobj->user_pcs = $writePcsNum;
					$inobj->smsuse = $smsuse;

					if($useInfo->data->user_id) {
						//// 값을 수정 한다
						executeQuery('sms.updateSmsUserUse', $inobj);
					} else {
						//// 새글 일때 값을 저장 한다
						$inobj->sms_srl = getNextSequence();
						$inobj->regdate = mktime();
						executeQuery('sms.insertSmsUserUse', $inobj);
					}
				}
			}


			//// 관리자 사용일때 sms 정보를 가져오는 부분 /////////
			if($smsBoardSet->admin_use == 'yes') {

				if($smsBoardSet->cate_use == 'yes') {

					if(!$obj->category_srl) {
						$oDocumentModel = &getModel('document');
						$docuInfo = $oDocumentModel->getDocument($obj->document_srl);
						$obj->category_srl = $docuInfo->variables['category_srl'];

					}

					//주소록 정보를 가져온다
					$addrList = $oSmsModel->getSmsAddr($bcode,$obj->category_srl);
					if(!is_array($addrList))$addrList = array();

					foreach($addrList as $val) {
						$phone = $this->phoneNumber($val['upcs']);
						if($phone && !$is_check_pcs[$phone]) {
							$is_check_pcs[$phone] = 'yes';

							$target.= '&send_pcs[]='.$phone;
							$target.= '&send_name[]='.$val['uname'];
							$target.= '&send_id[]='.$val['uid'];
						}
					}

				} else {
					//주소록 정보를 가져온다
					$addrList = $oSmsModel->getSmsAddr($bcode);
					if(!is_array($addrList))$addrList = array();
					foreach($addrList as $val) {
						if($this->phoneNumber($val['upcs'])) {
							if(!$is_check_pcs[$this->phoneNumber($val['upcs'])]) {

								$is_check_pcs[$this->phoneNumber($val['upcs'])] = 'yes';

								$target.= '&send_pcs[]='.$this->phoneNumber($val['upcs']);
								$target.= '&send_name[]='.$val['uname'];
								$target.= '&send_id[]='.$val['uid'];
							}
						}
					}
				}
			}
		}

		return $target;
	}


	/**
	 * @brief 관리자 직접 sms전송
	 * FIXME
	 **/
	function sendSmsDirectPerbizSend() {
		if(!$this->authCheck()) return;

		$logged_info = Context::get('logged_info');
		if($logged_info->is_admin != 'Y') {
			// 모듈 설정 가져오기
			$oSmsModel = &getModel('sms');
			$module_config = $oSmsModel->getSmsModuleConfig('sms');
			$permit_group = explode('|@|', $module_config->permit_addressbook);

			if($logged_info->group_list) {
				foreach($logged_info->group_list as $srl => $group) {
					if(in_array($srl, $permit_group)) $permit_addressbook = true;
				}
			}

			if(!$permit_addressbook) {
				$this->setMessage('msg_not_permitted');
				return;
			}
		}


		$obj = Context::getRequestVars();
		$bcode = (!$obj->bcode) ? 'direct' : $obj->bcode;


		$target = 'mode='.$this->sendEncode('user_check');
		$target.= '&send_method='.$this->sendEncode('direct');//관리자직접전송시에

		//머리글
		if($obj->bhead_use == 'no')$bhead =  $this->domain;
		else $bhead = $obj->bhead;

		$target.= '&appoint='.$this->sendEncode($obj->appointment);
		if($obj->appointment=='yes') {
			$target.= '&app_time='.$this->sendEncode($obj->send_appointment_time);
		}
		$content = str_replace('&nbsp',' ',$obj->send_content);
		$content = strip_tags($content);

		$target.= '&content='.$content;
		$target.= '&bhead='.$bhead;

		//회신번호
		if($obj->bpcs1) {
			$target.= '&rec_pcs='.$this->sendEncode($this->phoneNumber($obj->bpcs1.$obj->bpcs2.$obj->bpcs3));
		} else {
			$target.= '&rec_pcs='.$this->sendEncode($this->phoneNumber($obj->rec_pcs));
		}


		//주소록 정보를 가져온다
		$addrList= $this->getSendUser($bcode,'','','direct');
		$target.=$addrList['target'];

		// 추가된 로직=> 전화번호 직접추가시에
		$pcs_total = $obj->add_pcs_total;
		$flag = 0;
		for($p = 1; $p <= $pcs_total; $p++) {
			$pcsvar = 'add_pcs_'.$p;
			if($obj->$pcsvar) {
				$target.= '&send_pcs[]='.$this->phoneNumber($obj->$pcsvar);
				$flag++;
			}
		}

		// 보낼 사람 정보가 없을때
		if(($flag < 1) && ($addrList['total']<1)) {
			$this->setMessage('js_c5_msg');
			return;
		}

		$result = $this->sendPerbiz($target);
		$returnVal = unserialize(trim($result));

		$this->setMessage('js_c7_msg');
		$this->insertStatic($returnVal,$bcode,$send_point);

	}


	/**
	 * @brief 게시판에 글쓸때 sms전송
	 * FIXME
	 **/
	function insertSmsWritePerbizCheck($obj) {

		if($this->authCheck()) {

			$boardModule = &getController('board');
			$bcode = $boardModule->module_srl;
			$oSmsModel = &getModel('sms');
			$smsBoardSet = $oSmsModel->getSmsBoardInfo($bcode);
			if(!$smsBoardSet || !is_object($smsBoardSet) || $smsBoardSet->bpcs_use)	return new Object();

			if($smsBoardSet->bpcs_use != 'yes')$smsBoardSet->bpcs = '';

			if($smsBoardSet->buse == 'yes' && $smsBoardSet->bwrite_sms_use == 'yes') {
				$target = 'mode='.$this->sendEncode('user_check');

				//글의 내용일때
				if($smsBoardSet->bwrite_use == 'no') $content = $obj->title;
				else $content = $smsBoardSet->bwrite;

				$content = str_replace('&nbsp', ' ', $content);
				$content = strip_tags($content);

				//머리글설정
				if($smsBoardSet->bhead_use == 'no')$bhead =  $this->domain;
				else $bhead = $smsBoardSet->bhead;

				$target.= '&content='.$content;
				$target.= '&bhead='.$bhead;
				$target.= '&rec_pcs='.$this->sendEncode($smsBoardSet->bpcs);  //회신번호


				//주소록 정보를 가져온다
				$target.= $this->getSendUser($bcode,$obj,$smsBoardSet);


				$result = $this->sendPerbiz($target);
				$returnVal = unserialize(trim($result));

				// sms 전송 성공시에
				if($returnVal['code'] == '17') {
					
					if($smsBoardSet->log_use == 'yes') {
					    $obj->content = $obj->content." <img src='./modules/sms/tpl/img/sendsms.gif' border=0>";
					    executeQuery('document.updateDocument', $obj);
					}
				}

				$this->insertStatic($returnVal,$bcode,$send_point);
			}
		}

		return new Object();
	}


	/**
	 * @brief 게시판에 새글 수정시에 sms전송
	 * FIXME
	 **/
	function updateSmsWriteModifyPerbizSend($obj) {

		if($this->authCheck()) {

			$obj = Context::getRequestVars();
			$boardModule = &getController('board');
			$bcode = $boardModule->module_srl;
			$oSmsModel = &getModel('sms');
			$smsBoardSet = $oSmsModel->getSmsBoardInfo($bcode);
			if(!$smsBoardSet || !is_object($smsBoardSet) || $smsBoardSet->bpcs_use)	return new Object();

			if($smsBoardSet->bpcs_use != 'yes')$smsBoardSet->bpcs = '';

			if($smsBoardSet->buse == 'yes' && $smsBoardSet->bwrite_sms_modify_use == 'yes') {


				$target = 'mode='.$this->sendEncode('user_check');

				//글의 내용일때
				if($smsBoardSet->bwrite_use == 'no') $content = $obj->title;
				else $content = $smsBoardSet->bwrite;

				$content = str_replace('&nbsp',' ',$content);
				$content = strip_tags($content);


				//머리글설정
				if($smsBoardSet->bhead_use == 'no')$bhead =  $this->domain;
				else $bhead = $smsBoardSet->bhead;

				$target.= '&content='.$content;
				$target.= '&bhead='.$bhead;
				$target.= '&rec_pcs='.$this->sendEncode($smsBoardSet->bpcs);  //회신번호


				//주소록 정보를 가져온다
				$target.= $this->getSendUser($bcode,$obj,$smsBoardSet);


				$result = $this->sendPerbiz($target);
				$returnVal = unserialize(trim($result));
				/// sms 전송 성공시에

				if($returnVal['code'] == '17') {
					$updobj->document_srl = $obj->document_srl;

					if($smsBoardSet->log_use == 'yes')$updobj->content = $obj->content."<img src='./modules/sms/tpl/img/sendsms.gif' border=0>";
					else $updobj->content = $obj->content;

					executeQuery('document.updateDocument', $updobj);
				}
				$this->insertStatic($returnVal,$bcode,$send_point);

			}
		}

		return new Object();

	}


	/**
	 * @brief 댓글쓸때 sms 전송
	 * FIXME
	 **/
	function insertSmsReplyPerbizCheck($reobj) {
		if($this->authCheck()) {
			$obj = Context::getRequestVars();
			$boardModule = &getController('board');
			$bcode = $boardModule->module_srl;
			$oSmsModel = &getModel('sms');
			$smsBoardSet = $oSmsModel->getSmsBoardInfo($bcode);
			if(!$smsBoardSet || !is_object($smsBoardSet) || $smsBoardSet->bpcs_use)	return new Object();

			if($smsBoardSet->bpcs_use != 'yes')$smsBoardSet->bpcs = '';

			if($smsBoardSet->buse == 'yes' && $smsBoardSet->breply_sms_use == 'yes') {

				$target = 'mode='.$this->sendEncode('user_check');

				//글의 내용일때
				if($smsBoardSet->breply_use == 'no') $content = $obj->content;
				else $content = $smsBoardSet->breply;

				$content = str_replace('&nbsp',' ',$content);
				$content = strip_tags($content);

				//머리글설정
				if($smsBoardSet->bhead_use == 'no')$bhead =  $this->domain;
				else $bhead = $smsBoardSet->bhead;

				$target.= '&content='.$content;
				$target.= '&bhead='.$bhead;
				$target.= '&rec_pcs='.$this->sendEncode($smsBoardSet->bpcs);  //회신번호


				//주소록 정보를 가져온다
				$target.= $this->getSendUser($bcode,$reobj,$smsBoardSet,'reply');
				$result = $this->sendPerbiz($target);

				$returnVal = unserialize(trim($result));

				/// sms 전송 성공시에
				if($returnVal['code'] == '17') {

					if($smsBoardSet->log_use == 'yes') {
					    $reobj->content = $reobj->content."<img src='./modules/sms/tpl/img/sendsms.gif' border=0>";
                        $r = executeQuery('comment.updateComment',$reobj);
                    }
				}

				$this->insertStatic($returnVal,$bcode,$send_point);

			}
		}
		$retObj = new Object();
		return $retObj;

	}
	/**
	* @brief 댓글수정시에 sms 전송
	 * FIXME
	**/
	function updateSmsReplyModifyPerbizSend($reobj) {

		if($this->authCheck()) {
			$obj = Context::getRequestVars();
			$boardModule = &getController('board');
			$bcode = $boardModule->module_srl;
			$oSmsModel = &getModel('sms');
			$smsBoardSet = $oSmsModel->getSmsBoardInfo($bcode);
			if(!$smsBoardSet || !is_object($smsBoardSet) || $smsBoardSet->bpcs_use)	return new Object();

			if($smsBoardSet->bpcs_use != 'yes')$smsBoardSet->bpcs = '';

			if($smsBoardSet->buse == 'yes' && $smsBoardSet->breply_sms_modify_use == 'yes') {

				//인증을위한 정보셋팅 /////
				$target = 'mode='.$this->sendEncode('user_check');
				$target.= '&site='.$this->sendEncode($this->domain);
				$target.= '&site_id='.$this->sendEncode($this->auth_id);
				$target.= '&authkey='.$this->sendEncode($this->auth_key);

				//글의 내용일때
				if($smsBoardSet->breply_use == 'no') $content = $obj->content;
				else $content = $smsBoardSet->breply;

				$content = str_replace('&nbsp',' ',$content);
				$content = strip_tags($content);


				//머리글설정
				if($smsBoardSet->bhead_use == 'no')$bhead =  $this->domain;
				else $bhead = $smsBoardSet->bhead;

				$target.= '&content='.$content;
				$target.= '&bhead='.$bhead;
				$target.= '&rec_pcs='.$this->sendEncode($smsBoardSet->bpcs);  //회신번호

				//주소록 정보를 가져온다
				$target.= $this->getSendUser($bcode,$reobj,$smsBoardSet,'reply');

				$result = $this->sendPerbiz($target);
				$returnVal = unserialize(trim($result));

							/// sms 전송 성공시에
				if($returnVal['code'] == '17') {
					$updobj->comment_srl = $reobj->comment_srl;

					if($smsBoardSet->log_use == 'yes')$updobj->content = $reobj->content." <img src='./modules/sms/tpl/img/sendsms.gif' border=0>";
					else $updobj->content = $reobj->content;

					$r = executeQuery('comment.updateComment',$updobj);

				}
				$this->insertStatic($returnVal,$bcode,$send_point);
			}
		}
		$retObj = new Object();
		return $retObj;
	}

    /**
	 * @brief 결과값 디비에 저장
	 * FIXME
	 **/
	function insertStatic($result,$bcode,$send_point="") {
		//$obj = new Object();
		unset($log_obj);


		/// sms 전송 성공시에
		if($result['code'] == '17') {

			$oSmsModel = &getModel('sms');
			$failNum = $oSmsModel->pointUpdate($send_point,$result['point'],'minus');
			$succNum = $result['sendNum'];

			$log_obj->sms_srl = getNextSequence();
			$log_obj->sms_domain = $this->domain;
			$log_obj->bcode		 = $bcode;
			$log_obj->succNum	 = $succNum;

			$log_obj->ryear		 = date('Y');
			$log_obj->rmonth	 = date('m');
			$log_obj->rday		 = date('d');
			$log_obj->regdate	 = mktime();
			$log_obj->failNum	 = $failNum;

			//print_r($obj);
			executeQuery("sms.putsSmsChart",$log_obj);
			if($bcode == 'direct') {
				$this->setMessage('c7');
			}
		//// 실패햇을경우 //////
		} else {

			$log_obj->sms_srl = getNextSequence();
			$log_obj->sms_domain = $this->domain;
			$log_obj->bcode		 = $bcode;
			$log_obj->err_code   = $result['code'];
			$log_obj->regdate	 = mktime();

		//	print_r($obj);
			executeQuery("sms.putsSmsLogInfo",$log_obj);
			if($bcode == 'direct') {
				$this->setMessage('c3');
			}

		}

	}


	/**
	 * @brief 결제완료후에
	 **/
	function requestPerbizServer() {
		if(!$this->authCheck()) return;

		foreach($_POST as $key => $val) {
			if($key == 'module' || $key == 'act') {
				$vars[$key] = $val;
			} else {
				$vars[$key] = base64_decode($val);
			}
		}

		$oSmsModel = &getModel('sms');
		$smsBoardSet = $oSmsModel->getSmsBoardInfo($bcode);
		if(!$smsBoardSet || !is_object($smsBoardSet) || $smsBoardSet->bpcs_use)	return new Object();

		if($vars['site_id'] != $this->auth_id || $vars['auth_key'] != $this->auth_key) exit;

		$oSmsModel->pointUpdate('', $vars['point'], 'plus');

		exit;
	}


	/**
	 * @brief 회원가입시 관리자/가입자에게 sms 알림
	 **/
	function triggerInsertMember($obj) {
		if(!$this->authCheck()) return;

		$oMemberModel = &getModel('member');
		$oSmsModel = &getModel('sms');

		$obj = $oMemberModel->arrangeMemberInfo($obj);

		$oModuleModel = &getModel('module');
		$module_config = $oModuleModel->getModuleConfig('sms');

		$args['rec_pcs'] = $this->defaultPcs;
		$pcsfield = $this->pcsfield;

		if($module_config->join_sms_admin == 'Y') {
			$args = array();
			$args['send_pcs'][0] = $this->defaultPcs;
			$args['content'] = $oSmsModel->getContentParse($module_config->join_sms_admin_text, $obj);
			$result = $this->sendPerbizArray('user_check', $args);
			if($result['code'] == '17') $this->insertStatic($result,$bcode,$send_point);
		}

		if($module_config->join_sms_member == 'Y' && $obj->$pcsfield) {
			$args = array();
			$args['appoint'] = 'no'; // 즉시 전송
			$args['send_pcs'][0] = implode('', $obj->$pcsfield);
			$args['content'] = $oSmsModel->getContentParse($module_config->join_sms_member_text, $obj);
			$result = $this->sendPerbizArray('user_check', $args);
			if($result['code'] == '17') $this->insertStatic($result,'',$this->send_point);
		}
	}



 ///////////// 주소록 //////////////




	/**
	 * @brief 전체회원선택
	 **/
	function insertSmsAllMemberSet() {

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
	function deleteSmsAllMemberSet() {
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
	function insertSmsAddress() {
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
	function updateSmsGroupMember() {
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
	function deleteSmsGroupMember() {

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
	function deleteSmsAddress() {

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






}
?>