(function()
{
  $ = this.cj;

  function setup()
  {
    $('#civiboxoffice-staging-area tbody').children().insertBefore('.crm-event-manage-eventinfo-form-block-max_participants');
    $('#allowed-subscription-area').show();
    $('#civiboxoffice-staging-area').remove();
  }
  $(document).on('form-loaded', setup);
}
).call(this);
