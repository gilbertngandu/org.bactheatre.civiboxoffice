{if $seatInfo}
  {literal}
  <script>
  cj(document).ready(function() {
      cj('tr.crm-event-participantview-form-block-fee_amount').before(
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

{if $seatMap}
  <div id="seatmapdefault" style="display: none;">
    <input id="ft_category_pmp_id" name="ft_category_pmp_id" value="{$ft_category_pmp_id}" type="hidden"> 
    <input id="ft_category_ident" name="ft_category_ident" value="{$ft_category_ident}" type="hidden"> 
    <input id="ft_category_numbering" name="ft_category_numbering" value="{$ft_category_numbering}" type="hidden"> 
    <input id="ft_category_event_id" name="ft_category_event_id" value="{$ft_category_event_id}" type="hidden"> 
    <input id="ft_category_id" name="ft_category_id" value="{$ft_category_id}" type="hidden"> 
    {$seatMap} 
  </div>
{/if}
