<?PHP

	class BookmarksEditor{
		
		protected $version;

		public function __construct() {
		
			die("HERE");
		
			add_action( 'admin_menu', array($this,'bookmarkpress_editor_make') );
		}

		function bookmarkpress_editor_make(){

			die("HERE I AM");

			add_meta_box("bookmarkpress_editor", "BookmarkPress", "bookmarkpress_editor", "post");
		
		}

		function bookmarkpress_editor(){

			global $post;
			echo "BOOOOOOOOOOOOOOOOOOOOM";
				
		}
	
	}
	
	$new_edit = new BookmarksEditor();

?>