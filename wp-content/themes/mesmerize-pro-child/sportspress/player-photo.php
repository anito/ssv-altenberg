<?php
/**
 * Player Photo
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     1.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportspress_player_show_photo', 'yes' ) === 'no' ) return;

$defaults = array(
	'show_number' => get_option( 'sportspress_player_show_number', 'no' ) == 'yes' ? true : false,
	'show_name' => get_option( 'sportspress_player_show_name', 'no' ) == 'yes' ? true : false,
	'show_nationality' => get_option( 'sportspress_player_show_nationality', 'yes' ) == 'yes' ? true : false,
	'show_positions' => get_option( 'sportspress_player_show_positions', 'yes' ) == 'yes' ? true : false,
	'show_current_teams' => get_option( 'sportspress_player_show_current_teams', 'yes' ) == 'yes' ? true : false,
	'show_past_teams' => get_option( 'sportspress_player_show_past_teams', 'yes' ) == 'yes' ? true : false,
	'show_leagues' => get_option( 'sportspress_player_show_leagues', 'no' ) == 'yes' ? true : false,
	'show_seasons' => get_option( 'sportspress_player_show_seasons', 'no' ) == 'yes' ? true : false,
	'show_nationality_flags' => get_option( 'sportspress_player_show_flags', 'yes' ) == 'yes' ? true : false,
	'abbreviate_teams' => get_option( 'sportspress_abbreviate_teams', 'yes' ) === 'yes' ? true : false,
	'link_teams' => get_option( 'sportspress_link_teams', 'no' ) == 'yes' ? true : false,
);

extract( $defaults, EXTR_SKIP );

if ( ! isset( $id ) )
	$id = get_the_ID();

$player = new SP_Player( $id );

$name = $player->post->post_title;
$photo_filename = get_players_gender_photo_filename($id);

$current_teams = $player->current_teams();
if ( $current_teams ):
    $teams = array();
    foreach ( $current_teams as $team ):
        $team_name = sp_get_team_name( $team, $abbreviate_teams );
        if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
        $teams[] = $team_name;
    endforeach;
    $data[ __( 'Current Team', 'sportspress' ) ] = implode( ', ', $teams );
endif;
    
$user_id = get_user_id_by_player( $id );
$avatar = ( isset( $user_id ) ) ? get_avatar( $user_id, 200 ) : FALSE;

if ( $avatar ):
    $thumbnail = $avatar;
elseif ( has_post_thumbnail( $id ) ) :
    $thumbnail = get_the_post_thumbnail( $id, 'sportspress-fit-medium' );
else:
    $thumbnail = '<img src="/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/' . $photo_filename . '" class=" face attachment-thumbnail wp-post-image">';
endif;
?>

<div class="col-sm-12 single no-padding">
    <div class="card no-padding y-move bordered">
        <?php echo $thumbnail ?>
        <div data-type="column" class="col-padding-small col-padding-small-xs description-container">
            <h4 class="font-500"><?php echo $name ?></h4>
            <dl class="sp-player-details">
            <?php
            foreach( $data as $label => $value ):

                echo '<dt>' . $label . '</dt><dd>' . $value . '</dd>';

            endforeach;
            ?>
            </dl>
        </div>
    </div>
</div>