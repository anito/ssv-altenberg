<?php
/**
 * Team Staff
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     1.9.13
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! isset( $id ) )
	$id = get_the_ID();

$team = new SP_Team( $id );
$members = $team->staff();

?>


<?php
foreach ( $members as $staff ):
	$id = $staff->ID;
	$name = $staff->post_title;
	
	$staff = new SP_Staff( $id );
	$role = $staff->role();
	
	if ( $role )
		$name = '<span class="sp-staff-role">' . $role->name . '</span> ' . $name;
	?>
	<div class="sp-staff-wrapper">
	<?php
	sp_get_template( 'staff-photo.php', array( 'id' => $id ) );
	sp_get_template( 'staff-details.php', array( 'id' => $id ) );
	sp_get_template( 'staff-excerpt.php', array( 'id' => $id ) );
	sp_get_template( 'staff-contacts.php', array( 'id' => $id ), '', SP_STAFF_DIRECTORIES_DIR . 'templates/' );
?>
	</div>
<?php endforeach;

