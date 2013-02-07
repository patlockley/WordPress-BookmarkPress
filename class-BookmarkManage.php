<?php

class BookmarkManage {
	
	public function __construct() {	
		
		if ( is_admin() ) {
			add_action('admin_menu', array($this, 'menu_option'));
			add_action('admin_head', array($this, 'bookmark_manage_postform'));
			add_action('admin_enqueue_scripts', array($this,'display_styles') );
		}
		
	}
	
	function display_styles() {
	
		wp_register_style( 'bookmarkpress_css', plugins_url('/css/bookmarkpress.css', __FILE__) );
		wp_enqueue_style( 'bookmarkpress_css' );
		
	}
	
	function options_page() {

	  ?>
	  <div class="wrap">
		<h1>BookmarkPress Settings</h1>
		<div class="bookpressmanage">
		<h2>BookmarkPress Configuration</h2>
		<form method="post" action="">
			<p><?PHP
		
			wp_nonce_field('bookmarkpress_manage','bookmarkpress_manage');
			settings_fields( 'bookmarkpress' );
		
			$args = array(
							'child_of' => 0,
							'sort_order' => 'ASC',
							'sort_column' => 'post_title',
							'hierarchical' => 1,
							'parent' => -1,
							'offset' => 0,
							'post_type' => 'page',
							'post_status' => 'publish'
						); 
		
			$pages = get_pages( $args ); 
			
			?><label>Which page do you want to hold the site resource library?</label> <select name='bookmarkpress_page_library'><?PHP
			
			$selected_page = get_option("bookmarkpress_page_library");		

			foreach($pages as $page){
			
				echo "<option ";

				if($page->ID == $selected_page){
				
					echo " selected ";
				
				}

				echo " value=\"" . $page->ID . "\">" . $page->post_title . "</option>";
			
			}
			
			?></select>
			</p>
			<p><?PHP
			
				$args = array(
							'child_of' => 0,
							'sort_order' => 'ASC',
							'sort_column' => 'post_title',
							'hierarchical' => 1,
							'parent' => -1,
							'offset' => 0,
							'post_type' => 'page',
							'post_status' => 'publish'
						); 
		
				$pages = get_pages( $args ); 
			
				?><label>Which page do you want to hold the site Recent list?</label> <select name='bookmarkpress_page_recent'><?PHP
				
				$selected_page = get_option("bookmarkpress_page_recent");		

				foreach($pages as $page){
				
					echo "<option ";

					if($page->ID == $selected_page){
					
						echo " selected ";
					
					}

					echo " value=\"" . $page->ID . "\">" . $page->post_title . "</option>";
				
				}
			
			?></select>
			</p>
			<p><?PHP
			
				$args = array(
							'child_of' => 0,
							'sort_order' => 'ASC',
							'sort_column' => 'post_title',
							'hierarchical' => 1,
							'parent' => -1,
							'offset' => 0,
							'post_type' => 'page',
							'post_status' => 'publish'
						); 
		
				$pages = get_pages( $args ); 
			
				?><label>Which page do you want to hold the site Popular list?</label> <select name='bookmarkpress_page_popular'><?PHP
				
				$selected_page = get_option("bookmarkpress_page_popular");		

				foreach($pages as $page){
				
					echo "<option ";

					if($page->ID == $selected_page){
					
						echo " selected ";
					
					}

					echo " value=\"" . $page->ID . "\">" . $page->post_title . "</option>";
				
				}
			
			?></select>
			</p>
		</div>
		<div class="bookpressmanage">
			<h2>BookmarkPress Details</h2>
			<p>
				Bookmarklet Name<br/>
				<input type="text" name="bookmark_press_name" size="100" value="<?PHP echo get_option("bookmark_press_name"); ?>" />
			</p>
			<p>
				Bookmarklet Description<br/>
				<textarea name="bookmark_press_description" rows="5" cols="50"><?PHP echo get_option("bookmark_press_description"); ?></textarea>
			</p>
			<p>
				Bookmarks included in lists of posts<br/>
				<input type="checkbox" name="bookmark_press_include" <?PHP

				if(get_option("bookmark_press_include")){ echo " checked "; } ?>
				/> 
			</p>
		</div>
		<div class="bookpressmanage">
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
			</form>
		</div>
	</div>
	  <?php
	}
	
	function bookmark_manage_postform(){
	
		if (!empty($_POST['bookmarkpress_manage'])){

			if(!wp_verify_nonce($_POST['bookmarkpress_manage'],'bookmarkpress_manage') ){
			
				print 'Sorry, your nonce did not verify.';
				exit;
				
			}else{			
			
				if($_POST['option_page']=="bookmarkpress"){
					
					$pages = array($_POST['bookmarkpress_page_library'],$_POST["bookmarkpress_page_recent"],$_POST['bookmarkpress_page_popular']);
					
					$pages = array_filter(array_unique($pages));
					
					if(count($pages)===3){
					
						update_option("bookmarkpress_page_library",$_POST['bookmarkpress_page_library']);
						update_option("bookmarkpress_page_recent",$_POST["bookmarkpress_page_recent"]);
						update_option("bookmarkpress_page_popular",$_POST['bookmarkpress_page_popular']);
						update_option("bookmark_press_name",$_POST["bookmark_press_name"]);
						update_option("bookmark_press_description",trim($_POST["bookmark_press_description"]));
						if($_POST["bookmark_press_include"]==="on"){
							update_option("bookmark_press_include", true);
						}else{
							update_option("bookmark_press_include", false);
						}
						
					}else{
					
						$error = new WP_Error('unique_posts', __('Please use a different page for each post', 'bookmarkpress' ) );

						wp_die( $error->get_error_message(), __('Input Error', 'bookmarkpress') );
						
					}
					
				}
			
			}
		
		}
	
	}
	
	function menu_option() {
	
		add_options_page('BookmarkPress Menu Options', 'BookmarkPress Options', 'manage_options', 'bookmarkpress', array($this, 'options_page'));
		
	}

} 

$bookmarkManage = new BookmarkManage();

?>