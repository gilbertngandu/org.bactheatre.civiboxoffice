{if $seatInfo}
  <table id="seat-info-staging-area" style="display: none;">
    <tr id="seat-info" class="crm-event-participantview-form-block-seatinfo">
      <td class="label">Event Seats</td>
      <td>
        <table>
          <tr>
            <th>Seat Section</th>
            <th>Seat Row</th>
            <th>Seat Number</th>
          </tr>
          {foreach from=$seatInfo item=seat}
            <tr>
              <td>{$seat.pmz_name}</td>
              <td>{$seat.seat_row_nr}</td>
              <td>{$seat.seat_nr}</td></tr>
            </tr>
          {/foreach}
        </table>
      </td>
    </tr>
  </table>
  {literal}
    <script type="text/javascript">
      cj(document).ready(function() {
        var fee_section = cj('.crm-event-participantview-form-block-fee_amount');
        if (fee_section.length > 0) {
          cj('#seat-info').insertAfter(fee_section);
        } else {
          cj('#seat-info').insertAfter('tr.crm-event-eventfees-form-block-line_items');
        }
      });
    </script>
  {/literal}
{/if}
{if $seatMap}
  <div id="seat-map-staging-area" style="display: none;">
    <div id="seat-map-section">
      <div class="label" style="float: left; font-size: .95em; padding: 5px; text-align: right; width: 150px;">
        Event Seating
      </div>
      <div style="overflow: auto; width: 727px;">
        {if $subscription_place_map_category and $place_map_category}
          <div id="seat-map-selector-wrapper">
            <select id="seat-map-selector">
              <option value="general_admission">General Admission</option>
              <option value="subscription">Flex Pass</option>
            </select>
          </div>
          {/if}
        <div id="seat-map">
          {$seatMap} 
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    var event_id = {$event_id};
  </script>
{/if}
