//--------------------------------------------------------------
// Author : Muhammad Irfan
// jugnoon.com / support@jugnoon.com
//---------------------------------------------------------------
// Tree Reference IDs
// main (1: left, 5: right, 6: no connects), parent (2), child (3), normal (4), index (i)
var iDs = [];
var nConnects = [];
var conWidth = 100;
var conHeight = 40;
var pWidth = 90;
var pHeight = 70;
var zout = 0.05;
var currZoom = 1;
var tpt = 0;
var lpt = 0;
var flowchartSettings = {
	stub: [30, 30],
	gap: 0,
	cornerRadius: cornerRadius,
	alwaysRespectStubs: true
};
var bezierSettings = {
	curviness: 5
};
var statemachineSettings = {
	curviness: 5
};
var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6

var cbkColor = "";
var cftColor = "";
var cbrColor = "";
var currNodeID = 0;
var cntCssClass = ".vcart-inner";
$(function () {
	loadInit();
	//*******************************************************
	// Color Box
	//*******************************************************
	$('.color-box').colpick({
		colorScheme:'light',
		layout:'rgbhex',
		color:'ff8800',
		onSubmit:function(hsb,hex,rgb,el) {
			var id = $(el).attr('id');
			switch(id) {
			   case "gcolorbk":
			   bkColor = '#'+hex;
			   break;
			   case "gcolorfont":
			   ftColor = '#'+hex;
			   break;
			   case "gcolorborder":
			   brColor = '#'+hex;
			   break;
			   case "colorbk":
			   cbkColor = '#'+hex;
			   break;
			   case "colorfont":
			   cftColor = '#'+hex;
			   break;
			   case "colorborder":
			   cbrColor = '#'+hex;
			   break;
			   case "lnColor":
			     strokeColor = '#'+hex;
			   break;
			   case "lhoverColor":
			     hoverPaintStyle = '#'+hex;
			   break;
			}
			$(el).css('background-color', '#'+hex);
			$(el).colpickHide();
		}
	});
	//*******************************************************
	// Zoom In / Out Script
	//*******************************************************
	$('#chart-demo').on('mousewheel', function (e) {
	    // enable zoom on chrome or tree is in ready only mode
		//if(isChrome || readOnly) {
			var top = ((getHeight() + conHeight) / 2) - ($("#vcart").height() / 2);
			var left = ((getWidth() + conWidth) / 2) - ($("#vcart").width() / 2);
			if (e.deltaY == 1) {
				currZoom = currZoom + zout;
				tpt = tpt + 125;
				lpt = lpt + 125;
				$("#vcart").scrollTop(top + tpt);
				$("#vcart").scrollLeft(left + lpt);
			} else {
				currZoom = currZoom - zout;
				tpt = tpt - 125;
				lpt = lpt - 125;
				$("#vcart").scrollTop(top + tpt);
				$("#vcart").scrollLeft(left + lpt);
			}
			$("#chart-demo").css("zoom", currZoom);
			$("#chart-demo").css("-moz-transform", "Scale(" + currZoom + ")");
			$("#chart-demo").css("-moz-transform-origin", "0 0");
			$("#chart-demo").css("-webkit-transform", currZoom);
			$("#chart-demo").css("-ms-transform", currZoom);
			$("#chart-demo").css("-o-transform", currZoom);
			$("#chart-demo").css("transform", currZoom);
			//jsPlumb.setZoom(currZoom);
		//}
	});
	//*******************************************************
	// PlUploader Script
	//*******************************************************
	var uploader = new plupload.Uploader({
		runtimes: 'gears,html5,flash,silverlight',
		browse_button: 'tchange',
		container: 'modaledthumb',
		max_file_size: maxFileSize,
		url: dn + plUploadHandler,
		flash_swf_url: dn + plupload_flash_url,
		silverlight_xap_url: dn + plupload_silverlight_url,
		filters: [{
			title: "Image files",
			extensions: "jpg,gif,png"
		}]
	});
	uploader.bind('Init', function (up, params) {});
	$('#modaledthumb').on({
		click: function (e) {
			uploader.start();
			e.preventDefault();
			return false;
		}
	}, '#tchange');
	uploader.init();
	uploader.bind('FilesAdded', function (up, files) {
		var count = 0;
		$('#umsg').html("");
		$.each(files, function (i, file) {
			count++;
		});
		if (count > 1) {
			$.each(files, function (i, file) {
				uploader.removeFile(file);
			});
			Display_Message("#modalmsg", "Please select only one photo!", 0, 1);
			return false;
		} else {
			uploader.start();
		}
		up.refresh(); // Reposition Flash/Silverlight
	});
	uploader.bind('UploadProgress', function (up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
	});
	uploader.bind('Error', function (up, err) {
		$('#modalmsg').append("<div>Error: " + err.code +
			", Message: " + err.message +
			(err.file ? ", File: " + err.file.name : "") +
			"</div>"
		);
		up.refresh(); // Reposition Flash/Silverlight
	});
	uploader.bind('FileUploaded', function (up, file, info) {
		var rpcResponse = JSON.parse(info.response);
		var result;
		if (typeof (rpcResponse) != 'undefined' && rpcResponse.result == 'OK') {
			// uploaded successfully
			if (rpcResponse.url != '' && rpcResponse.filetype == 'image') {
				$('#modaledchange').attr('src', rpcResponse.url);
				var nodeid = $('#findex').html();
				$('#' + nodeid).data('photo', rpcResponse.url);
			} else { /* normal */ }
		} else {
			var code;
			var message;
			if (typeof (rpcResponse.error) != 'undefined') {
				code = rpcResponse.error.code;
				message = rpcResponse.error.message;
				if (message == undefined || message == "") {
					message = rpcResponse.error.data;
				}
			} else {
				code = 0;
				message = "Error uploading the file to the server";
			}
			Uploader.trigger("Error", {
				code: code,
				message: message,
				file: File
			});
		}
	});
	$('#einfo').on({
		click: function (e) {
			var nodeid = $('#findex').text();
			var uName = trim(setName());
			if (uName.length > 0) {
				// mark selected node as updated
				$('#' + nodeid).data('isupd', '1');
				$('#' + nodeid).html(setName());
				$('#' + nodeid).data('name', setName());
				$('#' + nodeid).data('fname', $('#txt_fname').val());
				$('#' + nodeid).data('sname', $('#txt_sname').val());
			}
			// photo setup
			if (typeof $('#' + nodeid).data('photo') != 'undefined')
				$('#' + nodeid).append('<br /><img class="img-rounded" src="' + $('#' + nodeid).data('photo') + '" style="width:' + pWidth + 'px; height:' + pHeight + 'px;" alt="' + setName() + ' Photo">');
			if ($("#modalckmale:checked").val() != undefined) {
				// male selected
				$('#' + nodeid).data('gender', 'male');
			} else if ($("#modalckfemale:checked").val() != undefined) {
				$('#' + nodeid).data('gender', 'female');
			}
			if(cbkColor != "")
			   $('#' + nodeid).attr('data-bkcolor', cbkColor);
            if(cftColor != "")
			   $('#' + nodeid).attr('data-ftcolor', cftColor);
			if(cbrColor != "")
			   $('#' + nodeid).attr('data-brcolor', cbrColor);
			
			if(cbkColor != "" && cftColor != "" && cbrColor != "")
			    $('#' + nodeid).css({'background-color': cbkColor,'color': cftColor, 'border': '1px solid ' + cbrColor});

            var ctitle = trim($('#txt_nd_title').val());
			if (ctitle != "")
				$('#' + nodeid).data('title', ctitle);
				
			var desc = trim($('#txt_nd_desc').val());
			if (desc != "")
				$('#' + nodeid).data('desc', desc);
				
			// contact info
			var cemail = trim($('#txt_email').val());
			if (cemail != "")
				$('#' + nodeid).data('email', cemail);
			var cweb = trim($('#txt_website').val());
			if (cweb != "")
				$('#' + nodeid).data('website', cweb);
			var ctel = trim($('#txt_tel').val());
			if (ctel != "")
				$('#' + nodeid).data('tel', ctel);
			var mob = trim($('#txt_mobile').val());
			if (mob != "")
				$('#' + nodeid).data('mobile', mob);
			// biographical info
			var profession = trim($('#txt_profession').val());
			if (profession != "")
				$('#' + nodeid).data('profession', profession);
			var company = trim($('#txt_company').val());
			if (company != "")
				$('#' + nodeid).data('company', company);
			var interests = trim($('#txt_interests').val());
			if (interests != "")
				$('#' + nodeid).data('interests', interests);
			var bio = trim($('#txt_bio').val());
			if (bio != "")
				$('#' + nodeid).data('bio', bio);
			
			
			// clear custom colors
			resetColors();
						
			$('#cModal').modal('hide');
			return false;
		}
	}, '#btnmodok');
	
	$('#txt_fname').on('input', function () {
		$('#aheading').html(setName());
	})
	$('#txt_sname').on('input', function () {
		$('#aheading').html(setName());
	})
	$('#txt_nd_title').on('input', function () {
		$('#aheading').html($('#txt_nd_title').val());
	})
	$('#modaldmode').on({
		click: function (e) {
			togglePanel('', 0); // 0: edit mode
			return false;
		}
	}, '#modaleditinfo');

	$('#vcart').on({
		click: function (e) {
			// get current node id 
			currNodeID = $(this).data("id");
			$('.window').each(function () {
				$(this).removeClass('active');
			});
			$(this).addClass('active');
			// reset modal form
			$('#modalfrm')[0].reset();
			// toggle mode();
			togglePanel(this, 2); // type: auto
			// load current node data
			loadModalData(this);
			if ($(this).data("id").indexOf("2p") != -1) {
				$('#modal6').hide(); // disable adding child nodes if its parent
			} else {
				$('#modal6').show();
			}
			if ($(this).data("id").indexOf("3p") != -1) {
				$('#modal5').hide(); // disable adding parent nodes if its parent
			} else {
				$('#modal5').show();
			}
			// remove photo if exist
			var photourl = defaultThumbUrl;
			if (typeof $(this).data('photo') != 'undefined')
				photourl = $(this).data('photo');
			$('#modaledchange').attr('src', photourl);
			
			var gd = $(this).data('gender');
			if (gd == 'male')
				$("#modalckmale").prop("checked", true);
			else
				$("#modalckfemale").prop("checked", true);
			
			$('#findex').html($(this).data("id"));
			var childPos = $(this).offset();
			var parentPos = $('.vcart-inner').offset();
			var childOffset = {
				top: childPos.top - parentPos.top,
				left: childPos.left - parentPos.left
			}
			$('#postop').html(childOffset.top);
			$('#posleft').html(childOffset.left);
			$('#aheading').html(genFullName(this));
			$('#cModal').modal('show');
			rpanels();
			return false;
		}
	}, '.window');
	//---------------------------------------------------------------
	// Modal Processings
	//---------------------------------------------------------------
	$('#modaldmode').on({
		click: function (e) {
			var nodeid = $('#findex').text();
			$('#' + nodeid).data('deleted', 'yes');
			$('#' + nodeid).hide();
			$('#cModal').modal('hide');
			return false;
		}
	}, '#modaldelete');
	$('.navbar-nav').on({
		click: function (e) {
			$("li").removeClass("active");
			$(this).addClass("active");
		}
	}, 'li');
	$('#cModal').on({
		click: function (e) {
			rpanels();
			return false;
		}
	}, '.acancel');
	
		
	$('#cModal').on({
		click: function (e) {
			AddPNode(0);
			return false;
		}
	}, '#_parentNode');
	
	$('#cModal').on({
		click: function (e) {
	    	AddPNode(1);
			return false;
		}
	}, '#_childeNode');


   $('#cModal').on({
	    mouseenter: function () {
	        $(".dur").show();
	    },
	
	    mouseleave: function () {
	         $(".dur").hide();
	    }
   }, '#_tThumb');
   
   $('#cModal').on({
		click: function (e) {
		    AddPNode(2);
			return false;
		}
	}, '#_leftNode');
	
   $('#cModal').on({
		click: function (e) {
			 AddPNode(3);
			return false;
		}
	}, '#_rightNode');
	
	$('#cModal').on({
		click: function (e) {
			 AddPNode(4);
			return false;
		}
	}, '#_createNode');
	
	$('#cModal').on({
		click: function (e) {
			 $('#epartner').show();
			 $('#einfo').hide();
		 	 $('#esubling').hide();
			return false;
		}
	}, '#_partnerOptions');

	$('#tcontainer').on({
		click: function (e) {
		    $('#treemsg').html('<h3>Saving....</h3>');
	        Save(defaultUName, 0);
	        prepareMsg("Chart Saved Successfully");
			return false;
		}
	}, '#_saveTree');
	
	$('#tcontainer').on({
		click: function (e) {
		 	$('#treemsg').html('<h3>Saving....</h3>');
		    Save(defaultUName, 1);
			prepareMsg("Tree Saved Successfully");
			return false;
		}
	}, '#_saveTree02');
	
    $('#tcontainer').on({
		click: function (e) {
			var swidth = $("#txt_linewidth").val();
			if(swidth != "") {
				  strokeLineWidth = swidth;
				  hoverstrokeLineWidth = swidth;
			}
			return false;
		}
	}, '#btn_updsetting');
	$('#tcontainer').on({
		click: function (e) {
		 	$('#treemsg').html('<h3>Saving....</h3>');
		    Save(defaultUName, 2);
			prepareMsg("Tree Saved Successfully");
			return false;
		}
	}, '#_saveTree03');
	
	$('#tcontainer').on({
		click: function (e) {
		    window.open(dn + 'print.php?fid=' + chartID,"_blank","toolbar=no, scrollbars=no, resizable=no, top=500, left=500, width=1100, height=700");
			return false;
		}
	}, '#_printTree');
	
	$('#tcontainer').on({
		click: function (e) {
		   document.location = dn + '' + redirectPageName + '&scroll=true';
			return false;
		}
	}, '#_smoothScroll');
	
	$('#tcontainer').on({
		click: function (e) {
		    document.location = dn + '' + redirectPageName + '&connect=0';	
			return false;
		}
	}, '#_flowchart');
	
	$('#tcontainer').on({
		click: function (e) {
		    document.location = dn + '' + redirectPageName + '&connect=1';	
			return false;
		}
	}, '#_bezier');
	
	$('#tcontainer').on({
		click: function (e) {
		    document.location = dn + '' + redirectPageName + '&connect=2';	
			return false;
		}
	}, '#_statemachine');
	
	$('#tcontainer').on({
		click: function (e) {
		    document.location = dn + '' + redirectPageName + '&readonly=true';	
			return false;
		}
	}, '#_readonly');
	//*********************************************************
	// JS Plumb Options
	//*********************************************************
	jsPlumb.ready(function () {
		if (chartID > 0) {
			LoadData(chartID);
		} else {
			AddDiv(conWidth, conHeight, 0, 0, '', 'Me', '1m', 0);
		}
	});
});

function loadInit() {
	if (readOnly) {
		$("#vcart").overscroll();
		$("#modaltopnav").hide();
	}
	if (smoothScroll)
		$("#vcart").overscroll();
	$("#vcart").scrollTop(((getHeight() + conHeight) / 2) - ($("#vcart").height() / 2));
	$("#vcart").scrollLeft(((getWidth() + conWidth) / 2) - ($("#vcart").width() / 2));
}

function resetColors() {
	cbkColor = "";
	cftColor = "";
	cbrColor = "";
}		

function trim(val) {
	return val.replace(/^\s+|\s+$/g, '');
}
// 0: edit mod, 1: display mode, 2 : auto
function togglePanel(obj, type) {
	if (type == 2) {
		if (typeof $(obj).data('isupd') != 'undefined') {
			// display mode
			$('#modalemode').hide();
			getNodeDisplay(obj);
		} else {
			$('#modalemode').show();
			$('#modaldmode').hide();
		}
	} else if (type == 0) {
		// edit mode
		$('#modalemode').show();
		$('#modaldmode').hide();
	} else if (type == 1) {
		// display mode
		$('#modalemode').hide();
		getNodeDisplay(obj);
	}
}

function getNodeItem(caption, name) {
	var str = '<div class="row item_pad"><div class="col-md-3">';
	str += caption;
	str += '</div><div class="col-md-9">';
	str += name;
	str += '</div></div>';
	return str;
}

function getNodeDisplay(obj) {
	var str = '<div class="row"><div class="col-md-3"><div class="pd_5">';
	var photourl = dn + 'images/holder.png';
	if (typeof $(obj).data('photo') != 'undefined')
		photourl = $(obj).data('photo');
	str += '<img src="' + photourl + '" style="width:100px; height:100px;" alt="Profile Photo" class="img-rounded">';
	str += '</div></div>';
	str += '<div class="col-md-9">';
	str += '<strong class="xxmedium-text bold">General Info</strong>';
	if (typeof $(obj).data('title') != 'undefined') {
	  if( $(obj).data('title') != "")
		 str += getNodeItem('Title', $(obj).data('title'));
	}
	if (typeof $(obj).data('desc') != 'undefined') {
		if($(obj).data('desc') != "")
		   str += getNodeItem('Description', $(obj).data('desc'));
	}
	var name = genFullName(obj);
	if (name != "") {
		if (name.indexOf("Default") == -1)
		   str += getNodeItem('Name', name);
	}
	var gender = trim($(obj).data('gender'));
	if (gender != "")
		str += getNodeItem('Gender', gender);
	str += '<hr />';
	str += '<strong class="xxmedium-text bold">Contact Info</strong>';
	// contact information
	if (typeof $(obj).data('email') != 'undefined')
		str += getNodeItem('Email', $(obj).data('email'));
	if (typeof $(obj).data('website') != 'undefined')
		str += getNodeItem('Website', $(obj).data('website'));
	if (typeof $(obj).data('tel') != 'undefined')
		str += getNodeItem('Home Tel', $(obj).data('tel'));
	if (typeof $(obj).data('mobile') != 'undefined')
		str += getNodeItem('Mobile', $(obj).data('mobile'));
	str += '<hr />';
	str += '<strong class="xxmedium-text bold">Biographical</strong>';
	if (typeof $(obj).data('birthplace') != 'undefined')
		str += getNodeItem('Birth Place', $(obj).data('birthplace'));
	if (typeof $(obj).data('deathplace') != 'undefined')
		str += getNodeItem('Death Place', $(obj).data('deathplace'));
	if (typeof $(obj).data('profession') != 'undefined')
		str += getNodeItem('Profession', $(obj).data('profession'));
	if (typeof $(obj).data('company') != 'undefined')
		str += getNodeItem('Company', $(obj).data('company'));
	if (typeof $(obj).data('interests') != 'undefined')
		str += getNodeItem('Interests', $(obj).data('interests'));
	if (typeof $(obj).data('bio') != 'undefined')
		str += getNodeItem('Bio Info', $(obj).data('bio'));
	str += '<hr />';
	str += '<div class="row"><div class="col-lg-12">';
	if (!readOnly) {
		str += '<button id="modaleditinfo" class="btn btn-primary btn-sm">Edit Information</button>&nbsp;';
		if ($(obj).attr('id') != "1m")
			str += '<button id="modaldelete" class="btn btn-danger btn-sm">Delete</button>&nbsp;';
	}
	str += '<button class="btn btn-sm" data-dismiss="modal" aria-hidden="true">Cancel</button>';
	str += '<hr />';
	str += '</div>'; // close col-md-9                
	str += '</div>'; // close row
	$('#modaldmode').html(str);
	$('#modaldmode').show(); // display if hidden
}

function loadModalData(obj) {
	// photo url
	var photourl = dn + 'images/holder.png';
	if (typeof $(obj).data('photo') != 'undefined')
		photourl = $(obj).data('photo');
	if (typeof $(obj).data('fname') != 'undefined')
		$('#txt_fname').val($(obj).data('fname'));
	if (typeof $(obj).data('sname') != 'undefined')
		$('#txt_sname').val($(obj).data('sname'));
	if (typeof $(obj).data('isupd') != 'undefined') {
		if ($(obj).data('gender') == 'male')
			$("#modalckmale").prop("checked", true);
		else
			$("#modalckfemale").prop("checked", true);
	}
	if (typeof $(obj).data('title') != 'undefined')
		$('#txt_nd_title').val($(obj).data('title'));
	if (typeof $(obj).data('desc') != 'undefined')
		$('#txt_nd_desc').val($(obj).data('desc'));
	
	// contact information
	if (typeof $(obj).data('email') != 'undefined')
		$('#txt_email').val($(obj).data('email'));
	if (typeof $(obj).data('website') != 'undefined')
		$('#txt_website').val($(obj).data('website'));
	if (typeof $(obj).data('tel') != 'undefined')
		$('#txt_tel').val($(obj).data('tel'));
	if (typeof $(obj).data('mobile') != 'undefined')
		$('#txt_mobile').val($(obj).data('mobile'));
	// biographical information
	if (typeof $(obj).data('profession') != 'undefined')
		$('#txt_profession').val($(obj).data('profession'));
	if (typeof $(obj).data('company') != 'undefined')
		$('#txt_company').val($(obj).data('company'));
	if (typeof $(obj).data('interests') != 'undefined')
		$('#txt_interests').val($(obj).data('interests'));
	if (typeof $(obj).data('bio') != 'undefined')
		$('#txt_bio').val($(obj).data('bio'));
}

function genFullName(obj) {
	var fullname = "";
	if (typeof $(obj).data('fname') != 'undefined')
		fullname = $(obj).data('fname');
	if (typeof $(obj).data('sname') != 'undefined')
		fullname = fullname + ' ' + $(obj).data('sname');
	if (trim(fullname) == "")
		fullname = $(obj).data("name");
	return fullname;
}

function genFirstName(obj) {
	var fullname = "";
	if (typeof $(obj).data('fname') != 'undefined')
		fullname = $(obj).data('fname');
	if (trim(fullname) == "")
		fullname = $(obj).data("name");
	return fullname;
}

function setName() {
	var name = $('#txt_fname').val() + ' ' + $('#txt_sname').val();
	var title = trim($('#txt_nd_title').val());
	if(trim(name) != "" && title != "")
	    return name + '<br /><strong>' + title + "</strong>";
	else if (trim(name) != "")
	    return name;
	else if(title != "")
	    return title;
	else
	    return defaultUName;
}

function rpanels() {
	$('#epartner').hide();
	$('#einfo').show();
	$('#esubling').hide();
}

function getWidth() {
	return $("#chart-demo").width() - conHeight;
}

function getHeight() {
	return $("#chart-demo").height() - conHeight;
}

function isEven(n) {
	return parseFloat(n) && (n % 2 == 0);
}

function isOdd(n) {
	return parseFloat(n) && (n % 2 == 1);
}

function setOffset(id, tdiff, ldiff) {
	var oset = $('#' + id).offset();
	var tpost = oset.top;
	var lpost = oset.left;
	tpost = tpost + tdiff;
	lpost = lpost + ldiff;
	$("#" + id).offset({
		top: tpost,
		left: lpost
	})
}
/* add parent nodes */
var oddCounter = 0;
var evenCounter = 0;
function AddPNode(type)
{	
	var caption = defaultUName;
	var diff = 0; //(conWidth - 275); // right side
	var partindex = 2; // (2p -> parent node)
	var rel = 1; // single father or mother
	var childPos = $('#' + currNodeID).offset();
	var parentPos = $(cntCssClass).offset();
	var childOffset = {
		top: childPos.top - parentPos.top,
		left: childPos.left - parentPos.left
	}
	var t = childOffset.top - (conHeight + 190); // on top side
	var l = childOffset.left - diff;
	if(type == 1) {
	    t = childOffset.top + (conHeight + 190); // on bottom side
		partindex = 3;
		rel = 2;
	} else if (type > 1) {
		t = childOffset.top; // on same line
		
		switch(type)
		{			
			case 2:
			    partindex = 1; // 1p -> left
				rel = 3;
				diff = (conWidth + 150); // left side
				l = childOffset.left - diff;
			break;
			case 3:
			    partindex = 5; // 1p -> right
				diff = (conWidth + 100); // right side
				l = childOffset.left + diff;
				rel = 4;
			break;
			case 4:
			    partindex = 6; // no connects
				rel = 5;
				diff = (conWidth + 100); // right side
				l = childOffset.left - diff;
			break;
		}
	}
	
	var pid = currNodeID + '-' + partindex + 'p';
	if ($('#' + pid).length > 0) {
		// 50 occurances
		var x = 50;
		var isfound = false;
		while (x > 0) {
			if ($('#' + pid + '' + x).length > 0) {
				var cid = x + 1;
				pid = pid + cid;
				var d = cid + 1;
				if(d > 2 && isOdd(d) && type < 2) {
				   oddCounter = oddCounter + 1;
				   l = l - (conWidth + 20) * (cid-oddCounter);
				} else {
				   if(type < 2) {
				     evenCounter = evenCounter + 1;
				     l = l + (conWidth + 20) * (cid-evenCounter);
				   } else {
					   if(type == 2)
					      l = l - (conWidth + 20) * (cid); 
					   else if(type == 3)
					      l = l + (conWidth + 20) * (cid); 
					   else
					      l = l - (conWidth + 20) * (cid); 
				   }
				}
				isfound = true;
				caption = caption + ' ' + (cid + 1);
				continue;
			}
			x = x - 1;
		}
		if (!isfound) {
			pid = pid + '1';
			if(type < 2)
				l = l + ((conWidth + 20) * 1);
			else {
			   if(type == 2)
				  l = l - (conWidth + 20) * 1; 
			   else if(type == 3)
				  l = l + (conWidth + 20) * 1; 
			   else
				  l = l - (conWidth + 20) * 1; 
			}
			caption = caption + ' 2';
		}
	} else {
		// reset counter
		oddCounter = 0;
		evenCounter = 0;
	}
	var name = genFirstName('#' + currNodeID);
	AddDiv(conWidth, conHeight, t, l, '', caption, pid, rel);
	$('#cModal').modal('hide');
}

function genPlumConnect(id, dest, sourcepos, destpos) {
	var settings = flowchartSettings;
	if (connectStyle == 'Bezier')
		settings = bezierSettings;
	else if (connectStyle == 'StateMachine')
		settings = statemachineSettings;
	jsPlumb.connect({
		source: id,
		target: dest,
		detachable: false,
		paintStyle: {
			strokeStyle: strokeColor,
			lineWidth: strokeLineWidth,
			joinstyle: "round"
		},
		hoverPaintStyle: {
			strokeStyle: hoverPaintStyle,
			lineWidth: hoverstrokeLineWidth
		},
		dragOptions: {
			cursor: "pointer",
			zIndex: 2000
		},
		/* enable to display arrows */
			/*overlays:[ 
				[ "Arrow", { location:0.1, id:"myLabel", direction: -1} ]
		],*/
		endpoint: "Blank",
		anchors: [sourcepos, destpos],
		connector: [connectStyle, settings]
	});
}
function AddDiv(w, h, t, l, c, txt, id, rel) {
	if (readOnly)
		return; // don't create any item if readonly
	if ($('#' + id).length > 0)
		return; // don't create any item if already exist
	var cwidth = 0;
	if (l == 0) cwidth = getWidth() / 2;
	else cwidth = l;
	var cheight = 0;
	if (t == 0) cheight = getHeight() / 2;
	else cheight = t;
	var css = "";
	var gender = "male";
	if (c != "") {
		gender = c;
		css = c;
	}
	var cap = '';
	if (txt != "")
		cap = txt;
	var dUser = false;
	if (cap == 'Me') {
		if (defaultUName != "")
			cap = defaultUName;
		css = css + " author";
		dUser = true;
	}
	var bcr = bkColor;
	if(cbkColor != "")
	   bcr = cbkColor;
	var fcr = ftColor;
	if(cftColor != "")
	   fcr = cftColor;
	var brcr = brColor;
	if(cbrColor != "")
	   brcr = cbrColor;
	   
	var Div = $('<div>', {
		id: id
	}, {
		class: 'window'
	}).text(cap).css({
		'min-height': h,
		'width': w,
		'background-color': bcr,
		'color': fcr,
		'border': '1px solid ' + brcr,
		top: cheight,
		left: cwidth,
		position: 'absolute'
	}).attr("data-id", id).appendTo('#chart-demo');
	var sourceid = $('#findex').html();
	var relcap = "pNode";
	switch (rel) {
	case 1:
		// parent node
		var anchorPos = "BottomCenter";
		genPlumConnect(id, currNodeID, anchorPos, "TopCenter");
		prepareConnectInfo(id, currNodeID, anchorPos, "TopCenter", relcap);
		break;
	case 2:
		var anchorPos = "TopCenter";
		genPlumConnect(id, currNodeID, anchorPos, "BottomCenter");
		prepareConnectInfo(id, currNodeID, anchorPos, "BottomCenter", relcap);
		break;
	case 3:
	    // left side
		var anchorPos = "Right";
		genPlumConnect(id, currNodeID, anchorPos, "Left");
		prepareConnectInfo(id, currNodeID, anchorPos, "Left", relcap);
		break;
	case 4:
	    // right side
		var anchorPos = "Left";
		genPlumConnect(id, currNodeID, anchorPos, "Right");
		prepareConnectInfo(id, currNodeID, anchorPos, "Right", relcap);
		break;
	case 5:
	    // no connection needed
		break;
	}
	
	jsPlumb.draggable($(Div));
	$(Div).addClass('chart-demo window');
	$(Div).attr('data-name', cap);
	if (dUser == true) {
		if (defaultFName != "")
			$(Div).attr('data-fname', defaultFName);
		if (defaultSName != "")
			$(Div).attr('data-sname', defaultSName);
		$(Div).attr('data-author', "1");
	} else {
		$(Div).attr('data-fname', cap);
	}
	$(Div).attr('data-gender', gender);
	$(Div).attr('data-postop', cheight);
	$(Div).attr('data-posleft', cwidth);
	   
	$(Div).attr('data-bkcolor', bcr);
	$(Div).attr('data-ftcolor', fcr);
	$(Div).attr('data-brcolor', brcr);
	iDs.push({
		eid: id,
		position: cheight
	});
	jsPlumb.repaintEverything();
	//alert(nConnects.length);
}

function Save(uname, checktype) {
	if (iDs.length > 0) {
		var nodes = [];
		for (var i = 0; i < iDs.length; i++) {
			var nodeid = iDs[i].eid;
			// store position of elements
			var childPos = $('#' + nodeid).offset();
			var parentPos = $('.vcart-inner').offset();
			var childOffset = {
				top: childPos.top - parentPos.top,
				left: childPos.left - parentPos.left
			}
			var tpost = childOffset.top; //$("#" + nodeid).data('postop');
			var lpost = childOffset.left; //$("#" + nodeid).data('posleft');
			var nodeinfo = $("#" + nodeid).html();
			var name = $("#" + nodeid).data('name');
			var gender = $("#" + nodeid).data('gender');
			var photourl = '';
			if (typeof $("#" + nodeid).data('photo') != 'undefined')
				photourl = $("#" + nodeid).data('photo');
			var fname = "";
			if (typeof $("#" + nodeid).data('fname') != 'undefined')
				fname = $("#" + nodeid).data('fname');
			var sname = "";
			if (typeof $("#" + nodeid).data('sname') != 'undefined')
				sname = $("#" + nodeid).data('sname');
			var title = "";
			if (typeof $("#" + nodeid).data('title') != 'undefined')
				title = $("#" + nodeid).data('title');	
			var desc = "";
			if (typeof $("#" + nodeid).data('desc') != 'undefined')
				desc = $("#" + nodeid).data('desc');	
			
			var email = "";
			if (typeof $("#" + nodeid).data('email') != 'undefined')
				email = $("#" + nodeid).data('email');
			var website = "";
			if (typeof $("#" + nodeid).data('website') != 'undefined')
				website = $("#" + nodeid).data('website');
			var tel = "";
			if (typeof $("#" + nodeid).data('tel') != 'undefined')
				tel = $("#" + nodeid).data('tel');
			var mobile = "";
			if (typeof $("#" + nodeid).data('mobile') != 'undefined')
				mobile = $("#" + nodeid).data('mobile');
			
			var profession = "";
			if (typeof $("#" + nodeid).data('profession') != 'undefined')
				profession = $("#" + nodeid).data('profession');
			var company = "";
			if (typeof $("#" + nodeid).data('company') != 'undefined')
				company = $("#" + nodeid).data('company');
			var interests = "";
			if (typeof $("#" + nodeid).data('interests') != 'undefined')
				interests = $("#" + nodeid).data('interests');
			var bio = "";
			if (typeof $("#" + nodeid).data('bio') != 'undefined')
				bio = $("#" + nodeid).data('bio');
			var chartid = 0;
			if(typeof chartID != "undefined")
			  chartid = chartID;
			else if (typeof $("#" + nodeid).data('chartid') != 'undefined')
				chartid = $("#" + nodeid).data('chartid');
			//alert(chartid + " chart id");
			var isdeleted = '';
			if (typeof $("#" + nodeid).data('deleted') != 'undefined')
				isdeleted = '1';
						
			var chartname = "";
			if($("#" + titleElementID).length > 0)
			  chartname = $("#" + titleElementID).val();
            var bkcolor = bkColor;
			var ftcolor = ftColor;
			var brcolor = brColor;
			if (typeof $("#" + nodeid).data('bkcolor') != 'undefined')
			   bkcolor = $("#" + nodeid).data('bkcolor');
			if (typeof $("#" + nodeid).data('ftcolor') != 'undefined')
			   ftcolor = $("#" + nodeid).data('ftcolor');
			if (typeof $("#" + nodeid).data('brcolor') != 'undefined')
			  brcolor = $("#" + nodeid).data('brcolor');
			nodes.push({
				nodeid: nodeid,
				tpost: tpost,
				lpost: lpost,
				name: name,
				gender: gender,
				title: title,
				desc: desc,
				photourl: photourl,
				fname: fname,
				sname: sname,
				email: email,
				website: website,
				tel: tel,
				mobile: mobile,
				profession: profession,
				company: company,
				interests: interests,
				bio: bio,
				chartid: chartid,
				isdeleted: isdeleted,
				username: uname,
				checktype: checktype,
				bkcolor: bkcolor,
				ftcolor: ftcolor,
				brcolor: brcolor,
				cradius: cornerRadius,
				cstyle: connectStyle,
				strokecolor: strokeColor,
				hovercolor: hoverPaintStyle,
				linewidth: strokeLineWidth,
				chartname: chartname
			});
		}
		var jsonString = JSON.stringify(nodes);
		$.ajax({
			type: 'POST',
			url: dn + '' + hd,
			data: JSON.stringify(nodes),
			contentType: 'application/json; charset=utf-8',
            dataType: 'json',
			success: function (msg) {
				if(msg != "0") {
					var _value = parseInt(msg);
					var isnum = /^\d+$/.test(_value);
					if (isnum) {
						chartID = parseInt(_value, 10);
						$('#fid').html(_value);
						prepareMsg("Data Saved Successfully");
						saveConnects(checktype);
					} else {
						prepareMsg(msg);
					}
				}
				else if (msg == "-1")
					prepareMsg("Error occured while creating Chart ID");
				else if (msg.indexOf('http') != -1)
					document.location = msg;
			},
		});
	}
}

function prepareConnectInfo(source, dest, sourcepos, destpos, relation) {
	var chartid = 0;
	if (typeof $("#" + source).data('chartid') != 'undefined')
		chartid = $("#" + source).data('chartid');
	nConnects.push({
		chartid: chartid,
		source: source,
		dest: dest,
		sourcepos: sourcepos,
		destpos: destpos,
		relation: relation
	});
}

function saveConnects(checktype) {
	if (nConnects.length > 0) {
		var connects = [];
		for (var i = 0; i < nConnects.length; i++) {
			var sourceid = nConnects[i].source;
			var destid = nConnects[i].dest;
			var sname = genFullName('#' + sourceid);
			var dname = genFullName('#' + destid);
			var chartid = nConnects[i].chartid;
			if(typeof chartID != "undefined")
			  chartid = chartID;
		
			var isdeleted = '';
			if (typeof $("#" + sourceid).data('deleted') != 'undefined' || typeof $("#" + destid).data('deleted') != 'undefined')
				isdeleted = '1';
			connects.push({
				chartid: chartid,
				source: sourceid,
				dest: destid,
				sourcepos: nConnects[i].sourcepos,
				destpos: nConnects[i].destpos,
				sname: sname,
				dname: dname,
				isdeleted: isdeleted,
				checktype: checktype
			});
		}
		$.ajax({
			type: 'POST',
			url: dn + '' + sconnects,
			data: {
				output: connects
			},
			success: function (msg) {
				if (msg == "")
					prepareMsg('Your Chart Saved Successfully');
				else if (msg.indexOf('http') != -1)
					document.location = msg;
				else
					prepareMsg(msg);
			},
		});
	}
}

function LoadData(id) {
	$.ajax({
		type: 'GET',
		url: dn + '' + lhandler,
		data: 'fid=' + id,
		success: function (msg) {
			var nodes = JSON.parse(msg);
			for (var i = 0; i < nodes.length; i++) {
				getNode(nodes[i]);
			}
			// load connects
			LoadConnects(id); 
		},
	});
}

function LoadConnects(id) {
	$.ajax({
		type: 'GET',
		url: dn + '' + chandler,
		data: 'fid=' + id,
		success: function (msg) {
			var connects = JSON.parse(msg);
			for (var i = 0; i < connects.length; i++) {
				genConnects(connects[i]);
			}
		},
	});
}

function genConnects(obj) {
	// reload settings and save it in proper variable for again saving
	strokeColor = obj.linecolor;
    hoverPaintStyle = obj.linehovercolor;
    strokeLineWidth = obj.linewidth;
    hoverstrokeLineWidth = obj.linewidth;
	connectStyle = obj.connectstyle;
	if($("#" + titleElementID).length >0)
	   $("#" + titleElementID).val(obj.title);
	var settings = flowchartSettings;
	if (obj.connectstyle == 'Bezier')
		settings = bezierSettings;
	else if (obj.connectstyle == 'StateMachine')
		settings = statemachineSettings;
	if($('#' + obj.selementid).length > 0 && $('#' + obj.delementid).length >0)
	{
		jsPlumb.connect({
			source: obj.selementid,
			target: obj.delementid,
			detachable: false,
			paintStyle: {
				strokeStyle: obj.linecolor,
				lineWidth: obj.linewidth
			},
			hoverPaintStyle: {
				strokeStyle: obj.linehovercolor,
				lineWidth: obj.linewidth
			},
			dragOptions: {
				cursor: "pointer",
				zIndex: 2000
			},
			/* enable to display arrows */
			/*overlays:[ 
				[ "Arrow", { location:0.1, id:"myLabel", direction: -1} ]
			],*/
			endpoint: "Blank",
			anchors: [obj.sconnectpos, obj.dconnectpos],
			connector: [connectStyle, settings]
		});
		prepareConnectInfo(obj.selementid, obj.delementid, obj.sconnectpos, obj.dconnectpos, "");
	}
}
function getFieldValue(obj) {
	if(obj != undefined)
	   return obj;
	else 
	   return "";
}
function getNode(obj) {
	var cwidth = getFieldValue(obj.leftpos);
	var cheight = getFieldValue(obj.toppos);
	var css = getFieldValue(obj.gender);
	var gender = getFieldValue(obj.gender);
	var cap = getFieldValue(obj.nodecaption);
	var nodename = "";
	if (trim(getFieldValue(obj.firstname)) != "")
		nodename = getFieldValue(obj.firstname);
	if (trim(getFieldValue(obj.surname)) != "")
		nodename = nodename + ' ' + getFieldValue(obj.surname);
	var title = trim(getFieldValue(obj.title));
	if(title != "" && trim(nodename) != "")
		nodename = nodename + "<br /><strong>" + title + "</strong>";
	else if(title != "")
	    nodename = title;
	if (trim(nodename) == "")
		nodename = getFieldValue(obj.nodecaption);
	else if (trim(getFieldValue(obj.photo)) != "")
		nodename = nodename + '<br /><img src="' + getFieldValue(obj.photo) + '" class="img-rounded" style="width:' + pWidth + 'px; height:' + pHeight + 'px;" alt="' + nodename + '"Photo" />';
	var Div = $('<div>', {
		id: getFieldValue(obj.elementid)
	}, {
		class: 'window'
	}).html(nodename).css({
		'min-height': conHeight,
		'width': conWidth,
		'background-color': getFieldValue(obj.bkcolor),
		'color': obj.ftcolor,
		'border': '1px solid ' + getFieldValue(obj.brcolor),
		top: cheight + 'px',
		left: cwidth + 'px',
		position: 'absolute'
	}).attr("data-id", getFieldValue(obj.elementid)).appendTo('#chart-demo');
	jsPlumb.draggable($(Div));
	$(Div).addClass('chart-demo window ' + css);
	$(Div).attr('data-chartid', getFieldValue(obj.chartid));
	$(Div).attr('data-name', nodename);
	$(Div).attr('data-fname', getFieldValue(obj.firstname));
	$(Div).attr('data-sname', getFieldValue(obj.surname));
	$(Div).attr('data-gender', gender);
	$(Div).attr('data-postop', cheight);
	$(Div).attr('data-posleft', cwidth);
	$(Div).attr('data-title', getFieldValue(obj.title));
	$(Div).attr('data-desc', getFieldValue(obj.description));
	$(Div).attr('data-bkcolor', getFieldValue(obj.bkcolor));
	$(Div).attr('data-ftcolor', getFieldValue(obj.ftcolor));
	$(Div).attr('data-brcolor', getFieldValue(obj.brcolor));

	if (trim(getFieldValue(obj.photo)) != "")
		$(Div).attr('data-photo', getFieldValue(obj.photo));
	
	if (trim(getFieldValue(obj.email)) != "")
		$(Div).attr('data-email', getFieldValue(obj.email));
	if (trim(getFieldValue(obj.website)) != "")
		$(Div).attr('data-website', getFieldValue(obj.website));
	if (getFieldValue(obj.hometel) != "")
		$(Div).attr('data-tel', getFieldValue(obj.hometel));
	if (trim(getFieldValue(obj.mobile)) != "")
		$(Div).attr('data-mobile', getFieldValue(obj.mobile));
	
	if (trim(getFieldValue(obj.profession)) != "")
		$(Div).attr('data-profession', getFieldValue(obj.profession));
	if (trim(getFieldValue(obj.company)) != "")
		$(Div).attr('data-company', getFieldValue(obj.company));
	if (trim(getFieldValue(obj.interests)) != "")
		$(Div).attr('data-interests', getFieldValue(obj.interests));
	if (trim(getFieldValue(obj.bionotes)) != "")
		$(Div).attr('data-bio', getFieldValue(obj.bionotes));
	// mark selected node to be shown in display view when clicked
	$(Div).attr('data-isupd', '1');
	// register element id for saving trees
	iDs.push({
		eid: getFieldValue(obj.elementid),
		position: cheight
	});
}

function prepareMsg(msg) {
	$('#' + msgLabel).html("<div class='alert alert-success alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + msg + "</div>");
}