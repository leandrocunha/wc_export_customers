<?php
/**
 * Plugin Name: WC Export Customers
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Export the list of customers in .csv format
 * Version: 0.0.2
 * Author: Leandro Cunha aka. Frango
 * Author URI: https://github.com/leandrocunha
 * License: A short license name. Example: GPL2
 */


// Prevente direct access
defined('ABSPATH') or die("No script kiddies please!");


// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


	// Load customers
	$customers = get_users( 'blog_id=1&role=customer' );
	$sitename = sanitize_key( get_bloginfo( 'name' ) );
    $filename = ( !empty($sitename) ) ? $sitename . '.' . date('ymdHis', time()) . '.csv' : date('ymdHis', time()) . '.csv';
    $users = array();

	// echo '<pre>';
	// print_r( $customers );
	// echo '</pre>';

	foreach ($customers as $customer) {
		// echo $customer->user_email;
		array_push( $users, $customer->user_email );
	}

	// print_r( $users );

	add_filter( 'woocommerce_reports_charts', 'export_to_csvv' );


    function export_to_csvv( $reports ) {
        $reports['customers']['reports']['export'] = array(
            'title'      	=> ' Exportar Clientes',
            'description'	=> 'Para exportar a lista de clientes no formato CSV, clique no botão abaixo "Exportar CSV".',
            'hide_title'	=> true,
            'callback'		=> 'download'
        );

        return $reports;
    }

    function download(){
		$output .= '<form method="post" action="" enctype="multipart/form-data">';
		$output .= wp_nonce_field( 'mhm-export-customer-email', '_wpnonce-mhm-export-customer-email' );
		$output .= '<p class="submit">';
		$output .= '<input type="hidden" name="_wp_http_referer" value="' . $_SERVER['REQUEST_URI'] . '" />';
		$output .= '<input type="submit" class="button-primary" value="Exportar CSV" />';
		$output .= '</p>';
		$output .= '</form>';

		echo $output;
    }


	if ( isset( $_POST['_wpnonce-mhm-export-customer-email'] ) ) {
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		// output the column headings
		fputcsv($output, array('Email'));

		// loop over the rows, outputting them
		$i = 0;
		foreach ($users as $user) {
			fputcsv($output, array($user));
			$i++;
		}

		die();
	}
}
