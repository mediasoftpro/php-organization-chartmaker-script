<?php
//header('Content-Type: application/json');
include_once("../include/config.php");
include_once(INCLUDE_ROOT . "bll/treemgt.php");
if(!isset($_REQUEST['fid']))
{
	echo "No Data Found!";
	exit;
}
$tree = new treemgt();
$item = $tree->fetch_values("chartnodes", array('chartid' => $_GET["fid"]), true);
echo json_encode($item);
?>