/**
 * @author tapiwa
 */
function addMega(){ 
	$(this).addClass("hovering"); 
} 

function removeMega(){ 
	$(this).removeClass("hovering"); 
}

var megaConfig = {     
		interval: 300, 
		sensitivity: 4,
		over: addMega,
		timeout: 300, 
		out: removeMega 
};

var submenuConfig = {
		interval: 50, 
		sensitivity: 4,
		over: addMega,
		timeout: 50, 
		out: removeMega 
};
$(document).ready(function(){
	 $("li.mega").hoverIntent(megaConfig);
	 $("span.submenu").hoverIntent(submenuConfig);
});