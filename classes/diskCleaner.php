<?php

/*
 * Class to reduce disk space used if less than minimum allowed is free.
 *  
 */
require_once $_SESSION['root_dir'] . '/classes/dataPath.php';
require_once $_SESSION['root_dir'] . '/classes/sanityCheck.php';
require_once $_SESSION['root_dir'] . '/classes/dbMotion.php';

/**
 * Description of diskCleaner
 *
 * @author nigel
 */
class diskCleaner {

    /**
     * @const String Query to select all event time stamps for oldest day. 
     * 
     *              The sub-query selects the day part of the date of the 
     *              oldest event to get the oldest day.
     *              This is then used to select all events on that day.
     *              These are then concatenated into a comma-separated string.
     */
    const QUERY_OLDEST_DAY_IDS = 'select group_concat(DISTINCT `event_time_stamp`+0 SEPARATOR \', \')
                                    FROM security.security
                                    WHERE `event_time_stamp` LIKE
                                            (SELECT concat(SUBSTRING(`event_time_stamp`, 1, 10), "%")
                                            FROM security.security
                                            ORDER BY `event_time_stamp`
                                            LIMIT 1)
                                    ORDER BY `event_time_stamp`';
    
    /**
     * @const string Query to get file paths given comma-separated list of record IDs (timestamps) 
     * 
     * Use sprintf to replace %s with list of IDs.
     */
    const QUERY_FILENAMES_FROM_IDS = 'SELECT `filename` FROM `security` WHERE `event_time_stamp` IN (%s)' ;
    
    /**
     * @const string Query to delete a record from the database given the records full disk file's path.
     * 
     * Use sprintf to replace %s with the file path.
     */
    const QUERY_DELETE_RECORD_BY_FILENAME = "DELETE FROM security WHERE filename='%s'" ;


    /**
     * @var dataPath Class encapsulating information about data store.
     */
    private $fileStoreInfo = null;

    public function __construct() {
        if ($this->isTooFull()) {
            // First do sanity check.
            $checker = new sanityCheck();
            if ($checker->hasErrors()) {
                $checker->doRepairs();
//                throw new RuntimeException('Errors found checking disk prior to attempting to free space. Reload to try again.') ;
            }

            // Now delete files and records.
            $this->releaseSpace();
        }
    }

    /**
     * 
     * @return dataPath Class encapsulating information about data store. Null on failure.
     */
    private function getFileStoreInfo(): dataPath {
        if (is_null($this->fileStoreInfo)) {
            $this->fileStoreInfo = new dataPath();
        }

        return ($this->fileStoreInfo);
    }

    /**
     * @return bool True if space has to be freed up so that the disk free space is more than the minimum allowed.
     */
    private function isTooFull(): bool {
        $minFreeAlowed = $_SESSION['disk']['freeSpaceBuffer'] + 0;
        $freeOnDisk = $this->getFileStoreInfo()->getFreePercent();

        return ($freeOnDisk < $minFreeAlowed) ? true : false;
    }

    /**
     * Free up space until there is more free space on disk than the minimum allowed.
     * Files are deleted from disk and matching SQL records removed.
     * The removal is done oldest files first.
     */
    private function releaseSpace(): null {
        while ($this->isTooFull()) {
            if (!$this->deleteOldestDay()) {
                $msg = 'Disk free space less than minimum specified with no files available to delete to free space.';
                echo $msg;
                throw new RuntimeException($msg);
            }
        }
    }

    /**
     * Delete the files and records for the oldest day.
     * @return bool True if files deleted, false if no files to delete.
     */
    private function deleteOldestDay(): bool {
        $timestamps = $this->getOldestDayTimestamps();
        $this->deleteOldestDayItems($timestamps);
    }

    /**
     * Delete files and database entries. 
     * @param {string}  IDs of items to delete. (Comma-separated list.)
     * @return	{void}	nothing
     */
    public function deleteOldestDayItems($timestamps) {
        // TODO: add code to allow testing mode, rather than just acting.
        $db = new dbMotion() ;
        
        $query = sprintf(self::QUERY_FILENAMES_FROM_IDS, $timestamps) ;
        $result = $db->query($query) ;

        for ($i = 0; $i < $result->num_rows; $i++) {
            $row = $result->fetch_array();
            $filename = $row['filename'];
            
            // delete the file first
            if (!unlink($filename)) {
                    die(sprintf(gettext("Error deleting %s"), $filename)) ;
            }

            // if no problem, delete the record from the database
            $query = sprintf (self::QUERY_DELETE_RECORD_BY_FILENAME, $filename) ;
            $db->query($query) ;
        }
    }

    /**
     * Get the timestamps of the items on the oldest day. 
     * 
     * @return string Comma-separated list of timestamps, or empty string if none.
     */
    public function getOldestDayTimestamps(): string {
        $db = new dbMotion();

        $result = $db->query(diskCleaner::QUERY_OLDEST_DAY_IDS);

        if ($result->num_rows === 0) {
            return ("");
        }

        $timestamps = $result->fetch_array(MYSQLI_NUM) ;
        
        if (is_array($timestamps)) {
            $timestamps = implode(', ', $timestamps);
        }
        
        // trim any trailing comma
        $timestamps = rtrim($timestamps, ', ');



        return ($timestamps) ;
    }

}
