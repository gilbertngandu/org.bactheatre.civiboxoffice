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
  {literal}
  <script>
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
	          if ( textval ) {
	            eval( "var option = "+ cj(this).attr("price") );
	            ele         = option[0];
	            optionPart = option[1].split(optionSep);
	            addticket   = parseFloat( optionPart[1] );
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

	  
	  cj(document).ready(function() {
	    if (cj('div#seatmap').length == 0) {
	      cj('fieldset#send_confirmation_receipt').before('<div id="seatmap"></div>');
	    }
	    cj('div#seatmap').html(cj('div#seatmapdefault').html());
	    cj('div#seatmapdefault').html('');
	    cj("form[name=Participant]").submit(function() {
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

  </script>
  {/literal}
{/if}
