<?php
/**
 * Post Excerpt
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     1.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportspress_team_show_details', 'no' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$post = get_post( $id );
$content = $post->post_content;

if ( $content ) {
	?>
	<p class="sp-content"><?php echo $content; ?></p>
	<?php
}