<?php
/*
 * CakePHP shell application to create SQL databases dump backups
 * Copyright (c) 2013 Mohammed Mahgoub
 * www.mmahgoub.com
 * https://bitbucket.org/mmahgoub/cakephp-backupme
 *
 * @author      Mohammed Mahgoub <mmahgoub@mmahgoub.com>
 * @license     MIT
 *
 */

/**
 * BackupShell class
 *
 * @uses          Shell
 * @package       cakephp-backupme
 * @subpackage    cakephp-backupme.vendors.shells
 */
class BackupShell extends Shell {
    
    var $tasks = array('ProgressBar');

    public function main() {
        //database configuration, default is "default"
        if(!isset($this->args[0])){
            $this->args[0] = 'default';
        }
        
        //rows per query (less rows = less ram usage but more running time), default is 0 which means all rows
        if(!isset($this->args[1])){
            $this->args[1] = 0;
        }
        
        //directory to save your backup, it will be created automatically if not found., default is webroot/db-backups/yyyy-mm-dd
        if(!isset($this->args[2])){
            $this->args[2] = 'db-backups/'.date('Y-m-d',time());
        }

        App::import('Core', 'ConnectionManager');
        $db = ConnectionManager::getDataSource($this->args[0]);
        $backupdir = $this->args[2];
        $seleced_tables = '*';
        //$tables = array('orders', 'users', 'profiles');

        if ($seleced_tables == '*') {
            $sources = $db->query("show full tables where Table_Type = 'BASE TABLE'", false);
            foreach($sources as $table){
                $table = array_shift($table);
                $tables[] = array_shift($table);
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
        
        $filename = 'db-backup-' . date('Y-m-d-H-i-s',time()) .'_' . (md5(implode(',', $tables))) . '.sql';
        
        $return = '';
        $limit = $this->args[1];
        $start = 0;
        
        if(!is_dir($backupdir)) {
            $this->out(' ', 1);
            $this->out('Will create "'.$backupdir.'" directory!', 2);
            if(mkdir($backupdir,0755,true)){
                $this->out('Directory created!', 2);
            }else{
                $this->out('Failed to create destination directory! Can not proceed with the backup!', 2);
                die;
            }
        }
            
        if ($this->__isDbConnected($this->args[0])) {
            
            $this->out('---------------------------------------------------------------');
            $this->out(' Starting Backup..');
            $this->out('---------------------------------------------------------------');

            foreach ($tables as $table) {
                $this->out(" ",2);
                $this->out($table);
                
                $handle = fopen($backupdir.'/'.$filename, 'a+');
                $return= 'DROP TABLE IF EXISTS `' . $table . '`;';
                    
                    $row2 = $db->query('SHOW CREATE TABLE ' . $table.';');
                    //$this->out($row2);
                    $return.= "\n\n" . $row2[0][0]['Create Table'] . ";\n\n";
                    fwrite($handle, $return);
                    
                for(;;){
                    if($limit == 0){
                        $limitation = '';
                    }else{
                        $limitation = ' Limit '.$start.', '.$limit;
                    }
                    
                    $result = $db->query('SELECT * FROM ' . $table.$limitation.';', false);
                    $num_fields = count($result);
                    $this->ProgressBar->start($num_fields);
                    
                    if($num_fields == 0){
                        $start = 0;
                        break;
                    }

                    foreach ($result as $row) {
                        $this->ProgressBar->next();
                        $return2 = 'INSERT INTO ' . $table . ' VALUES(';
                        $j = 0;
                        foreach ($row[$table] as $key => $inner) {
                            $j++;
                            if(isset($inner)){
                                if ($inner == NULL){
                                    $return2 .= 'NULL';
                                }else{
                                    $inner = addslashes($inner);
                                    $inner = ereg_replace("\n", "\\n", $inner);
                                    $return2.= '"' . $inner . '"';
                                }
                            }else {
                                $return2.= '""';
                            }

                            if ($j < (count($row[$table]))) {
                                $return2.= ',';
                            }
                        }
                        $return2.= ");\n";
                        fwrite($handle, $return2);
                        
                    }
                    $start+=$limit;
                    if($limit == 0){
                        break;
                    }
                }
                
                $return.="\n\n\n";
                fclose($handle);
            }

            $this->out(" ",2);
            $this->out('---------------------------------------------------------------');
            $this->out(' Yay! Backup Completed!');
            $this->out('---------------------------------------------------------------');
            
        }else{
            $this->out(' ', 2);
            $this->out('Error! Can\'t connect to "'.$this->args[0].'" database!', 2);
        }
    }

    function __isDbConnected($db = NULL) {
        $datasource = ConnectionManager::getDataSource($db);
        return $datasource->isConnected();
    }

}
?>