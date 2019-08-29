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

    /** @var string The full path and name of the file recording this event. */
    private $filename;

    /** @var int The number of frames. */ // TODO: check this description with the motion sources.
    private $frame;

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
    private $hour ;

    /**
     * Set the member variables using an associative array of values.
     * @param array $row An associative array containing the values from one row of the database.
     */
    public function loadFromArray(array $row) {
        $this->setCamera($row['camera']);
        $this->setFilename($row['filename']);
        $this->setFrame($row['frame']);
        $this->setFileType($row['file_type']);
        $this->setTimeStamp($row['ts']);
        $this->setTextEvent($row['text_event']);
        $this->setTimeStampEvent($row['event_time_stamp']);
        $this->setFileSize($row['file_size']);
        $this->setHour($row['hourfield']) ;
    }

    public function getCamera() {
        return $this->camera;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getFrame() {
        return $this->frame;
    }

    public function getFileType() {
        return $this->fileType;
    }

    public function getHour(): int {
        return ($this->hour);
    }

    public function getTimeStamp() {
        return $this->timeStamp;
    }

    public function getTimeStampEvent() {
        return $this->timeStampEvent;
    }

    public function getFileSize() {
        return $this->fileSize;
    }

    public function getTextEvent() {
        return $this->textEvent;
    }

    private function setCamera($camera) {
        $this->camera = $camera;
    }

    private function setFilename($filename) {
        $this->filename = $filename;
    }

    private function setFrame(int $frame) {
        $this->frame = $frame;
    }

    private function setFileType(int $fileType) {
        $this->fileType = $fileType;
    }

    private function setHour(int $hour) {
        $this->hour = $hour ;
    }
    private function setTimeStamp($timeStamp) {
        // TODO: convert to Unix timestamp
        $this->timeStamp = $timeStamp;
    }

    private function setTimeStampEvent($timeStampEvent) {
        // TODO: convert to Unix timestamp
        $this->timeStampEvent = $timeStampEvent;
    }

    private function setFileSize($fileSize) {
        // TODO: deal with filesize = 0. See setFileSize function in old_index.php.
        $this->fileSize = $fileSize;
    }

    private function setTextEvent($textEvent) {
        $this->textEvent = $textEvent;
    }

    public function __toString() {
        return ("<p>" . $this->getTimeStamp() . "\t" . $this->getFilename() . "</p>") ;
    }
}
