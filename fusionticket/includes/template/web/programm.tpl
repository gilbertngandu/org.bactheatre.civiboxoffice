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
 *}<!-- $Id: programm.tpl 1824 2012-07-14 22:14:35Z nielsNL $ -->
{include file="header.tpl" name=!program!}

<table class="table_trans">
  {event order="event_date,event_time"  main='on' ort='on' event_status='pub'}
    {counter print=false assign=count}
    {if $count is odd}
      <tr style='padding-bottom:30px;'>
    {/if}
    <td  valign="top" align='center' width='50%'>
      <div class="art-content-layout layout-item-1">
        <div class="art-content-layout-row" style='padding:10px;'>
          <div class="art-layout-cell layout-item-3"  style='text-align:left; width: 100%;padding:10px;'>
            {gui->image href="{$shop_event.event_image}" width=160 height=150 align='center' class="magnify has-tooltip" border="0" style="" alt="{$shop_event.event_name} in {$shop_event.ort_city}" title="{$shop_event.event_name} in {$shop_event.ort_city}" border="0"}
            <ul>
              <li><b>{!event_name!}:</b>
                <a class="title_link" href='index.php?event_id={$shop_event.event_id}'>
                  {$shop_event.event_name}
                </a>
                {if $shop_event.event_mp3}
                  <a  href='files/{$shop_event.event_mp3}'>
                    <img src='{$_SHOP_themeimages}audio-small.png' border='0' valign='bottom'>
                  </a>
                {/if}
              </li>
              <li>
                 <b>{!date!}:</b>
                    {if $shop_event.event_rep eq "main,sub"}
                      {$shop_event.event_date|date_format:!shortdate_format!}
                      {$shop_event.event_time|date_format:!time_format!}
                      {$shop_event.pm_name}
                    {elseif $shop_event.event_rep eq "main"}
                      {!div_dates!}
                    {/if}
              </li>
              {if $info_plus && $shop_event.event_open}
                <li><b>{!doors_open!}</b> {$shop_event.event_open|date_format:!time_format!}</li>
              {/if}
              <li>
                <b>{!venue!}:</b>
                <a onclick='showDialog(this);return false;' href='address.php?event_id={$shop_event.event_id}'>{$shop_event.ort_name}</a> -
                {$shop_event.ort_city}
              </li>
            </ul>
            <div  align='left'>{$shop_event.event_short_text}</div><br>
          </div>
        </div>
      </div>
      <br>
    </td>
    {if $count is even}
      </tr>
    {/if}
  {/event}
</table>
{include file="footer.tpl"}