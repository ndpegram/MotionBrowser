<?php

/**
 * Class to check that each file has an SQL record, and vice versa. 
 * 
 * Report if not.
 *
 * @author nigel
 */
if (!isset($_SESSION)) {
    session_start();
}

require_once $_SESSION['root_dir'] . '/lang.inc' ;
require_once $_SESSION['root_dir'] . '/classes/dbMotion.php';



class sanityCheck 
{
    private const ALL_SQL_RECORDS = 'select `filename` from `security` where 1' ;
    /** @var dbMotion Database for querying motion records. */
    private $db = null ;
    /** @var string[] Associative array of file names with path from the database. The key is the file name without the path. */
    private $dbPaths;
    /** @var string[] Array of file names sans path from the disk */
    private $diskFiles;

    public function sanityCheck() {
//        $this->setDatabase() ;
        $this->getAllDB() ;
        $this->getAllFiles() ;
        $this->compare() ;
    }

    private function getDatabase() 
    {
        if (is_null($this->db)){
            $this->db = new dbMotion() ;               
        }
        return ($this->db) ;
    }

    private function getAllDB() 
    {
        $db = $this->getDatabase() ;
        
        $result = $db->query(self::ALL_SQL_RECORDS) ;
        
        while ($row = $result->fetch_assoc()) {
            $this->addDBPath($row['filename']) ;
        }
    }

    private function getAllFiles() 
    {
        $path = $this->getFilePath() ;
        $iterator = new FilesystemIterator($path) ;
        
        foreach ($iterator as $file) {
            $this->addDiskFile($file->getFilename()) ;
        }
    }

    /**
     * Compare disk and SQL records for mismatches.
     * @return bool Returns false if no differences, true if mismatches found.
     */
    private function compare() 
    {
        $bRC = false ;
        $diskFilesIterator = new ArrayIterator($this->getDiskFiles()) ;
        $dbPaths = $this->getDbPaths() ;
        $notFound = [];
        
        foreach ($diskFilesIterator as $filename){
            if (key_exists($filename, $dbPaths)){
                unset($dbPaths[$filename]) ;                
            }
            else {
                $notFound[] = $filename ;
            }
        }
        
        if (sizeof($notFound) > 0) {
            $bRC = true ;
            printf ('The following files were not found in the database:<br />%s', implode(',<br />', $notFound)) ;
        }
        
        if (sizeof($dbPaths) > 0) {
            $bRC = true ;
            printf('The following database entries did not have files on disk:<br />%s', implode(',<br /> ', $dbPaths)) ;
        }
        
        return ($bRC) ;
    }

    private function addDBPath($path) 
    {
        $fileInfo = new SplFileInfo($path) ;
        $this->dbPaths[$fileInfo->getFilename()] = $path ;
    }

    private function getFilePath() {
        $files = $this->getDbPaths() ;
        $fileInfo = new SplFileInfo(reset($files)) ;
        $path = $fileInfo->getPath() ;
        return ($path) ;
    }

    private function getDbPaths() {
        return ($this->dbPaths) ;
    }

    private function getDiskFiles() {
        return ($this->diskFiles) ;
    }

    private function addDiskFile($file) {
        $this->diskFiles[] = $file ;
    }

}

//$checker = new sanityCheck() ;