{if $seatInfo}
  <div id="seatinfodefault" style="display: none;">
    <div class="header-dark">Event Seat(s)</div>
    {if $participantIDs}
      <div id="barcodes">
      {foreach from=$participantIDs item=code key=no}
        <img style="display: block; float: left;" src="{crmResURL ext=org.bactheatre.civiboxoffice file=barcode.php}?barcode={$code}">
      {/foreach}
      </div>
    {/if}
    <table>
      <tr><th>Seat Section</th><th>Seat Row</th><th>Seat Number</th></tr>
      {foreach from=$seatInfo item=seat}
        <tr><td>{$seat.pmz_name}</td><td>{$seat.seat_row_nr}</td><td>{$seat.seat_nr}</td></tr>
      {/foreach}
    </table>
  </div>
  {literal}
  <script>
  cj(document).ready(function() {
    if (cj('div#seatinfo').length == 0) {
      cj('div.event_info-group').after('<div id="seatinfo" class="crm-group event_seats-group"></div>');
    }
    cj('div#seatinfo').html(cj('div#seatinfodefault').html());
    cj('div#seatinfodefault').html('');
  });
  </script>
  {/literal}
{/if}

