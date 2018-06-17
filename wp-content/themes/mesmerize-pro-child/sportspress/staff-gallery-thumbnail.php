<?php
/**
 * Staff Gallery Thumbnail
 *
 * @author 		ThemeBoy
 * @package 	SportsPress_Staff_Directories
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$defaults = array(
	'id' => null,
	'row' => array(),
	'icontag' => 'dt',
	'captiontag' => 'dd',
	'caption' => null,
	'size' => 'thumbnail',
    'show_current_teams' => get_option( 'sportspress_staff_show_current_teams', 'yes' ) == 'yes' ? true : false,
    'link_teams' => get_option( 'sportspress_link_teams', 'no' ) == 'yes' ? true : false,
    'abbreviate_teams' => get_option( 'sportspress_abbreviate_teams', 'yes' ) === 'yes' ? true : false,
	'link_posts' => get_option( 'sportspress_link_staff', 'yes' ) == 'yes' ? true : false,
    'classes' => 'col-sm-12'
);

extract( $defaults, EXTR_SKIP );

$staff = new SP_Staff( $id );

$current_teams = $staff->current_teams();

$data = array();

// Add staff role to caption if available
$roles = get_the_terms( $id, 'sp_role' );
if ( $roles && ! is_wp_error( $roles ) ) {
    foreach ( $roles as $role ):
		$role_name[] = $role->name;
	endforeach;
	$staff_roles = array_reverse( $role_name );
	$staff_roles = implode(', ', $staff_roles );
    $name = $caption;
	$caption = '<strong>' . $staff_roles . '</strong> ' . $name;
}
// Add Team
if ( $show_current_teams && $current_teams ):
	$teams = array();
	foreach ( $current_teams as $current_team ):
		$team_name = sp_get_team_name( $current_team, $abbreviate_teams );
		if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $current_team ) . '">' . $team_name . '</a>';
		$teams[] = $team_name;
	endforeach;
	$data[ __( 'Current Team', 'sportspress' ) ] = implode( ', ', $teams );
endif;

$current_team_output = '<div class="">' .
            '<div class="sp-list-wrapper">';

foreach( $data as $label => $value ):

	$current_team_output .= '<dt>' . $label . '<dd>' . $value . '</dd>';

endforeach;

$current_team_output .= '</div></div>';

// Add caption tag if has caption
if ( $captiontag && $caption )
	$caption = '<' . $captiontag . ' class="wp-caption-text gallery-caption small-3 columns">' . wptexturize( $caption ) . '</' . $captiontag . '>';

if ( $link_posts )
	$caption = '<a class="test" href="' . get_permalink( $id ) . '">' . $caption . '</a>';

$user_id = get_user_id_by_author( $id );
$avatar = ( isset( $user_id ) ) ? get_avatar( $user_id, 200 ) : FALSE;

if ( has_post_thumbnail( $id ) ) :
    $thumbnail = get_the_post_thumbnail( $id, 'sportspress-fit-medium' );
elseif ( $avatar ):
    $thumbnail = $avatar;
else:
    $thumbnail = '<img src="/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/team-4.jpg" class=" face attachment-thumbnail wp-post-image">';
endif;
?>

<div class="<?php echo $classes ?>">
    <div class="card y-move no-padding bottom-border-color1 no-padding">
        <?php echo $thumbnail ?>
        <div data-type="column" class="col-padding-small col-padding-small-xs description-container">
            <h4 class="font-500"><?php echo $staff_roles ?></h4>
            <p class="small" style="font-style: italic;">
                <a href="<?=get_permalink( $id ) ?>"><?php echo $name ?></a>
            </p>
            <?php echo $current_team_output; ?>
        </div>
    </div>
</div>
