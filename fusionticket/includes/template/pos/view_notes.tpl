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
 *}<!-- $Id: view_notes.tpl 1788 2012-05-12 11:14:15Z nielsNL $ -->  <tr>
    <td colspan="2">
      {* new order notes *}
      <table class='admin_form' width='100%' cellspacing='1' cellpadding='2'>
        <tr>
          <td class='title' colspan='2'>{!order_note_title!}</td>
        </tr>
        {order_note order_id=$order.order_id order_var=$order}
        {counter assign='row' print=false}
        <tr class='admin_list_row_{$row%2}'>
         	<td class='admin_info' width='180'><strong>{$order_onote.onote_timestamp}</strong></td>
          <td class='admin_info' >
            {$order_onote.onote_subject}
            {if $order_onote.onote_private}
               <span style="text-align:right; " >{!onote_isprivate!}</span>
            {/if}
          </td>
        </tr>
        <tr class='admin_list_row_{$row%2}'>
          <td class='admin_info' width='180'><strong>{!onote_type!}</strong> : {$order_onote.onote_type}</td>
          <td class='admin_info' ><div style='overflow:hidden;'>{$order_onote.onote_note}</div></td>
        </tr>
 {*       <tr class='admin_list_row_{$row%2}'>
          <td class='admin_info' colspan='2'>
            <p style="float:left;"  >{!onote_private!} : {$order_onote.onote_private}</p>

            {gui->StartForm name=order_note_resend action=$smarty.server.REQUEST_URI data=$smarty.post table=false}
            {gui->hidden name='onote_id' value=$order_onote.id}
            {gui->hidden name='onote_order_id' value=$order.order_id}
            {gui->hidden name='order_id' value=$order.order_id}
            {gui->hidden name='action' value='resendnote'}
            {if $order_onote.onote_type eq 'payment'}
              <button type='submit' name='save_payment' value='' style='float:right;'>{!onote_resend_paid!}</button>
            {elseif $order_onote.onote_type eq 'ship'}
              <button type='submit' name='save_ship' value='' style='float:right;'>{!onote_resend_sent!}</button>
            {/if}
            </form>
          </td>
        </tr> *}
        {/order_note}
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">

      {gui->StartForm name=order_add_note width='100%' action=$smarty.server.REQUEST_URI data=$smarty.post}
      {gui->hidden name='action' value='addnote'}
      {gui->hidden name='onote_order_id' value=$order.order_id }
      {gui->hidden name='order_id' value=$order.order_id }
      {gui->selection name='onote_type' options=$order_onote_types con=true}
      {gui->checkbox name='onote_private'}
      {gui->input name='onote_subject' size=40}
      {gui->area name='onote_note' rows=8}
{*
      <tr class="admin_list_row_0" id="on_save_email_ship" style="display:none;">
        <td class='' style='text-align:center;'>
          <button type="submit" name="save_ship" id="save_ship">{!save_ship!}</button>
        </td>
        <td>
          <label for='onote_set_sent'>{!onote_set_sent!}</label>
          <input type='checkbox' id='onote_set_sent' name='onote_set_sent' value='1' />
        </td>
      </tr>
      <tr class="admin_list_row_0" id="on_save_email_payment" style="display:none;">
        <td class='' style='text-align:center;'>
          <button type="submit" name="save_payment" id="save_payment">{!save_payment!}</button>
        </td>
        <td>
          <label for='onote_set_paid'>{!onote_set_paid!}</label>
          <input type='checkbox' id='onote_set_paid' name='onote_set_paid' value='1' />
        </td>
      </tr>
      <tr class="admin_list_row_0" id="on_save_email_note" style="display:none;">
        <td class='' colspan='2' style='text-align:center;'>
          <button type="submit" name="save_note" id="save_note">{!save_note!}</button>
        </td>
      </tr>

      <script language="javascript" type="text/javascript">
      {literal}
      $('#onote_type-select').change(function(){
        if($(this).val() == 'ship'){
          $('#on_save_email_ship').show(); $('#on_save_email_note').hide();
          $('#on_save_email_payment').hide(); $('#onote_set_paid').attr('checked',false);
        }else if($(this).val() == 'payment'){
          $('#on_save_email_ship').hide(); $('#on_save_email_note').hide();
          $('#onote_set_sent').attr('checked',false); $('#on_save_email_payment').show();
        }else if($(this).val() == 'note'){
          $('#on_ship_note').attr('checked',false); $('#on_payment_note').attr('checked',false);
          $('#on_save_email_ship').hide(); $('#on_save_email_payment').hide();
          $('#on_save_email_note').show();
        }else{
          $('#on_save_email_ship').hide(); $('#on_save_email_payment').hide(); $('#on_save_email_note').hide();
          $('#on_ship_note').attr('checked',false); $('#on_payment_note').attr('checked',false);
        }
      }).change();
      {/literal}
      </script>
*}
      {gui->EndForm}
    </td>
  </tr>
  {if $order.order_note}
  <tr>
    <form name='f' action='view.php' method='post'>
      <td valign='top'>{!pos_enternote!}</td>
      <td>
        <input type="hidden" name="action" value="update_note" />
        <input type="hidden" name="order_id" value="{$order.order_id}" />
        <textarea name="note" cols="40" rows="8" wrap="VIRTUAL">{$order.order_note}</textarea>
        <br />
        <input type="submit" value="Save Note" />
      </td>
    </form>
  </tr>
  {/if}