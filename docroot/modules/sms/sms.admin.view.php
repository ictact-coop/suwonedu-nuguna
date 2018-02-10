<?PHP
/**
 * @class  smsAdminView
 * @author Perbiz (http://perbiz.co.kr)
 * @brief  sms 모듈의 admin view class
 **/
class smsAdminView extends sms {
	var $jsonObject = null;

	function init() {
		// 모듈 설정 가져오기
		$oSmsModel = &getModel('sms');
		$module_config = $oSmsModel->getSmsModuleConfig('sms');
		if(!is_array($module_config->permit_addressbook)) $module_config->permit_addressbook = explode('|@|', $module_config->permit_addressbook);
		Context::set('module_config', $module_config);

		// 템플릿 경로 지정 (board의 경우 tpl에 관리자용 템플릿 모아놓음)
		$template_path = sprintf('%stpl/', $this->module_path);
		$this->setTemplatePath($template_path);

		///////// json_encode 함수가없는경우 php5.0 이하인경우 json 클래스를 삽입한다 //
		if($this->jsonObject==null) {
			include 'json.util.php';
			$this->jsonObject = new Services_JSON();
		}
	}


	/**
	 * @brief 초기 화면
	 **/
	function dispSmsAdminContents() {
		$oSmsModel = &getModel('sms');

		//fsockopen 함수 사용가능한지 체크한다
		if(!function_exists('fsockopen')) {
			$this->setTemplateFile('sms_not_socket');
			return;
		}

		if(!$this->authCheck()){
			if($this->install != null) {
				$this->setTemplateFile('sms_notInstall');
				return;
				exit;
			}
			$join_check = Context::get('join_check');

			$pcs_list = $oSmsModel->getMemberPcsForm();
			Context::set('pcs_list',$pcs_list);

			if($join_check=='join') {
				Context::set('auth','fail');
				Context::set('domain',$this->domain);
				$this->setTemplateFile('sms_auth');
				return;
			} else {
				Context::set('referer_url', $_SERVER['HTTP_REFERER']);

				$this->setTemplateFile('sms_login');
				return;
			}
		}

		Context::set('auth','success');
		Context::set('user_id',$this->auth_id);
		Context::set('auth_key',$this->auth_key);
		Context::set('point',$this->point);

		$obj = Context::getRequestVars();
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;


		if($obj->string) {
			if($obj->search == 'searchMid') $args->string = $obj->string;
			else if($obj->search == 'searchBrowser') $args->browser = $obj->string;
		}

		if($obj->buse =='yes')$args->buse = $obj->buse;
		$output = executeQueryArray('sms.getSmsAdminList', $args);


		$num = $output->total_count - (($output->page - 1) * $args->list_count);

		if(!$output->data) $output->data = array();
		if($output->data) {
			$i=0;
			foreach($output->data as $key=>$val) {
				foreach($val as $skey=>$sval) {
					$list[$i][$skey] = $sval;
				}
				if($val->module_srl) {
					$smsUse = $oSmsModel->getSmsBoardInfo($val->module_srl);
					$list[$i]['buse'] = $smsUse->buse;
					$memberAddrData = $oSmsModel->getSmsAddr($val->module_srl);
					$list[$i]['addr_member_total']  = count($memberAddrData);

				}
				$list[$i]['no'] = $num;
			$num--;
			$i++;
			}
		}

		// 템플릿에 쓰기 위해서 context::set
		Context::set('module', $obj->module);
		Context::set('act', $obj->act);
		Context::set('string', $obj->string);
		Context::set('buse', $obj->buse);

		Context::set('list', $list);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('index');
	}


	/**
	 * @brief sms 관리자 직접전송 폼
	 **/
	function dispSmsAdminDirectSend() {
		$oSmsModel = &getModel('sms');

		if(!$this->authCheck())return;

		Context::set('auth', 'success');
		Context::set('point', $this->point);
		Context::set('site_domain', $this->domain);

		$hour  = date('H');

		$memberAddrData = $oSmsModel->getSmsAddr('direct');
		Context::set('memberAmount', count($memberAddrData));

		if($hour>=12)$select_pm = 'selected';
		else $select_am = 'selected';
		Context::set('select_am', $select_am);
		Context::set('select_pm', $select_pm);

		$pcsarr = explode('-', $this->defaultPcs);
		Context::set('bpcs1', $pcsarr[0]);
		Context::set('bpcs2', $pcsarr[1]);
		Context::set('bpcs3', $pcsarr[2]);

		// 템플릿 파일 지정
		$this->setTemplateFile('sms_direct');
	}


	/**
	 * @brief 카테고리 정보리스트 출력
	 **/
	function dispSmsAdminCateInfo() {

		$module_srl = Context::get('module_srl');
		$oDocumentModel = &getModel('document');
		$category_xml_file = $oDocumentModel->getCategoryXmlFile($module_srl);

		Context::set('category_xml_file', $category_xml_file);
		Context::set('module_srl', $module_srl);
		Context::loadJavascriptPlugin('ui.tree');

		$this->setTemplateFile('sms_category_list');
	}


	/**
	 * @brief 도메인 추가 폼
	 **/
	function dispSmsAdminDomain() {
		if(!$this->authCheck())return;
	}


	/**
	 * @brief 통계정보를 구한다
	 **/
	function dispSmsAdminStatis() {
		if(!$this->authCheck())return;

		$oSmsModel = &getModel('sms');
		$mode = Context::get('mode');
		if(!$mode)$mode = 'day';

		$module_srl  = Context::get('module_srl');
		$obj->bcode  = $module_srl;
		$choice_year = Context::get('choice_year');
		$choice_month= Context::get('choice_month');
		$choice_day  = Context::get('choice_day');

		Context::set('auth','success');
		Context::set('mode',$mode);
		Context::set('module',Context::get('module'));
		Context::set('act',Context::get('act'));
		Context::set('module_srl',Context::get('module_srl'));

		$obj = Context::getRequestVars();


		// 년별검색
		if($mode == 'year') {
			$choice_year  = date('Y'); // 기본설정 10년전부터
			$year_end     = date('Y');
			$obj->start_date = $choice_year-10;
			$obj->end_date   = $year_end;

			$dataList = $oSmsModel->getSmsStatis($module_srl,$obj,$mode);

			$total_sms = 0;
			for($y=$choice_year-10; $y<=$year_end; $y++) {
				if($y == $year_end) {
					$select = 'selected';
				} else {
					$select = '';
				}

				$list[$y] = array('date' => $y, 'select' => $select, 'domain' => '', 'module_no' => '', 'succ' => '');

				if($dataList) {
					foreach($dataList as $val) {
						if( $val->day * 1 == $y) {
							$total_sms =  $total_sms + $val->succ;

							if(!$module_srl)$val->bcode = '';

							$list[$y] = array(
								'date' => $y,
								'domain' => $val->domain,
								'module_no' => $val->bcode,
								'succ' => $val->succ,
								'select' => $select,
								'nextMode' => '&mode=month&choice_year='.$y.'&module_name='.Context::get('module_name'),
								'module_name' => Context::get('module_name')
							);

							break;
						}
					}
				}
			}

		// 월별검색일때
		} else if($mode == 'month') {
			if(!$choice_year)$choice_year  = date('Y');
			$choice_month = date('m');
			$month_end    = 12;

			$obj->start_date = mktime(0,0,1,1,1,$choice_year);
			$obj->end_date   = mktime(23,23,59,$month_end,31,$choice_year);

			$dataList = $oSmsModel->getSmsStatis($module_srl,$obj,$mode);

			$total_sms = 0;
			for($m=1; $m<=$month_end; $m++) {

				if($m == $choice_month) $select = 'selected';
				else $select = '';
				$list[$m] = array('date'=>$m,'select'=>$select,'domain'=>'','module_no'=>'','succ'=>'');

				if($dataList) {
					foreach($dataList as $val) {
						if( $val->day * 1 == $m) {
							$total_sms =  $total_sms+$val->succ;
							if(!$module_srl)$val->bcode = '';
							$list[$m] = array('date'=>$m,'domain'=>$val->domain,'module_no'=>$val->bcode,'succ'=>$val->succ,'select'=>$select,'nextMode'=>'&mode=day&choice_year='.$choice_year.'&choice_month='.$m.'&module_name='.Context::get('module_name'),'module_name'=>Context::get('module_name'));
							break;
						}
					}
				}
			}

		// 일별검색일때
		} else if($mode == 'day') {

			if(!$choice_year)$choice_year  = date('Y');
			if(!$choice_month)$choice_month = date('m');
			if($choice_month>12) {
				$choice_month = 1;
				$choice_year = $choice_year+1;

			}

			$choice_day   = date('d');
			$day_end      = date('d',mktime(0,0,0,$choice_month+1,0,$choice_year)); // 말일

			$obj->start_date = mktime(0,0,1,$choice_month,1,$choice_year);
			$obj->end_date   = mktime(23,23,59,$choice_month,$day_end,$choice_year);

			$dataList = $oSmsModel->getSmsStatis($module_srl,$obj,$mode);

			$total_sms = 0;
			for($d=1; $d<=$day_end; $d++) {
				if($d == $choice_day) {
					$select = 'selected';
				} else {
					$select = '';
				}

				$list[$d] = array('date'=>$d,'select'=>$select,'domain'=>'','module_no'=>'','succ'=>'');

				if($dataList) {
					foreach($dataList as $val) {
						if( $val->day * 1 == $d) {
							$total_sms =  $total_sms+$val->succ;

							if(!$module_srl)$val->bcode = '';

							$list[$d] = array(
								'date' => $d,
								'domain' => $val->domain,
								'module_no' => $val->bcode,
								'succ' => $val->succ,
								'select' => $select,
								'nextMode' => '&mode=choice&choice_year='.$choice_year.'&choice_month='.$choice_month.'&choice_day='.$d.'&module_name='.Context::get('module_name'),
								'module_name'=>Context::get('module_name')
							);

							break;
						}
					}
				}
			}

		// 선택한 날 검색
		} else if($mode == 'choice')  {
			if(!$choice_year)$choice_year  = date('Y');
			if(!$choice_month)$choice_month = date('m');

			if($choice_month>12) {
				$choice_month = 1;
				$choice_year = $choice_year + 1;
			}
			if(!$choice_day)$choice_day   = date('d');

			$lastDay = date('d', mktime(0, 0, 1, $choice_month + 1, 0, $choice_year));

			if($choice_day > $lastDay) {
				$choice_month = $choice_month + 1;
				if($choice_month>12) {
					$choice_month = 1;
					$choice_year = $choice_year + 1;
				}
				$choice_day = 1;
			}

			$obj->start_date = mktime(0, 0, 1, $choice_month, $choice_day, $choice_year);
			$obj->end_date   = mktime(23, 23, 59, $choice_month, $choice_day, $choice_year);

			$dataList = $oSmsModel->getSmsStatis($module_srl,$obj,$mode);
			if($dataList) {
				foreach($dataList as $val) {
					$total_sms =  $total_sms+$val->succ;
					$list[] = array('date'=>date('Y-m-d H:i:s', $val->rdate), 'domain' => $val->domain, 'module_no' => $val->bcode, 'succ' => $val->succ, 'module_name' => Context::get('module_name'));
				}
			}
		}

		Context::set('list', $list);
		Context::set('total_sms', $total_sms);
		Context::set('choice_year', $choice_year);
		Context::set('choice_month', $choice_month);
		Context::set('choice_day', $choice_day);
		Context::set('module_name', Context::get('module_name'));

		if($mode!='year') Context::set('choice_year_text', $choice_year);
		if($mode=='day' || $mode=='choice') Context::set('choice_month_text', '-'.$choice_month);
		if($mode=='choice') Context::set('choice_day_text', '-'.$choice_day);

		$this->setTemplateFile('sms_statis');
	}


	/**
	 * @brief 게시판 환경설정을 복사
	 **/
	function dispSmsAdminCopySetting() {
		//전체게시판 목록을 불러옴
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$args->modu_srl = Context::get('bcode');
		$output = executeQueryArray('sms.getSmsAdminList', $args);
		if(!$output->data)$output->data = array();

		Context::set('bcode', Context::get('bcode'));
		Context::set('btitle', Context::get('btitle'));

		Context::set('list', $output->data);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);

		// 레이아웃을 팝업으로 지정
		$this->setLayoutFile('popup_layout');

		// 템플릿 파일 지정
		$this->setTemplateFile('sms_copy_setting');
	}


	/**
	 * @brief 설정
	 * FIXME
	 **/
	function dispSmsAdminAdminModify() {
		$oSmsModel = &getModel('sms');
		if(!$this->authCheck())return;

		if(Context::get('delMode') == 'delete' && Context::get('del_domain')) {
			$oSmsController = getAdminController('sms');
			$oSmsController->deleteSmsAdminAddDomain();
		}

		// 그룹을 가져옴
		$oMemberModel = &getModel('member');
		$group_list = $oMemberModel->getGroups(0);
		Context::set('group_list', $group_list);

		$pcsarr = explode('-', $this->defaultPcs);
		$pcs_list = $oSmsModel->getMemberPcsForm();

		Context::set('auth', 'success');
		Context::set('pcsfield', $this->pcsfield);
		Context::set('pcs_list', $pcs_list);
		Context::set('user_pcs1', $pcsarr[0]);
		Context::set('user_pcs2', $pcsarr[1]);
		Context::set('user_pcs3', $pcsarr[2]);
		Context::set('site_domain', $this->domain);
		Context::set('auth_date', date('Y-m-d', $this->auth_date));
		Context::set('authId', $this->auth_id);
		Context::set('authKey', $this->auth_key);

		$args->user_id = $this->auth_id;
		$args->sort_index = 'regdate';
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$output = executeQueryArray('sms.dispSmsAdminAddDomain', $args);

		Context::set('list', $output->data);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);

		$this->setTemplateFile('sms_setting');
	}


	/**
	 * @brief 팝업창에서 아작스로 그룹에 대한 회원정보 가져옴
	 **/
	function selectSmsAdminGroupMember() {
		$obj = Context::getRequestVars();
		$obj->list_count = 10;
		$output = executeQueryArray('sms.dispSmsAdminGmListNavi', $obj);

		$oMemberModel = &getModel('member');
		$oSmsModel = &getModel('sms');

		$mList = array();

		// FIXME
		if($output->data) {
			$h=0;
			$pcsField = $this->pcsfield;
			foreach($output->data as $val) {
				$userInfo = $oMemberModel->getMemberInfoByMemberSrl($val->member_srl,0);
				foreach($userInfo as $sekey=>$seval) {
					$mList[$h][$sekey] = $seval;
					if($sekey == $pcsField) {
						$pcsValue = '';
						if($seval) {
							if(is_array($seval)) {
								foreach($seval as $skey=>$sval) {
									$pcsValue.= $sval.'-';
								}
								$pcsValue = substr($pcsValue,0,-1);

							} else $pcsValue = $seval;
						}

						$mList[$h]['upcs_return_val'] = $pcsValue;
					}

					if($sekey == 'user_id') {
						$obj->gcode = $obj->uid;
						$use_check = $oSmsModel->getGroupMemberUse($obj->bcode,$obj->gcode,$seval);
						//print_r($use_check);

						if($use_check->data)$mList[$h]['use_check'] = 'no';
						else $mList[$h]['use_check'] = 'yes';
					}
				}
			$h++;
			}
		}

		$minusNum = $output->total_count - (($output->page -1) * $obj->list_count);
		//// ajax 페이징을 쓰기위해 /////////
		$result = array('mlist'      => $mList,
						'total_count'=> $output->total_count,
						'total_page' => $output->total_page,
						'page'       => $output->page,
						'minusNum'   => $minusNum,
						'navi' =>$output->page_navigation
		);


		$result = $this->jsonObject->encode($result);
		$this->setMessage($result);
	}


	/**
	 * @brief 팝업 내용중 주소록::회원
	 **/
	function dispSmsAdminMemberAddrSetting() {
		$module_srl = Context::get('module_srl');
		if(!$module_srl) {
			if(Context::get('admin_send')) {
				$module_srl = Context::get('admin_send');
				Context::set('admin_send', $module_srl);
			} else if(Context::get('bcode')) {
				$module_srl = Context::get('bcode');
			}
		}

		$addr_use = Context::get('addr_use');
		Context::set('addr_use', $addr_use);
		Context::set('module_srl', $module_srl);
		Context::set('bcode', $module_srl);
		Context::set('act', Context::get('act'));

		$reqobj = Context::getRequestVars();
		if(!$reqobj->bcate)$bcate = 'defaultaddr';
		else $bcate = $reqobj->bcate;
		Context::set('bcate',$bcate);

		$string = Context::get('string');
		if($string) $args->search_string = $string;

		////////////// 회원 정보를 구하기 위해 //////////////////
		// 주소록에 추가된 회원과 그렇지 않은 회원을 구분한다 ///
		// default => 추가된회원(addrin)

		$oSmsModel = &getModel('sms');
		$allInfo = $oSmsModel->getSmsAllMember($module_srl,$bcate);//전체회원선택사항 검사

		$oMemberModel = &getModel('member');
		$args->page = Context::get('page');
		$args->list_count = 10;
		$args->page_count = 10;

		$output = executeQueryArray('sms.getSmsMemberList', $args);

		$pcsField = $this->pcsfield;
		$h=0;
		$incheckObj->module_srl = $module_srl;
		$incheckObj->bcate  = $bcate;

		$num = $output->total_count - (($output->page - 1) * $args->list_count);

		if(!is_array($output->data))$output->data = array();
		foreach($output->data as $val) {
			$pcsValue = '';
			$sms_srl  = '';
			$user_check  = '';
			$gseq     = '';

			// FIXME
			$userInfo = $oMemberModel->getMemberInfoByMemberSrl($val->member_srl,0);
			if($userInfo) {
				foreach($userInfo as $skey=>$sval) {
					$mList[$h][$skey] = $sval;
					if($skey == $pcsField) {
						if($sval) {
							if(is_array($sval)) {
								foreach($sval as $sekey=>$seval) {
									$pcsValue.= $seval.'-';
								}
								$pcsValue = substr($pcsValue,0,-1);
							} else $pcsValue = $sval;
						}
					}
				}
			}
			////// 회원전체선택일경우에 ///////////
			// 전체선택일경우 모든회원은 alluser라는 가상의 그룹에 포함되는걸로 인식한다 //////////
			if($allInfo) {
				//전체회원선택일 경우엔 그룹의 gseq값을 'alluser'로 한다
				$checkResult = $oSmsModel->getGroupMemberUse($module_srl,'alluser',$val->user_id,$bcate);
				if(!$checkResult->data) {
					$user_check = 'allIn'; // 추가된 그룹중에서 제외된 회원이 아니면
				} else {
					$user_check = 'allOut';
				}
				$gseq = 'alluser';
				Context::set('alluserCheck','allIn');
			} else {
				$groupCheck = $oSmsModel->getSmsMemberGroups($val->member_srl,$module_srl,$bcate); //회원의 그룹정보??
				// 회원이 속한 그룹이 주속록의 추가된 그룹에 포함되어있는지 검사
				if($groupCheck) {
					$checkResult = $oSmsModel->getGroupMemberUse($module_srl,$groupCheck,$val->user_id,$bcate);
					if(!$checkResult->data) {
						$user_check = 'groupIn'; // 추가된 그룹중에서 제외된 회원이 아니면
					} else {
						$user_check = 'groupOut';
					}
					$gseq = $groupCheck;

				}
				$incheckObj->useq = $val->member_srl;
				$addrInCheck = executeQuery("sms.getSmsMemberAddrList",$incheckObj);

				if($addrInCheck->data){
					$user_check = 'userIn';
					$sms_srl = $addrInCheck->data->sms_srl;
				}
			}

			$mList[$h] = array('uid'=>$val->user_id,
								'unick'=>$val->nick_name,
								'uname'=>$val->user_name,
								'useq'=>$val->member_srl,
								'upcs'=>$pcsValue,
								'sms_srl'=>$sms_srl,
								'no'    => $num,
								'gseq'=>$gseq,
								'user_check' => $user_check,
								);
			$num--;
			$h++;
		}

		// 템플릿에 쓰기 위해서 context::set
		Context::set('search',$search);
		Context::set('string',$string);

		Context::set('addr_use_checked','');
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('list', $mList);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('sms_addr_member_view');

	}
	/**
	 * @brief 팝업 내용중 주소록::그룹
	 **/
	function dispSmsAdminGroupAddrSetting() {

		$module_srl = Context::get('module_srl');
		if(!$module_srl) {
			if(Context::get('admin_send')) {
				$module_srl = Context::get('admin_send');
				Context::set('admin_send',$module_srl);
			} else if(Context::get('bcode')) {
				$module_srl = Context::get('bcode');
			}
		}

		Context::set('module_srl', $module_srl);
		Context::set('bcode',$module_srl);

		$bcate = Context::get('bcate');
		if(!$bcate)$bcate = 'defaultaddr';
		Context::set('bcate',$bcate);

		$oSmsModel = &getModel('sms');
		$groupInfo = $oSmsModel->getSmsAllMember();//전체회원선택사항 검사

		if($groupInfo) {
			Context::set('alluserCheck','allIn');

		} else {
			//////////전체 그룹목록 /////////////
			$args->page = Context::get('page');
			$args->list_count = 10;
			$args->page_count = 10;

			$search = Context::get('search');
			$string = Context::get('string');
			if($search && $string)$args->$search = $string;

			$output = executeQueryArray("sms.dispSmsGroupNaviList",$args);
			$obj->bcode      = $module_srl;
			$obj->bsort      = 'group';
			$obj->bcate   = $bcate;

			$num = $output->total_count - (($output->page - 1) * $args->list_count);

			if($output->data) {
				foreach($output->data as $val) {
					$obj->uid = $val->group_srl;
					$groupData = executeQuery("sms.getSmsAdminAddrGroupCheck",$obj);
					if($groupData->data->srl)$groupIn = 'groupIn';
					else $groupIn = '';

					$gList[] = array('gseq'  => $val->group_srl,
								'title' => $val->title,
								'no'    => $num,
								'groupIn'=>$groupIn);
				$num--;
				}
			}
			// 템플릿에 쓰기 위해서 context::set
			Context::set('string',$string);
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('list', $gList);
			Context::set('page_navigation', $output->page_navigation);
		}
		 // 템플릿 파일 지정
		$this->setTemplateFile('sms_addr_group_view');

	}
}
