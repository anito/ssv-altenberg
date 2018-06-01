<?php
/**
 * Staff Shortcode
 *
 * @author 		Axel Nitzschner
 * @category 	Shortcodes
 * @version   1.0
 */
class Shortcode_Staff_Advanced {

	/**
	 * Output the staff shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		if ( ! isset( $atts['id'] ) && isset( $atts[0] ) && is_numeric( $atts[0] ) )
			$atts['id'] = $atts[0];
		
		sp_get_template('wrapper-start.php');
		sp_get_template( 'staff-photo.php', $atts );
		sp_get_template( 'staff-details.php', $atts );
		sp_get_template( 'staff-contacts.php', $atts, '', SP_STAFF_DIRECTORIES_DIR . 'templates/' );
		sp_get_template('wrapper-end.php');
	}
}
