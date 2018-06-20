<?php
/**
 * Post Excerpt
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     1.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$id = get_the_ID();
$post = get_post( $id );
$header = is_player( $id ) ? HEADER_PLAYER_EXCERPT : is_staff( $id ) ? HEADER_STAFF_EXCERPT : '';
$excerpt = $post->post_excerpt;
if ( $excerpt ):
    ?>
	<div class="sp-excerpt">
        <?php echo $header . $excerpt; ?>
    </div>
<?php
endif;