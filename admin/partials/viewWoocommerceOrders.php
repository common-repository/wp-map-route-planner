<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	// including js and css
	wp_enqueue_style( 'vsz_rutp_admin-css' );
	wp_enqueue_style( 'vsz_rutp_bootstrap-min-css' );
	wp_enqueue_script('vsz_rutp_bootstrap-min-js');
	wp_enqueue_script( 'vsz_rutp_bootstrap-datepicker-min-js' );
	wp_enqueue_style( 'vsz_rutp_bootstrap-datepicker-min-css' );
	// wp_enqueue_style( 'routeplanner-css' );
	wp_enqueue_style( 'vsz_rutp_magnific-popup-css' );
	wp_enqueue_script('vsz_rutp_jquery-magnific-popup-js');
	wp_enqueue_style( 'vsz_rutp_font-awesome-css' );
	
	$fromDate = '';
	$toDate = '';
	$site_key = get_option('rp_site_key');
	$rp_default_longitude = get_option('rp_default_longitude');
	$rp_default_latitude = get_option('rp_default_latitude');
	$rp_default_zoom = get_option('rp_default_zoom');
	$restrictScreen = false;	/////// This variable will decide whether display map and other functionality or not.
								/////// If true than map will not displayed, submit and print buttons will be disabled
								/////// If false than map will be displayed, submit and print buttons will be functional
?><style>

</style>
<div class="wrap order-screen">
	<div class="header-main"><?php
	if(empty($site_key) || empty($rp_default_longitude) || empty($rp_default_latitude) || empty($rp_default_zoom)){
		$restrictScreen = true;
		?><div><div class="display-shortcode">
		Map settings are not inserted properly. Please insert map settings first to make this page functional. You can insert map settings from <a href="<?php echo admin_url()."admin.php?page=rp_setting"; ?>">here</a>.
	</div></div><?php
	}
	?><hr class="wp-header-end">
	<h3 class="h1-type">Orders Delivery Routes</h3>
	</div>
	<!--  Main display starts here   -->
	<div id="display_message"></div>
	<form method="GET" id="custom_filter_form">
		<!--  For initial parameters  -->
		<input type="hidden" id="countNumber" value="0" />
		<input type="hidden" name="page" value="rp_woo_orders" />
		<input type="hidden" name="pg" value="1" />
		<input type="hidden" name="orderby" value="<?php echo $orderby; ?>" />
		<input type="hidden" name="order" value="<?php echo $order; ?>" />
		<input type="hidden" name="vsz_from_date_for_view" value="<?php echo $fromDate;?>">
		<input type="hidden" name="vsz_to_date_for_view" value="<?php echo $toDate;?>">
		<input type="hidden" name="woo_view_orders_submit" value="">
		<div class="delivery-location-main">
			<div id="right-panel">
				<div class="row map-main">
					<div class="col-sm-8">
						<div class="wp-heading-inline hd-typ12">Search Orders</div>
						<div class="search_orders">
							<div class="row">
								<div class="col-sm-12">
									<!-- Structure for from and to date -->
									<div class="datepickerContainer">
										<div class="for_date_search  dateClass" id="datepicker" >
											<div class="input-daterange">
												<div class="row">
													<div class="col-sm-12"><div class="hd-typ13">Date </div></div>
													<div class="col-sm-6">
														<div class="form-group">
															<div class="input-group">
																<span class="input-group-addon">From</span>
																<input type="text" class="input-sm form-control inpt-fid" autocomplete="off" name="from_date" id="from_date_id" value="<?php echo $fromDate; ?>" />
																<span class="fa fa-calendar datecalendar block"></span>
															</div>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<div class="input-group">
																<span class="input-group-addon">To</span>
																<input type="text" class="input-sm form-control inpt-fid" autocomplete="off" name="to_date" id="to_date_id" value="<?php echo $toDate; ?>" />
																<span class="fa fa-calendar datecalendar"></span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- Structure for from and to date -->
									<div>
										<div>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<div class="for_id_search">
															<div class="hd-typ13"> Id / Firstname / Surname / Email </div>
															<input type="text" name="vsz_rp_search_by_id" class="vsz_rp_search_by_id form-control inpt-fid" placeholder="Search by ID / Firstname / Surname / Email" />
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group">
														<div class="for_address_search">
															<div class="hd-typ13"> Address / City / Postal Code </div>
															<input type="text" name="vsz_rp_search_by_address" class="vsz_rp_search_by_address form-control inpt-fid" placeholder="Search by Address / City / Postal Code" />
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group"><?php
														$countries_obj   = new WC_Countries();
														$countries   = $countries_obj->countries;
														?><div class="for_country_search"><?php
															if(!empty($countries)){
																?><div class="hd-typ13"> Country </div>
																<select name="vsz_rp_search_by_country" id="vsz_rp_search_by_country" class="form-control inpt-fid">
																	<option value=""> -- All -- </option><?php
																foreach($countries as $code=>$country){
																	?><option value="<?php echo $code; ?>"><?php echo $country; ?></option><?php
																}
																?></select><?php
															}
														?></div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group">
														<div class="for_state_search">
															<div class="hd-typ13"> Region / State </div>
															<select name="vsz_rp_search_by_state" id="vsz_rp_search_by_state" class="form-control inpt-fid">
																<option value=""> -- All -- </option>
															</select>
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group">
														<div class="for_user_search">
															<div class="hd-typ13"> Status </div><?php
															$order_statuses = wc_get_order_statuses();
															if(!empty($order_statuses) && count($order_statuses)>0){
																?><select name="vsz_rp_search_by_status" id="vsz_rp_search_by_status" class="form-control inpt-fid">
																	<option value=""> -- All -- </option><?php
																foreach($order_statuses as $key=>$val){
																	?><option value="<?php echo $key; ?>"> <?php echo $val; ?> </option><?php
																}
																?></select><?php
															}
														?></div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group">
														<div class="for_payment_search">
															<div class="hd-typ13"> Payment Methods </div>
															<select name="vsz_rp_search_by_payment" id="vsz_rp_search_by_payment" class="form-control inpt-fid">
																<option value=""> -- All -- </option><?php
																$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
																if(!empty($available_gateways)){
																	foreach($available_gateways as $gateway){
																		?><option value="<?php echo $gateway->title; ?>"><?php echo $gateway->title; ?></option><?php
																	}
																}
															?></select>
														</div>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<input type="button" name="vsz_rp_search_call" value="Search" title="Search" class="btn btn-success btn-block vsz_rp_search_call"  <?php if($restrictScreen){ echo 'disabled="disabled"'; } ?>/>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<input type="button" name="vsz_rp_reset_call" value="Reset" title="Reset" class="btn btn-warning btn-block vsz_rp_reset_call"  <?php if($restrictScreen){ echo 'disabled="disabled"'; } ?>/>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="left-side">
							<div class="form-group">
								<div class="hd-typ2">Starting Point<span class="text-danger">*</span></div>
								<input type="text" name="starting_point" class="form-control inpt-fid" id="starting_point" value="<?php echo isset($starting_point) ? $starting_point : '' ; ?>" placeholder="Address or zipcode" />
							</div>
							<div class="form-group">
								<div class="hd-typ2">Ending Point<span class="text-danger">*</span></div>
								<input type="text" name="ending_point" class="form-control inpt-fid" id="ending_point" value="<?php echo isset($ending_point) ? $ending_point : '' ; ?>" placeholder="Address or zipcode" />
							</div>
							<div class="form-group">
								<div class="hd-typ2">Delivery Locations</div>
								<div class="append_dom_here">

								</div>
							</div>
							<div id="animate_here"></div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<a href="#displayMapPopup" onclick="return validateRequiredFields();" class="btn btn-success btn-block pop-up-btn hidden submit-btn" <?php if($restrictScreen){ echo 'disabled="disabled"'; } ?>>Submit</a>
										<input type="button" onclick="return validateRequiredFields(this.className);" class="btn btn-success btn-block submit-btn" value="Submit" <?php if($restrictScreen){ echo 'disabled="disabled"'; } ?>/>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="searched_orders">
							<div class="order_append_here"><?php
							global $wpdb;
							$qry_order = "SELECT * FROM ".$wpdb->posts." WHERE post_type = 'shop_order'	ORDER BY ID DESC";
							$order_results = $wpdb->get_results($qry_order);
								?><div class="orders-listing" style="padding-top:0px;">
									<div class="tablenav top">
										<div class="row">
											<div class="col-sm-6">
												<div class="searched_orders">
													<div class="wp-heading-inline hd-typ12">Searched Orders</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="tablenav-pages">
													<span class="displaying-num" style="color: #000000;"><?php
														if(count($order_results)>0){
														echo count($order_results); ?> order(s) displayed<?php
														}
														else{
															echo "No order display";
														}
													?></span>
												</div>
											</div>
										</div>
									</div>
									<section>
										<div class="outerTable table-typ1 pane-hScroll">
											<table class="wp-list-table table-fixed widefat fixed striped pages orders-table-listing outer" style="border:1px solid #ccc;">
												<thead>
													<tr>
														<th class="manage-column column-cb"  style="vertical-align: middle;"><input type="checkbox" class="headTypeCheck" / style="margin-left: 0;"></th>
														<th class="manage-column column-title column-primary">ORDER ID</th>
														<th class="manage-column column-order_status column-primary">STATUS</th>
														<th class="manage-column name-email column-primary">NAME & EMAIL</th>
														<th class="manage-column address column-user_address column-primary">ADDRESS</th>
														<th class="manage-column column-user_region column-primary">REGION / STATE</th>
														<th class="manage-column column-user_postcode column-primary">POSTAL CODE</th>
														<th class="manage-column column-user_country column-primary">COUNTRY</th>
														<th class="manage-column column-order_duedate column-primary">ORDER DATE</th>
														<th class="manage-column column-order_payment_method column-primary">PAYMENT METHOD</th>
														<th class="manage-column column-order_total column-primary">TOTAL</th>
													</tr>
												</thead>
                                            </table>
                                            <div class="pane-vScroll">
                                                <table class="wp-list-table table-fixed widefat fixed striped pages orders-table-listing inner">
													<tbody><?php
											//////// Displaying kinitial load time orders
											if(!empty($order_results)){
												$count = 0;
												foreach ($order_results as $results){
													$ContactName = '';
													$ContactSurname = '';
													$EmailAddress = '';
													$POAddressline1 = '';
													$POAddressline2 = '';
													$POCity = '';
													$PORegion = '';
													$POPostalCode = '';
													$POCountry = '';
													$InvoiceDate = '';
													$DueDate = '';
													$Status = '';
													$Total = '';

													$order_wo = new WC_Order( $results->ID );
													$ContactName = $order_wo->billing_first_name;
													$ContactSurname = $order_wo->billing_last_name;
													$Currency = $order_wo->get_order_currency();
													$payment_method = $order_wo->payment_method_title;
													$orderKey = get_post_meta($order_wo->id,"_order_key",true);		//// getting order key
													$orderid = wc_get_order_id_by_order_key($orderKey); 			//// getting order id using order key
													$EmailAddress = $order_wo->billing_email;
													$POAddressline1 = $order_wo->shipping_address_1;
													$POAddressline2 = $order_wo->shipping_address_2;
													$POCity = $order_wo->shipping_city;
													$region = $order_wo->shipping_state;
													$POPostalCode = $order_wo->shipping_postcode;
													$POCountry = $order_wo->shipping_country;
													$OrderCustomerName = $order_wo->shipping_first_name." ".$order_wo->shipping_last_name;
													$stateArray = WC()->countries->states;
													$countryArray = WC()->countries->countries;
													
													// getting region
													if(isset($stateArray[$POCountry][$region])){
														$region = $stateArray[$POCountry][$region];
													}
													else{
														$region = $region;
													}
													
													// Getting country
													if(isset($countryArray[$POCountry])){
														$country = $countryArray[$POCountry];
													}
													else{
														$country = $region;
													}
													$InvoiceDate = new DateTime($order_wo->order_date);
													$InvoiceDate = $InvoiceDate->format('d/m/Y');
													$DueDate = $InvoiceDate;

													// for total
													$Total = $order_wo->get_total();
													$Total .= " ".$Currency;
													$Status .= $results->post_status;
													if($Status == "wc-completed"){
														$Status = '<span class="label label-success">Completed</span>';
													}
													else if($Status == "wc-on-hold"){
														$Status = '<span class="label label-info">Hold</span>';
													}
													else if($Status == "wc-cancelled"){
														$Status = '<span class="label label-danger">Cancelled</span>';
													}
													else if($Status == "wc-failed"){
														$Status = '<span class="label label-danger">Failed</span>';
													}
													else if($Status == "wc-processing"){
														$Status = '<span class="label label-primary">Processing</span>';
													}
													else if($Status == "wc-refunded"){
														$Status = '<span class="label label-info">Refunded</span>';
													}
													else if($Status == "wc-pending"){
														$Status = '<span class="label label-primary">Pending Payment</span>';
													}
													//// If not status match than it is a custom status. So displaying a common label for that
													else{
														$order_statuses = wc_get_order_statuses();
														if(!empty($order_statuses) && count($order_statuses)>0){
															foreach($order_statuses as $key=>$val){
																if($key == $Status){
																	$Status = $val;
																	break;
																}
															}
														}
														$Status = '<span class="label label-default">'.$Status.'</span>';
													}
													?><tr><?php
														?><td><?php
															?><input type="checkbox" class="singleCheck" name="getRoute[]" value="<?php echo $order_wo->id; ?>" /><?php
														?></td><?php
														?><td class="column-title"><?php
															echo $orderid;
														?></td><?php
														?><td class="column-order_status"><?php
															echo $Status;
														?></td><?php
														?><td class="name-email"><?php
															echo "First Name :- ".ucfirst($ContactName)."<br>Last Name:-".ucfirst($ContactSurname)."<br>".$EmailAddress;
														?></td><?php
														?><td class="address addressClass_<?php echo $orderid; ?>"><?php
															echo $POAddressline1." <br>".$POAddressline2." <br>".$POCity;
														?></td><?php
														?><td  class="addressClass_<?php echo $orderid; ?>"><?php
															echo $region;
														?></td><?php
														?><td  class="addressClass_<?php echo $orderid; ?>"><?php
															echo $POPostalCode;
														?></td><?php
														?><td  class="addressClass_<?php echo $orderid; ?> country"><?php
															echo $country;
														?></td><?php
														?><td><?php
															echo $DueDate;
														?></td><?php
														?><td><?php
															echo $payment_method;
														?></td><?php
														?><td><?php
															echo $Total;
														?></td></tr><?php
												}
											}
											else{
												?><tr class="no-items">
													<td class="colspanchange" colspan="2">No order found.</td>
												</tr><?php
											}
											?></tbody>
											</table>
                                            </div>
										</div>
									</section>
									<p style="margin-top: 20px; margin-bottom: 30px;">
										<input type="button" class="btn btn-success add_to_map_route" value="Add Order" />
										<input type="button" id="vsz_rp_uncheck_orders" class="btn btn-warning uncheck_orders" value="Un Check All" disabled />
									</p>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div id="displayMapPopup" class="pop-up-btn-mian mfp-hide">
	<div class="pop-inner">
		<div class="pop-up-header">Orders Delivery Routes</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="newPrintSection">
					<div class="printWholeSection">
						<div class="display_note col-sm-12">Note:- This map will show driving routes only.</div>
						<div class="row">
							<div class="col-sm-8">
								<div class="displayMap map-main-outer" id="vsz_rp_map_display">

								</div>
							</div>
							<div class="col-sm-4">
								<div class="map-left-section">
									<div class="display-list">
										<div id="directions-panel-listing"></div>
									</div>
								</div>
								<input type="button" id="printButton" value="Print" onclick="printAnyMaps();" class="btn btn-info btn-block" <?php if($restrictScreen){ echo 'disabled="disabled"'; } ?>/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="display_loader" class="loader-route"></div>
<script>
	jQuery(document).ready(function(){
	
		jQuery('.pop-up-btn').magnificPopup({
			type: 'inline',
			fixedContentPos: false,
			fixedBgPos: true,
			closeBtnInside: true,
			preloader: false,
			midClick: true,
			removalDelay: 300,
			closeOnBgClick: true,
			close: function(){
				jQuery("#vsz_rp_map_display").html('');
				jQuery("#directions-panel-listing").html('');
			}
		});

		///// For search calendar
		jQuery("#datepicker .input-daterange").datepicker({
			format : "dd/mm/yyyy",
			maxDate: "0",
			autoclose: true,
			todayHighlight: true,
			endDate: "today",
			container:'.datepickerContainer'
		});

		//////// For changing state values as per country
		jQuery("#vsz_rp_search_by_country").change(function(){
			var country = jQuery(this).val();
			if(country == ""){
				jQuery("#vsz_rp_search_by_state").html('<option value=""> -- All -- </option>');
			}
			else{
				///// Calling ajax
				jQuery(".loader-route").show();
				var ajax_states_nonce = '<?php echo wp_create_nonce( "vsz_rp_get_states_nonce" ); ?>';
				var data = {
					'action'	: 'vsz_rp_get_states',
					'country' :encodeURIComponent(country),
					'ajax_nonce':encodeURIComponent(ajax_states_nonce)
				};
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					data: data,
					success: function(data){
						jQuery(".loader-route").hide();
						jQuery("#vsz_rp_search_by_state").html(data);
					},
					error: function(data){
						jQuery(".loader-route").hide();
					}
				});
			}
		});

		////// For search
		jQuery(".vsz_rp_search_call").click(function(){
			var fromDate = jQuery("#from_date_id").val();
			var toDate = jQuery("#to_date_id").val();
			var searchById = jQuery(".vsz_rp_search_by_id").val().trim();
			var searchByAddress = jQuery(".vsz_rp_search_by_address").val().trim();
			var searchByCountry = jQuery("#vsz_rp_search_by_country").val().trim();
			var searchByState = jQuery("#vsz_rp_search_by_state").val().trim();
			var searchByStatus = jQuery("#vsz_rp_search_by_status").val().trim();
			var searchByPayment = jQuery("#vsz_rp_search_by_payment").val().trim();

			/// Checking for parameters
			if(fromDate == "" && toDate == "" && searchById == "" && searchByStatus == "" && searchByAddress == "" && searchByCountry == "" && searchByState == "" && searchByPayment == "" ){
				alert("Please insert atleast one value to search");
				return false;
			}
			else{
				if( (fromDate == "" && toDate != "") || (fromDate != "" && toDate == "") ){
					alert("Please insert from and to date properly");
				}
				else{
					///// Calling ajax
					jQuery(".loader-route").show();
					var ajax_nonce = '<?php echo wp_create_nonce( "vsz_rp_get_orders_nonce" ); ?>';
					var data = {
						'action'	: 'vsz_rp_get_orders',
						'fromdate' :encodeURIComponent(fromDate),
						'todate'	:encodeURIComponent(toDate),
						'searchbyid'	:encodeURIComponent(searchById),
						'searchbyaddress'	:encodeURIComponent(searchByAddress),
						'searchbycountry'	:encodeURIComponent(searchByCountry),
						'searchbystate'	:encodeURIComponent(searchByState),
						'searchbystatus'	:encodeURIComponent(searchByStatus),
						'searchbypayment'	:encodeURIComponent(searchByPayment),
						'ajax_nonce':encodeURIComponent(ajax_nonce)
					};
					jQuery.ajax({
						url: ajaxurl,
						type: 'POST',
						data: data,
						success: function(data){
							jQuery(".order_append_here").html(data);
							jQuery(".loader-route").hide();
							jQuery('html, body').animate({ scrollTop: jQuery(".order_append_here").offset().top-50 }, 1000);
						},
						error: function(data){
							jQuery(".loader-route").hide();
						}
					});
				}
			}
		});
		
		////// Reset functionality
		jQuery(".vsz_rp_reset_call").click(function(){
			var fromDate = jQuery("#from_date_id").val('');
			var toDate = jQuery("#to_date_id").val('');
			var searchById = jQuery(".vsz_rp_search_by_id").val('');
			var searchByAddress = jQuery(".vsz_rp_search_by_address").val('');
			var searchByCountry = jQuery("#vsz_rp_search_by_country").val('');
			var searchByState = jQuery("#vsz_rp_search_by_state").val('');
			var searchByStatus = jQuery("#vsz_rp_search_by_status").val('');
			var searchByPayment = jQuery("#vsz_rp_search_by_payment").val('');
			
			///// Calling ajax to reset order list
			jQuery(".loader-route").show();
			var ajax_nonce = '<?php echo wp_create_nonce( "vsz_rp_get_all_orders_nonce" ); ?>';
			var data = {
				'action'	: 'vsz_rp_get_all_orders',
				'ajax_nonce':encodeURIComponent(ajax_nonce)
			};
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function(data){
					jQuery(".order_append_here").html(data);
					jQuery(".loader-route").hide();
					// jQuery('html, body').animate({ scrollTop: jQuery(".order_append_here").offset().top-50 }, 1000);
				},
				error: function(data){
					jQuery(".loader-route").hide();
				}
			});
		});
		
		////// Giving checkbox functionality
		jQuery(".order_append_here").on("click",".singleCheck",function(){

			var totalCheckboxes = jQuery(".order_append_here").find(".singleCheck").length;
			var totalChecked = jQuery(".order_append_here").find(".singleCheck:checked").length;

			if(totalChecked>0){
				jQuery("#vsz_rp_uncheck_orders").prop("disabled",false);
			}
			else{
				jQuery("#vsz_rp_uncheck_orders").prop("disabled",true);
			}

			if(totalChecked == totalCheckboxes){
				jQuery(".headTypeCheck").prop("checked",true);
			}
			else{
				jQuery(".headTypeCheck").prop("checked",false);
			}
		});

		jQuery(".order_append_here").on("click",".headTypeCheck",function(){
			if(jQuery(this).prop("checked")){
				jQuery(".headTypeCheck").prop("checked",true);
			}
			else{
				jQuery(".headTypeCheck").prop("checked",false);
			}

			jQuery(".headTypeCheck").each(function(){
				if(jQuery(this).prop("checked")){
					jQuery(".outerTable tbody").find(".singleCheck").each(function(){
						jQuery(this).prop("checked",true);
					});
					jQuery("#vsz_rp_uncheck_orders").prop("disabled",false);
				}
				else{
					jQuery(".outerTable tbody").find(".singleCheck").each(function(){
						jQuery(this).prop("checked",false);
					});
					jQuery("#vsz_rp_uncheck_orders").prop("disabled",true);
				}

			});
		});

		//////////////////////////////////// ADDING ORDER FOR ROUTE  /////////////////////////////////////
		jQuery(".order_append_here").on("click",".add_to_map_route",function(){

			var numOrder = jQuery(".outerTable").find(".singleCheck:checked").length;
			var numAppended = jQuery(".append_dom_here").find(".appendedTextField").length;
			if(numOrder < 1){
				alert("Please chose atleast 1 order to add.");
				return false;
			}
			else if(numOrder > 20-numAppended){
				alert("Maximum 20 orders can be added for route.");
				return false;
			}

			htmlAppend = '';
			var numAdded = 0;
			var alreadyAddedOrdersNum = 0;
			var alreadyAddedOrdersNumArray = [];
			jQuery(".outerTable").find(".singleCheck:checked").each(function(){
				var orderId = jQuery(this).val();

				var checkAlreadyAdded = false;
				jQuery(".append_dom_here").find(".added-order-id").each(function(){
					var idVal = jQuery(this).val();

					if(idVal == orderId){
						checkAlreadyAdded = true;
						alreadyAddedOrdersNumArray.push(orderId);
					}
				});

				if(checkAlreadyAdded === true){
					alreadyAddedOrdersNum++;
					return;
				}

				var fullAddress = '<div class="appendedTextField"><div class="form-group">';
				var fullText = '';
				jQuery(".addressClass_"+orderId).each(function(){
					fullText += jQuery(this).text()+' ';
				});
				var domCount = jQuery("#countNumber").val();
				numAdded++;
				fullText = fullText.trim();
				fullAddress += '<input type="text" class="form-control inpt-fid" name="deliveries_address[]" value="'+fullText+'" />';
				fullAddress += '<input type="hidden" class="added-order-id" name="added-order-id" value="'+orderId+'" />';
				fullAddress += '<a href="javascript:;" value="Remove" class="removeButton fa fa-remove" id="remove-'+domCount+'"></a></div></div>';
				domCount++;
				jQuery("#countNumber").val(domCount);

				htmlAppend += fullAddress;
			});
			jQuery(".append_dom_here").append(htmlAppend);

			if(alreadyAddedOrdersNum>0){
				alert(alreadyAddedOrdersNum+" order(s) already added:\n"+alreadyAddedOrdersNumArray);
			}

			if(numAdded>0){
				alert(numAdded+" order(s) added successfully.");
				jQuery('html, body').animate({ scrollTop: jQuery("#animate_here").offset().top-150 }, 1000);
			}
			else{
				alert("No order added.");
			}
		});

		//////////////////////////////////// UNCHECK FUNCTIONALITY  /////////////////////////////////////
		jQuery(".order_append_here").on("click",".uncheck_orders",function(){
			jQuery(this).prop("disabled",true);
			jQuery(".outerTable").find(".headTypeCheck").prop("checked",false);
			jQuery(".outerTable").find(".singleCheck:checked").each(function(){
				jQuery(this).prop("checked",false);
			});
			
		});

	});

	//// For delete added order form delivery locations
	jQuery(".order-screen").on("click",".removeButton",function(){
		if(confirm("Are you sure to delete this delivery point?")){
			jQuery(this).parent().parent(".appendedTextField").remove();
		}
	});

</script><?php
if($restrictScreen === false){
?><script>
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
	var gmarkers = [];
	var usedPoints = [];
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

		map = new google.maps.Map(document.getElementById('vsz_rp_map_display'), {
			center: {lat: lati, lng: longi},
			zoom: defZoom,
		});
	}

	// to validate the required fields for map
	function validateRequiredFields(el){
		var origin_address = jQuery("#starting_point").val();
		var destination_address = jQuery("#ending_point").val();

		if(origin_address == ""){
			alert("Please insert Start point address or zipcode.");
			jQuery('.pop-up-btn').magnificPopup.close();
			return false;
		}
		if(destination_address == ""){
			alert("Please insert End point address or zipcode.");
			jQuery('.pop-up-btn').magnificPopup.close();
			return false;
		}

		jQuery(".loader-route").show();

		jQuery.magnificPopup.open({
                items: {
                    src: '#displayMapPopup',
                },
                type: 'inline'
            });
		initialize();
	}
	
	function initialize() {
		var lati = <?php echo $rp_default_latitude; ?>;
		var longi = <?php echo $rp_default_longitude; ?>;
		var defZoom = <?php echo $rp_default_zoom; ?>;

		directionsDisplay = new google.maps.DirectionsRenderer();
		var chicago = new google.maps.LatLng(lati, longi);
		
		var myOptions = {
		  zoom: defZoom,
		  mapTypeId: google.maps.MapTypeId.ROADMAP,
		  center: chicago
		}
		infoWindow = new google.maps.InfoWindow;
		map = new google.maps.Map(document.getElementById("vsz_rp_map_display"), myOptions);
		google.maps.event.addListener(map, 'click', function() {
			infoWindow.close();
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
		labelIndex = 0;
		directionsService = new google.maps.DirectionsService(); 
		var waypts = [];
		var deliveryAddressArray = document.getElementsByName('deliveries_address[]');
		for (var i = 0; i < deliveryAddressArray.length; i++) {

			var deliveryArray = deliveryAddressArray[i].value;
			if (deliveryArray.length > 0) {
				waypts.push({
				location: deliveryArray,
				stopover: true
				});
			}
		}
		var request = {
			origin: document.getElementById('starting_point').value,
			destination: document.getElementById('ending_point').value,
			waypoints: waypts,
			optimizeWaypoints: true,
			travelMode: google.maps.DirectionsTravelMode.WALKING
		};
		directionsService.route(request, RenderCustomDirections);
	}
	
	// Callback function for calculate and display route map
	function RenderCustomDirections(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			waypts = [];
			var polyline = new google.maps.Polyline({
				path: [],
				strokeColor: '#00b3fd',
				strokeWeight: 5
			});
			polyline.setMap(null);
			
			var bounds = new google.maps.LatLngBounds();
			var route = response.routes[0];
			var summaryPanel = document.getElementById("directions-panel-listing");
			startLocation = new Object();
			endLocation = new Object();
			
			// Clear the saved addresses
			usedPoints = [];
			
			summaryPanel.innerHTML = "";

			// For each route, display summary information.
			var innerHtml = '<table class="mrgB15" width="100%">'+
									'<tr>'+
										'<td><div class="hd-typ2 map-title" width="100%">Delivery Directives</div>'+
										'<div id="route-total" class="text-left" width="100%" align="left"></div></td>'+
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
			
			innerHtml += '</tr></table>';
			summaryPanel.innerHTML += innerHtml;
			// To display total distance in 1 point fraction
			totaldistance = (Math.round(totaldistance*10))/10;
			jQuery('#route-total').html("<b>Total Distance</b>: " +totaldistance + " KM");
			// Route Segment HTML ends //
		
			var path = response.routes[0].overview_path;
			var legs = response.routes[0].legs;
			var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			for (m=0;m<legs.length;m++) {
				if (m == 0) { 
					startLocation.latlng = legs[m].start_location;
					startLocation.address = legs[m].start_address;
					startLocation.marker = createMarker(legs[m].start_location,labels[labelIndex++ % labels.length],legs[m].start_address,"green",i);
				} else { 
					waypts[m] = new Object();
					waypts[m].latlng = legs[m].start_location;
					waypts[m].address = legs[m].start_address;
					waypts[m].marker = createMarker(legs[m].start_location,labels[labelIndex++ % labels.length],legs[m].start_address,"blue",i);
				}
				endLocation.latlng = legs[m].end_location;
				endLocation.address = legs[m].end_address;
				var steps = legs[m].steps;
				
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
			
			// Clear the saved addresses
			usedPoints.length = 0;
			usedPoints = usedPoints.filter( function( el ) {
			  return toRemove.indexOf( el ) < 0;
			} );
		}
		else{
			jQuery(".loader-route").hide();
			jQuery('.pop-up-btn').magnificPopup('close');
			window.alert('No routes found for given addresses. Please try again with correct addresses.');
			// else alert(status);
			//map and directions-panel should be hide
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
    
	// Callback function to create marker
	function createMarker(latlng, text, html, color) {
		var markerLat = latlng.lat();
		var markerLng = latlng.lng();
		var infowindow = new google.maps.InfoWindow({ 
			size: new google.maps.Size(150,50)
		});
		
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
				draggable: false	, 
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
	
	////// Print functionality starts  //////
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
	////// Print functionality ends  //////
	
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
	////// Print functionality ends  //////
</script>
<script>
// For sticky header at orders table
jQuery('.pane-hScroll').scroll(function() {
  jQuery('.pane-vScroll').width(jQuery('.pane-hScroll').width() + jQuery('.pane-hScroll').scrollLeft());
});
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $site_key; ?>"></script><?php
} ?>