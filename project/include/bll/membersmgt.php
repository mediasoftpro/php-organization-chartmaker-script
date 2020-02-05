<?php
if(!defined("INCLUDE_ROOT"))
   include_once("../../include/config.php");
include_once(INCLUDE_ROOT . "db.php");
include_once(INCLUDE_ROOT . "memcache.php");
include_once("mailtemplatemgt.php");
include_once(INCLUDE_ROOT . "mail/mail.php");
include_once(INCLUDE_ROOT . "settings/general.php");
include_once(INCLUDE_ROOT . "utility/utility.php");
include_once("dictionarymgt.php");

class membersmgt {
	
	// Member Script Written  : 07 Apr 2013 - 8:22 PM

    // Note: Important Member Terms

    // Role | User Type

    // type:
    // ........... 0: Normal User
    // ........... 1: Administrator
    // ........... 2: Premium User

    // isEnabled:
    // ........... 0: Disabled Member
    // ........... 1: Enabled Member
   
    // isautomail :-> enable / disable receiving mails

	function add($fields)
	{
		$db = new DB;
		$message = "0";
		try
		{
		   $db->Insert("users", $fields); 
		   
		   //mailtemplateprocess($entity->username, $entity->email);
		}
		catch (Exception $e) 
        {
			$message = $e->getMessage();
        }
		return $message;
	}
	
	function update($fields, $filters, $updateprofile = false, $queryanalysis = false)
    {
		$db = new DB();
		if($updateprofile)
			$fields = $this->screen_profile_content($fields);
		
        return $db->Update("users", $fields, $filters, $queryanalysis);
	   
	   return $output;
    }
	
	function delete($filters, $uName, $deleteContent = false)
	{	
	    try
        {
			$db = new DB();
			$db->Delete("users", $filters);
			// remove user directory
			if($deleteContent)
			{
				$path = SITE_DIRECTORY_PATH . "/" . $uName;
				$util = new utility();
				$util->deleteDir($path);
			}
			return true;
		}
		catch (Exception $e) 
		{
			return $e->getMessage();
		}
	}
		
	// return single value
	function return_value($username, $fieldname)
	{
		$db = new DB;
		return $db->ReturnValue("users", $fieldname, array('username' => $username));
	}
	
	// return multiple values
	function fetch_values($fields, $isadmin, $filters, $ismultiple = false, $queryonly=false)
    {
	    $db = new DB;
		$bind = $db->prepareBinds(NULL, $filters);
		if($fields == "")
		{
			if($isadmin)
			  $fields = "*";
			else
			  $fields = "u.username,u.register_date,u.views,u.picturename"; // default fields
		}
		$query = "SELECT " . $fields . " from users as u";
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

	function update_value($username, $fieldname, $value)
	{
		$db = new DB;
		$db->Update("users", array($fieldname => $value), array('username' => $username));
        return true; 
	}

    function check($filters)
    {
		$db = new DB();
        $output = $db->Check("users", $filters);
		if($output > 0)
		  return true;
		else
		  return false;
    }

    function increment_views($username, $views)
    {
        $current_views = $views + 1;
        membersmgt::update_value($username, "views", $current_views);
        return $current_views;
    }
    
	/***************************************************************************************
	// CORE Member Records Fetching Script
	//**************************************************************************************/
	function load_channels($entity, $queryonly = false)
    {
	   if(Feature_Cache == 1 && $entity->iscache) // cache enabled
	   {
	     $cache = new MyMemcahe();
		 $key = $this->generate_key("chnllst_", $entity);
	     $lst = $cache->Get($key); // fetch from cache
		 if($lst != NULL)
	        return  $lst; //;
		 else
		 {
			 $lst = $this->fetch_channels($entity, $queryonly);
			 // cache output
			 $cache->Add($key, $lst);
			 return $lst;
		 }
	   }
	   else
	   {
		  // cache not enabled
		  $lst = $this->fetch_channels($entity, $queryonly);
		  return $lst;
	   }
    }
    
	// non cache version of fetch channels
    function fetch_channels($entity, $queryonly = false)
	{
		$db = new DB();
		$startindex = ($entity->pagenumber - 1) * $entity->pagesize;
				
        $logic = $this->filter_logic($entity);
       
	    $adminfields = "";
		if($entity->adminmode)
		   $adminfields = ""; // no additional fields for admin use
	    $query = "select u.username,u.register_date,u.views,u.picturename,u.countryname,u.firstname,u.lastname,u.gender,u.relationshipstatus,u.hometown,u.currentcity,u.zipcode,u.isenabled" . $adminfields;
   			
		 // filter option is used to filter records based on viewed records today, this week, this month, all time
        switch ($entity->filter)
        {
            case 0:
                // all time viewed (no viewed filter)
                $query .= " from users as u " . $logic;
                break;
            case 1:
                // today viewed records
                $query .= " INNER JOIN view_stat_today as av ON av.contentid=u.userid WHERE av.itemtype=8 AND " . $logic;
                $query .= " DateDiff(v.date_added,CURDATE())=0";
                break;
            case 2:
                // this week viewed records
                $query .= " INNER JOIN view_stat_thisweek as av ON av.contentid=u.userid WHERE av.itemtype=8 AND " . $logic;
                $query .= " DateDiff(v.date_added,CURDATE())>-7";
                break;
            case 3:
                // this month viewed records
                $query .= " INNER JOIN view_stat_thismonth as av ON av.contentid=u.userid WHERE av.itemtype=8 AND " . $logic;
                $query .= " DateDiff(v.date_added,CURDATE())>-31";
                break;
        }
		
		$query .= " order by " . $entity->order;
		if($entity->limit)
		{
			// limited records // no pagination -> used in small listings
			$query .= " limit " . $entity->pagesize;
		}
		else
		{
			// full pagination based load listing -> used in main listings
			$query .= " LIMIT " . $startindex . "," . $entity->pagesize;
		}
		
		if(!$queryonly)
		{
			$db = new DB;
			$rec = $db->smartQuery(array(
			'sql' => $query,
			'par' => $this->bindsearchparams($entity),
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

    // cache version of count script
    function cache_count_channels($entity, $queryonly = false)
    {
       if(Feature_Cache == 1 && $entity->iscache) // cache enabled
	   {
	     $cache = new MyMemcahe();
		 $key = $this->generate_key("chnlcnt_", $entity);
	     $lst = $cache->Get($key); // fetch from cache
		 if($lst != NULL)
	        return  $lst; //;
		 else
		 {
			 $lst = $this->count_channels($entity, $queryonly);
			 // cache output
			 $cache->Add($key, $lst);
			 return $lst;
		 }
	   }
	   else
	   {
		  // cache not enabled
		  $lst = $this->count_channels($entity, $queryonly);
		  return $lst;
	   }
    }

    // non cache version of count script
    function count_channels($entity, $queryonly = false)
	{      
        $logic = $this->filter_logic($entity);
       
	    $query = "SELECT count(u.username) as total from users as u";
      			
		// filter option is used to filter records based on viewed records today, this week, this month, all time
        switch ($entity->filter)
        {
            case 0:
                // all time viewed (no viewed filter)
                $query .= " " . $logic;
				break;
			case 1:
                // today viewed records
                $query .= " INNER JOIN view_stat_today as av ON av.contentid=u.userid WHERE av.itemtype=8 AND " . $logic;
                $query .= " DateDiff(v.date_added,CURDATE())=0";
                break;
            case 2:
                // this week viewed records
                $query .= " INNER JOIN view_stat_thisweek as av ON av.contentid=u.userid WHERE av.itemtype=1 AND " . $logic;
                $query .= " DateDiff(v.date_added,CURDATE())>-7";
                break;
            case 3:
                // this month viewed records
                $query .= " INNER JOIN view_stat_thismonth as av ON av.contentid=u.userid WHERE av.itemtype=1 AND " . $logic;
                $query .= " DateDiff(v.date_added,CURDATE())>-31";
                break;
        }

        if(!$queryonly)
		{
			$db = new DB;
			$total = $db->smartQuery(array(
			'sql' => $query,
			'par' => $this->bindsearchparams($entity),
			'ret' => 'col'
			 ));
			return $total;
		}
		else
		{
			// for query analysis purpose
			return $query;
		}
		
    }
        	
    function generate_key($ref, $entity)
	{
		$key = $ref . "" . $entity->term . "" . $entity->countryname . "" . $entity->accounttype . "" . $entity->gender . "" . $entity->picturename . "" . $entity->relationshipstatus . "" . $entity->type . "" . $entity->month . "" . $entity->year . "" . $entity->datefilter;
		if($order != "")
			$key .= $order;
		if($pagenumber > 0)
		  $key .= $pagenumber;
		if($pagesize > 0)
		  $key .= $pagesize;
		
		$key =  preg_replace('/[^\w\._]+/', '_', $key); // remove illigal characters
	    return $key;
	}
	// core filter logic
    function filter_logic($entity)
    {
        $filters = array();
		if($entity->accounttype > 0)
		    $filters[] = " u.accounttype=:accounttype";
		if($entity->countryname != "")
		   $filters[] = " u.countryname=:countryname";
		if($entity->gender != "")
		   $filters[] = " u.gender=:gender";
		if($entity->isenabled != 2)
		   $filters[] = " u.isenabled=:isenabled";
		if($entity->picturename != 'none')
		   $filters[] = " u.picturename<>'none'";
		if($entity->relationshipstatus != "")
		   $filters[] = " u.relationshipstatus=:relationshipstatus";
		if($entity->type != 100)
		   $filters[] = " u.type=:type";
        if ($entity->month > 0 && $entity->year > 0)
           $filters[] = " Year(u.register_date)=:year AND MONTH(u.register_date)= :month";
        else if ($entity->year > 0)
           $filters[] = " Year(u.register_date)=:year";
		   
		if($entity->term != "")
		{
		    switch ($entity->searchtype)
            {
                case 0:
                    // username match only
					$filters[] = " u.username like :term";
                    break;
                case 1:
                    // broad search
                    $filters[] = " (u.username like :term OR u.countryname like :term OR u.hometown like :term OR u.zipcode like :term)";
                    break;
            }
		}
        if ($entity->datefilter != 0)
        {
            switch ($entity->datefilter)
            {
				case 1:
				   // today records
				   $filters[] = ' DateDiff(u.register_date,CURDATE())=0';
				   break;
				case 2:
				   // this week records
				   $filters[] = ' DateDiff(u.register_date,CURDATE())<=0 AND DateDiff(u.register_date,CURDATE())>-7';
				   break;
				case 3:
				   // this month records
				   $filters[] = ' DateDiff(u.register_date,CURDATE())<=0 AND DateDiff(u.register_date,CURDATE())>-31';
            }
        }
		$script = "";
        if ($entity->filter > 0)
            $script .= " AND";
		else
			$script .= ' WHERE ';
		
		$util = new utility();
		$script .=  implode(' AND ', $filters);
	    if ($entity->filter > 0)
        {
			if(!$util->endsWith($script,"AND"))
               $script .= " AND";
        }
		if($util->endsWith(trim($script),"WHERE"))
            $script = substr($script, $util->lastIndexOf($script,"WHERE") + 5) . ' ';
        return $script;
    }

    function bindsearchparams($entity)
    {
        $arr = array();

		if ($entity->term != "")
			$arr['term']= '%'.$entity->term.'%';
		
		if($entity->accounttype > 0)
		   $arr['accounttype'] = $entity->accounttype;
		if($entity->countryname != "")
		   $arr['countryname'] = $entity->countryname;
		if($entity->gender != "")
		   $arr['gender'] = $entity->gender;
		if($entity->isenabled != 2)
		   $arr['isenabled'] = $entity->isenabled;
		
		if($entity->relationshipstatus != "")
		   $arr['relationshipstatus'] = $entity->relationshipstatus;
		if($entity->type != 100)
		    $arr['type'] = $entity->type;
		if ($entity->month > 0 && $entity->year > 0)
        {
            $arr['year']= $entity->year;
			$arr['month']= $entity->month;
        }
        else if ($entity->year > 0)
            $arr['year']= $entity->year;
			
        return $arr;
    }
	   

    // channel auto complete
    function load_user_autocomplete($term)
    {		
		$db = new DB();
        $query = "SELECT username from users where isenabled=1 AND username like :term ORDER BY username asc LIMIT 10";
		$rec = $db->smartQuery(array(
		'sql' => $query,
		'par' => array('term' => '%'.$term.'%'),
		'ret' => 'obj'
		 ));
		 
		 $records = array();
		 $item = array();
		 while($r = $rec->fetch(PDO::FETCH_OBJ))
		 {
			 $item['value'] = $r->username;
			 $records[] = $item;
		 }
		 return json_encode($records, JSON_PRETTY_PRINT);
    }
   	
	function screen_profile_content($data)
	{ 
	    $dictionary = new dictionarymgt();
	    // screen data for voilation words
		if(array_key_exists('aboutme', $data))
		   $data['aboutme'] = $dictionary->process_screening($data['aboutme']);
		if(array_key_exists('occupations', $data))
		   $data['occupations'] =  $dictionary->process_screening($data['occupations']);
		if(array_key_exists('companies', $data))
		   $data['companies'] =  $dictionary->process_screening($data['companies']);
		if(array_key_exists('schools', $data))
		   $data['schools'] = $dictionary->process_screening($data['schools']);
		if(array_key_exists('interests', $data))
		   $data['interests'] =  $dictionary->process_screening($data['interests']);
		   
		if(array_key_exists('movies', $data))
		   $data['movies'] = $dictionary->process_screening($data['movies']);
		if(array_key_exists('musics', $data))
		   $data['musics'] =  $dictionary->process_screening($data['musics']);
		if(array_key_exists('books', $data))
		   $data['books'] =  $dictionary->process_screening($data['books']);
		if(array_key_exists('schools', $data))
		   $data['schools'] = $dictionary->process_screening($data['schools']);
		if(array_key_exists('books', $data))
		   $data['books'] =  $dictionary->process_screening($data['books']);
		   
		return $data;
	}
	
}

?>