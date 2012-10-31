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

 *}<!-- $Id: footer.tpl 1788 2012-05-12 11:14:15Z nielsNL $ -->
{if $smarty.request.ajax neq "yes"}

			</div>

			<div id="footer">
				Powered by <a href="http://fusionticket.org">Fusion Ticket</a> - The Free Open Source Box Office
			</div>
		</div>
    {literal}
    <script type="text/javascript" language="javascript">
    	jQuery(document).ready(function(){
        //var msg = ' errors';
        var emsg = '{/literal}{printMsg|escape:'quotes' key='__Warning__' addspan=false}{literal}';
        showErrorMsg(emsg);
        var nmsg = '{/literal}{printMsg|escape:'quotes' key='__Notice__' addspan=false}{literal}';
        showNoticeMsg(nmsg);
      });
      jQuery(document).load(function(){

      });
      var showErrorMsg = function(msg){
        if(msg) {
          jQuery("#error-text-main").html(msg);
          jQuery("#error-message-main").show();
          setTimeout(function(){jQuery("#error-message-main").hide();}, 10000);
        }
      }
      var showNoticeMsg = function(msg){
        if(msg) {
          jQuery("#notice-text").html(msg);
          jQuery("#notice-message").show();
          setTimeout(function(){jQuery("#notice-message").hide();}, 7000);
        }
      }
    </script>
    {/literal}
	</body>
</html>
{/if}