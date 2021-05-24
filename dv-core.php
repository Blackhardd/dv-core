<?php

/*
Plugin Name: DV Core
Plugin URI: https://topranker.cz/
Description: Theme custom functionality.
Version: 1.0
Author: Topranker
Author URI: https://topranker.cz/
License: GPL3
*/

if( !defined( 'DV_PLUGIN_VER' ) ):
    define( 'DV_PLUGIN_VER', '1.0' );
endif;

if( !defined( 'DV_PLUGIN_PATH' ) ):
    define( 'DV_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
endif;

if( !defined( 'DV_PLUGIN_URL' ) ):
    define( 'DV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
endif;


/**
 *  Requiring Composer autoload.
 */
require DV_PLUGIN_PATH . 'vendor/autoload.php';


/**
 *  Enqueue scripts and styles for plugin.
 */
add_action( 'admin_enqueue_scripts ', 'dv_enqueue_admin_scripts', 1, 1 );
function dv_enqueue_admin_scripts( $suffix ){
    wp_enqueue_style( 'adminstyle', DV_PLUGIN_URL . 'assets/css/dv-admin.css', array(), DV_PLUGIN_VER, 'all' );
}


/**
 *  Requiring plugin parts.
 */
require DV_PLUGIN_PATH . 'inc/framework.php';
require DV_PLUGIN_PATH . 'inc/ajax.php';
require DV_PLUGIN_PATH . 'inc/options.php';