<?php

/**
 * Class to check that each file has an SQL record, and vice versa. 
 * 
 * Report if not.
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/lang.inc';
require_once $_SESSION['root_dir'] . '/classes/dbMotion.php';

class sanityCheck {

    /** Constant used in constructor. */
    private const CHECK_ONLY = 0;

    /** Constant used in constructor. */
    private const CHECK_AND_REPAIR = 1;

    /** SQL query to list all records. */
    private const ALL_SQL_RECORDS = 'select `filename` from `security` where 1';

    /** SQL query to delete records without matching file. The parameter is for use with sprintf to insert file paths. */
    private const DELETE_ORPHAN_SQL = 'delete from `security` where `filename` in (%s) ';

    /** Check result value mask */
    private const ERRORS_NONE = 0;

    /** Check result value mask */
    private const ERRORS_FILES = 2 ^ 0;

    /** Check result value mask */
    private const ERRORS_SQL = 2 ^ 1;

    /** @var int Member variable containing results of sanity check. */
    private $errors = self::ERRORS_NONE;

    /** @var string[] Array of files names listed in SQL database but not on disk. */
    private $dbPathsNotFound;

    /** @var string[] Array of files on disk not found in the database. */
    private $filesNotInDatabase;

    /** @var dbMotion Database for querying motion records. */
    private $db = null;

    /** @var string[] Associative array of file names with path from the database. The key is the file name without the path. */
    private $dbPaths;

    /** @var string[] Array of file names sans path from the disk */
    private $diskFiles;

    public function sanityCheck($repair = self::CHECK_ONLY) {
        $this->getAllDB();
        $this->getAllFiles();
        $this->compare() ;

        if ($repair === self::CHECK_AND_REPAIR) {
            if ($this->errors && self::ERRORS_FILES) {
                $this->fixFiles();
            }

            if ($this->errors && self::ERRORS_SQL) {
                $this->fixSQL();
            }
        }
    }

    private function getDatabase() {
        if (is_null($this->db)) {
            $this->db = new dbMotion();
        }
        return ($this->db);
    }

    private function getAllDB() {
        $db = $this->getDatabase();

        $result = $db->query(self::ALL_SQL_RECORDS);

        while ($row = $result->fetch_assoc()) {
            $this->addDBPath($row['filename']);
        }
    }

    private function getAllFiles() {
        $path = $this->getFilePath();
        $iterator = new FilesystemIterator($path);

        foreach ($iterator as $file) {
            $this->addDiskFile($file->getFilename());
        }
    }

    /**
     * Compare disk and SQL records for mismatches.
     * @return bool Returns false if no differences, true if mismatches found.
     */
    private function compare() {
        //TODO *** replace error array direct access with getters and setters. ***
        $diskFilesIterator = new ArrayIterator($this->getDiskFiles());
        $this->dbPathsNotFound = $this->getDbPaths();
        $this->filesNotInDatabase = [];

        foreach ($diskFilesIterator as $filename) {
            if (key_exists($filename, $this->dbPathsNotFound)) {
                unset($this->dbPathsNotFound[$filename]);
            } else {
                $this->filesNotInDatabase[] = $filename;
            }
        }

        if (sizeof($this->filesNotInDatabase) > 0) {
            $this->errors |= self::ERRORS_FILES;
            printf('The following files were not found in the database:<br />%s', implode(',<br />', $this->filesNotInDatabase));
        }

        if (sizeof($this->dbPathsNotFound) > 0) {
            $this->errors |= self::ERRORS_SQL;
            printf('The following database entries did not have files on disk:<br />%s', implode(',<br /> ', $this->dbPathsNotFound));
        }
    }

    private function addDBPath($path) {
        $fileInfo = new SplFileInfo($path);
        $this->dbPaths[$fileInfo->getFilename()] = $path;
    }

    private function getFilePath() {
        $files = $this->getDbPaths();
        $fileInfo = new SplFileInfo(reset($files));
        $path = $fileInfo->getPath();
        return ($path);
    }

    private function getDbPaths() {
        return ($this->dbPaths);
    }

    private function getDiskFiles() {
        return ($this->diskFiles);
    }

    private function addDiskFile($file) {
        $this->diskFiles[] = $file;
    }

    public function fixSQL() {
        $query = sprintf (self::DELETE_ORPHAN_SQL, implode(', ', $this->dbPathsNotFound)) ;
        $this->getDatabase()->query($query) ;
    }

    public function fixFiles() {
        foreach ($this->filesNotInDatabase as $filename) {
            unlink($filename) ;
        }
    }

}
