<?php
if(!defined("INCLUDE_ROOT"))
   include_once  dirname(dirname(__FILE__)) . "/config.php";

include_once(INCLUDE_ROOT . "db.php");
class treemgt {
	
	function add($table, $fields)
	{
		$db = new DB();
		$lastinsertid = true;
        $id = $db->Insert($table, $fields, $lastinsertid, false); 
		return $id;
	}
	
	function update($table, $fields, $filters)
    {
		$db = new DB();
        return $db->Update($table, $fields, $filters);
    }
	
	function delete($table, $filters)
	{
		$db = new DB();
        $db->Delete($table, $filters);
		return true;
	}
		
	function return_value($table, $fieldname, $filter)
	{
		$db = new DB;
		return $db->ReturnValue($table, $fieldname, $filter);
	}
	
	
	function validate($table, $filters)
	{
		$db = new DB();
        $output = $db->Check($table, $filters);
		if($output > 0)
		  return true;
		else
		  return false;
	}
	
	// fetch single record
	function fetch_values($table, $filters, $ismultiple = false, $queryonly=false)
    {
	    $db = new DB;
		$bind = $db->prepareBinds(NULL, $filters);
		$query = "SELECT * from " . $table;
		$query .= $db->prepareFilters($filters);
		if(!$queryonly)
		{			
			$rec = $db->smartQuery(array(
			'sql' => $query,
			'par' => $bind,
			'ret' => 'obj'
			 ));
			 if(!$ismultiple)
			     return $rec->fetch(PDO::FETCH_OBJ);
		     else
			 {
				 $records = array();
				 while($r = $rec->fetch(PDO::FETCH_OBJ))
				 {	 
					 $records[] = $r;
				 }
				 return $records;
			 }
		}
		else
		{
			return $bind;
		}
	}
	// load records
	function load($table, $username, $pagenumber, $pagesize, $order, $queryonly = false){
		
		$startindex = ($pagenumber - 1) * $pagesize;
		
		$query = "SELECT * from " . $table;
		$bind = array();
		if($username != "")
		{
		  $query .= " where username=:username";
		  $bind['username']= $username;
		}
		
		$query .= " order by " . $order . " LIMIT " . $startindex . "," . $pagesize;
		if(!$queryonly)
		{
		$db = new DB;
        $rec = $db->smartQuery(array(
        'sql' => $query,
		'par' => $bind,
        'ret' => 'obj'
         ));
		 
		 $records = array();
 		 while($r = $rec->fetch(PDO::FETCH_OBJ))
		 {	
		 	 $records[] = $r;
		 }
		 return $records;
		 }
		 else
		 {
		 	return $query;
		 }
	}
	
	function countRecords($table, $username, $queryonly = false){
		$query = "select count(*) as total from " . $table;
		$bind = array();
		if($username != "")
		{
		  $query .= " where username=:username";
		  $bind['username']= $username;
		}
		if(!$queryonly)
		{
			$db = new DB;
			$total = $db->smartQuery(array(
			'sql' => $query,
			'par' => $bind,
			'ret' => 'col'
			 ));
			 return $total;
		 }
		 else
		 {
		 	return $query;
		 }
	}
}
?>