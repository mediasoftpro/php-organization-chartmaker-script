;(function() {
	jsPlumb.ready(function() {			

		var color = "gray";

		var instance = jsPlumb.getInstance({
			// notice the 'curviness' argument to this Bezier curve.  the curves on this page are far smoother
			// than the curves on the first demo, which use the default curviness value.			
			//Connector : [ "Bezier", { curviness:50 } ],
			Connector : [ "Flowchart", {stub:1} ],
			DragOptions : { cursor: "pointer", zIndex:2000 },
			PaintStyle : { strokeStyle:color, lineWidth:1 },
			EndpointStyle : { radius:4, fillStyle:color },
			HoverPaintStyle : {strokeStyle:"#ec9f2e" },
			EndpointHoverStyle : {fillStyle:"#ec9f2e" },
			Container:"chart-demo"
		});
			
		// suspend drawing and initialise.
		instance.doWhileSuspended(function() {		
			// add endpoints, giving them a UUID.
			// you DO NOT NEED to use this method. You can use your library's selector method.
			// the jsPlumb demos use it so that the code can be shared between all three libraries.
			var windows = jsPlumb.getSelector(".chart-demo .window");
			for (var i = 0; i < windows.length; i++) {
				instance.addEndpoint(windows[i], {
					uuid:windows[i].getAttribute("id") + "-bottom",
					anchor:"Bottom",
					maxConnections:-1
				});
				instance.addEndpoint(windows[i], {
					uuid:windows[i].getAttribute("id") + "-top",
					anchor:"Top",
					maxConnections:-1
				});
				instance.addEndpoint(windows[i], {
					uuid:windows[i].getAttribute("id") + "-left",
					anchor:"Left",
					maxConnections:-1
				});
				instance.addEndpoint(windows[i], {
					uuid:windows[i].getAttribute("id") + "-right",
					anchor:"Right",
					maxConnections:-1
				});
			}
			
		    instance.connect({uuids:["chartWindow2-right", "chartWindow3-left" ]});
			instance.connect({uuids:["chartWindow2-bottom", "chartWindow1-top" ]});
			instance.connect({uuids:["chartWindow3-bottom", "chartWindow1-top" ]});
			instance.connect({uuids:["chartWindow2-bottom", "chartWindow4-top" ]});
			instance.connect({uuids:["chartWindow3-bottom", "chartWindow4-top" ]});
			instance.connect({uuids:["chartWindow2-bottom", "chartWindow5-top" ]});
			instance.connect({uuids:["chartWindow3-bottom", "chartWindow5-top" ]});
			instance.connect({uuids:["chartWindow2-bottom", "chartWindow6-top" ]});
			instance.connect({uuids:["chartWindow3-bottom", "chartWindow6-top" ]});
			instance.connect({uuids:["chartWindow2-bottom", "chartWindow7-top" ]});
			instance.connect({uuids:["chartWindow3-bottom", "chartWindow7-top" ]});
			instance.draggable(windows);		
		});
	});
	
})();