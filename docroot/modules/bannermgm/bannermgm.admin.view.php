<?php
    /**
     * @file	bannermgm.admin.view.php
     * @class	bannermgmAdminView
     * @brief	Bannermgm Admin View
     * @author	ChoiHeeChul, KimJinHwan, ParkSunYoung
     **/
	class bannermgmAdminView extends bannermgm {
		/**
         * @brief 초기화
         **/
		function init() {
			// 템플릿 경로 지정 (board의 경우 tpl에 관리자용 템플릿 모아놓음)
			$template_path = sprintf("%stpl/",$this->module_path);
			$this->setTemplatePath($template_path);
		}
		
		/**
		* @brief 어드민 페이지에서 등록된 배너의 목록을 보여줌. 
		**/
		function dispBannermgmAdminContent() {
			// 등록된 베너의 리스트를 가져온다
			// board 모듈을 참고하였음
			$args->sort_index = "bannermgm_srl";
			$args->page = Context::get('page');
			$args->list_count = 20;
			$args->page_count = 10;
	
			$output = executeQueryArray('bannermgm.getBannermgmList', $args);
	
			// 템플릿에 가져온 배너의 리스트를 세팅한다
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('bannermgm_list', $output->data);
			Context::set('page_navigation', $output->page_navigation);
			
			// 템플릿 파일 지정(index.html)
			$this->setTemplateFile('index');
		}
	
		/**
		* @brief 배너관리 세부정보/수정 화면
		**/
		function dispBannermgmAdminInfo() {
			$this->dispBannermgmAdminInsert() ;
		}
	
		/**
		* @brief 배너관리 수정/입력 폼화면
		**/
		function dispBannermgmAdminInsert() {
			// 등록된 배너의 번호 가져오기
			$bannermgm_srl = Context::get('bannermgm_srl');
			
			// 이미 등록된 배너이면 템플릿에 정보들을 세팅
			if (isset($bannermgm_srl))
			{
				$args->bannermgm_srl = $bannermgm_srl;
				$output = executeQueryArray('bannermgm.getBannermgm', $args);
				Context::set('banner_name',$output->data[0]->banner_name);
				Context::set('link_url',$output->data[0]->link_url);
				$oModuleModel = &getModel('module');
				$target_module = $oModuleModel->getModuleInfoByModuleSrl($output->data[0]->module_srl);
				Context::set('target_module',$target_module);
				Context::set('image_path', $output->data[0]->image_path);
				$tmp_name = explode("/", $output->data[0]->image_path);
				$file_name = $tmp_name[count($tmp_name)-1];
				
				Context::set('file_name', $file_name);
			}
			
			// 템플릿 파일 지정(banner_insert.html)
			$this->setTemplateFile('banner_insert');
		}
	}
?>