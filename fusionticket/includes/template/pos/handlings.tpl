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

 *}<!-- $Id: handlings.tpl 1788 2012-05-12 11:14:15Z nielsNL $ -->

  	{assign var=min_date value=$cart->min_date_f()}
    {capture name='total_price'}
      {cart->total_price}
    {/capture}
  	{handling sp='on' event_date=$min_date}
      {assign var='total_price' value=$smarty.capture.total_price}

  		<tr class="{cycle name='payments' values='admin_list_row_0, admin_list_row_1'}">
  		  <td class='payment_form'> &nbsp;
      	  {if $smarty.post.handling_id == $shop_handling.handling_id}
      			 {assign var=checked value="checked='checked'"}
          {else}
             {assign var=checked value=""}
          {/if}

    		  <input {$checked} type='radio' id='handling_id' class='checkbox_dark' name='handling_id' value='{$shop_handling.handling_id}'>
  		  </td>
  		  <td class='payment_form'>
  		  	<label for='{$shop_handling.handling_id}_check'>
  		  	{if $shop_handling.handling_id eq 1}
  		  	   {!reserve!} {!tickets!}
	  	    {else}
    		  	{!payment!}: {eval var=$shop_handling.handling_text_payment}<br>
    		  	{!shipment!}: {eval var=$shop_handling.handling_text_shipment}
   		  	{/if}
  		  	</label>
  		  </td>
  		  <td  class='view_cart_td'  valign='top'  align='right' width='100' id='price_{$shop_handling.handling_id}'>
          &nbsp;
  		  </td>
  		</tr>
    {/handling}
    {* possibly never used
    <tr>
      <td class='user_item' height='16' colspan='2'>
         {!without_fee!}
      </td>
      <td  class='user_value'>
        <input type='checkbox' class='checkbox' name='no_fee' id='no_fee' value='1'>
      </td>
    </tr>
    *}
  {*
    <tr>
      <td class='user_item' height='16' colspan='2'>
         {!without_cost!}
      </td>
      <td  class='user_value'>
        <input type='checkbox' class='checkbox' name='no_cost' id='no_cost' value='1'>
      </td>
    </tr>
*}
  	{if !$update_view.currentres}
      {assign var=errstyle value='style="display:none;"'}
  	{/if}
		<tr class="err" {$errstyle} >
			<td colspan="3">
 			  {*$update_view.maxres*}
 			  {!limit!} <br>
			</td>
		</tr>
  	<tr>
     	<td class='view_cart_total' colspan='2'>
      	{!total_price!}
    	</td>
    	<td class='view_cart_total' align='right' id='total_price'>
      {*  {valuta value=$total_price|string_format:"%.2f"} *}
    	</td>
   	</tr>

