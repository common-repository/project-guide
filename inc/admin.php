<?php 
/**
 * Admin functions
 *
 * @package project-guide
 */

if (!defined('ABSPATH')) die('-1');

global $glb_prguide_nav_sections;
global $glb_prguide_data;
global $glb_prguide_status; 


/**
 * Init Project Guide
 * 
 */
function prguide_admin_init() {        
        
        global $glb_prguide_data;
        global $glb_prguide_status; 
        
        if (!isset($glb_prguide_data['json']) || !is_array($glb_prguide_data['json'])){
                if (!$glb_prguide_data['json'] = get_option('prguide_nav_jsondata')){
                        $glb_prguide_data['json'] = '[]';
                }                    
        }        
        
        $glb_prguide_status['nav'] = false;
        $a_data = json_decode($glb_prguide_data['json'],true);     
        if (is_array($a_data) && count($a_data) > 0){
                $glb_prguide_data['array']  = $a_data;
                $glb_prguide_status['nav'] = true;
        }  
        
}


/**
 * Add menu pages
 * 
 */
function prguide_admin_pages_init() {        
        
        $guide_menu_title = get_option('opt_guide_menu_title');
        $guide_menu_title = ($guide_menu_title ? $guide_menu_title : __('Project Guide','project-guide'));
        $guide_menu_title = esc_attr($guide_menu_title);
        
        $guide_disabled =(get_option('opt_guide_disabled')) ? true : false;
        
        if (prguide_current_user_can_view() && $guide_disabled === false){
                add_dashboard_page($guide_menu_title, $guide_menu_title, 'read', 'projectguide', 'prguide_admin_dashboard_page_content');        
        }
      
        add_submenu_page( 'edit.php?post_type='.PRGUIDE_POST_TYPE_ID, 
                __('Builder','project-guide'), 
                __('Builder','project-guide'),
                PRGUIDE_EDIT_CAPABILITY, 
                PRGUIDE_ADMIN_NAV_PAGE_ID, 
                'prguide_admin_builder'
        );      
        
        add_submenu_page( 'edit.php?post_type='.PRGUIDE_POST_TYPE_ID, 
                __('Settings','project-guide'), 
                __('Settings','project-guide'),
                PRGUIDE_EDIT_CAPABILITY, 
                PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 
                'prguide_admin_settings'
        );        
        
}
add_action('admin_menu', 'prguide_admin_pages_init');


/**
 * Get admin URL
 * 
 * @return type
 */ 
function prguide_get_admin_url(){    
        
        $blog_id = get_current_blog_id();              
        return get_admin_url($blog_id);     
        
}


/**
 * Create section depp link
 * 
 * @param type $section_id
 * @return boolean
 */
function prguide_get_section_deep_link($section_id=false){
        
        if ($section_id === false){
                return false;
        }
        
        return prguide_get_admin_url() . 'index.php?page=' . PRGUIDE_ADMIN_PAGE_ID .'#'. $section_id;
}


/**
 * Get admin URL add new projectguide item
 * 
 * @return type
 */ 
function prguide_get_admin_url_add_item(){            
        
        return prguide_get_admin_url() . 'post-new.php?post_type=' . PRGUIDE_POST_TYPE_ID;      
        
}


/**
 * Get admin URL edit content
 * 
 * @return type
 */ 
function prguide_get_admin_url_edit_item($post_id=false){    
        
        if ($post_id === false){ 
                return false;
        }
        
        return prguide_get_admin_url() . 'post.php?action=edit&post=' . $post_id;     
        
}

        
/**
 * Check, if user has a specific user role.
 * 
 * @return type
 */
function prguide_current_user_can_view() {        
        
        if ( !is_user_logged_in()) { 
                return false;                 
        }                
        
	$user = wp_get_current_user();
        
        if (!isset($user->roles) ||  !is_array($user->roles) || count($user->roles) <= 0){                
                return false;
        }
    
        $perm_access_roles = get_option('opt_guide_access_roles');
        if (!isset($perm_access_roles) || !is_array($perm_access_roles)){
                $perm_access_roles = array();
        }    
               
        foreach($user->roles as $current_role){                                              
               if (strtolower($current_role) == 'administrator' || in_array($current_role,$perm_access_roles)){
                       return true;
               }
       }
        
	return false;
}


/**
 * Get editable roles
 * 
 * @global type $wp_roles
 * @return type
 */
function prguide_get_editable_roles() {
    
        global $wp_roles;
        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);
        return $editable_roles;
        
}


/**
 * Generate permission checkboxes
 * 
 * 
 * @param type $field
 * @param type $checked
 * @return boolean
 */
function prguide_role_editable_chechboxes($field=false,$checked=array()){        
        
        if ($field === false){
                return false;
        }
        
        $checked = (is_array($checked)) ? $checked : array();
        
        $eroles = prguide_get_editable_roles();
        
        if (isset($eroles) && is_array($eroles) && count($eroles) > 0){
                
                foreach ($eroles as $erole){                   
                        
                       $erole['name'] = strtolower($erole['name']);
                                
                        if ($erole['name'] == 'administrator'){                                
                                
                                echo'<p>';                        
                                echo '<input type="checkbox" name="prguide_aancb_dis[]" value="' . esc_attr($erole['name']) . '" checked disabled />';
                                echo '<label>' . esc_html($erole['name']) . '</label>';
                                echo'</p>';
                                
                        }else{                        
                        
                                $field_status = (in_array($erole['name'],$checked)) ? 'checked' : '';

                                echo'<p>';                        
                                echo '<input type="checkbox" name="' . esc_attr($field) . '[]" value="' . esc_attr($erole['name']) . '" ' . $field_status . '/>';
                                echo '<label>' . esc_html($erole['name']) . '</label>';
                                echo'</p>';
                                
                        }
                        
                }
                
        }        
        
}

        
/**
 * Check status
 * 
 * @global type $glb_prguide_status
 * @param type $object
 * @return boolean
 */
function prguide_check_status($object=false){     
        
        global $glb_prguide_status;        
        
        if (isset($glb_prguide_status[$object]) && $glb_prguide_status[$object] === true){
                return true;
        }
        
        return false;
}


/**
 *  Check navigation status
 * 
 * @return type
 */
function prguide_check_status_nav(){     
        return prguide_check_status('nav');
}


/**
 * Check user capability
 * 
 * @return boolean
 */
function prguide_user_can_setup(){
        
        if (current_user_can(PRGUIDE_EDIT_CAPABILITY)){
                return true;
        }
        
        return false;        
        
}


/**
 *  Get section content elements by post_type
 * 
 * @param type $post_type
 * @return string|boolean
 */
function prguide_get_options_by_post_type($post_type=PRGUIDE_POST_TYPE_ID){
        
        if (!$post_type){
                return false;
        }
        
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'numberposts' => -1,
            'posts_per_page' => -1
        );
        $option_values = '';
        $the_query = new WP_Query( $args );         
        if ( $the_query->have_posts() ) {
                while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $option_values  .=  '<option value="'.get_the_ID().'">'.get_the_title().'</option>';                
            }
        }        
        wp_reset_postdata();
        
        return $option_values;
        
}


/*
 * Register options page plugin settings
 */
function prguide_register_plugin_settings() {
        
	register_setting(PRGUIDE_ADMIN_NAV_PAGE_ID, 'prguide_nav_jsondata');	
        register_setting(PRGUIDE_POST_TYPE_ID, 'prguide_config');	
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_nav_numbering','prguide_settings_validate_checkbox');	      
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_nav_group_lines','prguide_settings_validate_checkbox');	      
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_nav_on_click','prguide_settings_validate_checkbox');	        
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_guide_title');	        
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_debug','prguide_settings_validate_checkbox');
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_guide_disabled','prguide_settings_validate_checkbox');        
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_guide_menu_title');
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_guide_access_roles');          
        register_setting(PRGUIDE_ADMIN_SETTINGS_PAGE_ID, 'opt_debug','prguide_settings_validate_checkbox');	
        
        
        return;
}
add_action( 'admin_init', 'prguide_register_plugin_settings' );

 
/*
 * Register options page plugin settings
 */
function prguide_settings_validate_checkbox($input) {
        
        $input = ($input == '1') ? '1' : '0';    
        return $input;
    
}

/**
 * Print Debug informations
 * 
 * @return type
 */
function prguide_debug_informations(){
        
        if (!prguide_user_can_setup()) {
                return;                
        }        
        
        global $glb_prguide_data;
        
        $debug_informations = array();
        
        if (defined(PRGUIDE_VERSION)){
                $debug_informations['prguideversion'] = PRGUIDE_VERSION; 
        }
        
        if (function_exists('get_bloginfo')) {
                $debug_informations['wpversion'] =  get_bloginfo('version');
                $debug_informations['wpcharset'] =  get_bloginfo('charset');
                $debug_informations['wphtml_type'] =  get_bloginfo('html_type');                
        }        

        if (function_exists('wp_get_theme')) {                
                $debug_informations['theme'] = wp_get_theme();            
        }
        
        if (function_exists('get_option')) {
                $debug_informations['plugins'] =  get_option('active_plugins', array());
        }
        
        if (function_exists('get_option')) {
                $debug_informations['phpversion'] =  phpversion();
        }
        
       	if (function_exists('apache_get_version')) {
		 $debug_informations['apacheversion'] =  apache_get_version();
	} 
    
        if (function_exists('getMySqlVersion')) {
                $debug_informations['mxsqlversion'] = getMySqlVersion();
	}      
        
        echo '<pre>';
        echo '<code>';
        print_r($debug_informations);        
        echo '</code>';
        echo '</pre>';
        
        echo '<code>';
        echo htmlspecialchars($glb_prguide_data['json']);       
        echo '</code>';
        
        return;
    
}

/**
 * Generate navigation list element items
 * 
 * @global type $glb_prguide_nav_sections
 * @param type $items
 * @param type $first
 * @param string $loop_counter_prev
 * @param string $loop_counter_prev_prev
 * @param string $loop_counter_prev_prev_prev
 * @return string|boolean
 */
function prguide_generate_nav_li_items($items=array(),$first=true,$loop_counter_prev='',$loop_counter_prev_prev='',$loop_counter_prev_prev_prev=''){
        
        global $glb_prguide_nav_sections;        
        $output = '';
        $loop_counter = 1;       
        
        if (is_array($items) && count($items) > 0) {
                foreach ($items as $nav_element) {  
                        $nav_element_id = $nav_element['id'];
                                                       
                        $prev_1 = ($loop_counter_prev_prev_prev) ? $loop_counter_prev_prev_prev . '.' : '';      
                        $prev_2 = ($loop_counter_prev_prev) ? $loop_counter_prev_prev . '.' : '';      
                        $prev_3 = ($loop_counter_prev) ? $loop_counter_prev . '.' : '';                              
                        $numeration = (get_option('opt_nav_numbering')) ? $prev_1 . $prev_2 .  $prev_3  . $loop_counter . '.' : '';        
                        $has_children = (isset($nav_element['children'])) ? true : false;        
                        $has_children_class = ($has_children) ? 'pg-topic-has-children' : '';
                        $expand_all_trigger = ($has_children && $first === true) ? '<span>+</span>' : '';                        
                        $first_selector_class = ($first) ? 'pg-nav-first-element' : '';                                
                        $nav_element['numeration'] = $numeration;          
                        $nav_element_no_children = $nav_element;
                        unset($nav_element_no_children['children']);
                        $glb_prguide_nav_sections[$nav_element_id] = $nav_element_no_children; 
                        $numeration_id = trim(str_replace(".","",$nav_element['numeration']));
                        $output .= '<li class="pg-topic ' . $has_children_class .' ' . $first_selector_class .'">';                             
                        $output .= '<a href="#section-' . esc_attr($numeration_id) . '">' . $numeration .  ' ' . esc_html($nav_element['title']) . '</a>' . $expand_all_trigger;
                        if ($has_children) {                                                              
                                $output .= '<ul class="pg-main-nav-submenu">';                 
                                $output .= prguide_generate_nav_li_items($nav_element['children'],false,$loop_counter,$loop_counter_prev,$loop_counter_prev_prev);
                                $output .= '</ul>';
                        }
                        $output .= '</li>';
                        $loop_counter++;
                        
                        if ($first === true){                                                                
                                $loop_counter_prev = '';
                                $loop_counter_prev_prev = '';       
                                $loop_counter_prev_prev_prev = '';
                        }                        
                        
                }
                return $output;
        }

        return false;
}


/**
 * Generate Section
 * 
 * @global type $glb_prguide_nav_sections
 * @param type $items
 * @return boolean
 */
function prguide_generate_section_content($items=array()){
        
        global $glb_prguide_nav_sections;

        if (is_array($glb_prguide_nav_sections) && count ($glb_prguide_nav_sections) > 0){
                foreach($glb_prguide_nav_sections as $section){                                                               
                        $content_post_id = $section['customSelect'];
                        $new_chapter = ($content_post_id < 0) ? true : false;                                                
                        $numeration = (get_option('opt_nav_numbering')) ? $section['numeration'] . ' ' : '';                                                                        
                        $numeration_id =  'section-' . trim(str_replace(".","",$numeration));
                        $section_chapter = (isset($section['chapter'])) ? $section['chapter'] : '';                        
                        if ($new_chapter === false){                                        
                                $section_title = get_the_title($content_post_id);
                                settype($content_post_id,"integer");
                                $post_content = get_post($content_post_id);
                                $content = $post_content->post_content;                                
                                $content =  apply_filters('the_content',$content);
                                $content = do_shortcode( $content );                         
                        }else{                       
                                $section_title = $section['title'];                                
                        }
                        ?>       
                        <section>
                        <div id="<?php echo $numeration_id; ?>" class="pg-section-wrapper">
                                <div class="pg-section">
                                        <header>
                                                <h2 class="pg-section-title <?php  if ($new_chapter === true){ ?>pg-chapter-title<?php }else{ ?>topic-title<?php }?>"><?php echo $numeration; ?><?php esc_html_e($section_chapter); ?> <?php esc_html_e($section_title); ?></h2>
                                        </header>                                        
                                        <div class="pg-deep-link pg-hidden"><?php echo prguide_get_section_deep_link($numeration_id) ?></div>
                                        <?php  if ($new_chapter === false){ ?>
                                        <div class="pg-section-content">
                                                <p><?php echo $content; ?></p>
                                        </div>                                
                                        <div class="pg-section-bottom">                                              
                                                &raquo; <a class="pg-back-to-top" href="#top"><?php  _e('back to top','project-guide'); ?></a>
                                                <?php if ($new_chapter === false  && prguide_user_can_setup()){ ?>
                                                 <a href="<?php echo prguide_get_admin_url_edit_item($content_post_id) ?>" class="button alignright"><?php  _e('edit content','project-guide'); ?></a>
                                                <?php } ?>
                                        </div>                                
                                        <?php } ?>
                                </div>                                        
                        </div>
                        </section>

                      <?php
                }                                               
                return true;
        }           
        
        return false;
}

/**
 * Print admin page content
 * 
 */
function prguide_admin_dashboard_page_content(){              
        
        if (!prguide_current_user_can_view()){
                return false;
        }
        
        global $glb_prguide_data;
        
        $activate_sub_click_class = (get_option('opt_nav_on_click')) ? 'sub-onclick' : '';                                        
        $activate_group_lines_class = (get_option('opt_nav_group_lines')) ? 'pb-group-lines' : '';                                                
        
        $guide_title = get_option('opt_guide_title');
        $guide_title = ($guide_title ? $guide_title : __('Project Guide','project-guide'));
        
        ?>
        <div id="projectguide" class="pg-wrapper pg-cf">                                
                
                <div class="pg-inner-wrapper pg-cf">                         
                        <?php if (!prguide_check_status_nav()) {  ?>                        
                                <div class="prguide-notice">
                                        <header>
                                                <h1><?php esc_html_e($guide_title);?></h1>
                                        </header>                                           
                                        <p><?php _e('It looks like nothing has been setup yet!', 'project-guide'); ?></p>
                                        <?php if (prguide_user_can_setup()){ ?>
                                                <ul>
                                                        <li>&raquo; <a href="<?php echo prguide_get_admin_url_add_item(); ?>"><?php _e('Create section content', 'project-guide'); ?></a></li>
                                                        <li>&raquo; <a href="<?php menu_page_url(PRGUIDE_ADMIN_NAV_PAGE_ID, true); ?>"><?php _e('Project Guide Builder', 'project-guide'); ?></a></li>
                                                </ul>                                        
                                        <?php } ?>                                
                                </div>
                        <?php }else{ ?>                                
                                <div id="pg-nav">                        
                                        <div class="pg-sticky">                                                                    
                                                
                                                <?php if (prguide_user_can_setup()){ ?>
                                                        <a href="<?php menu_page_url(PRGUIDE_ADMIN_NAV_PAGE_ID, true); ?>" class="button"><?php _e('Edit Guide', 'project-guide'); ?></a>
                                                <?php } ?>                                
                                                
                                                <?php if (prguide_check_status_nav()){ ?>                                                 
                                                <nav>
                                                        <ul class="pg-main-nav <?php echo $activate_sub_click_class; ?> <?php echo $activate_group_lines_class; ?>">                                                                                        
                                                        <?php  echo prguide_generate_nav_li_items($glb_prguide_data['array']); ?>                                        
                                                        </ul>
                                                </nav>
                                                <?php }?>
                                        </div>   
                                </div>                         
                                <div id="pg-content">                                                 
                                        <?php if (prguide_check_status_nav()){ ?>                                                                     
                                                <header>
                                                        <h1><?php esc_html_e($guide_title);?></h1>
                                                </header>                                                                                                                                                                            
                                                <?php prguide_generate_section_content(); ?>
                                                <a class="button pg-top-nav" href="#projectguide"><?php  _e('Top','project-guide'); ?></a>                                                                 
                                                <div class="pg-footer"><?php _e('Created with','project-guide'); ?>  <a href="https://de.wordpress.org/plugins/project-guide/" title="Project Guide" target="_blank"><?php _e('Project Guide Plugin','project-guide'); ?></a> <?php _e('Version: ','project-guide'); ?>  <?php echo PRGUIDE_VERSION; ?> </div>
                                        <?php }?>    
                                </div>                         
                        <?php }?>                                    
                </div>                
        </div>

        <?php
}


/**
 * Project Guide Builder
 * 
 */
function prguide_admin_builder(){        
        
        if (!prguide_user_can_setup()) {
                return;                
        }
        
        global $glb_prguide_data;
        
        ?>      
        <div id="projectguide" class="pg-wrapper">
        <div class="pg-inner-wrapper pg-cf pg-option-nav">                         
                        <h1 class="pg-headline"><?php _e('Project Guide Builder', 'project-guide'); ?></h1>                             
                        <div class="dd pg-cf" id="domenu-0">
                                <button class="dd-new-item">+</button>
                                <li class="dd-item-blueprint">
                                        <button class="collapse" data-action="collapse" type="button" style="display: none;">–</button>
                                        <button class="expand" data-action="expand" type="button" style="display: none;">+</button>
                                         <div class="dd-handle dd3-handle"><?php _e('Drag', 'project-guide'); ?></div>
                                        <div class="dd3-content">
                                                <span class="item-name">[item_name]</span>
                                                <div class="dd-button-container">
                                                        <button class="item-add">+</button>
                                                        <button class="item-remove" data-confirm-class="item-remove-confirm">&times;</button>
                                                </div>
                                                <div class="dd-edit-box" style="display: none;"> 
                                                        <input type="text" name="title" autocomplete="off" placeholder="Item"
                                                               data-placeholder=""
                                                               data-default-value="<?php _e('New Content', 'project-guide'); ?> {?numeric.increment}">
                                                        <select name="customSelect">
                                                                <option value="-1"><?php _e('Chapter headline without content', 'project-guide'); ?></option>
                                                                <optgroup label="<?php _e('Add content from project guide', 'project-guide'); ?>">
                                                                        <?php echo prguide_get_options_by_post_type(); ?>
                                                                </optgroup>                                                            
                                                                <optgroup label="<?php _e('Add content from page', 'project-guide'); ?>">
                                                                        <?php echo prguide_get_options_by_post_type('page'); ?>
                                                                </optgroup>
                                                                <optgroup label="<?php _e('Add content from post', 'project-guide'); ?>">
                                                                        <?php echo prguide_get_options_by_post_type('post'); ?>
                                                                </optgroup>
                                                        </select>        
                                                        <i class="end-edit">                                                              
                                                                <button class="item-savedata">+</button>
                                                        </i>                                                  
                                                </div>
                                        </div>
                                </li>
                                <ol class="dd-list"></ol>
                        </div>
                        
                        <?php if (isset($_REQUEST['settings-updated']) && prguide_check_status_nav()){ ?>                        
                                <p class="prguide-show-button"><a href="<?php menu_page_url(PRGUIDE_ADMIN_PAGE_ID, true); ?>" class="button action aligncenter"><?php _e('View Guide', 'project-guide'); ?></a><p>
                        <?php }?>                        
                        
                        <div class="prguide-submit-nav-form">
                                <form method="post" action="options.php">                                
                                        <?php settings_fields(PRGUIDE_ADMIN_NAV_PAGE_ID); ?>
                                        <?php do_settings_sections(PRGUIDE_ADMIN_NAV_PAGE_ID); ?>                                
                                        <textarea name="prguide_nav_jsondata" class="prguide-nav-jsondata <?php echo (get_option('opt_debug')) ? 'prguide-debug' : ''; ?>"><?php echo sanitize_text_field($glb_prguide_data['json']); ?></textarea>                                                                                                   
                                        <?php submit_button(); ?>                                 
                                </form>
                        </div>
                </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
                var $domenu = $('#domenu-0');
                domenu = $('#domenu-0').domenu();
                var json_data =  <?php  echo json_encode($glb_prguide_data['json']); ?>;
                $('#domenu-0').domenu({
                data: json_data,
                maxDepth: 4, 
                }).parseJson()
                .on(['onItemCollapsed', 'onItemExpanded', 'onItemAdded', 'onSaveEditBoxInput', 'onItemDrop', 'onItemDrag', 'onItemRemoved', 'onItemEndEdit'], function(a, b, c) {
                       $('textarea[name=prguide_nav_jsondata]').val(domenu.toJson());
                       $('.prguide-show-button').hide();
              });
        });
        </script>
        
       <?php        
} 



/**
 *  Admin Settings
 * 
 * @return type
 */
function prguide_admin_settings(){     
        
        if (!prguide_user_can_setup()) {
                return;                
        }                
                
        
        if (isset($_GET['settings-updated'])) {            
                add_settings_error('prguide_settings_messages', 'prguide_settings_messages', __('Settings Saved', 'project-guide'), 'updated');
        } 
        
        settings_errors('prguide_settings_messages');
        
        $perm_access_roles = get_option('opt_guide_access_roles');
        
        ?>
        <div id="projectguide" class="pg-wrapper">
                <div class="pg-inner-wrapper pg-cf pg-option-nav">                         
                        <h1 class="pg-headline"><?php _e('Project Guide Settings', 'project-guide'); ?></h1>          
                        <form method="post" action="options.php">                                
                                <?php settings_fields(PRGUIDE_ADMIN_SETTINGS_PAGE_ID); ?>
                                <?php do_settings_sections(PRGUIDE_ADMIN_SETTINGS_PAGE_ID); ?>           
                                <div class="prguide-settings-section">                                
                                        <h2 class="pg-headline"><?php _e('Guide', 'project-guide'); ?></h2>          
                                        <table class="form-table">
                                                <tbody>
                                                        <tr>
                                                                <th scope="row"><label for="opt_guide_disabled"><?php _e('Disable', 'project-guide'); ?></label></th>
                                                                <td><input type="checkbox" name="opt_guide_disabled"value="1" <?php echo (get_option('opt_guide_disabled')=='1') ? 'checked' : ''; ?> />
                                                                <label class="sublabel"><?php _e('Disable guide menu.', 'project-guide'); ?></label></td></td>
                                                        </tr>
                                                        <tr>
                                                                <th scope="row"><label for="opt_guide_title"><?php _e('Permissions', 'project-guide'); ?></label></th>
                                                                <td><?php prguide_role_editable_chechboxes('opt_guide_access_roles',$perm_access_roles); ?>                 
                                                                <label class="sublabel"><?php _e('Grant roles access to the guide.', 'project-guide'); ?></label></td></td>
                                                        </tr>                                                               
                                                        <tr>
                                                                <th scope="row"><label for="opt_guide_title"><?php _e('Headline', 'project-guide'); ?></label></th>
                                                                <td><input type="text" name="opt_guide_title" value="<?php echo sanitize_text_field(get_option('opt_guide_title')); ?>" />
                                                                <label class="sublabel"><?php _e('Individual guide headline.', 'project-guide'); ?></label></td></td>
                                                        </tr>
                                                        <tr>
                                                                <th scope="row"><label for="opt_guide_menu_title"><?php _e('Menu title', 'project-guide'); ?></label></th>
                                                                <td><input type="text" name="opt_guide_menu_title" value="<?php echo sanitize_text_field(get_option('opt_guide_menu_title')); ?>" />
                                                                <label class="sublabel"><?php _e('Individual dashboard menu title.', 'project-guide'); ?></label></td>
                                                        </tr>                                                        
                                                </tbody>
                                        </table>
                                </div>                                     
                                <div class="prguide-settings-section">                                
                                        <h2 class="pg-headline"><?php _e('Navigation', 'project-guide'); ?></h2>          
                                        <table class="form-table">
                                                <tbody>
                                                        <tr>
                                                                <th scope="row"><label for="opt_nav_numbering"><?php _e('Chapter numbering', 'project-guide'); ?></label></th>
                                                                <td><input type="checkbox" value="1" name="opt_nav_numbering" <?php echo (get_option('opt_nav_numbering')) ? ' checked' : ''; ?> />
                                                                <label class="sublabel"><?php _e('Enable number prefix.', 'project-guide'); ?></label></td>
                                                        </tr>
                                                        <tr>
                                                                <th scope="row"><label for="opt_nav_group_lines"><?php _e('Group lines', 'project-guide'); ?></label></th>
                                                                <td><input type="checkbox" value="1" name="opt_nav_group_lines" <?php echo (get_option('opt_nav_group_lines')) ? ' checked' : ''; ?> />
                                                                <label class="sublabel"><?php _e('Enable navigation group lines.', 'project-guide'); ?></label></td>
                                                        </tr>
                                                        <tr>
                                                                <th scope="row"><label for="opt_nav_on_click"><?php _e('Open on click', 'project-guide'); ?></label></th>
                                                                <td><input type="checkbox" value="1" name="opt_nav_on_click" <?php echo (get_option('opt_nav_on_click')) ? ' checked' : ''; ?> />
                                                                <label class="sublabel"><?php _e('Show subnavigation on click.', 'project-guide'); ?></label></td>
                                                        </tr>

                                                </tbody>
                                        </table>
                                </div>                                   
                                 <div class="prguide-settings-section">                                
                                        <h2 class="pg-headline"><?php _e('Admin', 'project-guide'); ?></h2>          
                                        <table class="form-table">
                                                <tbody>
                                                        <tr>
                                                                <th scope="row"><label for="opt_debug"><?php _e('Debug', 'project-guide'); ?></label></th>
                                                                <td><input type="checkbox" value="1" name="opt_debug" <?php echo (get_option('opt_debug')) ? ' checked' : ''; ?> />
                                                                <label class="sublabel"><?php _e('Show debugging information.', 'project-guide'); ?></label></td>
                                                        </tr>
                                                </tbody>
                                        </table>                                        
                                        <?php  
                                        if (get_option('opt_debug')){ 
                                                prguide_debug_informations();                                                 
                                        } 
                                        ?>                                        
                                </div>                                   
                                <?php submit_button(); ?>                                 
                        </form>
                </div>
        </div>
        
        <?php
        
}

?>