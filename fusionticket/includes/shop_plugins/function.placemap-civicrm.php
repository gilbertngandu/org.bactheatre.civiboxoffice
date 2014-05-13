<?php
/**
%%%copyright%%%
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
 */

function smarty_function_placemap($params, $smarty){

    $pz = preg_match(strtolower('/no|0|false/'), $params['print_zone']);
    $imagesize = is ($params['imagesize'], 16);
    return placeMapDraw($params['category'], $params['restrict'], !$pz, $params['area'], $imagesize, is($params['seatlimit'],15));

}

function placeMapDraw($category, $restrict = false, $print_zone = true, $area = 'www', $imagesize = 16, $seatlimit = 15) {
    global $_SHOP;
    $imgpath = CRM_Core_Resources::singleton()->getUrl('org.bactheatre.civiboxoffice', 'fusionticket/images/');

    // $l_row = ' '.con('place_row').' ';
    // $l_seat = ' '.con('place_seat').' ';
    $l_row = ' : ';
    $l_seat = ' : ';

    $cat_ident = $category['category_ident'];
    $cat_num = 0;
    switch ($category['category_numbering']) {
        case 'both':
            $cat_num = 3;
            break;
        case 'rows':
            $cat_num = 2;
            break;
        case 'seat':
            $cat_num = 1;
            break;
    }
    $res = '';
    $pmp = PlaceMapPart::loadFull($category['category_pmp_id']);
    if (!$pmp) {
      return '';
    }
  //  print_r($category);
    $cats = $pmp->categories;
    $zones = $pmp->zones;

    $pmp->check_cache();

    if ($restrict) {
        $bounds = $pmp->category_bounds($cat_ident);
        $left   = $bounds['left'];
        $right  = $bounds['right'];
        $top    = $bounds['top'];
        $bottom = $bounds['bottom'];

    } else {
        $left   = 0;
        $right  = $pmp->pmp_width - 1;
        $top    = 0;
        $bottom = $pmp->pmp_height - 1;
    }
/*    if ($pmp->pmp_shift) {
        $cspan = 'colspan=2';
        $ml[1] = $mr[0] = '<img src="".$imgpath."dot.gif" style="width:5;height=10">';
        $res .= '<tr>';
        $width2 = ($right - $left) * 2 + 1;
        for ($k = 0; $k <= $width2; $k++) {
            $res .= '<img src="".$imgpath."dot.gif" style="width:5;height:10">';
        }
        $res .= '<br/>';
    }
*/
//    print_r($pmp);
 // $res = print_r($bounds,true).' '.$left.', '.$right.','. $top.','. $bottom;

   $ml[1] = $ml[0] = '';
   $mr[1] = $mr[0] = '';
    if ($pmp->pmp_shift) {
      $cspan = "colspan='2'";
     // $ml[1] = "<td class='ShiftRight pm_seatmap'>z</td>";
       $ml[1] = $mr[0] = "<td class='pm_shiftright' ><img style='width:".((int)($imagesize/2))."px;' border=0 src='".$imgpath."dot.gif' height='100%'></td>";
        $res .= '<tr>';
        $width2 = ($right - $left) * 2 + 2;
        for ($k = 0; $k <= $width2; $k++) {
            $res .= '<td class="pm_shiftright" style="heigth:1px;"><img src="'.$imgpath.'dot.gif" style="width:'.((int)($imagesize/2)).'px; height:0px"></td>';
        }
        $res .= '</tr>';

     } else {
      $cspan = "";
    }

    for ($j = $top; $j <= $bottom; $j++) {
       $first = '';
       $res .= '<tr>';
       $res .= $ml[$j % 2];

        for ($k = $left; $k <= $right; $k++) {
            $seat = $pmp->data[$j][$k];
            $sty ='';
            $reszz= "&nbsp;";
            $cspan =($pmp->pmp_shift)?'colspan=2':'';
            if ($seat[PM_ZONE] === 'L') {
                if ($seat[PM_LABEL_TYPE] == 'RE' and $irow = $pmp->data[$j][$k + 1][PM_ROW]) {
                    $reszz= "<div class='pm_seatmap'>$irow</div>";
                } elseif ($seat[PM_LABEL_TYPE] == 'RW' and $irow = $pmp->data[$j][$k - 1][PM_ROW]) {
                    $reszz= "<div class='pm_seatmap'>$irow</div>";
                } elseif ($seat[PM_LABEL_TYPE] == 'SS' and $iseat = $pmp->data[$j + 1][$k][PM_SEAT]) {
                    $reszz= "<div class='pm_seatmap'>$iseat</div>";
                } elseif ($seat[PM_LABEL_TYPE] == 'SN' and $iseat = $pmp->data[$j - 1][$k][PM_SEAT]) {
                    $reszz= "<div class='pm_seatmap'>$iseat</div>";
                } else
              if ($seat[PM_LABEL_TYPE] == 'T') {
                  $cspan = 'style="text-align:center;" colspan="'.(($pmp->pmp_shift)? ($seat[PM_LABEL_SIZE]*2):($seat[PM_LABEL_SIZE])).'"';
                  if ($seat[PM_LABEL_SIZE] == 0) {
                     continue;
                  } elseif (strlen($seat[PM_LABEL_TEXT])>3 * $seat[PM_LABEL_SIZE]){
                     $reszz= "<img class='pm_seatmap' src='".$imgpath."info.gif' alt='{$seat[PM_LABEL_TEXT]}' title='{$seat[PM_LABEL_TEXT]}'>";
                  } else {
                     $reszz= "{$seat[PM_LABEL_TEXT]}";
                  }
                } else
                if ($seat[PM_LABEL_TYPE] == 'E') {
                  $reszz = "<img class='pm_seatmap' src='".$imgpath."exit.gif' alt='exit' title='exit'>";
                } else {
                  $reszz = "<img class='pm_seatmap' style='{$sty};border-color:red' border=0 src='".$imgpath."dot.gif' title='{$seat[PM_LABEL_TYPE]}'>";
                }
            } elseif ($seat[PM_ZONE] and $seat[PM_CATEGORY]) {
                $zone = $zones[$seat[PM_ZONE]];
                $cat  = $cats[$seat[PM_CATEGORY]];
                $cat_id = $seat[PM_CATEGORY];
           //     $sty .= "background-color:{$zone->pmz_color};";

                if (isset($pmp->data[$j - 1][$k][PM_CATEGORY]) && ($pmp->data[$j - 1][$k][PM_CATEGORY] != $cat_id)) {
                    $sty .= "border-top-color: {$cat->category_color};";
                }

                if (isset($pmp->data[$j + 1][$k][PM_CATEGORY]) && ($pmp->data[$j + 1][$k][PM_CATEGORY] != $cat_id)) {
                    $sty .= "border-bottom-color: {$cat->category_color};";
                }

                if (isset($pmp->data[$j][$k - 1][PM_CATEGORY]) && ($pmp->data[$j][$k - 1][PM_CATEGORY] != $cat_id)) {
                    $sty .= "border-left-color: {$cat->category_color};";
                }

                if (isset($pmp->data[$j][$k + 1][PM_CATEGORY]) && ($pmp->data[$j][$k + 1][PM_CATEGORY] != $cat_id)) {
                    $sty .= "border-right-color: {$cat->category_color};";
                }
                $sty .= "; ";

                //Empty seats
                if ((! isset($seat[PM_STATUS])) || ($seat[PM_STATUS]== PM_STATUS_FREE)) {
                    if ($seat[PM_CATEGORY] == $cat_ident) {
                        $reszz = "<input type='hidden' id='place{$seat[PM_ID]}' class='myplaces' name='place[{$seat[PM_ID]}]' value='0'>";
                        $reszz .= "<img class='pm_seatmap pm_check' style='{$sty}' id='seat{$seat[PM_ID]}' onclick='javascript:gridClick({$seat[PM_ID]});' src='".$imgpath."seatfree.png' title='";
                        if ($print_zone) {
                            $reszz .= $zone->pmz_name . ' ';
                        }
                        if (($cat_num & 2) and $seat[PM_ROW] != '0') {
                            $reszz .= $l_row . $seat[PM_ROW];
                        }
                        if (($cat_num & 1) and $seat[PM_SEAT] != '0') {
                            $reszz .= $l_seat . $seat[PM_SEAT];
                        }
                        $reszz .= "'>";
                    } else {
                      $reszz = "<img class='pm_seatmap' style='{$sty};background-color:Gainsboro' border=0 src='".$imgpath."seatdisable.png'>";
                    }
                    ////////////Reserved seats, they will only be selectable if you have area='pos' set in cat...tpl
                } elseif ($seat[PM_STATUS] == PM_STATUS_RESP && $area === 'pos' && $seat[PM_CATEGORY] == $cat_ident) {
                    $zone = $zones[$seat[PM_ZONE]];
                    $reszz = "<img class='pm_seatmap' style='{$sty}' src='".$imgpath."seatselect.png' title='";
                    if ($print_zone) {
                        $reszz .= $zone->pmz_name . ': ';
                    }
                    if (($cat_num & 2) and $seat[PM_ROW] != '0') {
                        $reszz .= $l_row . $seat[PM_ROW];
                    }
                    if (($cat_num & 1) and $seat[PM_SEAT] != '0') {
                        $reszz .= $l_seat . $seat[PM_SEAT];
                    }
                    $reszz .= "'>";
                } else {
                  if ($seat[PM_CATEGORY] != $cat_ident) {
                    $sty .= ';background-color:Gainsboro';
                  }
                  $reszz = "<img class='pm_seatmap' style='{$sty}' src='".$imgpath."seatused.png'>";
                }
            } elseif ($seat[PM_ZONE]) {
                $reszz = "<img class='pm_seatmap' style='{$sty}' border=0 src='".$imgpath."dot.gif'>";
            } else  {
               $reszz = "<img class='pm_seatmap' style='{$sty}' border=0 src='".$imgpath."dot.gif' />";
            }
            $res .= "<td {$cspan} class='pm_seatmap'>{$reszz}</td>";
            $first ='';
        }
        $res .= $mr[$j % 2]."</tr>";
    }

    /*            <script language=\"JavaScript\" type=\"text/javascript\" src=\"wz_tooltip.js\"></script>    ";*/


    if (isset($_SHOP->lang)) {
    	$l = $_SHOP->lang;
    }

    switch ($pmp->pmp_scene) {
        case 'south':
            $res = "<table border=0 cellspacing=0 cellpadding=0>
                      <tr>
                        <td>
                          <table class='pm_table' border=0  cellspacing=0 cellpadding=0>$res</table>
                        </td>
                      </tr>
                      <tr>
                        <td align='center' valign='middle' style='vertical-align:middle; text-align:center'>
                          <img src='".$imgpath."scene_h_en.png'>
                        </td>
                      </tr>
                    </table>";
            break;
        case 'west':
           $res = "<table border=0 cellspacing=0 cellpadding=0>
                     <tr>
                       <td align='center' valign='middle' style='vertical-align:middle; text-align:center'>
                         <img src='".$imgpath."scene_v_en.png'>
                       </td>
                       <td>
                         <table border=0 class='pm_table' cellspacing=0 cellpadding=0>$res</table>
                       </td>
                     </tr>
                   </table>";
            break;
        case 'east':
            $res = "<table border=0  cellspacing=0 cellpadding=0>
                      <tr>
                        <td>
                          <table border=0 class='pm_table' cellspacing=0 cellpadding=0>$res</table>
                        </td>
                        <td align='center' valign='middle' style='vertical-align:middle; text-align:center'>
                          <img src='".$imgpath."scene_v_en.png'>
                        </td>
                      </tr>
                    </table>";
            break;
        case 'north':
            $res = "<table border=0 cellspacing=0 cellpadding=0>
               <tr>
                 <td align='center' valign='middle' style='vertical-align:middle; text-align:center'>
                   <img src='".$imgpath."scene_h_en.png'>
                 </td>
               </tr>
               <tr>
                 <td>
                   <table border=0 class='pm_table' cellspacing=0 cellpadding=0>$res</table>
                 </td>
               </tr>
             </table>";
          break;
        default:
            $res = "<table border=0 cellspacing=0 cellpadding=0>
               <tr>
                 <td>
                   <table border=0 class='pm_table' cellspacing=0 cellpadding=0>$res</table>
                 </td>
               </tr>
             </table>";
    }
    $resx ='
         <input id="maxseats" value="'.$seatlimit.'" type="hidden" size="3" maxlength="5">
         <input id="selectedseats" value="0" type="hidden" size="3" maxlength="5">
         <script>
          function gridClick(id) {
            x = cj("#place"+id).val();';
    if ($seatlimit >= 0) {
      $resx .='
            c = cj("#maxseats").val();
            sel = cj("#selectedseats").val();
            if ((x == 0) && (c >0)) {
              c--;
              sel++;
            } else if (( x != 0) && (c < '.$seatlimit.' )) {
              c++;
              sel--;
            } else if (c == 0) {
              alert("'.con('max_seats_reached').'");
              return;
            }
            cj("#maxseats").val(c);
            cj("#selectedseats").val(sel);';
    }
      $resx .='

            if (x == 0) {
              cj("#seat"+id).attr("src","'.$imgpath.'seatselect.png");
              cj("#place"+id).val(id);
            } else {
              cj("#seat"+id).attr("src","'.$imgpath.'seatfree.png");
              cj("#place"+id).val(0);
            }
          }
     </script>
';
   $res = $resx .'
<style type="text/css">
  .pm_seatmap {
     width:'.($imagesize).'px;
     height:'.($imagesize).'px;
     font-size: '.((int)($imagesize/1.75)).'px;
  }
  .pm_shiftright {
     width:'.((int)($imagesize/2)).'px;
     height:'.($imagesize).'px;
     border: 0px;
     padding: 0px  !important;
     margin:0px  !important;
  }
    .pm_shiftright img {
     border:  0px;
     padding: 0px  !important;
     margin:  0px  !important;
  }
</style>'."\n".$res;
    return $res;

}

?>
