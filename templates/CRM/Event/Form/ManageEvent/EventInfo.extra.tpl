{if $existing_seatmap}
  {literal}
  <script>
  cj(document).ready(function() {
    if (cj('tr.crm-event-manage-eventinfo-form-block-seatmap').length == 0) {
      cj('tr.crm-event-manage-eventinfo-form-block-end_date').after('<tr class="crm-event-manage-eventinfo-form-block-seatmap"><td class="label"><label for="seatmap">Seat Map</label></td><td id="seatmap"></td></tr>'); 
      {/literal}
        cj('td#seatmap').html('<strong>{$existing_seatmap}</strong><br><span class="description">After initial assignment, seat map can only be changed by deleting and recreating the event.</span>');
      {literal}
    }
  });
  {/literal}
  </script>
{/if}

{if $ft_categories}
  {literal}
  <script>
  cj(document).ready(function() {
    if (cj('tr.crm-event-manage-eventinfo-form-block-seatmap').length == 0) {
      cj('tr.crm-event-manage-eventinfo-form-block-end_date').after('<tr class="crm-event-manage-eventinfo-form-block-seatmap"><td class="label"><label for="seatmap">Seat Map</label></td><td id="seatmap"></td></tr>'); 
      cj('td#seatmap').html('<select id="ft_category_id" name="ft_category_id">'+
      {/literal}
      {foreach from=$ft_categories item=ft_category}
        '<option value="{$ft_category.category_id}">{$ft_category.ort_name} - {$ft_category.pm_name} - {$ft_category.category_name}</option>' +
      {/foreach}
      '</select><br><span class="description">After initial assignment, seat map can only be changed by deleting and recreating the event.</span>');
    {literal}
    }
  });
  {/literal}
  </script>
{/if}
