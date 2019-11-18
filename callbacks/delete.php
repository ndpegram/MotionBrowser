<?php

/*
 * Callback used to delete files and SQL records.
 * 
 * IDs to delete passed in POST var 'todelete'.
 */
if (!isset($_SESSION)) {
    session_start();
}

require_once $_SESSION['root_dir'] . '/lang.inc' ;
require_once $_SESSION['root_dir'] . '/classes/dbMotion.php';

$IDs = getIDs() ;
deleteItems($IDs) ;

/**
 * Delete files and database entries. Called in response to POST
 * @param	{string} Comma-separated list of IDs of items to delete. 
 * @return	{void}	nothing
 */
function deleteItems($IDs) { 
// ******************************************
// FIXME: review this code as it has just been copied across.
// TODO: convert access to SQL records to use an object. 
// TODO: Redo code elsewhere to use this same object to access and change db info.
    $testing = $_SESSION['testing'] ;
    $filenames = [];
    
    $db = new dbMotion() ;

    // get the list of filenames we are going to delete
    $query = "SELECT `filename` FROM `security` WHERE `event_time_stamp` IN ($IDs)";
    $result = $db->query($query) ;

    // loop for each one
    for ($i = 0 ; $i < $result->num_rows ; $i++) {
        // TODO: add sanity check to delete rows without matching files and vice versa (possibly on each call, or possibly using cron).
        $row = $result->fetch_assoc() ;
        $filename = $row['filename'];

        if (!$testing) {
            // delete the file first
            if (!unlink($filename)) {
                die(sprintf(gettext("Error deleting %s"), $filename));
            }

            // if no problem, delete the record from the database
            $query = "DELETE FROM security WHERE filename='" . $filename . "'";
            $result = $db->query($query) ;
        } else {
            // Running development machine/version--take no irreversible actions
            $filenames[] = $filename ;
        }
    }

    if ($testing) {
        $where = '' ;
        foreach ($filenames as $filename) {
            $where .= '"' . $filename . '", ' ;
        }
        $where = rtrim($where, ', ') ;
        $query = 'select count(*) from `security` where `filename` in (' . $where . ')';
        $result = $db->query($query) ;

        $row = $result->fetch_array() ;
        echo '<h3>Delete</h3><p>Query returned ' . $row[0] . ' items to be deleted out of ' . sizeof($filenames) . ' items selected to delete</p>';
        echo '<pre>' . implode('<br />', $filenames) . '</pre>' ;
        die;
    }

}

/**
 * Get the timestamps of the events to delete from the POST vars.
 * @return string The timestamps of the events to delete as a comma-separated string.
 */
function getIDs():string {
    $postIDs = filter_input(INPUT_POST, 'todelete', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ; 
    
    if ($postIDs === false) {
//        $postIDs = $_POST['todelete'] ;
        throw new UnexpectedValueException(gettext('No items to delete obtained from POST variable')) ;
    }

    if (is_array($postIDs)) {
        $IDs = implode(", ", $postIDs);
    }
    else {
        $IDs = $postIDs ;
    }
    
    // trim any trailing comma
    $IDs = rtrim($IDs, ', ');

    return ($IDs);
}