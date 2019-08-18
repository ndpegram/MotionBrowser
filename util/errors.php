<?php

/*
 * Error handling code.
 */

require_once $root_dir . "/lang.inc" ; 

class errors {
    /**
     * @var int The level of the error raised, as an integer.
     */
    private $errno = 0 ;
    /**
     * @var string The error message.
     */
    private $errstr = null  ;
    /**
     * @var string Name of file where error was raised.
     */
    private $errfile = null ;
    /**
     *
     * @var int Number of line in file where error was raised.
     */
    private  $errline = 0 ;

    function __construct() {
        set_error_handler(array($this, 'myErrorHandler'));
    }

    /**
     * Error handler function.
     * 
     * @param int $errno The level of the error raised, as an integer.
     * @param string $errstr The error message.
     * @param string $errfile Name of file where error was raised.
     * @param int $errline Number of line in file where error was raised.
     * @return bool If false, then processing will fall through to the standard 
     *              PHP error handler. If true, then will stop with this handler.
     */
    function myErrorHandler($errno, $errstr, $errfile, $errline): bool {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }
        
        $this->setErrno($errno) ;
        $this->setErrstr($errstr) ;
        $this->setErrfile($errfile) ;
        $this->setErrline($errline) ;
        
        return ($this->processError()) ;
    }
    
    /**
     * Error handler dispatcher. 
     * 
     * @return bool If false, then processing will fall through to the standard 
     *              PHP error handler. If true, then will stop with this handler.
     */
    function processError(): bool {
        switch ($this->getErrno()) {
            case E_USER_ERROR:
                $nRc = $this->userError() ;
                exit($nRc) ;
                break;

            case E_USER_WARNING:
                printf(gettext("Error warning %u %s\n"), $this->getErrno(), $this->getErrstr()) ;
                break;

            case E_USER_NOTICE:
                printf(gettext("Error notice %u %s\n"), $this->getErrno(), $this->getErrstr()) ;
                break;

            default:
                printf(gettext("Error unknown %u %s\n"), $this->getErrno(), $this->getErrstr()) ;
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }

    /**
     * Process user-defined errors.
     * 
     * @return int Use a non-zero integer to indicate failure. (For any command-
     *              line error checking.
     */
    private function userError(): int {
        printf(gettext("Error (user error) %u %s %u %s %s %s\n"),
                $this->getErrno(),
                $this->getErrstr(),
                $this->getErrLine(),
                $this->getErrfile(),
                PHP_VERSION,
                PHP_OS
        );
        return(1);
    }

    private function getErrno(): int {
        return $this->errno;
    }

    private function getErrstr(): string {
        return $this->errstr;
    }

    private function getErrfile(): string {
        return $this->errfile;
    }

    private function getErrline(): int {
        return $this->errline;
    }

    private function setErrno(int $errno) {
        $this->errno = $errno;
    }

    private function setErrstr(string $errstr) {
        $this->errstr = $errstr;
    }

    private function setErrfile(string $errfile) {
        $this->errfile = $errfile;
    }

    private function setErrline(int $errline) {
        $this->errline = $errline;
    }


}

//echo "<h1>Error test</h1>" ;
//$errors = new errors() ;
//$trigger_error = trigger_error("my bad", E_USER_ERROR) ;