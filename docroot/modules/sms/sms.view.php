<?PHP
/**
 * @class  smsAdminView
 * @author Perbiz (http://perbiz.co.kr)
 * @brief  sms 모듈의  view class
 **/
class smsView extends sms {

	function init() {
		// 모듈 설정 가져오기
		$oSmsModel = &getModel('sms');
		$module_config = $oSmsModel->getSmsModuleConfig('sms');
		$permit_group_list = explode('|@|', $module_config->permit_addressbook);

		$logged_info = Context::get('logged_info');

		$this->permit_group = false;
		if($logged_info && $logged_info->is_admin != 'Y') {
			if($permit_group_list) {
				foreach($logged_info->group_list as $srl => $group) {
					if(in_array($srl, $permit_group_list)) $this->permit_group = true;
				}
			}
		}
		if($logged_info->is_admin == 'Y') $this->permit_group = true;
	}


	/**
	 * @brief 초기 게시판별 설정 폼
	 * FIXME
	 **/
	function triggerDispSmsAdditionSetup(&$obj) {
		if(!$this->authCheck())return false;
		else Context::set('auth','success');

		$module_srl = Context::get('module_srl');
		if(!$module_srl && $this->module_srl) {
			$module_srl = $this->module_srl;
		}
		Context::set('module_srl', $module_srl);
		$oSmsModel  = &getModel('sms');

		//////////// 게시판에 설치된 모듈별로 sms셋팅정보 구하기 ////////////////
		if($module_srl) {
			$sms_info  = $oSmsModel->getSmsBoardInfo($module_srl);

			Context::set('bcode', $module_srl);

			if($sms_info) { // 설정정보가 저장되엇을때
				Context::set('sms_info', $sms_info);
				Context::set('buse_'.$sms_info->buse, 'checked');
				Context::set('bhead_'.$sms_info->bhead_use, 'checked');

				if($sms_info->buse == 'yes')$boardUseDis = 'block';
				else $boardUseDis = 'none';

				if($sms_info->bwrite_sms_use) Context::set('bwrite_sms_'.$sms_info->bwrite_sms_use,'checked');
				else  Context::set('bwrite_sms_yes','checked');

				Context::set('bwrite_'.$sms_info->bwrite_use,'checked');

				if($sms_info->bwrite_sms_modify_use == 'yes') {
					Context::set('bwrite_sms_modify_use','checked');
				}

				Context::set('breply_'.$sms_info->breply_use,'checked');
				Context::set('breply_sms_'.$sms_info->breply_sms_use,'checked');

				if($sms_info->breply_sms_modify_use == 'yes') {
					Context::set('breply_sms_modify_use','checked');
				}
				if($sms_info->bwrite_use == 'no')Context::set('bwrite_dis','none');
				if($sms_info->breply_use == 'no')Context::set('breply_dis','none');
				if($sms_info->bhead_use == 'no')Context::set('bhead_dis','none');
				if($sms_info->breply_sms_use == 'no'){
					Context::set('breply_sms_dis','none');
				}
				if($sms_info->bwrite_sms_use == 'no')Context::set('bwrite_sms_dis','none');

				$pcs = $sms_info->bpcs;
				$sear_pcs = explode('-',$sms_info->searchid_pcs);
				$sear_pcs1 = $sear_pcs[0];
				$sear_pcs2 = $sear_pcs[1];
				$sear_pcs3 = $sear_pcs[2];

				if($sms_info->admin_use == 'yes')$admin_check = 'checked';
				else $admin_check = '';

				if($sms_info->user_use == 'yes') {
					$user_check = 'checked';
					$extra_dis = 'block';
				} else {
					$user_check = '';
					$extra_dis = 'none';
				}

				$extraId = $sms_info->extraid;

				if($sms_info->log_use == 'yes')$log_yes = 'checked';
				else $log_no = 'checked';

				if($sms_info->bpcs_use == 'yes') {
					$pcs_yes = 'checked';
					$pcs_dis = 'block';
				} else {
					$pcs_no = 'checked';
					$pcs_dis = 'none';
				}

				if($sms_info->cate_use == 'yes') {
					$cate_yes = 'checked';
					$cate_dis = 'block';
					$addr_dis = 'none';

					/*
					$bcate_arr = explode(','.$sms_info->bbcate);
					for($k=0; $k<=count($bcate_arr); $k++) {
						$bcate_list[$bcate_arr[$k]]= $bcate_arr[$k];
					}
					*/

				} else {
					$cate_no = 'checked';
					$cate_dis = 'none';
					$addr_dis = 'block';
				}

				/// 추적 아이디 사용부분
				if($sms_info->searchid_use == 'yes') {
					$searchId_yes = 'checked';
					$searchId_dis = 'block';

				} else {
					$searchId_no = 'checked';
					$searchId_dis = 'none';
				}

				//추적 아이디 설정회원정보
				$searchIdData = $oSmsModel->getSmsAddr($module_srl,'searchId');



			} else { // 초기의 경우
				$pcs = $this->defaultPcs;

				$boardUseDis = 'none';
				Context::set('buse_no','checked');
				Context::set('bwrite_sms_yes','checked');
				Context::set('bwrite_no','checked');
				Context::set('breply_no','checked');
				Context::set('bwrite_dis','none');
				Context::set('breply_dis','none');
				Context::set('bhead_dis','none');
				Context::set('breply_sms_yes','checked');
				Context::set('bwrite_sms_yes','checked');
				Context::set('bhead_no','checked');

				$admin_check = '';
				$user_check = '';
				$extra_dis = 'none';
				$extraId = '';
				$log_no = 'checked';

				$pcs_yes = 'checked';
				$pcs_dis = 'block';

				$cate_no = 'checked';
				$cate_dis = 'none';
				$addr_dis = 'block';

				$searchId_no = 'checked';
				$searchId_dis= 'none';
				$searchIdData = array();
			}
			$pcsarr = explode('-',$pcs);

			//추가된 세팅
			Context::set('boardUseDis',$boardUseDis);
			Context::set('admin_check',$admin_check);
			Context::set('user_check',$user_check);
			Context::set('extra_dis',$extra_dis);
			Context::set('extraid',$extraId);
			Context::set('log_no',$log_no);
			Context::set('log_yes',$log_yes);
			Context::set('pcs_yes',$pcs_yes);
			Context::set('pcs_no',$pcs_no);
			Context::set('pcs_dis',$pcs_dis);
			Context::set('cate_yes',$cate_yes);
			Context::set('cate_no',$cate_no);
			Context::set('cate_dis',$cate_dis);
			Context::set('addr_dis',$addr_dis);
			Context::set('searchId_yes',$searchId_yes);
			Context::set('searchId_no',$searchId_no);
			Context::set('searchId_dis',$searchId_dis);

			Context::set('searchTotal',count($searchIdData));


			//확장변수를 가져오는 부분
			$oDocumentModel = &getModel('document');
			$extra_keys = $oDocumentModel->getExtraKeys($module_srl);
			$extraList = array();
			if($extra_keys) {
				foreach($extra_keys as $val) {
					if($val->type == 'checkbox') $extraList[]=$val;
				}
			}


			Context::set('extra_keys',$extraList);
			Context::set('extra_total',count($extraList));

			// 게시판 설정정보중에서 카테고리분류를 사용하는지 체크하기 위해
			$oModuleModel = &getModel('module');
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			Context::set('module_info',$module_info);

			// 카테고리리스트를 가져오는 부분
			$categoryList = $oDocumentModel->getCategoryList($module_srl);
			if(is_array($categoryList)) {
				foreach($categoryList as $val) {
					$cateUserList[$val->category_srl] = count($oSmsModel->getSmsAddr($module_srl,$val->category_srl));

				}
			}

			Context::set('cate_user_list',$cateUserList);
			Context::set('cate_list',$categoryList);
			Context::set('cate_total',count($categoryList));

			Context::set('bpcs1',$pcsarr[0]);
			Context::set('bpcs2',$pcsarr[1]);
			Context::set('bpcs3',$pcsarr[2]);

			Context::set('sear_pcs1',$sear_pcs1);
			Context::set('sear_pcs2',$sear_pcs2);
			Context::set('sear_pcs3',$sear_pcs3);

		}

		$memberAddrData = $oSmsModel->getSmsAddr();

		Context::set('memberAmount',count($memberAddrData));
		Context::set('atag',Context::get('atag'));


		$oTemplate = &TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl', 'sms_module_config');
		$obj .= $tpl;

		return new Object();
	}




	/**
	 * @brief 팝업 내용중 주소록::회원
	 **/
	function dispSmsMemberAddrSetting() {
		if(!$this->permit_group) {
			$this->dispSmsMessage('msg_not_permitted');
			return;
		}

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

		$this->setTemplatePath('./modules/sms/tpl');
		// 레이아웃을 팝업으로 지정
		$this->setLayoutPath('./common/tpl');
		$this->setLayoutFile('popup_layout');
		// 템플릿 파일 지정
		$this->setTemplateFile('sms_addr_member_view_na');

	}
	/**
	 * @brief 팝업 내용중 주소록::그룹
	 **/
	function dispSmsGroupAddrSetting() {
		if(!$this->permit_group) {
			$this->setMessage('msg_not_permitted');
			return;
		}


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

		$this->setTemplatePath('./modules/sms/tpl');
		// 레이아웃을 팝업으로 지정
		$this->setLayoutPath('./common/tpl');
		$this->setLayoutFile('popup_layout');
		 // 템플릿 파일 지정
		$this->setTemplateFile('sms_addr_group_view_na');

	}



	/**
	 * @brief 메세지 출력
	 **/
	function dispSmsMessage($msg_code) {
		$msg = Context::getLang($msg_code);
		if(!$msg) $msg = $msg_code;
		Context::set('message', $msg);
		$this->setLayoutFile('popup_layout');
		$this->setTemplatePath('./modules/sms/tpl');
		$this->setTemplateFile('message');
	}

}
