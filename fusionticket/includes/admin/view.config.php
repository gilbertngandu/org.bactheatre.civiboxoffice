<?php
/**
%%%copyright%%%
 *
 * FusionTicket - ticket reservation system
 *  Copyright (C) 2007-2012 Christopher Jenkins, Niels, Lou. All rights reserved.
 *
 * Original Design:
 *	phpMyTicket - ticket reservation system
 * 	Copyright (C) 2004-2005 Anna Putrino, Stanislav Chachkov. All rights reserved.
 *
 * This file is part of FusionTicket.
 *
 * This file may be distributed and/or modified under the terms of the
 * "GNU General Public License" version 3 as published by the Free
 * Software Foundation and appearing in the file LICENSE included in
 * the packaging of this file.
 *
 * This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
 * THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE.
 *
 * Any links or references to Fusion Ticket must be left in under our licensing agreement.
 *
 * By USING this file you are agreeing to the above terms of use. REMOVING this licence does NOT
 * remove your obligation to the terms of use.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * Contact help@fusionticket.com if any conditions of this licencing isn't
 * clear to you.
 */


if (!defined('ft_check')) {die('System intrusion ');}
require_once("admin/class.adminview.php");

class ConfigView extends AdminView{

  function form ($data, $err=null){
  	global $_SHOP;
    include ('config/data_config.php');
    $page = is($_REQUEST['page'],0);

  	$yesno = array('No'=>'confirm_no', 'Yes'=>'confirm_yes');
   //var_dump($data);

  	echo "<form method='POST' action='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data'>\n";
    echo "<input type='hidden' name='action' value='update'>\n";
    $this->form_head(con('config_title'),0);
    $this->print_hidden('page',0);
    echo '<tr><td colspan=2><div id="accordion">';
    foreach ($config as $group => $section){
      echo "<h3><a href='#' id='{$group}'>".con($group).'</a></h3>
                <div class="gui_form" style="margin:0;padding:1;"><table style="width:100%">';
      foreach ($section as $field => $row){
        if (count($rec)==1) $rec[1]='text';
        if (count($rec)==2) $rec[2]='';
        if (count($rec)==3) $rec[3]='';
        $this->MyInput($row[1], "{$field}", $data, '50', '50', $row[3]);
       }
       echo '</table></div>';
    }
    echo '</div></td></tr>';

    $this->form_foot();
    $this->addJQuery('	$(function() {
		$( "#accordion" ).accordion({
      autoHeight: false,
			collapsible: false,
			animated: "none",
       navigation: true,
      active: '.$page.',
			change: function(event, ui) {
			 var page = $( "#accordion" ).accordion( "option", "active" );

			  $("#page").val(page);
			}

		});
	});');

  }

	function draw () {
	global $_SHOP;
		if($_POST['action']=='update'){
		  $config = new config();
		  if(!$this->options_check($_POST) || !$config->fillPost() || !$config->saveEx()) {
		    $this->form( $_POST);
		    return;
			}
		  config::ApplyConfig();
		}
	  $row = config::LoadAsArray();
		$this->form($row);
		return;
  }

  function options_check (&$data){
  	global $_SHOP;

  	foreach(array('shopconfig_lastrun_int',    'shopconfig_maxres', 'shopconfig_restime', //'shopconfig_restime_remind',
                  'shopconfig_altpayment_time') as $check) {
      if(!is_numeric($data[$check])){
    		addError($check,'not_number');
    	}elseif($data[$check]<'0'){
    		addError($check,'too_low') ;
      }
   	}
  	if ($data['res_delay'] < $data['cart_delay']) {
    		addError('res_delay', 'res_delay_less_cart');
     }
  	return !hasErrors();

  }
  function MyInput($type, $field, $value, $size, $maxlength, $extra, $nolabel=false) {
    if (is_array($type)) {
  //  $this->label ($field, $nolabel);
      foreach ($type as $key => $newtype) {
        $this->MyInput($newtype, "{$field}[{$newtype}{$key}]", $value, $size, $maxlength, true);
      }
 // {/gui->label}
    } elseif ($type=='hidden'){
      $this->print_hidden($field, $value, $nolabel);
    } elseif ($type=='yesno'){
      $this->print_checkbox($field, $value, $nolabel);
    } elseif ($type=='select'){
      if (is_string($extra)) {
        if (method_exists($this, $extra )) {
          $extra = call_user_func(array($this, $extra  )) ;
        } else if (function_exists($extra )) {
          $extra = call_user_func($extra) ;
        }
        if (!is_array($extra)) {
          addError($field,'selectfunction_not_found');
        }
      }
      $this->print_select_assoc($field, $value, $nolabel, $extra);
    } elseif ($type=='area'){
      $this->print_area($field, $value, $nolabel, $extra['rows'], $extra['cols']);
    } elseif ($type=='array'){
      $this->print_view($field, 'Dit moet een array notatie worden', $nolabel);
    } elseif ($type=='number'){
      $this->print_input($field, $value, $nolabel, 10, 15, '','' , 'number');
    } elseif ($type=='file'){
      $this->print_file($field, $value, $nolabel);
    } else {
      $this->print_input($field, $value, $nolabel, $size, $maxlength, $x,$y , $type);
    }
  }

  function gettimezones() {
    $all = timezone_identifiers_list();
    $i = 0;
    foreach($all AS $zone) {
      $zone = explode('/',$zone);
      $zonen[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';
      $zonen[$i]['city'] = isset($zone[1]) ? $zone[1] : '';
      $zonen[$i]['subcity'] = isset($zone[2]) ? $zone[2] : '';
      $i++;
    }

    asort($zonen);
    $structure = array();
    foreach($zonen AS $zone) {
      extract($zone);
      if($continent == 'Africa' || $continent == 'America' || $continent == 'Antarctica' || $continent == 'Arctic' || $continent == 'Asia' ||
         $continent == 'Atlantic' || $continent == 'Australia' || $continent == 'Europe' || $continent == 'Indian' || $continent == 'Pacific') {
        if(isset($city) != ''){
          if (!empty($subcity)){
            $city = $city . '/'. $subcity;
          }
          $structure[$continent][$continent.'/'.$city] = $city;
        } else {
          $structure[$continent][$continent] = $continent;
        }
      }
    }
 //   var_dump($structure);
    return $structure;
  }

  function getThemeList(){
    global $_SHOP;
    $content = array();
    $dir= $_SHOP->tpl_dir . "theme".DS;
    if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && ((substr($file,0,1) != '_' && (is_dir($dir.$file)) ))) {
          $content[$file] =  "{$file} ";
        }
      }
      closedir($handle);
    }
    return $content;

  }

  function getCurrencies(){
    global $_SHOP;
    $keys= array_keys( $_SHOP->valutas);
    return array_combine($keys, $keys);// array
  }
}
?>