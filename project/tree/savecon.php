<?php
//header('Content-Type: application/json');
include_once("../include/config.php");
include_once(INCLUDE_ROOT . "bll/treemgt.php");
session_start();
if(!isset($_REQUEST['output']))
{
	echo "No Data Found!";
	exit;
}

$connects = $_POST['output'];
$checkouType = 0; // 0: save only, 1: save and continue
$checkoutType = $connects[0]['checktype'];
$tree = new treemgt();
if(count($connects) > 0)
{
    // validate whether existing familyid connection exist in database
	$fid = $connects[0]['chartid'];
	
	if($fid > 0)
	{
		if($tree->validate("chartconnects", array('chartid' => $fid)))
		{
			// record exist
			// delete all older records before saving new one
		   $tree->delete("chartconnects", array('chartid' => $fid));
		}
	}
	foreach($connects as $connect)
	{	
		if($connect['isdeleted'] == "")
		{
			$connectItem = array();
			$connectItem['chartid'] = $connect['chartid'];
			$connectItem['selementid'] = $connect['source'];
			$connectItem['delementid'] = $connect['dest'];
			$connectItem['sconnectpos'] = $connect['sourcepos'];
			$connectItem['dconnectpos'] = $connect['destpos'];
			// store node
			$tree->add('chartconnects', $connectItem);
		}
	}
}
echo ""; // save only
exit;
?>