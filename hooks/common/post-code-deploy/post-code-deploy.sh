#!/bin/sh
# Map the script inputs to convenient names.
site=$1
target_env=$2
drush_alias=$site'.'$target_env

# Import configurations.
drush @$drush_alias updb -y
# Import configurations.
drush @$drush_alias entup -y
# Import configurations.
drush @$drush_alias config-import sync -y
# Clear the cache.
drush @$drush_alias cache-rebuild
