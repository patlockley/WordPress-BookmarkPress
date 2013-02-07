<?php

class BookmarkLibrary {
	
	public function __construct() {	
		add_filter( 'the_content', array($this, "the_content")  );
		add_action( 'wp_ajax_nopriv_bookmarkpress_library_search', array($this, 'bookmarkpress_library_search') );
		add_action( 'wp_ajax_bookmarkpress_library_search', array($this, 'bookmarkpress_library_search') );
		add_action( 'wp_enqueue_scripts', array($this,'display_javascript') );
	}
	
	function display_javascript($hook) {
	
		wp_register_style( 'bookmarkpress_library_css', plugins_url('/css/bookmarkpress_library.css', __FILE__) );
		wp_enqueue_style( 'bookmarkpress_library_css' );
		
		wp_enqueue_script( 'bookmarkpress_library', plugins_url('/js/bookmarkpress_library.js', __FILE__), array('jquery'));		
		wp_localize_script( 'bookmarkpress_library', 'bookmarkpress_library', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'bookmarkpress_library_nonce' ) ) );
	
	}
	
	function bookmarkpress_library_search(){
	
		if(wp_verify_nonce($_REQUEST['nonce'], 'bookmarkpress_library_nonce')){
	
			global $wpdb;
		
			$table_name = $wpdb->prefix . "posts";
		
			switch($_REQUEST['extra']){
			
				case "date_desc" : $extra = " order by post_date DESC "; break;
				case "date_asc" : $extra = " order by post_date ASC "; break;
				case "title_desc" : $extra = " order by post_title DESC "; break;
				case "title_asc" : $extra = " order by post_title ASC "; break;
				
			}
		
			$results = $wpdb->query( 
				$wpdb->prepare( 
					"select id, post_title, post_date, guid, post_author FROM " . $table_name . "
					where post_type = 'bookmarkpress' 
					and post_title like '%s' " . $extra,
					"%". $_REQUEST['term'] . "%"
					)
			);
		
			if(count($wpdb->last_result)!==0){
			
				?><p style="font-size:80%">
					Date : <a onClick="bookmarkpress_library_search('date_desc')">Earliest</a> / <a onClick="bookmarkpress_library_search('date_asc')">Oldest</a> |
					Title : <a onClick="bookmarkpress_library_search('title_desc')">Z - A</a> / <a onClick="bookmarkpress_library_search('title_asc')">A - Z</a></p>
				<p style="font-size:80%">
					Likes : Most popular - <a onClick="bookmarkpress_library_search('pop_desc')">First</a> / <a onClick="bookmarkpress_library_search('pop_asc')">Last</a> |
					Visits : Most Visited - <a onClick="bookmarkpress_library_search('visits_desc')">First</a> / <a onClick="bookmarkpress_library_search('visits_asc')">Last</a> |
					Views : Most Viewed - <a onClick="bookmarkpress_library_search('views_desc')">First</a> / <a onClick="bookmarkpress_library_search('views_asc')">Last</a>
				</p><?PHP
			
				$output = array();
				
				$details = new BookmarkDetails();
			
				foreach($wpdb->last_result as $post){
					
					if(strpos($_REQUEST['extra'],"pop_")!==FALSE){
					
						$pop = get_post_meta( $post->id, '_bookmark_likes', true);
						
						if(trim($pop)==""){
						
							$pop = 0;
						
						}
					
						if(isset($output[$pop])){
						
							array_push($output[$pop], $details->make_post_html($post));
						
						}else{
						
							$output[$pop] = array($details->make_post_html($post));
						
						}
					
					}else if(strpos($_REQUEST['extra'],"visits_")!==FALSE){
					
						$visits = get_post_meta( $post->id, '_bookmark_visits', true);
						
						if(trim($visits)==""){
						
							$visits = 0;
						
						}
						
						if(isset($output[$visits])){
						
							array_push($output[$visits], $details->make_post_html($post));
						
						}else{
						
							$output[$visits] = array( $details->make_post_html($post));
						
						}
					
					}else if(strpos($_REQUEST['extra'],"views_")!==FALSE){
					
						$views = get_post_meta( $post->id, '_bookmark_views', true);
						
						if(trim($views)==""){
						
							$views= 0;
						
						}
						
						if(isset($output[$views])){
						
							array_push($output[$views], $details->make_post_html($post));
						
						}else{
						
							$output[$views] = array($details->make_post_html($post));
						
						}
					
					}else{
					
						$implode = true;
					
						array_push($output, $details->make_post_html($post));
						
					}					
					
				}
				
				if($implode){
				
					echo implode(" ", $output);
					
				}else{
				
					if(strpos($_REQUEST['extra'],"_asc")!==FALSE){
				
						krsort($output);
						
					}
					
					foreach($output as $posts){
						
						foreach($posts as $post){
								
							echo $post;
						
						}
						
					}
				
				}
			
			}else{
			
				echo "No posts found";
			
			}
		
		}
		
		die();
	
	}
	
	function the_content($content) {
	
		global $post;
		
		if($post->ID==get_option("bookmarkpress_page_library")){

			echo $content;

			?><form method="POST" action="">
				<p>Enter a term to search for</p>
				<input type="text" id="bookmarkpress_search" onKeyUp="bookmarkpress_library_search('')" />
			</form>
			<h2>Search Results</h2>
			<div id="bookmarkpress_results">Results will appear here</div><?PHP
			
		}else{
		
			return $content;
		
		}
		
	}

} 

$bookmarkLibrary = new BookmarkLibrary();

?>