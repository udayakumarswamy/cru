$(document).ready(function() {
        	 $('.frgt_pwrd').click(function() {
		 $('#myModal .close').trigger("click");
        
    });
    
	$('.nav_toggle, .nav_close a').click(function() {
        $('.my_nav').toggleClass('menu_open')
    });
	
	
	 $(".slide_img img").each(function() {  
   var imgsrc = $(this).attr("src");
   $(this).parent().css('background-image', 'url(' + imgsrc + ')');;
   
 //  alert(imgsrc);
  });  
	
	
	 
    });
   