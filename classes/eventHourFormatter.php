<?php

/**
 * Description of eventHourFormatter
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/classes/eventHour.php';
require_once $_SESSION['root_dir'] . '/classes/eventFormatter.php';

////////////////////////////////////////////////////////////////////////////////
interface eventHourFormatter {

    public function format(eventHour $anHour, int $numCameras);
}

////////////////////////////////////////////////////////////////////////////////
class textEventHourFormatter implements eventHourFormatter {

    public function format(\eventHour $anHour, int $numCameras): string {
        $text = "#" . $anHour->getHour() . "\n";

        foreach ($anHour->getEvents() as $event) {
            $text .= eventFormatUtils::formatEvent(formatUtils::FORMAT_TEXT, $event);
            $text .= "\n";
        }
        return ($text);
    }

}

////////////////////////////////////////////////////////////////////////////////
class htmlEventHourFormatter implements eventHourFormatter {

    public function format(\eventHour $anHour, int $numCameras): string {
        $hour_event_count = sizeof($anHour->getEvents());
        $camera_cells = array();

        for ($nCount = 1 ; $nCount <= $numCameras ; $nCount++){
            $camera_cells[$nCount] = "" ;
        }
        
        // hour summary row
        $html = sprintf('<tr class="hour-summary" id="%s">', $anHour->getHour());
        $html .= sprintf('<td valign=top class=timeline-hour>%02u%s</td>', $anHour->getHour(), gettext("hours"));
        $html .= sprintf('<td colspan=%u class=timeline-row-header>', 100);
        $html .= sprintf('<img class="plus" id="%s" src="images/plus.gif" border=0 onclick="plusclick(this);">', $anHour->getHour());
        $html .= sprintf(ngettext("%d event", "%d events", $hour_event_count), $hour_event_count);
        $html .= '</td></tr>';

        // Data cell content
        foreach ($anHour->getEvents() as $event) {
            $camera_cells[$event->getCamera()] .= eventFormatUtils::formatEvent(formatUtils::FORMAT_HTML, $event);
        }

        // Data row
        $html .= sprintf('<tr class="hour-events" id="%s">', $anHour->getHour());
        $html .= '<td class="timeline-hour"></td>';
        for ($nCount = 1 ; $nCount <= $numCameras ; $nCount++) {
            $html .= '<td>';
            if (isset($camera_cells[$nCount])) {
                $html .= $camera_cells[$nCount];
            }
            else {
                $html .= '&nbsp;' ;
            }
            $html .= '</td>';
        }
        $html .= '</tr>';

        return ($html);
    }

}

////////////////////////////////////////////////////////////////////////////////
class eventHourFormatUtils implements formatUtils {

    public static function formatEventHour(int $type, eventHour $anHour, int $numCameras) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
            case formatUtils::FORMAT_HTML:
                break;

            default :
                throw new InvalidArgumentException(sprintf(gettext("Invalid type argument passed to '%s'."), 'formatEventHour'));
        }

        $formatter = self::createFormatter($type);
        return ($formatter->format($anHour, $numCameras));
    }

    static function createFormatter(int $type) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
                $formatter = new textEventHourFormatter();
                break;

            case formatUtils::FORMAT_HTML:
                $formatter = new htmlEventHourFormatter();
                break;
        }

        return ($formatter);
    }

}
