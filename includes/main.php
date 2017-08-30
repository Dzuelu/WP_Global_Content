<?php

// Adds additional admin options to our custom post-type menu
require_once GC_PLUGIN_DIR . '/includes/admin.php';

// Our meta box builder for our custom post type 
require_once GC_PLUGIN_DIR . '/includes/block_meta_box.php';

add_action( 'plugins_loaded', 'gc_plugins_loaded' );
// Register shortcodes where we return the content
function gc_plugins_loaded() {
	add_shortcode( 'global-content', 'gc_shortcode_content' );
	add_shortcode( 'global-content-title', 'gc_shortcode_title' );
	add_shortcode( 'global-content-rand', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-0', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-1', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-2', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-3', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-4', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-5', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-6', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-7', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-8', 'gc_shortcode_random' );
	add_shortcode( 'global-content-rand-9', 'gc_shortcode_random' );
}

// Gets a block of data that has been saved in a custom post type
function gc_shortcode_content( $atts ) {
	$parms = shortcode_atts( array(
        'id' => 0,
        'cache_id' => 0,
    ), $atts );
    
    if( empty( $parms['id'] ) )
    	return '';//No id given, cant get post
    
    $allow_page_cache = get_post_meta( $parms['id'], 'allow_page_cache', true );
    if( $allow_page_cache == 'checked' ) {
    	// We were allowed to cache the page, find it if it exists
    	$args = array(
 		   'post_type'  => 'global_cached_block',
		    'meta_query' => array(
        		array(
					'key'     => 'cached_post_id',//Get's the specific post's cache
					'value'   => $parms['id'],),
		        array(
					'key'     => 'cached_id',//Get's a specific cache, default is 0
					'value'   => $parms['cache_id'],),),);
		$query = new WP_Query( $args );
		/* Restore original Post Data */
		//wp_reset_postdata();
		
		if( $query->found_posts > 0 ) {
			return $query->posts[0]->post_content;
		}
    	
    	//TODO Cache doens't exist, create it with the cache_id
    	// Get post data
    	$block = get_post( $parms['id'] );
    	// Setup content
    	$content = do_shortcode( $block->post_content );
    	// Save cache post
    	wp_insert_post(array(
    		'post_title'   => $block->post_title,
    		'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'  => 'global_cached_block',
			'post_author'  => $block->post_author,
			'meta_input'   => array(
				'cached_post_id' => $parms['id'],
				'cached_id' => $parms['cache_id'],),
			'filter' => true,// Tricks wp to not sanitize the post so we don't break our cache
			));
		//Increment saved posts count
		update_post_meta( $block->ID, 'cached_count', get_post_meta( $post->ID, 'cached_count', true ) + 1 );
		return $content;
	}
    
    //Not allowed to cache page, just get the content
    $block = get_post( $parms['id'] );
    // Block data can contain shortcodes so we need to check for them
    return do_shortcode( $block->post_content );
}

// 
function gc_shortcode_random( $atts, $content = "" ) {
	$parms = shortcode_atts( array(
        'separator' => '|',//set seperator if not set
        'num' => 1,// If set, returns this many non-repeating from list elements
        'min' => -1,// If this and max set, returns a random ammount between with non-repeating elements
        'max' => -1,// If this is set, returns a random ammount between with non-repeating elements
    ), $atts );
    
    if( empty( $content ) && strpos($content, $parms['separator']) !== false ) {
	    // Empty or doesn't contiain separator
    	return '';
    }
    
    // Split the data by seperator
    $rand_data_array = explode($parms['separator'], $content);
    // The number of elements to return
    $range = $parms['num'];
    
    if( $parms['max'] > 0 ) {
    	// Return a range of random data defined by $parms['max'] and $parms['min']
    	$range = rand( ( $parms['min'] != -1 ? $parms['min'] : 0 ), $parms['max'] );
	}
    	
    // Make sure that we have as many elements as range
    $range = min( $range, count( $rand_data_array ) - 1 );
    
    $content = '';
    shuffle( $rand_data_array );
    for ($i = 1; $i <= $range; $i++) {
    	$content .= do_shortcode( $rand_data_array[ $i ] );
    }
    return $content;
}

// Returns the title of the page that contains this shortcode
function gc_shortcode_title() {
	// This is called on the page that is 
	//using the shortcode so it get's it's title
	return get_the_title(get_the_ID());
}

add_action( 'init', 'GCG_init' );
// Registers our custom post type
function GCG_init() {
	
	// Register our main 
	register_post_type( 'global_block', array(
		'label'                 => __( 'Global Content', PluginSettings::text_domain() ),
		'description'           => __( 'Content that is consistant throughout the website', PluginSettings::text_domain() ),
		'labels'                => array(
			'name'                  => _x( 'Global Content Blocks', 'Post Type General Name', PluginSettings::text_domain() ),
			'singular_name'         => _x( 'Block', 'Post Type Singular Name', PluginSettings::text_domain() ),
			'menu_name'             => __( 'Global Content', PluginSettings::text_domain() ),
			'name_admin_bar'        => __( 'Global Content', PluginSettings::text_domain() ),
			//'parent_item_colon'     => __( 'Parent Block:', PluginSettings::text_domain() ),
			'all_items'             => __( 'All Blocks', PluginSettings::text_domain() ),
			'add_new_item'          => __( 'Add New Block', PluginSettings::text_domain() ),
			'add_new'               => __( 'Add New', PluginSettings::text_domain() ),
			'new_item'              => __( 'New Block', PluginSettings::text_domain() ),
			'edit_item'             => __( 'Edit Block', PluginSettings::text_domain() ),
			'update_item'           => __( 'Update Block', PluginSettings::text_domain() ),
			'view_item'             => __( 'View Block', PluginSettings::text_domain() ),
			'search_items'          => __( 'Search Blocks', PluginSettings::text_domain() ),
			'not_found'             => __( 'Not found', PluginSettings::text_domain() ),
			'not_found_in_trash'    => __( 'Not found in Trash', PluginSettings::text_domain() ),
			'items_list'            => __( 'Items list', PluginSettings::text_domain() ),
			'items_list_navigation' => __( 'Items list navigation', PluginSettings::text_domain() ),
			'filter_items_list'     => __( 'Filter items list', PluginSettings::text_domain() ),),
		'supports'              => array( 'title', 'editor' ),
		//'taxonomies'            => array( '', ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		//'menu_position'         => 5,
		//'menu_icon'             => 'dashicons-dashboard',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'page',
	));
	
	register_post_type( 'global_cached_block', array(
		'label'                 => __( 'Cached Global Content', PluginSettings::text_domain() ),
		'description'           => __( 'Global Content that has been cached for use later', PluginSettings::text_domain() ),
		'labels'                => array(
			'name'                  => _x( 'Cached Global Content', 'Post Type General Name', PluginSettings::text_domain() ),
			'singular_name'         => _x( 'Cached Global Content', 'Post Type Singular Name', PluginSettings::text_domain() ),
			'menu_name'             => __( 'Cache', PluginSettings::text_domain() ),
			'name_admin_bar'        => __( 'Cache', PluginSettings::text_domain() ),
			'parent_item_colon'     => __( 'Parent Cached Content:', PluginSettings::text_domain() ),
			'all_items'             => __( 'All Cached Content', PluginSettings::text_domain() ),
			//'add_new_item'          => __( 'Add New Car', PluginSettings::text_domain() ),
			//'add_new'               => __( 'Add New', PluginSettings::text_domain() ),
			//'new_item'              => __( 'New Car', PluginSettings::text_domain() ),
			//'edit_item'             => __( 'Edit Car', PluginSettings::text_domain() ),
			//'update_item'           => __( 'Update Car', PluginSettings::text_domain() ),
			'view_item'             => __( 'View Cached Content', PluginSettings::text_domain() ),
			'search_items'          => __( 'Search Cached Content', PluginSettings::text_domain() ),
			'not_found'             => __( 'Not found', PluginSettings::text_domain() ),
			'not_found_in_trash'    => __( 'Not found in Trash', PluginSettings::text_domain() ),
			'items_list'            => __( 'Items list', PluginSettings::text_domain() ),
			'items_list_navigation' => __( 'Items list navigation', PluginSettings::text_domain() ),
			'filter_items_list'     => __( 'Filter items list', PluginSettings::text_domain() ),),
		'supports'              => array( 'title', 'editor' ),
		//'taxonomies'            => array( '', ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => false,
		//'menu_position'         => 5,
		//'menu_icon'             => 'dashicons-dashboard',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'page',
		'capabilities' => array(
	   		'edit_post' => false,
	   		'read_post' => true,
	   		'delete_post' => true,
	   		'create_posts' => 'do_not_allow',),
	   	'map_meta_cap' => true,
	));
}

add_filter( 'manage_edit-global_block_columns', 'edit_global_block_columns' ) ;
//Custom colums for main global content page
function edit_global_block_columns() {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => 'Title',
		'global_shortcode' => 'Shortcode',
		'author' => 'Author',
		'allow_page_cache' => 'Allow Cached',
		'cached_count' => '# of Cached Pages',
		'date' => 'Date'
	);
	return $columns;
}

add_filter( 'manage_edit-global_cached_block_columns', 'edit_global_cached_block_columns' ) ;
//Custom colums for main global content page
function edit_global_cached_block_columns() {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => 'Title',
		'cached_post_id' => 'Cached Post ID',// The id of the post from which we cached from
		'cached_id' => 'Cached ID',// The custom id of the cache to be used with diffrent pages
		'date' => 'Date'
	);
	return $columns;
}

add_action( 'manage_global_block_posts_custom_column', 'manage_global_block_columns', 10, 2 );
add_action( 'manage_global_cached_block_posts_custom_column', 'manage_global_block_columns', 10, 2 );
// Filling custom columns with metadata, can be global_block's or global_cached_block's
function manage_global_block_columns( $column, $post_id ) {
	global $post;
	switch( $column ) {
		case 'allow_page_cache'://This is a debug option
			$column_var = get_post_meta( $post_id, 'allow_page_cache', true );
			
			if( empty( $column_var ) ) {
				echo 'Error: Not Set!';
			} else {
				echo $column_var;
			}
			break;
		case 'global_shortcode':
			$column_var = get_post_meta( $post_id, 'global_shortcode', true );
			
			if( empty( $column_var ) ) {
				echo 'Error: Not Set!';
			} else {
				echo $column_var;
			}
			break;
		case 'cached_count':
			$column_var = get_post_meta( $post_id, 'cached_count', true );
			
			if( empty( $column_var ) ) {
				echo '0';
			} else {
				echo $column_var;
			}
			break;
		case 'cached_post_id':
			$column_var = get_post_meta( $post_id, 'cached_post_id', true );
			
			if( empty( $column_var ) ) {
				echo 'Error: Not Set!';
			} else {
				echo $column_var;
			}
			break;
		case 'cached_id':
			$column_var = get_post_meta( $post_id, 'cached_id', true );
			
			if( empty( $column_var ) ) {
				echo '0';
			} else {
				echo $column_var;
			}
			break;
		default:
			break;
	}
}

add_action('wp_trash_post', 'cache_skip_trash');
// Disable trash for cache post types and just delete them
function cache_skip_trash($post_id) {
    if (get_post_type($post_id) == 'global_cached_block') {
        // Force delete
        wp_delete_post( $post_id, true );
    }
} 







