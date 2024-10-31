<?php

if ( !class_exists( 'Photography_Core_Shortcodes' ) ) {

	class Photography_Core_Shortcodes {

		public $core;

		public function __construct( $core ) {
			$this->core = $core;
			add_shortcode( 'mwt-section-header', array( $this, 'display_section_header' ) );
			add_shortcode( 'mwt-container', array( $this, 'display_container' ) );
			add_shortcode( 'mwt-collections', array( $this, 'display_collections' ) );
			add_shortcode( 'mwt-folders', array( $this, 'display_folders' ) );
			add_shortcode( 'mwt-keywords', array( $this, 'display_keywords' ) );
			add_shortcode( 'mwt-search', array( $this, 'display_search' ) );
		}
		
		// Section Header Block does this (shortcode not called)
		function display_section_header( $atts, $content = null ) {
			return '<div class="mwt-section-header">
				<h3>' . $content . '</h3>
				<div class="line"></div>
				</div>';
		}

		// Not used.
		function display_container( $atts, $content = null ) {
			$a = shortcode_atts( array(
					'padding' => 'nopadding',
			), $atts );
			$output = "<div class='container ".$a['padding']."'>".do_shortcode( $content )."</div>";
			return $output;
		}
		
		// Collections Block use this Shortcode
		function display_collections( $atts ) {
			$a = shortcode_atts( array( 'folder_id' => '' ), $atts );
			$collections = $this->core->get_collections($a['folder_id']);
			$html = '<pre>' . print_r( $collections, 1 ) . '</pre>';
			$html = apply_filters( 'mwt_collections_output', $html, $collections  );
			return $html;
		}

		// Collections Block use this Shortcode
		function display_folders( $atts ) {
			$a = shortcode_atts( array( 'folder_id' => '' ), $atts );
			$folders = $this->core->get_folders( $a['folder_id'] );
			$html = '<pre>' . print_r( $folders, 1 ) . '</pre>';
			$html = apply_filters( 'mwt_folders_output', $html, $folders  );
			return $html;
		}

		function display_keywords( $atts ) {
			$a = shortcode_atts( array(), $atts );
			$tags = get_terms( array( 'taxonomy' => 'attachment_keyword', 'hide_empty' => false ) );
			$tag_permalink = get_term_link( $tags[0] );
			$tag_cloud_markup = "<div class='mwt-tags-cloud'>";
			foreach ( $tags as $tag ) {
				$tag_cloud_markup .= "<a class='tag' href='".get_term_link($tag)."'>" . $tag->name . "</a>";
			}
			$tag_cloud_markup .= "</div>";
			return $tag_cloud_markup;
		}

		function display_search( $atts ) {
			$a = shortcode_atts( array(), $atts );
			$search = get_search_form( false );
			$search = str_replace( 'search-field', 'mwt-search', $search );
			return $search;
		}

	}

}

?>