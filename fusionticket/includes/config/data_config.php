<?php

/**
 *
 *
 * @version $Id: data_config.php 1840 2012-07-30 18:24:52Z nielsNL $
 * @copyright 2011
 */

$config = array();

$config['group_organizer'] = array(
  'organizer_name' =>     array('Demo Owner',  'text','*'),
  'organizer_address' =>  array('5678 Demo St',  'text','*'),
  'organizer_plz' =>      array('11001',  'text','*'),
  'organizer_ort' =>      array('Demo Town',  'text','*'),
  'organizer_state' =>    array('DT',  'text','*'),
  'organizer_country' =>  array('US',  'select','*','getCountries'),
  'organizer_email' =>    array('info@fusionticket.test',  'text','*'),
  'organizer_fax' =>      array('(555) 555-1215',  'text',''),
  'organizer_phone' =>    array('(555) 555-1214',  'text','*'),
 // 'organizer_place' =>    array('',  'text',''),
  'organizer_currency' => array('USD',  'select','*', 'getCurrencies'),
  'organizer_logo' =>     array('', 'file','^'),
);

$config['group_common'] = array(
  'secure_site'      => array(0,'yesno','*'),
  'UseRewriteURL'    => array(0,'yesno','*'),
  'theme_name'       => array('default','select','*','getThemeList'),
//  'useUTF8'          => array(0,'yesno'),
  'input_time_type'  =>  array(null,'select','*',array('12'=>'time_type_12','24'=>'time_type_24')),
  'input_date_type'  =>  array(null,'select','*',array('dmy'=>'date_type_dmy',
                                                      'mdy'=>'date_type_mdy',
                                                      'ymd'=>'date_type_ydm',
                                                      'ydm'=>'date_type_dmy')),
  'timezone'         => array(null,'select','*', 'gettimezones'),
//  'rowcount'         => array(null,'number','*'),
  'pdf_paper_size'   => array("A4",'text','*'),
  'pdf_paper_orientation' => array("portrait",'select','*', array('P'=>"portrait", 'L'=>'landscape')),
  'system_online'    => array('1','yesno','*'),
  'offline_message'  => array('System temperary offline','area','',array('rows'=>5,'cols'=>92)),
  'trace_on'         => array(null,'select','*', array('ALLSEND' => 'Email orphan errors with history',
                                                       'SEND'    => 'eMail orphan errors',
                                                       'ALL'     => 'Always log traces',
                                                       '0'       => 'Disable (faster response)')),

  'shopconfig_run_as_demo' => array(0,'yesno','*'), // " int(3) NOT NULL DEFAULT '0'"
);

$config['group_autocleanup'] = array(
  'shopconfig_lastrun' => array(0,'hidden'),
  'shopconfig_lastrun_int' => array(10,'number'),
  'shopconfig_check_pos' =>  array(0,'yesno'),//" enum('No','Yes') NOT NULL DEFAULT 'No'",
  'shopconfig_delunpaid' =>  array(0,'yesno'),//" enum('Yes','No') NOT NULL DEFAULT 'Yes'",
  'shopconfig_delunpaid_pos' =>  array(0,'yesno'),//" enum('Yes','No') NOT NULL DEFAULT 'Yes'",
  'cart_delay'  =>  array(600,'number','*'),// " int(11) NOT NULL DEFAULT '600'",
  'res_delay'   =>  array(660,'number','*'),// " int(11) NOT NULL DEFAULT '660'",
);

$config['group_ordering'] = array(
'shopconfig_user_activate'   =>  array(0,'select','*',  array('-1'=>con('act_restrict_cart'),
             '0'=>con('act_restrict_all'),
             '1'=>con('act_restrict_later'),
             '2'=>con('act_restrict_w_guest'),
             '3'=>con('act_restrict_quest_only'))),//" tinyint(4) NOT NULL DEFAULT '0'",
'shopconfig_maxres'          =>  array(10,'number','*'),//" int(11) NOT NULL DEFAULT '10'",
'shopconfig_maxorder'        =>  array(14,'number','*'), // " int(11) NOT NULL DEFAULT '14'",
'shopconfig_altpayment_time' =>  array(2,'number','*'),//" varchar(20) NOT NULL DEFAULT '2'",
'shopconfig_restime' => array(0,'number','*'),
'shopconfig_restime_remind' => array(0,'hidden'),
);

$config['group_upload'] = array(
  'allowed_uploads'    =>  array(null,'text','#@'),
//  'allowed_images'     =>  array(null,'area','#@',array('rows'=>5,'cols'=>92)),
//  'allowed_other'      =>  array(null,'text','#@'),
  'file_mode'          =>  array(null,'text'),
  'allowed_maxsize'    =>  array(9000,'number'),
  'allowed_maxwidth'   =>  array(200,'number'),
  'allowed_maxheight'  =>  array(200,'number'),
  );

/*
$config['group_login'] = array(
  'bot_protection_key'   =>  array(null,'text'),
  'bot_protection_email' =>  array(null,'text'),
  'bot_protection_diag'  =>  array(0,'yesno'),
  'max_login_attempts'   =>  array(5,'number')
  );
*/

$config['group_mail'] = array(
  'mail_smtp_host'   =>  array(null,'text'),
  'mail_smtp_user'   =>  array(null,'text'),
  'mail_smtp_pass'   =>  array(null,'password'),
  'mail_smtp_port'   =>  array(null,'number'),
  'mail_smtp_security'  =>  array(null,'select','', array(''=>'smtp_security_none', 'ssl'=>'smtp_security_ssl', 'tls'=>'smtp_security_tls'))
  );

$config['group_language'] = array(
  'langs'            =>  array(null,'text','#@'),
  'langs_names'      =>  array(null,'area','#@',array('rows'=>5,'cols'=>92)),
  'event_type_enum'  =>  array(null,'area','#@',array('rows'=>5,'cols'=>92)),
  'event_group_type_enum'  =>  array(null,'area','#@',array('rows'=>5,'cols'=>92)),
  'AutoDefineLangs'  =>  array(null,'yesno','*'),

);
/*
$config['group_versioncheck'] = array(
'shopconfig_proxyaddress' => array(0,'hidden'), // " varchar(300) NOT NULL DEFAULT ''",
'shopconfig_proxyport' => array(0,'hidden'), // " int(5) DEFAULT NULL",
'shopconfig_ftusername' => array(0,'hidden'), // " varchar(60) NOT NULL DEFAULT ''",
'shopconfig_ftpassword' => array(0,'hidden'), // " varchar(100) NOT NULL DEFAULT ''",
'shopconfig_keepdetails' => array(0,'hidden'), // " tinyint(1) NOT NULL DEFAULT '0'",
);
*/
?>