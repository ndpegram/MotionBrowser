<?php

/*
 * Error handling code.
 */
// TODO: internationalise strings.

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
        switch ($errno) {
            case E_USER_ERROR:
                exit ($this->userError()) ;
                break;

            case E_USER_WARNING:
                echo "<b>My WARNING</b> [$$this->errno] $$this->errstr<br />\n";
                break;

            case E_USER_NOTICE:
                echo "<b>My NOTICE</b> [$$this->errno] $$this->errstr<br />\n";
                break;

            default:
                echo "Unknown error type: [$$this->errno] $$this->errstr<br />\n";
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
        sprintf("<p><b>My ERROR</b> [%i] %s</p>\n<p>Fatal error on line %i in file %s, PHP %s (%s)</p><Aborting&hellip;</p>\n",
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

$errors = new errors() ;
$trigger_error = trigger_error("my bad", E_USER_ERROR) ;