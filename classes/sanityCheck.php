<?php

/**
 * Class to check that each file has an SQL record, and vice versa. 
 * 
 * Can fix found errors.
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/lang.inc';
require_once $_SESSION['root_dir'] . '/classes/dbMotion.php';

class sanityCheck {

    /** Constant used in constructor. */
    public const CHECK_ONLY = 0;

    /** Constant used in constructor. */
    public const CHECK_AND_REPAIR = 1;

    /** SQL query to list all records. */
    private const ALL_SQL_RECORDS = 'select `filename` from `security` where 1';

    /** SQL query to delete records without matching file. The parameter is for use with sprintf to insert file paths. */
    private const DELETE_ORPHAN_SQL = 'select count(*) from `security` where `filename` in ("%s") ';

    /** Check result value mask */
    public const ERRORS_NONE = 0;

    /** Check result value mask */
    public const ERRORS_FILES = 2 ^ 0;

    /** Check result value mask */
    public const ERRORS_SQL = 2 ^ 1;

    /** @var int Member variable containing results of check. Use the above constant masks to check results. */
    public $errors = self::ERRORS_NONE;

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

    /** @var string Path to directory where files are saved on disk. */
    private $filePath = null;

    /**
     * Constructor. 
     * 
     * Automatically checks if there are orphaned files on disk or orphaned SQL
     * records in the database.
     * 
     * @param int $repair Flag indicating whether to check only (default) or whether to check and repair.
     */
    public function sanityCheck($repair = self::CHECK_ONLY) {
        $this->getAllDB();
        $this->getAllFiles();
        $this->compare();

        if ($repair && $this->hasErrors()) {
            $this->doRepairs();
        }
    }

    /**
     * Repair any inconsistencies. Deleting files and/or deleting SQL records 
     * as necessary.
     */
    public function doRepairs() {
        if ($this->hasDiskErrors()) {
            $this->fixFiles();
        }

        if ($this->hasSQLErrors()) {
            $this->fixSQL();
        }
    }

    /**
     * Getter function. Gets the database object used to access SQL records.
     * 
     * @return dbMotion The database object to use to access SQL records.
     */
    private function getDatabase() {
        if (is_null($this->db)) {
            $this->db = new dbMotion();
        }
        return ($this->db);
    }

    /**
     * Get all file paths in SQL records. Add to the dbPaths string array member variable.
     */
    private function getAllDB() {
        $db = $this->getDatabase();

        $result = $db->query(self::ALL_SQL_RECORDS);

        while ($row = $result->fetch_assoc()) {
            $this->addDBPath($row['filename']);
        }
    }

    /**
     * Get all files in on disk in the directory used to store images and movies.
     * File names are added to the diskFiles member variable.
     */
    private function getAllFiles() {
        $path = $this->getFilePath();
        $iterator = new FilesystemIterator($path);

        foreach ($iterator as $file) {
            $this->addDiskFile($file->getFilename());
        }

        /* Will be null if no files on disk. */
        if (is_null($this->diskFiles)) {
            $this->diskFiles = [];
        }
    }

    /**
     * Compare disk and SQL records for mismatches.
     * 
     * Sets the errors member variable as appropriate.
     * If SQL errors found then the ERRORS_SQL mask is applied.
     * If disk errors are found then the ERRORS_FILES mask is applied.
     */
    private function compare() {
        //TODO *** replace error array direct access with getters and setters. ***
        //TODO replace printf with output to log file. Potentially use file which can be displayed in web interface.
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
            printf('<H3>The following %d files were not found in the database:</H3><p>%s</p>', sizeof($this->filesNotInDatabase), implode(',<br />', $this->filesNotInDatabase));
        }

        if (sizeof($this->dbPathsNotFound) > 0) {
            $this->errors |= self::ERRORS_SQL;
            printf('<H3>The following %d database entries did not have files on disk:</H3><p>%s</p>', sizeof($this->dbPathsNotFound), implode(',<br /> ', $this->dbPathsNotFound));
        }
    }

    /**
     * Add a filename to the dbPaths array member variable. The filename is 
     * extracted from the path before being added.
     * @param type $path The path to a file as returned from the database.
     */
    private function addDBPath($path) {
        $fileInfo = new SplFileInfo($path);
        $this->dbPaths[$fileInfo->getFilename()] = $path;
    }

    /**
     * Get the path where files are saved to disk. 
     * @return String Path to location where files are stored on disk.
     */
    private function getFilePath() {
        if (is_null($this->filePath)) {
            $files = $this->getDbPaths();
            $fileInfo = new SplFileInfo(reset($files));
            $path = $fileInfo->getPath();
            $this->filePath = $path;
        }

        return ($this->filePath);
    }

    /**
     *  Get the string array containing SQL file paths.
     * 
     * @return string[] Paths to files on disk which have been extracted from the database.
     */
    private function getDbPaths() {
        return ($this->dbPaths);
    }

    /**
     *  Get the string array containing file names found on the disk.
     * 
     * @return string[] Names of files on disk .
     */
    private function getDiskFiles() {
        return ($this->diskFiles);
    }

    /**
     * Add a file name to the diskFiles array member variable.
     * @param string $file Name of file to add.
     */
    private function addDiskFile($file) {
        $this->diskFiles[] = $file;
    }

    /**
     * Delete records from the SQL database which do not have matching files on disk.
     */
    private function fixSQL() {
        $db = $this->getDatabase();
        $query = sprintf(self::DELETE_ORPHAN_SQL, implode('", "', $this->dbPathsNotFound));
        if ($db->query($query)) {
            printf('<h3>Fixed database</h3><p>%s</p>', $query);
        }
    }

    /**
     * Delete files on disk which do not have matching SQL records.
     */
    private function fixFiles() {
        $path = $this->getFilePath();

        printf('<h3>Deleting files</h3>');
        foreach ($this->filesNotInDatabase as $filename) {
            $filePath = $path . '/' . $filename;
            unlink($filePath);
            printf('<p>unlinked %s</p>', $filePath);
        }
    }

    /**
     * Do errors exist in the database?
     * 
     * @return bool True if errors exist in the database.
     */
    public function hasSQLErrors(){
        return ($this->errors && self::ERRORS_SQL);
    }
    
    /**
     * Do errors exist in the files?
     * 
     * @return bool True if errors exist in the files.
     */
    public function hasDiskErrors() {
        return ($this->errors && self::ERRORS_FILES) ;
    }
    
    /**
     * Were errors found?
     * 
     * @return bool True if errors exist.
     */
    public function hasErrors(){
        $bRc = $this->errors !== self::ERRORS_NONE ? true : false ;
        return ($bRc) ;
    }
}
