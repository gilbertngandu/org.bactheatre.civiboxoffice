{if $seatInfo}
  <div id="seatinfodefault" style="display: none;">
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
      cj('div.event_fees-group table').after('<div id="seatinfo"></div>');
    }
    cj('div#seatinfo').html(cj('div#seatinfodefault').html());
    cj('div#seatinfodefault').html('');
  });
  </script>
  {/literal}
{/if}
