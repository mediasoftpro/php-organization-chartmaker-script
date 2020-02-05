<?php

class breadcrumb {
  
	public $name;
    public $url;
	public $isActive;
		
	function __construct()
	{
		$this->name = "";
		$this->url = "";
		$this->isActive = false;
	}
}
?>