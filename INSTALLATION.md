## Installation

Brief installation & usage step for chart maker script as follows

1.  Execute` /database/jchartdb.sql` script on your database to generate required database tables for storing chart data.
1. Open `/include/db.php` to setup database connection.
2. Open` /include/config.php` to configuration application.
3.  Chart maker script render charts in real time using jquery, its setup is very simple, already have examples for making editable, readonly charts in` /project/applications/` directory.

e.g default usage or chart application within your application.

```javascript
  var dn = 'http://localhost/chart/'; // setup api path
  var hd = 'tree/handler.php'; // api end point for processing chart data in backend
  var sconnects = 'tree/savecon.php'; // api end point for saving chart connections
  var lhandler = 'tree/load.php'; // api end point for loading chart data from database
  var chandler = 'tree/lconnects.php'; // api end point for loading chart connection data from database
  var strokeColor = '#005b0f'; 
  var hoverPaintStyle = '#ff0000';
  var strokeLineWidth = 1;
  var hoverstrokeLineWidth = 2;
  var connectStyle = 'Flowchart'; // Bezier, StateMachine, Flowchart,
  var offsetdiff = 20; // horizontal space between two nodes
  var defaultUName = 'Default Node'; // setup default name for first node when appear
  var defaultFName = '';
  var defaultSName = '';
  var redirectPageName = 'created.html'; // redirect page after creation tree.
  var chartID = '0'; // enter chart id (saved in database) for loading, 0 : mean create new chart
  var msgLabel = 'treemsg';
  var cornerRadius = '10';
  var overlaySettings = [ "Arrow", { location:0.1, id:"charLabel", direction: -1} ];
  // default node color settings
  var bkColor = "#666";
  var ftColor = "#fff";
  var brColor = "#000";
  var titleElementID = "txt_chartname";
  var smoothScroll = false;
  var readOnly = false;
  // plupload settings
  var plUploadHandler = "tree/modules/uploadhandler.php"; // api end point for upload photo
  var plupload_flash_url = "assets/plugins/plupload/js/plupload.flash.swf";
  var plupload_silverlight_url = "assets/plugins/plupload/js/plupload.silverlight.xap";
  var maxFileSize = "10mb";
  // default image url
  var defaultThumbUrl = "../assets/images/holder.png" // default thumbnail path if no thumbnail available
```

