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

 *}<!-- $Id: view_options.tpl 1788 2012-05-12 11:14:15Z nielsNL $ --><table  width='100%' border='0' cellspacing='0' cellpadding='1'
  style='padding-left:5px;'>
<tr><td class='title' colspan='2'>
    {!address!}</a>
</td></tr>
<tr><td class='user_address_td'>{pos->user_firstname}
 {pos->user_lastname}</td></tr>
 <tr><td class='user_address_td'>{pos->user_address}</td></tr>
 {if $pos->user_address1}
 <tr><td class='user_address_td'>{pos->user_address1}</td></tr>
 {/if}
<tr><td class='user_address_td'>{pos->user_zip} {pos->user_city}</td></tr>
<tr><td class='user_address_td'>{country code=$pos->user_country}</td></tr>
<tr><td class='user_address_td'>{pos->user_email}</td></tr></table>
<br><br><form action='view.php' method='GET'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' >
<tr><td class='title' colspan='2' >
    {!preferences!}</a>
</td></tr>
<tr><td width='300'>
<select name='user_prefs' >
<option value="pdt" {if $pos->user_prefs eq "pdt"} selected {/if}>{!send_orders_printer!}</option>
<option value="pdf" {if $pos->user_prefs eq "pdf"} selected {/if}>{!open_with_acrobat!}</option>
</select>
</td><td align='left'>
<input type='hidden' name='action' value='save_prefs'>
<input type='submit' name='save' value='{!save!}'>
</td></tr>

</table>
</form><br>
