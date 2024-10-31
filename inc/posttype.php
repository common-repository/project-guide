<?php
/**
 * Custom Posttype Initialisation 
 *
 * @package project-guide
 */


/**
 * Create custom post type(s)
 */
function prguide_custom_posttype_init() {
        
        if (!current_user_can(PRGUIDE_EDIT_CAPABILITY)) {   
                return;  //---------------->
        }

        $labels = array(
		'name' => __('Guide','project-guide'),
		'singular_name' => __('Guide','project-guide'),
		'add_new' => __('New Topic','project-guide'),
		'add_new_item' => __('Add Topic','project-guide'),
		'edit_item' => __('Edit Topic','project-guide'),
		'new_item' => __('New Topic','project-guide'),
		'view_item' => __('View Topic','project-guide'),
		'search_items' => __('Search Topic','project-guide'),
		'not_found' =>  __('No Topics found','project-guide'),
		'not_found_in_trash' => __('No Topics found','project-guide'),
		'parent_item_colon' => '',
		'menu_name' => __('Project Guide','project-guide')
	);     
                
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'capability_type' => 'page',
		'has_archive' => false, 
		'hierarchical' => true,
		'menu_position' => 21,		
                'supports' => array('title','editor','author','revisions'),
                'menu_icon' => PRGUIDE_PLUGIN_URL . '/img/pg-logo-nav.png'
	); 

        register_post_type(PRGUIDE_POST_TYPE_ID, $args);       
  
        flush_rewrite_rules();

}
add_action( 'init', 'prguide_custom_posttype_init' );

?>