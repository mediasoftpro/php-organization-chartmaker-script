<?php
//header('Content-Type: application/json');
include_once("../include/config.php");
include_once(INCLUDE_ROOT . "bll/treemgt.php");
session_start();
$data = json_decode(file_get_contents('php://input'), true);
/*echo var_dump($data);
exit;
if(!isset($data['output']))
{
	echo "No Data Found!";
	exit;
}*/

/*echo var_dump($data['output']);
exit;

	if(!isset($_REQUEST['output']))
	{
		echo "No Data Found!";
		exit;
	} */
	
	$nodes = $data; // json_decode(stripslashes($_POST['output']));
	if(count($nodes) == 0)
	{
		echo "No Data Found!";
	    exit;
	}
	//echo count($nodes) . var_dump($nodes[0]["chartid"]);
	//exit;
	$tree = new treemgt();
	$chartid = $nodes[0]["chartid"]; //$nodes[0]->chartid;
	$checkouType = 0; // 0: save only
    $checkoutType = $nodes[0]["checktype"]; //$nodes[0]->checktype;
	
	if($chartid == 0)
	{
		// Step I:
		// create new family id
		$treeItem = array();
		$treeItem['title'] =  $nodes[0]["chartname"]; //$nodes[0]->chartname;
		$treeItem['connectstyle'] =   $nodes[0]["cstyle"]; // $nodes[0]->cstyle;
		$treeItem['linewidth'] =  $nodes[0]["linewidth"];//$nodes[0]->linewidth;
		$treeItem['linecolor'] =  $nodes[0]["strokecolor"];//$nodes[0]->strokecolor;
		$treeItem['linehovercolor'] =  $nodes[0]["hovercolor"];//$nodes[0]->hovercolor;
		$treeItem['cornerradius'] =  $nodes[0]["cradius"];//$nodes[0]->cradius;
		$treeItem['dateadded'] =  date("Y-m-d H:i:s");
		$uName =  $nodes[0]["username"]; //$nodes[0]->username;
		$addRecord = true;
		$chartid = 0;
		if($uName == "")
		{
			$uName = mt_rand(0,mt_getrandmax());
			$_SESSION['temp_username'] = $uName;
		} else {
			if(isset($_SESSION['temp_username']))
			{
				//  get temp username tree id
				$item = $tree->fetch_values("charts", array('username' => $_SESSION['temp_username']), true);
				if(count($item) > 0)
				{
					// update tree record
					$chartid = $item->chartid;
					$tree->update('charts', array('username' => $uName), array('username' => $_SESSION['temp_username']));
				}
				unset($_SESSION['temp_username']);
				$addRecord = false;
			}
		}
		if($addRecord)
		{
			$treeItem['username'] = $uName; // fixed for time being
			$chartid = $tree->add('charts', $treeItem);
			if($chartid == 0)
			{
				echo "-1";
				exit;
			}
		}
		$_SESSION['chartid'] = $chartid;
	}
	else {

	    // update chart formatting information
		$chartData = array();
		$chartData['connectstyle'] =  $nodes[0]["cstyle"]; //$nodes[0]->cstyle;
		$chartData['linewidth'] =  $nodes[0]["linewidth"]; //$nodes[0]->linewidth;
		$chartData['linecolor'] = $nodes[0]["strokecolor"]; //$nodes[0]->strokecolor;
		$chartData['linehovercolor'] = $nodes[0]["hovercolor"]; //$nodes[0]->hovercolor;
		$chartData['cornerradius'] =   $nodes[0]["cradius"]; //$nodes[0]->cradius;
		if(trim($nodes[0]->chartname) != "")
			$chartData['title'] =  $nodes[0]["chartname"]; //$nodes[0]->chartname;
			
		$tree->update("charts", $chartData, array('chartid' => $chartid));
		
		
		// chartid > 0
		if($tree->validate("chartnodes", array('chartid' => $chartid)))
		{
			// record exist
			// delete all older records before saving new one
		   $tree->delete("chartnodes", array('chartid' => $chartid));
		} 
	}
	// Step II: Store all family node information in database
	if(count($nodes) > 0)
	{	
		foreach($nodes as $node)
		{
			if($node["isdeleted"] == "")
			{
				$nodeItem = array();
				$nodeItem['chartid'] = $chartid;
				$nodeItem['elementid'] = $node["nodeid"]; //$node->nodeid;
				$nodeItem['toppos'] = $node["tpost"]; //$node->tpost;
				$nodeItem['leftpos'] = $node["lpost"]; //$node->lpost;
				$nodeItem['title'] = $node["title"]; //$node->title;
				$nodeItem['description'] = $node["desc"]; //$node->desc;
				//$nodeItem['nodecaption'] = $node->nodeinfo;
				$nodeItem['firstname'] = $node["fname"]; //$node->fname;
				$nodeItem['surname'] = $node["sname"]; //$node->sname;
				$nodeItem['gender'] = $node["gender"]; //$node->gender;
				
				$nodeItem['photo'] = $node["photourl"]; //$node->photourl;
				
				$nodeItem['email'] = $node["email"]; //$node->email;
		
				$nodeItem['website'] = $node["website"]; //$node->website;
				$nodeItem['hometel'] = $node["tel"]; //$node->tel;
				$nodeItem['mobile'] = $node["mobile"]; //$node->mobile;
				$nodeItem['profession'] = $node["profession"]; //$node->profession;
				$nodeItem['company'] = $node["company"]; //$node->company;
				$nodeItem['interests'] = $node["interests"]; //$node->interests;
				$nodeItem['bionotes'] = $node["bio"]; //$node->bio;
				$nodeItem['bkcolor'] = $node["bkcolor"]; //$node->bkcolor;
				$nodeItem['ftcolor'] = $node["ftcolor"]; //$node->ftcolor;
				$nodeItem['brcolor'] = $node["brcolor"]; //$node->brcolor;
				// store node
				$tree->add('chartnodes', $nodeItem);
			}
		}
	}
	echo  $chartid; // var_dump($_REQUEST['output']); //
	exit;
?>