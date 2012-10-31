<?php
/**
%%%copyright%%%
 *
 * FusionTicket - ticket reservation system
 *  Copyright (C) 2007-2012 Christopher Jenkins, Niels, Lou. All rights reserved.
 *
 * Original Design:
 *  phpMyTicket - ticket reservation system
 *   Copyright (C) 2004-2005 Anna Putrino, Stanislav Chachkov. All rights reserved.
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


class Plugin_Smarty {

  function __construct ($smarty){
    global $_SHOP;
   // echo $_SHOP->tmp_dir.'smarty_plugins_cache.php';
   // var_dump($_SHOP);
    if (!file_exists($_SHOP->tmp_dir.'smarty_plugins_cache.php')) {
      $plugins = plugin::loadAll(false);
      $result = '<?php
if (!defined("ft_check")) { die("System intrusion"); }

class Smartyplugincache {'."\n";
      $names = array();
      $blocks = array();
      foreach ($plugins as $key => $obj ){
        $functions = get_class_methods($obj->plug('a'));
        var_dump($obj,$functions);

        foreach ($functions as $function){
          if ( preg_match("/^smarty(Function|Block)(.*?\w+)\z/i", $function, $matches) AND (!in_array($matches[2],$names) || !in_array($matches[2],$blocks))) {
            if (strtolower($matches[1])=='block') {
              $result .= "  function {$matches[2]}(\$params, \$content,\$template, &\$repeat) { return plugin_{$key}::{$function}(\$params, \$content, \$template, \$repeat); }\n";
              $blocks[] =$matches[2];
            } else {
              $result .= "  function {$matches[2]}(\$params, \$template) { return plugin_{$key}::{$function}(\$params, \$template); }\n";
              $names[] =$matches[2];
            }

          }
        }
      }
     echo $blocks = implode("','",$blocks);
      $blocks = ($blocks)?"array('".$blocks."')":'null';
      $result .= "  function __call(\$name, \$args) { return ''; }\n";
      $result .= "\n  function __initialize(\$smarty) {
        \$plugs = new Smartyplugincache();
        \$smarty->registerobject('plugin', \$plugs, null, true, {$blocks}); }\n";
      $result .= "}\n";

      file_put_contents (  $_SHOP->tmp_dir.'smarty_plugins_cache.php' ,$result);
    //  die('<pre>'.$result);
    }
    include( $_SHOP->tmp_dir.'smarty_plugins_cache.php');
//    $plugs = new Smartyplugincache($smarty, $plugins);
    if ($smarty) {
      Smartyplugincache::__initialize($smarty);
    }
//
  }
}
?>