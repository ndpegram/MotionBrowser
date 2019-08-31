<?php

/**
 * Object representing a motion video event. Essentially contains the data from a MySQL record.
 * At present, text_event is not used, but is included here for future use.
 *
 * @author nigel
 */
class event {

    // TODO: Inspect motion sources for other values and to confirm these descriptions. Note that the documentation does not describe these.
    CONST VIDEO_MP4 = 8;
    CONST IMAGE_JPEG = 1;

    /** @var int The camera ID. */
    private $camera;

    /** @var string The full path and name of the image recording this event. */
    private $filenameImage;

    /** @var string The full path and name of the video recording this event. */
    private $filenameVideo;

    /** @var int The number of frames. */ // TODO: check this description with the motion sources.
    private $frameImage;

    /** @var int The number of frames. */ // TODO: check this description with the motion sources.
    private $frameVideo;

    /** @var int The file type. */
    private $fileType;

    /** @var int A Unix timestamp representing the record modification timestamp. */ // TODO: setup contants to make converting SQL dates to unix timestamps.
    private $timeStamp;

    /** @var int A Unix timestamp representing the event timestamp. */
    private $timeStampEvent;

    /** @var string The file's size. */
    private $fileSize;

    /** @var string Unused at present */
    private $textEvent;

    /** @var int the hour of the event */
    private $hour;
    
    /** @var string the time of the event as a string. */
    private $time ;

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
        $this->setTime($row['timefield']) ;
        $this->setHour($row['hourfield']);
        $this->setTimeStamp($row['ts']);
        $this->setFileDetails($row['file_type'], $row['filename'], $row['frame'], $row['file_size']);
    }

    public function mergeEvent(event $anEvent) {
        // The only fields we need to worry about are the file ones.
        $filetype = (is_null($anEvent->getImageFilename())) ? event::VIDEO_MP4 : event::IMAGE_JPEG;
        $filesize = $anEvent->getVideoFileSize();

        switch ($filetype) {
            case self::IMAGE_JPEG:
                $filename = $anEvent->getImageFilename();
                $frame = $anEvent->getImageFrame();
                break;

            case self::VIDEO_MP4:
                $filename = $anEvent->getVideoFilename();
                $frame = $anEvent->getVideoFrame();
                break;
        }

        $this->setFileDetails($filetype, $filename, $frame, $filesize);
    }

    private function setFileDetails(int $filetype, string $filename, int $frame, string $filesize) {
        // Consolidate image and movie data.
        switch ($filetype) {
            case self::IMAGE_JPEG:
                $this->setImageFilename($filename);
                $this->setImageFrame($frame);
                break;

            case self::VIDEO_MP4:
                $this->setVideoFilename($filename);
                $this->setVideoFrame($frame);
                $this->setVideoFileSize($filesize);
                break;

            default:
                throw new ErrorException(gettext("Unknown file type in SQL"));
        }
    }

    public function getCamera() {
        return $this->camera;
    }

    public function getImageFilename() {
        return $this->filenameImage;
    }

    public function getVideoFilename() {
        return $this->filenameVideo;
    }

    public function getImageFrame() {
        return $this->frameImage;
    }

    public function getVideoFrame() {
        return $this->frameVideo;
    }

    public function getFileType() {
        return $this->fileType;
    }

    public function getHour(): int {
        return ($this->hour);
    }
    
    public function getTime() {
        return ($this->time) ;
    }

    public function getTimeStamp() {
        return $this->timeStamp;
    }

    public function getTimeStampEvent() {
        return $this->timeStampEvent;
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
        $this->filenameImage = $filename;
    }

    private function setVideoFilename($filename) {
        $this->filenameVideo = $filename;
    }

    private function setImageFrame(int $frame) {
        $this->frameImage = $frame;
    }

    private function setVideoFrame(int $frame) {
        $this->frameVideo = $frame;
    }

    private function setFileType(int $fileType) {
        $this->fileType = $fileType;
    }

    private function setHour(int $hour) {
        $this->hour = $hour;
    }
    
    private function setTime($time){
        $this->time = $time ;
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
        // TODO: deal with filesize = 0. See setFileSize function in old_index.php.
        $this->fileSize = $fileSize;
    }

    private function setTextEvent($textEvent) {
        $this->textEvent = $textEvent;
    }

    public function __toString() {
        return ("<p>" . $this->getTimeStamp() . "\t" . $this->getVideoFilename() . "\t" . $this->getImageFilename() . "</p>");
    }

    public function toHTML() {
        $html = '<div>';
        $html .= sprintf('<a href="stream.php?file=%s"><img src="thumbnail.php?image=%s&width=88&height=72" border=0></a><br />',
                $this->getVideoFilename(),
                $this->getImageFilename());
        $html .= sprintf('<input type="checkbox" name="%s" value=%s> %s %s',
                $this->getTimeStamp(),
                $this->getTimeStamp(),
                $this->getTime(),
                $this->getVideoFileSize());
        $html .= '</div>';
        return ($html);
    }

}
