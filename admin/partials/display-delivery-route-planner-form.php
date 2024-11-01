<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	wp_enqueue_style( 'vsz_rutp_admin-css' );
	wp_enqueue_style( 'vsz_rutp_bootstrap-min-css' );
	wp_enqueue_style( 'vsz_rutp_routeplanner-css' );
	wp_enqueue_style( 'vsz_rutp_font-awesome-css' );
	// wp_enqueue_script('bootstrap-min-js');
	$site_key = get_option('rp_site_key');
	$rp_default_longitude = get_option('rp_default_longitude');
	$rp_default_latitude = get_option('rp_default_latitude');
	$rp_default_zoom = get_option('rp_default_zoom');
	$restrictScreen = false;	/////// This variable will decide whether display map and other functionality or not.
								/////// If true than map will not displayed, submit and print buttons will be disabled
								/////// If false than map will be displayed, submit and print buttons will be functional
?>
<link href="<?php echo plugin_dir_url(__FILE__); ?>/routeplanner.css" type="text/css" />
<script src="https://maps.google.com/maps/api/js?key=<?php echo $site_key; ?>&sensor=false"></script>
<div class="wrap">

<div class="header-main">
	<h3 class="h1-type">Route Planner</h3><?php
	if(empty($site_key) || empty($rp_default_longitude) || empty($rp_default_latitude) || empty($rp_default_zoom)){
		$restrictScreen = true;
		?><div><div class="display-shortcode">
		Map settings are not inserted properly. Please insert map settings first to make this page functional. You can insert map settings from <a href="<?php echo admin_url()."admin.php?page=rp_setting"; ?>">here</a>.
	</div></div><?php
	}
	else{
	?><!-- <div class="display-shortcode">
		Put following shortcode to dislpay route planner screen: [RP_DISPLAY]
	</div> --><?php
	}
?></div>
<div id="right-panel">
	<div class="row map-main">
		<div class="col-md-4">
			<div class="hd-typ2">Start Point<span class="text-danger">*</span></div>
			<div class="form-group">
				<input id="origin_address" name="origin_address" value="" class="form-control inpt-fid" placeholder="Address or zipcode">
			</div>
			<div class="hd-typ2">End Point<span class="text-danger">*</span></div>
			<div class="form-group">
				<input id="destination_address" name="destination_address" value="" class="form-control inpt-fid" placeholder="Address or zipcode">
			</div>
			<div class="hd-typ2">Delivery Location</div>
			<div class="waypointsDiv"><?php
				for($i=0;$i<5;$i++){?>
					<div class="form-group">
						<input name="deliveries_address[]" value="" class="form-control inpt-fid" placeholder="Address or zipcode">
					</div>
				<?php }	?>
			</div>
			<div class="form-group">
			<div class="text-right mrgB15"><input type="button" class="addNewDelivery btn btn-primary mrgB15 btn-block" value="Add Delivery Point" /></div>
			<div class="row">
				<div class="text-left col-md-6">
					<input type="button" id="submit" value="Submit" onclick="validateRequiredFields();" class="btn btn-success btn-block" <?php if($restrictScreen){ echo 'disabled="disabled"'; } ?>/>
				</div>
				<input type="hidden" id="countNumber" value="<?php echo $i; ?>" />
				<div class="text-right col-md-6 ">
					<div class="">
						<input type="button" id="submit" value="Print" onclick="printAnyMaps();" class="btn btn-info btn-block vsz_rp_print_map_button"  disabled="disabled"/>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="newPrintSection">
				<div class="printWholeSection">
					<div class="displayMap">
						<div class="row">
							<div class="hd-typ2 map-title col-sm-6">Google Map</div>
							<div class="display_note col-sm-6 pull-right text-right">Note:- This map will show driving routes only.</div>
						</div>
						<div id="map123" class="map-main-outer"></div>
					</div>
					<div class="display-list">
						<div id="directions-panel-listing"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="loader-route"></div>

</div>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>-->

<script>
	jQuery(document).ready(function(){
		
		// To disable the print button
		jQuery(".vsz_rp_print_map_button").prop("disabled",true);
		
		jQuery(".addNewDelivery").click(function(){
			var domCount = jQuery("#countNumber").val();
			jQuery(".waypointsDiv").append('<div class="form-group"><input name="deliveries_address[]" value="" class="form-control inpt-fid" placeholder="Address or zipcode"><a href="javascript:;" value="Remove" class="removeButton fa fa-remove" id="remove-'+domCount+'"></a></div>');
			domCount++;
			jQuery("#countNumber").val(domCount);
		});

		jQuery(".waypointsDiv").on("click",".removeButton",function(){
			if(confirm("Are you sure to delete this delivery point?")){
				jQuery(this).parent(".form-group").remove();
			}
		});
	});
</script><?php
if($restrictScreen === false){
?>
<script>
	google.maps.event.addDomListener(window, 'load', initMap);
	/********* equalHeight *******************/
	equalheight = function(container){
		var currentTallest = 0,
			currentRowStart = 0,
		rowDivs = new Array(),
					$el,
		topPosition = 0;
		jQuery(container).each(function() {
			$el = jQuery(this);
			jQuery($el).height('auto');
			topPostion = $el.position().top;
			if (currentRowStart != topPostion) {
				for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
					rowDivs[currentDiv].height(currentTallest);
				}
				rowDivs.length = 0; // empty the array
				currentRowStart = topPostion;
				currentTallest = $el.height();
				rowDivs.push($el);
			} else {
				rowDivs.push($el);
				currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
			}
			for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
				rowDivs[currentDiv].height(currentTallest);
			}
		});
	}

	var map, infoWindow;
	var z = 0;
	var usedPoints = [];
	var gmarkers = [];
	var map = null;
	var startLocation = null;
	var endLocation = null;
	var directionsService = null;
	var waypts = null;
	var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var labelIndex = 0;
	function initMap() {
		var lati = <?php echo $rp_default_latitude; ?>;
		var longi = <?php echo $rp_default_longitude; ?>;
		var defZoom = <?php echo $rp_default_zoom; ?>;

		map = new google.maps.Map(document.getElementById('map123'), {
			center: {lat: lati, lng: longi},
			zoom: defZoom,
		});
	}

	// to validate the required fields for map
	function validateRequiredFields(){
		var origin_address = jQuery("#origin_address").val();
		var destination_address = jQuery("#destination_address").val();

		if(origin_address == ""){
			alert("Please insert Start point address or zipcode.");
			jQuery(".vsz_rp_print_map_button").prop("disabled",true);
			return false;
		}
		if(destination_address == ""){
			alert("Please insert End point address or zipcode.");
			jQuery(".vsz_rp_print_map_button").prop("disabled",true);
			return false;
		}

		// start loader
		jQuery(".loader-route").show();
		// display directions-panel
		//initMapRoute();
		initialize();
	}
	
	function initialize() {
		var lati = <?php echo $rp_default_latitude; ?>;
		var longi = <?php echo $rp_default_longitude; ?>;
		var defZoom = <?php echo $rp_default_zoom; ?>;

		directionsDisplay = new google.maps.DirectionsRenderer();
		var chicago = new google.maps.LatLng(lati, longi);
		// var latlng = new google.maps.LatLng(39.305, -76.617);
		var myOptions = {
		  zoom: defZoom,
		  mapTypeId: google.maps.MapTypeId.ROADMAP,
		  center: chicago
		}
		map = new google.maps.Map(document.getElementById("map123"), myOptions);
		google.maps.event.addListener(map, 'click', function() {
			infowindow.close();
		});
		directionsDisplay.setMap(map);
		calcRoute();
		
		// end loader
		var delay = 5000;
		setTimeout(function() {
			jQuery(".loader-route").hide();
		}, delay);
	}
 
	function calcRoute() {
		directionsService = new google.maps.DirectionsService(); 
		var waypts = [];
		var deliveryAddressArray = document.getElementsByName('deliveries_address[]');
		for (var i = 0; i < deliveryAddressArray.length; i++) {
			// displaying values in console
			// console.log(deliveryAddressArray[i].value);

			var deliveryArray = deliveryAddressArray[i].value.trim();
			if (deliveryArray.length > 0) {
				waypts.push({
				location: deliveryArray,
				stopover: true
				});
			}
		}
		var request = {
			origin: document.getElementById('origin_address').value,
			destination: document.getElementById('destination_address').value,
			waypoints: waypts,
			optimizeWaypoints: true,
			travelMode: google.maps.DirectionsTravelMode.WALKING
		};
		directionsService.route(request, RenderCustomDirections);
	}
	function RenderCustomDirections(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			labelIndex = 0;
			waypts = [];
			var bounds = new google.maps.LatLngBounds();
			var route = response.routes[0];
			var summaryPanel = document.getElementById("directions-panel-listing");
			startLocation = new Object();
			endLocation = new Object();
			
			var polyline = new google.maps.Polyline({
				path: [],
				strokeColor: '#00b3fd',
				strokeWeight: 5
			});
			polyline.setMap(null);
			
			// Clear the saved addresses
			usedPoints = [];
			
			summaryPanel.innerHTML = "";

			// For each route, display summary information.
			var innerHtml = '<div class="list-section padding-new">'+
								'<table class="mrgB15" width="100%">'+
									'<tr>'+
										'<td class="hd-typ2 map-title" width="50%">Delivery Directives</td>'+
										'<td id="route-total" width="50%" class="text-right" align="right"></td>'+
									'</tr>'+
								'</table>';
			innerHtml += '<table id="directions-panel" width="100%"><tr>';
			var totaldistance = 0;
			// Route Segment HTML starts //
			for (var i = 0; i < route.legs.length; i++) {
				var routeSegment = i;
				var routeSegment = (i >= 26 ? idOf((i / 26 >> 0) - 1) : '') +  'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i % 26 >> 0];
				j = i+1;
				var nextrouteSegment = (j >= 26 ? idOf((j / 26 >> 0) - 1) : '') +  'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[j % 26 >> 0];
				var roundedDistance = (Math.round(route.legs[i].distance.value/100))/10;
				
				innerHtml += '<tr><td width="100%"><span class="box-typ1"><p class="title"><b>Route Segment: ' + routeSegment + ' to ' + nextrouteSegment +
				 '</b></p><p class="address">' + route.legs[i].start_address + ' <b>to</b> ' + route.legs[i].end_address + '</p><p class="distance">' + roundedDistance + ' KM </p></span></td></tr>';
				 totaldistance = totaldistance + roundedDistance;
			}
			
			innerHtml += '</tr></table></div>';
			summaryPanel.innerHTML += innerHtml;
			// To display total distance in 1 point fraction
			totaldistance = (Math.round(totaldistance*10))/10;
			jQuery('#route-total').html("<b>Total Distance</b>: " +totaldistance + " KM");
			// Route Segment HTML ends //
		
			var path = response.routes[0].overview_path;
			var legs = response.routes[0].legs;
			// alert("processing "+legs.length+" legs");
			for (i=0;i<legs.length;i++) {
				if (i == 0) { 
					startLocation.latlng = legs[i].start_location;
					startLocation.address = legs[i].start_address;
					startLocation.marker = createMarker(legs[i].start_location,labels[labelIndex++ % labels.length],legs[i].start_address,"green",i);
				} else { 
					waypts[i] = new Object();
					waypts[i].latlng = legs[i].start_location;
					waypts[i].address = legs[i].start_address;
					waypts[i].marker = createMarker(legs[i].start_location,labels[labelIndex++ % labels.length],legs[i].start_address,"blue",i);
				}
				endLocation.latlng = legs[i].end_location;
				endLocation.address = legs[i].end_address;
				var steps = legs[i].steps;
				// alert("processing "+steps.length+" steps");
				for (j=0;j<steps.length;j++) {
					var nextSegment = steps[j].path;
					for (k=0;k<nextSegment.length;k++) {
						polyline.getPath().push(nextSegment[k]);
						bounds.extend(nextSegment[k]);
					}
				}
			}
			
			polyline.setMap(map);
			map.fitBounds(bounds);
			endLocation.marker = createMarker(endLocation.latlng,labels[labelIndex++ % labels.length],endLocation.address,"red");
			jQuery(".vsz_rp_print_map_button").prop("disabled",false);
			
			// Clear the saved addresses
			usedPoints.length = 0;
			usedPoints = usedPoints.filter( function( el ) {
			  return toRemove.indexOf( el ) < 0;
			} );
		}
		else{
			alert("No routes found for given addresses. Please try again with correct addresses.");
			jQuery("#directions-panel-listing").html('');
			jQuery(".vsz_rp_print_map_button").prop("disabled",true);
			jQuery(".loader-route").hide();
		}
		
		equalheight("#directions-panel tr .box-typ1");
    }
	
	// Callback function for getting marker image
	function getMarkerImage(iconColor) {
		return "<?php echo dirname(plugin_dir_url(__FILE__)); ?>/images/marker_"+iconColor+".png";
	}
	
	// Marker sizes are expressed as a Size of X,Y
	// where the origin of the image (0,0) is located
	// in the top left of the image.

	// Origins, anchor positions and coordinates of the marker
	// increase in the X direction to the right and in
	// the Y direction down.

	var iconImage = new google.maps.MarkerImage('mapIcons/marker_red.png',
		// This marker is 20 pixels wide by 34 pixels tall.
		new google.maps.Size(20, 34),
		// The origin for this image is 0,0.
		new google.maps.Point(0,0),
		// The anchor for this image is at 9,34.
		new google.maps.Point(9, 34));
	var iconShadow = new google.maps.MarkerImage('http://www.google.com/mapfiles/shadow50.png',
		// The shadow image is larger in the horizontal dimension
		// while the position and offset are the same as for the main image.
		new google.maps.Size(37, 34),
		new google.maps.Point(0,0),
		new google.maps.Point(9, 34));
	// Shapes define the clickable region of the icon.
	// The type defines an HTML &lt;area&gt; element 'poly' which
	// traces out a polygon as a series of X,Y points. The final
	// coordinate closes the poly by connecting to the first
	// coordinate.
	var iconShape = {
		coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
		type: 'poly'
	};
	var infowindow = new google.maps.InfoWindow({ 
		size: new google.maps.Size(150,50)
	});
    
	// Callback function to create marker
	function createMarker(latlng, text, html, color) {
		var markerLat = latlng.lat();
		var markerLng = latlng.lng();
		///// Adding way points to an array to avoid same lat long not override the title
		var count=usedPoints.length;
		var skipTitle = false;
		var m,p, current;
		for(m = 0; m < count; ++m){
			current = usedPoints[m];
			
			// Checking for same address marker
			if(current != undefined && current[0] == markerLat && current[1] == markerLng){
				skipTitle = true;
			}
		}
		
		var contentString = '<b>'+text+'</b><br>'+html;
		
		if(skipTitle === false){
			var marker = new google.maps.Marker({
				position: latlng,
				draggable: false, 
				map: map,
				shadow: iconShadow,
				icon: getMarkerImage(color),
				shape: iconShape,
				label: text,
				title: text,
				zIndex: Math.round(latlng.lat()*-100000)<<5,
			});
			usedPoints[z] = [markerLat,markerLng];
			z++;
		}
		/////// This color must be the color of end point marker
		else if(color == "red"){
			/////////////////////////////////
			///// Case for end point  ///////
			/////////////////////////////////
			/*
			***** For end point all other same location markers must be removed and end point marker must be displayed.
			*/
			// Checking all markers if any have same address as end point
			for(var x=0;x<gmarkers.length;x++){
				var currentMarker = gmarkers[x];
				if(currentMarker.position != undefined && currentMarker.position.lat() == markerLat && currentMarker.position.lng() == markerLng){
					// Found a marker with same address so delete this marker
					var delMarker = currentMarker;
					delMarker.setMap(null);
				}
			}
			// adding end point marker
			var marker = new google.maps.Marker({
				position: latlng,
				draggable: false, 
				map: map,
				shadow: iconShadow,
				icon: getMarkerImage(color),
				shape: iconShape,
				label: text,
				title: text,
				zIndex: Math.round(latlng.lat()*-100000)<<5,
			});
		}
		else{
			return;
		}
		marker.myname = text;
		gmarkers.push(marker);
		
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent(contentString); 
			infowindow.open(map,marker);
		});
		return marker;
	}
	function myclick(i) {
		google.maps.event.trigger(gmarkers[i],"click");		// Display marker info
	}
	
	//////// Print functionality starts  ////////
	function printAnyMaps() {
		var $body = jQuery('body');
		var $mapContainer = jQuery('.printWholeSection');
		var $mapContainerParent = $mapContainer.parent();
		var $printContainer = jQuery('<div style="position:relative;">');

		$printContainer
		.height($mapContainer.height())
		.append($mapContainer)
		.prependTo($body);

		var $content = $body
		.children()
		.not($printContainer)
		.not('script')
		.not('style')
		.detach();

		/**
		* Needed for those who use Bootstrap 3.x, because some of
		* its `@media print` styles ain't play nicely when printing.
		*/
		var $patchedStyle = jQuery('<style media="print">')
		.text(
		  'img { max-width: none !important; }' +
		  'a[href]:after { content: ""; }'
		)
		.appendTo('head');

		window.print();

		$body.prepend($content);
		$mapContainerParent.prepend($mapContainer);

		jQuery(".newPrintSection").append($printContainer);
		$printContainer.remove();
		$patchedStyle.remove();
	}
	//////// Print functionality ends  ////////
</script>
<?php } ?>