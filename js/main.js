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

