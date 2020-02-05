<?php

/* Operate on the database using our super-safe PDO system */
class DB
{
    /* PDO istance */
    private $db = NULL;
    /* Number of the errors occurred */
    private $errorNO = 0;

    private $dbhost = '127.0.0.1';
	private $dbusername = 'mediasoftpro';
	private $dbpassword = '';
	private $dbname = 'c9';
	
    /* Connect to the database, no db? no party */
    public function __construct()
    {
        try
        {
			$dsn = 'mysql:host='. $this->dbhost .';';
			if($this->dbname != ""){
			   $dsn .= 'dbname='.$this->dbname.';'; // We might not always be connecting to a created database.
		    }
			$this->db = new PDO($dsn, $this->dbusername, $this->dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }
        catch (PDOException $e) 
        {
            exit('App shoutdown: ' . $e->getMessage());
        }
    }

    /* Have you seen any errors recently? */
    public function getErrors() { return ($this->errorNO > 0) ? $this->errorNO : false; }

    /* Perform a full-control query */
    public function smartQuery($array)
    {
        # Managing passed vars
        $sql = $array['sql'];
        $par = (isset($array['par'])) ? $array['par'] : array();
        $ret = (isset($array['ret'])) ? $array['ret'] : 'res';

        # Executing our query
        $obj = $this->db->prepare($sql);
        $result = $obj->execute($par);

       # Error occurred...
       if (!$result) { ++$this->errorNO; }

        # What do you want me to return?
        switch ($ret)
        {
            case 'obj':
            case 'object':
                return $obj;
            break;

            case 'ass':
            case 'assoc':
            case 'fetch-assoc':
                return $obj->fetch(PDO::FETCH_ASSOC);
            break;

            case 'all':
            case 'fetch-all':
                return $obj->fetchAll();
            break;
			case 'col':
                return $obj->fetchColumn();
            break;
			case 'ins':
				return $this->db->lastInsertId();
			break;
            case 'res':
            case 'result':
                return $result;
            break;
            default:
                return $result;
            break;
        }
    }
    
	/* Generat Insert data to database function */
	public function Insert($tablename, $fields, $lastinsertid = true, $queryonly = false)
	{
		$query = db::insertQuery($tablename, $fields);
		$bind = db::prepareBinds($fields, NULL);
		$flag = "ins";
		if(!$queryonly)
		{
			if(!$lastinsertid)
			  $flag = "res";
			$output = db::smartQuery(array(
				'sql' => $query,
				'par' => $bind,
				'ret' => $flag
				));
			return $output;
		}
		else
		{
			return $query;
		}
	}
	
	/* Generat Update database records function */
	public function Update($tablename, $fields, $filters, $queryonly = false)
	{
		$query = db::updateQuery($tablename, $fields, $filters);
		$bind = db::prepareBinds($fields, $filters);
		$flag = "res";
		if(!$queryonly)
		{
		    $output = db::smartQuery(array(
			'sql' => $query,
			'par' => $bind,
			'ret' => $flag
			));
			return $output;
		}
		else
		{
		    return $query;
		}
	}
	/* Generate Delete database recodrs */
	public function Delete($tablename, $filters, $queryonly = false)
	{
		$query = db::deleteQuery($tablename, $filters);
		$bind = db::prepareBinds(NULL, $filters);
		$flag = "res";
		if(!$queryonly)
		{
		    $output = db::smartQuery(array(
			'sql' => $query,
			'par' => $bind,
			'ret' => $flag
			));
			return $output;
		}
		else
		{
		    return $query;
		}
	}
	/* Generate Count Database Query. */
	public function Count($tablename, $filters, $queryonly = false)
	{
		$query = db::countQuery($tablename, $filters);
		$bind = db::prepareBinds(NULL, $filters);
		$flag = "col";
		if(!$queryonly)
		{
		    $total = db::smartQuery(array(
			'sql' => $query,
			'par' => $bind,
			'ret' => $flag
			));
			return $total;
		}
		else
		{
		    return $query;
		}
	}
	
	/* Generate Check Database Query. If records found return true else return false */
	public function Check($tablename, $filters, $queryonly = false)
	{
		$total = db::Count($tablename,$filters, $queryonly);
		if($queryonly)
		  return $total; // query analysis
		else
		{
			if($total > 0)
			  return true;
			else
			  return false;
		}
	}
	
	/* Return single field value */
	public function ReturnValue($tablename, $fieldname, $filters, $queryonly = false)
	{
		$query = db::returnValueQuery($tablename, $fieldname, $filters);
		$bind = db::prepareBinds(NULL, $filters);
		if(!$queryonly)
		{
		    $db = new DB;
			$rec = $db->smartQuery(array(
			'sql' => $query,
			'par' => $bind,
			'ret' => 'fetch-assoc'
			));
			return $rec['value'];
		}
		else
		{
		    return $query;
		}
	}
		
    /* Generate Insert Query */
	public function insertQuery($tablename, $fields)
	{
		$script = "";
		if(count($fields) > 0)
		{
			$counter = 0;
			$fielditems = "";
			$fieldvalues = "";
			foreach($fields as $key => $value)
			{
				if($counter > 0)
				{
				   $fielditems .= ",";
				   $fieldvalues .= ",";
				}
				$fielditems .= $key;
                $fieldvalues .= ":" . $key;
				$counter++;
			}
			$script = " INSERT INTO " . $tablename . "(" . $fielditems . ")VALUES(" . $fieldvalues . ");";
		}
		return $script;
	}
	/* Generate Update Query */
	public function updateQuery($tablename, $fields, $filters)
	{
		$query = "";
		if(count($fields)>0)
		{
			$counter = 0;
			$query .= "UPDATE " . $tablename . " SET ";
			foreach($fields as $key => $value)
			{
				if($counter > 0)
				   $query .= ",";
				$query .= $key . "=:" . $key;
				$counter++;
			}
			$query .= db::prepareFilters($filters);
		}
		return $query;
	}
	/* Generate Delete Query */
	public function deleteQuery($tablename, $filters)
	{
		$query = "";
		$query .= "DELETE FROM " . $tablename;
		$query .= db::prepareFilters($filters);
		return $query;
	}
	/* Generate Check Query */
	public function countQuery($tablename, $filters)
	{
		$query = "";
		$query .= "SELECT COUNT(*) FROM " . $tablename;
		$query .= db::prepareFilters($filters);
		return $query;
	}
	
	/* Generate Single Field Value Query */
	public function returnValueQuery($tablename, $fieldname, $filters)
	{
		$query = "";
		$query .= "SELECT " . $fieldname . " as value FROM " . $tablename;
		$query .= db::prepareFilters($filters);
		return $query;
	}
	
	/* Generate Filters for internal use only */
	function prepareFilters($filters)
	{
		$query = "";
		$counter = 0;
		if(count($filters) > 0)
		{
			$query .= " WHERE ";
			foreach($filters as $key => $value)
			{
				if($counter > 0)
				   $query .= " AND ";
				$query .= $key . "=:" . $key;
				$counter++;
			}
		}
		return $query;
	}
	
	/* Generate Binds e.g :key = "value"; list */
	public function prepareBinds($fields, $filters)
	{
		$binds = array();
		if(!is_null($fields))
		{
			if(count($fields) > 0)
			{
				foreach($fields as $key => $value)
				{
					$binds[$key] = $value;
				}
			}
		}
		if(!is_null($filters))
		{
			if(count($filters) > 0)
			{
				foreach($filters as $key => $value)
				{
					$binds[$key] = $value;
				}
			}
		}
		return $binds;
	}
    /* Get PDO istance to use it outside this class */
    public function getPdo() { return $this->db; }

    /* Disconnect from the database */
    public function __destruct() { $this->db = NULL; }
	
	
	// old compatibility
	public function Connect(){
		$DataSource = 'localhost:3306';
		$DbUserName = 'root';
		$DbPassword = 'saimajj';
		$DabaseName = 'vsk80';
		
        $link = @mysql_connect($DataSource, $DbUserName, $DbPassword);
		if (mysql_errno() != 0) {
  			//header("HTTP/1.0 500 Internal Server Error");
  			//exit;
		}
		if (!$link) {
			return false;
		} else {
			@mysql_select_db($DabaseName);
			return true;
		}
	}

	public function Query($sql){
		$result = mysql_query($sql);
		if (mysql_errno() != 0) {
			echo mysql_error();
  			//header("HTTP/1.0 500 Internal Server Error");
  			//exit();
		} else {
			return $result;
		}
	}

	public function Prepare($value){
		if (get_magic_quotes_gpc()){
			$value = stripslashes($value);
		}

		return mysql_real_escape_string($value);
	}
	
	public function RowCount($result){
		return mysql_num_rows($result);
	}

	public function GetId(){
		return mysql_insert_id();
	}
}

?>
