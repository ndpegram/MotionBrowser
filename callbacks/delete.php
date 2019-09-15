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

$IDs = filter_input(INPUT_POST, 'todelete') ; 
deleteItems($IDs) ;

/**
 * Delete files and database entries. Called in response to POST
 * @param	{MIXED}	IDs of items to delete. (
 *                  Can be an array or comma-separated list.
 * @return	{void}	nothing
 */
function deleteItems($IDs) { 
// ******************************************
// FIXME: review this code as it has just been copied across.
// TODO: convert access to SQL records to use an object. 
// TODO: Redo code elsewhere to use this same object to access and change db info.
    global $testing, $conn;
    $filenames = '';
    $nFiles = 0;

    $conn = getDBConnection();

    if (is_array($IDs)) {
        $IDs = implode(", ", $IDs);
    }

    // trim any trailing comma
    $IDs = rtrim($IDs, ', ');

    // get the list of filenames we are going to delete
    $query = "SELECT `filename` FROM `security` WHERE `event_time_stamp` IN ($IDs)";
    $result = mysqli_query($conn, $query) or
            die(sprintf(gettext("query failed <br />debugging errno: %d  <br />debugging error: %s <br /> %s <br /> line: %d <br /> query: %s <br /> result: %s"),
                            mysqli_connect_errno(),
                            mysqli_connect_error(),
                            __FILE__,
                            __LINE__,
                            $query,
                            $result
    ));

    // loop for each one
    for ($i = 0; $i < mysqli_num_rows($result); $i++) {
        // TODO: add sanity check to delete rows without matching files and vice versa (possibly on each call, or possibly using cron).
        $row = mysqli_fetch_array($result);
        $filename = $row['filename'];

        if (!$testing) {
            // delete the file first
            if (!unlink($filename)) {
                die(sprintf(gettext("Error deleting %s"), $filename));
            }

            // if no problem, delete the record from the database
            $query = "DELETE FROM security WHERE filename='" . $filename . "'";
            mysqli_query($conn, $query) or
                    die(sprintf(gettext("query failed <br />debugging errno: %d  <br />debugging error: %s <br /> %s <br /> line: %d <br /> query: %s <br /> result: %s"),
                                    mysqli_connect_errno(),
                                    mysqli_connect_error(),
                                    __FILE__,
                                    __LINE__,
                                    $query,
                                    ""
            ));
        } else {
            // Running development machine/version--take no irreversible actions
            $nFiles++;
            $filenames .= '"' . $filename . '"' . ', ';
        }
    }

    // trim trailing comma
    $filenames = rtrim($filenames, ', ');

    if ($testing) {

        $query = 'select count(*) from `security` where `filename` in (' . $filenames . ')';
        $result = mysqli_query($conn, $query) or
                die(sprintf(gettext("query failed <br />debugging errno: %d  <br />debugging error: %s <br /> %s <br /> line: %d <br /> query: %s <br /> result: %s"),
                                mysqli_connect_errno(),
                                mysqli_connect_error(),
                                __FILE__,
                                __LINE__,
                                $query,
                                $result
        ));

        $row = mysqli_fetch_row($result);
        echo '<h3>Delete</h3><p>Query returned ' . $row[0] . ' items to be deleted out of ' . $nFiles . ' items selected to delete</p>';
        $filenames = str_replace(',', '<br />', $filenames);
        echo "<pre>$filenames</pre>";
        die;
    }

    mysqli_free_result($result);
}
