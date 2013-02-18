For some large Drupal sites, we do not need backup the hold site, but just the
content inside an organic group. This module help you to do that.

### Backup

1. Queue all content ID in the OG.
1. Process queued item, one by one, export to csv file.
1. Upto the configuration, we will:
    1. Compress the backed up content to a tar ball.
    1. Or commit the changes to git repository.

Drush Command:

    drush dumper_backup %nid
    drush dumper_backup %nid --destination=git --git-url=git@github.com/…

### Restore

1. Depend on excellent migrate.module to restore dumped content.
1. …

Drush Command:

    drush dumper_restore %nid %token
