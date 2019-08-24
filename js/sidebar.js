/* 
 * Scripts used by the sidebar.
 */

/**
 * Create a month calendar with the selected date that passed as a parameter.
 * The calendar is then loaded into the minicalendar div.
 * 
 * @param {int} Unix timestamp to use to create month calendar
 * @returns {undefined}
 */
function displayMonth(ts, URL) {
    var divMinicalendar = document.querySelector("div.minicalendar");
//    $.get(
//            URL,
//            function (data, status) {
//                divMinicalendar.innerHTML = data;
//            }
//    );

    $.post(URL,
            {
                ts: ts,
            },
            function (data, status) {
                divMinicalendar.innerHTML = data;
            });
}
