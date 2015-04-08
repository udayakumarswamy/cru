<?php
/*
 * Cru Doctrine
 * Functions
 * Campus Crusade for Christ
 */

require_once("config.inc.php");
require_once("Database.singleton.php");

function in_array_r($needle, $haystack, $strict = true) {
  foreach ($haystack as $item) {
    if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
      return true;
    }
  }
  return false;
}

function getVisibilityClause($type) {
  $visibility = null;

  if($type == INTERN || $type == VOLUNTEER || $type == PART_TIME_FIELD_STAFF || $type == ASSOCIATE_STAFF) {
    $visibility = "(p.Visibility = 0 OR p.Visibility = 5)";
  } 
  else if($type == STUDENT || $type == OTHER) {
    $visibility = "(p.Visibility = 0 OR p.Visibility = 4)";
  }
  else {
    $visibility = "(p.Visibility >= 0)";
  }

  return $visibility;
}

function getActiveCoaches($db) {
  $coaches = array();

  $sql     =  "SELECT u.Email as id, CONCAT(u.FName, ' ', u.LName) as name, r.Name as region
               FROM  user u
               INNER JOIN region r ON u.Region = r.ID
               WHERE u.Type < ".STUDENT."
               AND u.Status = ".ACTIVE."
               ORDER BY u.LName;";

  $coaches = $db->fetch_array($sql);

  return $coaches;
}

function getUserNameByID($db, $id) {
  $coachName = array();

  $sql     =  "SELECT u.FName as name
               FROM  user u
               WHERE u.Email = '".$db->escape($id)."';";

  $coachName = $db->query_first($sql);

  return $coachName['name'];
}

function getUserNameByID_blog($db, $id) {
  $coachName = array();

  $sql     =  "SELECT u.FName fname,u.LName lname
               FROM  user u
               WHERE u.Email = '".$db->escape($id)."';";

  $coachName = $db->query_first($sql);

  return $coachName['fname'].' '.$coachName['lname'][0];
}

function getRegions($db) {
  $regions = array();

  $sql     =  "SELECT r.ID as id, r.Name as name
               FROM  region r
               ORDER BY r.Name;";

  $regions = $db->fetch_array($sql);

  return $regions;
}

function getDoctrineFlaggedQuestions($db, $eleid) {
  
  $res = array();

  $sql     =  "SELECT r.Coach as doctrine
               FROM  input r
               WHERE r.ID = ".$eleid;

  $res = $db->query_first($sql);

  return $res['doctrine'];
}


function getRegionNameByID($db, $id) {
  $regionName = array();

  $sql     =  "SELECT r.Name as name
               FROM  region r
               WHERE r.ID = ".$id;

  $regionName = $db->query_first($sql);

  return $regionName['name'];
}

function getUserTypes($db, $typeClause = REGIONAL_ADMIN) {
  $user_types = array();

  //get user types for selection
  $sql = "SELECT ut.ID as id, ut.Name as name
          FROM  user_type ut
          WHERE ut.ID > ".$typeClause."
          ORDER BY ut.ID;";

  $user_types = $db->fetch_array($sql);

  return $user_types;
}

function getUserStatuses($db) {
  $user_statuses = array();

  $sql =  "SELECT ID as id, Name as name
           FROM  user_status
           ORDER BY Name;";

  $user_statuses = $db->fetch_array($sql);

  return $user_statuses;
}

function getColumnBySpecCol($db, $sourcecolname,$sourcecolvalue,$reqcol,$table) {
  
  $reqres = array();

  $sql     =  "SELECT $reqcol as reqcol
               FROM  $table
               WHERE $sourcecolname = '$sourcecolvalue'";

  $reqres = $db->query_first($sql);

  return $reqres['reqcol'];
}
function getModIdByEleID($db, $eleid) {
  
  $reqres = array();

  $sql     =  "SELECT ModuleId FROM section WHERE ID = (SELECT SectionId FROM page WHERE ID = (SELECT PageId FROM element WHERE ElementId = '$eleid'))";

  $reqres = $db->query_first($sql);

  return $reqres['ModuleId'];
}

function get_blog_post_title($db, $inputid) {
  
  $reqres = array();

  $sql     =  "SELECT type,PageId FROM element WHERE ElementId = ".$inputid;

//echo $sql;
  $reqres = $db->query_first($sql);

  if($reqres['type']=='input'){
	  
	  $sql_q = "SELECT Question FROM input WHERE ID = ".$inputid;
	  
	 // echo $sql_q;exit;
	  $req_q = $db->query_first($sql_q);
	  
	  return $req_q['Question'];
	  
  }else if ($reqres['type']=='blogpost'){
	
	//	  echo $sql_q;exit;
	
	$sql_q = "SELECT Title FROM page WHERE ID = ".$reqres['PageId'];
	  
	  $req_q = $db->query_first($sql_q);

	  return $req_q['Title'];
  }
}
?>