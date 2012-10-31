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
 *}<!-- $Id: order.tpl 1799 2012-05-23 07:32:06Z nielsNL $ -->{include file="header.tpl"}
<div id="order-div" style="width:100%;">
   <br />
  <form id="order-form" name='addtickets' action='ajax.php?x=addtocart' method='post'>

    <table width="100%" cellpadding='2' cellspacing='2' bgcolor='white' >
      <tbody>
        <tr>
          <td width="120" class='user_item'>{!event!}:</td>
          <td class='user_value' >
            {!date_from!} {!yyyy_mm_dd!}: <input type="text" id="event-from" size="10" />
            {!date_to!} {!yyyy_mm_dd!}: <input type="text" id="event-to" size="10" /><br />
            <select id="event-id" name="event_id" size="1"></select>
            {!free_seat!}: <span id="ft-event-free-seats" >0</span> ({!approx!})
          </td>
        </tr>
        <tr>
          <td class='user_item'>{!select_category!}:</td>
          <td class='user_value'>
            <select name='category_id' id='cat-select' style="width:250px;">
              <option value='0'></option>
            </select>
            {!free_seat!}: <span id="ft-cat-free-seats" >0</span> ({!approx!})
          </td>
        </tr>
        <tr id='discount-name' {* style="display:none;" *}>
          <td class='user_item' >{!discounts!}:</td>
          <td class='user_value'>
            <select name='discount_id' id='discount-select' style="width:250px;"> {* display:none;      *}
              <option value='0'></option>
            </select>
          </td>
        </tr>
        <tr>
          <td id="qty-name" style="" class='user_item'>{!tickets_nr!}:</td>
          <td class='user_value' class="seat-selection" >
            <div id="show-seats" style="display:none;float:left;">
              {gui->button url="button" name='show_seats'}
            </div>
            <div id="seat-qty" style="display:none;float:left;">
               <input type='number' name='place' size='4' maxlength='2' />
               {gui->button url="submit" id="continue" name='add_tickets' type=1}
            </div>
           <div style='float:right;'>
             {gui->button url="button" id="clear-button" name='clear_selection'}
          </div>
         </td>
        </tr>
       </tbody>
    </table>
    <div id="continue-div" style="width:100%; overflow:auto;"></div>
    <div id="seat-chart" title="{!select_seat_pos!}"></div>
  </form>

  <table id="cart_table" class="scroll" cellpadding="0" cellspacing="0"></table>
  <div id="cart-pager"></div>
  <div id="order_action" title="{!pos_order_page!}"></div>
  <br />
    <table width='100%'>
      <tr>
        <td valign='top'  width='50%'>
            <form id="pos-user-form" name="pos-user">
               {include file='user.tpl'}
            </form>
         </td>
         <td valign='top' align='right'>
          <form>
             <table id='handling-table' width='99%' cellspacing='2' cellpadding='2'  bgcolor='white'>
              <thead>
                <tr>
                  <td colspan='3' class='title' align='left'>{!handlings!}</td>
                </tr>
               </thead>
              <tbody id='handling-block'>
                {include file='handlings.tpl'}
              </tbody>
            </table>
          </form>

         </td>
      </tr>
    </table>
  <br />
  <table width='100%'>
    <tr>
        <td class='title' align='left'>
           {!ordertickets!}
        </td>
      </tr>
      <tr>
        <td style="text-align:center;">
          <form id="ft-pos-order-form">
            {* update->view event_date=$min_date user=user->user_id *}
            <button type='button' id='checkout' name='action' value='PosCheckout' style="float:none;">{!order_it!}</button>
            &nbsp;
            <button type='button' id='cancel' name='action' value='PosCancel' style="float:none;">{!cancel!}</button>
          </form>
        </td>
      </tr>
  </table>
</div>
<br> <br>
<script type="text/javascript">
{literal}
  $(document).ready(function(){
    loadOrder();
  });
{/literal}
</script>
{if !$nofooter}
  {include file="footer.tpl"}
{/if}
