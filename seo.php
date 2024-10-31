<?php

if ( !class_exists( 'Photography_Core_SEO' ) ) {

  class Photography_Core_SEO {

    public function __construct( $core ) {
      add_action( 'wp_head', array( $this, 'init_seo' ), 5, 2 );
    }

    function init_seo() {
      global $post;
      $seo_thumbnail = "";
      $seo_title = get_bloginfo( 'title' );
      $seo_description = get_bloginfo( 'description' );
      if ( is_front_page() || is_home() ) {
        $home_desc = get_theme_mod( 'seo_site_description' );
        if ( $home_desc )
          $seo_description = $home_desc;
      }
      else if ( is_singular() ) {
        global $post;
        $seo_title = get_the_title();
        $excerpt = $post->post_excerpt;
        if ( empty( $excerpt ) )
          $excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 40 );
        if ( $excerpt )
          $seo_description = $excerpt;
        if ( has_post_thumbnail( $post->ID ) )
          $seo_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium')[0];
      }
?>
<meta name="description" content="<?php echo $seo_description; ?>" />
<meta property="og:title" content="<?php echo $seo_title; ?>"/>
<meta property="og:description" content="<?php echo $seo_description; ?>"/>
<meta property="og:type" content="article"/>
<meta property="og:url" content="<?php echo the_permalink(); ?>"/>
<meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
<meta property="og:image" content="<?php echo $seo_thumbnail; ?>"/>
<?php
    }

  }

}

?>