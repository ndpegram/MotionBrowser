/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function openwindow(url, title, xx, yy)
{
	var wh = open(url, title, 'scrollbars=no,status=no,menubar=no,resizable=no,toolbar=yes,width=' + xx + ',height=' + yy);
	wh.location.href = url;
	if (wh.opener == null) wh.opener = self;
	wh.focus();
}

/**
 * Hide all timeline detail rows (rows showing the videos).
 * @returns {void} 
 */
function timelineDetailsHide(){
    // Hide all table rows of class 'hour-events'
    $(".hour-events").hide();
}

/**
 * plusClick Respond to the plus image being clicked.
 * @param {object} image
 * @returns {void}
 */
function plusclick(image)
{
    // row ID is same as image, but with 'hour-events' class
    var ID = image.id ;
    var selector = "tr#"  + ID + '.hour-events' ;
    var row = $(selector) ;

    // Toggle image and row
    if (!row.is(":visible") ) {
        image.src = "images/minus.gif";
        row.show() ;
    }
    else {
        image.src = "images/plus.gif";
        row.hide() ;
    }
}

