##Description
 A shell application to create SQL databases dump backups.

 This script is based upon the one written by David Walsh and published on his blog on August 18, 2008
 http://davidwalsh.name/backup-mysql-database-php

 Many thanks to him!

##Instructions
1. Download the repo and put it in your app/ folder.
2. Open up your CakePHP shell and run the command "cake backup" (You can use cron jobs)

 - This script backup all of your tables by default but you can select specific tables by 
   uncommenting $tables = array('orders', 'users', 'profiles'); and filling your own table names.

 Notice: This application uses ProgressBar Task written by Matt Curry. If you want to use it should be in 
 vendors/shells/tasks directory otherwise please comment the lines which contains:
        $this->ProgressBar->start($num_fields);
        $this->ProgressBar->next();
 I don't know why you wanna do that! That progress bar is awesome! Thanks Matt!
 
##Arguments
 1. Database configuration, default is "default"
 2. Rows per query (less rows = less ram usage but more running time) default is 0 which means all rows
 3. Absolute path for the directory to save your backup, it will be created automatically if not found, default is webroot/db-backups/yyyy-mm-dd

#Additional possible features
 1. Upload backup using FTP.
