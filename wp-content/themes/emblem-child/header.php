<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Rookie
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="initial-scale=1.0, maximum-scale=3.0, user-scalable=no, width=device-width">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<!-- Global Site Tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-107307404-1"></script>
</head>

<body <?php body_class(); ?>>
<div class="sp-header">
		<?php do_action( 'sportspress_header' ); ?>
</div>
<nav id="site-navigation-mobile" class="main-navigation-mobile" role="navigation">
		<button class="menu-toggle">
				<a class="dashicons dashicons-menu menu-btn" data-menu-target="panel"></a>
				<?php $logo = logo(); if ( $logo ) { ?>
				<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo $logo; ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
				<?php } ?>
				<span id="menu-search"><?php if ( hasSearch() ) get_search_form(); ?></span>
				<a class="dashicons dashicons-search menu-search"></a>
		</button><!-- #site-navigation -->
</nav>
<nav class="pushit pushit-left">
		<div id="panel" class="wptouch-menu show-hide-menu">
				<div class="wptouch-menu menu categories">
						<h3><?php _e( 'Pages' ); ?></h3>
						<?php wptouch_show_menu(); ?>
				</div>

			<?php if ( function_exists( 'wptouch_fdn_show_login' ) && wptouch_fdn_show_login() ) { ?>
				<ul class="menu-tree login-link">
					<li>
					<?php if ( !is_user_logged_in() ) { ?>
					<a class="login-toggle tappable" href="<?php echo wp_login_url( esc_url_raw( $_SERVER['REQUEST_URI'] ) ); ?>">
						<i class="wptouch-icon-key"></i> Login
					</a>
				<?php } else { ?>
					<a href="<?php echo wp_logout_url( esc_url_raw( $_SERVER['REQUEST_URI'] ) ); ?>" class="tappable" title="<?php _e( 'Logout', 'wptouch-pro' ); ?>">
						<i class="wptouch-icon-user"></i>
						<?php _e( 'Logout', 'wptouch-pro' ); ?>
					</a>
				<?php } ?>
					</li>
				</ul>
			<?php } ?>
		</div>
</nav>
<div id="page" class="hfeed site page-wrapper">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'rookie' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<?php rookie_header_area(); ?>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
		<?php do_action( 'rookie_before_template' ); ?>
