<div id="post-<?php the_ID();?>" <?php post_class(); ?>>
  <div class="post-content-single">
    <h3 class="page-title">
        <blockquote>
        <?php
        $say = array( 'Ooorschwernochmableede', 'Goddfordammichnochäma' );
        $random_key = array_rand( $say, 1 );
        _e( $say[ $random_key ], 'mesmerize' );
        ;?>
        </blockquote>
    </h3>
      <?php if (is_home() && current_user_can('publish_posts')): ?>
        <p><?php printf(__('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'mesmerize'), esc_url(admin_url('post-new.php')));?></p>
      <?php elseif (is_search()): ?>
        <p><?php _e('...leider keine Treffer', 'mesmerize');?></p>
        <?php // get_search_form();?>
      <?php else: ?>
        <p><?php _e('...diese Seite existiert leider nicht', 'mesmerize');?></p>
        <?php // get_search_form();?>
      <?php endif; ?>
  </div>
</div>
