<table id="price_set_associations_section" style="display: none;" class="form-layout">
  <tbody>
    <tr>
      <td class="label">
        <label for="price_set_assoc">Price Set Associations</label>
      </td>
      <td>
        {foreach from=$allowed_subscriptions item=allowed_subscription}
          {$allowed_subscription->subscription_event->title}
          <table>
            {foreach from=$allowed_subscription->price_set_associations key=i item=price_set_association}
              <tr>
                <td>
                  {$price_set_association->subscription_price_field->label}
                </td>
                <td id="price-set-associations">
                  <input type="hidden" name="price_set_association[{$allowed_subscription->id}][{$i}][subscription_price_field_id]" value="{$price_set_association->subscription_price_field_id}">
                  <select id="price_set_assoc_for_subscription_price_field_id_{$subscription_price_field->price_field_id}" name="price_set_association[{$allowed_subscription->id}][{$i}][allowed_event_price_field_id]" class="price_set_associations_select" data-price-set-association-label="{$price_set_association->subscription_price_field->label}">
                    <option value="">- None -</option>
                    {foreach from=$allowed_event_price_fields item=allowed_event_price_field}
                      <option value="{$allowed_event_price_field->id}" {if $allowed_event_price_field->id == $price_set_association->allowed_event_price_field_id}selected{/if}>
                        {$allowed_event_price_field->label}
                      </option>
                    {/foreach}
                  </select>
                </td>
              </tr>
            {/foreach}
          </table>
        {/foreach}
      </td>
    </tr>
  </tbody>
</table>
<script type="text/javascript" src="{$manage_event_fees_js_url}"></script>
