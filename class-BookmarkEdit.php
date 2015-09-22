<?PHP

	class BookmarksEditor{
		
		protected $version;

		public function __construct() {
		
			add_action( 'admin_menu', array($this,'bookmarkpress_editor_make') );

		}

		function bookmarkpress_editor_make(){

			add_meta_box("bookmarkpress_editor", "BookmarkPress", array($this, "bookmarkpress_editor"), "bookmarkpress");
		
		}

		function bookmarkpress_editor(){

			global $post;
			?><h3><span><?php _e('URL to Bookmark'); ?></span></h3>
				<div class="inside">
					<p>
						<textarea name="bookmarkpress" style="width:100%"><?PHP echo get_post_meta( $post->ID, '_bookmark_url', true ); ?></textarea>
					</p>
					<?PHP
					
						$plays = get_post_meta( $post->ID, '_bookmark_views', true );
						
						?><p>Views : <?PHP echo ($plays+1); ?> | Visits : <span class='cursor' id='bookmarkpress_visits'><?PHP echo get_post_meta( $post->ID, '_bookmark_visits', true ); ?></span> | Likes : <span class='cursor' id='bookmarkpress_like'><?PHP echo get_post_meta( $post->ID, '_bookmark_likes', true ); ?></span></p>";
				</div>			
			<?PHP
			
			do_action("bookmarkpress_extra_fields_edit_create");
			
		}
	
	}
	
	$new_edit = new BookmarksEditor();

?>