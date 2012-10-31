{*                  %%%copyright%%%
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
 *}<!-- $Id: event_prices.tpl 1822 2012-07-11 22:26:35Z nielsNL $ -->
{if $shop_event.event_pm_id}
  <div class="art-content-layout-br layout-item-0"></div>
  <b>{!cat_description!}</b>
  <div class="art-content-layout" style="width: 100%;">
    <div class="art-content-layout-row" >
      <div class="art-layout-cell layout-item-4" style="width: 100%;">
        <table border=0 class='table_midtone'>
      		<tr class='small_table_dark' >
      			<th>{!category!}</th>
      			<th width='15%'>{!price!}</th>
      			<th>{!tickets_available!}</th>
      		</tr>
          {category event_id=$shop_event.event_id stats="on"}
            {cycle assign='cycle' name='events' values="tr_0,tr_1" print=NO}
            <tr class='{$cycle}'>
              <td ><b>{$shop_category.category_name}</b>
                {discount event_id=$shop_event.event_id cat_price=$shop_category.category_price}
                  <br>&nbsp;
                  <span class='note'>
                     {$shop_discount.discount_name}: {valuta value=$shop_discount.discount_price|string_format:"%.2f"}
                  </span>
                {/discount}
              </td>
              <td align='right' style='text-align:right'>
                {valuta value=$shop_category.category_price}
              </td>
              <td  align='right' width='10%' style='text-align:right'>
                {if $shop_category.category_free>0}
                	{assign var=event_has_seats value="true"}
      	          {if $shop_category.category_free/$shop_category.category_size ge 0.2}
                    <span>{$shop_category.category_free}</span>
                  {else}
                    <span style='color:Orange; '><b>{$shop_category.category_free}</b></span>
                  {/if}
                {else}
                  <span color='red'>{!category_sold!}</span>
                {/if}
              </td>
            </tr>
          {/category}
        </table>
        <div class='note' align='right' style='text-align:right'>
         {!prices_in!} {$organizer_currency}
        </div>
      </div>
    </div>
  </div>
  <div class="art-content-layout-br layout-item-0"></div>
  <div class="art-content-layout layout-item-1">
    <div class="art-content-layout-row" style='padding:10px;'>
      {if $user->mode() eq '-1' and !$user->logged}
        <p><center>{!Please_login!}</center></p>
      {elseif $shop_event.event_date ge $smarty.now|date_format:"%Y-%m-%d"}
          <div class="art-layout-cell layout-item-3"  style='text-align:right; width: 100%;padding:10px;'>
		        {gui->button url="?event_id={$event_id}&action=buy" name="buy_tickets"}
      	  </div>
      {else}
        <p><center>{!old_event!}</center></p>
      {/if}
    </div>
  </div>
  <br>

{/if}