<?php

class BookmarkFeed {
	
	public function __construct() {	
		add_action( 'do_feed_bookmarkpress', array($this,'display_feed'), 10, 1);
	}
	
	function display_feed(){
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "posts";
		
		$results = $wpdb->query("select id, post_title, post_date, guid, post_author FROM " . $table_name . " where post_type = 'bookmarkpress'");
		
		if(count($wpdb->last_result)!==0){
						
			header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

			echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
			<rss version="2.0"
				xmlns:content="http://purl.org/rss/1.0/modules/content/"
				xmlns:wfw="http://wellformedweb.org/CommentAPI/"
				xmlns:dc="http://purl.org/dc/elements/1.1/"
				xmlns:atom="http://www.w3.org/2005/Atom"
				xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
				xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
				<?php do_action('rss2_ns'); ?>>
				<channel>
					<title><?php bloginfo_rss('name'); ?> Bookmarks</title>	
					<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
					<link><?php self_link(); ?></link>
					<description><?php bloginfo_rss("description") ?></description>
					<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
					<language><?php echo get_option('rss_language'); ?></language>
					<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
					<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
					<?php do_action('rss2_head'); ?>
					<?PHP
					
						$results = $wpdb->query("select id, post_title, post_date, guid, post_author FROM " . $table_name . " where post_type = 'bookmarkpress'");
		
						foreach($wpdb->last_result as $post){
	
							print_r($post);
							?><item>
								<title><?php the_title_rss() ?></title>
								<link><?php the_permalink_rss() ?></link>
								<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
								<?php the_category_rss('rss2') ?>
								<?php rss_enclosure(); ?>
								<?php do_action('rss2_item'); ?>
							</item><?PHP
		
						}
				
					?></channel>
					</rss><?PHP
	
		}
	
	}

} 

$bookmarkFeed = new BookmarkFeed();

?>