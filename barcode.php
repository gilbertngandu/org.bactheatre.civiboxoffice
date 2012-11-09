<?php

require_once('Barcode39.php');

if (isset($_GET['barcode'])) {
  $bc = new Barcode39($_GET['barcode']);
}
else {
  return;
} 

// set text size
$bc->barcode_text_size = 6;

// set barcode bar thickness (thick bars)
$bc->barcode_bar_thick = 4;

// set barcode bar thickness (thin bars)
$bc->barcode_bar_thin = 2;

$bc->draw();

?>
