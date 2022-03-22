<?php
/**
 * The sidebar containing the main widget area
 *
 */

if ( ! is_active_sidebar('sidebar-1') ) {
    return;
}
?>

<div class="col-xs-12 col-sm-4 col-md-3 page-sidebar-column">
    <div class="sidebar">
        <div class="sidebar-row">
            <?php
            dynamic_sidebar( "sidebar-1" );

            $teams = wbp_get_teams();

            $ssv_terms = get_the_terms($post, SSV_CATEGORY_BASE);
            $tag_terms = get_the_terms($post, 'post_tag');

            $terms = wp_parse_args($ssv_terms, $tag_terms);
            $slugs = array_map( function( $term ) {
                return $term->slug;
            }, $terms );

            $slugs = array_unique($slugs);

            foreach ( $teams as $team ) {
                if( in_array( $team->post_name, $slugs ) ) {
                    $team_id = $team->ID;
                    dynamic_sidebar( "sportspress_pages_sidebar_$team_id" );
                }
            }


            ?>
        </div>
    </div>
</div>
