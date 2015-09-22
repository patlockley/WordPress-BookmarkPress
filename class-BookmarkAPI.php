<?PHP

	class bookmarkAPI{
	
		public function __construct() {
		
			add_action('do_feed_search_feed', array($this,"search_feed"));
			add_filter('query_vars', array($this,'search_feed_queryvars'));

		}

		function search_feed_queryvars($qvars)
		{
		  $qvars[] = 'terms';
		  return $qvars;
		}

		function search_feed()
		{

			header('Content-Type: application/xml');
			
			global $wpdb; 

			$keyword = strip_tags(htmlentities($_GET['terms']));
			
			$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts
				   WHERE ($wpdb->posts.post_type = 'bookmarkpress'
				   and $wpdb->posts.post_title like '%" . $keyword . "%')
				   ORDER BY $wpdb->posts.post_date DESC";

			$allposts = $wpdb->get_results($querystr, OBJECT);
		   
			echo "<rss version='2.0'><channel>";

			echo "<title>" . get_bloginfo('name') . "</title>";
			echo "<description>" . get_bloginfo('description') . "</description>";
			echo "<link>" . get_bloginfo('wpurl') . "?feed=oer</link>";
										
			foreach($allposts as $key => $value){

				echo "<item>\n";
				echo "\t<link>" . str_replace("&","&amp;",$value->guid) . "</link>\n";
				echo "\t<guid>" . str_replace("&","&amp;",$value->guid) . "</guid>\n";
				echo "\t<title>" . $value->post_title . "</title>\n";
				echo "\t<description>" . $value->post_excerpt . "</description>\n";

				$date = $value->post_date_gmt;

				$text = explode(" ", $date);

				$days = explode("-", $text[0]);

				$time = mktime(0,0,0,$days[1],$days[2],$days[0]);

				echo "\t<pubDate>" . date("D, d M Y " , $time) . $text[1]  . " GMT</pubDate>";
				echo "\n</item>\n";
			}

			echo "</channel></rss>";

		}  

	}
	
	$bookmarkAPI = new bookmarkAPI;

?>