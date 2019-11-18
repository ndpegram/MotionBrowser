/* 
 * Scripts used by the sidebar.
 */

/**
 * Create a month calendar with the selected date that passed as a parameter.
 * The calendar is then loaded into the minicalendar div.
 * 
 * @param {int} ts Unix timestamp to use to create month calendar
 * @returns {undefined}
 */
function displayMonth(ts) {
    var divMinicalendar = document.querySelector("div.minicalendar");
    var divMain = document.querySelector("div.main");

    // Redraw the calendar.
    var URL = getBaseDir() + "/callbacks/getCalendar.php";

    var cursor = document.body.style.cursor;
    document.body.style.cursor = 'wait';

    $.post(URL,
            {
                ts: ts
            },
            function (data, status) {
                // Redraw the calendar
                divMinicalendar.innerHTML = data;
                // Clear the main window
                document.querySelector("div.main").innerHTML = "";
            });

    // Display details for the day in the main window.
    URL = getBaseDir() + "/callbacks/getEvents.php?ts=" + ts;

    $.post(URL,
            {
                ts: ts
            },
            function (data, status) {
                // Redraw the calendar
                divMain.innerHTML = data;
                // hide all event rows in the table.
                $(".hour-events").hide();
                //show the original cursor.
                document.body.style.cursor = cursor;
                //reinitialise lightbox (requried to get AJAX content to work).
                $(".html5lightbox").html5lightbox();
            });
}

/**
 * 
 * @returns {String} The base URL.
 */
function getBaseDir() {
    // Get base directory so can then get callback using relative paths. 
    //This allows installation on a range of servers without having to change hard-coded file locations.
    var scripts = document.getElementsByTagName('script');       // Get the first script DOM (this one)
    var path = scripts[0].baseURI.split('?')[0];      // remove any ?query
    var mydir = path.split('/').slice(0, -1).join('/') + '/';      // remove last filename part of path
    return mydir;
}

/**
 * Set all video selection checkboxes to selected.
 * @returns {undefined}
 */
function select_all() {
    set_all(true);
}

/**
 * Set all video selection checkboxes to deselected.
 * @returns {undefined}
 */
function select_none() {
    set_all(false);
}

/**
 * Set all video selection checkboxes to the state indicated by the parameter.
 * @param {boolean} value The selection state for all checkboxes.
 * @returns {undefined}
 */
function set_all(value) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = value;
    }
}

/**
 * Delete selected items (with confirmation).
 * @return	{void}		nothing
 */
// TODO: allow error message from delete to be formatted in html. May need to use external library dialogs instead of vanilla alert.

function deleteSelection() {
    // are any items selected?
    var IDs = [];
    var numSelected = 0;
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            numSelected++;
            IDs.push(checkboxes[i].value);
        }
    }

    // No, alert and exit
    if (numSelected === 0) {
        alert(jsGettext("No items selected for deletion") + '.');
        return false;
    }

    // Exit if deletion not confirmed.
    msg = jsGettext('Delete selected items') + '?' ;
    if (confirm(msg)) {
        // Get date to return to
        theDate = $(".selected-day").attr('id'); 
        // Do the deletion.
        params = {todelete: IDs};
        URL = getBaseDir() + "/callbacks/delete.php";
        $.post(URL, params,
                function (data) {
                    // Enable the line below if debugging.
//                    alert(data);
                }
                )
                .done(function () {
                    // Show the page we were on.
                    displayMonth(theDate);
                }
                )
                .fail(function () {
                    alert('Error deleting "' + IDs + '".');
                }
                );

    }

}

/**
 * 
 * @param {String} szKey The key to look up in the translation database.
 * @returns {String} The translated key.
 */
function jsGettext(szKey) {
    var szReturn = szKey; // Use key if function call fails
    var URL = getBaseDir() + "/callbacks/js_gettext.php";

    $.ajax({
        async: false, // We want to get the value from the callback before returning.
        type: 'POST',
        url: URL,
        data: {message: szKey},
        success: function(data){
                    szReturn = data ;
                },
        error: function(jqXHR, textStatus, errorThrown){
                    console.log("Error in jsGettext().\n Error getting string '" + szKey + "' from js_gettext.php. Default value used.");
                    console.log('Status: ' + textStatus) ;
                    console.log ('Error thrown: ' + errorThrown) ;
                    szReturn = 'error' ;        }
                }) ;

    return (szReturn) ;
}