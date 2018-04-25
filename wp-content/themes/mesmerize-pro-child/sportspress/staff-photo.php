<?php
/**
 * Staff Photo
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     1.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( get_option( 'sportspress_staff_show_photo', 'yes' ) === 'no' ) return;

$defaults = array(
	'id' =>  null,
	'row' => array(),
	'icontag' => 'dt',
	'captiontag' => 'dd',
	'caption' => null,
	'size' => 'thumbnail',
	'link_posts' => get_option( 'sportspress_link_staff', 'yes' ) == 'yes' ? true : false,
    'link_phone' => get_option( 'sportspress_staff_link_phone', 'yes' ) == 'yes' ? true : false,
	'link_email' => get_option( 'sportspress_staff_link_email', 'yes' ) == 'yes' ? true : false,
    'show_nationality' => get_option( 'sportspress_staff_show_nationality', 'yes' ) == 'yes' ? true : false,
	'show_current_teams' => get_option( 'sportspress_staff_show_current_teams', 'yes' ) == 'yes' ? true : false,
	'show_past_teams' => get_option( 'sportspress_staff_show_past_teams', 'yes' ) == 'yes' ? true : false,
	'show_nationality_flags' => get_option( 'sportspress_staff_show_flags', 'yes' ) == 'yes' ? true : false,
	'link_teams' => get_option( 'sportspress_link_teams', 'no' ) == 'yes' ? true : false,
	'abbreviate_teams' => get_option( 'sportspress_abbreviate_teams', 'yes' ) === 'yes' ? true : false,
    'classes' => 'sp-template-details'
);

extract( $defaults, EXTR_SKIP );

if ( ! isset( $id ) )
	$id = get_the_ID();
    $post = get_post( $id );
    $name = $post->post_title;
    
$staff = new SP_Staff( $id );

$phone = $staff->phone;
$email = $staff->email;
$nationalities = $staff->nationalities();
$current_teams = $staff->current_teams();
$past_teams = $staff->past_teams();
    
$data = array();

/*
 * staff name
 */
$link_staff = get_option( 'sportspress_team_link_staff', 'no' ) === 'yes' ? true : false;
$staff_name = '<span class="sp-staff-name">' . ($link_staff ? '<a href="'. get_permalink( $id ) .'">'. $name .'</a>' : $name) . '</span>';

/*
 * staff contacts
 */
if ( $phone !== '' ):
	if ( $link_phone ) $phone = '<a href="tel:' . $phone . '">' . $phone . '</a>';
//	$data[ __( 'Phone', 'sportspress' ) ] = $phone;
endif;

if ( $email !== '' ):
	if ( $link_email ) $email = '<a href="mailto:' . $email . '">' . $email . '</a>';
//	$data[ __( 'Email', 'sportspress' ) ] = $email;
endif;

/*
 * staff details
 */
if ( $show_nationality && $nationalities && is_array( $nationalities ) ):
	$values = array();
	foreach ( $nationalities as $nationality ):
		if ( 2 == strlen( $nationality ) ):
			$legacy = SP()->countries->legacy;
			$nationality = strtolower( $nationality );
			$nationality = sp_array_value( $legacy, $nationality, null );
		endif;
		$country_name = sp_array_value( $countries, $nationality, null );
		$values[] = $country_name ? ( $show_nationality_flags ? '<img src="' . plugin_dir_url( SP_PLUGIN_FILE ) . 'assets/images/flags/' . strtolower( $nationality ) . '.png" alt="' . $nationality . '"> ' : '' ) . $country_name : '&mdash;';
	endforeach;
	$data[ __( 'Nationality', 'sportspress' ) ] = implode( '<br>', $values );
endif;

if ( $show_current_teams && $current_teams ):
	$teams = array();
	foreach ( $current_teams as $team ):
		$team_name = sp_get_team_name( $team, $abbreviate_teams );
		if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
		$teams[] = $team_name;
	endforeach;
	$data[ __( 'Current Team', 'sportspress' ) ] = implode( ', ', $teams );
endif;

if ( $show_past_teams && $past_teams ):
	$teams = array();
	foreach ( $past_teams as $team ):
		$team_name = sp_get_team_name( $team, $abbreviate_teams );
		if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
		$teams[] = $team_name;
	endforeach;
	$data[ __( 'Past Teams', 'sportspress' ) ] = implode( ', ', $teams );
endif;

//$data = apply_filters( 'sportspress_staff_details', $data, $id );

$output = '<div class="">' .
            '<div class="sp-list-wrapper">';

foreach( $data as $label => $value ):

	$output .= '<dt>' . $label . '<dd>' . $value . '</dd>';

endforeach;

$output .= '</div></div>';

// Add staff role to caption if available
$roles = get_the_terms( $id, 'sp_role' );
if ( $roles && ! is_wp_error( $roles ) ) {
    foreach( $roles as $role ) {
        $staff_role[] = $role->name;
    }
//	$staff_role = array_shift( $roles );
	$staff_roles = implode( ', ', $staff_role );
	$caption = '<strong>' . $staff_roles . '</strong> ' . $name;
//	$caption = '<strong>' . $staff_role->name . '</strong> ' . $name;
}

// Add caption tag if has caption
if ( $captiontag && $caption )
	$caption = '<' . $captiontag . ' class="wp-caption-text gallery-caption small-3 columns">' . wptexturize( $caption ) . '</' . $captiontag . '>';



if ( has_post_thumbnail( $id ) )
	$thumbnail = get_the_post_thumbnail( $id, $size, array('class' => 'face'));
else
	$thumbnail = '<img src="/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/team-2.jpg" class=" face attachment-thumbnail wp-post-image">';

?>

<div class="<?php echo $classes ?>">
    <div class="card bottom-border-color1 no-padding no-shadow col-sm-card">
        <?php echo $thumbnail ?>
        <div data-type="column" class="col-padding-small col-padding-small-xs description-container">
            <h4 class="font-500"><?php echo $staff_roles ?></h4>
            <h4><?php echo $staff_name ?></h4>
            <?php echo $output; ?>
        </div>
    </div>
</div>