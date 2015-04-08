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
$cm_id  = isset($_GET['ac'])       ? $_GET['ac']      : '';
$t  = isset($_GET['t'])       ? $_GET['t']      : '';
require_once("function.inc.php");
?>
<?php 

try {
    //get modules from db
    $modules = array();
    $topics = array();	
	$additional_meterial = array();
	$additional_content = array();
		
    
    //initialize the database object
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
    $db->connect();
    
    $topic_name = '';
	echo $m_id;
    if(isset($_GET['m']) || isset($_GET['ac'])){
        if($m_id !=""){
            $mod_id = $m_id;
        }
        else{
            $mod_id = $cm_id;
        }
        $mod_query = "select name from module where id = $mod_id";
        $m_value = $db->fetch_array($mod_query);
        foreach($m_value as $val){
            $topic_name = $val['name'];
        }
    }
    
    if(isset($_GET['t'])){
        $t_id = $_GET['t'];
        $top_query = "select topic_title from add_resource_topic where id = $t_id";
        $t_value = $db->fetch_array($top_query);
        foreach($t_value as $val_t){
            $topic_name = $val_t['topic_title'];
        }
    }
	
	$sql_topic = "SELECT * FROM add_resource_topic";

    $topics = $db->fetch_array($sql_topic);
	
	$sql = "SELECT * FROM module ORDER BY Ord;";

    $modules = $db->fetch_array($sql);
	if($m_id=='all'){
		
		
    	$sql_posts = "SELECT * FROM additional_resource WHERE type='meterials'";
		
		$additional_meterial = $db->fetch_array($sql_posts);
		
		$additional_content = array();

	}else if($m_id!='all' && $m_id!=''){
		$sql_posts = "SELECT * FROM additional_resource WHERE  type='meterials' and module_id = $m_id";
		
		$additional_meterial = $db->fetch_array($sql_posts);
		
		$additional_content = array();
		
	}
	if($cm_id=='all'){
		
    	$sql_content = "SELECT * FROM additional_resource WHERE type='content'";
		
		$additional_content = $db->fetch_array($sql_content);
		
		$additional_meterial = array();
		
	}else if($cm_id!='all' && $cm_id!=''){
		
		$sql_content = "SELECT * FROM additional_resource WHERE type='content' and module_id = $cm_id";
		
		$additional_content = $db->fetch_array($sql_content);
		
		$additional_meterial = array();
		
			}
	
	if($t=='all'){
		
		$additional_content = array();
		
    	$sql_content_topic_wise = "SELECT * FROM additional_resource WHERE type='content' AND id IN (SELECT distinct(resource_id) FROM add_resource_topic_question_relation)";
		
		$additional_content = $db->fetch_array($sql_content_topic_wise);
		
		$additional_meterial = array();
		
	}else if($t!='all' && $t!=''){
		
		$additional_content = array();
		
		$sql_content_topic_wise = "SELECT * FROM additional_resource WHERE type='content' AND id IN (SELECT resource_id FROM add_resource_topic_question_relation WHERE topic_id = '$t')";
		
		$additional_content = $db->fetch_array($sql_content_topic_wise);
		
		$additional_meterial = array();
		
			}
	
    //$db->close();
}
catch (PDOException $e) {
    echo $e->getMessage();
}

?>
<div class="register_banner">
<div class="container ">
<div class="rgstr_bnr_txt">
<h1>Resources</h1>
</div>
</div>
</div>

<div class="container main">
<div class="row">
<div class="col-md-12">
 <div class="row">
 <div class="col-md-12 bread_crumb">
 <a href="#">Home</a> > <a href="#">Resources</a>
 
 </div>
 </div>

 <div class="row">
 <div class="col-md-7">
 <h3>Resources</h3>

<p> Want more? The CruDoctrine additional resource page is an ever growing resource for you to continually refer back to in order to dig deeper. There are two ways that we want to resource you.</p>
<p>First, you’ll notice downloadable pdf’s for small group bible studies or discipleship appointments. These are designed to either be used alongside CruDoctrine as a discussion tool for students who worked through the module already or as an independent bible study. They are meant to take the module’s topic and study it from one or a few passages.</p>
<p>Second, we will continually be adding additional content that will take each module a bit further. So, as we come across blogs, videos, and articles, we will be dumping those resources in here for you to continue to grow and for you to use to minister to those in your world.</p>
<p>Enjoy.</p>

  <div class="blog_links">
  <ul>
  
   <?php 
	foreach($additional_meterial as $post){
		
		//$yrdata= strtotime($post['Date']);upload/resource/1
		
		?>
		
  <li><?php echo $post['title'];?>  <?php if($post['file_path']!=''){?><a href="../upload/resource/<?php echo $post['module_id'];?>/<?php echo $post['file_path'];?>" target="_new" >View/Download<br /><?php }?></a></li>
	<?php } ?>
  </ul>
  
  </div>
 
 
 
  
 
 
 </div>
 


 <div class="col-md-4 pull-right right_pnl">
 
 <div class="panel panel-warning">
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
			<?php foreach($modules as $module){ ?>
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
			<?php } ?>
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
             <li><a href="#">Doctrine</a></li>
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

 	
  </body>
</html>
