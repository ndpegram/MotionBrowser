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

    // Get base directory so can then get callback using relative paths. 
    //This allows installation on a range of servers without having to change hard-coded file locations.
    var scripts= document.getElementsByTagName('script');       // Get the first script DOM (this one)
    var path= scripts[0].baseURI.split('?')[0];      // remove any ?query
    var mydir= path.split('/').slice(0, -1).join('/')+'/';      // remove last filename part of path
    URL = mydir + "/callbacks/getCalendar.php"

    $.post(URL,
            {
                ts: ts,
            },
            function (data, status) {
                // Redraw the calendar
                divMinicalendar.innerHTML = data;
                // Clear the main window
                document.querySelector("div.main").innerHTML = "" ;
            });
}

function showDate(ts) {
    displayMonth(ts) ;
}

/**
 * Set all video selection checkboxes to selected.
 * @returns {undefined}
 */
function select_all(){
	set_all(true);
}

/**
 * Set all video selection checkboxes to deselected.
 * @returns {undefined}
 */
function select_none(){
	set_all(false);
}

/**
 * Set all video selection checkboxes to the state indicated by the parameter.
 * @param {boolean} value The selection state for all checkboxes.
 * @returns {undefined}
 */
function set_all(value){
	var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = value ;
	}
}

