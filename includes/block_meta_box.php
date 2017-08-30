<?php

//
class Block_Meta_Box {
	
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}
	}

	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
		add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
	}

	public function add_metabox() {
		add_meta_box(
			'global_content_meta',// meta_id
			'Block Metadata',// meta_title
			array( $this, 'render_metabox' ),// meta_callback
			'global_block',// meta screen (post type)
			'advanced',// meta context
			'default'// meta priority
		);
	}
	
	private function get_shortcode( $post, $cached_count ) {
		//Generates the meta shortcode string that html likes
		//This shortcode id is the block post id
		return "[global-content id='" . $post->ID . "' cache_id='" . ($cached_count + 1) . "']";
	}
	
	public function render_metabox( $post ) {
		
		// Add nonce for security and authentication.
		wp_nonce_field( 'global_content_meta_nonce_action', 'global_content_meta_nonce' );
		
		// Get existing meta values
		//$var = get_post_meta( $post->ID, 'gcg_allow_page_cache', true );
		$cached_count = get_post_meta( $post->ID, 'cached_count', true );
		$allow_page_cache = get_post_meta( $post->ID, 'allow_page_cache', true );
		
		//Check values
		//if( empty( $var ) ) $var = 'default_value';
		if( empty( $cached_count ) ) $cached_count = 0;
		if( empty( $allow_page_cache ) ) $allow_page_cache = '';
				
		//Build form
		echo '<table class="form-table">';

		echo '	<tr>';
		echo '		<th><label for="gcg_shortcode" class="Shortcode_label"> Shortcode copy </label></th>';
		echo '		<td>';
		echo '			<input type="text" id="gcg_shortcode" name="gcg_shortcode_input" style="width:100%" value="' . Block_Meta_Box::get_shortcode( $post, $cached_count ) . '" readonly>';
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="gcg_cache_count" class="num_cached_blocks_label_title"> # of Cached Pages </label></th>';
		echo '		<td>';
		echo '			<label for="gcg_cache_count" class="num_cached_blocks_label_count"> ' . $cached_count . ' </label>';
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="gcg_allow_page_cache" class="car_cruise_control_label"> Allow Page Cache </label></th>';
		echo '		<td>';
		echo '			<input type="checkbox" id="allow_page_cache" name="allow_page_cache" class="allow_page_cache_field" ' . checked( $allow_page_cache, 'checked', false ) . '> ';
		echo '			<span class="description">' . 'Allows the caching of this content, on save will clear any cached data and repopulate on next page request.' . '</span>';
		echo '		</td>';
		echo '	</tr>';

		echo '</table>';
		
	}
	
	public function save_metabox( $post_id, $post ) {
		
		// Add nonce for security and authentication.
		$nonce_name   = $_POST['global_content_meta_nonce'];
		$nonce_action = 'global_content_meta_nonce_action';

		// Check if a nonce is set.
		if ( ! isset( $nonce_name ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;
		
		// Get user input
		//$textbox = isset( $_POST[ 'textbox_id' ] ) ? sanitize_text_field( $_POST[ 'textbox_id' ] ) : '';
		//$checkbox = isset( $_POST[ 'checkbox_id' ] ) ? 'checked' : '';
		$allow_page_cache = isset( $_POST[ 'allow_page_cache' ] ) ? 'checked' : 'not-checked';
		
		//Sets to 0 for first time saves
		$cached_count = get_post_meta( $post->ID, 'cached_count', true );
		if( empty( $cached_count ) ) $cached_count = 0;
		
		if( $allow_page_cache == 'checked' ) {
			// TODO: Delete all cached posts in post type global_cached_block because we updated the main post
			$args = array(
				'post_type'  => 'global_cached_block',
		    	'meta_query' => array(
        			array(
						'key'     => 'cached_post_id',//Get's the specific post's cache
						'value'   => $post->ID,),),);
			
			$query = new WP_Query( $args );
			/* Restore original Post Data */
			//wp_reset_postdata();
			
			foreach($query->posts as $post) {
				//This might hit the DB many times but I'm not seeing a good way to do it otherwise
				wp_delete_post( $post->ID, true );
			}
			
			//update_post_meta( $post_id, 'cached_count', 0 );
			$cached_count = 0;
		}
		
		//Update metadata
		//update_post_meta( $post_id, 'metadata_id', $var );
		update_post_meta( $post_id, 'global_shortcode', Block_Meta_Box::get_shortcode( $post, $cached_count ) );
		update_post_meta( $post_id, 'allow_page_cache', $allow_page_cache );
		update_post_meta( $post_id, 'cached_count', $cached_count );
		
	}
	
}

new Block_Meta_Box;

