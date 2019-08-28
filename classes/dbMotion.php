<?php

/**
 * Convenience class to encapsulate database actions for motion data.
 * @author nigel
 */
require_once $_SESSION['root_dir'] . '/libs/mysql/mysql.php';

class dbMotion extends database {

    public function __construct() {
        parent::__construct(
                $_SESSION['mysql']['db'],
                $_SESSION['mysql']['host'],
                $_SESSION['mysql']['user'],
                $_SESSION['mysql']['password']);
    }

}
