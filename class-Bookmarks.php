<?php
 
class Bookmarkpress {

	protected $version;

	public function __construct() {
		add_action( 'init', array($this,'init_early'), 0 );
		add_action( 'save_post', array($this, "save_post")   );
		add_filter( 'the_content', array($this, "the_content")  );
		add_action( 'wp_ajax_nopriv_bookmarkpress_visit', array($this, 'bookmarkpress_visit') );
		add_action( 'wp_ajax_bookmarkpress_visit', array($this, 'bookmarkpress_visit') );
		add_action( 'wp_ajax_nopriv_bookmarkpress_like', array($this, 'bookmarkpress_like') );
		add_action( 'wp_ajax_bookmarkpress_like', array($this, 'bookmarkpress_like') );
		add_action( 'wp_enqueue_scripts', array($this,'display_javascript') );
	}
	
	function display_javascript($hook) {
	
		wp_register_style( 'bookmarkpress_css', plugins_url('/css/bookmarkpress.css', __FILE__) );
		wp_enqueue_style( 'bookmarkpress_css' );
		
		wp_enqueue_script( 'bookmarkpress', plugins_url('/js/bookmarkpress.js', __FILE__), array('jquery'));		
		wp_localize_script( 'bookmarkpress', 'bookmarkpress', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'bookmarkpress_nonce' ) ) );
	
	}
	
	function bookmarkpress_visit(){
	
		if(wp_verify_nonce($_REQUEST['nonce'], 'bookmarkpress_nonce')){
	
			$visits = get_post_meta( $_POST['post'], '_bookmark_visits', true );
			update_post_meta( $_POST['post'], '_bookmark_visits', $visits+1);
			echo $visits+1;
		
		}
		
		die();
	
	}
	
	function bookmarkpress_like(){
	
		if(wp_verify_nonce($_REQUEST['nonce'], 'bookmarkpress_nonce')){
	
			$likes = get_post_meta( $_POST['post'], '_bookmark_likes', true );
			update_post_meta( $_POST['post'], '_bookmark_likes', $likes+1);
			echo $likes+1;
			
		}
		
		die();
	
	}
	
	public function init_early() {
		$labels = array(
			'name' => __( 'Bookmarks', 'bookmarkpress' ),
			'singular_name' => __( 'Bookmark', 'bookmarkpress' ),
			'add_new' => __( 'Add New', 'bookmarkpress' ),
			'add_new_item' => __( 'Add New Bookmark', 'bookmarkpress' ),
			'edit_item' => __( 'Edit Bookmark', 'bookmarkpress' ),
			'new_item' => __( 'New Bookmark', 'bookmarkpress' ),
			'view_item' => __( 'View Bookmark', 'bookmarkpress' ),
			'search_items' => __( 'Search Bookmarks', 'bookmarkpress' ),
			'not_found' => __( 'No bookmarks found.', 'bookmarkpress' ),
			'not_found_in_trash' => __( 'No bookmarks found in Trash.', 'bookmarkpress' ),
			'parent_item_colon' => __( 'Parent Bookmark:', 'bookmarkpress' ),
		);
		$args = array( 
			'can_export' => true, 
			'description' => __( 'Bookmarks are a links to other web pages, with a description.', 'bookmarkpress' ), 
			'has_archive' => false, 
			'hierarchical' => false,
			'labels' => $labels, 
			'public' => true,
			'publicly_queryable' => true,
			'supports' => array( 'title', 'editor', 'comments'), 
		 );
		register_post_type( 'bookmarkpress', $args );
		
		global $wp_rewrite;

		$wp_rewrite->flush_rules();
		
	}
	
	public function save_post( $post_id ) {
	
		apply_filters("bookmarkpress_before_save", $_POST);
	
		$url = @ $_POST[ 'bookmarkpress' ];
		
		if ( $url ){
		
			update_post_meta( $post_id, '_bookmark_url', esc_url( $url ) );
	
		}
			
		do_action("bookmarkpress_extra_fields_save", $post_id);
		
	}

	public function the_content( $content ) {
		
		global $post;
	
		$post = get_post( get_the_ID() );
		if ( 'bookmarkpress' != $post->post_type )
			return $content;

		$bookmark_url = get_post_meta( $post->ID, '_bookmark_url', true );
		
		if ( ! $bookmark_url )
			return $content;

		$plays = get_post_meta( $post->ID, '_bookmark_views', true );
		update_post_meta( $post->ID, '_bookmark_views', $plays+1);

		$iframe = "<iframe style='width:100%; height:600px;' src='" . esc_attr( $bookmark_url ) . "'></iframe>";

		$stats = "<p>Views : " . ($plays+1) . " | Visits : <span class='cursor' id='bookmarkpress_visits'>" . get_post_meta( $post->ID, '_bookmark_visits', true ) . "</span> | Likes : <span class='cursor' id='bookmarkpress_like'>" . get_post_meta( $post->ID, '_bookmark_likes', true ) . "</span><span class='cursor' id='bookmarkpress_like_button' onclick='javascript:bookmarkpress_like(" . $post->ID . ");'> | Like this resource </span></p>";
		
		$bookmark = "<p class='bookmarkpress-url'><span>URL (click to visit)</span> <a id='bookmarkpress_visit_link' onclick='javascript:bookmarkpress_visit(" . $post->ID . ");' target='new' href='" . esc_attr( $bookmark_url ) . "' class='bookmarkpress-bookmark-url'>" . esc_html( $bookmark_url ) . "</a></p>";

		$output = $iframe . $stats . '<div>' . $content . $bookmark . '</div>';

		$output = apply_filters("bookmarkpress_extra_fields_display", $output);

		return $output;
		
	}

}

$bookmarkpress = new Bookmarkpress();

?>