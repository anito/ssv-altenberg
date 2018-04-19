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

if ( ! isset( $id ) )
	$id = get_the_ID();

$staff = new SP_Staff( $id );
$role = $staff->role();
$name = $staff->post_title;

if ( $role )
		$name = '<strong class="sp-staff-role">' . $role->name . '</strong> ' . $name . get_the_title( $id );


if ( has_post_thumbnail( $id ) ):
	?>
	<div class="sp-template sp-template-staff-photo sp-template-photo sp-staff-photo">
		<?php echo get_the_post_thumbnail( $id, 'sportspress-fit-medium' ); ?>
	</div>
<?php
endif;
?>
<div class="sp-template sp-template-staff-name sp-template-name sp-staff-name" style="display: inline-block;">
		<?php echo $name; ?>
</div>