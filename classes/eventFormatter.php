<?php

/**
 * Description of eventFormatter
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/classes/event.php';
require_once $_SESSION['root_dir'] . '/classes/formatUtils.php';

////////////////////////////////////////////////////////////////////////////////
interface eventFormatter {

    public function format(event $anEvent);
}

////////////////////////////////////////////////////////////////////////////////
class textEventFormatter implements eventFormatter {

    public function format(\event $anEvent): string {
        $text = $anEvent->getTimeStamp() . "\t" . $anEvent->getVideoFilename() . "\t" . $anEvent->getImageFilename();
        return ($text);
    }

}

////////////////////////////////////////////////////////////////////////////////
class htmlEventFormatter implements eventFormatter {

    public function format(\event $anEvent): string {
        $html = '<div>';
        $html .= sprintf('<a href="stream.php?file=%s"><img src="callbacks/thumbnail.php?image=%s&width=88&height=72" border=0></a><br />',
                $anEvent->getVideoFilename(),
                $anEvent->getImageFilename());
        $html .= sprintf('<input type="checkbox" name="%s" value=%s> %s %s',
                $anEvent->getTimeStamp(),
                $anEvent->getTimeStamp(),
                $anEvent->getTime(),
                $anEvent->getVideoFileSize());
        $html .= '</div>';
        return ($html);
    }

}

class eventFormatUtils extends formatUtils {

    public static function formatEvent(int $type, event $anEvent) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
            case formatUtils::FORMAT_HTML:
                break;

            default :
                throw new InvalidArgumentException(sprintf(gettext("Invalid type argument passed to '%s'."), 'formatEvent'));
        }

        $formatter = self::createEventFormatter($type);
        return ($formatter->format($anEvent));
    }

    private static function createEventFormatter(int $type) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
                return (new textEventFormatter());

            case formatUtils::FORMAT_HTML:
                return (new htmlEventFormatter());
        }
    }

}
