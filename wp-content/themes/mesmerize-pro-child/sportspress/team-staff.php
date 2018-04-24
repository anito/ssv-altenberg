<?php
/**
 * Team Staff
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version   2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! isset( $id ) )
	$id = get_the_ID();

$team = new SP_Team( $id );
$team_name = $team->post->post_title;
$members = $team->staff();
$link_staff = get_option( 'sportspress_team_link_staff', 'no' ) === 'yes' ? true : false;
$header_start = '<span class="sp-team-name"><h3>Team ' . $team_name . '</h3>';
$header_end = '</span>';
        
echo $header_start;
foreach ( $members as $staff ):
	$id = $staff->ID;
	$name = $staff->post_title;
	
	$staff = new SP_Staff( $id );
	$roles = $staff->roles();

    $output_link_staff = '<span class="sp-staff-name">' . ($link_staff ? '<a href="'. get_permalink( $id ) .'">'. $name .'</a>' : $name);
    $output_link_staff .= '</span>';
    
	echo $output_link_staff;
	if ( ! empty( $roles ) ):
		$roles = wp_list_pluck( $roles, 'name' );
		$name = '<span class="sp-staff-role">' . implode( '<span class="sp-staff-role-delimiter">/</span>', $roles ) . '</span> ' . $name;
	endif;
	sp_get_template( 'staff-photo.php', array( 'id' => $id ) );
//	sp_get_template( 'staff-details.php', array( 'id' => $id ) );
endforeach;
echo $header_end;
