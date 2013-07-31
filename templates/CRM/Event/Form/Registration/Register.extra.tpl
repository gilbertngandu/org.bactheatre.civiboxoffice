{if $seatMap}
  <div id="seatmapdefault" style="display: none;">
    {$seatMap} 
  </div>
{/if}
{if $add_subscription_section}
  <div id="subscription-section-staging-area" style="display: none;">
    <div id="subscription-section" class="crm-section subscription-section">
      <fieldset>
	<legend>Flex Pass</legend>
	<div class="label">
	  <label for="subscription_email_address">Email Address</label>
	</div>
	<div class="content">
	  <input type="text" id="subscription_email_address" name="subscription_email_address">
	  <input type="hidden" id="subscription_participant_id" name="subscription_participant_id">
	</div>
	<div id="subscription-choice-area" class="content">
	</div>
	<div id="subscription-message-area" style="height: 2em;">
	  <div id="subscription-messages" class="content">
	  </div>
	</div>
      </div>
    </fieldset>
  </div>
  <script type="text/javascript">
    var event_id = {$event_id};
  </script>
{/if}
