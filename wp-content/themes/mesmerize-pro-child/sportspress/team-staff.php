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
$header = '<span class="sp-team-name"><h3>Team ' . $team_name . '</h3></span>';
        
foreach ( $members as $staff ):
	$id = $staff->ID;
	sp_get_template( 'staff-photo.php', array( 'id' => $id ) );
endforeach;
