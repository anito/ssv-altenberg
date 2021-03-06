<?php mesmerize_get_header(); ?>

    <div class="content blog-page">
        <div class="<?php mesmerize_page_content_wrapper_class(); ?>">
            <div class="row flex-center">
                <div class="col-xs-12 <?php mesmerize_posts_wrapper_class(); ?> page-main-column">
                    <div class="row">
                        <?php
                        if (have_posts()):
                            while (have_posts()):
                                the_post();
                                get_template_part('template-parts/content', get_post_format());
                            endwhile;
                        else:
                            get_template_part('template-parts/content', 'none');
                        endif;
                        ?>
                    </div>
                    <div class="navigation-c">
                        <?php
                        if (have_posts()):
                            mesmerize_print_pagination();
                        endif;
                        ?>
                    </div>
                </div>
                <?php // get_sidebar(); ?>
            </div>
        </div>
    </div>

<?php get_footer();
