<?php
    /**
     * @file	bannermgm.admin.controller.php
     * @class	bannermgmAdminController
     * @brief	Bannermgm Admin Controller
     * @author	ChoiHeeChul, KimJinHwan, ParkSunYoung
     **/
	class bannermgmModel extends bannermgm {
		/**
         * @brief 초기화
         **/
		function init() {
		
		}
	
		/**
         * @brief 배너번호에 해당하는 배너 정보를 가져온다
         * @param $bannermgm_srl 배너의 번호
         * @return 가져온 배너의 정보
         **/
		function getBannerInfo($bannermgm_srl)
		{
			$args->bannermgm_srl = $bannermgm_srl;
			$output = executeQuery('bannermgm.getBannermgm', $args);
			
			// 오류 발생시 멈춤
			if(!$output->toBool()) return $output;
			
			return $output->data;
	    }
	}
?>