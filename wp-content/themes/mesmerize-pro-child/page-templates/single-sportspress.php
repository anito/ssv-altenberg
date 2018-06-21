<?php
/*
Template Name: Single Sportspress Template
*/

mesmerize_get_header(); ?>

    <div class="page-content">
        <div class="<?php mesmerize_page_content_wrapper_class(); ?> page-main-column">
            <div class="sp-header-wrapper">
                <div class="sp-header-icon">
                    <i class="colorful_peach fa fa-address-book-o icon large reverse round"></i>
                </div>
                <div class="sp-header-text">
                        <h4 class="sp-header-bloginfo"><?php echo bloginfo(); ?></h4>
                        <h2 class="sp-header-title"><?php do_action('sportspress_header', get_the_ID() ); ?></h2>
                </div>
            </div>
            <?php
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', 'page');
            endwhile;
            ?>
        </div>
    </div>

<?php

get_footer(); ?>
