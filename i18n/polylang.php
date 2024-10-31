<?php

if ( !class_exists( 'Photography_Core_Polylang' ) ) {

	class Photography_Core_Polylang {

		public $language = null;
		public $default_language = null;

		function __construct() {
			add_filter( 'init', array( $this, 'init' ) );
			add_filter( 'pcore_get_collections', array( $this, 'get_collections' ), 10, 1 );
			add_filter( 'pcore_get_folders', array( $this, 'get_folders' ), 10, 1 );
			add_filter( 'pcore_resolve_folder_id', array( $this, 'resolve_folder_id' ), 10, 1 );
		}

		function init() {
			$this->default_language = pll_default_language();
			$this->language = pll_current_language();
		}

		function translate_block_one( &$row ) {
			$translation = null;
			if ( $row['type'] === 'collection' ) {
				$id = pll_get_post( $row['id'], $this->language );
				if ( empty( $id ) )
					return;
				$translation = get_post( $id );
				if ( !empty( $translation ) ) {
					$row['id'] = $id;
					$row['name'] = $translation->post_title;
					$row['slug'] = $translation->post_slug;
					$row['description'] = $translation->post_excerpt;
				}
			}
			if ( $row['type'] === 'folder' ) {
				$id = pll_get_term( $row['id'], $this->language );
				if ( empty( $id ) )
					return;
				$translation = get_term( $id, 'meow_folder' );
				if ( !is_wp_error( $translation ) ) {
					$row['id'] = $id;
					$row['name'] = $translation->name;
					$row['slug'] = $translation->slug;
					$row['description'] = $translation->description;
				}
			}
		}

		function resolve_folder_id( $id ) {
			$id = pll_get_term( $id, $this->default_language );
			return $id;
		}

		function translate_blocks( &$rows ) {
			foreach ( $rows as &$row ) {
				$this->translate_block_one( $row );
			}
		}

		function get_collections( $rows ) {
			$this->translate_blocks( $rows );
			return $rows;
		}

		function get_folders( $rows ) {
			$newRows = array();
			foreach ( $rows as $row ) {
				$language = pll_get_term_language( $row['id'] );
				if ( $language === $this->default_language )
					array_push( $newRows, $row );
			}
			$this->translate_blocks( $newRows );
			return $newRows;
		}

	}
}

?>