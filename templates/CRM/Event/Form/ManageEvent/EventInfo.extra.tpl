{if $show_civiboxoffice_staging_area}
  <table id="civiboxoffice-staging-area" style="display: none;">
    <tbody>
      <tr class="crm-event-manage-eventinfo-form-block-seatmap">
	<td class="label">
	  <label for="seatmap">General Admission Seat Map</label>
	</td>
	<td>
	  {if $general_admission_seat_map}
	    <strong>{$general_admission_seat_map}</strong>
	  {else}
	    <select id="general_admission_category_id" name="general_admission_category_id">
	      {foreach from=$ft_categories item=ft_category}
		<option value="{$ft_category.category_id}">{$ft_category.ort_name} - {$ft_category.pm_name} - {$ft_category.category_name}</option>
	      {/foreach} 
	    </select>
	  {/if}
	  <div class="description">
	    After initial assignment, seat map can only be changed by deleting
	    and recreating the event.
	  </div>
	</td>
      </tr>
      <tr class="crm-event-manage-eventinfo-form-block-seatmap">
	<td class="label">
	  <label for="seatmap">Flex Pass Seat Map</label>
	</td>
	<td>
	  {if $subscription_seat_map}
	    <strong>{$subscription_seat_map}</strong>
	  {else}
	    <select id="subscription_category_id" name="subscription_category_id">
	      {foreach from=$ft_categories item=ft_category}
		<option value="{$ft_category.category_id}">{$ft_category.ort_name} - {$ft_category.pm_name} - {$ft_category.category_name}</option>
	      {/foreach} 
	    </select>
	  {/if}
	  <div class="description">
	    After initial assignment, seat map can only be changed by deleting
	    and recreating the event.
	  </div>
	</td>
      </tr>
      {if $subscription_events}
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
      {/if}
    </tbody>
  </table>
  <script>
    $('#civiboxoffice-staging-area tbody').children().insertBefore('.crm-event-manage-eventinfo-form-block-max_participants');
    $('#allowed-subscription-area').show();
    $('#civiboxoffice-staging-area').remove();
  </script>
{/if}
