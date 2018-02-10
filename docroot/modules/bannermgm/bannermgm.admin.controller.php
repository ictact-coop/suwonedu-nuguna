<?php
    /**
     * @file	bannermgm.admin.controller.php
     * @class	bannermgmAdminController
     * @brief	Bannermgm Admin Controller
     * @author	ChoiHeeChul, KimJinHwan, ParkSunYoung
     **/
	class bannermgmAdminController extends bannermgm {
		/**
         * @brief 초기화
         **/
		function init() {
		}
		
		/**
		 * @brief 어드민 페이지에서 입력한 배너 정보 저장및 업데이트
		 **/
		function procBannermgmAdminUpdate() {
			// 어드민 페이지에서 입력한 값 가져오기
			$source_args = Context::getRequestVars();
			
			// 가져온 값 저장
			$args->bannermgm_srl = $source_args->bannermgm_srl;
			$args->link_url = $source_args->link_url;
			$args->banner_name = $source_args->banner_name;
			$args->regdate = $source_args->regdate;
			$args->updatedate = $source_args->updatedate;
			$args->image_path = $source_args->image_path;
			
			// 이미 존재하는 베너일경우 업데이트
			if (isset($args->bannermgm_srl) )
			{
				$output = executeQuery('bannermgm.updateBannermgm',$args);
			}
			// 존재하지 않을경우 생성
			else
			{	
				$args->bannermgm_srl = getNextSequence();
				$output = executeQuery('bannermgm.insertBannermgm',$args);
			}
			$this->setMessage('success_registed');
		}
		
		/**
		 * @brief 어드민 페이지에서 등록된 배너 삭제
		 **/
		function procBannermgmAdminDelete() {
			// 삭제할 배너의 번호를 가져와서 삭제
			$args->bannermgm_srl = Context::get('bannermgm_srl');
			$output = executeQuery('bannermgm.deleteBannermgm',$args);
			
			$this->setMessage('success_deleted');
		}
		
		/**
		 * @brief 어드민 페이지에서 배너에 사용할 이미지를 등록하면,\n
		 * 이미지 파일을 서버에 옮겨 저장한다.
		 **/
		function procBannermgmAdminUploadBanner() {
			// 업로드할 파일 가져오기
			$target = Context::get('target');
			$target_file = Context::get($target);

			// 필수 요건이 없거나 업로드된 파일이 아니면 오류 발생
			if(!$target_file || !is_uploaded_file($target_file['tmp_name']) || !preg_match('/\.(gif|jpeg|jpg|png|swf)/i',$target_file['name'])) {
				Context::set('error_messge', Context::getLang('msg_invalid_request'));

			// 요건을 만족하고 업로드된 파일이면 지정된 위치로 이동
			} else {
				$tmp_arr = explode('.',$target_file['name']);
				$ext = $tmp_arr[count($tmp_arr)-1];
				$banner_srl = Context::get('banner_srl');
			
				$path = sprintf('./files/attach/banner_images/%s/', date("YmdHis"));
				$filename = sprintf('%s%s', $path, $target_file['name']);

				if(!is_dir($path)) FileHandler::makeDir($path);

				move_uploaded_file($target_file['tmp_name'], $filename);
		
				Context::set('filename', $filename);
			}
	
			$this->setTemplatePath($this->module_path.'tpl');
			$this->setTemplateFile('image_file_uploaded');
		}
	}
?>