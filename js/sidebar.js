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

    // Get directory of script so can then get callback using relative paths. 
    //This allows installation on a range of servers without having to change hard-coded file locations.
    var scripts= document.getElementsByTagName('script');       // Get the last script DOM (this one)
    var path= scripts[scripts.length-1].src.split('?')[0];      // remove any ?query
    var mydir= path.split('/').slice(0, -1).join('/')+'/';      // remove last filename part of path
    URL = mydir + "../callbacks/getCalendar.php"

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
