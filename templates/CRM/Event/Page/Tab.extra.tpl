{if $seatInfo}
  {literal}
  <script>
  cj(document).ready(function() {
      cj('tr.crm-event-participantview-form-block-fee_amount').after(
      '<tr class="crm-event-participantview-form-block-seatinfo">' +
        '<td class="label">Event Seats</td>' +
        '<td>' +
          '<table>' +
            '<tr><th>Seat Section</th><th>Seat Row</th><th>Seat Number</th></tr>' +
            {/literal}
            {foreach from=$seatInfo item=seat}
              '<tr><td>{$seat.pmz_name}</td><td>{$seat.seat_row_nr}</td><td>{$seat.seat_nr}</td></tr>' +
            {/foreach}
            {literal}
          '</table>' +
        '</td>' +
      '</tr>');
  });
  </script>
  {/literal}
{/if}
