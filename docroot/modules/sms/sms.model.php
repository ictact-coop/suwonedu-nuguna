<?php
class smsModel extends sms {

	function init() {
	}



	/**
	 * @berif sms 모듈 설정 가져오기
	 */
	function getSmsModuleConfig() {
		// 모듈 설정 가져오기
		$oModuleModel = &getModel('module');
		$module_config = $oModuleModel->getModuleConfig('sms');

		// 기본 값 설정
		$module_config->join_head_use = ($module_config->join_head_use) ? $module_config->join_head_use : 'Y';
		if(!$module_config->sms_remain_alert_chk) $module_config->sms_remain_alert_chk = '0,10,50,100';
		if(!is_array($module_config->sms_remain_alert_chk)) $module_config->sms_remain_alert_chk = explode(',', $module_config->sms_remain_alert_chk);

		$module_config->join_sms_admin_text = ($module_config->join_sms_admin_text) ? $module_config->join_sms_admin_text : '['.$this->domain.'] ';
		$module_config->join_sms_member_text = ($module_config->join_sms_member_text) ? $module_config->join_sms_member_text : '['.$this->domain.'] ';



		if(!$module_config->sms_remain_alert_use) $module_config->sms_remain_alert_use = 'Y';
		$module_config->sms_idle_time_sh = ($module_config->sms_idle_time_sh) ? sprintf('%02d', $module_config->sms_idle_time_sh) : '23';
		$module_config->sms_idle_time_sm = ($module_config->sms_idle_time_sm) ? sprintf('%02d', $module_config->sms_idle_time_sm) : '30';
		$module_config->sms_idle_time_eh = ($module_config->sms_idle_time_eh) ? sprintf('%02d', $module_config->sms_idle_time_eh) : '07';
		$module_config->sms_idle_time_em = ($module_config->sms_idle_time_em) ? sprintf('%02d', $module_config->sms_idle_time_em) : '00';

		return $module_config;
	}


	/**
	 * @brief 게시판별로 sms 설정값을 가져옴
	 **/
	function getSmsBoardInfo($bcode) {
		$this_obj = new Object();
		$this_obj->bcode = $bcode;
		$output = executeQuery("sms.getSmsSiteBoardList",$this_obj);
		if(!$output->data) return new Object();
		return $output->data;
	}


	/**
	 * @brief 주소록 :: 네비게이션을 장착
	 **/
	function getSmsAddrNavi($module_srl = '') {
		if(!$module_srl)$module_srl = Context::get('module_srl');

		if(!$module_srl && $this->module_srl) {
			$module_srl = $this->module_srl;
			Context::set('module_srl',$module_srl);
		}

		$bcate = Context::get('bcate');

		if(!$bcate) $bcate = 'defaultaddr';

		$args->bcate = $bcate;
		$args->module_srl = $module_srl;
		$args->page = Context::get('page');
		$output = executeQueryArray('sms.getSmsAddrNaviList', $args);

		return $output;
	}


	/**
	 * @brief 주소록 :: 주소록속에 전체 회원선택이 있는지 검사한다
	 **/
	function getSmsAllMember($module_srl = '', $bcate = '') {
		if(!$module_srl) $module_srl = Context::get('module_srl');
		if(!$bcate) $bcate = Context::get('bcate');
		if(!$bcate) $bcate = 'defaultaddr';

		$args->bcate = $bcate;
		$args->module_srl = $module_srl;
		$output = executeQuery('sms.getSmsAddrList',$args);

		if($output->data->bsort == 'alluser') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @berif 그룹 구함
	 **/
	function getSmsGroup() {
		$module_srl = Context::get('module_srl');

		if(!$module_srl && $this->module_srl) {
			$module_srl = $this->module_srl;
			Context::set('module_srl', $module_srl);
		}

		$bcate = Context::get('bcate');

		if(!$bcate) $bcate = 'defaultaddr';

		$args->bcate = $bcate;
		$args->bsort = 'group';
		$args->module_srl = $module_srl;
		$output = executeQueryArray('sms.getSmsGroupList',$args);

		if($output->data) {
			foreach($output->data as $val) {
				$list[$val->gseq] = $val->gseq;
			}

		}

		return $list;
	}


	/**
	 *@brief  member_srl이 속한 속한 그룹이 주소록에 추가 되었는지 알아봄
	 **/
	function getSmsMemberGroups($member_srl,$module_srl,$bcate) {
		$args->member_srl = $member_srl;
		$args->module_srl = $module_srl;
		$args->bcate      = $bcate;

		$output = executeQuery('sms.getSmsMemberGroups', $args);

		if(!$output->data) {
			return false;
		} else {
			return $output->data->group_srl;
		}
	}


	/**
	 * @brief 주소록 :: 그룹만 뿌림->분할
	 **/
	function getSmsGroupNavi() {
		$module_srl = Context::get('module_srl');

		if(!$module_srl && $this->module_srl) {
			$module_srl = $this->module_srl;
			Context::set('module_srl', $module_srl);
		}

		$bcate = Context::get('bcate');

		if(!$bcate) $bcate = 'defaultaddr';

		$args->bcate = $bcate;
		$args->module_srl = $module_srl;
		$args->page = Context::get('page');
		$output = executeQueryArray('sms.getSmsGroupNaviList', $args);

		return $output;
	}


	/**
	 * @brief 주소록 :: 회원정보만 뿌림
	 **/
	function getSmsMemberNavi() {
		$module_srl = Context::get('module_srl');

		if(!$module_srl) {
			$module_srl = Context::get('admin_send');
			Context::set('admin_send',$module_srl);
		}

		$bcate = Context::get('bcate');

		if(!$bcate) $bcate = 'defaultaddr';

		$args->bcate = $bcate;
		$args->page = Context::get('page');
		$args->module_srl = $module_srl;
		$output = executeQueryArray('sms.getSmsMemberNaviList', $args);

		return $output;
	}


	/**
	 * @brief 그룹멤버중에서 sms 사용금지시킨회원정보
	 **/
	function getGroupMemberUse($bcode,$gcode,$userId="",$bcate="") {
		$thisObj = new Object();

		$thisObj->bcode  = $bcode;
		$thisObj->gcode  = $gcode;
		if($bcate)$thisObj->bcate  = $bcate;
		else $thisObj->bcate = 'defaultaddr';


		if($userId) {
			$thisObj->userid = $userId;
			$output = executeQueryArray('sms.getSmsAdminGmUseList', $thisObj);
		} else $output = executeQueryArray('sms.getSmsAdminGmUseGroupList', $thisObj);

		return $output;
	}

	/**
	 * @brief 주소록 :: 실제 sms 전송시에 모든그룹 및 회원에게 전송시킴
	 **/
	function getSmsAddr($module_srl = '', $bcate = '') {
		if(!$module_srl)$module_srl = Context::get('module_srl');
		Context::set('module_srl', $module_srl);

		if(!$bcate) {
			$reqobj = Context::getRequestVars();
			if(!$reqobj->bcate)$bcate = 'defaultaddr';
			else $bcate = $reqobj->bcate;
		}

		$args->module_srl = $module_srl;
		$args->bcate   = $bcate;

		$output = executeQueryArray('sms.getSmsAddrList', $args);

		$oMemberModel = &getModel('member');

		if($output->data) {
			$j=0;
			foreach($output->data as $val) {
				/////// 주소정보중 전체회원선택일때 ///////

				if($val->bsort =='alluser') {

					$outGroup = executeQueryArray('sms.getMemberList',null);
					$user_info_list = array();
					foreach($outGroup->data as $skey => $sval) {
						$_tmp =	$sval;
						$_tmp->extra_vars =	unserialize($sval->extra_vars);
						$user_info_list[$_tmp->member_srl] = $_tmp;
					}

					if(count($user_info_list)) {
						foreach($user_info_list as $skey => $userInfo ) {
							// 금지시킨회원정보를 가져온뒤에 존재하면 정보를 저장하지 않는다 //
							$checkData = $this->getGroupMemberUse($module_srl,$val->gseq,$userInfo->user_id);
							if($checkData->data) continue;

							$userPcs = $this->pcsfield;
							$pcsValue = $userInfo->extra_vars->{$userPcs};
							$pcsValue = str_replace("|@|","",$pcsValue);
							if(strlen($pcsValue) < 10) continue;

							$outList[$j]['upcs'] = $pcsValue;
							$outList[$j]['uname']= $userInfo->user_name;
							$outList[$j]['uid']  = $userInfo->user_id;
							$j++;
						}
					}

				/////// 주소정보중 그룹일때 ///////
				} else if($val->bsort =='group') {
					// 그룹에 속한 회원정보를 가져온다 //

					$outGroup = executeQueryArray('sms.dispSmsAdminGmList', $val);
					$user_info_list = array();
					foreach($outGroup->data as $skey => $sval) {
						$_tmp =	$sval;
						$_tmp->extra_vars =	unserialize($sval->extra_vars);
						$user_info_list[$_tmp->member_srl] = $_tmp;
					}

					if(count($user_info_list)) {
						foreach($user_info_list as $skey => $userInfo ) {
							// 그룹중 금지시킨회원정보를 가져온뒤에 존재하면 정보를 저장하지 않는다 //
							$checkData = $this->getGroupMemberUse($module_srl,$val->gseq,$userInfo->user_id);
							if($checkData->data) continue;

							$userPcs = $this->pcsfield;
							$pcsValue = $userInfo->extra_vars->{$userPcs};
							$pcsValue = str_replace("|@|","",$pcsValue);
							if(strlen($pcsValue) < 10) continue;

							$outList[$j]['upcs'] = $pcsValue;
							$outList[$j]['uname']= $userInfo->user_name;
							$outList[$j]['uid']  = $userInfo->user_id;
							$j++;
						}
					}

				//// 주소정보중 일반회원일때 /////////
				} else {
					$userInfo  = $oMemberModel->getMemberInfoByMemberSrl($val->useq,0);

					$userPcs = $this->pcsfield;
					$pcsValue = '';
					if($userInfo) {
						foreach($userInfo as $skey=>$sval) {
							if($skey == $userPcs) {
								if($sval) {
									if(is_array($sval)) {
										foreach($sval as $sekey=>$seval) {
											$pcsValue.= $seval;
										}
									} else $pcsValue = $sval;

								}
							}
						}
					}

					$outList[$j]['upcs'] = $pcsValue;
					$outList[$j]['uname']= $val->uname;
					$outList[$j]['uid']  = $val->uid;
					$j++;

				}
			}
		}
		return $outList;


	}


	/**
	 * @brief sms 통계정보를 가져옴
	 **/
	function getSmsStatis($module_srl = '', $obj, $mode) {
		if($module_srl) {
			$obj->sms_domain = $this->domain;
			$obj->module_srl = $module_srl;

			if($mode == 'year') $result = executeQueryArray('sms.getSmsStatisYear', $obj);
			else if($mode == 'month') $result = executeQueryArray('sms.getSmsStatisMonth', $obj);
			else if($mode == 'day') $result = executeQueryArray('sms.getSmsStatisDay', $obj);
			else if($mode == 'choice') $result = executeQueryArray('sms.getSmsStatis', $obj);

		} else {

			$obj->sms_domain = $this->domain;
			if($mode == 'year') {
				$result = executeQueryArray('sms.getSmsDomainStatisYear', $obj);
			} else if($mode == 'month') $result = executeQueryArray('sms.getSmsDomainStatisMonth', $obj);
			else if($mode == 'day') $result = executeQueryArray('sms.getSmsDomainStatisDay', $obj);
			else if($mode == 'choice') $result = executeQueryArray('sms.getSmsDomainStatis', $obj);

		}

		return $result->data;
	}


	function getSmsGroupInfo($gid) {
		$oMemberModel = &getModel('member');
		$result = $oMemberModel->getGroup($gid);

		// 그룹의 총회원수를 구한다 //
		$g_obj->gseq = $gid;
		$outGroup = executeQuery('sms.dispSmsAdminGmList', $g_obj);
		$result->total = count($outGroup->data);

		return $result;
	}


	/**
	 * @brief 회원가입폼중 추가폼에서 전화번호 형식만을 가져옴
	 **/
	function getMemberPcsForm() {
		// 추가로 설정한 가입 항목 가져오기
		$oMemberModel = &getModel('member');
		$form_list = $oMemberModel->getJoinFormList();
		$pcs_list = array();

		if($form_list) {
			foreach($form_list as $key=>$val) {
				if($val->column_type == 'tel' || $val->column_type == 'text') {
					$pcs_list[$key] = array('column_name'  => $val->column_name,
									'column_title' => $val->column_title,
									'is_active'    => $val->is_active);
				}
			}
		}

		return $pcs_list;
	}

	/**
	 * @brief 포인트 업데이트
	 **/
	function pointUpdate($point = '', $retPoint = '', $check = '') {
		if($check == 'minus') $remain_point = $this->point - $point;
		unset($point_obj);

		if(!$retPoint) $retPoint = $remain_point;
		if(!$retPoint) $retPoint = 0;

		$point_obj->site_domain = $this->domain;
		$point_obj->site_id   = $this->auth_id;
		$point_obj->authKey   = $this->auth_key;
		$point_obj->point     = $retPoint;

		executeQuery('sms.updateSmsPoint', $point_obj);

		$this->point = $retPoint;
	}


	/**
	 * @brief 추적아이디 검사
	 **/
	function getSearchId($module_srl,$user_id) {
		$searchList = $this->getSmsAddr($module_srl, 'searchId');

		if(is_array($searchList)) {
			foreach($searchList as $val) {
				if($user_id == $val['uid']) {
					return $val['uid'];
					break; // ?
				}
			}
		}
		return false;
	}

	function getContentParse($content, $member_info) {
		if(!$member_info) return $content;

		$content = str_replace(
			array('#id#', '#name#', '#nick#', '#email#'),
			array($member_info->user_id, $member_info->user_name, $member_info->nick_name, $member_info->email_address),
			$content
		);

		return $content;
	}


}
