<?php

/**
 *
 *
 * @version $Id: json.php 1785 2012-05-12 07:10:13Z nielsNL $
 * @copyright 2011
 */
  define('ft_check','admin');
  require_once('../includes/classes/class.router.php');
  if (isset($_POST['page'])) {
    $page = $_POST['page'];
  } elseif (isset($_GET['page'])) {
    $page = $_GET['page'];
  }
  router::draw($page, 'admin/json');

?>