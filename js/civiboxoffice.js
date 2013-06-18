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

cj(document).ready(function() {
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
