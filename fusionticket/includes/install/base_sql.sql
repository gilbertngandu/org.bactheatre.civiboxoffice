INSERT INTO `Admin` (`admin_id`,`admin_login`, `admin_password`,`admin_status`) VALUES
('1','demo','c514c91e4ed341f263e458d44b3bb0a7','organizer');



INSERT INTO `Handling` (`handling_id`, `handling_payment`, `handling_shipment`, `handling_fee_fix`, `handling_fee_percent`, `handling_email_template`, `handling_pdf_template`, `handling_pdf_ticket_template`, `handling_pdf_format`, `handling_html_template`, `handling_sale_mode`, `handling_extra`, `handling_text_shipment`, `handling_text_payment`, `handling_expires_min`, `handling_alt`, `handling_alt_only`) VALUES
(1, NULL, NULL, 0.00, 0.00, 'res=email_res', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7200, NULL, 'No'),
(2, 'cash', 'sp', 0.00, 0.00, 'ord=,send=,paid=', 'Invoice_pdf2', '', 'a:2:{i:0;a:4:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;}i:1;s:8:"portrait";}', 'Put some explanations here (edit this in Admin - Order Handlings )', 'sp', NULL, 'Point of sale', 'Cash', NULL, 1, 'No'),
(3, 'entrance', 'entrance', 0.00, 0.00, 'ord=,send=,paid=', 'Receipt_pdf2', '', 'a:2:{i:0;a:4:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;}i:1;s:0:"";}', 'Put some explanations here (edit this in Admin - Order Handlings )', 'sp,www', NULL, 'At the entrance\r\n', 'At the entrance\r\n', NULL, 1, 'No'),
(4, 'invoice', 'email', 1.00, 0.00, 'ord=,send=,paid=', 'Invoice_pdf2', '', 'a:2:{i:0;a:4:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;}i:1;s:0:"";}', 'Put some explanations here (edit this in Admin - Order Handlings )', 'www', NULL, 'By e-mail', 'Invoice', NULL, 1, 'No'),
(5, 'invoice', 'post', 0.00, 0.00, 'ord=,send=,paid=', 'Invoice_pdf2', 'Ticket_pdf2', 'a:2:{i:0;a:4:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;}i:1;N;}', 'Put some explanations here (edit this in Admin - Order Handlings )', 'sp', NULL, 'By post', 'Invoice', NULL, 1, 'No');


INSERT INTO `Template` (`template_id`, `template_name`, `template_type`, `template_text`, `template_ts`, `template_status`) VALUES
(1, 'forgot_passwd', 'systm', '<template deflang="en">\r\n<TO email="$user_firstname $user_lastname &lt;$user_email&gt;"/>\r\n\r\n<subject lang="en" value="Your new password"/>\r\n\r\n<text lang="en">\r\nDear $user_firstname $user_lastname!\r\n\r\nThere is your new password: $new_password\r\n\r\nHave a nice day\r\n\r\n</text>\r\n\r\n<html lang="en">\r\n<p>Dear $user_firstname $user_lastname!</p>\r\n\r\n<p>There is your new password: <b>$new_password</b></p>\r\n\r\n<p>Have a nice day!</p>\r\n \r\n\r\n</html>\r\n\r\n</template>\r\n', '2010-09-27 15:59:00', 'comp'),
(2, 'Signup_email' , 'systm', '<template deflang="en">\r\n\r\n<TO email="$user_firstname $user_lastname &lt;$user_email&gt;"/>\r\n\r\n<subject lang="en" value="Confimation of registration"/>\r\n\r\n\r\n<text lang="en">\r\nDear $user_firstname $user_lastname!\r\n\r\nPlease click on the link below:\r\n\r\nPlease copy the following link into your browser:\r\n$link\r\n\r\nThank you\r\n\r\n</text>\r\n\r\n<html lang="en">\r\n<p>Dear $user_firstname $user_lastname!</p>\r\n\r\n<p>Please click on the link below:</p>\r\n\r\n<a href="$link">Click Here</a>\r\n\r\n<p>Thank You</p>\r\n \r\n</html>\r\n\r\n</template>\r\n', '2010-09-27 15:59:35', 'comp'),
(3, 'email_res'    , 'systm', '<?xml version="1.0" encoding="UTF-8" ?>\r\n<template deflang="en">\r\n<TO email="$user_firstname $user_lastname &lt;$user_email&gt;"/>\r\n<CC email="log@demo.mail.com"/>\r\n<BCC email="log@demo.mail.com"/>\r\n\r\n<subject lang="en" value="Confirmation of your tickets, order no. $order_id"/>\r\n\r\n<text lang="en">\r\nTEST SYSTEM - PLEASE EDIT IN TEMPLATES!\r\n\r\nDear $user_firstname $user_lastname!\r\n\r\nThis E-Mail confirms your ticket order from Demo \r\nYour reservation number is: $order_id\r\n\r\nYour tickets are only reserved for a limited time. To purchase the tickets please go to: $order_link$order_id\r\nThank you very much.\r\n\r\n$order_date\r\n\r\n</text>\r\n\r\n<html lang="en">\r\n<b>TEST SYSTEM - PLEASE EDIT IN TEMPLATES!</b>\r\n\r\n<p>Dear $user_firstname $user_lastname!</p>\r\n\r\n<p>This E-Mail confirms your ticket order from Demo </p>\r\n<p>Your purchase number is: <b>$order_id</b></p>\r\n\r\n<p>Your tickets are only reserved for a limited time. To purchase the tickets please go <a href="$order_link$order_id"> here</a>\r\n</p>\r\n\r\n<p>\r\nThank you very much.\r\n</p><p>\r\n$order_date\r\n\r\n</p>\r\n</html>\r\n</template>', '2010-09-27 15:58:20', 'comp'),
(4, 'Receipt_pdf2' , 'pdf2', '{literal}\r\n<style type="text/css">\r\n<!--\r\ntable	{ vertical-align: middle; }\r\ntr	{ vertical-align: middle; }\r\ntd	{ vertical-align: middle; }\r\n}\r\n-->\r\n</style>\r\n{/literal}\r\n\r\n<page backcolor="#FEFEFE" backtop="0" backbottom="30mm" footer="date;;heure;page" style="font-size: 8pt">\r\n\r\n<h1 align="center">{!tmp_receipt!}</h1><br>\r\n\r\n	<table align="center" cellspacing="0" style="width: 95%; text-align: left">\r\n		<tr>\r\n			<td style="width:10%; border-left-style:solid; border-left-width:1px; border-top-style:solid; border-top-width:1px"><b>{!tmp_sold_to!}</b></td>\r\n			<td style="width:40%; border-right-style:solid; border-right-width:1px; border-top-style:solid; border-top-width:1px">&nbsp;</td>\r\n			<td style="width:50%; border-left-style:solid; border-left-width:1px; text-align:center" rowspan="2">\r\n			{if $organizer_logo}\r\n			<img src=''{$_SHOP_files}{$organizer_logo}''/><br><br>\r\n			{/if}\r\n			{$organizer_name}<br>\r\n			{$organizer_address}<br>\r\n			{$organizer_ort}, {$organizer_state}&nbsp; {$organizer_plz}<br>\r\n			{!user_phone!}: {$organizer_phone}<br>\r\n			{!user_fax!}: {$organizer_fax}<br>\r\n			{$organizer_email}</td><br>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:10%; border-left-style:solid; border-left-width:1px; border-bottom-style:solid; border-bottom-width:1px">&nbsp;</td>\r\n			<td style="border-right-style:solid; border-right-width:1px; border-bottom-style:solid; border-bottom-width:1px" width="40%">\r\n\r\n\r\n			<h4>{$user_firstname|capitalize} {$user_lastname|capitalize}<br>\r\n				{$user_address|capitalize}<br>\r\n					{if $user_address1|capitalize:true}\r\n						{$user_address1|capitalize:true}<br>\r\n					{/if} {$user_city|capitalize}, {$user_state|capitalize} {$user_zip} <br>\r\n				{$user_email}<br>\r\n				{$user_phone}</h4><br><br></td>\r\n		</tr>\r\n	</table>\r\n\r\n	<br><br><br>\r\n	<table align="center" cellspacing="3" style="width: 95%; text-align: left; border-collapse:collapse" border="1" bordercolor="#000000">\r\n		<tr>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center"height="20">\r\n			{!tmp_order_date!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_order_number!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_order_method!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_shipping_method!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_payment_method!}</th>\r\n		</tr>\r\n		<tr>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$order_date|date_format:"%B %e, %Y"}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$order_id}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{if $order_place=="www"}On-Line{else}Box Office{/if}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$handling_shipment|capitalize}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$handling_payment|capitalize}</td>\r\n		</tr>\r\n</table>\r\n	<br><br><br>\r\n{foreach key=cid item=con from=$bill name=foo}\r\n	{if $smarty.foreach.foo.first}\r\n\r\n		<table align="center" cellspacing="0" cellpadding="0" style="width: 95%;" border="1" bordercolor="#000000" >\r\n			<tr>\r\n				<th style="width: 30%; background: #E7E7E7; text-align: center;" height="20">\r\n				{!tmp_description!}</th>\r\n				<th style="width: 10%; background: #E7E7E7; text-align: center;">\r\n				{!temp_quantity!}</th>\r\n				<th style="width: 17%; background: #E7E7E7; text-align: center;">\r\n				{!tmp_category!}</th>\r\n				<th style="width: 17%; background: #E7E7E7; text-align: center;">\r\n				{!tmp_discounts!}</th>\r\n				<th style="width: 13%; background: #E7E7E7; text-align: right;">\r\n				{!tmp_price!}</th>\r\n				<th style="width: 13%; background: #E7E7E7; text-align: right;">\r\n				{!tmp_total!} </th>\r\n			</tr>\r\n		</table><br>\r\n\r\n	{/if}\r\n\r\n	<table align="center" cellspacing="0"  style="width: 95%;  font-size:; border-left-width:0px; border-right-width:0; border-top-width:0px; border-bottom-width:0px" border="0" cellpadding="0">\r\n		<tr>\r\n			<td style="border-style:none; border-width:medium; width: 30%; background: #F7F7F7; text-align: center; ">{$con.event_name}</td>\r\n			<td style="border-style:none; border-width:medium; width: 10%; background: #F7F7F7; text-align: center">{$con.qty}</td>\r\n			<td style="border-style:none; border-width:medium; width: 17%; background: #F7F7F7; text-align: center">{$con.category_name}</td>\r\n			<td style="border-style:none; border-width:medium; width: 17%; background: #F7F7F7; text-align: center">{$con.discount_name}</td>\r\n			<td style="border-style:none; border-width:medium; width: 13%; background: #F7F7F7; text-align: right">{valuta value=$con.seat_price|string_format:"%.2f"}</td>\r\n			<td style="border-style:none; border-width:medium; width: 13%; background: #F7F7F7; text-align: right">\r\n				{valuta value=$con.total|string_format:"%.2f"}</td>\r\n		</tr>\r\n	</table>\r\n{/foreach}<br>\r\n\r\n	  	<table align="center" style="width: 95%; " border="0">\r\n			<tr>\r\n				<th style="background-position: 0% 0%; width: 87%; text-align: right; background-image:none; background-repeat:repeat; background-attachment:scroll">\r\n				{!tmp_subtotal!} \r\n				</th>\r\n				<th style="width: 13%; background: #F7F7F7; text-align: right;">{valuta value=$order_subtotal|string_format:"%.2f"}</th>\r\n			</tr>\r\n</table>\r\n\r\n\r\n<table align="center" style="width: 95%; " border="0">\r\n	<tr>\r\n		<th style="background-position: 0% 0%; width: 87%; text-align: right; background-image:none; background-repeat:repeat; background-attachment:scroll">\r\n		{!tmp_fee!} \r\n		</th>\r\n		<td style="width: 13%; background: #F7F7F7; text-align: right;">\r\n{valuta value=$order_fee|string_format:"%.2f"}</td>\r\n	</tr>\r\n</table>\r\n\r\n\r\n	<table align="center" style="width: 95%; " border="0">\r\n		<tr>\r\n			<th style="background-position: 0% 0%; width: 87%; text-align: right; background-image:none; background-repeat:repeat; background-attachment:scroll">\r\n			<b>{!tmp_order_total!}</b> </th>\r\n			<th style="width: 13%; background: #F7F7F7; text-align: right;"><b>{valuta value=$order_total_price|string_format:"%.2f"}</b></th>\r\n		</tr>\r\n		\r\n</table>\r\n<nobreak>\r\n<br><br><br>\r\n	<table align="center" width="95%" cellspacing="1">\r\n		<tr>	\r\n			<td align="center"><h1>{!tmp_thank_you!}</h1></td>\r\n		</tr>\r\n	</table>\r\n</nobreak>\r\n</page>', '2010-09-25 18:09:58', 'comp'),
(5, 'Invoice_pdf2' , 'pdf2', '{literal}\r\n<style type="text/css">\r\n<!--\r\ntable	{ vertical-align: middle; }\r\ntr	{ vertical-align: middle; }\r\ntd	{ vertical-align: middle; }\r\n}\r\n-->\r\n</style>\r\n{/literal}\r\n\r\n<page backcolor="#FEFEFE" backtop="0" backbottom="30mm" footer="date;;heure;page" style="font-size: 8pt">\r\n<br>\r\n	\r\n	<table align="center" border="0" style="border-collapse: collapse" id="table1" width="95%">\r\n		<tr>\r\n			<td><h1>{!tmp_invoice!}</h1></td>\r\n		</tr>\r\n	</table>\r\n\r\n<table align="center" cellspacing="0" style="border-width:0px; width: 95%; text-align: left">\r\n		<tr>\r\n			<td style="border-style:none; border-width:medium; width:50%; ">&nbsp;</td>\r\n			<td style="border-style:none; border-width:medium; width:50%; text-align:center" rowspan="2">\r\n			{if $organizer_logo}\r\n			<img src=''{$_SHOP_files}{$organizer_logo}'' width="75" height="75"/><br><br>\r\n			{/if}\r\n			{$organizer_name}<br>\r\n			{$organizer_address}<br>\r\n			{$organizer_ort}, {$organizer_state}&nbsp; {$organizer_plz}<br>\r\n			{!user_phone!}:{$organizer_phone}<br>\r\n			{!user_fax!}:{$organizer_fax}<br>\r\n			{$organizer_email}</td><br>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-style:none; border-width:medium; " width="50%">\r\n\r\n\r\n			<h4>{$user_firstname|capitalize} {$user_lastname|capitalize}<br>\r\n				{$user_address|capitalize}<br>\r\n					{if $user_address1|capitalize:true}\r\n						{$user_address1|capitalize:true}<br>\r\n					{/if} {$user_city|capitalize}, {$user_state|capitalize} {$user_zip} <br>\r\n				{$user_email}<br>\r\n				{$user_phone}<br></h4></td>\r\n		</tr>\r\n		<tr>\r\n			<td width="100%">&nbsp;</td>\r\n			<td width="100%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan="2" width="100%">{$user_firstname},<br><br>{!tmp_inv_message!}\r\n			</td>\r\n		</tr>\r\n</table>\r\n\r\n	<br><br><br>\r\n	<table align="center" cellspacing="3" style="width: 95%; text-align: left; border-collapse:collapse" border="1" bordercolor="#000000">\r\n		<tr>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center" height="20">\r\n			{!tmp_order_date!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_order_number!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_order_method!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_shipping_method!}</th>\r\n			<th style="border-style:solid; border-width:1px; width: 20%; background: #E7E7E7; text-align: center">\r\n			{!tmp_payment_method!}</th>\r\n		</tr>\r\n		<tr>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$order_date|date_format:"%B %e, %Y"}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$order_id}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{if $order_place=="www"}On-Line{else}Box Office{/if}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$handling_shipment|capitalize}</td>\r\n			<td width="20%" style="border-style:solid; border-width:1px; text-align: center">{$handling_payment|capitalize}</td>\r\n		</tr>\r\n</table>\r\n	<br><br><br>\r\n{foreach key=cid item=con from=$bill name=foo}\r\n	{if $smarty.foreach.foo.first}\r\n\r\n		<table align="center" cellspacing="0" cellpadding="0" style="width: 95%;" border="1" bordercolor="#000000" >\r\n			<tr>\r\n				<th style="width: 30%; background: #E7E7E7; text-align: center;" height="20">\r\n				{!tmp_description!}</th>\r\n				<th style="width: 10%; background: #E7E7E7; text-align: center;">\r\n				{!temp_quantity!}</th>\r\n				<th style="width: 17%; background: #E7E7E7; text-align: center;">\r\n				{!tmp_category!}</th>\r\n				<th style="width: 17%; background: #E7E7E7; text-align: center;">\r\n				{!tmp_discounts!}</th>\r\n				<th style="width: 13%; background: #E7E7E7; text-align: right;">\r\n				{!tmp_price!}</th>\r\n				<th style="width: 13%; background: #E7E7E7; text-align: right;">\r\n				{!tmp_total!}</th>\r\n			</tr>\r\n		</table><br>\r\n\r\n	{/if}\r\n\r\n	<table align="center" cellspacing="0"  style="width: 95%;  font-size:; border-left-width:0px; border-right-width:0; border-top-width:0px; border-bottom-width:0px" border="0" cellpadding="0">\r\n		<tr>\r\n			<td style="border-style:none; border-width:medium; width: 30%; background: #F7F7F7; text-align: center; ">{$con.event_name}</td>\r\n			<td style="border-style:none; border-width:medium; width: 10%; background: #F7F7F7; text-align: center">{$con.qty}</td>\r\n			<td style="border-style:none; border-width:medium; width: 17%; background: #F7F7F7; text-align: center">{$con.category_name}</td>\r\n			<td style="border-style:none; border-width:medium; width: 17%; background: #F7F7F7; text-align: center">{$con.discount_name}</td>\r\n			<td style="border-style:none; border-width:medium; width: 13%; background: #F7F7F7; text-align: right">{valuta value=$con.seat_price|string_format:"%.2f"}</td>\r\n			<td style="border-style:none; border-width:medium; width: 13%; background: #F7F7F7; text-align: right">\r\n				{valuta value=$con.total|string_format:"%.2f"}</td>\r\n		</tr>\r\n	</table>\r\n{/foreach}<br>\r\n\r\n	  	<table align="center" style="width: 95%; " border="0">\r\n			<tr>\r\n				<th style="background-position: 0% 0%; width: 87%; text-align: right; background-image:none; background-repeat:repeat; background-attachment:scroll">\r\n				{!tmp_subtotal!} \r\n				</th>\r\n				<th style="width: 13%; background: #F7F7F7; text-align: right;">{valuta value=$order_subtotal|string_format:"%.2f"}</th>\r\n			</tr>\r\n</table>\r\n\r\n\r\n<table align="center" style="width: 95%; " border="0">\r\n	<tr>\r\n		<th style="background-position: 0% 0%; width: 87%; text-align: right; background-image:none; background-repeat:repeat; background-attachment:scroll">\r\n		{!tmp_fee!} \r\n		</th>\r\n		<td style="width: 13%; background: #F7F7F7; text-align: right;">\r\n{valuta value=$order_fee|string_format:"%.2f"}</td>\r\n	</tr>\r\n</table>\r\n\r\n\r\n	<table align="center" style="width: 95%; " border="0">\r\n		<tr>\r\n			<th style="background-position: 0% 0%; width: 87%; text-align: right; background-image:none; background-repeat:repeat; background-attachment:scroll">\r\n			<b>{!tmp_order_total!}</b> </th>\r\n			<th style="width: 13%; background: #F7F7F7; text-align: right;"><b>{valuta value=$order_total_price|string_format:"%.2f"}</b></th>\r\n		</tr>\r\n		\r\n</table>\r\n<nobreak>\r\n<br><br><br>\r\n	<table align="center" width="95%" cellspacing="1">\r\n		<tr>	\r\n			<td align="center"><h1>{!tmp_thank_you!}</h1></td>\r\n		</tr>\r\n	</table>\r\n</nobreak>\r\n</page>', '2010-09-25 18:09:58', 'comp'),
(6, 'Ticket_pdf2'  , 'pdf2', '{literal}\r\n<style type="text/css">\r\n<!--\r\ntable	{ vertical-align: middle; }\r\ntr		{ vertical-align: middle; }\r\ntd		{ vertical-align: middle; }\r\n}\r\n-->\r\n</style>\r\n{/literal}\r\n<page>\r\n<br><br>\r\n	<table align="center" cellspacing="0" style="width: 90%">\r\n		<tr>\r\n			<td  colspan="6" style="border-left-style: solid; border-left-width: 1px; border-top-style: solid; border-top-width: 1px" align="center" width="50%">\r\n			<b><i>{$organizer_name} Presents</i></b></td>\r\n			<td  colspan="4" style="border-right-style: solid; border-right-width: 1px; border-top-style: solid; border-top-width: 1px" rowspan="2" width="50%"><barcode type="CODE39"; style="width:350px" value="{$barcode_text}"></barcode></td>\r\n		</tr>\r\n		<tr>\r\n			<td  colspan="6" style="border-left-style: solid; border-left-width: 1px; vertical-align:top" align="center" width="50%">\r\n				{if $event_name|count_characters:true < 20}\r\n					<h1 align="center">{$event_name}</h1>\r\n				{else}\r\n					<h4 align="center">{$event_name}</h4>\r\n				{/if}\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td colspan="4" bgcolor="#E6E6E6" align="center" width="40%"><b>\r\n			Location Details</b></td>\r\n			<td style= "width: 5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td bgcolor="#E6E6E6" colspan="2" align="center" width="40%"><b>Date \r\n			and Time</b></td>\r\n			<td style="border-right-style: solid; border-right-width: 1px;" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td style="bgcolor" colspan="2" align="center" width="20%" rowspan="5">\r\n			<img border="0" src="{$_SHOP_files}{$ort_image}" width="90"></td>\r\n			<td style="bgcolor" colspan="2" align="center" width="20%">{$ort_name}</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="20%"><b>&nbsp;Event Date:</b></td>\r\n			<td width="20%">{$event_date|date_format:" %a - %h %e, %Y"}</td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%"></td>\r\n			<td style="bgcolor" colspan="2" align="center" width="20%">{$ort_address}</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="20%"><b>&nbsp;Start Time:</b></td>\r\n			<td width="20%">{$event_time|date_format:" %I:%M %p"}</td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td style="bgcolor" colspan="2" align="center" width="20%">{$ort_city}, \r\n			{$ort_state}&nbsp; {$ort_zip}</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="20%"><b>&nbsp;Doors Open:</b></td>\r\n			<td width="20%">{$event_open|date_format:" %I:%M %p"}</td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%"></td>\r\n			<td style="bgcolor" colspan="2" align="center" width="20%">{$ort_phone}</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td style="align=" colspan="2" width="40%">&nbsp;</td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td style="bgcolor" align="center" colspan="2" width="20%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td colspan="2" style="align" width="40%">&nbsp;</td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td align="center" colspan="4" style="bgcolor" width="40%" bgcolor="#E6E6E6">\r\n				{if $pmp_name}\r\n					<b>{$pmp_name}</b>\r\n				{else} \r\n					<b>Seating Section</b>\r\n				{/if}\r\n			</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td bgcolor="#E6E6E6" align="center" colspan="2" style="align" width="40%"><b>\r\n			Ticket Details</b></td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td width="5%"><b>Zone:</b></td>\r\n			<td width="15%" align="left"><i>{$pmz_short_name}</i></td>\r\n			<td width="10%">&nbsp;</td>\r\n			<td width="10%" align="right">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td "width=20%" width="20%"><b>&nbsp;Type:</b></td>\r\n			<td width="20%"><i>{$category_name}</i></td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%"></td>\r\n			<td align="right" width="5%">&nbsp;</td>\r\n			<td width="15%">&nbsp;</td>\r\n			<td width="10%" align="right"><b>Row #:&nbsp;</b></td>\r\n			<td width="10%"><i>{$seat_row_nr}</i></td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="20%"><b>&nbsp;Price:</b></td>\r\n			<td width="20%"><i>$ {$category_price} <small>{$organizer_currency}</small></i></td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td align="right" width="5%">&nbsp;</td>\r\n			<td width="15%">&nbsp;</td>\r\n			<td width="10%" align="right"><b>Seat #:&nbsp;</b></td>\r\n			<td width="10%"><i>{$seat_nr}</i></td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="20%">&nbsp;</td>\r\n			<td width="20%">&nbsp;</td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px" width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="15%">&nbsp;</td>\r\n			<td width="10%" align="right">&nbsp;</td>\r\n			<td width="10%" align="right"><i>&nbsp;</i></td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="5%">&nbsp;</td>\r\n			<td width="20%">{if $discount_name}<b>&nbsp;Discount:</b>{/if}</td>\r\n			<td width="20%"><i>{$discount_name}</i></td>\r\n			<td style="border-right-style: solid; border-right-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-left-style: solid; border-left-width: 1px; border-bottom-style: solid; border-bottom-width: 1px" width="5%">&nbsp;</td>\r\n			<td style="border-bottom-style: solid; border-bottom-width: 1px" width="5%">&nbsp;</td>\r\n			<td style="border-bottom-style: solid; border-bottom-width: 1px" width="15%">&nbsp;</td>\r\n			<td style="border-bottom-style: solid; border-bottom-width: 1px" width="10%">&nbsp;</td>\r\n			<td style="border-bottom-style: solid; border-bottom-width: 1px" width="10%" align="right">&nbsp;</td>\r\n			<td style="border-bottom-style:solid; border-bottom-width:2px" style="border-bottom-style: solid; border-bottom-width: 1px" width="5%">&nbsp;</td>\r\n			<td style="border-bottom-style:solid; border-bottom-width:2px" style="border-bottom-style: solid; border-bottom-width: 1px" width="5%">&nbsp;</td>\r\n			<td style="border-bottom-style: solid; border-bottom-width: 1px" width="20%">&nbsp;</td>\r\n			<td style="border-bottom-style: solid; border-bottom-width: 1px" width="20%">&nbsp;</td>\r\n			<td style="border-right-style: solid; border-right-width: 1px; border-bottom-style: solid; border-bottom-width: 1px" width="5%">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan="10" align="right" height="30\r\n			" width="100%"><small>&copy;&nbsp;Fusion Ticket</small></td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan="10" style="vertical-align: top" align="justify" width="100%">\r\n				<b>NOTE TO PURCHASER: TREAT THESE TICKETS AS YOU WOULD ANY OTHER \r\n				VALUABLE OR CASH.</b>  Unauthorized duplication, alteration, or \r\n				sale of this ticket may prevent admittance to the event. Present \r\n				this page at the time of admission to be scanned. The unique bar \r\n				code on this ticket allows only one entry per ticket</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan="10" style=" border-top-style: dashed; border-top-width: 3px" width="100%"></td>\r\n		</tr>\r\n	</table>\r\n</page>', '2010-09-27 21:37:13', 'comp'),
(7, 'Tickets_Attached', 'email', '<?xml version="1.0" encoding="UTF-8" ?>\r\n<template deflang="en">\r\n\r\n<TO email="$user_firstname $user_lastname &lt;$user_email&gt;"/>\r\n<BCC email="demo@demotickets.com"/>\r\n<HEADER name="2" value="1"/>\r\n\r\n<subject lang="en" value="Your tickets (order nr. $order_id)"/>\r\n\r\n<text lang="en"> \r\n\r\nDear $user_firstname $user_lastname! \r\n\r\nYour tickets are attached at the top of this email.  \r\n\r\nYou will need Adobe reader to view and print your tickets.  If you do not have Adobe Reader installed visit: http://www.adobe.com for a free copy.\r\n\r\nAny problems please email demotickets@demotheater.com\r\n\r\n</text>\r\n\r\n<html lang="en">\r\n<p>Dear $user_firstname $user_lastname!</p>\r\n\r\n<p>Your tickets are attached at the top of this email. </p>\r\n\r\n<p>You will need Adobe reader to view and print your tickets.  If you do not have Adobe Reader installed <a href="http://www.adobe.com">Click Here</a> for a free copy.</p>\r\n\r\n\r\n<p><i>If you have questions or problems please email: demotickets@demotheater.com></i></p>\r\n</html>\r\n\r\n<order_pdf order_id="$order_id" name="order_$order_id.pdf" mark_send="yes"/>\r\n\r\n</template>', '2010-09-27 16:04:04', 'comp'),
(8, 'Order_Confirmed' , 'email', '<?xml version="1.0" encoding="UTF-8" ?>\r\n<template deflang="en">\r\n\r\n<TO email="$user_firstname $user_lastname &lt;$user_email&gt;"/>\r\n\r\n<subject lang="en" value="Confirmation of your tickets, order no. $order_id"/>\r\n\r\n<text lang="en">Dear $user_firstname $user_lastname!\r\n\r\nThis E-Mail confirms your ticket order from Demo Tickets. \r\n\r\nYour Receipt  Number: $order_id\r\n\r\nThank you for your support!\r\n\r\n$order_date\r\n\r\n</text>\r\n\r\n<html lang="en">\r\n\r\n<p>Dear $user_firstname $user_lastname!</p>\r\n\r\n<p>This E-Mail confirms your ticket order from Demo Tickets. \r\n</p>\r\n<p>Your Receipt  Number: <b>$order_id</b>\r\n</p>\r\n\r\n<p>\r\nThank you for your support!</p>\r\n<p> $order_date </p>\r\n\r\n</html>\r\n\r\n</template>', '2010-09-27 16:00:13', 'comp'),
(9, 'Reserved_Invoice_Attached', 'email', '<?xml version="1.0" encoding="UTF-8" ?>\r\n<template deflang="en">\r\n\r\n<TO email="$user_firstname $user_lastname &lt;$user_email&gt;"/>\r\n\r\n<subject lang="en" value="Invoice for order no. $order_id"/>\r\n\r\n<text lang="en">Dear $user_firstname $user_lastname!\r\n\r\nThis E-Mail confirms your reservation.\r\nYour order number is: $order_id\r\nThe invoice is attached. You have 10 days to pay the invoice.\r\n\r\nOnce we receive your payment we will send your e-tickets by email.\r\n\r\nThank you for your support"\r\n\r\nOrder Date:$order_date\r\nOrder ID: $order_id\r\n\r\nTo contact us email us here: demo@demotheater.com\r\n\r\n</text>\r\n\r\n<html lang="en">\r\n<p>Dear $user_firstname $user_lastname!</p>\r\n\r\n<p>This E-Mail confirms your reservation.</p>\r\n<p>Your purchase number is: <b>$order_id</b></p>\r\n\r\n<p>\r\nThe invoice is attached. You have 10 days to pay the invoice.\r\nOnce we receive your payment we will send your e-tickets by email.\r\n</p>\r\n\r\n<p>\r\nThank you for your support!</p>\r\n<p>\r\nOrder Date:$order_date\r\nOrder ID: $order_id\r\n</p>\r\n<p>To contact us, email: <a href="mailto:demo@demotheater.com">Sales</a>\r\n</p>\r\n</html>\r\n\r\n\r\n<order_pdf order_id="$order_id" name="invoice_$order_id.pdf" mark_send="no" mode="summary"/>\r\n\r\n</template>', '2010-09-27 16:00:32', 'comp');