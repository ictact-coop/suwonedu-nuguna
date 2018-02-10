<?php
/**
 * @class  sms
 * @author Perbiz (http://perbiz.co.kr)
 * @brief  sms 모듈의 최상위클래스
 **/
class sms extends ModuleObject {
	var $db;
	var $tbl_sms_site;
	var $domain;
	var $perbiz_server = 'sms.perbiz.co.kr';
	var $perbiz_index  = '/requestServer/xe.php';
	var $auth_date;
	var $auth_id;
	var $auth_key;
	var $pcsfield;
	var $point      = 30; //기본포인트 30포인트
	var $send_point = 1; // sms 1통 보낼때 가감되는 포인트
	var $defaultPcs;
	var $install = null;

	var $triggers = array(
		// action forward에 등록 (관리자 모드에서 사용하기 위함)
		array('module.dispAdditionSetup', 'sms', 'view', 'triggerDispSmsAdditionSetup', 'after'),
		// 게시판에 게시글 쓸때 sms전송을 위함
		array('document.insertDocument', 'sms', 'controller', 'insertSmsWritePerbizCheck', 'after'),
		// 게시판에 게시글 수정시에 sms전송을 위함
		array('document.updateDocument', 'sms', 'controller', 'updateSmsWriteModifyPerbizSend', 'after'),
		// 게시판에 댓글 쓸때 sms전송을 위함
		array('comment.insertComment', 'sms', 'controller', 'insertSmsReplyPerbizCheck', 'after'),
		// 회원가입시 알림을 위한 트리거
		array('member.insertMember', 'sms', 'controller', 'triggerInsertMember', 'after')
	);
	var $add_column = array(
		// 2009. 07. 09 sms_board에 컬럼 추가
		// 새글 사용유무
		array('sms_board', 'bwrite_sms_use', 'varchar', 4),
		// 새글수정 사용유무
		array('sms_board', 'bwrite_sms_modify_use', 'varchar', 4),
		// 댓글수정 사용유무
		array('sms_board', 'breply_sms_modify_use', 'varchar', 4),
		// 2009. 07. 22 sms_board에 컬럼 추가
		array('sms_board', 'admin_use', 'varchar', 4),
		array('sms_board', 'user_use', 'varchar', 4),
		array('sms_board', 'extraid', 'varchar', 30),
		array('sms_board', 'log_use', 'varchar', 4),
		array('sms_board', 'bpcs_use', 'varchar', 4),
		array('sms_board', 'cate_use', 'varchar', 4),
		array('sms_board', 'searchid_pcs', 'varchar', 14),
		array('sms_board', 'searchid_use', 'varchar', 4)
	);


	function sms() {
		/// 인증을 위한 기본작업 ////
		$domain = $_SERVER['HTTP_HOST'];
		$domain = str_replace('www.', '', $domain);
		$domain = str_replace('WWW.', '', $domain);

		$default_site = executeQuery('sms.dispSmsAdminDefaultSite');

		if($default_site->data) {
			if(count($default_site->data)>1)$checkRow = $default_site->data[0];
			else $checkRow = $default_site->data;

			$this->auth_id  = $checkRow->site_id;
			$this->auth_key = $checkRow->authkey;
			$this->auth_date= $checkRow->regdate;

			if($checkRow->site_domain)$this->domain = $checkRow->site_domain;
			if($checkRow->point > 0)$this->point    = $checkRow->point;

			$this->defaultPcs = $checkRow->pcs;
			$this->pcsfield = $checkRow->pcsfield;

		} else if($default_site->error) {
			 $this->install = true;
		} else $this->domain = $domain;

	}


	/**
	 * @brief 서버간 통신시 인코딩
	 **/
	function sendEncode($val) {
		return base64_encode($val);
	}


	/**
	 * @brief 퍼비즈 서버로 전송
	 * sendPerbizArray()의 코드로 대체 예정
	 **/
	function sendPerbiz($content) {
		$fp = @fsockopen($this->perbiz_server, 80, $errno, $errstr, 30);
		if($fp) {
			@fputs($fp, "POST ".$this->perbiz_index." HTTP/1.0\r\n");
			@fputs($fp, "Host: ".$this->perbiz_server."\r\n");
			@fputs($fp, "User-Agent: PHP Script\r\n");
			@fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			@fputs($fp, "Content-Length: ".strlen($content)."\r\n");
			@fputs($fp, "Connection: close\r\n\r\n");
			@fputs($fp, $content."\r\n\r\n");
			$flag = 0;
			$result = '';
			while(!feof($fp)) {
				$line = @fgets($fp, 128);
				if((strncasecmp($line, 'Content-Type:text/html', 10) == 0) || $flag == 1) {
					if($flag == 1) $result .= $line;
					$flag = 1;
				}
			}
			@fclose($fp);
		}
		$result = trim(str_replace("\r\n\r\n", '', $result));
		$result = str_replace("\r\n", '', $result);
		$result = str_replace(' ', '', $result);

		return $result;
	}


	/**
	 * @brief 퍼비즈 서버로 전송
	 * 보완중
	 **/
	function sendPerbizArray($mode, $args) {
		$fp = @fsockopen($this->perbiz_server, 80, $errno, $errstr, 30);

		$oSmsController = &getController('sms');
		$data = array();

		// 모듈 설정 가져오기
		$oSmsModel = &getModel('sms');
		$module_config = $oSmsModel->getSmsModuleConfig('sms');

		// 발송 제한 시간대 확인
		// 시간 설정이 있어야하고, 예약발송 도는 관리자 직접 발송이 아니면 처리
		if($module_config->use_sms_idle == 'Y' && !$args['appoint'] && $args['send_method'] != 'direct') {
			$now_date = mktime();
			$idle_stime = mktime($module_config->sms_idle_time_sh, $module_config->sms_idle_time_sm, 0, date('m'), date('d'), date('Y'));
			if($module_config->sms_idle_time_sh >= $module_config->sms_idle_time_eh) {
				$idle_etime = mktime($module_config->sms_idle_time_eh, $module_config->sms_idle_time_em, 0, date('m'), date('d')+1, date('Y'));
			} else {
				$idle_etime = mktime($module_config->sms_idle_time_eh, $module_config->sms_idle_time_em, 0, date('m'), date('d'), date('Y'));
			}

			// 발송 제한 시간에 포함되면 예약 문자로 전송
			if($now_date >= $idle_stime && $now_date <= $idle_etime) {
				$args['appoint'] = 'yes';
				$args['app_time'] = date('Y-m-d H:i:s', $idle_etime);
			}
		}

		// 인증이 필요한 경우 데이터 추가
		if(in_array($mode, array('reauthkey', 'add_domain', 'del_domain', 'sms_sync', 'user_check'))) {
			$data['site'] = $this->sendEncode($this->domain);
			$data['site_id'] = $this->sendEncode($this->auth_id);
			$data['authkey'] = $this->sendEncode($this->auth_key);
		}

		$data['mode'] = $this->sendEncode($mode);

		// 데이터에 따른 처리
		foreach($args as $key => $item) {
			switch($key) {
				case 'site':
				case 'site_id':
				case 'authkey':
				case 'send_method':
				case 'appoint':
				case 'app_time':
				case 'authkey':
					$data[$key] = $this->sendEncode($item);
					break;
				case 'content':
					$data[$key] = strip_tags(str_replace('&nbsp', ' ', $item));
					break;
				case 'send_pcs':
				case 'send_id':
				case 'send_name':
				case 'send_nick':
				case 'send_email':
					foreach($item as $val) {
						$value = $val;
						if($key == 'send_pcs') $value = $oSmsController->phoneNumber($val);
						$data[$key.'[]'] = $value;
					}
					break;
				case 'rec_pcs':
					$data[$key] = $this->sendEncode($oSmsController->phoneNumber($item));
				default:
					$data[$key] = $item;
			}
		}
		$args = '';

		// 쿼리문자열 생성
		foreach($data as $key => $item) {
			$data[$key] = $key.'='.$item;
		}
		$content = implode('&', $data);

		if($fp) {
			@fputs($fp, "POST ".$this->perbiz_index." HTTP/1.0\r\n");
			@fputs($fp, "Host: ".$this->perbiz_server."\r\n");
			@fputs($fp, "User-Agent: PHP Script\r\n");
			@fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			@fputs($fp, "Content-Length: ".strlen($content)."\r\n");
			@fputs($fp, "Connection: close\r\n\r\n");
			@fputs($fp, $content."\r\n\r\n");
			$flag = 0;
			$result = '';
			while(!feof($fp)) {
				$line = @fgets($fp, 128);
				if((strncasecmp($line, 'Content-Type:text/html', 10) == 0) || $flag == 1) {
					if($flag == 1) $result .= $line;
					$flag = 1;
				}
			}
			@fclose($fp);
		}

		$result = trim(str_replace("\r\n\r\n", '', $result));
		$result = str_replace("\r\n", '', $result);
		$result = str_replace(' ', '', $result);
		$result = unserialize($result);

        $this->point = $result['point'];

		// 잔여건수 알림
		if($module_config->sms_remain_alert_use == 'Y') {
			$remain_point = $result['point'] - 1;

			foreach($module_config->sms_remain_alert_chk as $item) {
				if($item < $this->point && $item >= $remain_point) {
					$remain_alert = true;
					break;
				}
			}

			if($remain_alert === true) {
				$content = ($remain_point == 0) ? Context::getLang('sms_none_alert'): sprintf(Context::getLang('sms_remain_alert'), $remain_point);
				$ru = array(
					'send_pcs' => array($this->defaultPcs),
					'content' => $this->domain.$content,
					'rec_pcs' => $this->defaultPcs,
					'appoint' => 'no' /* 즉시 발송 */
				);

				$result = $this->sendPerbizArray('user_check', $ru);
				if($result['code'] == '17') $this->insertStatic($result, '', $this->send_point);

			}
		}

		return $result;
	}


	function authCheck() {
		if(!$this->auth_id || !$this->auth_key || !$this->pcsfield) return false;

		return true;
	}


	function moduleInstall() {
		$oModuleController = &getController('module');

		// $this->triggers 트리거 일괄 추가
		foreach($this->triggers as $trigger) {
			$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}

		return new Object();
	}


	function checkUpdate() {
		$oDB = &DB::getInstance();
		$oModuleModel = &getModel('module');

		// $this->triggers 트리거 일괄 검사
		foreach($this->triggers as $trigger) {
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4])) return true;
		}

		// $this->add_column 컬럼추가 일괄 검사
		foreach($this->add_column as $column) {
			if(!$oDB->isColumnExists($column[0], $column[1])) return true;
		}

		return false;
	}


	function moduleUpdate() {
		$oDB = &DB::getInstance();
		$oModuleModel = &getModel('module');
		$oModuleController = &getController('module');

		// $this->triggers 트리거 일괄 업데이트
		foreach($this->triggers as $trigger) {
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4])) {
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}

		// $this->add_column 컬럼추가 일괄 업데이트
		foreach($this->add_column as $column) {
			if(!$oDB->isColumnExists($column[0], $column[1])) {
				$oDB->addColumn($column[0], $column[1], $column[2], $column[3]);
			}
		}

		return new Object(0, 'success_updated');
	}


	/**
	 * @brief 캐시 파일 재생성
	 **/
	function recompileCache() {
	}

}
