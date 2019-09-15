<?php

/**
 * Class encapsulating location where files are saved by motion.
 * The path is obtained by querying the database.
 * 
 * Includes functions to get free space.
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/classes/dbMotion.php';

class dataPath {
    /** Query used to get file storage information from database. */
    private const query = 'SELECT filename FROM security order by time_stamp desc limit 1' ;
    /** @var string Any path on which one wishes to find out disk info. */
    private $path = null ;
    /** @var float The size of the disk in bytes. */
    private $size = 0 ;
    /** @var float The free space available on the disk in bytes. */
    private $free = 0 ;
    
    function __construct () {
        $this->setPath() ;
        $this->setSize() ;
        $this->setFree() ;
    }
    
    function getPath() {
        return $this->path;
    }

    function getSize() {
        return $this->size;
    }

    private function setPath() {
        $this->path = $this->getPathFromDB() ;
    }

    private function setSize() {
        $this->size = disk_total_space($this->getPath()) ;
    }

    private function setFree() {
        $this->free = disk_free_space($this->getPath()) ;
    }

    function getFree() : float {
        return $this->free;
    }

    function getFreePercent(): int {
        $quotient = $this->getFree() / $this->getSize()  ;
        $percent = round($quotient * 100) ;
        return ($percent) ;
    }
    
    function getFreePercentString(): string {
        $szPercent = sprintf("%u%%", $this->getFreePercent()) ;
        return ($szPercent) ;
    }
    
    private function getPathFromDB() : string {
        /** @var database */
        $db = new dbMotion();
        $result = $db->query(self::query) ;

        $row = $result->fetch_assoc();
        $result->free() ;

        $path_part = pathinfo($row['filename']);
        return ($datadisque=$path_part['dirname']) ;
    }
}
