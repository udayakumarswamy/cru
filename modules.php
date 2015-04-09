<?php
/*
 * Cru Doctrine
 * Modules
 * Campus Crusade for Christ
 */
try {
  //ensure user authentication
  $auth = false;

  session_start();
  if(isset($_SESSION['email'])){
    $auth = true;
  }

  if(!$auth){
    header('Location: /#login');
  }

  require_once("config.inc.php"); 
  require_once("Database.singleton.php");
  require_once("function.inc.php"); 

  //get session values
  $email  = $_SESSION['email'];
  $type   = $_SESSION['type'];

  //determine page content
  $mod = isset($_GET['m'])        ? $_GET['m']        : '';
  $sec = isset($_GET['s'])        ? $_GET['s']        : '';
  $pag = isset($_GET['p'])        ? $_GET['p']        : '';
  $req = isset($_GET['request'])  ? $_GET['request']  : '';

  //module, section, and page arrays 
  $module     = array();
  $section    = array();
  $page       = array();

  //initialize the database object
  $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
  $db->connect();

  $transitionType = '';

  if ($mod != -1) {
    //transition to the next module
    if ($mod != '') {
        if($req != '') {
            //page title
            $title = 'Module '.$mod.' '.$req;
            
            //page type
            $transitionType = $req;
        } 
        else {
            //get module information
            $sql =  "SELECT m.ID, m.Number, m.Name, m.Ord, m.Descr, m.Banner, m.FrontImg, s.ID AS FirstSection
                     FROM module m
                     INNER JOIN section s ON s.ModuleId = m.ID
                     WHERE m.ID = ".(int)$mod."
                     AND s.Ord = 0";

            $module = $db->query_first($sql);
            $module['Order'] = $module['Ord'];

            if($db->affected_rows > 0) {
              //page title
              $title = 'Module '.$module['Number'];

              //page type
              $transitionType = MODULE;
            } 
            else {
              header('Location: /work');
            }
        }
    //transition to the next section
    } 
    elseif ($sec != '') {
        //get section, module, & first page information
        $sql = "SELECT s.*, m.Number, m.Name AS ModuleName, m.Ord AS ModuleOrder, m.Banner, p.ID AS PageId, p.Ord AS PageOrder, p.Visibility, p.Type
                FROM section s 
                INNER JOIN module m on s.ModuleId = m.Id 
                INNER JOIN page p on s.ID = p.SectionId 
                WHERE s.ID = ".(int)$sec.
                " ORDER BY p.Ord ASC";

        $result = $db->query_first($sql);
        
        //module information
        $module['ID']       = $result['ModuleId'];
        $module['Number']   = $result['Number'];
        $module['Name']     = $result['ModuleName'];
        $module['Order']    = $result['ModuleOrder'];
        $module['Banner']   = $result['Banner'];
        
        //section information
        $section['ID']      = $result['ID'];
        $section['Title']   = $result['Title'];
        $section['Order']   = $result['Ord'];
        
        //page information
        $page['ID']         = $result['PageId'];
        $page['Order']      = $result['PageOrder'];
        $page['Visibility'] = $result['Visibility'];
        $page['Type']       = $result['Type'];
        
        //page title
        $title = 'Module '.$module['Number'];
        
        //page type
        $transitionType = PAGE;

    //transition to the next page
    } 
    elseif ($pag != '') {
        //get page, section, & module information
        $sql = "SELECT p.*, s.ModuleId, s.Title AS SectionTitle, s.Ord AS SectionOrder, m.Number, m.Name AS ModuleName, m.Ord AS ModuleOrder, m.Banner
                FROM page p
                INNER JOIN section s on p.SectionId = s.ID
                INNER JOIN module m on s.ModuleId = m.Id
                WHERE p.ID = ".(int)$pag;

        $result = $db->query_first($sql);

        //module information
        $module['ID']       = $result['ModuleId'];
        $module['Number']   = $result['Number'];
        $module['Name']     = $result['ModuleName'];
        $module['Order']    = $result['ModuleOrder'];
        $module['Banner']   = $result['Banner'];

        //section information
        $section['ID']      = $result['SectionId'];
        $section['Title']   = $result['SectionTitle'];
        $section['Order']   = $result['SectionOrder'];

        //page information
        $page['ID']         = $result['ID'];
        $page['Order']      = $result['Ord'];
        $page['Visibility'] = $result['Visibility'];
        $page['Type']       = $result['Type'];

        //page title
        $title = 'Module '.$module['Number'];

        //page type
        $transitionType = PAGE;
    } 
    else {
        //page title
        $title = 'Modules';

        //page type
        $transitionType = 'directory';
    }

    //ensure user has proper access to loading page
    $auth = false;

    if ($transitionType == PAGE && ($type > COACH && $type != OTHER)) {
      //fetch user progress to validate loading page
      $sql = "SELECT pr.Status, p.Ord AS Page, s.Ord AS Section, m.Ord AS Module
              FROM progress pr
              INNER JOIN page p ON pr.ID = p.ID
              INNER JOIN section s ON p.SectionId = s.ID
              INNER JOIN module m ON s.ModuleId = m.ID
              WHERE pr.Email = '".$db->escape($email)."'
              AND pr.Type = '".PAGE."'
              AND pr.ID = ".$page['ID'];

      $progress = $db->query_first($sql);

      if($progress['Module'] >= $module['Order']) {
          if($progress['Section'] >= $section['Order']) {
              $auth = $progress['Page'] >= $page['Order'] ? true : false;
          }
      }
    } 
    else {
      //fetch user progress to validate loading page
      $sql = "SELECT pr.Status, m.Ord AS Module
              FROM progress pr
              INNER JOIN module m ON pr.ID = m.ID
              WHERE pr.Email = '".$db->escape($email)."'
              AND pr.Type = '".MODULE."'
              AND pr.ID = ".$module['ID'];

      $progress = $db->query_first($sql);

      //seed progress with the first page
      if ($db->affected_rows > 0) {
        $auth = $progress['Module'] >= $module['Order'] ? true : false;
      } 
      else {
        //get the first page ID
        $sql     = "SELECT p.ID AS Page
                    FROM page p
                    INNER JOIN section s ON p.SectionId = s.ID
                    INNER JOIN module m ON s.ModuleId = m.ID
                    WHERE (p.Ord = 0 AND s.Ord = 0 AND m.Ord = ".$module['Order'].");";

        $result  = $db->query_first($sql);

        //insert the first page progress record
        $data = array();
        $data['Email']  = $email;
        $data['ID'] = $result['Page'];
        $data['Type'] = PAGE;
        $data['Status'] = STARTED;
        $data['Update'] = date( 'Y-m-d' );
        //execute query
        $db->insert("progress", $data);

        //insert the first module progress record
        $data = array();
        $data['Email']  = $email;
        $data['ID'] = $module['ID'];
        $data['Type'] = MODULE;
        $data['Status'] = STARTED;
        $data['Update'] = date( 'Y-m-d' );
        //execute query
        $db->insert("progress", $data);

        $auth = true;
      }
    }

    if(!$auth) {
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
  }
  else {
    header('Location: /work');
  }
} 
catch (PDOException $e) {
  echo $e->getMessage();
}

$content = $transitionType.'.php';


//content
?>
<?php include('header.php'); ?>

<?php include($content); ?>




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
    
    </script>
  </body>
</html>
