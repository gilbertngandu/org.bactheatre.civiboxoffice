(function()
{
  $ = this.cj;

  function setup()
  {
    $('#allowed-subscription-area').insertBefore('.crm-event-manage-eventinfo-form-block-max_participants');
    $('#allowed-subscription-area').show();
    $('#civiboxoffice-staging-area').remove();
  }
  $(document).on('form-loaded', setup);
}
).call(this);
