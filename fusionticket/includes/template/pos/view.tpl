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
 *}<!-- $Id: view.tpl 1798 2012-05-20 12:28:15Z nielsNL $ -->

 {* Check for update note first *}
{if $smarty.post.action eq "update_note"}
  {order->save_order_note order_id=$smarty.post.order_id note=$smarty.post.note}
{elseif $smarty.post.action eq "addnote"}
  {order->add_order_note}
{elseif $smarty.post.action eq "resendnote"}
  {order->resend_note}
{/if}

{if $smarty.get.action eq 'cancel_order'}
  {order->cancel order_id=$smarty.get.order_id reason=$smarty.get.place}
  {include file="process_select.tpl"}

{elseif $smarty.get.action eq 'cancel_ticket'}
  {order->delete_ticket order_id=$smarty.get.order_id ticket_id=$smarty.get.ticket_id}
  {include file="process_select.tpl"}

{elseif $smarty.post.action eq 'confirm'}
  {include file="process_select.tpl"}

{elseif $smarty.request.action eq 'reorder'}
  {include file="view_reorder.tpl"}

{elseif $smarty.post.action eq 'order_res'}
  {order->res_to_order order_id=$smarty.post.order_id handling_id=$smarty.post.handling place='pos'}
  {if $order_success}
    {include file='process_select.tpl'}
  {else}
    <div class='error'>Error</div>
    {include file="process_select.tpl"}
  {/if}
{else}
  {if $smarty.request.order_id}
    {if $smarty.post.action eq "setpaid"}
      {$order->set_paid_f($smarty.post.order_id)}
  	{/if}
    {if $smarty.post.action eq 'setsend'}
    	{* $order->set_status_f($smarty.post.order_id,'ord') *}
    	{$order->setStatusSent($smarty.post.order_id)}
    {/if}
  {/if}

  {include file="process_select.tpl"}
{/if}