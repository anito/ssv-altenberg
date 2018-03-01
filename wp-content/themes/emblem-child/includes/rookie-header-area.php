<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Display header area sections.
 */
function rookie_header_area() {
	$options = get_option( 'themeboy', array() );
	if ( array_key_exists( 'logo_url', $options ) && ! empty( $options['logo_url'] ) ) {
		$logo = $options['logo_url'];
		$logo = esc_url( set_url_scheme( $logo ) );
	}
	
	if ( ! array_key_exists( 'nav_menu_search', $options ) || $options['nav_menu_search'] ) {
		$has_search = true;
	} else {
		$has_search = false;
	}
	
	$display_header_text = display_header_text();
	
	$style_options = apply_filters( 'rookie_header_image_style_options', array(
        'background' => __( 'Background', 'rookie' ),
        'image' => __( 'Image', 'rookie' ),
    ) );

	reset( $style_options );
	$style = key( $style_options );
	
	if ( array_key_exists( 'header_image_style', $options ) && array_key_exists( $options['header_image_style'], $style_options ) ) {
		$style = $options['header_image_style'];
	}
	
	$header = get_header_image();
	
	$header_textcolor = get_header_textcolor();
	$header_textcolor = str_replace( '#', '', $header_textcolor );

	add_filter( 'rookie_header_area_sections', function() {
			return array( 'widgets', 'banner', 'branding', 'menu', 'titles' );
	}, 10 );
	$sections = apply_filters( 'rookie_header_area_sections', array(
		'widgets',
		'branding',
		'banner',
		'menu',
		'titles'
	) );
	?>
	<?php if ( $header && 'background' == $style ) { ?>
	<div class="header-area header-area-custom<?php if ( isset( $logo ) ) { ?> header-area-has-logo<?php } ?><?php if ( $has_search ) { ?> header-area-has-search<?php } ?><?php if ( $display_header_text ) { ?> header-area-has-text<?php } ?>" style="background-image: url(<?php header_image(); ?>);">
	<?php } else { ?>
	<div class="header-area<?php if ( isset( $logo ) ) { ?> header-area-has-logo<?php } ?><?php if ( $has_search ) { ?> header-area-has-search<?php } ?>">
	<?php } ?>
		<?php foreach ( $sections as $section ) { ?>
			<?php if ( 'widgets' == $section ) { ?>
				<?php if ( is_active_sidebar( 'header-1' ) ) { ?>
				<div id="tertiary" class="site-widgets" role="complementary">
					<div class="site-widget-region">
						<?php dynamic_sidebar( 'header-1' ); ?>
					</div>
				</div>
				<?php } ?>
			<?php } elseif ( 'banner' == $section && $header && 'image' == $style ) { ?>
				<div class="site-banner">
						<a class="" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<img class="site-banner-image" src="<?php header_image(); ?>" alt="<?php bloginfo( 'description' ); ?>">
						</a>
				</div><!-- .site-banner -->
			<?php } elseif ( 'branding' == $section ) { ?>
				<div class="site-branding<?php if ( ! isset( $logo ) && ! $display_header_text ) { ?> site-branding-empty<?php } ?>">
					<div class="site-identity">
						<?php if ( isset( $logo ) ) { ?>
						<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo $logo; ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
						<?php } ?>
						<?php if ( $display_header_text ) { ?>
						<hgroup style="color: #<?php echo $header_textcolor; ?>">
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
						</hgroup>
						<?php } ?>
					</div>
				</div><!-- .site-branding -->
			<?php } elseif ( 'menu' == $section ) { ?>
				<div class="site-menu">
					<nav id="site-navigation" class="main-navigation hidemobile" role="navigation">
						<button class="menu-toggle menu-btn" data-menu-target="panel"><span class="dashicons dashicons-menu"></span></button>
						<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
						<?php if ( $has_search ) get_search_form(); ?>
					</nav><!-- #site-navigation -->
				</div>
			<?php } elseif ( 'titles' == $section ) { ?>
				<div class="site-titles">
					<hgroup style="color: #<?php echo $header_textcolor; ?>">
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
						</hgroup><!-- #site-titles -->
				</div>
			<?php } else { ?>
				<?php do_action( 'rookie_header_area_section_' . $section ); ?>
			<?php } ?>
		<?php } ?>
	</div>
	<?php
}