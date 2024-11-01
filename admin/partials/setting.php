<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


$site_key = '';
$rp_default_longitude = '';
$rp_default_latitude = '';
$rp_default_zoom = '';
$secret_key = '';

// update process
if(isset($_POST['rp_setting_submit'])){
	// getting parameters
	$site_key = isset($_POST['rp_site_key']) ? $_POST['rp_site_key'] : '';
	$rp_default_longitude = isset($_POST['rp_default_longitude']) ? $_POST['rp_default_longitude'] : '';
	$rp_default_latitude = isset($_POST['rp_default_latitude']) ? $_POST['rp_default_latitude'] : '';
	$rp_default_zoom = isset($_POST['rp_default_zoom']) ? $_POST['rp_default_zoom'] : '';

	if($site_key == ""){
		$errorMessage = "Site key should not be blank.";
		goto elsePart;
	}
	if($rp_default_longitude == ""){
		$errorMessage = "Default longitude should not be blank.";
		goto elsePart;
	}
	if($rp_default_latitude == ""){
		$errorMessage = "Default latitude should not be blank.";
		goto elsePart;
	}
	if($rp_default_zoom == ""){
		$errorMessage = "Default zoom should not be blank. Please insert default zoom level in between 1 to 12.";
		goto elsePart;
	}
	else if($rp_default_zoom > 12 || $rp_default_zoom<1){
		$errorMessage = "Please insert default zoom level in between 1 to 12.";
		goto elsePart;
	}

	// saving site key
	update_option('rp_site_key',sanitize_text_field($site_key));
	update_option('rp_default_longitude',sanitize_text_field($rp_default_longitude));
	update_option('rp_default_latitude',sanitize_text_field($rp_default_latitude));
	update_option('rp_default_zoom',sanitize_text_field($rp_default_zoom));
	$successMessage = "Setting saved successfully";
}
elsePart:
$site_key = get_option('rp_site_key');
$rp_default_longitude = get_option('rp_default_longitude');
$rp_default_latitude = get_option('rp_default_latitude');
$rp_default_zoom = get_option('rp_default_zoom');

if(empty($rp_default_zoom)){
	$rp_default_zoom = 9;
}

wp_enqueue_script( 'vsz_rutp_bootstrap-min-js' );
wp_enqueue_style( 'vsz_rutp_bootstrap-min-css' );
wp_enqueue_style( 'vsz_rutp_admin-css' );
?><style>
	html body{
		background-color: #F1F1F1;
	}
	.ui-sortable-handle{
		cursor: pointer ! important;
	}
	.displayOnlyButton{
		text-align: center;
		padding: 20px;
	}
</style>
<div class="container">
	<div class="wrap">
		<h3 class="wp-heading-inline h1-type">Map Settings</h3>
		<hr class="wp-header-end">
		<div class="displayMessage"><?php
			if(isset($successMessage) && !empty($successMessage)){
				?><div class="alert alert-success alert-dismissible">
					<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">
						<span aria-hidden="true">x</span>
					</button>
					<?php echo $successMessage; ?>
				</div><?php
			}
			if(isset($errorMessage) && !empty($errorMessage)){
				if(is_array($errorMessage)){
					foreach($errorMessage as $m){
						?><div class="alert alert-danger alert-dismissible">
							<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">
								<span aria-hidden="true">x</span>
							</button>
							<?php echo $m; ?>
						</div><?php
					}
				}
				else{
					?><div class="alert alert-danger alert-dismissible">
						<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">
							<span aria-hidden="true">x</span>
						</button>
						<?php echo $errorMessage; ?>
					</div><?php
				}
			}
		?></div>
		<form name="settingForm" id="settingForm" method="POST" class="">
			<div id="poststuff">
			<!-- For publish post button -->
				<div id="post-body" class="metabox-holder columns-2">
					<div id="postbox-container-1" class="postbox-container">
						<div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
							<div id="submitdiv" class="postbox">
								<button type="button" class="handlediv button-link" aria-expanded="true">
									<span class="screen-reader-text">Toggle panel: Publish</span>
									<span class="toggle-indicator" aria-hidden="true"></span>
								</button>
								<h2 class="hndle ui-sortable-handle">
									<span>Publish</span>
								</h2>
								<div class="inside">
									<div class="displayOnlyButton">
										<input type="submit" id="publish" name="rp_setting_submit" class="button button-primary button-large" value="Update" />
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="post-body" class="metabox-holder columns-2">
					<!-- For surgery information -->
					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="surgery_info_box" class="postbox">
								<button class="handlediv button-link" type="button" aria-expanded="true">
									<span class="screen-reader-text">Toggle panel: Surgery Information</span>
									<span class="toggle-indicator" aria-hidden="true"></span>
								</button>
								<h2 class="hndle ui-sortable-handle">
									<span><b>Default Map Settings</b></span>
								</h2>
								<div class="inside">
									<p>
										<div class="row">
											<div class="col-sm-12">
											<div class="form-group">
											<div class="hd-typ13"> Google Map Key*</div>
											<input type="text" id="rp_site_key" name="rp_site_key" class="rp_site_key form-control inpt-fid" value="<?php echo stripslashes($site_key); ?>" /></div></div>
										</div>
									</p>
									<p>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
													<div class="hd-typ13">Default Latitude*</div>
													<input type="text" id="rp_default_latitude" name="rp_default_latitude" class="rp_default_latitude form-control inpt-fid" value="<?php echo stripslashes($rp_default_latitude); ?>" />
												</div>
											</div>
										</div>
									</p>
									<p>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
													<div class="hd-typ13">Default Longitude*</div>
													<input type="text" id="rp_default_longitude" name="rp_default_longitude" class="rp_default_longitude form-control inpt-fid" value="<?php echo stripslashes($rp_default_longitude); ?>" />
												</div>
											</div>
										</div>
									</p>
									<p>
										<div class="row">
											<div class="col-sm-12">
											<div class="form-group">
											<div class="hd-typ13">Default Zoom Level*</div>
											<input type="number" min="1" max="12" id="rp_default_zoom" name="rp_default_zoom" class="rp_default_zoom form-control inpt-fid" value="<?php echo stripslashes($rp_default_zoom); ?>" />
											</div>
											</div>
										</div>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		//// To show / hide metabox by heading
		jQuery(".ui-sortable-handle").click(function(){
			var parentId = jQuery(this).parent(".postbox").attr('id');
			if(jQuery("#"+parentId).find(".inside").css("display") === "none"){
				jQuery("#"+parentId+" .inside").show();
				jQuery("#"+parentId).removeClass("closed");
			}
			else{
				jQuery("#"+parentId+" .inside").hide();
				jQuery("#"+parentId).addClass("closed");
			}
		});
		//// To show / hide metabox by arrow sign
		jQuery(".postbox .handlediv.button-link").click(function(){
			var parentId = jQuery(this).parent(".postbox").attr('id');
			if(jQuery("#"+parentId).find(".inside").css("display") === "none"){
				jQuery("#"+parentId+" .inside").show();
				jQuery("#"+parentId).removeClass("closed");
			}
			else{
				jQuery("#"+parentId+" .inside").hide();
				jQuery("#"+parentId).addClass("closed");
			}
		});

		jQuery(".displayMessage").on("click",".notice-dismiss",function(){
			jQuery(this).parent(".notice").remove();
		});

		// validate the form
		jQuery('input[name="rp_setting_submit"]').click(function(){
			var site_key = jQuery("#rp_site_key").val();
			site_key = site_key.trim();
			var latitude = jQuery("#rp_default_latitude").val();
			latitude = latitude.trim();
			var longitude = jQuery("#rp_default_longitude").val();
			longitude = longitude.trim();
			var default_zoom = jQuery("#rp_default_zoom").val();
			default_zoom = default_zoom.trim();
			var digitRegx = /^([.\-0-9])+$/;  // digit checking regex
			
			if(site_key == ""){
				jQuery(".displayMessage").html('<div class="alert alert-danger alert-dismissible">'+
							'<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">'+
								'<span aria-hidden="true">x</span>'+
							'</button>'+
							'Site key should not be blank.'+
						'</div>');
				return false;
			}
			if(latitude == ""){
				jQuery(".displayMessage").html('<div class="alert alert-danger alert-dismissible">'+
							'<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">'+
								'<span aria-hidden="true">x</span>'+
							'</button>'+
							'Default latitude should not be blank.'+
						'</div>');
				return false;
			}
			else if(!digitRegx.test(latitude)){
				jQuery(".displayMessage").html('<div class="alert alert-danger alert-dismissible">'+
							'<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">'+
								'<span aria-hidden="true">x</span>'+
							'</button>'+
							'Latitude should be in digit only.'+
						'</div>');
				return false;
			}
			if(longitude == ""){
				jQuery(".displayMessage").html('<div class="alert alert-danger alert-dismissible">'+
							'<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">'+
								'<span aria-hidden="true">x</span>'+
							'</button>'+
							'Default longitude should not be blank.'+
						'</div>');
				return false;
			}
			else if(!digitRegx.test(longitude)){
				jQuery(".displayMessage").html('<div class="alert alert-danger alert-dismissible">'+
							'<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">'+
								'<span aria-hidden="true">x</span>'+
							'</button>'+
							'Longitude should be in digit only.'+
						'</div>');
				return false;
			}
			if(site_key == ""){
				jQuery(".displayMessage").html('<div class="alert alert-danger alert-dismissible">'+
							'<button class="close" type="button" data-dismiss="alert" aria-label="Close" style="display:block;">'+
								'<span aria-hidden="true">x</span>'+
							'</button>'+
							'Default zoom should not be blank. Please insert default zoom level in between 1 to 12.'+
						'</div>');
				return false;
			}
		});

	});
</script>