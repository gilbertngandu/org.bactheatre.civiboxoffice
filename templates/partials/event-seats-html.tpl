<tr>
  <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
    Event Seat(s)
  </th>
</tr>
<tr>
  <td colspan="2" style="padding: 4px; border-bottom: 1px solid #999;">
    <div id="barcodes">
      {foreach from=$participant_ids item=code key=no}
        <img style="display: block; float: left;" src="{crmResURL ext=org.bactheatre.civiboxoffice file=barcode.php}?barcode={$code}">
      {/foreach}
    </div>
    <table>
      <tr>
	<th>Seat Section</th>
	<th>Seat Row</th>
	<th>Seat Number</th>
      </tr>
      {foreach from=$seat_info item=seat}
	<tr>
	  <td>{$seat.pmz_name}</td>
	  <td>{$seat.seat_row_nr}</td>
	  <td>{$seat.seat_nr}</td>
	</tr>
      {/foreach}
    </table>
  </td>
</tr>
