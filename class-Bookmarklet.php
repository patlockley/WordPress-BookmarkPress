<?php

class Bookmarklet_creation {
	
	public function __construct() {	
		if ( is_admin() ) {
			add_action( 'wp_ajax_nopriv_bookmarkpress', array($this, 'redirect_to_login') );
			add_action( 'wp_ajax_nopriv_bookmarkpress_post', array($this, 'redirect_to_login') );
			add_action( 'wp_ajax_bookmarkpress', array($this, 'bookmarklet') );
			add_action( 'wp_ajax_bookmarkpress_post', array($this, 'handle_post') );
			add_action( 'tool_box', array($this, "tool_box") );
		}
	}
	
	public function redirect_to_login() {
		wp_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
		exit;
	}

	public function tool_box() {
		$vars = array();
		$vars[ 'bookmarklet_link' ] = $this->get_bookmarklet_link();
		?><div class="tool-box">
			<h3 class="title"><?PHP echo get_option("bookmark_press_name"); ?></h3>
			<p><?PHP echo get_option("bookmark_press_description"); ?></p>

			<p class="description"><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.', 'oerb') ?></p>
			<p class="pressthis bookmarkthis"><a onclick="return false;" oncontextmenu="if(window.navigator.userAgent.indexOf('WebKit')!=-1)jQuery('.bookmarkpressthis-code').show().find('textarea').focus().select();return false;" href="<?php echo htmlspecialchars( $this->get_bookmarklet_link() ); ?>" ><span><?PHP echo get_option("bookmark_press_name"); ?></span></a></p>
			<div class="bookmarkpressthis-code" style="display:none;">
			<p class="description"><?php _e('If your bookmarks toolbar is hidden: copy the code below, open your Bookmarks manager, create new bookmark, type Press This into the name field and paste the code into the URL field.', 'oerb') ?></p>
			<p><textarea rows="5" cols="120" readonly="readonly"><?php echo htmlspecialchars( $this->get_bookmarklet_link() ); ?></textarea></p>
			</div>
		</div><?PHP
	}

	public function bookmarklet() {
		
		require_once('templates-admin/bookmarklet-ui.php');
		exit;
	}

	public function handle_post() {
		// define some basic variables
		$quick = array();
		if ( isset( $_POST[ 'publish' ] ) && current_user_can( 'publish_posts' ) )
			$quick[ 'post_status' ] = 'publish';
		elseif ( isset( $_POST[ 'review' ] ) )
			$quick[ 'post_status' ] = 'pending';
		else
			$quick[ 'post_status' ] = 'draft';
		$quick[ 'post_category' ] = isset($_POST[ 'post_category' ]) ? $_POST[ 'post_category' ] : null;
		
		$quick[ 'post_type' ] = "bookmarkpress";
		$quick[ 'tax_input' ] = isset($_POST[ 'tax_input' ]) ? $_POST[ 'tax_input' ] : null;
		$quick[ 'post_title' ] = ( trim($_POST[ 'title' ]) != '' ) ? $_POST[ 'title' ] : '  ';
		$quick[ 'post_content' ] = isset($_POST[ 'content' ]) ? $_POST[ 'content' ] : '';

		$post_id = wp_insert_post($quick, true);
		if ( is_wp_error($post_id) )
			wp_die($post_id);
			
		add_post_meta( $post_id, "_bookmark_views", 0);
		add_post_meta( $post_id, "_bookmark_visits", 0);
		add_post_meta( $post_id, "_bookmark_likes", 0);

		$status = $quick[ 'post_status' ];

		$post = get_post( $post_id );

		$message = ( 'publish' == $status ) ? __( 'Your %s has been published.', 'oerb' ) : __( 'Your %s has been saved.', 'oerb' ) ;
		$post_type_object = get_post_type_object( $post->post_type );
		$message = sprintf( $message, strtolower( $post_type_object->labels->singular_name ) );
		require_once('templates-admin/publish-confirm.php');
		die();
	}

	protected function get_bookmarklet_link() {
		$link = "javascript:
				var n = document;";
				
		$link = apply_filters("bookmarkpress_properties_javascript", $link);		
			
	$link .= "var rootNode = n;
			
	while (n) {
	
		if(n.hasAttributes()){
				
			";

		$link = apply_filters("bookmarkpress_attributes_javascript", $link);			
			
		$link .= "
				
		}
		
		if(n.innerHTML!=''){
				
			";
			
		$link = apply_filters("bookmarkpress_innerhtml_javascript", $link);
			
			
		$link .= "
				
		}

		if (n.v) {
			n.v = false;
			if (n == rootNode) {
				break;
			}
			if (n.nextSibling) {
				n = n.nextSibling;
			} else {
				n = n.parentNode;
			}
		} else {
			if (n.firstChild) {
				n.v = true;
				n = n.firstChild;
			}else if (n.nextSibling) {
				n = n.nextSibling;
			}else {
				n = n.parentNode;
			}
		}
				
	}
	f='" . admin_url( 'admin-ajax.php' ) . "',
				l=n.location,
				w=window,
				e=encodeURIComponent,
				u=f+'?action=bookmarkpress&url='+e(l.href)+'&title='+e(n.title)";
					
			$link =	apply_filters("bookmarkpress_linkvariables_javascript", $link);
	
	$link .=";
				a=function(){if(!w.open(u,'t','toolbar=0,resizable=1,scrollbars=1,status=1,width=720,height=570'))l.href=u;};
				if (/Firefox/.test(navigator.userAgent)) setTimeout(a, 0); else a();
				void(0)";

		$link = str_replace(array("\r", "\n", "\t"),  '', $link);

		return $link;
	}

} 

$bookmarklet = new Bookmarklet_creation();

?>