  <!-- Required Header .tpl Start -->
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  {strip}
  <meta name="description" content="{$organizer_name|clean}{if !empty($my_event_short_text)} - {$my_event_short_text|clean} {/if}" />

  <title>{$organizer_name|clean}{if !empty($my_event_name)} - {$my_event_name|clean}{/if}</title>
   {if !empty($my_event_keywords)}
    <META NAME="keywords" CONTENT="{$my_event_keywords|clean}">
   {/if}

 {/strip}
  {minify type='css'}

  {minify type='js' base='scripts/jquery'} {* Shows the default list *}
  {minify type='js' base='scripts/jquery' files='jquery.countdown.pack.js,jquery.maphilight.js,jquery.metadata.min.js'}

  <!--Start Image Mapping-->
<style type="text/css">
		.Buttonloading {
			background : url('images/theme/default/grid-loading.gif') no-repeat  1px 1px !important;
			padding-left: 20px !important;
		}
</style>
  <!--End Image Mapping-->

  <script type="text/javascript">
  	var lang = new Object();
  	lang.required = '{!mandatory!}';        lang.phone_long = '{!phone_long!}'; lang.phone_short = '{!phone_short!}';
  	lang.fax_long = '{!fax_long!}';         lang.fax_short = '{!fax_short!}';
  	lang.email_valid = '{!email_valid!}';   lang.email_match = '{!email_match!}';
  	lang.pass_short = '{!pass_too_short!}'; lang.pass_match = '{!pass_match!}';
  	lang.not_number = '{!not_number!}';     lang.condition ='{!check_condition!}';

    jQuery(document).ready(function(){
        $("*[class*='has-tooltip']").tooltip({
          delay:0,
          showBody: "~",
          showURL:false,
          track: true,
          opacity: 1,
          fixPNG: true,
          fade: 250
        });
      });

    var showDialog = function(element){
      jQuery.get(jQuery(element).attr('href'),
        function(data){
          jQuery("#showdialog").html(data);
          jQuery("#showdialog").modal({
            autoResize:true,
            maxHeight:500,
            maxWidth:800
          });
        }
      );
      return false;
    }

    function BasicPopup(a) {
      showDialog(a);
      return false;
    }
  </script>
  <!-- Required Header .tpl  end -->