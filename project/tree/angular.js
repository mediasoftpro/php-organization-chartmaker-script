//******************************************************************************
// Modal Popup Controller
//******************************************************************************
function modalController($scope) {
  $scope.sthumbEdit = function() {
     $(".dur").show();
  };
  $scope.hthumbEdit = function() {
     $(".dur").hide();
  };
  $scope.parentNode = function() {
	 AddPNode(0);
  };
  $scope.childNode = function() {
	 AddPNode(1);
  };
  $scope.leftNode = function() {
	 AddPNode(2);
  };
  $scope.rightNode = function() {
	 AddPNode(3);
  };
  $scope.createNode = function() {
	 AddPNode(4);
  };
  $scope.partnerOptions = function() {
	 $('#epartner').show();
	 $('#einfo').hide();
 	 $('#esubling').hide();
  };
}
//******************************************************************************
// Toolbar Controller
//******************************************************************************
function toolbarController($scope) {
  $scope.smoothScroll = function() {
     document.location = dn + '' + redirectPageName + '&scroll=true';
  };
 
  $scope.flowChart = function() {
	 document.location = dn + '' + redirectPageName + '&connect=0';	
  };
  
  $scope.bezier = function() {
	document.location = dn + '' + redirectPageName + '&connect=1';	
  };
  
  $scope.stateMachine = function() {
	 document.location = dn + '' + redirectPageName + '&connect=2';	
  };
  
  $scope.readOnly = function() {
	 document.location = dn + '' + redirectPageName + '&readonly=true';	
  };
  
  $scope.creatNew = function() {
	 document.location = dn + '' + redirectPageName + '&reset=true';
  };
  
  $scope.printTree = function() {
	 window.open(dn + 'print.php?fid=' + familyID,"_blank","toolbar=no, scrollbars=no, resizable=no, top=500, left=500, width=1100, height=700");
  };

  // save tree
  $scope.saveTree = function() {
	 $('#treemsg').html('<h3>Saving....</h3>');
	 Save(defaultUName, 0);
	 prepareMsg("Tree Saved Successfully");
  };
  // save & proceed
  $scope.saveTree02 = function() {
	  $('#treemsg').html('<h3>Saving....</h3>');
	  Save(defaultUName, 1);
	  prepareMsg("Tree Saved Successfully");
  };
  // proceed only
  $scope.saveTree03 = function() {
	  $('#treemsg').html('<h3>Saving....</h3>');
	  Save(defaultUName, 2);
	  prepareMsg("Tree Saved Successfully");
  };
}