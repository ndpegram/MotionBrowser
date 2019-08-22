<?php

/**
 * Create and display a small HTML calendar in month view. For sidebars.
 * Defaults to display the current month.
 * CSS classes:
 *  minicalendar
 *  normal-day
 *  weekend-day
 *  selected-day
 *  today
 *  calendar-month
 *  calendar-header
 *  calendar-footer
 * @author nigel
 */

class CalendarMonthSmall {
    /** @var string The HTML representing the month calendar widget. */
    private $HTML = null ;
    /** @var int The date to display, expressed as a unix timestamp. */
    private $theDate ;
    /** @var int The number of blank days at the start of the month. */
    private $offset;
    /** @var int The number of days in the month. */
    private $daysInMonth;

    function __construct($theDate = null) {
        $this->setDateFromTime($theDate) ;
        
    }

    public function setDateFromTime(int $theDate = null) {
        if (is_null($theDate)) {
            $theDate = time() ;
        }
        $this->theDate = $theDate;
        $this->setDaysInMonth() ;
        $this->setOffset() ;
    }

    public function setDateFromValues (int $year, int $month, int $day){
        $ts = strtotime($day."/".$month."/".$year) ;
        $this->setDateFromTime($ts) ;
    }
    
    private function getCalendarHeader():string {
        $header =  "<table class='minicalendar' border=\"1\">\n";
        $header .= "\t<tr><th></th><th colspan=5>";
        $header .= $this->getMonthName() . " " . $this->getYear() ;
        $header .= "</th><th></th></tr>\n" ;
        $header .= "\t<tr><th>Su</th><th>M</th><th>Tu</th><th>W</th><th>Th</th><th>F</th><th>Sa</th></tr>";
        return($header) ;
    }
    
    private function getCalendarFooter():string {
        $footer = "</table>\n" ;
        return($footer) ;
    }
    
    /**
     * Get the HTML code for the calendar.
     * @return string The html for the calendar.
     */
    public function getHTML():string {
        $HTML= $this->getCalendarHeader() ;
        $HTML .= $this->getCalendarBody() ;
        $HTML .= $this->getCalendarFooter() ;
        return ($HTML) ;
    }
    
    private function getCalendarBody():string {
        $day = 0;
        $HTML = "" ;
        
        while ($day < $this->daysInMonth){
            $HTML .= $this->getCalendarRow($day) ;
            $day += 7 ;
        }

        return($HTML) ;
    }
    
    protected function getCalendarCellContent(string $date){
        return $date ;
    }

    private function getCalendarRow(int $day): string {
        $HTML = "<tr>" ;
        $dates = $this->getCalendarRowDates($day) ;
        for ($nCount = 0 ; $nCount < sizeof($dates) ; $nCount++) {
            $HTML .= "<td>" . $this->getCalendarCellContent($dates[$nCount]) . "</td>" ;            
        }
        $HTML .= "</tr>\n" ;
        return ($HTML) ;
    }
    
    private function getCalendarRowDates(int $day): array {
        $dates = (array) [] ;
        
        if ($day == 0) {
            $day = 0 - $this->offset + 1 ;
        }
        
        for ($nCount = 0 ; $nCount < 7 ; $nCount++) {
            $date = $day + $nCount  ;
            if (($date < 1) || ($date > $this->daysInMonth)) {
                $szDate = "" ;
            }
            else {
                $szDate = "".$date ;
            }
            
            $dates[$nCount] = $szDate ;
        }
        
        return ($dates) ;
    }
    
    private function getDate():int {
        return $this->theDate;
    }

    private function numDaysInMonth():int{
        return (date("t", $this->getDate())) ;
    }
    
    private function getMonthName():string {
        return (date("F", $this->getDate())) ;
    }
    
    private function getYear():int {
        return(date("Y", $this->getDate())) ;
    }
        
    private function getStartingOffset():int {
        return(date("w", $this->getDate())) ;
    }

    private function setOffset() {
        $this->offset = $this->getStartingOffset() ;
        
    }

    private function setDaysInMonth() {
        $this->daysInMonth = $this->numDaysInMonth() ;
    }

}

$cal = new CalendarMonthSmall() ;
echo $cal->getHTML() ;