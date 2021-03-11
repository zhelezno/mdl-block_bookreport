<?php
  
require_once(__DIR__ . '/../../../../config.php'); 

global $DB;

print_r($DB->count_records('block_bookreport', NULL)); 
