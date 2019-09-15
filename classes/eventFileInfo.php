<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Adds the parallel URL access set up in the web server. 
 * 
 * The setup should be done so that the files in the motion directory are
 * able to be accessed from the root of the MotionBrowser directory via the URL
 * '/video'.
 * 
 * For example, if the files are in /var/lib/motion/ and MotionBrowser is hosted
 * at security.short.url.com then the videos and images should be accessible via
 * security.short.url.com/video/
 * 
 * @author nigel
 */
class eventFileInfo extends SplFileInfo {
    /** The path from the root to the images and videos. */
    private CONST URL_PATH = '/video/' ;
    /** @var String URL to access the file. */
    private $URL ;
    
    public function __construct(string $file_name) {
        parent::__construct($file_name);
        $this->setURL() ;
    }
    
    private function setURL() {
        $URL = self::getVideoBaseURL() ;
        $URL .= self::URL_PATH . $this->getBasename() ;
        $this->URL = $URL ;
    }
    
    public function getURL() {
        return ($this->URL) ;
    }
    
    private static function getVideoBaseURL() : string {
        $referer = parse_url(filter_input(INPUT_SERVER, 'HTTP_REFERER')) ;
        $base = $referer['scheme'] . '://' . $referer['host'] . $referer['path'] ;
        $nPos = strrpos($base, '/') ;
        $URL = substr($base, 0, $nPos) ;
        return ($URL) ;
    }

}
