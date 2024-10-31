<?php
/*
Plugin Name: Photography Core
Plugin URI: https://meowapps.com
Description: Photography Core is the heart of the themes made for photographers. Features are the API, helpers, Gutenberg blocks, Lightroom, etc.
Version: 0.3.0
Author: Jordy Meow
Author URI: https://meowapps.com
Text Domain: photography-core

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html

Originally developed for two of my websites:
- Jordy Meow (https://offbeatjapan.org)
- Haikyo (https://haikyo.org)
*/

if ( !class_exists( 'Photography_Core' ) ) {

	class Photography_Core {

		public $featured;
		public $core;

		public function __construct() {
			global $photocore;
			include( 'api.php' );
			$photocore = new Photography_Core_API( $this );
			$this->core = $photocore;
			include( 'seo.php' );
			new Photography_Core_SEO( $this->core );
			add_action( 'init', array( $this, 'init' ) );
		}

		public function init() {
			$this->create_post_type();
			$this->create_taxonomy();
			include( 'featured.php' );
			$featured = new Photography_Core_Featured();
			if ( class_exists( 'Meow_WPLR_Sync_Core' ) ) {
				include( 'plugins/wplr-sync.php' );
				new Photography_Core_WPLR_Sync();
			}
			include( 'shortcodes.php' );
			include( 'folders-order.php' );
			include( 'blocks/blocks.php' );
			new Photography_Core_Shortcodes( $this->core );
			new Photography_Core_Blocks( $this->core );
			add_filter( 'gutenberg_can_edit_post_type', array( $this, 'active_gutenberg' ), 10, 2 );
			$this->testAPI();
		}

		public function active_gutenberg( $can_edit, $post_type ) {
			if ( $post_type == 'meow_collection' ) {
				$can_edit = true;
			}
			return $can_edit;
		}

		public function testAPI() {
			// global $photocore;
			// $hierarchy = $this->core->get_folder_hierarchy(null, true);
			// print_r( $hierarchy );
			// exit;
			// print_r( $photocore->hierarchy_to_collections( $hierarchy ) );
			// print_r( $photocore->hierarchy_to_folders( $hierarchy ) );
			// print_r($photocore->get_breadcrumbs_from_folder(907));
			// print_r($photocore->get_breadcrumbs_from_collection(835));
			// exit;
			// $folders = $photocore->get_all_collections();
			// print_r( $folders );
			// $folders = $photocore->get_folders(0);
			// print_r( $folders );
			// $collections = $photocore->get_collections(0);
			// print_r( $collections );
			// exit;
		}

		private function create_post_type() {
			$labels = array(
				'name'               => _x( 'Collections', 'Name', 'photography-core' ),
				'singular_name'      => _x( 'Collection', 'Singular Name', 'photography-core' ),
				'menu_name'          => _x( 'Collections', 'Menu Name', 'photography-core' ),
				'name_admin_bar'     => _x( 'Collection', 'Admin Bar Name', 'photography-core' ),
				'add_new'            => _x( 'Add New', 'Add New', 'photography-core' ),
				'add_new_item'       => __( 'Add New Collection', 'photography-core' ),
				'new_item'           => __( 'New Collection', 'photography-core' ),
				'edit_item'          => __( 'Edit Collection', 'photography-core' ),
				'view_item'          => __( 'View Collection', 'photography-core' ),
				'all_items'          => __( 'All Collections', 'photography-core' ),
				'search_items'       => __( 'Search Collections', 'photography-core' ),
				'parent_item_colon'  => __( 'Parent Collections:', 'photography-core' ),
				'not_found'          => __( 'No collections found.', 'photography-core' ),
				'not_found_in_trash' => __( 'No collections found in Trash.', 'photography-core' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'photography-core' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'menu_icon'          => 'dashicons-images-alt',
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'collection' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 10,
				'show_in_rest'       => true,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' ),
				//'taxonomies'				 => array( 'meow_folder' )
			);
			$args = apply_filters( 'meow_collection_post_type', $args );
			register_post_type( 'meow_collection', $args );
		}

		private function create_taxonomy() {

			// Create Folders
			$labels = array(
				'name'              => _x( 'Folders', 'Name', 'photography-core' ),
				'singular_name'     => _x( 'Folder', 'Singular Name', 'photography-core' ),
				'search_items'      => __( 'Search Folders', 'photography-core' ),
				'all_items'         => __( 'All Folders', 'photography-core' ),
				'parent_item'       => __( 'Parent Folder', 'photography-core' ),
				'parent_item_colon' => __( 'Parent Folder:', 'photography-core' ),
				'edit_item'         => __( 'Edit Folder', 'photography-core' ),
				'update_item'       => __( 'Update Folder', 'photography-core' ),
				'add_new_item'      => __( 'Add New Folder', 'photography-core' ),
				'new_item_name'     => __( 'New Folder Name', 'photography-core' ),
				'menu_name'         => __( 'Folders', 'photography-core' ),
			);
			$args = array(
				'hierarchical'          => true,
				'public'                => true,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'query_var'             => true,
				'show_in_rest'       		=> true,
				'update_count_callback' => '_update_generic_term_count',
				'rewrite'               => array( 'slug' => 'folder' ),
			);
			$args = apply_filters( 'meow_folder_taxonomy', $args );
			register_taxonomy( 'meow_folder', array( 'meow_collection' ), $args );

			// Create Media Keywords
			$labels = array(
				'name'              => _x( 'Keywords', 'taxonomy general name' ),
				'singular_name'     => _x( 'Keyword', 'taxonomy singular name' ),
				'search_items'      => __( 'Search Keywords' ),
				'all_items'         => __( 'All Keywords' ),
				'parent_item'       => __( 'Parent Keyword' ),
				'parent_item_colon' => __( 'Parent Keyword:' ),
				'edit_item'         => __( 'Edit Keyword' ),
				'update_item'       => __( 'Update Keyword' ),
				'add_new_item'      => __( 'Add New Keyword' ),
				'new_item_name'     => __( 'New Keyword Name' ),
				'menu_name'         => __( 'Keywords' )
			);
			$args = array(
				'hierarchical'          => true,
				'public'                => true,
				'publicly_queryable'		=> true,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'query_var'             => true,
				'update_count_callback' => '_update_generic_term_count',
				'rewrite'               => array( 'slug' => 'keyword' )
			);
			$args = apply_filters( 'attachment_keyword_taxonomy', $args );
			register_taxonomy( 'attachment_keyword', array( 'attachment' ), $args );
		}

	}

}

new Photography_Core();

?>
