<?php
/*
 * Template Name: Front Page Template
 */
mesmerize_get_header('homepage');
?>

<div class="page-content">
  <div class="<?php mesmerize_page_content_wrapper_class(); ?> page-main-column">
   <?php 
      while ( have_posts() ) : the_post();
        the_content();
      endwhile;
     ?>
  </div>
</div>

<?php get_footer(); ?>
