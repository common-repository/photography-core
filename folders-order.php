<?php

/*
*	This module is totally inspired from the excellent YIKES Simple Taxonomy Ordering Scripts by Yikes and Evan Herman.
* However, it was modified to work only with the meow_folder taxonomy.
*/

if ( ! class_exists( 'Photography_Core_Folders_Order' ) ) {

	class Photography_Core_Folders_Order {

		function __construct() {
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'wp_ajax_update_taxonomy_order', array( $this, 'wp_ajax_update_taxonomy_order' ) );
			add_action( 'load-edit-tags.php', array( $this, 'load_edit_tags' ) );
		}

		public function admin_head() {
			if( is_admin() ) {
				$screen = get_current_screen();
				if( isset( $screen ) && isset( $screen->base ) ) {
					if( $screen->base == 'edit-tags' ) {
						$this->ensure_terms_have_tax_position_value( $screen );
						if( ! isset( $_GET['orderby'] ) && $this->is_taxonomy_position_enabled( $screen->taxonomy ) ) {
							$this->enqueue_scripts_and_styles();
							add_filter( 'terms_clauses', array( $this, 'terms_clauses' ), 10, 3 );
						}
					}
				}
			}
		}

		public function load_edit_tags() {
			$screen = get_current_screen();
			// ensuere that our terms have a `tax_position` value set, so they display properly
			$this->ensure_terms_have_tax_position_value( $screen );
			// retreive a list of enabled taxonomies
		}

		public function init() {
			/* Front End Re-Order of Hierarchical Taxonomies */
			if( ! is_admin() ) {
				add_filter( 'terms_clauses', array( $this, 'terms_clauses' ), 10, 3 );
			}
		}

		public function enqueue_scripts_and_styles() {
			wp_enqueue_style( 'order-drag-drop-styles', plugin_dir_url( __FILE__ ) . 'css/order-drag-drop.css' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'order-drag-drop', plugin_dir_url(__FILE__) . 'js/order-drag-drop.js', array( 'jquery-ui-core', 'jquery-ui-sortable' ), true );
			wp_localize_script( 'order-drag-drop', 'simple_taxonomy_ordering_data', array(
				'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'preloader_url' => esc_url( admin_url( 'images/wpspin_light.gif' ) ),
			) );
		}

		public function ensure_terms_have_tax_position_value( $screen ) {
			if ( isset( $screen ) && isset( $screen->taxonomy ) ) {
				$terms = get_terms( $screen->taxonomy, array( 'hide_empty' => false ) );
				$x = 1;
				foreach( $terms as $term ) {
					if( ! get_term_meta( $term->term_id, 'tax_position', true ) ) {
						update_term_meta( $term->term_id, 'tax_position', $x );
						$x++;
					}
				}
			}
		}

		public function terms_clauses( $pieces, $taxonomies, $args ) {

			foreach( $taxonomies as $taxonomy ) {
				if( $this->is_taxonomy_position_enabled( $taxonomy ) ) {
					global $wpdb;
					$join_statement = " LEFT JOIN $wpdb->termmeta AS term_meta ON t.term_id = term_meta.term_id AND term_meta.meta_key = 'tax_position'";
					if ( !$this->does_substring_exist( $pieces['join'], $join_statement ) ) {
						$pieces['join'] .= $join_statement;
					}
					$pieces['orderby'] = "ORDER BY CAST( term_meta.meta_value AS UNSIGNED )";
				}
			}
			return $pieces;
		}

		protected function does_substring_exist( $string, $substring ) {
			return ( strstr( $string, $substring ) === false ) ? false : true;
		}

		public function wp_ajax_update_taxonomy_order() {
			$array_data = $_POST['updated_array'];
			foreach( $array_data as $taxonomy_data ) {
				update_term_meta( $taxonomy_data[0], 'tax_position', (int) ( $taxonomy_data[1] + 1 ) );
			}
			wp_die();
			exit;
		}

		public function is_taxonomy_position_enabled( $taxonomy_name ) {
			if( ! $taxonomy_name ) {
				return false;
			}
			$tax_object = get_taxonomy( $taxonomy_name );
			if( $tax_object && is_object( $tax_object ) ) {
				if( isset( $tax_object->tax_position ) && $tax_object->tax_position || $taxonomy_name === 'meow_folder' ) {
					return true;
				} else {
					return false;
				}
			}
			return false;
		}

	}

}

new Photography_Core_Folders_Order;

?>
