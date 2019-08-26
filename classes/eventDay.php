<?php

/**
 * Object encapsulating the motion events for a day.
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'].'/libs/mysql/mysql.php' ;
require_once $_SESSION['root_dir'].'/classes/eventHour.php';

class eventDay {
    /** @var eventHour[] motion events collected by hour of event. */
    private $eventsForHour ;
    /** @var int timestamp for the day. */
    private $ts ;
    /** Query used to select a day's event. */
    CONST QUERY =
		'SELECT *, TIME(event_time_stamp) as timefield, HOUR(event_time_stamp) as hourfield, '.
		'event_time_stamp+0 as time_stamp '.
		'FROM security '.
		'WHERE event_time_stamp >= %s000000 '.
		'AND event_time_stamp <= %s235959 '.
		//'AND file_type=8 '. // list only movies
		'ORDER BY hourfield, camera, timefield, file_type';

    /**
     * 
     * @param type $ts The timestamp of the day whose events are to be loaded. If null, then today is used.
     */
    public function __construct($ts = null) {
        if (is_null($ts)) {
            $ts = time() ;
        }
        
        $this->setTS($ts) ;        
        $this->loadDay() ;
    }

    private function loadDay(){
        $hour = -1 ;
        $day = $this->getTsDay() ;
        $query = sprintf(SELF::QUERY, $day, $day) ;
        $db = $this->getDB() ;
    }
    
    private function addEventsForHour(array $eventsForHour) {
        $this->eventsForHour[] = $eventsForHour;
    }
    
    private function getDB() {
        
        if (!isset($_SESSION['mysql'])){
            //TODO: finish
        }
        
        return($_SESSION['db']) ;
    }

    private function getEventsForHour(): array {
        return $this->eventsForHour;
    }

    private function getTs() {
        return $this->ts;
    }
    
    /**
     * Get the day in a format for insertion into SQL query.
     * Format: yyyymmdd
     */
    private function getTsDay() {
        return(date("Ymd", $this->getTs())) ;
    }

    private function setTs($ts) {
        $this->ts = $ts;
    }


}
