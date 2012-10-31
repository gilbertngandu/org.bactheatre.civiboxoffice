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

 *}<!-- $Id: index.tpl 1788 2012-05-12 11:14:15Z nielsNL $ --><table cellpadding='3' width='100%'>

  <tr>
    <td colspan='5' class='title'>{!pos_homepage!}</td>
  </tr>
  <tr>
  	<td class="sub_title" width="45%">Currently Running Event groups</td>
    <td>{event_group  group_status='pub'}<a class='list' href='index.php?group_id={$shop_event_group.event_group_id}'>
		{$shop_event_group.event_group_name}</a>{/event_group}
	</td>
  </tr>
  <tr>
    <td class="sub_title"><p>Stats:</p></td>
    <td></td>
   </tr>
  <tr>
    <td class="sub_title"><strong>To Order</strong> tickets please click "Book Tickets".</td>
    <td><b><a class="link" href="index.php?action=calendar">Book Tickets</a></b></td>
  </tr>
  <tr>
    <td class="sub_title"><strong>To Reserve</strong> seats please click "Reserve Tickets".</td>
    <td><b><a class="link" href="index.php?action=calendar">Reserve Tickets</a></b></td>
  </tr>
  <tr>
    <td class="sub_title"><strong>To Proccess</strong> Paid Tickets.</td>
	<td><b><a class="link" href="index.php?process=on">Process Tickets</a></b></td>
    <td></td>
  </tr>
  <tr>
    <td class="sub_title"><strong>To Post / Set Status</strong> for tickets follow.</td>
    <td></td>
  </tr>
  <tr>
    <td class="sub_title"><strong>To Find Tickets / Users</strong> follow.</td>
    <td></td>
  </tr>
  <tr>
    <td valign="top" class="title"><p><strong>Help:</strong></p>    </td>
    <td><p>To order tickets follow the procces through its all fairly self explainitory.<br>

        <strong>First</strong> select the event you would like to book for.<br>
    Then you will be presented with some catergorys, These are sections of seats, each one is priced and will display different parts of the theater map. Open seating will just ask how many seats you would like to buy.<br>

    <strong>Second</strong>, Select either how many seats you want or which seats you want by ticking the green box's.<br>
    <strong>Third, </strong>Continue to check out where you will see the total tickets selected and the total cost. Choose your method of payment.<br>
    Cash Payment will assume you have taken the money when you booked the tickets.<br>
    Debit / Credit Card will proccess you through paypal, to pay for the tickets.</p>
    </td>
  </tr>
</table>

