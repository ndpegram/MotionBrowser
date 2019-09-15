<?php

/**
 * Object representing a motion video event. Essentially contains the data from a MySQL record.
 * At present, text_event is not used, but is included here for future use.
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/libs/getid3/getid3.php';
require_once $_SESSION['root_dir'] . '/classes/eventFileInfo.php';

class event {
    private const QUERY_SAVE_FILE_SIZE = "update security set file_size='%s' where filename ='%s';";
    // Filetypes. See motion.h in the motion sources.
    private CONST IMAGE                 = 1;
    private CONST IMAGE_SNAPSHOT        = 2  ;
    private CONST IMAGE_MOTION          = 4  ;
    private CONST VIDEO_MPEG            = 8  ;
    private CONST VIDEO_MPEG_MOTION     = 16 ;
    private CONST VIDEO_MPEG_TIMELAPSE  = 32 ;
    private CONST IMAGE_ANY = event::IMAGE | event::IMAGE_SNAPSHOT | event::IMAGE_MOTION ;
    private CONST VIDEO_ANY = event::VIDEO_MPEG | event::VIDEO_MPEG_MOTION | event::VIDEO_MPEG_TIMELAPSE ;

    /** @var int The camera ID. */
    private $camera;

    /** @var int The number of frames. */ 
    // TODO: check this description with the motion sources.
    // TODO: Do we need this?
    private $frameImage;

    /** @var int The number of frames. */ 
    // TODO: check this description with the motion sources.
    // TODO: Do we need this?
    private $frameVideo;

    /** @var int A Unix timestamp representing the record modification timestamp. */ 
    // TODO: setup constants to make converting SQL dates to unix timestamps.
    private $timeStamp;

    /** @var int A Unix timestamp representing the event timestamp. */
    private $timeStampEvent;

    /** @var string The file's size. */
    // TODO: review setting and getting this in light of change to use SplFileInfo class.
    private $fileSize;

    /** @var string Unused at present */
    private $textEvent;

    /** @var int the hour of the event */
    private $hour;

    /** @var string the time of the event as a string. */
    private $time;

    /** @var string the length of the video. */
    private $videoLength = null;
    
    /** @var eventFileInfo File information for the video file */
    private $videoFileInfo = null ;

    /** @var eventFileInfo File information for the image file */
    private $imageFileInfo = null ;

    /**
     * Set the member variables using an associative array of values. 
     * Set the file name to that of the video or data file depending on file type.
     * filename, frame, file_type and time_stamp vary according to file type.
     * 
     * @param array $row An associative array containing the values from one row of the database.
     */
    public function loadFromArray(array $row) {
        $this->setCamera($row['camera']);
        $this->setTextEvent($row['text_event']);
        $this->setTimeStampEvent($row['event_time_stamp']);
        $this->setTime($row['timefield']);
        $this->setHour($row['hourfield']);
        $this->setTimeStamp($row['ts']);
        $this->setFileDetails($row['file_type'], $row['filename'], $row['frame'], $row['file_size']);
    }

    public function mergeEvent(event $anEvent) {
        // The only fields we need to worry about are the file ones.
        $filetype = ($anEvent->hasImageFile()) ? event::IMAGE_ANY : event::VIDEO_ANY ;
        $filesize = $anEvent->getVideoFileSize();

        switch ($filetype) {
            case self::IMAGE_ANY:
                $filename = $anEvent->getImageFilename();
                $frame = $anEvent->getImageFrame();
                break;

            case self::VIDEO_ANY:
                $filename = $anEvent->getVideoFilename();
                $frame = $anEvent->getVideoFrame();
                break;
        }

        $this->setFileDetails($filetype, $filename, $frame, $filesize);
    }

    private function setFileDetails(int $filetype, string $filename, int $frame, ?string $filesize) {
        $nFiletype = (((int)$filetype) & event::VIDEO_ANY) ? event::VIDEO_ANY : event::IMAGE_ANY ;
        // Consolidate image and movie data.
        switch ($nFiletype) {
            case self::IMAGE_ANY:
                $this->setImageFilename($filename);
                $this->setImageFrame($frame);
                break;

            case self::VIDEO_ANY:
                $this->setVideoFilename($filename);
                $this->setVideoFrame($frame);
                // TODO: review this in light of conversion to SplFileInfo class.
                $this->setVideoFileSize($filesize);
                break;

            default:
                throw new ErrorException(gettext("Unknown file type in SQL"));
        }
    }
    
    private function hasImageFile():bool {
        if (is_null($this->imageFileInfo) || is_null($this->getImageFilename())){
            return (false) ;
        }
        return (true) ;
    }

    public function getCamera() {
        return $this->camera;
    }

    public function getImageFilename() {
        if (is_null($this->imageFileInfo)){
            $msg = sprintf (gettext('Accessing NULL member variable %s.'), 'event::imageFileInfo') ;
            throw new RuntimeException($msg) ;
        }
        return $this->imageFileInfo->getRealPath() ;
    }

    public function getVideoFilename() {
        if (is_null($this->videoFileInfo)){
            $msg = sprintf (gettext('Accessing NULL member variable %s.'), 'event::imageFileInfo') ;
            throw new RuntimeException($msg) ;
        }
        return $this->videoFileInfo->getRealPath() ;
    }

    public function getImageFrame() {
        return $this->frameImage;
    }

    public function getVideoFrame() {
        return $this->frameVideo;
    }

    public function getHour(): int {
        return ($this->hour);
    }

    public function getTime() {
        return ($this->time);
    }

    public function getTimeStamp() {
        return $this->timeStamp;
    }

    public function getTimeStampEvent() {
        return $this->timeStampEvent;
    }
    
    public function getURLVideo(){
        return ($this->videoFileInfo->getURL()) ;
    }

    public function getVideoFileSize() {
        return $this->fileSize;
    }

    public function getTextEvent() {
        return $this->textEvent;
    }

    private function setCamera($camera) {
        $this->camera = $camera;
    }

    private function setImageFilename($filename) {
        $this->imageFileInfo = new eventFileInfo($filename) ;
    }

    private function setVideoFilename($filename) {
        $this->videoFileInfo = new eventFileInfo($filename) ;
    }

    private function setImageFrame(int $frame) {
        $this->frameImage = $frame;
    }

    private function setVideoFrame(int $frame) {
        $this->frameVideo = $frame;
    }

    private function setHour(int $hour) {
        $this->hour = $hour;
    }

    private function setTime($time) {
        $this->time = $time;
    }

    private function setTimeStamp($timeStamp) {
        // TODO: convert to Unix timestamp
        $this->timeStamp = $timeStamp;
    }

    private function setTimeStampEvent($timeStampEvent) {
        // TODO: convert to Unix timestamp
        $this->timeStampEvent = $timeStampEvent;
    }

    private function setVideoFileSize($fileSize) {
        $bSave = false;

        if (is_null($fileSize) || ($fileSize == 0)) {
            $this->fileSize = $this->calculateVideoFileSize($this->getVideoFilename());
            $bSave = true;
        }
        else {
            $this->fileSize = $fileSize ;
        }

        if ($bSave) {
            $this->saveFileSize();
        }
    }

    private function calculateVideoFileSize(string $szFileName): string {
        if (file_exists($szFileName)) {
            $fSize = filesize($szFileName);
            $szFileSize = $this->formatBytes($fSize);
        }
        else {
            $szFileSize = gettext("n/a") ;
        }
        return ($szFileSize);
    }

    private function saveFileSize() {
        $query = sprintf(event::QUERY_SAVE_FILE_SIZE, $this->getVideoFileSize(), $this->getVideoFilename());

        $db = new dbMotion();
        $bRc = $db->query($query);
        if (!$bRc) {
            $szMsg = sprintf(gettext("query failed <br />debugging errno: %d  <br />debugging error: %s <br /> %s <br /> line: %d <br /> query: %s <br /> result: %s"),
                    mysqli_connect_errno(),
                    mysqli_connect_error(),
                    __FILE__,
                    __LINE__,
                    $query,
                    ""
                    );
            throw new RuntimeException($szMsg);
        }
    }

    private function formatBytes(float $bytes, int $precision = 2): string {
        $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $i = 0;

        while ($bytes > 1024) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function setTextEvent($textEvent) {
        $this->textEvent = $textEvent;
    }

    public function getVideoLength() {
        if (!file_exists($this->getVideoFilename())){
            return gettext("n/a") ;
        }
        
        if (is_null($this->videoLength)) {
            // Initialize getID3 engine
            $getID3 = new getID3;

            // Analyze file
            $fileInfo = $getID3->analyze($this->getVideoFilename());

            if (!isset($fileInfo['error'])) {
                // TODO: write out to database.
                $this->videoLength = $fileInfo['playtime_string']; // playtime in minutes:seconds, formatted string
            }
        }

        return ($this->videoLength);
    }

}
