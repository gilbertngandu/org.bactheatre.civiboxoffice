<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2010
 */

/*
To extend the admin menu you need to add atliest 2 functions:
   function do[adminviewname]_items($items)  // to add new tabs at the
     parameter: $items is the list of tab's to be extends with new tabs.
     return: the new array;
   the value $this->plugin_acl is a counter that can be used to identify
   the right tab is selected. When you need more tabs need in the same admin page
   you need to add a value to the above property
     like:
       function do[adminviewname]_Items($items){
         $items[$this->plugin_acl+00] ='yournewtab|admin'; // <== admin is for usermanagement
         $items[$this->plugin_acl+01] ='yournewtab2|admin'; // <== admin is for usermanagement
         return $items;
       }
   function do[adminviewname]_Draw($id, $view) // will be used to handle
    parameter: $id is the value used above with ($this->plugin_acl+00)
    parameter: $view is the $view object so you can use it within the plugin.
    return: none;


*/

class plugin_AdvancedSearch extends baseplugin {

	public $plugin_info		  = 'Advanced Search/Edit plugin';
	/**
	 * description - A full description of your plugin.
	 */
	public $plugin_description	= 'This plugin extends the Search pages';
	/**
	 * version - Your plugin's version string. Required value.
	 */
	public $plugin_myversion		= '0.0.4';
	/**
	 * requires - An array of key/value pairs of basename/version plugin dependencies.
	 * Prefixing a version with '<' will allow your plugin to specify a maximum version (non-inclusive) for a dependency.
	 */
	public $plugin_requires	= null;
	/**
	 * author - Your name, or an array of names.
	 */
	public $plugin_author		= 'Lxsparks';
	/**
	 * contact - An email address where you can be contacted.
	 */
	public $plugin_email		= 'lxsparks@hotmail.com';
	/**
	 * url - A web address for your plugin.
	 */
	public $plugin_url			= 'http://www.fusionticket.org';

  public $plugin_actions  = array ('config','install','uninstall','priority','enable','protect');
  protected $directpdf = null;


  function getTables(& $tbls){
      /*
         Use this section to add new database tables/fields needed for this plug-in.
      */
   }


 	function showFormOptions($array, $active, $echo=true){
        $string = '';

        foreach($array as $k => $v){
            $s = ($active == $k)? ' selected="selected"' : '';
            $string .= '<option value="'.$k.'"'.$s.'>'.$v.'</option>'."\n";
        }

        if($echo)   echo $string;
        else        return $string;
    }

	function print_status ($user_status){  //An extended version of the function in view.search
		if($user_status=='1'){
		  return con('sale_point');
		}else if ($user_status=='2'){
		  return con('member');
		}else if($user_status=='3'){
		  return con('guest');
		}else if($user_status=='4'){
		  return con('pos');
		}
	}

	function page_links ($currentpage, $range, $totalpages, $limit){
	/******  build the pagination links ******/

		$pageLinks='';
						// range of num links to show
						$range = 3;

						// if not on page 1, don't show back links
						if ($currentpage > 1) {
						   // show << link to go back to page 1
						   $pageLinks= " <a href='{$_SERVER['PHP_SELF']}?currentpage=1&submit=1&npp={$limit}'><<</a> ";
						   // get previous page num
						   $prevpage = $currentpage - 1;
						   // show < link to go back to 1 page
						   $pageLinks.= " <a href='{$_SERVER['PHP_SELF']}?currentpage=$prevpage&submit=1&npp={$limit}'><</a> ";
						} // end if

						// loop to show links to range of pages around current page
						for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
						   // if it's a valid page number...
						   if (($x > 0) && ($x <= $totalpages)) {
							  // if we're on current page...
							  if ($x == $currentpage) {
								 // 'highlight' it but don't make a link
								 $pageLinks.=  " [<b>$x</b>] ";
							  // if not current page...
							  } else {
								 // make it a link
								 $pageLinks.=  " <a href='{$_SERVER['PHP_SELF']}?currentpage=$x&submit=1&npp={$limit}'>$x</a> ";
							  } // end else
						   } // end if
						} // end for

						// if not on last page, show forward and last page links
						if ($currentpage != $totalpages) {
						   // get next page
						   $nextpage = $currentpage + 1;
							// echo forward link for next page
						   $pageLinks.=  " <a href='{$_SERVER['PHP_SELF']}?currentpage=$nextpage&submit=1&npp={$limit}'>></a> ";
						   // echo forward link for lastpage
						   $pageLinks.=  " <a href='{$_SERVER['PHP_SELF']}?currentpage=$totalpages&submit=1&npp={$limit}'>>></a> ";
						} // end if


				/****** end build pagination links ******/
		return($pageLinks);
		}

	function search_pattern ($str, $type, $length) {

		$result = "";
		/**
		* Sanitize data, comming from the forms.
		*
		* @param $data mixed Anykind of data, posted from a particualr form field.
		* @param $type string data type name - "string", "integer", etc...
		* @param $allowed_tags string List of allowed HTML tags, if any. Example: "<p><b><font>"
		* function sanitize($data, $type, $allowed_tags)
		*/

		if($type == "string"){
		$result = (string) trim(strip_tags($str));
		}elseif($type == "int"){
		$result = (int) trim(strip_tags($str));
		}


		$result = preg_replace('/[^A-Za-z0-9\-\?]/', '', $result); // Ensure we only have letters or hyphens and our question mark/s
		$result = substr($result,0,$length); // Make sure it is ($length) characters or less

		$result = str_replace('?', '%', $result);
		echo "<br />The value is: ".$result."<br />";

		return ($result);
	}

	function clean_customerData($str, $len) { // Clean up the data to match specific patterns

		$str = (strlen($str > $len)) ?  substr($str, 0, $len) : $str;
		if (stristr($str, '-') === FALSE) { // Is the name hypenated? No >

		$str = trim(ucwords(strtolower($str)));

		} else { $str=explode('-', $str);  // Is the name hypenated? Yes >

			foreach ($str as $key=>$data) {

				$data = trim(ucwords(strtolower($data)));
				$str2[$key] = $data;
				}
			$str = implode("-", $str2);
			$ucList=array('On', 'In', 'Under','Upon','Cum'); // Place connector names that need to be back to lowercase
			$lcList=array('on', 'in', 'under','upon','cum');
			$str=str_replace($ucList, $lcList, $str);
		}

		$str=preg_replace ('/\s\s+/', ' ', $str); // Reduce extra whitespaces

		// Not perfect - Macintosh for example will be MacIntosh...
		$str=preg_replace(
			'/
			(?: ^ | \\b )         # assertion: beginning of string or a word boundary
			( O\' | Ma?c | M?c |Fitz)  # attempt to match Irish surnames
			( [^\W\d_] )          # match next char; we exclude digits and _ from \w
			/xe',
			"'\$1' . strtoupper('\$2')",
			$str);
			return ($str);
		}

	function clean_customerZip($str, $ctry) { // Clean up Postcodes to match specific (Country?) patterns

			$str = trim(strtoupper($str));
			$str=preg_replace ('/\s+/', '', $str); // Remove all whitespaces

			if ($ctry == 'GB') {  // UK Post Code

				$s1 = substr($str, 0, -3);
				$s2 = substr($str, -3);
				$str = $s1.' '.$s2;

			} elseif ($ctry == 'CA' ) {  // Canadia Post code

				$s1 = substr($str, 0, 3);
				$s2 = substr($str, 3, 3);
				$str = $s1.' '.$s2;

			} elseif ($ctry == 'US' || is_numeric($str)) {  // US Post code

				$str = substr($str, 0, 5);

			} else {  // Default setting

				$str = $str;
			}

			return ($str);
		}

	function clean_customerTel($str, $ctry) { // Clean up Postcodes to match specific (Country?) patterns

			$str = trim($str);
			$str=preg_replace ('/\s+/', '', $str); // Remove all whitespaces

			if ($ctry == 'GB') {  // UK Phone Numbers

				$std3 = array('028','029','023','024'); //3 digit STD
				$std4 = array('0117','0113','0114','0115','0116'); // 4 digit STD
				$stdLon = array('0207','0208'); // London STD
				$i = 0;


				if ($i == 0) {
					foreach ($std3 as $std) {

						if (strpos($str, $std) === 0) {

						$s1 = $std;
						$s2 = substr($str, 3, 4);
						$s3 = substr($str, 7, 4);
						$str = $s1.' '.$s2.' '.$s3;
						$i =1;
						}
					}

				}

				if ($i == 0) {
					foreach ($std4 as $std) {

						if (strpos($str, $std) === 0) {

						$s1 = $std;
						$s2 = substr($str, 4, 3);
						$s3 = substr($str, 7, 4);
						$str = $s1.' '.$s2.' '.$s3;
						$i = 1;
						}
					}

				}

				if ($i == 0) {
				foreach ($stdLon as $std) {

						if (strpos($str, $std) === 0) {

						$s1 = substr($std, 0, 3);
						$s2 = substr($str, 3, 4);
						$s3 = substr($str, 7, 4);
						$str = $s1.' '.$s2.' '.$s3;
						$i = 1;
						}
					}

				}

				if ($i == 0) {  // if number doesn't match any of the above: default UK format

					$s1 = substr($str, 0, 5);
					$s2 = substr($str, 5, 6);
					$str = $s1.' '.$s2;
				}

			} elseif ($ctry == 'CA'  || 'US') { // Format: [1] xxx xxx xxxx

				$num_len = strlen($str);
				$s1 ='';
				if ($num_len == 11 && $str[0] == '1') {  // Is it 11 numbers starting with 1?
				$s1 = '1 ';
				$str = preg_replace('/^1/', '', $str);
				}
				$s2 = substr($str, 0, 3);
				$s3 = substr($str, 3, 3);
				$s4 = substr($str, 6, 4);
				$str = $s1.$s2.' '.$s3.' '.$s4;

			} else { // Default format

				//  *************************** ???  ****************************
			}

			return ($str);
		}

	function clean_customerManual($str, $len) { //Sanitise data added manually
		print_r($str);
		//$str = trim($str);
		$str = preg_replace ('/\s\s+/', ' ', $str); // Reduce extra whitespaces inside
		//$str = (strlen($str > $len)) ?  substr($str, 0, $len) : $str; // Truncate anything longer then it should be
		/*$str = preg_replace('/<.[^<>]*?>/', '', $str);*/
		/*$str = preg_replace('/&nbsp;|&#160;/', '', $str);*/

		return ($str);

	}

  	function bac_searchForm (&$data, $view) { //Added by Lxsparks 06/11
	global $_SHOP;
	$results_table = '';
	$basicOptions=array('0'=>'N/A', '1'=>'Yes', '2'=>'No');
	$alphaStart=array('A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','X'=>'X','Y'=>'Y');
	$alphaEnd=array('B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','X'=>'X','Y'=>'Y','Z'=>'Z');
	unset($_SESSION['results']); //Ensure nothing is carried over from previous searches


	//**** Main Search Options Form ****\\
	echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>
		<input type='hidden' name='action' value='global_search'/>\n";  //Form value = global_search

	//**** Customer Acitivity/Status Search ****\\
    $view->form_head(con('global_search_title_activity'));
	echo "<tr><td colspan='2'>".con('global_search_notes_activity')."</td></tr>";
	echo "<tr><td class='admin_name'>".con('user_active')."</td><td class='admin_value'>
		<select name='user_active'>
		    <option value='0'>".con('choose_na')."</option>
            <option value='1'>".con('yes')."</option>
		    <option value='2'>".con('no')."</option>
		</select></td></tr>";
	echo "<tr><td class='admin_name'>".con('user_ticket_status')."</td><td class='admin_value'>
		<select name='user_ticket_status'>
			<option value='0'>".con('choose_na')."</option>
			<option value='1'>".con('yes')."</option>
			<option value='2'>".con('no')."</option>
		</select></td></tr>";
	//get class into the page
	require_once('../includes/plugins/advancedsearch/calendar/classes/tc_calendar.php');
	echo "<script language='javascript' src='../includes/plugins/advancedsearch/calendar/calendar.js'></script>";
	echo "<link href='../includes/plugins/advancedsearch/calendar/calendar.css' rel='stylesheet' type='text/css'>";

	//instantiate class and set properties
	$date_now=date('Y-m-d');
	$myCalendar = new tc_calendar("date1", true);
	$myCalendar->setIcon("../includes/plugins/advancedsearch/calendar/images/iconCalendar.gif");
	//$myCalendar->setDate(01, 03, 2001);
	$myCalendar->setPath("../includes/plugins/advancedsearch/calendar/");
	$myCalendar->setYearInterval(2000, 2015);
	$myCalendar->dateAllow('', $date_now);
	//$myCalendar->setOnChange("myChanged('test')");
	//output the calendar

	echo "<tr><td class='admin_name'>".con('user_login_last')."</td><td class='admin_value'>";
	$myCalendar->writeScript();

	echo "</td></tr>";
	echo "<tr class='admin_value'><td>&nbsp;</td><td class='admin_value' align='right' colspan='1'>
		<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_activity' type='submit'>".con(btn_search)."</button>
		<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
		</td></tr>";
	echo "</table>";

	//**** Customer Lastname Search ****\\
	echo "<table class='admin_form' width='600' cellspacing='1' cellpadding='4'"; //Start of Surname word search
	echo "<thead><tr><td class='admin_list_title' colspan='2'>".con('global_search_title_surname')."</td></tr></thead>";
	echo "<tr><td colspan='2'>".con('global_search_notes_surname')."</td></tr>";
	echo "<tr><td class='admin_name'>".con('user_surname')."</td>
		<td class='admin_value'><input type='text' name='searchLastName' size='25' /></td>";
    echo "</td></tr>";
	echo "<tr class='admin_value'><td>&nbsp;</td><td class='admin_value' align='right' colspan='1'>
		<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_surname' type='submit'>".con(btn_search)."</button>
		<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
		</td></tr>";
	echo "</table>";

	//**** Customer Lastname A-Z Search ****\\
	echo "<table class='admin_form' width='600' cellspacing='1' cellpadding='4'"; //Start of Surname alphabet search
	echo "<thead><tr><td class='admin_list_title' colspan='2'>".con('global_search_title_AZ')."</td></tr></thead>";
	echo "<tr><td colspan='2'>".con('global_search_notes_az')."</td></tr>";
	echo "<tr><td class='admin_name'>".con('user_surname_start')."</td><td class='admin_value'>
			<select name='startLetter'>
			<option value='0'>Choose a letter</option>";
	$this->showFormOptions($alphaStart);
	echo "</select></td></tr>";
	echo "<tr><td class='admin_name'>".con('user_surname_end')."</td><td class='admin_value'>
			<select name='endLetter'>
			<option value='0'>Choose a letter</option>";
	$this->showFormOptions($alphaEnd);
	echo "</select></td></tr>";

	echo "<tr class='admin_value'><td>&nbsp;</td><td class='admin_value' align='right' colspan='1'>
		<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_surnameAZ' type='submit'>".con('btn_search')."</button>
		<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con('btn_reset')."</button>
		</td></tr>";
	echo "</table>";

	echo "</form>"; // End of Main Form




	//**** Duplicate POS and User Customers Search Form ****\\
	echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>
            <input type='hidden' name='action' value='global_search'/>\n";
   $view->form_head(con('search_title_global_posmerge'));
   //$view->form_head("Head");
  // echo "<table>";
	//echo "<tr><td colspan='2'>".con('search_notes_global_posMerge')."</td></tr>";
	echo "<tr><td class='admin_name'>".con('posmerge_email')."</td><td class='admin_value'>
	<input type='checkbox' value='selected' name='posmerge_email'></td></tr>";
	echo "</td></tr>";
	echo "<tr class='admin_value'><td>&nbsp;</td><td class='admin_value' align='right' colspan='1'>
		<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_posUser' type='submit'>".con('btn_search')."</button>
		<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con('btn_reset')."</button>
		</td></tr>";
	echo "</table>";
	echo "</form>"; // End of Duplicates Form

	}

	function searchForm_R (&$data, $view) {  //Added by Lxsparks 06/11

		if ($_POST['submit']=='search_activity') {

					$results_table = '0';
					$count = 0;
					$query_title = array();

					if($data["user_active"]=="0"){
					  $query_title[]=con('global_customer__activated_both');
					  $count++;
					}
					if($data["user_active"]=="1"){
					  $query_type[]="auth.active IS NULL";
					  $query_title[]=con('global_customer__activated_yes');
					  $count++;
					}
					if($data["user_active"]=="2"){
					  $query_type[]="auth.active IS NOT NULL";
					  $query_title[]=con('global_customer__activated_no');
					  $count++;
					}
					if($data["user_ticket_status"]=="0"){
					  $query_title[]=con('global_customer__tickets_both');
					  $count++;
					}
					if($data["user_ticket_status"]=="1"){
					  $query_type[]="user_current_tickets >= 1";
					  $query_title[]=con('global_customer__tickets_yes');
					  $count++;
					}
					if($data["user_ticket_status"]=="2"){
					  $query_type[]="user_current_tickets < 1" ;
					  $query_title[]=con('global_customer__tickets_none');
					  $count++;
					}
					if ($count <1) {
					  return addWarning('search_choice_two_fields');
					}

					$login_date = isset($_REQUEST["date1"]) ? $_REQUEST["date1"] : "";

					$table_opt='city';

					if ($login_date<>"0000-00-00") {
						$query_type[]="user_lastlogin <='$login_date'";
						$query_title[]=con('global_customer_login_req').": ". $login_date;

						} else {
						$query_title[]=con('global_customer_login_notreq');
						}

			} else if ($_POST['submit']=='search_address') { // Search on Address based on a LIKE pattern

					$results_table = '0';

					$name=$data["searchAddress"];
					$name1=$data["searchAddress1"];



					if (isset($name)) {
					$name=str_replace(chr(173), "", $name);
					$name=$this->search_pattern ($name, 'string', 75);
						if (strlen($name)!='0'){$query_type[]="User.user_address LIKE '$name'";
							$table_opt='address';
							echo "<br /> NAME <br />";
						}
					}

					if (isset($name1)) {
					$name1=str_replace(chr(173), "", $name1);
					$name1=$this->search_pattern ($name1, 'string', 75);
						if (strlen($name1)!='0'){$query_type[]="User.user_address1 LIKE '$name1'";
							$table_opt2='address1';
							echo "<br /> NAME 1 <br />";
						}
					}


					$query_title[]=con('global_customer_address').": ". $name;



			} else if ($_POST['submit']=='search_city') { // Search on City based on a LIKE pattern

					$results_table = '0';

					$name = ($data["searchCity"]);
					$name=str_replace(chr(173), "", $name);
					$name=$this->search_pattern ($name, 'string', 50);

					if (isset($name)){$query_type[]="User.user_city LIKE '$name'";}
					$query_title[]=con('global_customer_city').": ". $name;
					$table_opt='city';

			} else if ($_POST['submit']=='search_zip') { // Search on Postcode based on a LIKE pattern

					$results_table = '0';

					$name = ($data["searchZip"]);
					$name=str_replace(chr(173), "", $name);
					$name=$this->search_pattern ($name, 'string', 10);

					if (isset($name)){$query_type[]="User.user_zip LIKE '$name'";}
					$query_title[]=con('global_customer_zip').": ". $name;
					$table_opt='zip';

			} else if ($_POST['submit']=='search_zipRange') { // Search on Postcode based on a LIKE pattern

					$results_table = '0';

					$name = ($data["startZip"]);
					$name=str_replace(chr(173), "", $name);
					$name=$this->search_pattern ($name, 'string', 10);

					$name1 = ($data["endZip"]);
					$name1=str_replace(chr(173), "", $name1);
					$name1=$this->search_pattern ($name1, 'string', 10);

					if (strlen($name) !='0'  && strlen($name1) !='0'){$query_type[]="User.user_zip BETWEEN '$name' AND '$name1'";}
					$query_title[]=con('global_customer_ziprange').": ". $name;
					$table_opt='zip';

			} else if ($_POST['submit']=='search_state') { // Search on State based on a LIKE pattern

					$results_table = '0';

					$name = ($data["searchState"]);
					$name=str_replace(chr(173), "", $name);
					$name=$this->search_pattern ($name, 'string', 50);

					if (isset($name)){$query_type[]="User.user_state LIKE '$name'";}
					$query_title[]=con('global_customer_state').": ". $name;
					$table_opt='state';

			} else if ($_POST['submit']=='search_surname') { // Search on the surname based on a LIKE pattern

				$results_table = '0';

				$name = ($data["searchLastName"]);

				$name=str_replace(chr(173), "", $name);

				$name=$this->search_pattern ($name, 'string', 50);

				if (isset($name)){$query_type[]="User.user_lastname LIKE '$name'";}
				$query_title[]=con('global_customer_surname').": ". $name;
				$table_opt='city';

				//$query_type[]="User.user_lastname REGEXP '$name'";



			} else if ($_POST['submit']=='search_surnameAZ') {  // Surnames between two letters

				$results_table = '0';

				$s1 = substr(($data["startLetter"]), 0, 1);
				$s2 = substr(($data["endLetter"]), 0 ,1);


				$s3 = strcmp($s1, $s2);
				if ($s3 !== -1) {
					return addWarning('search_AZ_aBiggerZ');
					}


				$S1 .='%';
				$S2 .='%';

				$query_type[]="User.user_lastname BETWEEN '$s1' and '$s2' OR User.user_lastname LIKE '$s2'";

				$query_title[]=con('global_customer_surname_between').": ". $s1." & ".$s2;


			} else if ($_POST['submit']=='search_posUser') {  // POS, Guest and Member duplicate checker

				$results_table = '1';
				$count = 0;
				if($data["posMerge_email"]=="selected"){
					$query_type[]="auth.active IS NULL";
					$query_title[]=con('global_customer__activated_both');
					$count++;
					}
				if ($count <1) {
					return addWarning('search_choice_one_fields');
					}

			} else if ($_POST['submit']=='search_ticketsBetween') { // Search on Tickets ordered between based on a LIKE pattern

					$results_table = '2';
					$start_date = isset($_REQUEST["date3"]) ? $_REQUEST["date3"] : "";
					$end_date = isset($_REQUEST["date4"]) ? $_REQUEST["date4"] : "";


					//$name = ($data["searchCity"]);
					//$name=str_replace(chr(173), "", $name);
					//$name=$this->search_pattern ($name, 'string', 50);

					//if (isset($name)){$query_type[]="User.user_city LIKE '$name'";}
					//$query_title[]=con('global_customer_city').": ". $name;
					//$table_opt='city';

					if ($start_date<>"0000-00-00"  && $end_date<>"0000-00-00" ) {
						$query_type[]="`ti`.order_date  BETWEEN '$start_date' AND '$end_date'";
						$query_title[]=con('global_customer_tickets_between').": ". $start_date." and ".$end_date;

						} else {
						$query_title[]=con('global_customer_login_notreq');
						}

					$table_opt='city';
			}

			if (isset($query_type)) {

				if ($results_table == '0') {  // Build using the general query
					$query="SELECT auth.active, User.*
						FROM User LEFT JOIN auth ON auth.user_id = User.user_id
						WHERE User.user_status !=1 AND ". implode("\n AND ",$query_type)." ORDER BY User.user_lastname ASC, User.user_firstname ASC, User.user_status ASC";

					} elseif ($results_table == '1'){  // Build using the POS/Member/Guest query

						$query="SELECT * FROM User WHERE user_email IN(SELECT user_email FROM User GROUP BY user_email HAVING count(user_email)>1) ORDER BY user_email, user_status ASC";

					} elseif ($results_table == '2') {  // Build using the tickets query

						$query="SELECT  `U`.*, `ti`.order_user_id, `ti`.order_date
							FROM `Order` as `ti`, `User` as `U`
							WHERE `ti`.order_user_id=`U`.user_id AND ". implode("\n AND ",$query_type)."
							GROUP BY `U`.user_id";


					//echo "<br />".$query."<br />";  //Check the query build

					}


			} else {  $query="SELECT auth.active, User.* FROM User LEFT JOIN auth ON auth.user_id = User.user_id WHERE User.user_status !=1 ORDER BY User.user_lastname ASC, User.user_firstname ASC, User.user_status ASC";  // Otherwise go for everyone!
			}


				//
				// Take queries and connect to database
				// Then pass onto table to display
				//

				if (isset($_SESSION['results'])) {

					$query=$_SESSION['results'];
					$table_opt=$_SESSION['table_opt'];
					unset($_SESSION['results, table_opt']);
					$results_table = '0';
					}


				if(!$res=ShopDB::query($query)){
					user_error(shopDB::error());
					return;
					}

				if (!ShopDB::num_rows($res)) {
					return addWarning('no_result');
					} else {$num_results = ShopDB::num_rows($res);
					}


			if ($results_table == '0' || $results_table == '2' ) {  // Which table to use to display results: '0' = General



				// find out how many rows are in the table
				//$sql = "SELECT COUNT(*) FROM numbers";
				//$result = mysql_query($sql, $conn) or trigger_error("SQL", E_USER_ERROR);
				//$r = mysql_fetch_row($result);
				//$numrows = $num_results[0];
				$numrows = $num_results;

				// number of rows to show per page
				$rowsperpage = $_REQUEST['npp'];


				if (isset($rowsperpage)) {
				$limit = $rowsperpage;
				}else{
				$limit = 10; // How many items to show per page by default if visitor hasn't used dropdown.
				}
				//$rowsperpage = 20; ##
				// find out total pages
				$totalpages = ceil($numrows / $limit);

				// get the current page or set a default
				if (isset($_SESSION['currentpage']) && is_numeric($_SESSION['currentpage'])) {
				   // cast var as int
				   $currentpage = (int) $_SESSION['currentpage'];
				   unset ($_SESSION['currentpage']);
				} else {
				   // default page num
				   $currentpage = 1;
				} // end if

				// if current page is greater than total pages...
				if ($currentpage > $totalpages) {
				   // set current page to last page
				   $currentpage = $totalpages;
				} // end if
				// if current page is less than first page...
				if ($currentpage < 1) {
				   // set current page to first page
				   $currentpage = 1;
				} // end if

				// the offset of the list, based on current page
				$offset = ($currentpage - 1) * $rowsperpage;

				// get the info from the db
				//$sql = "SELECT id, number FROM numbers LIMIT $offset, $rowsperpage";
				//$result = mysql_query($sql, $conn) or trigger_error("SQL", E_USER_ERROR);

					$query2 = ($query." LIMIT $offset, $limit");

				if(!$res=ShopDB::query($query2)){
					user_error(shopDB::error());
					return;
					}

			echo "<table><tr><td>";
			echo "
				<form name='npp' method='POST' action='{$_SERVER['PHP_SELF']}'>
				<input type=hidden name='page' value='$currentpage'>
				<input type=hidden name='update' value='page'>
				Results Per Page:<select name='npp'>
				<option selected>$limit</option>
				<option value=5>5</option>
				<option value=10>10</option>
				<option value=25>25</option>
				<option value=50>50</option>
				<option value=100>100</option>
				</select>
				<input type=submit value='Go'>
				</form>
				";
				echo "</td><td>";
				echo $this->page_links($currentpage, $range, $totalpages, $limit);
				echo "</td></tr></table>";

			echo "<form name='search_results' method='POST' action='{$_SERVER['PHP_SELF']}' >
				<input type='hidden' name='action' value='global_editData'/>\n";  //Form value = global_edit

				echo "<table class='admin_list' width='$this->width' cellspacing='1' cellpadding='4'>\n";
				echo "<tr><td colspan='4' class='admin_list_title'>".con('global_table_results_header').": ".$num_results." ".con('global_table_customers')."</td></tr>";
				foreach ($query_title as $search_criteria){
				echo "<tr><td colspan='4'>".con("$search_criteria")."</td></tr>";
				};
				echo "<tr><th>".con('global_table_id')."</th><th>".con('global_table_name')."</th><th>".con('global_table_'.$table_opt)."</th><th>".con('global_table_joined')."</th><th>".con('global_table_last_visit')."</th><th>".con('global_table_qty')."</th><th>".con('global_table_status')."</th><th></th></tr>";
				 $alt=0;
				while($row=shopDB::fetch_assoc($res)){
					$flag=1;
					$joinedDate=strtotime($row["user_created"]);
					$lastVisited=strtotime($row["user_lastlogin"]);
					echo "<tr class='admin_list_row_$alt'>
						<td class='admin_list_item'>".$row["user_id"]."</td>
						<td class='admin_list_item'>
						<a class='link' href='{$_SERVER['PHP_SELF']}?action=user_detail&user_id=".$row["user_id"]."'>".
						$row["user_lastname"].", ".$row["user_firstname"]."
						</a>
						</td>
						<td class='admin_list_item'>".$row["user_".$table_opt]."";
							if (isset($table_opt2)) { echo"<br>".$row["user_".$table_opt2]."</td>"; } else {echo "</td>";}
					echo "<td class='admin_list_item'>".date("jS M Y", $joinedDate)."</td>";
					if (!$lastVisited ==""){	//If there is no record of a visit then the the table cell will be empty
						echo "<td class='admin_list_item'>".date("jS M Y", $lastVisited)."</td>";
						} else { echo "<td></td>"; }
					echo "<td class='admin_list_item'>".$row["user_current_tickets"]."</td>
						<td class='admin_list_item'>".$this->print_status($row["user_status"])."</td>" ;


					echo "<td class='admin_list_item' width='40' align='left' nowrap>
						<input type='checkbox' name='rowID[]' value=".$row['user_id']." />";

					if ($row["user_current_tickets"]=="0") { //Delete button only available when patrons have no current tickets
						echo $view->show_button("javascript:if(confirm(\"".con('delete_item')."\")){location.href=\"{$_SERVER['PHP_SELF']}?action=delete_patron&ID={$row["user_id"]}&status={$row["user_status"]}\";}","remove",2,array('tooltiptext'=>"Delete {$row['user_firstname']} {$row['user_lastname']}?"));
						}
					echo "</td>";



					echo "</tr>\n";
				  $alt=($alt+1)%2;
				}

				echo "</table>";

				$_SESSION['results'] = $query;
				$_SESSION['table_opt'] =$table_opt;


				echo "<table>"; // **** Select ALL and Option Buttons ****
				echo"<tr><td>
				<label><input type='checkbox' value='on' name='allbox' onclick='checkAll();'/>Select/De-select all</label><br />
				</td></tr>";

				echo "<tr></tr>";
				echo "<tr class='admin_value'><td>&nbsp;</td><td class='admin_value' align='right' colspan='1'>
				<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Manually edit' style='' name='submit' value='edit_manual' type='submit'>".con(btn_manual_edit)."</button>
				<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Clean it up' style='' name='submit' value='edit_format' type='submit'>".con(btn_format_data)."</button>
				<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Global edit' style='' name='submit' value='edit_global' type='submit'>".con(btn_global_edit)."</button>

				</td></tr>";

				echo "</table></form>";
					echo "<table><tr><td>"; // Navigation - bottom
				echo "
				<form name='npp' method='POST' action='{$_SERVER['PHP_SELF']}'>
				<input type=hidden name='page' value='$currentpage'>
				<input type=hidden name='update' value='page'>
				Results Per Page:<select name='npp'>
				<option selected>$limit</option>
				<option value=5>5</option>
				<option value=10>10</option>
				<option value=25>25</option>
				<option value=50>50</option>
				<option value=100>100</option>
				</select>
				<input type=submit value='Go'>
				</form>
				";
				echo "</td><td>";
				echo $this->page_links($currentpage, $range, $totalpages, $limit);
				echo "</td></tr></table>";

				return true;

			} elseif ($results_table == '1') { // Which table to use to display results: '1' = Fieldsets based on email address

				echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>
				<input type='hidden' name='action' value='global_editPos'/>\n";

				$view->form_head(con('search_title_global_posmerge_update'));
				echo "<tr><td colspan='4' class='admin_list_title'>".con('global_table_results_header').": ".$num_results." ".con('global_table_customers')."</td></tr>";

				echo "<tr><td colspan='2'>".con('search_notes_global_posMerge_update')."</td></tr>\n";
				echo "<tr><td></td></tr>";


				$set = array();
				while ($record=shopDB::fetch_object($res)) {
					$set[strtolower($record->user_email)][]=$record; // Ensure emails match regardless of case
					}
				echo"<table><tr><td colspan='2'>";
				$count=1;
				foreach ($set as $user_email => $records) {
					$user_member=$user_guest=$user_pos='0';
					echo "<fieldset><table class='admin_form' width='500' cellspacing='1' cellpadding='4'>\n";
					echo "<legend>Email: {$user_email}</legend>\n";
					echo "<tr><th>".con('global_table_id')."</th><th>".con('global_table_name')."</th><th>".con('global_table_city')."</th><th>".con('global_table_joined')."</th><th>".con('global_table_last_visit')."</th><th>".con('global_table_qty')."</th><th>".con('global_table_status')."</th><th></th></tr>";
					foreach ($records as $record) {
							$joinedDate=strtotime($record->user_created);
							$lastVisited=strtotime($record->user_lastlogin);
							echo "<tr class='admin_list_row_$alt'>
							<td class='admin_list_item'>".$record->user_id."</td>
							<td class='admin_list_item'>
							<a class='link' href='{$_SERVER['PHP_SELF']}?action=user_detail&user_id=".$record->user_id."'>".
							$record->user_lastname.", ".$record->user_firstname."
							</a>
							</td>
							<td class='admin_list_item'>".$record->user_city."</td>
							<td class='admin_list_item'>".date("jS M Y", $joinedDate)."</td>";
						if (!$lastVisited ==""){	//If there is no record of a visit then the the table cell will be empty
							echo "<td class='admin_list_item'>".date("jS M Y", $lastVisited)."</td>";
							} else { echo "<td></td>"; }
						echo "<td class='admin_list_item'>".$record->user_current_tickets."</td>
							<td class='admin_list_item'>".$this->print_status($record->user_status)."</td>" ;
						echo "</tr>\n";
						if ($record->user_status ==4){$user_pos=$record->user_id;}
						if ($record->user_status ==3){$user_guest=$record->user_id;}
						if ($record->user_status ==2){$user_member=$record->user_id;}

					}
					echo  "<input type='hidden' name='user_pos_id[$count]' value='".$user_pos."'/>\n";
					echo  "<input type='hidden' name='user_guest_id[$count]' value='".$user_guest."'/>\n";
					echo  "<input type='hidden' name='user_member_id[$count]' value='".$user_member."'/>\n";
					echo "<tr><td colspan='5' class='admin_name'>".con('posMerge_select_user')."</td>
					<td colspan='2' class='admin_value'><input type='checkbox' name='ref_id[$count]' value='".$count."'></td></tr>";
					echo "</table></fieldset><br />\n";
					$count ++;
					}

			//	$view->form_foot(2,'','Merge');
						echo "<tr class='admin_value'><td class='admin_value' align='center' >";


			echo	"<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Job1' style='' name='action' value='global_update' type='submit' onClick=\"return confirmSubmit('".con('confirm_sure')."')\">".con('btn_merge')."</button>";
			echo	"<button id='cancel' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Cancel' style='' name='cancel'  onClick=\"return confirmSubmit('".con('confirm_cancel')."')\" onclick=\"window.location = '{$_SERVER['PHP_SELF']}'\">".con('btn_cancel')."</button>


					</td></tr>";


			echo "</table>";
			echo "</form>";


				return true;

			}
	}

	function global_editClean (&$data, $view){ // Added by Lxsparks 05/11
		global $_SHOP, $_COUNTRY_LIST;

		if (!isset($_COUNTRY_LIST)) {
		  If (file_exists(INC."lang".DS."countries_". $_SHOP->lang.".inc")){
			include_once(INC."lang".DS."countries_". $_SHOP->lang.".inc");
		  }else {
			include_once(INC."lang".DS."countries_en.inc");
		  }
		}

		$count = 0;
		$query_title = array();


		if (is_array($_POST['rowID'])) {
			//print('It is an array');
			$id=($_POST['rowID']);
			//print_r($id);



			$query="SELECT * FROM User WHERE User.user_id IN ('".join("','", $id)."') ORDER BY User.user_lastname ASC, User.user_firstname ASC, User.user_status ASC";

			//$query="SELECT auth.active, User.* FROM User LEFT JOIN auth ON auth.user_id = User.user_id WHERE User.user_status !=1";


			} else { return addWarning('search_choice_one_fields'); }

			if(!$res=ShopDB::query($query)){
			user_error(shopDB::error());
			return;
			}

			if (!ShopDB::num_rows($res)) {
			return addWarning('no_result');
			} else {$num_results = ShopDB::num_rows($res);
			}

	if ($_POST['submit'] == 'edit_manual') {	//Start of edit_manual

		$results_table = '0';
		$readonly='';
		$notes='search_notes_global_edit_manual';

		while($row=shopDB::fetch_assoc($res)){

			$rows['user_id'][]=$row['user_id'];
			$rows['user_firstname'][]=$row['user_firstname'];
			$rows['user_lastname'][]=$row['user_lastname'];
			$rows['user_address'][]=$row['user_address'];
			$rows['user_address1'][]=$row['user_address1'];
			$rows['user_city'][]=$row['user_city'];
			$rows['user_state'][]=$row['user_state'];
			$rows['user_zip'][]=$row['user_zip'];
			$rows['user_phone'][]=$row['user_phone'];
			$rows['user_fax'][]=$row['user_fax'];
			$rows['user_email'][]=$row['user_email'];
			$rows['user_status'][]=$row['user_status'];
			$rows['user_country'][]=$row['user_country'];

		}


	  } elseif ($_POST['submit'] == 'edit_format') { // End of Manual Edit Data / Start of Cleanup Data

		$results_table = '0';
		$readonly="readonly='readonly'";
		$notes='search_notes_edit_format';

		while($row=shopDB::fetch_assoc($res)){

			$rows['user_id'][]=$row['user_id'];
			$ctry = $rows['user_country'][]=$row['user_country'];

			$rows['user_firstname'][]=$this->clean_customerData($row['user_firstname'], 50);
			$rows['user_lastname'][]=$this->clean_customerData($row['user_lastname'], 50);
			$rows['user_address'][]=$this->clean_customerData($row['user_address'], 75);
			$rows['user_address1'][]=$this->clean_customerData($row['user_address1'], 75);
			$rows['user_city'][]=$this->clean_customerData($row['user_city'], 50);
			$rows['user_state'][]=$this->clean_customerData($row['user_state'], 50);
			$rows['user_zip'][]=$this->clean_customerZip($row['user_zip'],$ctry);

			$rows['user_phone'][]=$this->clean_customerTel($row['user_phone'], $ctry);
			$rows['user_fax'][]=$this->clean_customerTel($row['user_fax'], $ctry);

			$rows['user_email'][]=$row['user_email'];
			$rows['user_status'][]=$row['user_status'];

			$exp="(GIR 0AA|[A-PR-UWYZ]([0-9][0-9A-HJKPS-UW]?|[A-HK-Y][0-9][0-9ABEHMNPRV-Y]?) [0-9][ABD-HJLNP-UW-Z]{2})";

		}


	  } elseif ($_POST['submit'] == 'edit_global') { // End of Cleanup Data / Global edit

		$results_table = '1';
		$readonly='';
		$notes='search_notes_global_edit_global';

		while($row=shopDB::fetch_assoc($res)){

		$rows['user_id'][]=$row['user_id'];
		$rows['user_firstname'][]=$row['user_firstname'];
		$rows['user_lastname'][]=$row['user_lastname'];
		$rows['user_address'][]="";
		$rows['user_address1'][]="";
		$rows['user_city'][]="";
		$rows['user_state'][]="";
		$rows['user_zip'][]="";
		$rows['user_phone'][]="";
		$rows['user_fax'][]="";
		$rows['user_email'][]=$row['user_email'];
		$rows['user_status'][]=$row['user_status'];
		$rows['user_country'][]=$row['user_country'];
		}
	  }


	  if ($results_table=='0') { // DISPLAY results table '0'

			echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>
					<input type='hidden' name='action' value='global_search'/>\n";

			echo "<table>";
			echo "<tr><td class='admin_list_title'>".con('global_table_results_header_edit').": ".$num_results." ".con('global_table_customers')."</td></tr>";
			echo "<tr><td>".con("$notes")."</td></tr>\n";

			//while($row=shopDB::fetch_assoc($res)){
			for ($i='0';$i<count($rows['user_id']);$i++) {


			echo "<tr><td><table>";


			echo "<tr>	<td class='admin_list_item' width ='15' style='font-weight:bold;'>".$rows['user_id'][$i]." <input type='hidden' name='id[]' value='".$rows['user_id'][$i]."' /> </td>
						<td><input ".$readonly." type='text' name='firstname[]' size='25' value ='". $rows['user_firstname'][$i]."' /></td>
						<td><input ".$readonly." type='text' name='lastname[]' size='25' value ='". $rows['user_lastname'][$i]."' /></td>
						<td class='admin_list_item'>".$this->print_status($rows['user_status'][$i])."</td>
						<td>".$rows['user_country'][$i]."</td>
						<td></td>			</tr>";
			echo "<tr>	<td></td>
						<td><input ".$readonly." type='text' name='address[]' size='25' value ='". $rows['user_address'][$i]."' /></td>
						<td><input ".$readonly." type='text' name='address1[]' size='25' value ='". $rows['user_address1'][$i]."' /></td>	<td></td> </tr>";

			echo "<tr>	<td></td>
						<td><input ".$readonly." type='text' name='city[]' size='25' value ='". $rows['user_city'][$i]."' /></td>
						<td><input ".$readonly." type='text' name='state[]' size='25' value ='". $rows['user_state'][$i]."' /></td>
						<td><input ".$readonly." type='text' name='zip[]' size='10' value ='". $rows['user_zip'][$i]."' /></td>	<td></td>  </tr>";
			echo "<tr>	<td></td>
						<td><input ".$readonly." type='text' name='phone[]' size='25' value ='". $rows['user_phone'][$i]."' /></td>
						<td><input ".$readonly." type='text' name='fax[]' size='25' value ='". $rows['user_fax'][$i]."' /></td>	<td></td>			</tr>";
			echo "<tr>	<td></td>
						<td colspan='3'><input ".$readonly." type='text' name='email[]' size='40' value ='". $rows['user_email'][$i]."' /></td>			<td></td>			</tr>";

			echo "</td></tr></table>";



			}

			echo "<tr class='admin_value'><td class='admin_value' align='center' >";


			echo	"<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Job1' style='' name='action' value='global_update' type='submit' onClick=\"return confirmSubmit('".con(confirm_sure)."')\">Update data</button>";
			echo	"<button id='cancel' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Cancel' style='' name='cancel'  onClick=\"return confirmSubmit('".con(confirm_cancel)."')\" onclick=\"window.location = '{$_SERVER['PHP_SELF']}'\">Cancel</button>


					</td></tr>";


			echo "</table>";
			echo "</form>";

			return true;

		} // End of results_table '0'

		else if ($results_table=='1'){


			$data=serialize($rows['user_id']);

			echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>
				<input type='hidden' name='action' value='global_update_global'/>\n
					<input type='hidden' name='users' value='$data'/>\n";

			echo "<table>";
			echo "<tr><td class='admin_list_title'>".con('global_table_results_header_edit').": ".$num_results." ".con('global_table_customers')."</td></tr>";
			echo "<tr><td>".con("$notes")."</td></tr>\n";

			// Have to PASS user ID values as an array to the form_r
			// Have to PASS a value to distinguish which query needs to be used
			// form_r will then have to have two seperate query builds

			echo "<tr><td><table>";

//echo "<td class='admin_list_item' width='40' align='left' nowrap>

			echo "	<tr><th>Data Field</th><th>Information</th><th>Select</th>";

			echo "	<tr><td>".con('user_address')."</td><td><input type='text' name='address' size='25' value ='' /></td>
					<td><input type='checkbox' name='sel_address' value='1' /></td></tr>
					<tr><td>".con('user_address1')."</td><td><input type='text' name='address1' size='25' value ='' /></td>
					<td><input type='checkbox' name='sel_address1' value='1' /></td></tr>";

			echo "	<tr><td>".con('user_city')."</td><td><input type='text' name='city' size='25' value ='' /></td>
					<td><input type='checkbox' name='sel_city' value='1' /></td></tr>
					<tr><td>".con('user_state')."</td><td><input type='text' name='state' size='25' value ='' /></td>
					<td><input type='checkbox' name='sel_state' value='1' /></td></tr>
					<tr><td>".con('user_zip')."</td><td><input type='text' name='zip' size='10' value ='' /></td>
					<td><input type='checkbox' name='sel_zip' value='1' /></td></tr>";

			echo  "		<tr><td>".con('user_country')."</td><td><SELECT name='country'>";

			foreach  ($_COUNTRY_LIST as $k => $v) {
				echo '<option value="'.$k.'"'.$s.'>'.$v.'</option>';
			}

			echo "		</SELECT></td><td><input type='checkbox' name='sel_country' value='1' /></td></tr>";

			echo"	<tr><td></td><td></td></tr>";

			echo "	<tr><td>".con('user_phone')."</td><td><input type='text' name='phone' size='25' value ='' /></td>
					<td><input type='checkbox' name='sel_phone' value='1' /></td></tr>
					<tr><td>".con('user_fax')."</td><td><input type='text' name='fax' size='25' value ='' /></td>
					<td><input type='checkbox' name='sel_fax' value='1' /></td></tr>";

			echo"	<tr><td></td><td></td></tr>";

			echo "</td></tr></table>";

			echo "<tr class='admin_value'><td class='admin_value' align='center' >";

			echo	"<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Job1' style='' name='submit' value='update_global' type='submit' onClick=\"return confirmSubmit('".con('confirm_sure')."')\">Update data</button>";
			echo	"<button id='cancel' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Cancel' style='' name='cancel'  onClick=\"return confirmSubmit('".con('confirm_cancel')."')\" onclick=\"window.location = '{$_SERVER['PHP_SELF']}'\">Cancel</button>
					</td></tr>";

			echo "</table>";
			echo "</form>";

			return true;

		} // End of results_table '1'

	  }

	function global_updateR ($data, $view) {

		Global $_SHOP, $_COUNTRY_LIST;
		if (!isset($_COUNTRY_LIST)) {
		  If (file_exists(INC."lang".DS."countries_". $_SHOP->lang.".inc")){
			include_once(INC."lang".DS."countries_". $_SHOP->lang.".inc");
		  }else {
			include_once(INC."lang".DS."countries_en.inc");
		  }
		}


		if ($_POST['submit']=='update_global') { // Global update customers with the same details

			$id=unserialize($data['users']);  // Get the users back into an array
			$IDs=implode(',', $id);
			$ctry=$data['country'];

			if ($data['sel_address'] =='1') {
				$address=$this->clean_customerData($data['address'], 75);
				$query_type[]="tb1.user_address='$address'";}
			if ($data['sel_address1'] =='1') {$address1=$this->clean_customerData($data['address1'], 75);
				$query_type[]="tb1.user_address1='$address1'";}
			if ($data['sel_city'] =='1') {$city=$this->clean_customerData($data['city'], 50);
				$query_type[]="tb1.user_city='$city'";}
			if ($data['sel_state'] =='1') {$state=$this->clean_customerData($data['state'], 50);
				$query_type[]="tb1.user_state='$state'";}
			if ($data['sel_zip'] =='1') {$zip=$this->clean_customerZip($data['zip'],$ctry);
				$query_type[]="tb1.user_zip='$zip'";}
			if ($data['sel_country'] =='1' && array_key_exists($ctry, $_COUNTRY_LIST)) {
				$query_type[]="tb1.user_country='$ctry'";}
			if ($data['sel_phone'] =='1') {$phone=$this->clean_customerTel($data['phone'],$ctry);
				$query_type[]="tb1.user_phone='$phone'";}
			if ($data['sel_fax'] =='1') {$fax=$this->clean_customerTel($data['fax'],$ctry);
				$query_type[]="tb1.user_fax='$fax'";}

			if (!isset($query_type)) {
					return addWarning('search_choice_one_fields');
				} else {
					$query="UPDATE User tb1 SET ". implode("\n , ",$query_type)." WHERE tb1.user_id IN($IDs)";
				}



		} else {

			$id=$data['id']; //Assign each colum data with an array sent from the form
			$firstname=$data['firstname'];
			$lastname=$data['lastname'];
			$address=$data['address'];
			$address1=$data['address1'];
			$city=$data['city'];
			$state=$data['state'];
			$zip=$data['zip'];
			$phone=$data['phone'];
			$fax=$data['fax'];
			$email=$data['email'];

			$query="UPDATE User tb1 LEFT JOIN auth tb2 ON tb1.user_id = tb2.user_id
			SET tb1.user_firstname='$firstname[$i]', tb1.user_lastname='$lastname[$i]', tb1.user_address='$address[$i]', tb1.user_address1='$address1[$i]', tb1.user_city='$city[$i]',
			tb1.user_state='$state[$i]', tb1.user_zip='$zip[$i]', tb1.user_phone='$phone[$i]', tb1.user_fax='$fax[$i]', tb1.user_email='$email[$i]', tb2.username='$email[$i]'  WHERE tb1.user_id='$id[$i]'";


		}


		// Loop through and update each record set
		$i='0';
		$v='0';
		foreach($id as $ids){


			if(!$res=ShopDB::query($query)){
			   user_error(shopDB::error());
			   echo "Update failed.<br />";
			   return;
			   } else {
					$v++; // If record was successfully updated - record it
			   }
			$i++;
		}

		if ($v > 0) {
			echo "<p style='font-weight:bold;'>".con("global_update_title_updated")."</p>";
			echo "<p>".con("global_update_notes_updated").": ".$v."</p>";
			return true;

		} else {

		echo "<p style='font-weight:bold;'>".con("global_update_title_notupdated")."</p>";
		echo "<p>".con("global_update_notes_notupdated")."</p>";
		return true;
		}
	}

	function global_posR(&$data, $view) { // Added by Lxsparks 06/11
		global $_SHOP;

		if ($data["user_pos_id"] || $data["user_member_id"]) {
			echo "Results have been recieved<br />";


			print_r ($data["ref_id"]);
			echo "<br />";
			echo "MEMBER: ";
			print_r ($data["user_member_id"]);
			echo "<br />";
			echo "POS: ";
			print_r ($data["user_pos_id"]);
			echo "<br />";
			echo "GUEST: ";
			print_r ($data["user_guest_id"]);
			echo "<br />";



			echo $count."<br />";
			//$ref_ID=$data["ref_id"];
			$ref_ID=array_values($data["ref_id"]);
			$member_ID=$data["user_member_id"];
			$pos_ID=$data["user_pos_id"];
			$guest_ID=$data["user_guest_id"];
			$count=count($ref_ID);


			$ii=0;
			for($i=0;$i<$count;$i++){
			$v=$ref_ID[$i];


			//***********************************//

			// Build two arrays (OLD and NEW) which are the pairs of customer IDs where one will be replaced by the other
			// If there are 3 customer types Guests will go straight to Members
			// Echo the results on screen

			if ($member_ID[$v] !=0 && $pos_ID[$v] !=0) { //POS -> Members
				$new_ID[$ii]=$member_ID[$v];
				$old_ID[$ii]=$pos_ID[$v];
				echo con("pos_cust_rec")." ".$pos_ID[$v].", ".con("replaced_by")." ".con("member_cust_rec")." ".$member_ID[$v]."<br />";
				$ii++;
				}

			if ($member_ID[$v] !=0 && $guest_ID[$v] !=0) { //Guest -> Members
				$new_ID[$ii]=$member_ID[$v];
				$old_ID[$ii]=$guest_ID[$v];
				echo con("guest_cust_rec")." ".$guest_ID[$v].", ".con("replaced_by")." ".con("member_cust_rec")." ".$member_ID[$v]."<br />";
				$ii++;
				}

			if ($pos_ID[$v] !=0 && $guest_ID[$v] !=0  && $member_ID[$v]=0) { //Guest -> POS (if not into Member)
				$new_ID[$ii]=$pos_ID[$v];
				$old_ID[$ii]=$guest_ID[$v];
				echo con("guest_cust_rec")." ".$guest_ID[$v].", ".con("replaced_by")." ".con("pos_cust_rec")." ".$pos_ID[$v]."<br />";
				$ii++;
				}
		}


			//This query loops through the number of old member details that need updating
			//1) Updates all OLD ID Orders to NEW ID Orders
			//2) Updates all OLD ID Seats to NEW ID Seats
			//3) Updates the NEW ID current tickets with itself and the value of those in the OLD ID current tickets
			for($i=0;$i<count($old_ID);$i++){

				$query="UPDATE `Order` AS `O`, `Seat` AS `S`, `User` AS `a` JOIN `User` AS `b` ON a.user_id='".$new_ID[$i]."' AND b.User_id='".$old_ID[$i]."'
				SET O.order_user_id=IF(O.order_user_id='".$old_ID[$i]."','".$new_ID[$i]."',O.order_user_id),
				S.seat_user_id=IF(S.seat_user_id='".$old_ID[$i]."','".$new_ID[$i]."',S.seat_user_id),
				a.user_order_total=a.user_order_total+b.user_order_total,
				a.user_current_tickets=a.user_current_tickets+b.user_current_tickets,
				a.user_total_tickets=a.user_total_tickets+b.user_total_tickets";

				if(!$res=ShopDB::query($query)){
					user_error(shopDB::error());
					echo "<br />Order/Seats NOT updated";
					return;
				} else { echo "<br />Order/Seats updated".$old_ID[$i]." with ".$new_ID[$i]."<br />";}

			}
			//***********************************//
			// Merge empty fields of the new (user being kept/updated) with old data if it exists.
			// Custom fields are also merged for future use.
			// Update last_login with the newest date.
			// Update user_creatred with the oldest date.

			$old = implode(',', $old_ID);
			$query="SELECT * FROM User WHERE user_id IN ($old)";

			if(!$res_old=ShopDB::query($query)){
				user_error(shopDB::error());
			   return;
			}

			$new = implode(',', $new_ID);
			$query="SELECT * FROM User WHERE user_id IN ($new)";

			if(!$res_new=ShopDB::query($query)){
				user_error(shopDB::error());
			   return;
			}

			if (!ShopDB::num_rows($res_new)) {
			return addWarning('no_result');
			} else {$n=ShopDB::num_rows($res_new);
			}

			$i=0;
			for($i;$i<$n;$i++){
				$row_old[$i]=shopDB::fetch_assoc($res_old);
				$row_new[$i]=shopDB::fetch_assoc($res_new);
				}

			print_r ($row_old);
			echo "<br /><br />";
			print_r ($row_new);
			echo "<br />";
			echo "<br />";

			$i=0;
			for($i;$i<$n;$i++){
				if (strlen($row_new[$i]['user_address1']) == 0 && strlen($row_old[$i]['user_address1']) != 0){ $row_new[$i]['user_address1']=$row_old[$i]['user_address1'];}
				if (strlen($row_new[$i]['user_state']) == 0 && strlen($row_old[$i]['user_state']) != 0){ $row_new[$i]['user_state']=$row_old[$i]['user_state'];}
				if (strlen($row_new[$i]['user_phone']) == 0 && strlen($row_old[$i]['user_phone']) != 0){ $row_new[$i]['user_phone']=$row_old[$i]['user_phone'];}
				if (strlen($row_new[$i]['user_fax']) == 0 && strlen($row_old[$i]['user_fax']) != 0){ $row_new[$i]['user_fax']=$row_old[$i]['user_fax'];}
				if (strlen($row_new[$i]['user_prefs']) == 0 && strlen($row_old[$i]['user_prefs']) != 0){ $row_new[$i]['user_prefs']=$row_old[$i]['user_prefs'];}
				if (strlen($row_new[$i]['user_custom1']) == 0 && strlen($row_old[$i]['user_custom1']) != 0){ $row_new[$i]['user_custom1']=$row_old[$i]['user_custom1'];}
				if (strlen($row_new[$i]['user_custom2']) == 0 && strlen($row_old[$i]['user_custom2']) != 0){ $row_new[$i]['user_custom2']=$row_old[$i]['user_custom2'];}

				if ($row_new[$i]['user_custom3'] == 0 && $row_old[$i]['user_custom3'] != 0){ $row_new[$i]['user_custom3']=$row_old[$i]['user_custom3'];} // INT default value is 0
				if ($row_new[$i]['user_custom4'] < $row_old[$i]['user_custom4']){ $row_new[$i]['user_custom4']=$row_old[$i]['user_custom4'];} // Date format
				if ($row_new[$i]['user_created'] > $row_old[$i]['user_created']){ $row_new[$i]['user_created']=$row_old[$i]['user_created'];} // Date format user the oldest date
				if ($row_new[$i]['user_lastlogin'] < $row_old[$i]['user_lastlogin']){ $row_new[$i]['user_lastlogin']=$row_old[$i]['user_lastlogin'];} // Date format user the newest date

				if (strlen($row_new[$i]['user_owner_id']) == 0 && strlen($row_old[$i]['user_owner_id']) != 0  || $row_new[$i]['user_owner_id'] == 0 && $row_old[$i]['user_owner_id'] != 0){ $row_new[$i]['user_owner_id']=$row_old[$i]['user_owner_id'];}

			echo "<br />New user details: ".$row_new[$i]['user_address1'].".  Old user details: ".$row_old[$i]['user_address1'].".<br />";

			}

			// Loop through and update each record set
			$i='0';
			$v='1';
			for($i;$i<$n;$i++){

				$query="UPDATE User SET
				user_address1='".$row_new[$i]['user_address1']."', user_state='".$row_new[$i]['user_state']."', user_phone='".$row_new[$i]['user_phone']."', user_fax='".$row_new[$i]['user_fax']."',
				user_prefs='".$row_new[$i]['user_prefs']."', user_custom1='".$row_new[$i]['user_custom1']."', user_custom2='".$row_new[$i]['user_custom2']."',
				user_custom3='".$row_new[$i]['user_custom3']."', user_custom4='".$row_new[$i]['user_custom4']."', user_created='".$row_new[$i]['user_created']."',
				user_lastlogin='".$row_new[$i]['user_lastlogin']."', user_owner_id='".$row_new[$i]['user_owner_id']."'
				WHERE user_id='".$row_new[$i]['user_id']."'";

				if(!$res=ShopDB::query($query)){
				   user_error(shopDB::error());
				   echo "<br />DISASTER!!<br />";
				   return;
				   } else {
						$v++; // If record was successfully updated - record it
				   }
				$i++;
			}

			if ($v > 0) {
				echo "<p style='font-weight:bold;'>".con("global_update_title_updated")."</p>";
				echo "<p>".con("global_update_notes_updated").": ".$v."</p>";
				//return true;

			} else {
				echo "<p style='font-weight:bold;'>".con("global_update_title_notupdated")."</p>";
				echo "<p>".con("global_update_notes_notupdated")."</p>";
				return true;
			}


			//***********************************//
			//Then we DELETE the OLD user/s from the User table
			$query="DELETE FROM User WHERE user_id IN ($old)";

			if(!$res=ShopDB::query($query)){
				user_error(shopDB::error());
				echo "<br />Failed to delete!<br />";
				 echo"Value of OLD: "; print_r($old);
			   return;
			} else { echo"*Value of OLD: "; print_r($old);}




		//***********************************//

		} else { return; }
	}

	function deleteTable(&$data, $view) { // Added by Lxsparks 05/11
		global $_SHOP;
		$id= $_GET['ID'];
		$status=$_GET['status'];
		echo $id." and status: ".$status;

		if ($status == '2') {  //

			$query="DELETE auth.*, User.* FROM auth JOIN User ON auth.user_id=User.user_id WHERE auth.user_id='$id'";

			} else { $query="DELETE User.* FROM User WHERE User.user_id='$id'";
			}

		if(!$res=ShopDB::query($query)){
			   user_error(shopDB::error());
			   return;
			}
     }

	// ***************************************************************************** //

	function searchForm (&$data, $view) {

	global $_SHOP;
	$results_table = '';
	$basicOptions=array('0'=>'N/A', '1'=>'Yes', '2'=>'No');
	$alphaStart=array('A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','X'=>'X','Y'=>'Y');
	$alphaEnd=array('B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','X'=>'X','Y'=>'Y','Z'=>'Z');
	unset($_SESSION['results']); //Ensure nothing is carried over from previous searches


	echo"<h1>".con(form_title_main)."</h1>";


	// *** Start of Drop-down Slide  ADDRESS block *** \\
	echo "<div class='dg_header'>".con('search_title_address')."</div>
			<div class='dg_body'>
				<div>\n";

		echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>"; // *** Start of Form *** \\
		echo "<input type='hidden' name='action' value='global_search'/>\n";  //Form value = global_search

			//Start of Street search
			echo "<h1>".con('global_search_title_street')."</h1>";
			echo "<p>".con('global_search_notes_street')."</p>";
			echo "<p class='admin_name'><label>".con('user_address')."</label><input type='text' name='searchAddress' size='50' /></p>";
			echo "<p class='admin_name'><label>".con('user_address1')."</label><input type='text' name='searchAddress1' size='50' /></p>";


			echo "<p>
				<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_address' type='submit'>".con('btn_search')."</button>
				<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con('btn_reset')."</button>
				</p>";

			echo "<hr>";

			//Start of City word search
			echo "<h1>".con('global_search_title_city')."</h1>";
			echo "<p>".con('global_search_notes_city')."</p>";
			echo "<p class='admin_name'><label>".con('user_city')."</label><input type='text' name='searchCity' size='50' /></p>";

			echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_city' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

			echo"<hr>";

			//Start of State word search
			echo "<h1>".con('global_search_title_state')."</h1>";
			echo "<p>".con('global_search_notes_state')."</p>";
			echo "<p class='admin_name'><label>".con('user_state')."</label><input type='text' name='searchState' size='50' /></p>";

			echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_state' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

			echo "<hr>";

			//Start of Postcode word search
			echo "<h1>".con('global_search_title_zip')."</h1>";
			echo "<p>".con('global_search_notes_zip')."</p>";
			echo "<p class='admin_name'><label>".con('user_zip')."</label><input type='text' name='searchZip' size='10' /></p>";

			echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_zip' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

			echo "<hr>";

			//Start of Postcode Range search
			echo "<h1>".con('global_search_title_zipRange')."</h1>";
			echo "<p>".con('global_search_notes_zipRange')."</p>";
			echo "<p class='admin_name'><label>".con('user_zip_start')."</label><input type='text' name='startZip' size='10' /></p>";
			echo "<p class='admin_name'><label>".con('user_zip_end')."</label><input type='text' name='endZip' size='10' /></p>";

			echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_zipRange' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";


		echo "</form>"; // *** End of Form *** \\
		echo "			</div>
					</div>\n";
	echo "</div>";
	// *** End of Drop-down Slide block *** \\

	// *** Start of Drop-down Slide NAME block *** \\
	echo "<div class='dg_header'>".con(search_title_name)."</div>
			<div class='dg_body'>
				<div>\n";

		echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>"; // *** Start of Form *** \\
		echo "<input type='hidden' name='action' value='global_search'/>\n";  //Form value = global_search

			//Start of Surname word search
			echo "<h1>".con('global_search_title_surname')."</h1>";
			echo "<p>".con('global_search_notes_surname')."</p>";
			echo "<p class='admin_name'><label>".con('user_surname')."</label><input type='text' name='searchLastName' size='25' /></p>";

			echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_surname' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

			echo "<hr>";

			 //Start of Surname alphabet search
			echo "<h1>".con('global_search_title_AZ')."</h1>";
			echo "<p>".con('global_search_notes_AZ')."</p>";
			echo "<p class='admin_name'><label class='opt'>".con('user_surname_start')."</label>
					<select name='startLetter'>
					<option value='0'>Choose a letter</option>";
			$this->showFormOptions($alphaStart);
			echo "</select></p>";
			echo "<p class='admin_name'><label class='opt'>".con('user_surname_end')."</label>
					<select name='endLetter'>
					<option value='0'>Choose a letter</option>";
			$this->showFormOptions($alphaEnd);
			echo "</select></p>";

		echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_surnameAZ' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

		echo "</form>"; // *** End of Form *** \\
		echo "			</div>
					</div>\n";
	echo "</div>";
	// *** End of Drop-down Slide COMBINE block *** \\

	// *** Start of Drop-down Slide block *** \\
	echo "<div class='dg_header'>".con(search_title_combine)."</div>
			<div class='dg_body'>
				<div>\n";

		echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>"; // *** Start of Form *** \\
		echo "<input type='hidden' name='action' value='global_search'/>\n";  //Form value = global_search

		echo "<h1>".con('search_title_global_posMerge')."</h1>";
		echo "<p>".con('search_notes_global_posMerge')."</p>";
		echo "<p class='admin_name'><label class='opt'>".con('posmerge_email')."</label><input type='checkbox' value='selected' name='posMerge_email'>";
		echo "</p>";


		echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_posUser' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

		echo "</form>"; // *** End of Form *** \\
		echo "			</div>
					</div>\n";
	echo "</div>";
	// *** End of Drop-down Slide block *** \\

		// *** Start of Drop-down Slide ACTIVITY block *** \\
	echo "<div class='dg_header'>".con(search_title_activity)."</div>
			<div class='dg_body'>
				<div>\n";

		echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>"; // *** Start of Form *** \\
		echo "<input type='hidden' name='action' value='global_search'/>\n";  //Form value = global_search

	//**** Customer Acitivity/Status Search ****\\
    echo "<h1>".(con('global_search_title_activity'))."</h1>";
	echo "<p>".con('global_search_notes_activity')."</p>";
	echo "<p class='admin_name'><label class='opt'>".con('user_active')."</label>
		<select name='user_active'>
		    <option value='0'>".con('N/A')."</option>
            <option value='1'>".con('Yes')."</option>
		    <option value='2'>".con('No')."</option>
		</select></p>";
	echo "<p class='admin_name'><label class='opt'>".con('user_ticket_status')."</label>
		<select name='user_ticket_status'>
			<option value='0'>".con('N/A')."</option>
			<option value='1'>".con('Yes')."</option>
			<option value='2'>".con('No')."</option>
		</select></p>";
	//get class into the page
	require_once('../includes/plugins/advancedsearch/calendar/classes/tc_calendar.php');
	echo "<script language='javascript' src='../includes/plugins/advancedsearch/calendar/calendar.js'></script>";
	echo "<link href='../includes/plugins/advancedsearch/calendar/calendar.css' rel='stylesheet' type='text/css'>";

	//instantiate class and set properties
	$date_now=date('Y-m-d');
	$myCalendar = new tc_calendar("date1", true);
	$myCalendar->setIcon("../includes/plugins/advancedsearch/calendar/images/iconCalendar.gif");
	//$myCalendar->setDate(01, 03, 2001);
	$myCalendar->setPath("../includes/plugins/advancedsearch/calendar/");
	$myCalendar->setYearInterval(2000, 2015);
	$myCalendar->dateAllow('', $date_now);
	$myCalendar->setAlignment('oleft', 'otop');
	//$myCalendar->setOnChange("myChanged('test')");
	//output the calendar

	echo "<p class='admin_name'><label class='opt'>".con('user_login_last')."</label></p>";

	$myCalendar->writeScript();


		echo "<p class='form_buttons'>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_activity' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

		echo "<hr>";

		//**** Customer Tickets Between Search ****\\
		echo "<h1>".(con('global_search_title_ticketsBetween'))."</h1>";
		echo "<p>".con('global_search_notes_ticketsBetween')."</p>";

		 echo "<p class='admin_name'><label>".con('user_tickets_between')."</label></p>";


		  $date3_default =date('Y-m-d');
      $date4_default = date('Y-m-d');
	$myCalendar = new tc_calendar("date3", true, false);
	  $myCalendar->setIcon("../includes/plugins/advancedsearch/calendar/images/iconCalendar.gif");
	  $myCalendar->setDate(date('d', strtotime($date3_default))
            , date('m', strtotime($date3_default))
            , date('Y', strtotime($date3_default)));
	  $myCalendar->setPath("../includes/plugins/advancedsearch/calendar/");
	  $myCalendar->setYearInterval(2000, 2020);
	  $myCalendar->setAlignment('left', 'top');
	  $myCalendar->setDatePair('date3', 'date4', $date4_default);
	  $myCalendar->writeScript();

	  $myCalendar = new tc_calendar("date4", true, false);
	  $myCalendar->setIcon("../includes/plugins/advancedsearch/calendar/images/iconCalendar.gif");
	  $myCalendar->setDate(date('d', strtotime($date4_default))
           , date('m', strtotime($date4_default))
           , date('Y', strtotime($date4_default)));
	//$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  $myCalendar->setPath("../includes/plugins/advancedsearch/calendar/");
	  $myCalendar->setYearInterval(2000, 2020);
	  $myCalendar->dateAllow('', $date_now);
	  $myCalendar->setAlignment('left', 'top');
	  $myCalendar->setDatePair('date3', 'date4', $date3_default);

	  $myCalendar->writeScript();



		echo "<p class='form_buttons'>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_ticketsBetween' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

		echo "</form>"; // *** End of Form *** \\
		echo "			</div>
					</div>\n";
	echo "</div>";
	// *** End of Drop-down Slide block *** \\

		// *** Start of Drop-down Slide ADMIN block *** \\
	echo "<div class='dg_header'>".con(search_title_admin)."</div>
			<div class='dg_body'>
				<div>\n";


		echo "<p> Currently not in use - reserved for future functions.</p>";
		/*
		echo "<form method='POST' action='{$_SERVER['PHP_SELF']}'>"; // *** Start of Form *** \\
		echo "<input type='hidden' name='action' value='global_search'/>\n";  //Form value = global_search



		echo "<p>
			<button id='search' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Search' style='' name='submit' value='search_surnameAZ' type='submit'>".con(btn_search)."</button>
			<button id='res' class=' admin-button ui-state-default ui-corner-all link admin-button-text ' alt='Reset' style='' name='res' type='reset'>".con(btn_reset)."</button>
			</p>";

		echo "</form>"; // *** End of Form *** \\
		*/
		echo "			</div>
					</div>\n";
	echo "</div>";
	// *** End of Drop-down Slide block *** \\


	}


  function doSearchView_Items($items){
      $items[$this->plugin_acl] ='Advanced Search/Edit|admin';
      return $items;
    }

  function doSearchView_Draw($id, $view){
    global $_SHOP;
    // this section works the same way as the draw() function insite the views it self.


	 If (file_exists("../includes/plugins/advancedsearch/lang/site_". $_SHOP->langs.".inc")){ //Currently the $_SHOP->langs is stuck on 'en'
			include_once("advancedsearch/lang/site_". $_SHOP->langs.".inc");
		  }else {
			include_once("advancedsearch/lang/site_en.inc");
		  }

	?>
		<link rel="stylesheet" type="text/css" href="../includes/plugins/advancedsearch/style.css" type="text/css">
		<script type="text/javascript" src="../includes/plugins/advancedsearch/scripts/scripts.js"></script>
	<?php

		if ($_POST['action']=='global_search') { // Test: no action so carry on!
			$this->searchForm_R($_POST, $view);
			}

		elseif($_GET['u']=='1'){
		$_SESSION['currentpage'] = $_GET['currentpage'];
		if ($this->searchForm_R($_POST, $view)) return;
		}

		elseif($_POST['update']=='page'){
		$_SESSION['npp'] = $_POST['npp'];
		if ($this->searchForm_R($_POST, $view)) return;
		}

		elseif($_POST['action']=='global_editData'){
		//$_SESSION['npp'] = $_POST['npp'];
		if ($this->global_editClean($_POST, $view)) return;
		}

		elseif($_REQUEST['action']=='global_update'){
		if ($this->global_updateR($_POST, $view)) return;
		}

		elseif($_REQUEST['action']=='global_update_global'){
		if ($this->global_updateR($_POST, $view)) return;
		}

		elseif($_REQUEST['action']=='delete_patron'){
		if ($this->deleteTable($_POST, $view)) return;
		}

		elseif($_REQUEST['action']=='global_editPos'){
		if ($this->global_posR($_POST, $view)) return;
		}

		else {
		$this->searchForm($_POST, $view);
		}


    if ($id==$this->plugin_acl) {
      // do your tasks here.  // Is this part extending





    }
  }












}
?>