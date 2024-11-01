<?php
// Exit if accessed directly
if(!defined( 'ABSPATH' ) ) {
	exit;
}

/// Checking for nonce
if(!check_ajax_referer( 'vsz_rp_get_orders_nonce', 'ajax_nonce')){
	wp_die("You don't have permission to view this page");
}

/// Checking for user accessibility
if ( !current_user_can( 'manage_options' ) ) {
	wp_die("You don't have permission to view this page");
}

	// Getting parameters
	$search = '';
	$fromDate = isset($_POST['fromdate']) && !empty($_POST['fromdate']) ? sanitize_text_field(urldecode($_POST['fromdate'])) : '';
	$toDate = isset($_POST['todate']) && !empty($_POST['todate']) ? sanitize_text_field(urldecode($_POST['todate'])) : '';
	$searchbyid = isset($_POST['searchbyid']) && !empty($_POST['searchbyid']) ? sanitize_text_field(urldecode($_POST['searchbyid'])) : '';
	$searchbyaddress = isset($_POST['searchbyaddress']) && !empty($_POST['searchbyaddress']) ? sanitize_text_field(urldecode($_POST['searchbyaddress'])) : '';
	$searchbycountry = isset($_POST['searchbycountry']) && !empty($_POST['searchbycountry']) ? sanitize_text_field(urldecode($_POST['searchbycountry'])) : '';
	$searchbystate = isset($_POST['searchbystate']) && !empty($_POST['searchbystate']) ? sanitize_text_field(urldecode($_POST['searchbystate'])) : '';
	$searchbystatus = isset($_POST['searchbystatus']) && !empty($_POST['searchbystatus']) ? sanitize_text_field(urldecode($_POST['searchbystatus'])) : '';
	$searchbypayment = isset($_POST['searchbypayment']) && !empty($_POST['searchbypayment']) ? sanitize_text_field(urldecode($_POST['searchbypayment'])) : '';
	$where = ' 1=1 ';

	// To set from date search
	if(!empty($fromDate)){
		$dateInfo = date_parse_from_format('d/m/Y', $fromDate);
		$date = new DateTime($dateInfo['month']."/".$dateInfo['day']."/".$dateInfo['year']);
		$from_date = date_format($date,"Y-m-d");
		$fromTimeCoded = $from_date." 00:00:00";
		$where .= " AND P.post_date >= '".$fromTimeCoded."' ";
	}
	// To set to date search
	if(!empty($toDate)){
		$dateInfo = date_parse_from_format('d/m/Y', $toDate);
		$date = new DateTime($dateInfo['month']."/".$dateInfo['day']."/".$dateInfo['year']);
		$to_date = date_format($date,"Y-m-d");
		$toTimeCoded = $to_date." 23:59:59";
		$where .= " AND P.post_date <= '".$toTimeCoded."' ";
	}

	// To set search by ID, first_name, last_name, email
	if(!empty($searchbyid)){
		$where .= " AND (
							P.ID = '".$searchbyid."'
							OR ( M.meta_key = '_billing_first_name' AND M.meta_value LIKE '%".$searchbyid."%' )
							OR ( M.meta_key = '_shipping_first_name' AND M.meta_value LIKE '%".$searchbyid."%' )
							OR ( M.meta_key = '_billing_last_name' AND M.meta_value LIKE '%".$searchbyid."%' )
							OR ( M.meta_key = '_shipping_last_name' AND M.meta_value LIKE '%".$searchbyid."%' )
							OR ( M.meta_key = '_billing_email' AND M.meta_value LIKE '%".$searchbyid."%' )
						) ";
	}

	// To set search by address line 1, address line 2, postcode, city, state,company
	if(!empty($searchbyaddress)){
		$where .= " AND (
							( M1.meta_key = '_shipping_address_1' AND M1.meta_value LIKE '%".$searchbyaddress."%' )
							OR ( M1.meta_key = '_shipping_address_2' AND M1.meta_value LIKE '%".$searchbyaddress."%' )
							OR ( M1.meta_key = '_shipping_company' AND M1.meta_value LIKE '%".$searchbyaddress."%' )
							OR ( M1.meta_key = '_shipping_city' AND M1.meta_value LIKE '%".$searchbyaddress."%' )
							OR ( M1.meta_key = '_shipping_postcode' AND M1.meta_value LIKE '%".$searchbyaddress."%' )
						) ";
	}

	// To set search by country
	if(!empty($searchbycountry)){
		$where .= " AND (
							( M2.meta_key = '_shipping_country' AND M2.meta_value LIKE '%".$searchbycountry."%' )
						) ";
	}

	// To set search by State
	if(!empty($searchbystate)){
		$where .= " AND (
							( M3.meta_key = '_shipping_state' AND M3.meta_value LIKE '%".$searchbystate."%' )
						) ";
	}

	// To set search by status
	if(!empty($searchbystatus)){
		$where .= " AND P.post_status = '".$searchbystatus."' ";
	}

	// To set search by payment method
	if(!empty($searchbypayment)){
		$where .= " AND (
							( M4.meta_key = '_payment_method_title' AND M4.meta_value LIKE '%".$searchbypayment."%' )
						) ";
	}

	global $wpdb, $woocommerce;
	$query_order = "SELECT DISTINCT P.* FROM ".$wpdb->posts." P LEFT JOIN ".$wpdb->postmeta." as M ON P.ID = M.post_id
					LEFT JOIN ".$wpdb->postmeta." as M1 ON P.ID = M1.post_id
					LEFT JOIN ".$wpdb->postmeta." as M2 ON P.ID = M2.post_id
					LEFT JOIN ".$wpdb->postmeta." as M3 ON P.ID = M3.post_id
					LEFT JOIN ".$wpdb->postmeta." as M4 ON P.ID = M4.post_id
					WHERE P.post_type = 'shop_order' AND ".$where." ORDER BY P.ID DESC";
	
	$order_results = $wpdb->get_results( $query_order );			// gettong orders loop
	// var_dump($query_order); wp_die();
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
													if(isset($stateArray[$POCountry][$region])){
														$region = $stateArray[$POCountry][$region];
													}
													else{
														$region = $region;
													}
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
	<script>
// For sticky header at orders table
jQuery('.pane-hScroll').scroll(function() {
  jQuery('.pane-vScroll').width(jQuery('.pane-hScroll').width() + jQuery('.pane-hScroll').scrollLeft());
});
</script>
<?php
wp_die();