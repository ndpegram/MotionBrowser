
// Events to execut once document loaded.
$( document ).ready(function() {
    // collapse the timeline table
    timelineDetailsHide() ;
});


/**
 * Delete selected items (with confirmation).
 * @return	{void}		nothing
 */
// TODO: allow error message from delete to be formatted in html. May need to use external library dialogs instead of vanilla alert.

function deleteSelection() {
	// are any items selected?
	var IDs = [] ;
	var numSelected = 0 ;
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
			numSelected++ ;
			IDs.push (checkboxes[i].value) ;
		}
    }

	// No, alert and exit
	if (numSelected == 0) {
		$.post(
			"./js_gettext.php",
			{message: "No items selected for deletion"},
			function(data){
				alert (data + ".") ;
			}
		);
		return false ;
	}

	// Exit if deletion not confirmed.
	$.post(
		"./js_gettext.php",
		{message: "Delete selected items"},
		function(data){
			var theDate ;
			var params ;
			var msg ;

			msg = data + "?" ;

			if(confirm(msg))  {
				// Get date to return to
				theDate = $(".today").html() ;
				// Do the deletion.
				params = {what: "delete", todelete: IDs} ;
				$.post (document.location.href,  params,
					function(data){
						// Enable the line below if debugging.
						alert (data) ;
					}
				)
				.done(function(){
					// Show the page we were on.
					params = {view_date: theDate} ;
					post (document.location, params) ;
				})
				.fail(function(){
					alert ('Error deleting'+'.') ;
				});

			}
		}
	);

}



function select_all(){
	set_all(true);
}

function select_none(){
	set_all(false);
}

function set_all(value){
	var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = value ;
	}
}

function openwindow(url, title, xx, yy)
{
	var wh = open(url, title, 'scrollbars=no,status=no,menubar=no,resizable=no,toolbar=yes,width=' + xx + ',height=' + yy);
	wh.location.href = url;
	if (wh.opener == null) wh.opener = self;
	wh.focus();
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
    var selector = "#"  + ID + '.hour-events' ;
    var row = $(selector) ;

    // Toggle image and row
    if (image.src.indexOf('mais.gif') !== -1) {
        image.src = "./menos.gif";
        row.show() ;
    }
    else {
        image.src = "./mais.gif";
        row.hide() ;
    }
}

// See https://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit
/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param	{string}	path the path to send the post request to
 * @param	{object}	Associative array of the paramiters to add to the url
 * @param	{string}	[method=post] the method to use on the form
 * @return	{void}		nothing
 */

function post(path, params, method='post') {

  // The rest of this code assumes you are not using a library.
  // It can be made less wordy if you use one.
  const form = document.createElement('form');
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  form.submit();
}

/**
 * Hide all timeline detail rows (rows showing the videos).
 * @returns {void} 
 */
function timelineDetailsHide(){
    // Hide all table rows of class 'hour-events'
    $(".hour-events").hide();
}