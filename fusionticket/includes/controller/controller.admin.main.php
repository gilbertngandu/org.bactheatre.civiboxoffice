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
  protected $menu_width =  200;
  protected $body_width =  '100%';
  public    $width      = 1000;
  protected $section    = 'admin';
  protected $useSSL = true;
  public    $session_name  = "AdminSession";
  public    $auth_required = true;
  public    $auth_status   = "organizer";
  public    $title = 'administrator';
  public function __construct($context='action', $fond='index', $action) {
    global $_SHOP;
    $_SHOP->is_admin = true;
    parent::__construct($context, $fond, $action);
  }

  public function init(){
    global $_SHOP;
    if($_SHOP->system_online=='0'){
      addWarning("site_offline_message");// die('testing');
    }

    parent::init();
  }
  function loadMenu(){
    global $_SHOP;
    require_once ("admin/class.adminmenu.php");
    $this->menu = new MenuAdmin($this);
    if (!$this->isAllowed($this->current_page.'_admin')) {
      return false;
    }
    if (!file_exists( INC ."{$this->section}/view.{$this->current_page}.php")) {
      $this->showForbidden();
      return false;
    }
    $this->menu->current_page = $this->current_page;
    require_once ("{$this->section}/view.{$this->current_page}.php");
    $fond = str_replace('.','' ,$this->current_page );
    $classname = "{$fond}View";
  //  var_dump($this);
    $body = new $classname($this->body_width);
    $this->executed = $body->execute();
    if (!$this->executed) {
      $this->width = $body->page_width + $this->menu_width;
      $this->title = $body->title;
      if ($body->ShowMenu) {
        $menu = array($this->menu);
        $body->extramenus($menu);
        $this->setmenu($menu);
      }
      $this->setbody($body);
    }
    return true;
  }

  public function draw() {
    parent::draw();
  }

  function drawOrganizer () {
      global $_SHOP;
    echo "       <table width='100%'>
         <tr>
           <td>&nbsp;
             <font color='#555555'><b>" . con('welcome') . " " . $_SHOP->organizer_name . "</b></font>
           </td>
           <td  align='right'>&nbsp;";
  //        echo "<select name='setlang' onChange='set_lang(this)'>";

    //        $sel[$_SHOP->lang] = "selected";
    //        foreach($_SHOP->langs_names as $lang => $name) {
    //            echo"<option value='$lang' {$sel[$lang]}>$name</option>";
  //        }
  //        echo "</select>";
    echo "
           </td>
         </tr>
       </table>";
  }

  function drawHeader(){
    global $_SHOP;
    if (!isset($_SERVER["INTERFACE_LANG"]) or !$_SERVER["INTERFACE_LANG"]) {
        $_SERVER["INTERFACE_LANG"] = $_SHOP->langs[0];
    }
    if (isset($_SHOP->system_status_off) and $_SHOP->system_status_off) {
        AddWarning('system_halted');
    }
     //+'&href={$_SERVER["REQUEST_URI"]}'
    echo "<head>
    <meta HTTP-EQUIV=\"content-type\" CONTENT=\"text/html; charset=UTF-8\">
    <META HTTP-EQUIV=\"Content-Language\" CONTENT=\"" . $_SERVER["INTERFACE_LANG"] . "\">
    <title>" . $this->getTitle() . "</title>
    <link rel='shortcut icon' type='images/png' href='../images/favicon.png' />

    ".minify('css','','')."
    ".minify('css','colorpicker/layout.css,colorpicker/colorpicker.css','css')."
    <link rel='stylesheet' href='../admin/admin.css' />

    ".minify('js','','scripts/jquery')."
    ".minify('js','jquery.dimensions.min.js,jquery.caret.js,colorpicker/colorpicker.js','scripts/jquery')."

    <script type=\"text/javascript\" >
      function set_lang(box)
      {
      	lang = box.options[box.selectedIndex].value;
      	if (lang) location.href = '?setlang='+lang;
      }
      $(document).ready(function(){
        var msg = '".printMsg('__Warning__', null, false)."';
        if(msg) {
          $('#error-text').html(msg);
          $('#error-message').show();
          setTimeout(function()".'{'."$('#error-message').hide();}, 10000);
        }
        var msg = '".printMsg('__Notice__', null, false)."';
        if(msg) {
          $('#notice-text').html(msg);
          $('#notice-message').show();
          setTimeout(function()".'{'."$('#notice-message').hide();}, 10000);
        }
      });
    jQuery(document).ready(function(){
      jQuery(\"*[class*='has-tooltip']\").tooltip({
        delay:0,
        showBody: \"~\",
        showURL:false,
        track: true,
        opacity: 1,
        fixPNG: true,


        fade: 250
      });
    });

      var field_length=0;

      function TabNext(obj,event,len,next_field) {
        if (event == 'down') {
          field_length=obj.value.length;
        }
        else if (event == 'up') {
          if (obj.value.length != field_length) {
            field_length=obj.value.length;
            if (field_length == len) {
              next_field.focus();
            }
          }
        }
      }

    </script>
  </head>
  <body >
  	<div id='wrap' style='width:". $this->width . "px;'>
       <div  id='header'>
          <img src=\"".$_SHOP->images_url."logo.png\" border='0'/>
          <h2>".con($this->title)."</h2>
       </div>
       <div id='navbar'>";
      $this->drawOrganizer();
      echo '
       </div><br>
       <DIV style="MARGIN-TOP: 0.35em;MARGIN-Bottom: 0.35em; DISPLAY: none" id=error-message class="ui-state-error ui-corner-all" title="Order Error Message">
          <P><SPAN style="FLOAT: left; MARGIN-RIGHT: 0.3em" class="ui-icon ui-icon-alert"></SPAN><div id=error-text>ffff<br>tttttcv ttt </div> </P>
       </DIV>
       <DIV style="MARGIN-TOP: 0.35em; MARGIN-Bottom: 0.35em; DISPLAY: none" id=notice-message class="ui-state-highlight ui-corner-all" title="Order Notice Message">
          <P><SPAN style="FLOAT: left; MARGIN-RIGHT: 0.3em" class="ui-icon ui-icon-info"></SPAN><div id=notice-text>fff</div> </P>
       </DIV>
    ';
  }

  function drawContent() {
    if (!$this->executed) {
      echo "<table border=0 width='" . $this->body_width . "' class='aui_bico'><tr>";
      if ($menu = $this->items["menu"]) {
        echo "<td class='aui_bico_menu' width='" . $this->menu_width . "' valign='top'>\n";
        $this->drawChild($menu);
        echo "</td>";
      }
      echo "<td class=aui_bico_body valign=top>";

      $body = $this->items["body"];
      if (is_object($body)) {
        If ($menu) {
          $body->setWidth($this->width - ($this->menu_width+8));
        } else {
          $body->setWidth($this->width);
        }
      }
      $this->drawChild($body);
      echo"</td></tr></table>\n";
    }

    if(is_object($body)){
      $this->setJQuery($body->getJQuery());
    }
  }


  function drawFooter() {
    global $_SHOP;

    echo "
      <br><br>
      <div id='footer'>
     		 <!-- To comply with our GPL please keep the following link in the footer of your site -->
				 Copyright &copy; 2012 by <b>Fusion Ticket solutions Limited </b> | Powered By <a href='http://fusionticket.org' target='_blank'>Fusion Ticket</a>
			</div>

		</div>\n";
    if (strpos(constant('CURRENT_VERSION'),'svn') !== false) {
      print_r($_SHOP->Messages);
    }
echo "
      <script type=\"text/javascript\">
        $(document).ready(function(){
          $(\"a[class*='has-tooltip']\").tooltip({
            delay:40,
            showURL:false,
            bodyHandler: function() {
              if($(this).children('div').html() != ''){
                return $(this).children('div').html();
              }else{
                return false;
              }
            }
          });
          ". is($this->items['jquery'],'') ."
        });
      </script>

	</body>
</html>";
  }
}

?>