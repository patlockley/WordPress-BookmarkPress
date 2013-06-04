<?php

class BookmarkPostsFilter {
	
	public function __construct() {	
		
		add_filter( 'posts_where' , array($this, 'posts_where') );
		add_filter( 'pre_get_posts', array($this, 'add_to_query') );
		
	}

	function posts_where( $where ) {
			
		global $wpdb;	
		
		//$some_posts = get_posts( array( 'suppress_filters' => false ) );
		
		if(get_option("bookmark_press_include")){
			
			$where = str_replace("{$wpdb->posts}.post_type = 'post'", "({$wpdb->posts}.post_type = 'post' or {$wpdb->posts}.post_type = 'bookmarkpress')", $where);
			//$where = " BULL SHIT";
		
		}
		
		return $where;
		
	}

    function add_to_query( $query ) {
	
		if(get_option("bookmark_press_include")){
	
			$supported = $query->get( 'post_type' );
			if ( !$supported || $supported == 'post' )
				$supported = array( 'post', 'bookmarkpress' );
			elseif ( is_array( $supported ) )
				array_push( $supported, 'bookmarkpress' );
			$query->set( 'post_type', $supported );
			return $query;
			
		}
			
		return $query;
		
    }

} 

$bookmarkPostsFilter = new BookmarkPostsFilter();

?>