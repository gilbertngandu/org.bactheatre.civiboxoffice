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
 *}<!-- $Id: process_dateselect.tpl 1788 2012-05-12 11:14:15Z nielsNL $ -->        <tr>
          <td colspan='5' align='center'>
            <form action='view.php' method='get'>
              <table border='0' width='100%' style='border-top:#45436d 1px solid;border-bottom:#45436d 1px solid;' >
                <tr>
                  <td class='admin_info' width='12%'>{!date_from!}</td>
                  <td class='note'  width='35%'>
                    <input type='text' name='fromd' value='{$smarty.get.fromd}' size='2' maxlength='2' onKeyDown="TabNext(this,'down',2)" onKeyUp="TabNext(this,'up',2,this.form['fromm'])" /> -
                    <input type='text' name='fromm' value='{$smarty.get.fromm}' size='2' maxlength='2' onKeyDown="TabNext(this,'down',2)" onKeyUp="TabNext(this,'up',2,this.form['fromy'])" /> -
                    <input type='text' name='fromy' value='{$smarty.get.fromy}' size='4' maxlength='4'/> {!dd_mm_yyyy!}
                  </td>
                  <td class='admin_info' width='12%'>{!date_to!}</td>
                  <td class='note'  width='35%'>
                    <input type='text' name='tod' value='{$smarty.get.tod}' size='2' maxlength='2' onKeyDown="TabNext(this,'down',2)" onKeyUp="TabNext(this,'up',2,this.form['tom'])" /> -
                    <input type='text' name='tom' value='{$smarty.get.tom}' size='2' maxlength='2' onKeyDown="TabNext(this,'down',2)" onKeyUp="TabNext(this,'up',2,this.form['toy'])" /> -
                    <input type='text' name='toy' value='{$smarty.get.toy}' size='4' maxlength='4' /> {!dd_mm_yyyy!}
                  </td>
                  <td class='admin_info' colspan='2'>
                    <input type='submit' name='submit' value='{!submit!}' />
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
