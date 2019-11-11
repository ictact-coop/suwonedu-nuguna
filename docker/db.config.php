<?php if(!defined("__XE__")) exit();
$db_info = (object)array (
  'master_db' => 
  array (
    'db_type' => 'mysqli',
    'db_port' => '3306',
    'db_hostname' => 'db',
    'db_userid' => 'root',
    'db_password' => 'nuguna',
    'db_database' => 'nuguna',
    'db_table_prefix' => 'xe_',
  ),
  'slave_db' => 
  array (
    0 => 
    array (
      'db_type' => 'mysqli',
      'db_port' => '3306',
      'db_hostname' => 'db',
      'db_userid' => 'root',
      'db_password' => 'nuguna',
      'db_database' => 'nuguna',
      'db_table_prefix' => 'xe_',
    ),
  ),
  'default_url' => 'http://localhost:8888/',
  'use_rewrite' => 'Y',
  'time_zone' => '+0900',
  'use_prepared_statements' => 'Y',
  'qmail_compatibility' => 'N',
  'use_db_session' => 'N',
  'use_ssl' => 'none',
  'use_sso' => 'N',
  'use_cdn' => 'N',
  'use_html5' => 'N',
  'use_mobile_view' => 'N',
  'admin_ip_list' => NULL,
  'sitelock_whitelist' => 
  array (
    0 => '',
  ),
);