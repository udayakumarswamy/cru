<?php
/*
 * Cru Doctrine
 * Header
 * Campus Crusade for Christ
 */

if (!isset($_SESSION)) {
  ini_set('session.gc_maxlifetime', 7200);
  ini_set('session.gc_probability', 1);
  ini_set('session.gc_divisor', 100);
  session_start();
}

//page title
$title = 'Welcome';

//check for session
$loggedin    = false;
$email       = '';
$fname       = '';
$lname       = '';
$type        = '';
$status      = '';
$userMessage = '';

if(isset($_SESSION['email'])) {
    $loggedin   = true;
    $email      = $_SESSION['email'];
    $fname      = isset($_SESSION['fname']) ? $_SESSION['fname']    : '';
    $lname      = isset($_SESSION['lname']) ? $_SESSION['lname']    : '';
    $type       = isset($_SESSION['type']) ? $_SESSION['type']      : '';
    $status     = isset($_SESSION['status']) ? $_SESSION['status']  : '';
    $search_word     = isset($_SESSION['search_word']) ? $_SESSION['search_word']  : '';
    $userMessage= 'Welcome '.$fname.'!';
} 

require_once("config.inc.php"); 
require_once("Database.singleton.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>CRU</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
     <link href="css/custom_styles.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,300,300italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Custom styles for this template -->
    <link href="css/carousel.css" rel="stylesheet">

	
	
  </head>
<!-- NAVBAR
================================================== -->
  <body>
     
 <div class="header_top">
 <div class="container">
 <ul class="top_links">
<?php  if($loggedin) { ?>
 <li><img src="images/admin_img.jpg"></li>
 <li><a href="#">Admin</a></li>
 <li><a href="logout.php" >Logout</a></li>
 <?php } else {
	?>
  <li><a href="about.php">About</a></li>
    <li><a href="#">Register</a></li>
	<li><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>
	<?php
} ?>
 </ul>
 <div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> </button>
        <h3 class="modal-title">Please Login</h3>
      </div>
      <div class="modal-body log_in">
         <div class="row">
        <div class="col-md-9 center-block" id="login">
		<form id="formLogin" action="login.php" method="post">
		<div id="errors"><?php echo $errors; ?></div>
        <input type="email" id="email" class="form-control" placeholder="Username" >
        <input class="form-control" type="password" id="password" placeholder="Password" >
                <button type="submit" class="btn btn-primary" id="formLoginLogin">Login</button>
                <a href="#" class="frgt_pwrd"  data-toggle="modal"  data-target="#forgotpassword">Forgot Password?</a>
        </form>
        </div>
        
        <div class="col-md-12 top_margin">
        <div class="log_dvdr"></div>
        <div class="log_or">Or</div>
        </div>
<div class="col-md-12">
  <div class="row">
        <div class="col-md-9 center-block">
        <button type="submit" class="btn btn-lg fb_btn btn-primary btn-block">Login using <span>Facebook</span></button>
        </div>
        </div>
        </div>
        </div> 
        
        
      </div>
       
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="forgotpassword">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> </button>
        <h3 class="modal-title">Forgot Password ?</h3>
      </div>
      <div class="modal-body log_in">
         <div class="row">
        <div class="col-md-9 center-block">
        <input type="email" class="form-control" placeholder="Username" >
<input type="email" class="form-control" placeholder="Email" >
                <button type="submit" class="btn btn-primary">Submit</button>
                
        
        </div>
        
         
 
        </div> 
        
        
      </div>
       
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>


 </div>
 </div>

<div class="header"><div class="container">
<a href="index.php" class="cru_logo" ><img src="images/cru_logo.jpg"/></a>
<a href="index.php" class="pull-right core_logo " ><img src="images/coredoctrine_logo.jpg"/></a>
</div>
<?php
          if($loggedin) {
			  ?>
<nav class="my_nav">
	
        <div class="container">
        <div class="nav_close">
        <a href="#">Close</a>
        </div>
        
    <div class="pull-right search_cont">
                      <input class="form-control" placeholder="Search" type="text">
                      <button class="btn btn-warning" type="submit">Go</button>
                      </div>
          <div class="nav_inner" id="navbar">
            <ul class="nav navbar-nav">
              <li class="active"><a href="index.php">Home</a></li>
             <li><a href="my_profile.php"> MY PROFILE</a></li>
              <li><a href="#">MY WORK</a></li>
               <li><a href="#">BLOG</a></li>
                <li><a href="#">RESOURCES</a></li>
                 <li><a href="about.php">ABOUT</a></li>
              
            </ul>
                      

          </div><!--/.nav-collapse -->
          
          
        </div><!--/.container-fluid -->
      </nav>
	  <?php } ?>
</div>

<script type="text/javascript">

  //validate form submission
  $('#login form').submit(function() {
    var submit = false;
    var errors = '';

    if ($('#login #email').val().length == 0) {
      $('#login #email').css('border-color', 'orange');
      errors += '<div>Please enter your email.</div>';
    }
    else {
      $('#login #email').css('border-color', '').siblings('a').css('display','');
    }

    if ($('#login #password').val().length == 0) {
      $('#login #password').css('border-color', 'orange');
      errors += '<div>Please enter your password.</div>';
    }
    else {
      $('#login #password').css('border-color', '').siblings('a').css('display','');
    }

    if (errors !== '') {
      $('#login #errors').html(errors);
      submit = false;
    } 
    else {
      submit = true;
    }

    if(submit) {
      $.ajax({
        url: 'login.php',
        type: 'POST',
        data: {
          ajax       : true,
          submit     : true,
          email      : $('form #email').val(),
          password   : $('form #password').val(),
          rememberMe : $('form #rememberMe').val()
        },
        dataType: "html",
        success: function(msg) {
			console.log(msg);
          if(msg != 'error') {
            $('#loginbox  #login').click();
            $('#header').html($(msg).find('#header').html());
            window.location.reload(true);
          } 
          else {
            $('#login #errors').html('<div>Login failed. Please check your email and password.</div>')
          }
        }
      });
    }
    return false;
  });

  //jquery class interaction states
  $('button').addClass('ui-state-default');

  $('.ui-state-default').hover(
    function(){
      $(this).addClass("ui-state-hover");
    },
    function(){
      $(this).removeClass("ui-state-hover");
    }
  );
</script>
