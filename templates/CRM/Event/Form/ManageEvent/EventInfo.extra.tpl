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

{if $subscription_events}
  <table id="civiboxoffice-staging-area" style="display: none;">
    <tbody>
      <tr id="subscription-max-uses-area">
	<td class="label">
	  <label for="subscription_max_uses">
	    Subscription Max Uses
	  </label>
	</td>
	<td>
	  <input type="text" name="subscription_max_uses" id="subscription_max_uses" value="{$subscription_max_uses}">
	</td>
      </tr> 
      <tr id="allowed-subscription-area">
	<td class="label">
	  <label for="allowed_subscription_ids">
	    Allowed Subscriptions
	  </label>
	</td>
	<td>
	  <select id="allowed_subscription_ids" name="allowed_subscription_ids[]" multiple="true">
	    {foreach from=$subscription_events item=subscription_event}
	    <option value="{$subscription_event->id}" {if $subscription_event->civiboxoffice_allowed} selected="true"{/if}>
		{$subscription_event->title}
	      </option>
	    {/foreach}
	  </select>
	</td>
      </tr>
    </tbody>
  </table>
  <script>
    cj(document).trigger('form-loaded');
  </script>
{/if}
