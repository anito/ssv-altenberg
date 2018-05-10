<div id="post-<?php the_ID(); ?>"<?php post_class(); ?>>
    <div class="post-content-single">
        <h1><?php mesmerize_single_item_title(); ?></h1>
        <?php get_template_part('template-parts/content-post-single-header') ?>
        <div class="post-content-inner">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('post-thumbnail', array("class" => "space-bottom-small space-bottom-xs"));
            }

            the_content();

            wp_link_pages(array(
                'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'mesmerize') . '</span>',
                'after'       => '</div>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
                'pagelink'    => '<span class="screen-reader-text">' . esc_html__('Page', 'mesmerize') . ' </span>%',
                'separator'   => '<span class="screen-reader-text">, </span>',
            ));
            ?>
        </div>

        <?php echo get_the_tag_list('<p class="tags-list"><i data-cp-fa="true" class="font-icon-25 fa fa-tags"></i>&nbsp;', ' ', '</p>'); ?>
    </div>


    <?php
    $is_slideshow = apply_filters('is_slideshow', false);
    $excluded = exclude_categories__excluded_categories();
    
    if( $is_slideshow ) {
        echo '<div class="nav-wrapper slide-show"><label>Slidehow Navigation</label>';
        the_post_navigation(array(
            'next_text' => '<span class="meta-nav" aria-hidden="true">' . esc_html__('Nächste Slideshow:', 'mesmerize') . '</span> ' .
                           '<span class="screen-reader-text">' . esc_html__('Nächste Slideshow', 'mesmerize') . '</span> ' .
                           '<span class="post-title">%title</span><i class="font-icon-post fa fa-angle-double-right"></i>',
            'prev_text' => '<i class="font-icon-post fa fa-angle-double-left"></i>' .
                           '<span class="meta-nav" aria-hidden="true">' . esc_html__('Vorherige Slideshow:', 'mesmerize') . '</span> ' .
                           '<span class="screen-reader-text">' . esc_html__('Vorherige Slideshow:', 'mesmerize') . '</span> ' .
                           '<span class="post-title">%title</span>',
            'in_same_term' => true
        ));
        echo '</div>';
    }
    echo '<div class="nav-wrapper posts"><label>Beitrags Navigation</label>';
    the_post_navigation(array(
        'next_text' => '<span class="meta-nav" aria-hidden="true">' . esc_html__('Nächste:', 'mesmerize') . '</span> ' .
                       '<span class="screen-reader-text">' . esc_html__('Nächster Post', 'mesmerize') . '</span> ' .
                       '<span class="post-title">%title</span><i class="font-icon-post fa fa-angle-double-right"></i>',
        'prev_text' => '<i class="font-icon-post fa fa-angle-double-left"></i>' .
                       '<span class="meta-nav" aria-hidden="true">' . esc_html__('Vorherige:', 'mesmerize') . '</span> ' .
                       '<span class="screen-reader-text">' . esc_html__('Vorheriger Post:', 'mesmerize') . '</span> ' .
                       '<span class="post-title">%title</span>',
        'excluded_terms' => $excluded
    ));
    echo '</div>';
    ?>


    <?php
    if (comments_open() || get_comments_number()):
        comments_template();
    endif;
    ?>
</div>
