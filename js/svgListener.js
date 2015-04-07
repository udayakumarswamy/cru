/****			Adding Event Listeners			****/
$(document).ready(function(){
	svgAddListener();
});
function svgAddListener(){
	var mysvg = document.getElementById("mySvg");
	var svg = mysvg.contentDocument; // Load SVG Document
	var gTags = svg.getElementsByTagName("g"); // fetching all g tags
	var glen = gTags.length;				// no of g tags
	var g_color = "#d3d3d3";
	
	/****          Read all the states(<path> tags inside <g> tag) Add Event Listener  ****/
	for (var i = 0,la_circle; i < gTags.length; i++){

		var l_gTag = gTags[i];
		var la_path = l_gTag.getElementsByTagName("path"); // fetching path(states map) tags from current g tag

		if (l_gTag.id == "dc"){
			la_circle = l_gTag.getElementsByTagName("circle"); // fetching circle(DC state only ) tags from current g tag
			addEventListeners(la_circle[0]);
		}
		for(var i_path = 0 ; i_path < la_path.length; i_path++){
			g_color = "#d3d3d3";
			addEventListeners(la_path[i_path]);
		}
	}
	/****     End of Add Event Listener         ****/
}

/****			Highlighting State and infoBox when mouse moved hoovered	****/
function mouseOver(){
	var l_region ,la_region;
	var p_gTag =this.parentNode;	// Get parent node , g-tag
	var p_title = p_gTag.getElementsByTagName("title"); // fetching State name
	var panel_content ;
	g_color = this.style.fill;
	this.style.fill = "#ffc427";//"#fe000f";
	l_region = (this.className.animVal).split(" ");
	la_region = getRegionData(l_region[0]);
	l_region = la_region[0];
	p_title = p_title[0].textContent;
	$("#infobox").show();
	panel_content = "<b>"+p_title+"</b>";
	panel_content += "<br>"+la_region[0];
	panel_content += ":<br>"+la_region[1];
	$("#st").html(panel_content);
}

/****			Un-highlight  State and infoBox when mouse moved out	****/
function mouseOut(){
	
	var infoBox = document.getElementById("infoCanvs");
	var infoBox_ctx = infoBox.getContext("2d");
	
	this.style.fill = "#d3d3d3";
	$("#infobox").hide();
	//$("#infobox").slideToggle(100);
	$("#st").html('');
		
}

/**** 			Reading state infomation when where mouse clicked			****/
function mouseClick(){
	var p_gTag =this.parentNode;	// Get parent node , g-tag
	var p_title = p_gTag.getElementsByTagName("title"); // fetching State name
	var l_region = (this.className.animVal).split(" ");
	var la_region = getRegionData(l_region[0]);
	
	p_title = p_title[0].textContent;
	document.getElementById("rgName").value= la_region[0];
}

/****			AddEventListener 		****/
function addEventListeners(l_node){
	l_node.addEventListener("mouseover", mouseOver,false);
	l_node.addEventListener("mouseout", mouseOut,false);
	l_node.addEventListener("click",mouseClick,false);
}



function getRegionData(cName){

	switch(cName) {
		case "neRegion":
			return g_neRegion;
			break;
		case "maRegion":
		   return g_maRegion;
			break;
		 case "msRegion":
		   return g_msRegion;
			break;
		case "seRegion":
		   return g_seRegion;
			break;
		 case "glRegion":
		   return g_glRegion;
			break;
		case "umRegion":
		   return g_umRegion;
			break;
		 case "gpRegion":
		   return g_gpRegion;
			break;
		case "rrRegion":
		   return g_rrRegion;
			break;
		case "gnRegion":
		   return g_gnRegion;
			break;
		case "psRegion":
			return g_psRegion
			break;
	}
}

