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
	'link_posts' => get_option( 'sportspress_link_staff', 'yes' ) == 'yes' ? true : false,
);

extract( $defaults, EXTR_SKIP );

// Add staff role to caption if available
$roles = get_the_terms( $id, 'sp_role' );
if ( $roles && ! is_wp_error( $roles ) ) {
	$staff_role = array_shift( $roles );
    $name = $caption;
	$caption = '<strong>' . $staff_role->name . '</strong> ' . $name;
}

// Add caption tag if has caption
if ( $captiontag && $caption )
	$caption = '<' . $captiontag . ' class="wp-caption-text gallery-caption small-3 columns">' . wptexturize( $caption ) . '</' . $captiontag . '>';

if ( $link_posts )
	$caption = '<a href="' . get_permalink( $id ) . '">' . $caption . '</a>';

if ( has_post_thumbnail( $id ) )
	$thumbnail = get_the_post_thumbnail( $id, $size, array('class' => 'face'));
else
	$thumbnail = '<img src="/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/team-2.jpg" class=" face attachment-thumbnail wp-post-image">';

//echo "<{$itemtag} class='gallery-item col-sm-6 col-md-3'>";
//echo "
//	<{$icontag} class='card y-move no-padding bordered'>"
//		. '<a href="' . get_permalink( $id ) . '">' . $thumbnail . '</a>'
//	. "</{$icontag}>";
//echo $caption;
//echo "</{$itemtag}>";
//
//
//
//if ( has_post_thumbnail( $id ) )
//	$thumbnail = get_the_post_thumbnail( $id, $size );
//else
//	$thumbnail = '<img width="" height="" src="/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/team-2.jpg" class="face attachment-thumbnail wp-post-image">';
//
//echo "<{$itemtag} class='gallery-item'>";
//echo "
//	<{$icontag} class='gallery-icon portrait'>"
//		. '<a href="' . get_permalink( $id ) . '">' . $thumbnail . '</a>'
//	. "</{$icontag}>";
//echo $caption;
//echo "</{$itemtag}>";
?>

<div class="col-sm-12 col-md-3">
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
