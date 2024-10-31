<?php

if ( !class_exists( 'Photography_Core_API' ) ) {

	class Photography_Core_API {

		public $core;

		public function __construct( $core ) {
			$this->core = $core;
			if ( is_admin() ) {
				add_action( 'wp_ajax_pcore_collection', array( $this, 'ajax_collection' ) );
				add_action( 'wp_ajax_pcore_collections', array( $this, 'ajax_collections' ) );
				add_action( 'wp_ajax_pcore_folders', array( $this, 'ajax_folders' ) );
			}
			else {
				if ( class_exists( 'Polylang' ) ) {
					require_once( 'i18n/polylang.php' );
					new Photography_Core_Polylang();
				}
			}
		}

		public function ajax_collection() {
			$folderId = intval( $_POST['collectionId'] );
			$results = $this->get_collection( $folderId );
			echo json_encode( $results );
			die();
		}

		public function ajax_collections() {
			$folderId = intval( $_POST['folderId'] );
			$results = $this->get_collections( $folderId );
			echo json_encode( $results );
			die();
		}

		public function ajax_folders() {
			$folderId = intval( $_POST['folderId'] );
			$results = $this->get_folders( $folderId );
			echo json_encode( $results );
			die();
		}

		public function get_breadcrumbs_from_folder( $folderId ) {
			$hierarchy = array();
			$parent = get_term_by( 'id', $folderId, 'meow_folder');
			array_push( $hierarchy, array(
				'id' => $parent->term_id,
				'type' => 'folder',
				'name' => $parent->name,
				'url' => get_term_link( $parent->term_id, 'meow_folder' )
			) );
			while ( !empty( $parent ) && $parent->parent != '0' ) {
				$term_id = $parent->parent;
				$parent = get_term_by('id', $term_id, 'meow_folder');
				array_push( $hierarchy, array(
					'id' => $parent->term_id,
					'type' => 'folder',
					'name' => $parent->name,
					'url' => get_term_link( $parent->term_id, 'meow_folder' )
				) );
			}
			return array_reverse( $hierarchy );
		}

		public function get_breadcrumbs_from_collection( $collectionId ) {
			$hierarchy = array();
			$category = get_the_terms( $collectionId, 'meow_folder' );
			if ( !empty( $category ) && count( $category ) > 0 ) {
				$last_category = $category[0];
				$term_id = $last_category->term_id;
				$hierarchy = $this->get_breadcrumbs_from_folder( $term_id );
			}
			$post = get_post( $collectionId );
			array_push( $hierarchy, array(
				'id' => $collectionId,
				'type' => 'collection',
				'name' => $post->post_title,
				'url' => get_post_permalink( $collectionId ) ) );
			return $hierarchy;
		}


		// Get the collection $collectionId
		public function get_all_collections() {
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT ID as 'id', post_title as 'name', post_name as 'slug',
					post_date as 'createdOn',
					post_excerpt as 'description',
					'collection' as 'type'
				FROM $wpdb->posts p
				WHERE post_type = 'meow_collection'
				AND post_status = 'publish'
				", ARRAY_A
			);
			foreach ( $rows as &$row )
				$row['photos'] = $this->count_images_in_collection( (int)$row['id'] );
			return $rows;
		}

		// Get the collection $collectionId
		public function get_collection( $collectionId ) {
			global $wpdb;
			$collectionId = esc_sql( $collectionId );
			$rows = $wpdb->get_results( "SELECT ID as 'id', post_title as 'name', post_name as 'slug',
					post_excerpt as 'description',
					'collection' as 'type'
				FROM $wpdb->posts p
				WHERE post_type = 'meow_collection'
				AND p.ID = $collectionId
				AND post_status = 'publish'
				", ARRAY_A
			);
			foreach ( $rows as &$row )
				$row['photos'] = $this->count_images_in_collection( (int)$row['id'] );
			return empty( $rows ) ? null : $rows[0];
		}

			// Get the collections in $folderId
		public function get_collections( $folderId = 0 ) {
			global $wpdb;

			// In the case of a multilingual website
			$folderId = apply_filters( 'pcore_resolve_folder_id', $folderId );

			// Order filter
			$order = apply_filters( 'pcore_collections_order', null, $folderId );
			if ( $order === 'title' )
				$order = ' ORDER BY name ASC';

			// Filter for Collections Order
			if ( empty( $order ) && class_exists( 'CPTO' ) ) {
				global $CPTO;
				$options = $CPTO->functions->get_options();
				if ( isset( $options['show_reorder_interfaces']['meow_collection'] ) ) {
					if ( $options['show_reorder_interfaces']['meow_collection'] === 'show' )
					$order = ' ORDER BY menu_order ASC';
				}
			}
			else {
				// Default
				$order = ' ORDER BY p.post_date DESC';
			}

			$folderId = esc_sql( $folderId );
			$rows = $wpdb->get_results( "SELECT ID as 'id', post_title as 'name', post_name as 'slug',
					post_excerpt as 'description',
					'collection' as 'type'
				FROM $wpdb->posts p
				LEFT JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
				WHERE post_type = 'meow_collection'" .
				( !empty( $folderId ) ? "AND tr.term_taxonomy_id = $folderId" : "AND object_id IS NULL" ) . "
				AND post_status = 'publish'" . $order, ARRAY_A
			);
			foreach ( $rows as &$row )
				$row['photos'] = $this->count_images_in_collection( (int)$row['id'] );

			$rows = apply_filters( 'pcore_get_collections', $rows );
			return $rows;
		}

		public function count_images_in_collection( $id ) {
			$post = get_post( $id );
			$post_content = $post->post_content;
			preg_match( '/\[gallery.*ids=.(.*).\]/', $post_content, $ids );
			if ( isset( $ids[1] ) ) {
				$ids = explode( ',', $ids[1] );
				return count( $ids );
			}
			return 0;
		}

		public function get_folder_thumbnail( $id ) {
			return get_term_meta( $id, '_thumbnail_id', true );
		}

		// Get the folders in $folderId
		public function get_folders( $folderId = null ) {
			global $wpdb;

			// In the case of a multilingual website
			$folderId = apply_filters( 'pcore_resolve_folder_id', $folderId );

			// Filter for Folders Order
			$order = apply_filters( 'pcore_folders_order', 'tax_position', $folderId );
			if ( $order === 'tax_position' )
				$order = ' ORDER BY tm.meta_value';
			else if ( $order === 'title' )
				$order = ' ORDER BY name';

			$folderId = esc_sql( $folderId );
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT tt.term_id as 'id', name, slug, description,
					'folder' as type,
					count as 'collections',
					(SELECT COUNT(*) FROM $wpdb->term_taxonomy ttt WHERE ttt.parent = tt.term_id) as 'folders'
				FROM $wpdb->terms t
				LEFT JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id
				LEFT JOIN $wpdb->termmeta tm ON t.term_id = tm.term_id AND tm.meta_key = 'tax_position'
				WHERE taxonomy = 'meow_folder' AND parent = %d" . $order, $folderId ), ARRAY_A
			);
			foreach ( $rows as &$row )
				$row['thumbnail_id'] = $this->get_folder_thumbnail( $row['id'] );

			$rows = apply_filters( 'pcore_get_folders', $rows );
			return $rows;
		}

		// Get the hierarchy of folders and collections
		public function get_hierarchy( $currentFolder = null, $foldersOnly = false ) {
			$folders = $this->get_folders( $currentFolder );
			foreach ( $folders as &$folder ) {
				$folder['children'] = $this->get_hierarchy( $folder['id'], $foldersOnly );
			}
			$collections = $foldersOnly ? array() : $this->get_collections( $currentFolder );
			return array_merge( $collections, $folders );
		}

		public function get_folder_hierarchy() {
			return $this->get_hierarchy( null, true );
		}

		// Transform hierarchy to a vertical list of folders
		// Folders are padded in order to be used in a <select>
		public function hierarchy_to_folders( $hierarchy, $level = 1 ) {
			$results = array();
			foreach ( $hierarchy as $item ) {
				if ( $item['type'] == 'collection' )
					continue;
				$name = $level > 0 ? ( ' ' . $item['name'] ) : $item['name'];
				array_push( $results, array(
					'id' => $item['id'],
					'name' => $item['name'],
					'padded_name' => str_pad( $name, strlen( $name ) + $level, '-', STR_PAD_LEFT )
				) );
				if ( isset( $item['children'] ) ) {
					$subs = $this->hierarchy_to_folders( $item['children'], $level + 1 );
					$results = array_merge( $results, $subs );
				}
			}
			return $results;
		}

		// Transform hierarchy to a vertical list of collections
		// Folders are padded in order to be used in a <select>
		public function hierarchy_to_collections( $hierarchy, $level = 1 ) {
			$results = array();
			foreach ( $hierarchy as $item ) {
				if ( $item['type'] == 'folder' ) {
					if ( isset( $item['children'] ) ) {
						$subs = $this->hierarchy_to_collections( $item['children'], $level + 1 );
						$results = array_merge( $results, $subs );
					}
					continue;
				}
				$name = $level > 0 ? ( ' ' . $item['name'] ) : $item['name'];
				array_push( $results, array(
					'id' => $item['id'],
					'name' => $item['name'],
					'padded_name' => str_pad( $name, strlen( $name ) + $level, '-', STR_PAD_LEFT )
				) );
			}
			return $results;
		}

	}

}

?>
