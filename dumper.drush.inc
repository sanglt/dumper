<?php
/**
 * @file dumper.drush.inc
 *
 * Drush integration.
 */

/**
 * Implements hook_drush_command().
 */
function dumper_drush_command() {
  $items['dumper_backup'] = array(
    'description' => 'Backup contents in an organic group',
    'callback' => 'dumper_drush_command_backup',
    'arguments' => array('nid' => 'Node ID of organic group'),
    'examples' => array(
      'drush dumper_backup 3345' => 'Backup content for organic group #3345.',
    ),
  );

  $items['dumper_restore'] = array(
    'description' => 'Restore the backup',
    'callback' => 'dumper_drush_command_restore',
    'arguments' => array(
      'nid'  => 'Node ID of organic group',
      'date' => 'Date when the organic group content is backed up.',
    ),
    'examples' => array(
      'drush dump_restore 3345 2013-01-15' => 'Restore content for organic group #3345 from the backup image created on 2013 January 15th.',
    ),
  );

  return $items;
}

/**
 * Implement drush_hook_COMMAND_validate().
 */
function drush_dumper_backup_validate($args = NULL) {
  drush_print_r($args);
}

/**
 * Callback for drush command drumper_backup
 *
 * @param int $nid
 * @return boolean
 */
function dumper_drush_command_backup($nid) {
  if (!$nid) {
    drush_set_error('Missing nid');
    return FALSE;
  }

  try {
    $dumper = new Dumper_Controller_Og($node = node_load($nid));
    $dumper->queue();
    $dumper->process();
  }
  catch (Exception $e) {
    drush_set_error($e->getMessage());
    return FALSE;
  }
}

/**
 * Callback for drush command drumper_restore.
 *
 * @param int $nid
 * @param string $date Format Y-m-d
 * @return boolean
 */
function dumper_drush_command_restore($nid, $date) {
  if (!$nid) {
    drush_set_error('Missing nid');
    return FALSE;
  }

  if (!$date) {
    drush_set_error('Missing date');
    return FALSE;
  }

  $restorer = new Restorer_Controller_Og($node = node_load($nid));
  $restorer->restore();
}
