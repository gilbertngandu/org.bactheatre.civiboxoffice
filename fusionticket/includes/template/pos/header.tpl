{*
%%%copyright%%%
 * phpMyTicket - ticket reservation system
 * Copyright (C) 2004-2005 Anna Putrino, Stanislav Chachkov. All rights reserved.
 *
 * This file is part of phpMyTicket.
 *
 * This file may be distributed and/or modified under the terms of the
 * "GNU General Public License" version 2 as published by the Free
 * Software Foundation and appearing in the file LICENSE included in
 * the packaging of this file.
 *
 * Licencees holding a valid "phpmyticket professional licence" version 1
 * may use this file in accordance with the "phpmyticket professional licence"
 * version 1 Agreement provided with the Software.
 *
 * This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
 * THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE.
 *
 * The "phpmyticket professional licence" version 1 is available at
 * http://www.phpmyticket.com/ and in the file
 * PROFESSIONAL_LICENCE included in the packaging of this file.
 * For pricing of this licence please contact us via e-mail to
 * info@phpmyticket.com.
 * Further contact information is available at http://www.phpmyticket.com/
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * Contact info@phpmyticket.com if any conditions of this licencing isn't
 * clear to you.

 *}<!-- $Id: header.tpl 1794 2012-05-17 15:40:09Z nielsNL $ -->
{if $smarty.request.ajax neq "yes"}
<html>
	<head>
		<meta http-equiv="Content-Language" content="English" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" type="images/png" href="{$_SHOP_images}favicon.png" />
		<title>FusionTicket: Box Office / Sale Point </title>
    {minify type='css'  base=''}
    {minify type='css'  base='css' files='formatting.css,ui.jqgrid.css'}

    {minify type='js' base='scripts/jquery'}
    {minify type='js' base='scripts/jquery' files='i18n/grid.locale-en.js,jquery.jqGrid.min.js,DD_roundies.js'}
    {minify type='js' base='pos/scripts' files='pos.jquery.style.js,pos.jquery.ajax.js,pos.jquery.order.functions.js,pos.jquery.order.js,pos.jquery.order.user.js,pos.jq.forms.js,pos.jq.current.js,pos.jq.current.functions.js'}
    <!--[if lt IE 9]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
		<script type="text/javascript">
      var address = '{$_SHOP_root}';
			var lang = new Object();
			lang.required = '{!mandatory!}';        lang.phone_long = '{!phone_long!}'; lang.phone_short = '{!phone_short!}';
			lang.fax_long = '{!fax_long!}';         lang.fax_short = '{!fax_short!}';
			lang.email_valid = '{!email_valid!}';   lang.email_match = '{!email_match!}';
			lang.not_number = '{!not_number!}';
			lang.chart_title = '{!select_seat_pos!}';
		</script>
    <style>
      .ui-state-highlight {
      background: #cdefeb url(images/ui-bg_flat_55_cdefeb_40x100.png) 50% 50% repeat-x !important ;
     }
    </style>
	</head>

	<body>

		<div id="wrap">
			<div id="header">
				<div class="loading">
					<img src="{$_SHOP_themeimages}LoadingImageSmall.gif" width="16" height="16" alt="Loading data, please wait" />
				</div>
       		<img src='{$_SHOP_images}logo.png'  border='0'/>
 				{!box_office!}
			</div>
			<div id="navbar">
				<ul>
					<li><a href="index.php" accesskey="b" tabindex="11">{!pos_booktickets!}</a></li>
					<li><a href="view.php" accesskey="t" tabindex="12">{!pos_currenttickets!}</a></li>
					<li><a href='?action=logout' >{!logout!}</a></li>

				</ul>
			</div>

      <!-- Message Divs -->
    <div id="error-message-main" title="{!order_error_message!}" class="ui-state-error ui-corner-all" style="display:none; padding: 0 .7em;" >
       <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
          <strong>{!alert!} </strong><span id='error-text-main'></span>
       </p>
    </div>

      <div id="notice-message" title="{!order_notice_message!}" class="ui-state-highlight ui-corner-all center" style=" padding: 1em; margin-top: .7em; display:none;" >
        <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
          <span id='notice-text'>fff</span>
        </p>
      </div>
      <!-- End Message Divs -->

      <div style="display:none" id='showdialog'>&nbsp;</div>
			<div id="right">
{/if}