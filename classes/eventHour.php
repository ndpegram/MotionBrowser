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
    /** @var event[] An associative array of event objects. The key is the event timestamp. */
    private $events ;
    
    /**
     * Add an event to the events array.
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
    
    public function getEvents() : array {
        return ($this->events) ;
    }
    
    public function __toString() {
        $text = "<h1>" . $this->getHour() . "</h1>" ;

        foreach ($this->getEvents() as $event) {
            $text .= $event ;
        }
        return ($text) ;
    }
}
