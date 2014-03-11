(function()
{
  $ = this.cj;

  var PriceSetManager = function()
  {
    this.initialize();
  }

  PriceSetManager.prototype =
  {
    initialize: function()
    {
      console.log('hello');
      $('#price_set_id').bind('change', $.proxy(this.request_price_fields, this));
    },

    request_price_fields: function()
    {
      this.show_loading();
      data =
      {
        'price_set_id': $('#price_set_id').val(),
      };
      options =
      {
        'data': data,
        'dataType': 'json',
        'error': $.proxy(this.request_price_fields_error, this),
        'success': $.proxy(this.request_price_fields_success, this),
        'type': 'GET',
        'url': '/civicrm/civiboxoffice/price_fields',
      };
      $.ajax(options);
    },

    request_price_fields_error: function(jq_xhr, error_type, exception)
    {
      this.show_error("There was an error requesting price field information: " + jq_xhr + ", " + error_type + ", " + exception);
    },

    request_price_fields_success: function(data, text_status, jq_xhr)
    {
      if (data == null)
      {
        this.show_error("There was an error looking up price set information: " + data + ', ' + text_status + ", " + jq_xhr);
        return;
      }
      options_html = '<option value="">- None -</option>';
      $(data).each(function(i, price_field) {
        options_html += '<option value="' + price_field.id + '">' + price_field.label + "</option>\n";
      });
      selects = $('.price_set_associations_select');
      selects.html(options_html);
      selects.each(function(i, select) {
        select = $(select);
        label = select.data('price-set-association-label');
        select.children().filter(function(index) {
          return $(this).text() == label;
        }).attr('selected', true);
      });
    },

    show_error: function(message)
    {
       $('price_set_associations').html("<div style='color: red;'>" + message + "</div>");
    },

    show_loading: function()
    {
      $('price_set_associations').html('loading...');
    }
  };

  function setup()
  {
    $('#price_set_associations_section').insertAfter('#priceSet');
    $('#price_set_associations_section').show();
    new PriceSetManager();
  }
  setup();
}
).call(this);
