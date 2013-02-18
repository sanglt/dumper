<?php
/**
 * @TODO Save entity path if supported.
 */

$school = og_load($school_id = 123);
$dumper = new Dumper_Controller_Og($school);

// To dump a school/oganic group, first we will queue the mappings first
$dumper->queue();

// After queue the mappings, we really process the queue
if ($dumper->process()) {
  // Processed
}
else {
  // Processed fails
}
