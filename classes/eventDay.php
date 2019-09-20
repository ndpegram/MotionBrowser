<?php

/**
 * Object encapsulating the motion events for a day.
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/classes/dbMotion.php';
require_once $_SESSION['root_dir'] . '/classes/eventHour.php';
require_once $_SESSION['root_dir'] . '/classes/event.php';

class eventDay {

    /** @var eventHour[] motion events collected by hour of event. */
    private $hourEvents;

    /** @var int timestamp for the day. */
    private $ts;

    /** Query used to select a day's event. */
    private CONST QUERY = 'SELECT *, TIME(event_time_stamp) as timefield, HOUR(event_time_stamp) as hourfield, ' .
            'event_time_stamp+0 as ts ' .
            'FROM security ' .
            'WHERE event_time_stamp >= %s000000 ' .
            'AND event_time_stamp <= %s235959 ' .
            //'AND file_type=8 '. // list only movies
            'ORDER BY hourfield, camera, timefield, file_type';

    /** Query used to get cameras with data for this day. */
    private CONST QUERY_CAMERAS = 'SELECT distinct `camera` FROM `security` ' .
            'WHERE event_time_stamp >= %s000000 ' .
            'AND event_time_stamp <= %s235959 ' .
            'ORDER BY `camera` ';

    /**
     * 
     * @param type $ts The timestamp of the day whose events are to be loaded. If null, then today is used.
     */
    public function __construct(?int $ts = null) {
        if (is_null($ts)) {
            $ts = time();
        }

        $this->setTS($ts);
        $this->loadDay();
    }

    private function loadDay() {
        $szDay = $this->getTsDay();
        $query = sprintf(SELF::QUERY, $szDay, $szDay);
        $db = new dbMotion();
        $result = $db->query($query);
        $this->processSqlResult($result);
    }

    private function processSqlResult(mysqli_result $result) {
        $hour = null;
        $eventsForHour = null;

        // Currently uses the fact that the query is sorted and grouped by hour. Could do this by adding events and filtering in an addEvent method.
        foreach ($result as $aRow) {
            if ($hour <> $aRow['hourfield']) {
                $this->addEventsForHour($eventsForHour);
                $hour = $aRow['hourfield'];
                $eventsForHour = new eventHour();
            }

            $event = new event();
            $event->loadFromArray($aRow);
            $eventsForHour->addEvent($event);
        }

        if (!is_null($eventsForHour)) {
            $this->addEventsForHour($eventsForHour);
        }
    }

    private function addEventsForHour(?eventHour $eventsForHour) {
        if (is_null($eventsForHour)) {
            return;
        }

        $this->hourEvents[$eventsForHour->getHour()] = $eventsForHour;
    }

    public function getEventsForHour(): ?array {
        if (isset($this->hourEvents)){
            return $this->hourEvents;
        }
        else {
            return (null) ;
        }
    }

    private function getTs() {
        return $this->ts;
    }

    /**
     * Get the day in a format for insertion into SQL query.
     * Format: yyyymmdd
     */
    private function getTsDay() {
        return(date("Ymd", $this->getTs()));
    }

    private function setTs($ts) {
        $this->ts = $ts;
    }

    public function getCameras(): array {
        $szDay = $this->getTsDay();
        $query = sprintf(self::QUERY_CAMERAS, $szDay, $szDay);
        $db = new dbMotion();
        $result = $db->query($query);
        $cameras = array();
        foreach ($result as $row) {
            $cameras[] = $row['camera'];
        }
        return($cameras);
    }
    
 }

//session_start() ;
//$day = new eventDay();
//echo ($day->toHTML());
