(function()
{
  $ = this.cj;

  function select_default_values_in_dropdowns() {

  }

  function setup()
  {
    $('#civiboxoffice-staging-area tbody').children().insertAfter('#map-field');
    $('#allowed-subscription-area').show();
    $('#civiboxoffice-staging-area').remove();
  }
  $(document).on('form-loaded', setup);
}
).call(this);
