<?PHP

	class bookmarkActivityStream{
	
		public function __construct() {
		
			add_action('do_feed_activity_stream', array($this,"activity_stream"));

		}
		
		function build_output($url, $verb, $target, $title){
		
			$output = '"actor": {
		           "url": "' . $url . '",
			    "objectType": "person"
		        },
		        "verb": "' . $verb . '",
			   "object" : {
		          "url": "' . $target . '"
		        },
		        "target": {
		          "url": "' . $target . '",
		          "objectType": "image",
		          "displayName": "' . $title . '"
		        }';
				
			return $output;
		
		}

		function activity_stream()
		{

			global $wpdb;

			header('Content-Type: application/json');

			$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts
				   WHERE $wpdb->posts.post_type = 'bookmarkpress'
				   ORDER BY $wpdb->posts.post_date DESC";

			$allposts = $wpdb->get_results($querystr, OBJECT);
			
			$json = array();

			foreach($allposts as $key => $value){
			
				$bookmark_url = get_post_meta( $value->ID, '_bookmark_url', true );
				$plays = get_post_meta( $value->ID, '_bookmark_views', true );
				$visits = get_post_meta( $value->ID, '_bookmark_visits', true );
				$likes = get_post_meta( $value->ID, '_bookmark_likes', true );

				if(trim($bookmark_url)!=""){
	
					if($plays!=0){
					
						array_push($json, $this->build_output($value->guid, "view", $bookmark_url, $value->post_title));

					}
					
					if($visits!=0){
						
						array_push($json, $this->build_output($value->guid, "visit", $bookmark_url, $value->post_title));
						
					}
					
					if($likes!=0){
						
						array_push($json, $this->build_output($value->guid, "like", $bookmark_url, $value->post_title));
						
					}
					
				}

			}
			
			echo '{
					"items" : [{';
					
			echo implode(",", $json);
			
			echo "}]
			}";

		}  

	}
	
	$bookmarkActivityStream = new bookmarkActivityStream;

?>