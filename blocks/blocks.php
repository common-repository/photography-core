<?php

if ( !class_exists( 'Photography_Core_Blocks' ) ) {

	class Photography_Core_Blocks {

		public $core;

		public function __construct( $core ) {
			if ( function_exists( 'register_block_type' ) ) {
				$this->core = $core;
				$this->backend_editor();
				load_plugin_textdomain( 'photography-core', false, basename( __DIR__ ) . '/languages' );
			}
		}

		function backend_editor() {

			// // For WordPress 5
			// $jed = null;
			// if ( function_exists( 'wp_get_jed_locale_data' ) ) {
			// 	$jed = 'wp_get_jed_locale_data';
			// }
			// // For Gutenberg Beta Plugin
			// else if ( function_exists( 'gutenberg_get_jed_locale_data' ) ) {
			// 	$jed = 'gutenberg_get_jed_locale_data';
			// }
			// if ( empty( $jed ) )
			// 	return;

			// Section Header
			wp_register_script(
				'mwt-section-header-js', plugins_url( 'blocks/section-header/block.js', dirname( __FILE__ ) ),
				array( 'wp-editor', 'wp-i18n', 'wp-element' ), filemtime( plugin_dir_path( __FILE__ ) . 'section-header/block.js' )
			);
			wp_register_style(
				'mwt-section-header-css', plugins_url( 'blocks/section-header/editor.min.css', dirname( __FILE__ ) ),
				array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __FILE__ ) . 'section-header/editor.min.css' )
			);
			register_block_type( 'photography-core/section-header', array(
				'editor_script' => 'mwt-section-header-js',
				'editor_style'  => 'mwt-section-header-css'
			));
			// wp_add_inline_script(
			// 	'mwt-section-header-js',
			// 	'wp.i18n.setLocaleData( ' . json_encode( $jed( 'photography-core' ) ) . ', "photography-core" );',
			// 	'before'
			// );

			// Collections
			wp_register_script(
				'mwt-collections-js', plugins_url( 'blocks/collections/block.js', dirname( __FILE__ ) ),
				array( 'wp-editor', 'wp-i18n', 'wp-element' ), filemtime( plugin_dir_path( __FILE__ ) . 'collections/block.js' )
			);
			wp_register_style(
				'mwt-collections-css', plugins_url( 'blocks/collections/editor.min.css', dirname( __FILE__ ) ),
				array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __FILE__ ) . 'collections/editor.min.css' )
			);
			register_block_type( 'photography-core/collections', array(
				'editor_script' => 'mwt-collections-js',
				'editor_style'  => 'mwt-collections-css'
			));
			// wp_add_inline_script(
			// 	'mwt-collections-js',
			// 	'wp.i18n.setLocaleData( ' . json_encode( $jed( 'photography-core' ) ) . ', "photography-core" );',
			// 	'before'
			// );

			// Folders
			wp_register_script(
				'mwt-folders-js', plugins_url( 'blocks/folders/block.js', dirname( __FILE__ ) ),
				array( 'wp-editor', 'wp-i18n', 'wp-element' ), filemtime( plugin_dir_path( __FILE__ ) . 'folders/block.js' )
			);
			wp_register_style(
				'mwt-folders-css', plugins_url( 'blocks/folders/editor.min.css', dirname( __FILE__ ) ),
				array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __FILE__ ) . 'folders/editor.min.css' )
			);
			register_block_type( 'photography-core/folders', array(
				'editor_script' => 'mwt-folders-js',
				'editor_style'  => 'mwt-folders-css'
			));
			// wp_add_inline_script(
			// 	'mwt-folders-js',
			// 	'wp.i18n.setLocaleData( ' . json_encode( $jed( 'photography-core' ) ) . ', "photography-core" );',
			// 	'before'
			// );

			// Keywords
			wp_register_script(
				'mwt-keywords-js', plugins_url( 'blocks/keywords/block.js', dirname( __FILE__ ) ),
				array( 'wp-editor', 'wp-i18n', 'wp-element' ), filemtime( plugin_dir_path( __FILE__ ) . 'keywords/block.js' )
			);
			wp_register_style(
				'mwt-keywords-css', plugins_url( 'blocks/keywords/editor.min.css', dirname( __FILE__ ) ),
				array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __FILE__ ) . 'keywords/editor.min.css' )
			);
			register_block_type( 'photography-core/keywords', array(
				'editor_script' => 'mwt-keywords-js',
				'editor_style'  => 'mwt-keywords-css'
			));
			// wp_add_inline_script(
			// 	'mwt-keywords-js',
			// 	'wp.i18n.setLocaleData( ' . json_encode( $jed( 'photography-core' ) ) . ', "photography-core" );',
			// 	'before'
			// );

			// Keywords
			wp_register_script(
				'mwt-search-js', plugins_url( 'blocks/search/block.js', dirname( __FILE__ ) ),
				array( 'wp-editor', 'wp-i18n', 'wp-element' ), filemtime( plugin_dir_path( __FILE__ ) . 'search/block.js' )
			);
			wp_register_style(
				'mwt-search-css', plugins_url( 'blocks/search/editor.min.css', dirname( __FILE__ ) ),
				array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __FILE__ ) . 'search/editor.min.css' )
			);
			register_block_type( 'photography-core/search', array(
				'editor_script' => 'mwt-search-js',
				'editor_style'  => 'mwt-search-css'
			));
			// wp_add_inline_script(
			// 	'mwt-search-js',
			// 	'wp.i18n.setLocaleData( ' . json_encode( $jed( 'photography-core' ) ) . ', "photography-core" );',
			// 	'before'
			// );

			// Params
			$hierarchy = $this->core->get_hierarchy();
			$folders = $this->core->hierarchy_to_folders( $hierarchy );
			wp_localize_script( 'mwt-folders-js', 'mwt_block_params', array(
				'logo' => trailingslashit( plugin_dir_url( __FILE__ ) ) . '../img/meowapps.png',
				'folders' => $folders
			) );
		}

	}

}

?>