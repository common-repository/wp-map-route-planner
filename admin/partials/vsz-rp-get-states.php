<?php
// Exit if accessed directly
if(!defined( 'ABSPATH' ) ) {
	exit;
}
?><option value=""> -- All -- </option><?php
if(!check_ajax_referer( 'vsz_rp_get_states_nonce', 'ajax_nonce')){
	return;
}

if( !isset($_POST['country']) || empty($_POST['country']) ){
	wp_die();
}	

$country = isset($_POST['country']) ? sanitize_text_field(urldecode($_POST['country'])) : '';
if(empty($country)){
	wp_die();
}
else{
	$state_obj   = new WC_Countries();
	$totalStates = $state_obj->get_states();
	if(isset($totalStates[$country])){
		$states   = $totalStates[$country];
		if(!empty($states)){
			foreach($states as $code=>$state){
				?><option value="<?php echo $code; ?>"><?php echo $state; ?></option><?php
			}
		}
		return;
	}
	else{
		$states = '';
		return;
	}
}

wp_die();