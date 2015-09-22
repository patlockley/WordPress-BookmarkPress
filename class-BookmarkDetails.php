<?PHP

class BookmarkDetails{

	function bookmarkpress_post_details($post_id){
	
		$pop = get_post_meta( $post_id, '_bookmark_likes', true);
			
		$visits = get_post_meta( $post_id, '_bookmark_visits', true);
					
		$views = get_post_meta( $post_id, '_bookmark_views', true);
	
		$data = "";
	
		if(trim($pop)==""){
		
			$data .= "Likes 0 | ";
		
		}else{
		
			$data .= "Likes " . $pop . " | ";
		
		}
		
		if(trim($visits)==""){
		
			$data .= " Visits 0 | ";
		
		}else{
		
			$data .= " Visits " . $visits . " | ";
		
		}
		
		if(trim($views)==""){
		
			$data .= " Views 0 ";
		
		}else{
		
			$data .= " Views " . $views;
		
		}

		$data .= str_replace($post_id,"",apply_filters("bookmarkpress_add_post_details", $post_id));
		
		return $data;
	
	}
	
	function make_post_html($post){
	
		return '<div><p><a href="' . $post->guid . '">' . $post->post_title . '</a></p><p>Added : '. $post->post_date . ' by <a href="' . get_author_posts_url( $post->post_author) . '">' . get_the_author_meta( "user_nicename", $post->post_author )  . '</a> | ' . $this->bookmarkpress_post_details($post->id)  . '</p></div>';
	
	}
	

}

?>