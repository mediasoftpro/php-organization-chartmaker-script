<script type="text/javascript">
  var dn = '<?= SITE_DOMAIN ?>';
  var hd = 'tree/handler.php';
  var sconnects = 'tree/savecon.php';
  var lhandler = 'tree/load.php';
  var chandler = 'tree/lconnects.php';
  var strokeColor = '#005b0f';
  var hoverPaintStyle = '#ff0000';
  var strokeLineWidth = 1;
  var hoverstrokeLineWidth = 2;
  var connectStyle = '<?= $connectStyle ?>'; // Bezier, StateMachine, Flowchart,
  var offsetdiff = 20; // horizontal space between two nodes
  var defaultUName = '<?= $userName; ?>';
  var defaultFName = '<?= $firstName; ?>';
  var defaultSName = '<?= $lastName; ?>';
  var redirectPageName = '<?= $pageName ?>';
  var chartID = '<?= $chartID ?>';
  var msgLabel = 'treemsg';
  var cornerRadius = '<?= $cornerRadius ?>';
  var overlaySettings = [ "Arrow", { location:0.1, id:"charLabel", direction: -1} ];
  // default node color settings
  var bkColor = "#666";
  var ftColor = "#fff";
  var brColor = "#000";
  var titleElementID = "<?= $titleElementID ?>";
  <?php 
	   if($smoothScroll)
		  echo "var smoothScroll = true;\n";
		else
		  echo "var smoothScroll = false;\n";
		  
		if($readOnly)
		   echo "var readOnly = true;\n";
		else if (!$isEditable)
		   echo "var readOnly = true;\n";
		else
		   echo "var readOnly = false;\n";
  ?>
$(function () {

  $('.cpanel-body').on({
		click: function (e) {
			var swidth = $("#txt_linewidth").val();
			if(swidth != "") {
				  strokeLineWidth = swidth;
				  hoverstrokeLineWidth = swidth;
			}
			return false;
		}
	}, '#btn_updsetting');
  
});
</script>