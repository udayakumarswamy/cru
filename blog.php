<?php include('header.php'); ?>
<?php
/* 
 * Cru Doctrine
 * Home Page
 * Campus Crusade for Christ
 */
session_start();
$email      = $_SESSION['email'];
$m_id  = isset($_GET['m'])       ? $_GET['m']      : '';
$t_id  = isset($_GET['t'])       ? $_GET['t']      : '';
$r_id  = isset($_GET['r'])       ? $_GET['r']      : '';

require_once("function.inc.php");

try {
    //get modules from db
    $modules = array();
    
    //initialize the database object
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
    $db->connect();
	
	$sql = "SELECT * FROM module ORDER BY Ord;";
	
	//execute query and return to module array
    $modules = $db->fetch_array($sql);
	
	$sql_reg = "SELECT * FROM region";
	
	//execute query and return to module array
    $regions = $db->fetch_array($sql_reg);
	
	$e = 0;
	
	$sql_posts = '';
	
	if(($m_id=='' || $m_id=='all') && ($t_id == '' && $r_id == '')){
	
    	$sql_posts = "SELECT InputId input_id FROM response where blog=1 and Response!='' ORDER BY Date DESC";
$e++;
	}else if($m_id!='' && $m_id!='all' && $t_id == '' && $r_id == ''){
		
		$sql_posts = "SELECT InputId input_id FROM response where blog=1 and Response!='' and Module_id = $m_id ORDER BY Date DESC";
		$e++;
	}

	if(($t_id==''|| $t_id=='all') && ($m_id == '' && $r_id == '')){
	
    	$sql_posts = "SELECT InputId input_id FROM response WHERE blog=1 AND Response!='' AND InputID IN (SELECT DISTINCT(question_id) FROM question_topic_relation) ORDER BY DATE DESC
";

	}else if($t_id!='' && $t_id!='all' && $m_id == '' && $r_id == ''){
		
		$sql_posts = "SELECT InputId input_id FROM response WHERE blog=1 AND Response!='' AND InputID IN (SELECT DISTINCT(question_id) FROM question_topic_relation WHERE topic_id = $t_id) ORDER BY DATE DESC
";		
	}
	
	if(($r_id==''|| $r_id=='all') && ($m_id == '' && $t_id == '')){
	
    	$sql_posts = "SELECT InputId input_id FROM response WHERE blog=1 AND Response!='' ORDER BY DATE DESC";

	}else if($r_id!='' && $r_id!='all' && $m_id == '' && $t_id == ''){
		
		$sql_posts = "SELECT InputId input_id FROM response WHERE blog=1 AND Response!='' AND Email IN (SELECT DISTINCT(Email) FROM user WHERE Region = $r_id) ORDER BY DATE DESC";
		
	}
	
	
	 $blog_posts = $db->fetch_array($sql_posts);
	 
	 $unique_blog_posts = array_map("unserialize", array_unique(array_map("serialize", $blog_posts)));

    //$db->close();
	$sql_topic = "SELECT * FROM add_blog_topics";

    $topics = $db->fetch_array($sql_topic);
}

catch (PDOException $e) {
    echo $e->getMessage();
}

?>
<div class="register_banner">
<div class="container ">
<div class="rgstr_bnr_txt">
<h1>Blog</h1>
</div>
</div>
</div>

<div class="container main">
<div class="row">
<div class="col-md-12">
 <div class="row">
 <div class="col-md-12 bread_crumb">
 <a href="#">Home</a> > <a href="#">Blog</a>
 
 </div>
 </div>

 <div class="row">
 <div class="col-md-7">
 <h3>Blog Posts</h3>

 <p>Throughout CruDoctrine there will be opportunities to add content to the CruDoctrine blog post for all of the CruDoctrine users to see. On some pages, there is a blank box to the right of the page for you to share any thoughts that could encourage other users related to the content you are studying.</p>
 <p>Along with these blog boxes, there are some questions in the “So What” sections of each module that include the option to “flag for blog post”. These questions provide you with an opportunity to encourage other CruDoctrine users by sharing what God is doing in your own life. So, be sure to look for the questions with the “flag for blog post” option.</p>
 
 <div class="blog_main">
 <?php 
	
	$i = 1;
	
	foreach($unique_blog_posts as $postid){
		
		$qstn = get_blog_post_title($db, $postid['input_id']);
		
		$inputid = $postid['input_id'];
		
		$sql_blog_data = "SELECT * from response where Blog=1 and response!='' and InputId = ".$inputid." ORDER BY Date DESC";
		
		//echo $sql_blog_data;exit;
		
    $blog_post_data = $db->fetch_array($sql_blog_data);
		//print_r($blog_post_data);exit;
		
		?>
 <h6 class="ylw_link"><?php echo $qstn; ?></h6>
 
 <?php foreach($blog_post_data as $bposts){
			
			$yrdata= strtotime($bposts['Date']);
			?>
			<?php $i++;}
echo "<hr />";
//exit;
}?>
 <p class="bold_txt">Spend a few minutes writing a summary of module 10 - The Great Commission. And if you choose to, you can click the blog post below to add it to the CruDoctrine blog so that the community can see it.</p>
 <div class="col-md-12">
 <div class="blog_inner">
 <div class="blog_lft"><img src="images/blog_photo.jpg"/></div>
 <div class="blog_rgt">
 <div class="blog_top"><span class="pull-left"><?php echo getUserNameByID_blog($db, $bposts['Email']); ?></span> <span class="pull-right">August 02, 2014  08:42:14</span></div>
 <p class="blog_txt">Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. </p>
 </div>
 </div>
 <div class="comments_main">
 <div class="cmnts_top">15 Comments <a href="#" class="ylw_link">View all</a> <a class="ylw_link" data-toggle="collapse" href="#postacomment" aria-expanded="false" aria-controls="collapseExample">Post a comment</a>
 <div class="collapse"   aria-expanded="false" id="postacomment">
<div class="row">
          <div class="col-md-12">
<br>
 <h3 class="modal-title ylw_link light_font" style="margin:10px 0 10px 0;">Post a Comment</h3>

          
 <input type="text" class="form-control full-width" placeholder="Name"/>           
           
  <input type="text" class="form-control full-width" placeholder="Email"/>         
<textarea placeholder="Please enter your comments here" class="form-control full-width"></textarea>    
    
<button class="btn btn-warning continu_btn no_left pull-right" style="margin:20px 0 40px 0;" type="submit">Submit</button>
<br><br>
          </div>
          
          </div>
</div>
 
 </div>
 
 
  
 <div class="cmnts_btm"><div class="blog_inner">
 <div class="blog_lft"><img src="images/blog_photo.jpg"/></div>
 <div class="blog_rgt">
 <div class="blog_top"><span class="pull-left">Eston A</span> <span class="pull-right">August 02, 2014  08:42:14</span></div>
 <p class="blog_txt">Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. </p>
 </div>
 </div></div>
 
 
 </div>
 
 
 
 
 
 </div>
 </div>
 
 

 <h6 class="ylw_link">Module 10: Great Commission</h6>
 <p class="bold_txt">Spend a few minutes writing a summary of module 10 - The Great Commission. And if you choose to, you can click the blog post below to add it to the CruDoctrine blog so that the community can see it.</p>
 <div class="col-md-12">
 <div class="blog_inner">
 <div class="blog_lft"><img src="images/blog_photo.jpg"/></div>
 <div class="blog_rgt">
 <div class="blog_top"><span class="pull-left">Eston A</span> <span class="pull-right">August 02, 2014  08:42:14</span></div>
 <p class="blog_txt">Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. </p>
 </div>
 </div>
 <div class="comments_main">
 <div class="cmnts_top">15 Comments <a href="#" class="ylw_link">View all</a> <a href="#" class="ylw_link">Post a comment</a></div>
 <div class="cmnts_btm"><div class="blog_inner">
 <div class="blog_lft"><img src="images/blog_photo.jpg"/></div>
 <div class="blog_rgt">
 <div class="blog_top"><span class="pull-left">Eston A</span> <span class="pull-right">August 02, 2014  08:42:14</span></div>
 <p class="blog_txt">Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. Catat. Aruptaquos estis que dolorum fugia ipsapita cus ad et quatem ut officillauta dest quid ut eati omnis dipsum facipid essitat facid molupta delecuptis nus dollorro berspe iumenit archil enimus. </p>
 </div>
 </div></div>
 
 
 </div>
 
 
 
 
 
 </div>
 </div>
 
 
 </div>
 
 <div class="col-md-4 pull-right right_pnl">
 
 <div class="panel panel-warning float_div">
            <div class="panel-heading">
              <h3 class="panel-title">View by</h3>
            </div>
            <div class="panel-body">
              <div aria-multiselectable="true" role="tablist"  class="panel-group" id="accordion" >
      
      <div class="panel panel-default">
        <div id="headingOne" role="tab" class="panel-heading">
          
            <a aria-controls="collapseOne" aria-expanded="false" href="#collapseOne" data-parent="#accordion" data-toggle="collapse" class="collapsed">
            Module 
            </a>
            <a href="#" class="static_link"><span class="underline">(Most recent)</span></a>
          
        </div>
        <div aria-labelledby="headingOne" role="tabpanel" class="panel-collapse collapse" id="collapseOne" aria-expanded="false" style="height: 0px;">
          <div class="panel-body">
            <ul class="module_inner">
			<?php foreach($modules as $module){?>
             <li><a href="?m=<?php echo $module['ID'];?>" id="m_<?php echo $module['ID'];?>"><?php echo $module['Name'];?></a></li>
			<?php }?>
            </ul>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div id="headingTwo" role="tab" class="panel-heading">
          
            <a aria-controls="collapseTwo" aria-expanded="false" href="#collapseTwo" data-parent="#accordion" data-toggle="collapse" class="collapsed">
            Topic  
            </a>
                      <a href="#" class="static_link"><span class="underline">(Most recent)</span></a>

        </div>
        <div aria-labelledby="headingTwo" role="tabpanel" class="panel-collapse collapse" id="collapseTwo" aria-expanded="false" style="height: 0px;">
          <div class="panel-body">
            <ul class="module_inner">
			<?php foreach($topics as $topic){ ?>
             <li><a href="?t=<?php echo $topic['id'];?>" id="t_<?php echo $topic['id'];?>"><?php echo $topic['topic_title'];?></a></li>
			 <?php }?>
             
            </ul>
          </div>
        </div>
      </div>
      
      
      
       
      
      
      <div class="panel panel-default">
        <div id="headingThree" role="tab" class="panel-heading">
          
            <a aria-controls="collapseThree" aria-expanded="false" href="#collapseThree" data-parent="#accordion" data-toggle="collapse" class="collapsed">
            Region  
            </a>
            <a href="#" class="static_link"><span class="underline">(Most recent)</span></a>
        </div>
        <div aria-labelledby="headingThree" role="tabpanel" class="panel-collapse collapse" id="collapseThree" aria-expanded="false" style="height: 0px;">
          <div class="panel-body">
            <ul class="module_inner">
			<?php foreach($regions as $region){ ?>
             <li><a href="?r=<?php echo $region['ID'];?>" id="r_<?php echo $region['ID'];?>"><?php echo $region['Name'];?></a></li>
			<?php }?>
            </ul>
          </div>
        </div>
      </div>
            
      
      
       
      
      
      
       
      
      
      
      
      
      
      
      
      
      
      
    </div>
            </div>
          </div>
 </div>
 
 </div>

</div>
</div>

 
 


</div>



<footer>® 2015 Cru All Rights Reserved</footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/docs.min.js"></script>
    <script src="js/cru_custom.js"></script>
 <script type="text/javascript">
 $(window).scroll(function() {
		
        var styledDiv = $('footer');
         var targetScroll = $('.float_div').position().top;
         var currentScroll = $('html').scrollTop() || $('body').scrollTop();
var footertotop=$('footer').position().top;
var adtobottom=$('.float_div').position().bottom;


     if (currentScroll>600) {
      $('.float_div').css({position:"fixed ",top:"10px ", width:"302px"});


    } else {
      if (currentScroll<=600) {
       $('.float_div').css({position:"relative ",top:""});
 
      }
    }
    
    if ( $(window).scrollTop() + $(window).height() > footertotop) {

$('.float_div').css('margin-top',  0);
}

else  {
$('.float_div').css('margin-top', 0);
}

		});
 $(document).ready(function() {
	 

	 
	 
var showChar = 190;
var ellipsestext = "...";
var moretext = "expand";
var lesstext = "collapse";
var content = $('.blog_txt').html();
 if (content.length > showChar) {
    var show_content = content.substr(0, showChar);
    var hide_content = content.substr(showChar, content.length - showChar);
    var html = '<span class="cont_collapsed">' + show_content 
                + ellipsestext
                + '</span><span class="cont_expanded">'
                + content
                + '</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="cont_collapsed ylw_link moretext">'
                + moretext
                + '</a><a href="javascript:void(0)" class="cont_expanded ylw_link lesstext">' 
                + lesstext
                + '</a>'
    $('.blog_txt').html(html);
}

$.each($(".limitedTextSpace"), function(index, value) {
        showMoreLess(value);
    });

    $(".cont_expanded").hide();
    $(".cont_expanded, .cont_collapsed").click(function() {
        $(this).parent().children(".cont_expanded, .cont_collapsed").toggle();
    });
	 
    });
   
 
 </script>
 	
  </body>
</html>
