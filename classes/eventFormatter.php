<?php

/**
 * Description of eventFormatter
 *
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/classes/event.php';
require_once $_SESSION['root_dir'] . '/classes/formatUtils.php';
require_once $_SESSION['root_dir'] . '/classes/eventFileInfo.php';

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
//        return (self::test) ;
        $videoURL = $anEvent->getURLVideo () ;
        $html = '<div>';
        $html .= sprintf('<a target="_blank" href="%1$s" class="html5lightbox" title="%1$s"><img src="callbacks/thumbnail.php?image=%2$s&width=88&height=72" border=0></a><br />',
                        $videoURL,
                        $anEvent->getImageFilename());
        $html .= sprintf('%s <input type="checkbox" name="%s" value=%s> <br />Size: %s <br />Length: %s',
                $anEvent->getTime(),
                $anEvent->getTimeStamp(),
                $anEvent->getTimeStamp(),
                $anEvent->getVideoFileSize(),
                $anEvent->getVideoLength() 
                );
        $html .= '</div>';
        return ($html);
    }

    public const test='		<div>
			<a target=_blank href="http://localhost/MotionBrowser/video/717-20190901011332.mp4" class="html5lightbox" >
				<img src="../MotionBrowser/callbacks/thumbnail.php?image=/var/lib/motion/CAM1_717-20190901011333-22.jpg&amp;width=88&amp;height=72" border="0">
			</a>
			<br>01:13:32 
			<input type="checkbox" name="20190901011332" value="20190901011332"> 
			<br>Size: 37 MB 
			<br>Length: 0:44
		</div>
' ;
}

class eventFormatUtils implements formatUtils {

    public static function formatEvent(int $type, event $anEvent) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
            case formatUtils::FORMAT_HTML:
                break;

            default :
                throw new InvalidArgumentException(sprintf(gettext("Invalid type argument passed to '%s'."), 'formatEvent'));
        }

        $formatter = self::createFormatter($type);
        return ($formatter->format($anEvent));
    }

    static function createFormatter(int $type) {
        switch ($type) {
            case formatUtils::FORMAT_TEXT:
                return (new textEventFormatter());

            case formatUtils::FORMAT_HTML:
                return (new htmlEventFormatter());
        }
    }

}
