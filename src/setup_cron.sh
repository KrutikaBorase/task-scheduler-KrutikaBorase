#!/bin/bash

# Path to your PHP executable
PHP_PATH=$(which php)

# Absolute path to cron.php
SCRIPT_PATH=$(realpath "$(dirname "$0")/cron.php")

# Log file for cron output
LOG_FILE="$HOME/task_scheduler_cron.log"

# Cron job definition (runs every hour)
CRON_JOB="0 * * * * $PHP_PATH $SCRIPT_PATH >> $LOG_FILE 2>&1"

# Check if cron job already exists
(crontab -l 2>/dev/null | grep -v -F "$SCRIPT_PATH"; echo "$CRON_JOB") | crontab -

echo "✅ Cron job set up to run every hour. You can view logs in: $LOG_FILE"
