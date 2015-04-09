<?php
/*
 * Cru Doctrine
 * Modules - Progress
 * Campus Crusade for Christ
 */

//get values
session_start();
$email      = isset($_SESSION['email'])                                       ? $_SESSION['email']   : '';
$type       = isset($_SESSION['type'])                                        ? $_SESSION['type']    : '';
$submit     = isset($_POST['submit'])                                         ? true                 : false;
$assessment = (isset($_POST['assessment']) && $_POST['assessment'] == 'true') ? true                 : false;
$isSection  = (isset($_POST['isSection'])  && $_POST['isSection']  == 'true') ? true                 : false;
$answer     = isset($_POST['answer'])                                         ? $_POST['answer']     : 0;
$pageId     = isset($_POST['pageId'])                                         ? $_POST['pageId']     : 0;
$sectionId  = isset($_POST['sectionId'])                                      ? $_POST['sectionId']  : 0;
$moduleId   = isset($_POST['moduleId'])                                       ? $_POST['moduleId']   : 0;
$pageOrd    = isset($_POST['pageOrd'])                                        ? $_POST['pageOrd']    : 0;
$sectionOrd = isset($_POST['sectionOrd'])                                     ? $_POST['sectionOrd'] : 0;
$moduleOrd  = isset($_POST['moduleOrd'])                                      ? $_POST['moduleOrd']  : 0;
$cur_date   = date( 'Y-m-d' );
$errors     = isset($_POST['errors'])                                         ? $_POST['errors']     : '';

require_once("config.inc.php"); 
require_once("Database.singleton.php");
require_once("function.inc.php"); 
require_once("swift/lib/swift_required.php");

//initialize the database object
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
$db->connect();
//assessment pages
if($assessment) {
  //if this is a section, then fetch the first page
  if ($isSection) {
    $sql = "SELECT p.ID AS Page
            FROM page p
            WHERE p.Ord = 0 
            AND p.SectionId = ".(int)$answer;

    $page = $db->query_first($sql);
    if($db->affected_rows > 0) {
      $answer = $page['Page'];
    }
  }
  //verify that the answer page is incomplete
  $sql = "SELECT Status 
          FROM progress 
          WHERE ID = ".(int)$answer."
          AND Email = '".$db->escape($email)."'
          AND TYPE = '".$db->escape(PAGE)."'";
  //execute query 
  $newPageStatus = $db->query_first($sql);
  if($db->affected_rows > 0) {
    //return the incorrect answer page
    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".PHP_EOL;
    echo '<next>'.PHP_EOL;
    echo    '<type>'.PAGE.'</type>'.PHP_EOL;
    echo    '<id>'.$answer.'</id>'.PHP_EOL;
    echo '</next>'.PHP_EOL;
  }
  else {
    $submit = true;
  }
}

//determine the next page
if($submit) {
  //mark current page complete
  $data = array();
  $data['Status'] = COMPLETE;
  $data['Update'] = $cur_date;
    
  //execute query
  $db->update("progress", $data, "Email = '".$db->escape($email)."' AND ID = ".(int)$pageId." AND TYPE = '".$db->escape(PAGE)."'");

  //1. first attempt to get the next page
  //   select all pages that have an Ord > than the existing Ord, in the current section and module, and that should be visible to the current user
  $next = null;
  $visibility = getVisibilityClause($type);

  $sql = "SELECT p.ID AS Page, p.Visibility AS Visibility, s.ID AS Section, m.ID AS Module
          FROM page p
          INNER JOIN section s ON p.SectionId = s.ID
          INNER JOIN module m ON s.ModuleId = m.ID
          WHERE (p.Ord > ".$pageOrd." AND s.ID = ".$sectionId." AND m.ID = ".$moduleId.") AND
                ".$visibility."
                ORDER BY m.Ord, s.Ord, p.Ord;";

  //execute query 
  $next = $db->query_first($sql);

  if($db->affected_rows == 0) {
    //2. next, attempt to get a page from the next section
    //   select all pages from all sections in the current module where the section Ord is > than the current section Ord and that should be visible to the current user
    $sql = "SELECT p.ID AS Page, p.Visibility AS Visibility, s.ID AS Section, m.ID AS Module
            FROM page p
            INNER JOIN section s ON p.SectionId = s.ID
            INNER JOIN module m ON s.ModuleId = m.ID
            WHERE (s.Ord > ".$sectionOrd." AND m.ID = ".$moduleId.") AND
                  ".$visibility."
                  ORDER BY m.Ord, s.Ord, p.Ord;";
    
    $next = $db->query_first($sql);
  }

  if($db->affected_rows == 0) {
      
      $sql = "SELECT u.Email, u.FName AS f_name, u.LName AS l_name, m.ID AS ModuleID, m.Number AS ModuleNumber, m.Name AS ModuleName, r.InputId AS ResponseID, s.Title AS Section, p.ID AS PageID, p.Title AS Page, i.Question, r.Response, c.Coach AS CoachEmail
          FROM response r
          INNER JOIN user     u    ON r.Email      = u.Email
          LEFT  JOIN coach    c    ON u.Email      = c.Student
          INNER JOIN input    i    ON r.InputId    = i.ID
          INNER JOIN element  e    ON i.ID         = e.ElementId
          INNER JOIN page     p    ON e.PageId     = p.ID
          INNER JOIN section  s    ON p.SectionId  = s.ID
          INNER JOIN module   m    ON s.ModuleId   = m.ID
          WHERE u.Status = ".ACTIVE."
          AND u.Email = '".$db->escape($email)."'
          AND m.ID = $moduleId
          AND r.Coach = 1
          ORDER BY m.Ord, s.Ord, p.Ord";
      // echo $sql;   
         $module = $db->fetch_array($sql); 
         $CoachEmail = '';
         $moduleId = '';
        // $str_report = $sql."<BR>";
          foreach ($module as $response) {
			  
			  $CoachEmail = $response['CoachEmail'];
			  $moduleId = $response['ModuleID'];
			  $fname = $response['f_name'];
			  $lname = $response['l_name'];
			  
			  $is_doctrine = getDoctrineFlaggedQuestions($db, $response['ResponseID']);
			  
			  if($is_doctrine){
				
				$str_report .= "<b>".$response['Question']." (Flagged automatically by CruDoctrine):</b><br /><br />".$response['Response']."<br /><br />".PHP_EOL;
			  
			  }else{
				  
				  $str_report .= "<b>".$response['Question']." (Flagged by ".$fname."): </b><br /><br />".$response['Response']."<br /><br />".PHP_EOL;
				  
			  }
			  
        }
        
        $sql = "SELECT * from additional_questions WHERE moduleid = $moduleId";
                                                    
        $questions = $db->fetch_array($sql);
		
		$str_report_add = "<br /><br /><b>Additional coaching questions for coaching call:</b><br /><br />";
        
        foreach ($questions as $row){
        $str_report_add .= $row['question']."<br />";
      }
        $str_report_add .="<br />Of course, you can see everyone that you are coaching and their flagged questions at <a href='http://cru.goodworksdevserver.com/admin/?p=reports&id=coach_task'>Coaching admin page</a><br />";
		
        $transport = null;
      if(SMTP_SSL == '') {
        $transport = Swift_SmtpTransport::newInstance(SMTP_SERVER, SMTP_PORT)
          ->setUsername(ADMIN_EMAIL_USERNAME)
          ->setPassword(ADMIN_EMAIL_PASSWORD);
      }else {
        $transport = Swift_SmtpTransport::newInstance(SMTP_SERVER, SMTP_PORT, SMTP_SSL)
          ->setUsername(ADMIN_EMAIL_USERNAME)
          ->setPassword(ADMIN_EMAIL_PASSWORD);
      }
	  $coach_name = getUserNameByID($db, $CoachEmail);
	  
      $mailer = null;
      $mailer = Swift_Mailer::newInstance($transport);

      $emailMessageBody = "Hello ".$coach_name.",<br /><br />";
	  

	$emailMessageBody .= $fname." ".$lname." just completed module ".$moduleId." in CruDoctrine.  Below are the flagged questions for this module as well as additional coaching questions that can aid you in your coaching call with them.<br /><br /><b>Flagged Questions:</b><br /><br />";
	
      $emailMessageBody .= $str_report;
	  
	  $emailMessageBody .= $str_report_add;
      $emailMessageBody .= "<br />Regards,<br />";
      $emailMessageBody .= ADMIN_EMAIL_FULLNAME;

      $emailMessage = Swift_Message::newInstance('Status Report')
        ->setFrom(array(ADMIN_EMAIL_USERNAME => ADMIN_EMAIL_FULLNAME))
        ->setTo(array($CoachEmail))
        ->setBody($emailMessageBody,'text/html');

      $result = $mailer->send($emailMessage);
      
    //3. lastly, attempt to get a page from the first section in the next module
    //   select all pages from the first section of the next module
    $sql = "SELECT p.ID AS Page, p.Visibility AS Visibility, s.ID AS Section, m.ID AS Module
            FROM page p
            INNER JOIN section s ON p.SectionId = s.ID
            INNER JOIN module m ON s.ModuleId = m.ID
            WHERE (s.Ord = 0 AND m.Ord = ".($moduleOrd + 1).") AND
                  ".$visibility."
                  ORDER BY m.Ord, s.Ord, p.Ord;";
    
    $next = $db->query_first($sql);
  }

  if($db->affected_rows > 0) {
    $type   = $next['Module'] == $moduleId ? PAGE : MODULE;
    $id     = $type == PAGE ? $next['Page'] : $next['Module'];

    //verify that next page is incomplete
    $sql = "SELECT Status 
            FROM progress 
            WHERE ID = ".(int)$next['Page']."
            AND Email = '".$db->escape($email)."'
            AND TYPE = '".$db->escape(PAGE)."'";
    //execute query 
    $newPageStatus = $db->query_first($sql);
    if($db->affected_rows == 0) {
      //mark next page started
      $data = array();
      $data['Email']  = $email;
      $data['ID'] = (int)$next['Page'];
      $data['Type'] = PAGE;
      $data['Status'] = STARTED;
      $data['Update'] = $cur_date;

      //execute query
      $db->insert("progress", $data);
    }
    if ($type == MODULE) {
      //verify that next module is incomplete
      $sql = "SELECT Status 
              FROM progress 
              WHERE ID = ".$next['Module']."
              AND Email = '".$db->escape($email)."' 
              AND TYPE = '".$db->escape(MODULE)."'";
      //execute query 
      $newModuleStatus = $db->query_first($sql);
      if($db->affected_rows == 0) {
        //mark next module started
        $data = array();
        $data['Email']  = $email;
        $data['ID'] = $next['Module'];
        $data['Type'] = MODULE;
        $data['Status'] = STARTED;
        $data['Update'] = $cur_date;

        //execute query
        $db->insert("progress", $data);
      }
      //mark current module complete
      $data = array();
      $data['Status'] = COMPLETE;
      $data['Update'] = $cur_date;

      //execute query
      $db->update("progress", $data, "Email = '".$db->escape($email)."' AND ID = ".(int)$moduleId." AND TYPE = '".$db->escape(MODULE)."'");
    }

    //return next page
    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".PHP_EOL;
    echo '<next>'.PHP_EOL;
    echo    '<type>'.$type.'</type>'.PHP_EOL;
    echo    '<id>'.$id.'</id>'.PHP_EOL;
    echo '</next>'.PHP_EOL;
  }
  else {
    //mark the last module complete
    $data = array();
    $data['Status'] = COMPLETE;
    $data['Update'] = $cur_date;

    //execute query
    $db->update("progress", $data, "Email = '".$db->escape($email)."' AND ID = ".(int)$moduleId." AND TYPE = '".$db->escape(MODULE)."'");

    //return end of modules
    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".PHP_EOL;
    echo '<next>'.PHP_EOL;
    echo    '<type>module</type>'.PHP_EOL;
    echo    '<id>-1</id>'.PHP_EOL;
    echo '</next>'.PHP_EOL;
  }
}
$db->close();
?>