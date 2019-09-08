<?php

/**
 * Description of eventDayFormatter
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/classes/eventDay.php';
require_once $_SESSION['root_dir'] . '/classes/eventHourFormatter.php';

////////////////////////////////////////////////////////////////////////////////
interface eventDayFormatter {

    public function format(eventDay $aDay);
}

////////////////////////////////////////////////////////////////////////////////
class textEventDayFormatter implements eventDayFormatter {
    public function format(\eventDay $aDay): string {
        $text = "";
        $numCameras = sizeof($aDay->getCameras()) ;

        foreach ($aDay->getEventsForHour() as $hour) {
            $text .= eventHourFormatUtils::formatEventHour(formatUtils::FORMAT_TEXT, $hour, $numCameras);
        }
        return ($text);
    }

}

////////////////////////////////////////////////////////////////////////////////
class htmlEventDayFormatter implements eventDayFormatter {

    public function format(\eventDay $aDay): string {
        
        if (null === $aDay->getEventsForHour()){
            return (gettext(gettext("no_events"))) ;
        }
        
        $html = "";
        $cameras = $aDay->getCameras();
        $numCameras = sizeof($cameras);


        $html = "<TABLE id=idtable border=1 cellspacing=0 cellpadding=4 class=timeline>\n";

        // Header row
        $html .= '<TR class=timeline-header><th> </th>';
        foreach ($cameras as $cam) {
            $webcam = 'http://' . $_SESSION['webcam']['server'] . ':' . $_SESSION['webcam']['webcam_port'][($cam)] . '/';
            $title = sprintf(gettext("camera name %d"), $cam);
            $href = sprintf("javascript:openwindow('%s', '%s', %u, %u);", $webcam, $title, $_SESSION['webcam']['x'], $_SESSION['webcam']['y']);
            $html .= "<th>$title" .
                    "<a href=\"$href\">" .
                    ' <img src=images/icon_video.gif border=0 alt="' . gettext("see_camera") . '">' .
                    '</a></th>';
        }
        $html .= "</TR>\n";

        // Body rows
        foreach ($aDay->getEventsForHour() as $hour) {
            $html .= eventHourFormatUtils::formatEventHour(formatUtils::FORMAT_HTML, $hour, $numCameras);
        }

        // Footer/finish.
        $html .= "</TABLE>";
        $html .= "<script type='text/javascript'> timelineDetailsHide() ; </script>" ;
        
        return ($html) ;
    }

}

////////////////////////////////////////////////////////////////////////////////
class eventDayFormatUtils implements formatUtils {

    public static function formatEventDay(int $type, eventDay $aDay) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
            case formatUtils::FORMAT_HTML:
                break;

            default :
                throw new InvalidArgumentException(sprintf(gettext("Invalid type argument passed to '%s'."), 'formatEventDay'));
        }

        $formatter = self::createFormatter($type);
        return ($formatter->format($aDay));
    }

    static function createFormatter(int $type) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
                return (new textEventDayFormatter());

            case formatUtils::FORMAT_HTML:
                return (new htmlEventDayFormatter());
        }
    }

}
