<?PHP

	class bookmarkCSV{
	
		public function __construct() {
		
			add_action('do_feed_csv_export', array($this,"csv_export"));

		}
		
		function csv_export(){

			global $wpdb;

			header('Content-Type: application/csv');

			header('Content-Disposition: attachment; filename="data.csv"');

			$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts
						  WHERE $wpdb->posts.post_type = 'bookmarkpress'
						  and $wpdb->posts.post_status = 'publish'";

			$pageposts = $wpdb->get_results($querystr, OBJECT);

			foreach($pageposts as $post){

				echo '"' . $post->post_title . '",';

				$posttags = get_the_tags($post->ID);
				if ($posttags) {
				  foreach($posttags as $tag) {

					if(strpos($tag->name,"(")!==FALSE){

							$tag_name = explode("(",$tag->name);

							echo str_replace(")","",$tag_name[1]) . ", ";

					}else{

						  echo $tag->name . ", ";

					}

				  }
				}

				$post_categories = wp_get_post_categories( $post->ID );
				$cats = array();

				foreach($post_categories as $c){
					  $cat = get_category( $c );
					  echo $cat->name . ", ";
				}
				echo "\",";
				echo "\"";
				the_author_meta( "display_name", $post->post_author);
				echo "\",";

				echo "\"" . $post->guid . "\"\n";

			}

		}

	}
	
	$bookmarkCSV = new bookmarkCSV;

?>