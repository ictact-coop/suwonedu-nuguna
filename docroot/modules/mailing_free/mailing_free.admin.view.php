<?php

    class mailing_freeAdminView extends mailing_free {

        function init() {
        }

        function dispMailing_freeAdminInsert() {

            $oModuleModel = &getModel('module');
            $config = $oModuleModel->getModuleConfig('mailing_free');
            Context::set('config',$config);

            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('mailing_free');
        }

    }
?>
