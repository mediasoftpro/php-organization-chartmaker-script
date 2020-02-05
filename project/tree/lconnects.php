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
$settings = $tree->fetch_values("charts", array('chartid' => $_GET["fid"]), false);
$item = $tree->fetch_values("chartconnects", array('chartid' => $_GET["fid"]), true);
foreach($item as $node) {
 $node->title = $settings->title;
 $node->connectstyle = $settings->connectstyle;
 $node->linewidth = $settings->linewidth;
 $node->linecolor = $settings->linecolor;
 $node->linehovercolor = $settings->linehovercolor;
 $node->cornerradius = $settings->cornerradius;
}
echo json_encode($item);
?>