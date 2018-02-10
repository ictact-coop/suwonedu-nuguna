<?php

    class mailing_free extends ModuleObject {

        function moduleInstall() {

            $oModuleController = &getController('module');
            $oModuleController->insertActionForward('mailing_free', 'view', 'dispMailing_freeAdminInsert');

            return new Object();
        }

        function checkUpdate() {
            $oModuleModel = &getModel('module');
            $act = $oModuleModel->getActionForward('dispMailing_freeAdminInsert');
            if(!$act) return true;
            
            return false;
        }

        function moduleUpdate() {
        	$this->moduleInstall();
            return new Object(0, 'success_updated');
        }

        function recompileCache() {
        }
    }
?>