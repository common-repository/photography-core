<?php

if ( !class_exists( 'Photography_Core_Featured' ) ) {

  class Photography_Core_Featured {

    public function __construct() {
    	add_action( 'meow_folder' . '_add_form_fields', array( $this, 'featured_add_texonomy_field' ) );
    	add_action( 'meow_folder' . '_edit_form_fields', array( $this, 'featured_edit_texonomy_field' ) );
    	add_filter( 'manage_edit-' . 'meow_folder' . '_columns', array( $this, 'featured_taxonomy_columns' ) );
    	add_filter( 'manage_' . 'meow_folder' . '_custom_column', array( $this, 'featured_taxonomy_column' ), 10, 3 );
      add_action( 'edit_term', array( $this, 'featured_save_taxonomy_image' ) );
      add_action( 'create_term', array( $this, 'featured_save_taxonomy_image' ) );

      // Style the image in category list
      if ( strpos( $_SERVER['SCRIPT_NAME'], 'edit-tags.php' ) > 0 ) {
      	add_action( 'admin_head', array( $this, 'featured_add_style' ) );
      	add_action( 'quick_edit_custom_box', array( $this, 'featured_quick_edit_custom_box' ), 10, 3 );
      	add_filter( 'attribute_escape', array( $this, 'featured_change_insert_button_text' ), 10, 2 );
      }
    }

    function featured_add_style() {
    	echo '<style type="text/css" media="screen">
    		th.column-thumb {width:60px;}
    		.form-field img.taxonomy-image {border:1px solid #eee;max-width:300px;max-height:300px;}
    		.inline-edit-row fieldset .thumb label span.title {width:48px;height:48px;border:1px solid #eee;display:inline-block;}
    		.column-thumb span {width:48px;height:48px;border:1px solid #eee;display:inline-block;}
    		.inline-edit-row fieldset .thumb img,.column-thumb img {width:48px;height:48px;}
    	</style>';
    }

    // add image field in add form
    function featured_add_texonomy_field() {
    	wp_enqueue_media();
    	echo '<div class="form-field">
    		<label for="taxonomy_image">' . __( 'Image', 'meow-theme-core' ) . '</label>
    		<input type="text" name="taxonomy_image" readonly id="taxonomy_image" value="" />
    		<input type="hidden" name="taxonomy_image_id" id="taxonomy_image_id" value="" />
    		<br/>
    		<button class="meow_featured_upload_image_button button">' . __( 'Choose image', 'meow-theme-core' ) . '</button>
    	</div>' . $this->meow_featured_script();
    }

    // add image field in edit form
    function featured_edit_texonomy_field( $taxonomy ) {
    	wp_enqueue_media();
    	$image_url = $this->featured_taxonomy_image_url( $taxonomy->term_id, NULL, TRUE );
      $attachment_id = get_term_meta( $taxonomy->term_id, '_thumbnail_id', true );

    	echo '<tr class="form-field">
    		<th scope="row" valign="top">
    			<label for="taxonomy_image">' . __( 'Image', 'meow-theme-core') . '</label></th>
    		<td>' .
    			( empty( $image_url ) ? '' : '<img class="taxonomy-image" width="95%" src="' . $image_url . '"/><br/>' ) . '
    			<input type="hidden" name="taxonomy_image_id" id="taxonomy_image_id" value="' . $attachment_id . '" />
    			<input type="text" name="taxonomy_image" readonly id="taxonomy_image" value="' . $image_url . '" /><br />
    			<button class="meow_featured_upload_image_button button">' . __( 'Choose image', 'meow-theme-core' ) . '</button>
    			<button class="meow_featured_remove_image_button button">' . __( 'Remove image', 'meow-theme-core' ) . '</button>
    		</td>
    	</tr>' . $this->meow_featured_script();
    }

    // upload using wordpress upload
    function meow_featured_script() {
    	return '<script type="text/javascript">
    	    jQuery(document).ready(function($) {
    			var wordpress_ver = "' . get_bloginfo("version") . '", upload_button;
    			$(".meow_featured_upload_image_button").click(function(event) {
    				upload_button = $(this);
    				var frame;
    				event.preventDefault();
    				if (frame) {
    					frame.open();
    					return;
    				}
    				frame = wp.media();
    				frame.on( "select", function() {
    					// Grab the selected attachment.
    					var attachment = frame.state().get("selection").first();
    					frame.close();
    					if (upload_button.parent().prev().children().hasClass("tax_list")) {
    						upload_button.parent().prev().children().val(attachment.attributes.url);
    						upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
    					}
    					else
    						$("[name=taxonomy_image_id]").val(attachment.attributes.id);
    						$("[name=taxonomy_image]").val(attachment.attributes.url);
    				});
    				frame.open();
    			});

    			$(".meow_featured_remove_image_button").click(function() {
    				$("[name=taxonomy_image_id]").val("");
    				$("[name=taxonomy_image]").val("");
    				$(this).parent().siblings(".title").children("img").attr("src","");
    				$(".inline-edit-col :input[name=\'taxonomy_image\']").val("");
    				return false;
    			});

    	    });
    	</script>';
    }

    function featured_save_taxonomy_image( $term_id ) {
      if ( isset( $_POST['taxonomy_image_id'] ) ) {
    		update_term_meta( $term_id, '_thumbnail_id', $_POST['taxonomy_image_id'] );
    	}
    }

    // get taxonomy image url for the given term_id (Place holder image by default)
    function featured_taxonomy_image_url( $term_id = NULL, $size = 'full', $return_placeholder = FALSE ) {
    	if ( !$term_id )  {
    		if ( is_category() )
    			$term_id = get_query_var( 'cat' );
    		elseif ( is_tag() )
    			$term_id = get_query_var( 'tag_id' );
    		elseif ( is_tax() ) {
    			$current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
    			$term_id = $current_term->term_id;
    		}
    	}

    	$taxonomy_image_url = '';
      $attachment_id = get_term_meta( $term_id, '_thumbnail_id', true );
    	if ( !empty( $attachment_id ) ) {
    		$taxonomy_image_url = wp_get_attachment_image_src( $attachment_id, $size );
    	  $taxonomy_image_url = $taxonomy_image_url[0];
    	}

      if ( $return_placeholder )
    		return ( $taxonomy_image_url != '' ) ? $taxonomy_image_url : '';
    	else
    		return $taxonomy_image_url;
    }

    function featured_quick_edit_custom_box( $column_name, $screen, $name ) {
    	if ( $column_name == 'thumb' ) {
  			echo '
          <fieldset>
      			<div class="thumb inline-edit-col">
      				<label>
      					<!-- span class="title"><img src="" alt="Thumbnail"/></span -->
      					<span class="input-text-wrap"><input type="text" name="taxonomy_image" readonly value="" class="tax_list" /></span>
      					<input type="hidden" name="taxonomy_image_id" value="" />
      					<span class="input-text-wrap">
      						<button class="meow_featured_upload_image_button button">' .
      							__( 'Choose image', 'meow-theme-core' ) . '</button>
      						<button class="meow_featured_remove_image_button button">' .
      							__( 'Remove image', 'meow-theme-core' ) . '</button>
      					</span>
      				</label>
      			</div>
    		  </fieldset>
        ';
    	}
    }

    /**
     * Thumbnail column added to category admin.
     *
     * @access public
     * @param mixed $columns
     * @return void
     */
    function featured_taxonomy_columns( $columns ) {
      if ( !isset( $columns['cb'] ) )
        return $columns;
    	$new_columns = array();
    	$new_columns['cb'] = $columns['cb'];
    	$new_columns['thumb'] = __( 'Image', 'meow-theme-core' );
    	unset( $columns['cb'] );
    	return array_merge( $new_columns, $columns );
    }

    function featured_taxonomy_column( $columns, $column, $id ) {
    	if ( $column == 'thumb' ) {
    	$src = $this->featured_taxonomy_image_url( $id, 'thumbnail', TRUE );
    		if ( empty( $src ) )
    			return $columns;
    		$columns = '<span><img src="' . $src . '" alt="' . __( 'Thumbnail', 'meow-theme-core' )
          . '" class="wp-post-image" /></span>';
    	}
    	return $columns;
    }

    // Change 'insert into post' to 'use this image'
    function featured_change_insert_button_text($safe_text, $text) {
        return str_replace( "Insert into Post", "Use this image", $text);
    }

    // display taxonomy image for the given term_id
    function meow_featured_taxonomy_image( $term_id = NULL, $size = 'full', $attr = NULL, $echo = TRUE ) {
    	if ( !$term_id ) {
    		if ( is_category() )
    			$term_id = get_query_var('cat');
    		elseif ( is_tag() )
    			$term_id = get_query_var('tag_id');
    		elseif ( is_tax() ) {
    			$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    			$term_id = $current_term->term_id;
    		}
    	}

    	if ( !empty( $attachment_id ) ) {
    		$taxonomy_image_url = wp_get_attachment_image_src( $attachment_id, $size );
    		$taxonomy_image_url = $taxonomy_image_url[0];
    	}

    	$attachment_id = get_term_meta( $term_id, '_thumbnail_id', true );
    	if ( !empty($taxonomy_image_id ) ) {
    		$taxonomy_image = wp_get_attachment_image( $attachment_id, $size, FALSE, $attr );
    	}

    	if ( $echo )
    		echo $taxonomy_image;
    	else
    		return $taxonomy_image;
    }

  }

}

?>