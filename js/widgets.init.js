/**
 * widget.init.js
 * 
 * Common behaviour for initializing widgets
 * 
 * @author tapiwa@munzwa.tk
 * 
 */
$(document).ready(function(){
	$('.autofocus:first').focus(); //put focus on the first element with 'autofocus' class
	$('table.datagrid tr:odd').addClass("rowdark"); //set color for every odd row
});