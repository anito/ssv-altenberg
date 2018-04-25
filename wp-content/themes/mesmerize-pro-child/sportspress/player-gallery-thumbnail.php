<?php
/**
 * Player Gallery Thumbnail
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$defaults = array(
	'id' => null,
	'icontag' => 'dt',
	'captiontag' => 'dd',
	'caption' => null,
	'size' => 'sportspress-crop-medium',
	'link_posts' => get_option( 'sportspress_link_players', 'yes' ) == 'yes' ? true : false,
);

extract( $defaults, EXTR_SKIP );

// Add player number to caption if available
$player_number = get_post_meta( $id, 'sp_number', true );
if ( '' !== $player_number )
	$caption = '<strong>' . $player_number . '</strong> ' . $caption;

// Add caption tag if has caption
if ( $captiontag && $caption )
	$caption = '<' . $captiontag . ' class="wp-caption-text gallery-caption small-3 columns' . ( '' !== $player_number ? ' has-number' : '' ) . '">' . wptexturize( $caption ) . '</' . $captiontag . '>';

if ( $link_posts )
	$caption = '<a href="' . get_permalink( $id ) . '">' . $caption . '</a>';

if ( has_post_thumbnail( $id ) )
	$thumbnail = get_the_post_thumbnail( $id );
else
    $photo_filename = get_players_gender_photo_filename($id);
	$thumbnail = '<img src="/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/' . $photo_filename . '" class=" face attachment-thumbnail wp-post-image">';
    // get generic players photo for its gender


//echo "<{$itemtag} class='gallery-item'>";
//echo "
//	<{$icontag} class='gallery-icon portrait'>"
//		. '<a href="' . get_permalink( $id ) . '">' . $thumbnail . '</a>'
//	. "</{$icontag}>";
//echo $caption;
//echo "</{$itemtag}>";
?>

<div class="col-sm-12">
    <div class="card y-move no-padding bordered">
        <?php echo $thumbnail ?>
        <div data-type="column" class="col-padding-small col-padding-small-xs description-container">
            <h4 class="font-500"><?php echo $staff_role->name ?></h4>
            <p class="small" style="font-style: italic;">
                <a href="<?=get_permalink( $id ) ?>"><?php echo $name ?></a>
            </p>
            <div class="social-icons-group" data-type="group">
                <a href="#">
                    <i class="gray fa icon fa-facebook-square small"></i>
                </a>
                <a href="#">
                    <i class="gray fa icon fa-twitter-square small"></i>
                </a>
                <a href="#">
                    <i class="gray fa icon fa-linkedin-square small"></i>
                </a>
                <a href="#">
                    <i class="gray fa icon fa-google-plus-square small"></i>
                </a>
            </div>
        </div>
    </div>
</div>
