<?php

/**
 * Object encapsulating the motion events for a day.
 *
 * @author nigel
 */
session_start();
require_once $_SESSION['root_dir'] . '/libs/mysql/mysql.php';
require_once $_SESSION['root_dir'] . '/classes/eventHour.php';
require_once $_SESSION['root_dir'] . '/classes/event.php';

class eventDay {

    /** @var eventHour[] motion events collected by hour of event. */
    private $hourEvents;

    /** @var int timestamp for the day. */
    private $ts;

    /** Query used to select a day's event. */
    CONST QUERY = 'SELECT *, TIME(event_time_stamp) as timefield, HOUR(event_time_stamp) as hourfield, ' .
            'event_time_stamp+0 as timestamp ' .
            'FROM security ' .
            'WHERE event_time_stamp >= %s000000 ' .
            'AND event_time_stamp <= %s235959 ' .
            //'AND file_type=8 '. // list only movies
            'ORDER BY hourfield, camera, timefield, file_type';

    /**
     * 
     * @param type $ts The timestamp of the day whose events are to be loaded. If null, then today is used.
     */
    public function __construct($ts = null) {
        if (is_null($ts)) {
            $ts = time();
        }

        $this->setTS($ts);
        $this->loadDay();
    }

    private function loadDay() {
        $hour = -1;
        $szDay = $this->getTsDay();
        $query = sprintf(SELF::QUERY, $szDay, $szDay);
        $db = $this->getDB();
        $result = $db->query($query);
        $this->processSqlResult($result);
    }

    private function processSqlResult(mysqli_result $result) {
        $hour = null;
        $eventsForHour = null;
        while ($aRow = $result->fetch_assoc()) {
            if ($hour <> $aRow['hourfield']) {
                if (!is_null($hour)){
                    $this->addEventsForHour($eventsForHour);
                }
                $hour = $aRow['hourfield'];
            }

            $event = new event();
            $event->loadFromArray($aRow);
            $eventsForHour[] = $event;
        }
    }

    private function addEventsForHour(array $eventsForHour) {
        if (is_null($eventsForHour)) {
            return;
        }

        $hour = new evenHour();
        foreach ($eventsForHour as $anEvent) {
            $hour->addEvent($anEvent);
        }
        $this->hourEvents[] = $hour;
    }

    private function getDB() {
        $db = new database($_SESSION['mysql']['db'],
                $_SESSION['mysql']['host'],
                $_SESSION['mysql']['user'],
                $_SESSION['mysql']['password']);

        return($db);
    }

    private function getEventsForHour(): array {
        return $this->hourEvents;
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

}

$day = new eventDay();
