<?php

class members {
  
	public $userid;
    public $username;
	public $password;
	public $email;
    public $countryname;
	public $firstname;
	public $lastname;
    public $gender;
	public $birthdate;
    public $register_date;
	public $last_login;
	public $isenabled;
  	public $isallowbirthday;
    public $accounttype;
	public $views;
	public $aboutme;
    public $picturename;
	public $relationshipstatus;
    public $website;
	public $hometown;
	public $currentcity;
    public $zipcode;
	public $occupations;
    public $companies;
	public $schools;
	public $movies;
    public $musics;
	public $isautomail;
	public $type;
	public $roleid;
	// query related
	public $order;  // order records by fields
    public $month;  // filter records by monts (archive listing)
	public $year; // filter records by year (archive listing)
	public $filter; // filter records by views (today viewed, ths week viewed, this month viewed etc)
	public $datefilter; // filter records by added date (today added, this week added, this month added, etc)
	public $searchtype; // 0: category search, 1: tag search, 2: both category or tag, 3: broad search
	public $iscache; // load cache or non cache listings
	public $pagenumber; // current page no of listing
	public $pagesize; // current pagesize of listing
	public $term; // search term
	public $limit; // restrict records to certain limit (e.g 8 records)
	public $adminmode; // restrict records to certain limit (e.g 8 records)
	
	function __construct()
	{
		$this->userid = 0;
		$this->username = ""; // unique identifier
		$this->password = "";
		$this->email = "";
		$this->countryname = "";
		$this->firstname = "";
		$this->lastname = 0;
		$this->gender = "Male";
		$this->birthdate = date("Y-m-d H:i:s");
		$this->register_date =  date("Y-m-d H:i:s");
		$this->last_login =  date("Y-m-d H:i:s");
		$this->isenabled = 0;
		$this->isallowbirthday = 0;
		$this->accounttype = 0; 
		$this->views = 0;
		$this->aboutme = "";
		$this->picturename = "";
		$this->relationshipstatus = "single";
		$this->website = "";
		$this->hometown = "";
		$this->currentcity = "";
		$this->zipcode = ""; 
		$this->occupations = "";
		$this->companies = "";
		$this->schools = "";
		$this->movies = "";
		$this->musics = "";
		$this->books = "";
		// user settings
		$this->isautomail = 1;
		// other settings
		$this->type = 0; // 0: normal member, 1: administrator, 2: premium users
		$this->roleid  = 0; // 0: no role exist
				
		// query related
		$this->order = "register_date desc"; // field depend on database field type
		$this->month = 0; // 0: no filter
		$this->year = 0; // 0: no flter
		$this->filter = 0; // 0: no view filter, 1: today viewed, 2: this week viewed, 3: this month viewed (for this you should enable advance view statistic)
		$this->datefilter = 0; // 0: no date filter, 1: today added, 2: this week added, 3: this month added
		$this->searchtype = 3; // 3: broad search, 0: category based, 1: tag based, 2: both category or tag
		$this->iscache = false; // cache false
		$this->pagenumber = 1;
		$this->pagesize = 20; // default page size 20
		$this->term = ""; // no search term
		$this->limit = false; // restrict records to certain limit (e.g 8 records)
		$this->adminmode = false; // load listings in admin section
	}
	
}
?>