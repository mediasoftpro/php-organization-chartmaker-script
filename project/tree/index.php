<?php
   include_once("../include/config.php");
   include_once(ROOTPATH . "include/settings/general.php");
   include_once(ROOTPATH . "include/utility/labels.php");
   include_once(ROOTPATH . "include/utility/utility.php");
   include_once(ROOTPATH . "include/modules/top.php");
   include_once(ROOTPATH . "include/settings/registration.php"); // registration form settings
   
   // page headers
   $headers = new vskHeader();
   $headers->title = _("Family Tree Creation Script");
   $headers->description = "";
   echo $headers->tHeaders();	
?>
<body>
    <?php
	   $topActiveIndex = 9; // tree
	   $showHeader = false;
	   include_once(ROOTPATH . "include/modules/header.php");
	     // bread crumb
	   include_once(ROOTPATH . "include/entity/breadcrumb.php");
	   $breadItems = array();
	   	    
	   // bread caption
	   $bcrumb = new breadcrumb();
	   $bcrumb->name = "Family Tree";
       $bcrumb->isActive = true;
	   $breadItems[] = $bcrumb;
	   include_once(ROOTPATH . "include/modules/breadcrumb.php");
	   // core js
	   include_once("js.php");
	?>
    <div class="container" id="content">
       <div class="row item_pad_4">
       	      <div class="col-lg-6">
                   <div id="treemsg"></div>
                   <div id="lstatus"></div>
              </div>
              <div class="col-lg-6">
                   <div class="item_r">
       		         <button id="savetree" class="btn btn-primary btn-sm">Save Changes</button>&nbsp;<button id="sscroll" class="btn btn-primary btn-sm">Smooth Scroll</button>
                   </div>
              </div>
       </div>
      
       <div class="row">
         <div class="col-lg-12">
            <h2>Family Tree</h2>
            <hr />
            <div id="vcart">			
             <div class="vcart-inner chart-demo" id="chart-demo">
                
            </div>            
            <div class="clear"></div>
        </div>
        </div>
      </div>
       <div id="dinfo">
       	  <span id="findex"></span>
          <span id="fseindex"></span>
          <span id="fteindex"></span>
          <?php 
		    $familyID = "";
			if(isset($_GET['fid']))
				$familyID = $_GET['fid'];
		   ?>
          <span id="fid"><?=$familyID?></span>
          <span id="postop"></span>
          <span id="posleft"></span>
          <br />
          <div id="vinfo"></div>
       </div>
       
      <?php
	     include_once("modal.php");
	  	 include_once(ROOTPATH . "include/modules/footer.php");
	  ?>
    </div>
    <?php
	    // javascript at the end of page to load page faster
		$bscript = "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/lib/jquery-ui-1.9.2-min.js\"></script>\n";
        $bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/lib/jquery.ui.touch-punch.min.js\"></script>\n";
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/lib/jsBezier-0.6.js\"></script>\n";   
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/lib/jsplumb-geom-0.1.js\"></script>\n"; // jsplumb geom functions
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/util.js\"></script>\n"; // jsplumb util
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/dom-adapter.js\"></script>\n"; // jsplumb util
        $bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/jsPlumb.js\"></script>\n"; // main jsplumb engine
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/endpoint.js\"></script>\n"; // endpoint
        $bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/connection.js\"></script>\n"; // connection      
        $bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/anchors.js\"></script>\n"; // anchors 
        $bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/defaults.js\"></script>\n"; // connectors, endpoint and overlays   
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/connectors-bezier.js\"></script>\n"; // connectors, endpoint and overlays  
        $bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/connectors-flowchart.js\"></script>\n"; // flowchart connectors
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/connector-editors.js\"></script>\n"; // flowchart connectors
        $bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/renderers-svg.js\"></script>\n"; // SVG renderer
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/renderers-canvas.js\"></script>\n"; // canvas renderer
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/renderers-vml.js\"></script>\n"; // vml renderer
		$bscript .= "<script src=\"" . SITE_DOMAIN . "jsPlumb-master/src/jquery.jsPlumb.js\"></script>\n"; // jquery jsPlumb adapter
		$bscript .= "<script src=\"" . SITE_DOMAIN . "js/jquery.overscroll.min.js\"></script>\n"; // jquery jsPlumb adapter
        $bscript .= "<script type=\"text/javascript\" src=\"" . SITE_DOMAIN . "plupload/js/plupload.full.js\"></script>\n";
		$bscript .= "<script type=\"text/javascript\" src=\"" . SITE_DOMAIN . "tree/tree.js\"></script>\n";
		echo $headers->bHeaders($bscript);
	?>
  </body>
</html>
