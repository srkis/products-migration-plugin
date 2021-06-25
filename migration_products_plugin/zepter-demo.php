<?php

/**
 * Plugin Name:  Get Products via REST API - Migrate products Demo
 * Description:  Migrate products from remote server .
 * Plugin URI:   https://github.com/srkis
 * Author:       Srdjan Stojanovic
 * Version:      1.0
 * Text Domain:  getpostsviarestapi
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package getpostsviarestapi
 */
// Disable direct file access.

if (!defined('ABSPATH')) {
    exit;
}



include_once(ABSPATH . 'wp-includes/pluggable.php');
include_once(ABSPATH . 'wp-includes/user.php');
include_once(ABSPATH . 'wp-content/plugins/zepter-demo/classes/Zepter_category_widget.php');
include_once dirname( __FILE__ ) . '/includes/admin_settings_page.php';
require_once(ABSPATH . 'wp-load.php');





global $woocommerce, $country;

if(isset($_GET['country'])){
    $country = $_GET['country'];
}


$visitorData = getVisitorData();

if(is_object($visitorData) && $visitorData->status == 'success'){
    global $visitorData;

}else{
    $visitorData = getVisitorData();
    global $visitorData;
}







if (isset($_POST['action']) && $_POST['action'] == 'get_products') {


    $migrateProducts = $_POST['migrateProducts'];
    $limit= $_POST['limit'];
    $migrateSingle = $_POST['migrateSingle'];
    $prod_id = $_POST['prod_id'];


        $result = array(
            "status" => "fail",
            "message" => "Error! Fields can not be empty!");
        header('Content-type: application-json; charset=utf8;');
        echo json_encode($result);
        die;

}


// Hook before calculate fees
add_action('woocommerce_cart_calculate_fees' , 'add_user_discounts');


/**
 * Add custom fee if more than three article
 * @param WC_Cart $cart
 */
function add_user_discounts( WC_Cart $cart ){

    global $woocommerce;
    $shop_country = $woocommerce->countries->get_base_country();


    switch( $shop_country) {
        case 'RS': $percentage = 20;
            break;

        case 'SI': $percentage = 22;
            break;

        case 'RU': $percentage = 18;;
            break;

        default:
            $percentage = 20;

    }

    $text = "Tax for " . " '" . $shop_country . "' " . $percentage . '%';
    $price = $cart->get_subtotal();
    $new_price = ($percentage / 100) * $price;

    $cart->add_fee($text, $new_price);

}


add_filter( 'woocommerce_get_price_html', 'kd_custom_price_message',10, 2 );
add_filter( 'woocommerce_cart_item_price', 'kd_custom_price_message', 10, 2);
add_filter( 'woocommerce_cart_item_subtotal', 'kd_custom_price_message', 10, 2 ); // added
add_filter( 'woocommerce_cart_subtotal', 'kd_custom_price_message', 10, 2); // added
add_filter( 'woocommerce_cart_total', 'kd_custom_price_message', 10, 2 ); // added



function kd_custom_price_message( $price) {
    global $visitorData, $country;
    if(!is_object($visitorData)){
        $visitorData = getVisitorData();
    }

    global $woocommerce;
    $shop_country = $woocommerce->countries->get_base_country();

    //switch( isset($_GET['country']) && $_GET['country'] !='' ? $_GET['country'] : $visitorData->countryCode ) {
    switch( $shop_country) {
        case 'RS': $afterPriceSymbol = ' RSD';
            break;

        case 'SI': $afterPriceSymbol = '';
            break;

        case 'RU': $afterPriceSymbol = '';
            break;

        default:
            $afterPriceSymbol = '€';

    }

    return $price . $afterPriceSymbol;
}


add_filter('woocommerce_product_get_price', 'return_custom_price', $product = null, 2);

function return_custom_price($price, $product) {
    global $post, $visitorData, $country;

    if(!is_object($visitorData)){
        $visitorData = getVisitorData();
    }

    global $woocommerce;
    $shop_country = $woocommerce->countries->get_base_country();

    switch( $shop_country ) {
        //switch( isset($_GET['country']) && $_GET['country'] !='' ? $_GET['country'] : $visitorData->countryCode ) {
        case 'RS': $price;
            break;

        case 'SI': $price =  round((int) $price / 117,2);
            break;

        case 'RU': $price =  round((int) $price / 1.66,2);
            break;

        default:
            $afterPriceSymbol = '€';

    }

    return $price;

}

add_filter('woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2);

function change_existing_currency_symbol( $currency_symbol, $currency) {
    global $post, $country, $visitorData;

    if(!is_object($visitorData)){
        $visitorData = getVisitorData();
    }

    global $woocommerce;
    $shop_country = $woocommerce->countries->get_base_country();

    switch( $shop_country ) {
        // switch( isset($_GET['country']) && $_GET['country'] !='' ? $_GET['country'] : $visitorData->countryCode ) {
        case 'RS': $currency_symbol = '';
            break;

        case 'SI': $currency_symbol = '€';
            break;

        case 'RU': $currency_symbol =  '&#8381;';
            break;

        default:
            $currency_symbol = '€';

    }
    return $currency_symbol;
}

// Change the default country and state on checkout page.
// This works for a new session.
add_filter( 'default_checkout_billing_country', 'xa_set_default_checkout_country' );
add_filter( 'default_checkout_billing_state', 'xa_set_default_checkout_state' );
function xa_set_default_checkout_country() {
    global $visitorData;

    if(!is_object($visitorData)){
        $visitorData = getVisitorData();
    }
    // Returns empty country by default.
    //   return null;
    // Returns US as default country.
    return $visitorData->countryCode;
}

function xa_set_default_checkout_state() {
    // Returns empty state by default.
    //   return null;
    // Returns Belgrade as default state.
    return 'Belgrade';
}


function getUserId(){

    $user_ID = get_current_user_id();

    return $user_ID;
}

$userId = getUserId();


if(isset($_GET['migrateSingle']) && isset($_GET['prod_id'])){
    global $migrateSingle;
    $migrateSingle = true;

}


if(isset($_GET['migrateProducts']) && $_GET['migrateProducts'] == 'all' && isset($_GET['limit'])) {

    global $migrateAll;
    $migrateAll = true;
    $runtime = 'run_only_01';

    if (get_option('my_run_only_once_option') != $runtime) {
        $updated = update_option('my_run_only_once_option', $runtime);
        if ($updated === true) {

            //insertPost($userId,$runtime);
         //   add_action('wp_loaded', 'insertPost');
        }
    }
}


delete_option('my_run_only_once_option');

if(isset($_POST['migrateProducts']) && isset($_POST['limit'])) {
        $runtime = 'run_only_01';
        if (get_option('my_run_only_once_option') != $runtime) {
            $updated = update_option('my_run_only_once_option', $runtime);
            if ($updated === true) {
        
                add_action( 'wp_ajax_myaction', 'insertPost' );

            }
        }

    }




function insertPost($userId)
{
    global $migrateAll, $migrateSingleb;


    $queries = array();
    parse_str($_SERVER['QUERY_STRING'], $queries);

    $API_URL = 'someapiurl';

    $response = wp_remote_get( $API_URL.$_POST['migrateProducts']."&limit=".$_POST['limit']."");



    if(!empty($queries) && count($queries) > 0 && $migrateSingle == true){

        $response = wp_remote_get( $API_URL.$queries['migrateSingle']."&prod_id=".$queries['prod_id']."");
    }

    if ( is_array($response )) {
        $header = $response['headers']; // array of http header lines
        $body = $response['body']; // use the content
        $res = json_decode($body, true);

        foreach ($res['zepter_products'] as $product) {

            foreach ($product['images'] as $image) {
                $coverImg = array_filter($product['images'], function ($value) {
                    return strpos($value, 'cover') !== false;
                });
            }

           // var_dump("<pre>", $coverImg) . '\r\n';

            $data = [
                'post_author' => $userId,
                'post_name' => $product['name'],
                'post_title' => $product['name'],
                'post_type' => 'product',
                'post_status' => 'publish',
                'post_content' => $product['product_desc'],
                'post_excerpt' => 'excerpt 3',
            ];

            if(strlen($data['post_content']) > 2) {
              $post_id = wp_insert_post($data);

                       
            if( ! term_exists($product['category'], 'product_cat')) {

                $term =  wp_insert_term(
                    $product['category'], // the term
                    'product_cat', // the taxonomy
                    array(
                        'description'=> $product['cat_description'],
                        'slug' => $product['slug']
                    )
                );

                wp_set_object_terms( $post_id, $term['term_id'], 'product_cat' );

            }else{

                $term = get_term_by('name', $product['category'], 'product_cat');

               wp_set_object_terms($post_id, $term->term_id, 'product_cat');

            }

        }

            if (is_int(wp_is_post_revision($post_id)))
                return;

            if (is_int(wp_is_post_autosave($post_id)))
                return;

            update_post_meta($post_id, '_visibility', 'visible');
            update_post_meta($post_id, '_stock_status', 'instock');
            update_post_meta($post_id, '_tax_status', 'taxable');
            update_post_meta($post_id, 'total_sales', '0');
            update_post_meta($post_id, '_downloadable', 'no');
            update_post_meta($post_id, '_virtual', 'yes');
            update_post_meta($post_id, '_regular_price', '');
            update_post_meta($post_id, '_sale_price', '');
            update_post_meta($post_id, '_purchase_note', '');
            update_post_meta($post_id, '_featured', 'no');
            update_post_meta($post_id, '_weight', '11');
            update_post_meta($post_id, '_length', '11');
            update_post_meta($post_id, '_width', '11');
            update_post_meta($post_id, '_height', '11');
            update_post_meta($post_id, '_sku', 'SKU11');
            update_post_meta($post_id, '_product_attributes', array());
            update_post_meta($post_id, '_sale_price_dates_from', '');
            update_post_meta($post_id, '_sale_price_dates_to', '');
            update_post_meta($post_id, '_price', $product['product_price']);
            update_post_meta($post_id, '_sold_individually', '');
            update_post_meta($post_id, '_manage_stock', 'yes');
            wc_update_product_stock($post_id, 3, 'set');
            update_post_meta($post_id, '_backorders', 'no');

            if (is_int(wp_is_post_revision($post_id)))
                return;

            if (is_int(wp_is_post_autosave($post_id)))
                return;

            if(isset($coverImg[2])){
                attach_product_thumbnail($post_id, $coverImg[2], 0);
            }else if(isset($coverImg[4])){
                attach_product_thumbnail($post_id, $coverImg[4], 0);

            }else if(isset($coverImg[1])){
                attach_product_thumbnail($post_id, $coverImg[1], 0);
            }else{
                attach_product_thumbnail($post_id, $coverImg[0], 0);
            }

            foreach ($product['images'] as $image) {
                attach_product_thumbnail($post_id, $image, 1);
            }

            
        }
    }
}



/**
 * Attach images to product (feature/ gallery)
 */
function attach_product_thumbnail($post_id, $url, $flag){

    //If allow_url_fopen is enable in php.ini
    if( ini_get('allow_url_fopen') ) {

        $image_url = $url;
        $url_array = explode('/',$url);
        $image_name = $url_array[count($url_array)-1];
        $image_data = file_get_contents($image_url); // Get image data

    }else{

        //If allow_url_fopen is not enable in php.ini then use this
        $image_url = $url;
        $url_array = explode('/',$url);
        $image_name = $url_array[count($url_array)-1];
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $image_url);
        // Getting binary data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $image_data = curl_exec($ch);
        curl_close($ch);
    }

    $upload_dir = wp_upload_dir(); // Set upload folder
    $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); //    Generate unique name
    $filename = basename( $unique_file_name ); // Create image file name
    // Check folder permission and define file location
    if( wp_mkdir_p( $upload_dir['path'] ) ) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }
    // Create the image file on the server
    file_put_contents( $file, $image_data );
    // Check image file type
    $wp_filetype = wp_check_filetype( $filename, null );
    // Set attachment data
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name( $filename ),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    // Create the attachment
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    // Include image.php
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    // Assign metadata to attachment
    wp_update_attachment_metadata( $attach_id, $attach_data );
    // asign to feature image
    if( $flag == 0){
        // And finally assign featured image to post
        set_post_thumbnail( $post_id, $attach_id );
    }
    // assign to the product gallery
    if( $flag == 1 ){
        // Add gallery image to product
        $attach_id_array = get_post_meta($post_id,'_product_image_gallery', true);
        $attach_id_array .= ','.$attach_id;
        update_post_meta($post_id,'_product_image_gallery',$attach_id_array);
    }
}

function getVisitorData() {
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, 'http://ip-api.com/json');
    // Getting binary data
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $res = json_decode($response = curl_exec($ch));
    curl_close($ch);

    return $res;
}

function admin_js() {

    wp_register_script('admin_js', plugin_dir_url(__FILE__).'scripts/foradmin.js',  array('jquery'));
    wp_enqueue_script('admin_js');

    wp_register_style( 'loader', plugin_dir_url(__FILE__).'css/loader.css' );
    wp_enqueue_style('loader');
}


// Register and load the widget
function wpb_load_widget() {
    register_widget( 'Zepter_category_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
add_action('wp_loaded', 'getUserId');

add_action('wp_loaded', 'getVisitorData');
//delete_option('my_run_only_once_option');
add_action('admin_enqueue_scripts', 'admin_js');

register_activation_hook(__FILE__, 'yt_playlist_gallery_page');
