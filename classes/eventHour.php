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
    private $events = array() ;
    
    /**
     * Add an event to the events array.
     * If the hour property has not yet been set, automatically set it.
     * @param event $event
     */
    public function addEvent(event $event) {
        // consolidate image and video events using timestamp as the array key.
        if (key_exists($event->getTimeStamp(), $this->events)){
            $this->events[$event->getTimeStamp()]->mergeEvent($event) ;
        } else {
            $this->events[$event->getTimeStamp()] = $event ;
        }
        
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
    
    public function toHTML(int $numCameras) {
        $hour_event_count = sizeof($this->getEvents()) ;
        $camera_cells = array() ;
        
        for ($nCount = 1 ; $nCount <= $numCameras ; $nCount++){
            $camera_cells[$nCount] = "" ;
        }
        
        // hour summary row
        $html = sprintf ('<tr class="hour-summary" id="%s">', $this->getHour()) ;
        $html .= sprintf('<td valign=top class=timeline-hour>%02u%s</td>', $this->getHour(), gettext("hours"));
        $html .= sprintf('<td colspan=%u class=timeline-row-header>', $numCameras) ;
        $html .= sprintf('<img class="plus" id="%s" src="images/plus.gif" border=0 onclick="plusclick(this);">', $this->getHour()) ;
        $html .= sprintf(ngettext("%d event", "%d events", $hour_event_count), $hour_event_count) ;
        $html .= '</td></tr>' ;

        // Data cell content
        foreach ($this->getEvents() as $event){
            $camera_cells[$event->getCamera()] .= $event->toHTML() ;
        }
        
        // Data row
        $html .= sprintf('<tr class="hour-events" id="%s">', $this->getHour()) ;
        $html .= '<td class="timeline-hour"></td>' ;
        for ($nCount = 1 ; $nCount <= sizeof($camera_cells) ; $nCount++) {
            $html .= '<td>' ;
            $html .= $camera_cells[$nCount] ;
            $html .= '</td>' ;
        }
        $html .= '</tr>' ;
        
        return ($html) ;
    }
}
