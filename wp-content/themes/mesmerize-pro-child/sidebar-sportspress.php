<?php

if( !is_active_sidebar('mesmerize_pages_sidebar') )
{
    return;
}

?>

<div class="sidebar">
    <div class="sidebar-row">
        <?php
        $post_type = get_post_type();
        if( 'sp_event' === get_post_type() ) {
            $teams = get_post_meta( $post->ID, 'sp_team' );
            $team_id = array_shift($teams);
            if( $team_id ) {
                
                $team = get_post($team_id);
                if( is_object( $team ) ) {
                    
                    $name = $team->post_name;
                    $type = $team->post_type;
                    
                    if( 'sp_team' === $type ) // Only Team vs. Team Modus
                        dynamic_sidebar( "sportspress_pages_sidebar_$team_id" );
                }
            }
        }
        dynamic_sidebar( "sportspress_pages_sidebar" );
        ?>
    </div>
</div>