<?php
// get the time in either 12 or 24 hr format
// get date in the format the user chose
// Get the actual result lines for the stock history report
	global $wpdb; 
	$table_name = $wpdb->prefix . "stockmanager";
	foreach ( $records as $record ) {
// display the stock report history page
	global $pagenow;
// Build the actual detail rows
// display the stock report history details
	global $wpdb;
    $tab = $_GET[ 'id' ];