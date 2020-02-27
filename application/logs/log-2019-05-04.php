<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2019-05-04 17:00:02 --> Severity: Warning --> Creating default object from empty value /var/www/vhosts/cloudtest.versipos.com/httpdocs/application/models/Clients_model.php 41
ERROR - 2019-05-04 17:00:03 --> Query error: Column 'portfolio_id' cannot be null - Invalid query: INSERT INTO `tblactivitylog` (`description`, `date`, `portfolio_id`, `staffid`) VALUES ('Email Send To [Email: admin@demo.com, Template: Task Deadline Reminder - Sent to Assigned Members]', '2019-05-04 17:00:03', NULL, '[CRON]')
ERROR - 2019-05-04 17:00:03 --> Query error: Unknown column 'daily_notofied_date' in 'field list' - Invalid query: UPDATE `tblstafftasks` SET `deadline_notified` = 1, `daily_notofied_date` = '2019-05-04'
WHERE `id` = '279'
ERROR - 2019-05-04 17:00:04 --> Query error: Column 'portfolio_id' cannot be null - Invalid query: INSERT INTO `tblactivitylog` (`description`, `date`, `portfolio_id`, `staffid`) VALUES ('Email Send To [Email: admin@demo.com, Template: Task Deadline Reminder - Sent to Assigned Members]', '2019-05-04 17:00:04', NULL, '[CRON]')
ERROR - 2019-05-04 17:00:04 --> Query error: Unknown column 'daily_notofied_date' in 'field list' - Invalid query: UPDATE `tblstafftasks` SET `deadline_notified` = 1, `daily_notofied_date` = '2019-05-04'
WHERE `id` = '280'
