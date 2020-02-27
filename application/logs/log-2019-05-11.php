<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2019-05-11 15:20:52 --> 404 Page Not Found: Faviconico/index
ERROR - 2019-05-11 17:00:04 --> Query error: Column 'portfolio_id' cannot be null - Invalid query: INSERT INTO `tblactivitylog` (`description`, `date`, `portfolio_id`, `staffid`) VALUES ('Email Send To [Email: admin@demo.com, Template: Task Deadline Reminder - Sent to Assigned Members]', '2019-05-11 17:00:04', NULL, '[CRON]')
ERROR - 2019-05-11 17:00:04 --> Query error: Unknown column 'daily_notofied_date' in 'field list' - Invalid query: UPDATE `tblstafftasks` SET `deadline_notified` = 1, `daily_notofied_date` = '2019-05-11'
WHERE `id` = '280'
