<?php
/*
Plugin Name: Project Guide
Plugin URI: https://wordpress.org/plugins/project-guide/
Description: Create a simple and awesome project guide in your WordPress admin area.
Version: 1.2.1
Author: Tobias Karnetzke
Author URI: http://www.athoss.de
Tested up to: 4.6.2
Text Domain: project-guide
Domain Path: /languages
License: GPLv2 or later  
*/

if (!defined('ABSPATH')) die('-1');

define('PRGUIDE_VERSION','1.0.1');
define('PRGUIDE_PLUGIN_DIR_NAME','project-guide');
define('PRGUIDE_PLUGIN_PATH',dirname(__FILE__));
define('PRGUIDE_WP_PLUGINS_URL',plugins_url());
define('PRGUIDE_PLUGIN_URL',plugin_dir_url( __FILE__ ));
define('PRGUIDE_CSS_DIR','css');
define('PRGUIDE_ADMIN_PAGE_ID','projectguide');
define('PRGUIDE_ADMIN_NAV_PAGE_ID','pgnavigation');
define('PRGUIDE_ADMIN_SETTINGS_PAGE_ID','pgsettings');
define('PRGUIDE_POST_TYPE_ID','projectguide');
define('PRGUIDE_EDIT_CAPABILITY','manage_options');


/**
 * Load plugin textdomain.
 *
 * @since 1.0.1
 */
function prguide_init() {      
        
        register_nav_menu('projectguide', __('Navigation','project-guide'));          
        
        load_plugin_textdomain( 'project-guide', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
        
        prguide_admin_init();
        
}
add_action( 'init', 'prguide_init' );


/**
 *  Load scripts and styles
 *  
 * @since 1.0.1
 */
function prguide_admin_enqueue_scripts() {                
        
        wp_enqueue_script('jquery');                
        wp_enqueue_script( 'pg-global', plugin_dir_url( __FILE__ ) . '/js/global.js',array(), '1.0.1',true );	
        wp_enqueue_script( 'pg-domenu', plugin_dir_url( __FILE__ ) . '/lib/domenu-master/jquery.domenu-0.95.77.min.js',array(), '0.95.77',true );
        wp_enqueue_style( 'pg-googlefonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro|Inconsolata:700|" rel="stylesheet', false ); 
        wp_enqueue_style( 'pg-admin', PRGUIDE_PLUGIN_URL . PRGUIDE_CSS_DIR . '/admin.css', false );   
        wp_enqueue_style( 'pg-guide', PRGUIDE_PLUGIN_URL . PRGUIDE_CSS_DIR . '/guide.css', false );           
        
}
add_action( 'admin_enqueue_scripts', 'prguide_admin_enqueue_scripts' );

/*
 * Include scripts
 * 
 */
include('inc/admin.php');
include('inc/posttype.php');

?>