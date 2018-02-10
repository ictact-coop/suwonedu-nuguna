<?php

class mailing_freeAdminController extends mailing_free {

    function init() {
    }

    function procMailing_freeAdminSendMail() {
        $args = Context::gets('sender_nickname','sender_email','title','content');            
        $oMail = new Mail();
        $oMail->setTitle($args->title);
        
        $m_allow = $args->m_allow;
        $content = nl2br($args->content);
        
        $oMail->setContent($content);
        $oMail->setSender($args->sender_nickname, $args->sender_email);
        
        $cnt = 0;
        
		    $oModuleModel = &getModel('module');
		    $config = $oModuleModel->getModuleConfig('mailing_free');

		    $args->is_mailing = 'Y';
		    $output = executeQueryArray('mailing_free.getEmailAddrList');

		    if(!$output->toBool()) {
		        return $output;
		    }

		    if($output->data){
		        $member_list = $output->data;
		        foreach($member_list as $m){
		            if($m->allow_mailing == 'Y'){
		                $oMail->setReceiptor($m->nick_name, $m->email_address);
		                $oMail->send();
		                $cnt++;
		            }
		          }
		    }

        $this->setMessage( sprintf(Context::getLang('msg_send_success'), $cnt) );
    }

}
?>
