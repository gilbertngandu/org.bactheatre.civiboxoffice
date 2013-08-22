function pluralize(count, singular, plural)
{
  if (count == 1)
  {
    return singular;
  }
  else
  {
    return plural;
  }
}

function totalTickets() {
  var totalfee       = 0;
  var totaltickets   = 0;
  var thousandMarker = ",";
  var seperator      = ".";
  var symbol         = "$";
  var optionSep      = "|";
  cj("input,#priceset select,#priceset").each(function () {

    if ( cj(this).attr("price") ) {
      var eleType =  cj(this).attr("type");
      if ( this.tagName == "SELECT" ) {
	eleType = "select-one";
      }
      switch( eleType ) {

	case "checkbox":

	  //default calcution of element.
	  eval( "var option = " + cj(this).attr("price") ) ;
	  ele        = option[0];
	  optionPart = option[1].split(optionSep);
	  addticket   = parseFloat( optionPart[1] );

	  if( cj(this).attr("checked") ) {
	    totaltickets   += (isNaN(addticket) ? 0 : addticket);
	  }

	  break;

	case "radio":

	  //default calcution of element.
	  eval( "var option = " + cj(this).attr("price") );
	  ele        = option[0];
	  optionPart = option[1].split(optionSep);
	  addticket   = parseFloat( optionPart[1] );

	  if( cj(this).attr("checked") ) {
	    totaltickets   += (isNaN(addticket) ? 0 : addticket);
	  }
	  break;

	case "text":

	  //default calcution of element.
	  var textval = parseFloat( cj(this).val() );
	  if ( textval && !isNaN(textval) ) {
	    eval( "var option = "+ cj(this).attr("price") );
	    ele         = option[0];
	    optionPart = option[1].split(optionSep);
	    addticket   = parseFloat( optionPart[1] );
	    if (isNaN(addticket))
	    {
	      addticket = 1;
	    }
	    var curval  = textval * addticket;
	    if ( textval >= 0 ) {
	      totaltickets   += curval;
	    }
	  }
	  break;

	case "select-one":

	  //default calcution of element.
	  var ele = cj(this).attr("id");
	  eval( "var selectedText = " + cj(this).attr("price") );
	  var addticket = 0;
	  if ( cj(this).val( ) ) {
	    optionPart = selectedText[cj(this).val( )].split(optionSep);
	    addticket   = parseFloat( optionPart[1] );
	  }

	  if ( addticket ) {
	    totaltickets  += (isNaN(addticket) ? 0 : addticket);
	    break;
	  }
      }
    }
  });
  return totaltickets;
}

(function()
{
  $ = this.cj;

  var Subscription = function(data)
  {
    this.initialize(data);
  }

  Subscription.prototype =
  {
    find_price_field_data_by_price_field_id: function(id)
    {
      var found_price_field_data = null;
      var subscription = this;
      $(this.price_fields).each(function()
      {
	if ('price_' + this['id'] == id && this['subscription_participant_id'] == subscription.participant_id) 
	{
	  found_price_field_data = this;	  
	}
      });

      return found_price_field_data;
    },

    initialize: function(data)
    {
      this.event_title = data['event_title'];
      this.max_uses = data['max_uses'];
      this.participant_id = data['participant_id'];
      this.price_fields = data['price_fields'];
      this.line_items = data['line_items'];
      this.uses = data['uses'];
    },

    line_items_string: function()
    {
      var item_strings = [];
      $(this.line_items).each(function()
      {
	item_strings.push(this.label + ': ' + this.qty); 
      });
      return item_strings.join(', ');
    }
  };

  var SubscriptionManager = this.SubscriptionManager = function()
  {
    this.initialize();
    this.lookup_subscription();
  };

  SubscriptionManager.prototype =
  {
    add_error: function(message)
    {
      var message_html = "<div class=\"red\">\n" + message + "\n</div>\n";
      this.message_area.append(message_html);
    },

    add_message: function(message)
    {
      var message_html = "<div>\n" + message + "\n</div>\n";
      this.message_area.append(message_html);
    },

    clear: function()
    {
      this.clear_messages();
      $('#seatmap').html('<h2>Loading seatmap. Please wait...</h2>');
      $('#subscription-choice-area').html('');
      $('#subscription_participant_id').val('');
      $('#payment_information').show();
      $('.email-Primary-section').parent().show();
      this.clear_price_fields();
    },

    clear_messages: function()
    {
      this.message_area.html('');
    },

    clear_price_fields: function()
    {
      price_fields = $('#priceset input');
      price_fields.prop('disabled', false);
      price_fields.prop('readonly', false);
      price_fields.css('background-color', '');
      price_fields.filter('[type=text]').val('');
      price_fields.parents('.crm-section').show();
      price_fields.each(function(index)
      {
	var price_field = $(this);
	label_field = $('label[for="' + price_field.attr('id') + '"]');
	var orig_price_label = label_field.data('orig-price-label');
	if (orig_price_label != null)
	{
	  label_field.html(orig_price_label);
	}
      });
    },

    initialize: function()
    {
      if ($("#subscription-section-staging-area").length == 0) {
	return;
      }
      this.message_area = $('#subscription-messages');
      $('#subscription-section').insertBefore('#priceset');
      this.subscription_choice_area = $('#subscription-choice-area');
      this.subscription_email_address = $('#subscription_email_address');
      this.original_subscription_email_address = this.subscription_email_address.val().trim();
      this.subscription_email_address.on('blur', $.proxy(this.lookup_subscription, this));
      this.subscription_participant_id = $('#subscription_participant_id');
    },

    lookup_error: function(jq_xhr, error_type, exception)
    {
      this.add_error('There was an error requesting subscription information: ' + exception);
    },

    lookup_subscription: function()
    {
      if (this.subscription_email_address.val().trim() != this.original_subscription_email_address) {
	this.clear();
      }
      if (this.subscription_email_address.val().trim() == '') {
	this.request_seatmap('general_admission');
	return;
      }
      this.original_subscription_email_address = this.subscription_email_address.val().trim();
      data = {
	'allowed_event_id': event_id,
	'qfKey': $('input[name="qfKey"]').val(),
	'subscription_email_address': this.subscription_email_address.val(),
      };
      options = {
	'data': data,
	'dataType': 'json',
	'error': $.proxy(this.lookup_error, this),
	'success': $.proxy(this.lookup_success, this),
	'type': 'GET',
	'url': '/civicrm/civiboxoffice/subscription_lookup',
      };
      $.ajax(options);
    },

    lookup_success: function(data, text_status, jq_xhr)
    {
      if (data == null) {
	this.add_error("There was an error looking up your subscription information.");
	return;
      }
      if (data['error']) {
	this.add_error(data['error_message']);
	this.request_seatmap('general_admission');
      } else {
	this.update_subscriptions_data(data);
      }
    },

    present_choices: function()
    {
      this.subscription_choice_area.html('');
      this.subscription_choice_area.html('<label for="subscription-selector">Congratulations! We have found your Flex Passes. Please select the one you wish to use.</label><select id="subscription-selector">\n</select>');
      subscription_selector = $('#subscription-selector');
      subscription_selector.append('<option value=""> - I don\'t want to use a subscription. - </option>');
      $(this.subscriptions).each(function(index)
      {
	var option_html;
	var subscription_info;

	subscription_info = this.event_title + ' (' + this.line_items_string() + ') - Used ' + this.uses + '/' + this.max_uses + ' times.';
	if (this.uses >= this.max_uses) 
	{
	  option_html = '<option disabled="true">' + subscription_info + ' Used up.</option>';
	}
	else
	{
	  option_html = '<option id="subscription_' + index + '" value="' + index + '">' + subscription_info + '</option>';
	}
	subscription_selector.append(option_html);
      });
      subscription_selector.on('change', $.proxy(this.subscription_selected, this));
    },

    set_data: function(subscriptions_data)
    {
      this.subscriptions = [];
      for (var i = 0; i < subscriptions_data.length; ++i)
      {
	this.subscriptions.push(new Subscription(subscriptions_data[i]));
      }
    },

    set_fields: function()
    {
      var subscription = this.current_subscription;
      var price_fields = $('#priceset input');
      var price_regex = /^(.*)(\$.*)$/;
      price_fields.each(function()
      {
	var price_field = $(this);
	var price_field_data = subscription.find_price_field_data_by_price_field_id(price_field.attr('id'));
	if (price_field_data == null)
	{
	  if (price_field.attr('type') == 'radio')
	  {
	    if (price_field.data('amount') == '0')
	    {
	      price_field.prop('checked', true);
	    }
          }
          else
          {
	    price_field.val(null);
	    price_field.prop('disabled', true);
          }
          price_field.parentsUntil('#priceset', '.crm-section').hide();
	}
	else
	{
	  if (price_field_data['quantity'] == null)
	  {
	    price_field.val(0);
	  }
	  else
	  {
	    price_field.val(price_field_data['quantity']);
	  }
	  label_field = $('label[for="' + price_field.attr('id') + '"]');
	  label_field.data('orig-price-label', label_field.html());
	  label_field.html(label_field.html().replace(/-.*\$.*$/, ''));
	  price_field.prop('readonly', true);
	  price_field.css('background-color', 'rgb(235, 235, 228)');
	  eval( 'var option = ' + price_field.attr('price') );
	  element_index = option[0];
	  price[element_index] = 0;
	  price_field.attr('price', '[' + element_index + ',"0||"]');
	}
      });
      totalfee = 0;
      display(totalfee);
      $('#payment_information').hide();
      $('.email-Primary-section').parent().hide();
      $('#email-Primary').val(this.subscription_email_address.val());
      this.subscription_participant_id.val(subscription.participant_id);
      uses_remaining = subscription.max_uses - subscription.uses;
      this.add_message("Congratulations! We have found your Flex Pass. Please select your seats below. You have " + uses_remaining + " " + pluralize(uses_remaining, 'show', 'shows') + ' left on your flex pass.');
    },

    request_seatmap: function(category_type)
    {
      data = {
	'allowed_event_id': event_id,
	'category_type': category_type,
	'qfKey': $('input[name="qfKey"]').val(),
      };
      options = {
	'data': data,
	'dataType': 'json',
	'error': $.proxy(this.lookup_error, this),
	'success': $.proxy(this.request_seatmap_success, this),
	'type': 'GET',
	'url': '/civicrm/civiboxoffice/seat_map',
      };
      $.ajax(options);
    },

    request_seatmap_success: function(data, text_status, jq_xhr)
    {
      if (data == null) {
	this.add_error("There was an error looking up your subscription information.");
	return;
      }
      if (data['error']) {
	this.add_error(data['error_message']);
      } else {
	$('#seatmap').html(data['seatmap']);
      }
    },

    subscription_selected: function(event)
    {
      this.clear_messages();
      this.clear_price_fields();
      var subscription_selector = $(event.target);
      var index = subscription_selector.val();
      if (index == '')
      {
	this.request_seatmap('general_admission');
	this.current_subscription = null;
	this.clear();
      }
      else
      {
	this.request_seatmap('subscription');
	this.current_subscription = this.subscriptions[index];
	this.set_fields();
      }
    },

    update_subscriptions_data: function(data)
    {
      $('#seatmap').html(data['seatmap']);
      this.set_data(data['subscriptions']);
      if (this.subscriptions.length == 1)
      {
	this.current_subscription = this.subscriptions[0];
	if (this.current_subscription.uses > this.current_subscription.max_uses)
	{
	  this.add_error("You have already used your Flex Pass " + this.current_subscription.uses + " out of " + this.current_subscription.max_uses + " times so you cannot use it any more.");
	}
	else
	{
	  this.set_fields();
	}
      }
      else
      {
	this.present_choices();
      }
    }
  };
}).call(this);

cj(document).ready(function() {
  new SubscriptionManager();
  if (cj('#seatmapdefault').length == 0) {
    return;
  }
  if (cj('div#seatmap').length == 0) {
    cj('fieldset#priceset').after('<div id="seatmap"></div>');
  }
  cj('div#seatmap').html(cj('div#seatmapdefault').html());
  cj('div#seatmapdefault').html('');
  cj("form[name=Register]").submit(function() {
    tickets = totalTickets();
    seats = cj("#selectedseats").val();
    if (tickets != seats) {
      alert("Ticket quantity (" + tickets + ") does not match number of selected seats (" + seats + ").");
      return false;
    }
    else {
      return true;
    }
  });
});
