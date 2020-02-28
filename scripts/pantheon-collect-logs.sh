#!/bin/bash

# Find your site UUID from your Pantheon Dashboard URL.
# For example, 12345678-1234-1234-abcd-0123456789ab is the site UUID for 
# https://dashboard.pantheon.io/sites/12345678-1234-1234-abcd-0123456789ab

# Add that site UUID to line 10 (see line 9 as an example).

# SITE_UUID=12345678-1234-1234-abcd-0123456789ab
SITE_UUID=d29edec2-eb9a-40e2-8519-3afe3b32c0d7
ENV=live
for app_server in `dig +short appserver.$ENV.$SITE_UUID.drush.in`;
do
  rsync -rlvz --size-only --ipv4 --progress -e 'ssh -p 2222' $ENV.$SITE_UUID@appserver.$ENV.$SITE_UUID.drush.in:logs/nginx-access* ./logs
done

# Unpack archived logs
gunzip ./logs/nginx-access.log-*