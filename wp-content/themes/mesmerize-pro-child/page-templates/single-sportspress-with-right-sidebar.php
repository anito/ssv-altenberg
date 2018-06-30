<?php
/*
Template Name: Single Sportspress Template With Right Sidebar
*/

mesmerize_get_header(); ?>

    <div class="page-content">
        <div class="<?php mesmerize_page_content_wrapper_class(); ?>">
            <div class="row">
                <div class="sp-header-wrapper">
                    <div class="sp-header-icon">
                        <i class="colorful_peach fa fa-address-book-o icon large reverse round"></i>
                    </div>
                    <div class="sp-header-text">
                            <h4 class="sp-header-bloginfo"><?php echo bloginfo(); ?></h4>
                            <h2 class="sp-header-title"><?php do_action('sportspress_header', get_the_ID() ); ?></h2>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 page-main-column">
                    <?php
                    while (have_posts()) : the_post();
                        get_template_part('template-parts/content', 'page');
                    endwhile;
                    ?>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3 page-sidebar-column">
                    <?php mesmerize_get_sidebar('sportspress'); ?>
                </div>
            </div>
        </div>
    </div>

<?php

get_footer(); ?>
