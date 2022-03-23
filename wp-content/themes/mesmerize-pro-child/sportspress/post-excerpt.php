<?php
/**
 * Post Excerpt
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     2.6.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! isset( $id ) )
    $id = get_the_ID();

$post = get_post( $id );
$excerpt = $post->post_excerpt;
$header = wbp_is_player( $id ) ? HEADER_PLAYER_EXCERPT : is_staff( $id ) ? HEADER_STAFF_EXCERPT : '';
if ( $excerpt ) {
    ?>
	<div class="sp-excerpt">
        <?php echo $header . $excerpt; ?>
    </div>
<?php
}