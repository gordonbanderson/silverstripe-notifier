#!/bin/sh

# This script is an EXAMPLE ONLY. You must copy this into your system's script
# execution framework (init.d, service etc) and run it as a daemon AFTER
# editing the relevant paths for your system roots.

SILVERSTRIPE_ROOT=/var/www
SILVERSTRIPE_CACHE=/var/www/silverstripe-cache/flossydock

inotifywait --daemon --monitor --event attrib --format "php $SILVERSTRIPE_ROOT/vendor/bin/sake dev/tasks/ProcessJobQueueTask job=%f" $SILVERSTRIPE_CACHE/queuedjobs | sh
#inotifywait -m -r /var/www/ --timefmt %d-%m-%Y --format '%T%w%f%e' >> /srv/log.txt
