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

function subscription_add_error(message) {
  var message_html = "<div class=\"red\">\n" + message + "\n</div>\n";
  cj('#subscription-messages').append(message_html);
}

function subscription_add_message(message) {
  var message_html = "<div>\n" + message + "\n</div>\n";
  cj('#subscription-messages').append(message_html);
}

function subscription_clear_error_messages() {
  cj('#subscription-messages').html('');
}

function subscription_ajax_error(jq_xhr, error_type, exception) {
  subscription_add_error('There was an error requesting subscription information: ' + exception);
}

function subscription_update_fields(data, text_status, jq_xhr) {
  if (data == null) {
    subscription_add_error("There was an error looking up your subscription information.");
    return;
  }
  if (data['error']) {
    subscription_add_error(data['error_message']);
  } else {
    var quantity_for_price_field_ids = data['quantity_for_price_field_ids'];
    cj.each(quantity_for_price_field_ids, function(key, value) {
      $('#price_' + key).val(value);
    });
    price_fields = cj('#priceset input');
    price_fields.prop('readonly', true);
    price_fields.css('background-color', 'rgb(235, 235, 228)');
    subscription_add_message("The ticket quantities below have been updated according to your Flex Pass. Please select your seats below.");
  }
}

function setup_subscription() {
  if (cj("#subscription-section-staging-area").length == 0) {
    return;
  }
  cj('#subscription-section').insertBefore('#priceset');
  cj('#subscription_email_address').on('blur', function() {
    if ($(this).val().trim() == '') {
      return;
    }
    subscription_clear_error_messages();
    data = {
      'allowed_event_id': event_id,
      'subscription_email_address': $(this).val(),
    };
    options = {
      'data': data,
      'dataType': 'json',
      'error': subscription_ajax_error,
      'success': subscription_update_fields,
      'type': 'GET',
      'url': '/civicrm/civiboxoffice/subscription_lookup',
    };
    cj.ajax(options);
  });
}

cj(document).ready(function() {
  setup_subscription();
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
