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



/*/Check page is secure
if($_SERVER['SERVER_PORT'] != 443 || $_SERVER['HTTPS'] !== "on") {
$url = $_SHOP->root_secured.$_SERVER['REQUEST_URI'];
echo "<script>window.location.href='$url';</script>"; exit;
//header("Location: https://"$_SHOP->root_secured.$_SERVER['SCRIPT_NAME']);exit;}
}
//remove the www. to stop certificate errors.
if(("https://".$_SERVER['SERVER_NAME']."/") != ($_SHOP->root_secured)) {
$url = $_SHOP->root_secured.$_SERVER['REQUEST_URI'];
echo "<script>window.location.href='$url';</script>"; exit;
}*/

//require_once (CLASSES.'class.smarty.php');
require_once("classes/class.controller.php");
require_once('shop_plugins/function.minify.php');
// remove the # below under linux to get a list of locale tags.

#  print_r(list_system_locales());



class ctrlAdminMain extends Controller  {
  protected $section    = 'admin';
  protected $useSSL = true;
  public    $session_name  = "JsonSession";
  public    $auth_required = false;

  public function __construct($context='action', $fond='index', $action) {
    parent::__construct($context, $fond, $action);
  }

  public function draw() {
    $this->$executed = true;
    parent::draw();
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
      $this->request    = $_REQUEST;
      if ($fond=='index') {
        $this->actionName = $this->action;
        $this->action = 'do'.ucfirst($this->action);
        $result = $this->callAction();
      } else {
        $result = plugin::call(array($fond, $action), $this);
      }
        if(!$result){
          $object = array("status" => false, "reason" => 'Missing action request');
          echo json_encode($object);
        }
      }else{
      header("Status: 400");
      echo "This is for AJAX / AJAJ / AJAH requests only, please go else where.";
    }
  }

}

?>