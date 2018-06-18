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
$header_for_players = is_player( $id ) ? HEADER_PLAYER_EXCERPT : '';
$post = get_post( $id );
$excerpt = $post->post_excerpt;
if ( $excerpt ):
    ?>
	<div class="sp-excerpt">
        <?php echo $header_for_players . $excerpt; ?>
    </div>
<?php
endif;