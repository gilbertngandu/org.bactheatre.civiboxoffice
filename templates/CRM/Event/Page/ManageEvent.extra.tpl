<li style="display: none;" class="seat-assignment-link-template">
  <a title="Seat Assignments" class="action-item-wrap" href="{crmURL p='civicrm/civiboxoffice/seat_assignments?event_id}">
    Seat Assignments
  </a>
</li>
{literal}
<script type="text/javascript">
  var rows = $('#event_status_id tr div.crm-event-participants ul.panel');
  var id_matcher = /.*_(\d+)$/;
  var link_template = $('.seat-assignment-link-template');

  rows.each(function()
  {
    var match = id_matcher.exec(this.id);
    var event_id = match[1];
    var new_item = link_template.clone();
    new_item.removeClass('seat-assignment-link-template');
    new_item_link = new_item.children('a');
    new_item_link.attr('href', new_item_link.attr('href') + '=' + event_id);
    new_item.show();
    $(this).append(new_item);
  });
</script>
{/literal}
