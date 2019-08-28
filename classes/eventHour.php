<?php

/**
 * Object containing events for a particular hour.
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'].'/classes/event.php';

class eventHour {
    /** @var int The hour of the events 0..23. */
    private $hour = null ;
    /** @var event[] Array of event objects. */
    private $events ;
    
    /**
     * Add an event to the end of the events array.
     * If the hour property has not yet been set, automatically set it.
     * @param event $event
     */
    public function addEvent(event $event) {
        $this->events[] = $event ;
        if (is_null($this->hour)){
            $this->setHour($event->getHour());
        }
    }
    
    public function getHour(): int {
        return ($this->hour) ;
    }
    public function setHour (int $hour) {
        $this->hour = $hour ;
    }
    
}
