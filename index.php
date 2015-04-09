<?php include('header.php'); ?>
<?php if($loggedin) {
	?>
<?php
try {
    //get modules from db
    $modules = array();
    
    //initialize the database object
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
    $db->connect();

    $sql = "SELECT * FROM module ORDER BY Ord;";

    //execute query and return to module array
    $modules = $db->fetch_array($sql);

    //fetch user's current module
    $sql = "SELECT m.Ord AS Module
            FROM progress pr
            INNER JOIN module m ON pr.ID = m.ID
            WHERE pr.Email = '".$db->escape($email)."'
            AND pr.Type = '".MODULE."'
            ORDER BY m.Ord DESC
            LIMIT 1;";
            
    $progress = $db->query_first($sql);
    $currentModule = $progress['Module'];

    $db->close();
}
catch (PDOException $e) {
    echo $e->getMessage();
}

?>	
	<!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
        <li data-target="#myCarousel" data-slide-to="3"></li>
        <li data-target="#myCarousel" data-slide-to="4"></li>
        <li data-target="#myCarousel" data-slide-to="5"></li>
        <li data-target="#myCarousel" data-slide-to="6"></li>
        <li data-target="#myCarousel" data-slide-to="7"></li>
        <li data-target="#myCarousel" data-slide-to="8"></li>
        <li data-target="#myCarousel" data-slide-to="9"></li>
       </ol>
      
       
       
      <div class="carousel-inner" role="listbox">
      <div class="container slide_controls">
       <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
 
      </a>
      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
   
      </a>
      </div>
	  
	  
	  <?php
	  if(count($modules) > 0) {
		  foreach ($modules as $module) {
                    if($module['Number'] > 0) {
		  ?>
        <div class="item <?php echo ($module['Number'] == 1) ? 'active' : '' ; ?>">
        <div class="slide_img">
          <img src="<?=$module['FrontImg'];?>" alt=" " >
        </div>
          <div class="container banner_txt">
          <div class="col-md-10"><?=$module['Descr'];?><br>
          <a href="modules.php?m=<?=intval($module['Number']);?>" class="btn btn-warning continu_btn">Continue to Module <?=intval($module['Number']);?></a>
          </div>
          
          </div>
          
        </div>
	  <?php
		  }
		  }
	  }
	  ?>
      </div>
      
    </div><!-- /.carousel -->
	<?php
} else {
	include('welcome.php');
} ?>


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
