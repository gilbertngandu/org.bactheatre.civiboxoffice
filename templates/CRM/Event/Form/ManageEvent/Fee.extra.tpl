jliu123
  <table id="civiboxoffice-staging-area" style="display: none;">
    <tbody>

      <tr class="price_set_associations_section">
        <tr>
	  <td class="label">
	    <label for="price_set_assoc">Price Set Associations</label>
	  </td>

          <td>

            {foreach from=$subscription_price_fields item=subscription_price_field}
            <tr class="price_set_assoc_row">
              <td>
                <label> {$subscription_price_field->label} - {$subscription_price_field->subscription_title} </label>
              </td>

              <td>
	        <select id="price_set_assoc_for_subscription_price_field_id_{$subscription_price_field->price_field_id}" name="price_set_assoc_for_subscription_price_field_id_{$subscription_price_field->price_field_id}">
	          <option value="">- None -</option>

                  {foreach from=$allowed_event_price_fields item=allowed_event_price_field}
		  <option value="{$allowed_event_price_field->id}">{$allowed_event_price_field->label}  - {$allowed_event_price_field->title}</option>
	          {/foreach}
	        </select>
              </td>
            </tr>
            {/foreach}

          </td>
        </tr>
      </tr>

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

    </tbody>
  </table>
  <script>
    cj(document).trigger('form-loaded');

  </script>
