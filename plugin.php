<?php
/*
Plugin Name:	Tailored Shortcodes
Description:	Allows you to create shortcodes which output HTML.  This is useful for including forms or other blocks of custom HTML wherever you need them.
Version:		1.0.1
Author:			Tailored Media
Author URI:		http://www.tailoredmedia.com.au
*/

new TWS_Shortcodes();

class TWS_Shortcodes {
	public	$post_type		= 'tailored_shortcode';
	public	$meta_shortcode	= '_shortcode';
	public	$meta_markup	= '_markup';

	
	/**
	 *	Constructor
	 */
	function __construct() {
		// Global
		add_action('init', array($this,'load_custom_post_types'));
		
		// Public
		add_action('wp_loaded', array($this,'load_shortcodes'));
		
		// Admin
		add_action('add_meta_boxes', array($this,'add_meta_boxes'), 10, 2);
		add_action('save_post', array($this,'save_meta_boxes'));
	}
	
	
	/**
	 *	Shortcodes
	 */
	function load_shortcodes() {
		// Don't load them in admin area.
		if (is_admin()) return false;
		// Go through posts to load all shortcodes
		$posts = get_posts(array(
			'posts_per_page'	=> -1,
			'post_type'			=> $this->post_type,
		));
		$shortcodes = array();
		foreach ($posts as $post) {
			$shortcode = get_post_meta($post->ID, $this->meta_shortcode, true);
			if ($shortcode)	$shortcodes[$shortcode] = $post->ID;
		}
		// Now register shortcodes
		foreach ($shortcodes as $shortcode => $post_id) {
			add_shortcode( $shortcode, array($this,'handle_shortcode') );
		}
	}
	
	function handle_shortcode( $atts=false, $content='', $shortcode=false ) {
		// Load code for this shortcode
		$post = $this->get_form_by_shortcode($shortcode);
		$code = get_post_meta($post->ID, $this->meta_markup, true);
		if (!$code)		return false;
		// Output
		return $code;
	}
	
	
	// Helper to fetch the post that has the specified shortcode
	function get_form_by_shortcode( $shortcode='' ) {
		$args = array(
			'post_type'	=> $this->post_type,
			'meta_query'	=> array(
				array(
					'key'		=> $this->meta_shortcode,
					'value'		=> $shortcode,
					'compare'	=> '=',
				),
			),
		);
		$posts = get_posts($args);
		if (!$posts)	return false;
		return $posts[0];
	}
	
	
	/**
	 *	Meta Boxes
	 */
	function save_meta_boxes( $post_id=false ) {
		// Verify
		if (!wp_verify_nonce($_POST['save_shortcode'], 'save_shortcode'))		return $post_id;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )						return $post_id;
		// Handle data
		if ($_POST['shortcode_code'])	update_post_meta($post_id, $this->meta_shortcode,	$_POST['shortcode_code']);
		if ($_POST['shortcode_markup'])	update_post_meta($post_id, $this->meta_markup,	$_POST['shortcode_markup']);
	}
	
	function add_meta_boxes( $post_type, $post ) {
		add_meta_box('form-show', __('Shortcode'), array($this,'metabox_shortcode'), $this->post_type, 'normal', 'default' );
		add_meta_box('form-code', __('Markup/Code'), array($this,'metabox_code'), $this->post_type, 'normal', 'default' );
	}
	
	function metabox_shortcode( $post=false ) {
		$shortcode = get_post_meta($post->ID, $this->meta_shortcode, true);
		$shortcode_txt = ($shortcode) ? $shortcode : 'SHORTCODE';
		wp_nonce_field('save_shortcode', 'save_shortcode');
		?>

		<p>You can embed your code on any page using a shortcode.  Please specify your code below.  To use, add code like <code>[<?php echo $shortcode_txt; ?>]</code></p>
		<p><label><span>Shortcode:</span> <input type="text" name="shortcode_code" value="<?php echo esc_attr($shortcode); ?>" /> (Must be unique)</label></p>

		<?php

		// Check if shortcode is already registered in WordPress
		if ($shortcode) {
			global $shortcode_tags;
			if (array_key_exists($shortcode, $shortcode_tags)) {
				echo '<div class="notice notice-error"><p>Alert: Shortcode is already registered</p></div>';
			}
			//echo '<pre>'.print_r($shortcode_tags,true).'</pre>';
		}
		

        
	}
	
	function metabox_code( $post=false ) {
		$code = get_post_meta($post->ID, $this->meta_markup, true);
		?>

		<p><label>
			<span>Copy/paste your code or markup here:</span>
			<textarea name="shortcode_markup" id="tws-code" rows="100" style="width:100%; height:40em;"><?php echo esc_textarea($code); ?></textarea>
		</label></p>

		<?php

		$settings = wp_enqueue_code_editor(array(
			'type'		=> 'text/html',
			'htmlhint'	=> array(
				'space-tab-mixed-disabled'	=> false, //'space',
			),
		));
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "tws-code", %s ); } );',
				wp_json_encode( $settings )
			)
		);

		?>
		<style><!--
		body .CodeMirror-wrap { border:1px solid rgba(0,0,0,0.1); border-left:0; margin:1.5em 0; height:auto; }
		--></style>
		<?php
	}
	
	/**
	 *	Custom post type
	 */
	function load_custom_post_types() {
		register_post_type($this->post_type, array(
			'labels'		=> array(
				'name'			=> 'Tailored Shortcodes',
				'singular_name'	=> 'Tailored Shortcode',
				'add_new'		=> 'Add Shortcode',
				'add_new_item'	=> 'Add New Shortcode',
				'edit_item'		=> 'Edit Shortcode',
				'new_item'		=> 'New Shortcode',
			),
			'public'			=> false,
			'show_ui'			=> true,
			'capability_type'	=> 'page',
			'hierarchical'		=> false,
			'supports'		=> array(
				'title',
			),
			'menu_position'	=> 20,
		));
	}
	
}



?>