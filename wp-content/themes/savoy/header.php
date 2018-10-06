<?php
	global $nm_theme_options, $nm_globals, $nm_body_class;
	
    // Page load transition class
    $nm_body_class .= ' nm-page-load-transition-' . $nm_theme_options['page_load_transition'];
	
	// CSS animations preload class
	$nm_body_class .= ' nm-preload';
	
	// Top bar class
    $nm_body_class .= ( $nm_theme_options['top_bar'] ) ? ' has-top-bar' : '';
    
    // Header: Layout slug
    $header_slugs = array( 'default' => '', 'menu-centered' => '', 'centered' => '', 'stacked' => '', 'stacked-centered' => '' );
    $nm_globals['header_layout'] = ( isset( $_GET['header'] ) && isset( $header_slugs[$_GET['header']] ) ) ? $_GET['header'] : $nm_theme_options['header_layout'];
    
    // WooCommerce variables
    $woocommerce_activated = nm_woocommerce_activated();

	// Header: Classes
	$nm_body_class .= ( $nm_theme_options['header_fixed'] ) ? ' header-fixed' : '';
    $nm_body_class .= ' header-mobile-' . $nm_theme_options['header_layout_mobile'];
    
    // Header: Classes - Transparency
    $page_header_transparency = ( $post ) ? get_post_meta( $post->ID, 'nm_page_header_transparency', true ) : array();
    if ( ! empty( $page_header_transparency ) ) {
        $nm_body_class .= ' header-transparency header-transparency-' . $page_header_transparency;
    } else if ( $nm_theme_options['header_transparency'] ) {
        if ( is_front_page() ) {
            $nm_body_class .= ( $nm_theme_options['header_transparency_homepage'] !== '0' ) ? ' header-transparency header-transparency-' . $nm_theme_options['header_transparency_homepage'] : '';
        } else if ( $woocommerce_activated ) {
            if ( is_shop() ) {
                $nm_body_class .= ( $nm_theme_options['header_transparency_shop'] !== '0' ) ? ' header-transparency header-transparency-' . $nm_theme_options['header_transparency_shop'] : '';
            } else if ( is_product_taxonomy() ) {
                $nm_body_class .= ( $nm_theme_options['header_transparency_shop_categories'] !== '0' ) ? ' header-transparency header-transparency-' . $nm_theme_options['header_transparency_shop_categories'] : '';
            }
        }
    }
    
    // Header: Classes - Border
	if ( is_front_page() ) {
		$nm_body_class .= ( isset( $_GET['header_border'] ) ) ? ' header-border-1' : ' header-border-' . $nm_theme_options['home_header_border'];
	} elseif ( $woocommerce_activated && ( is_shop() || is_product_taxonomy() ) ) {
		$nm_body_class .= ' header-border-' . $nm_theme_options['shop_header_border'];
	} else {
		$nm_body_class .= ' header-border-' . $nm_theme_options['header_border'];
	}
	
    // Widget panel class
    $nm_body_class .= ' widget-panel-' . $nm_theme_options['widget_panel_color'];
    
    // WooCommerce: login
    if ( $woocommerce_activated && ! is_user_logged_in() && is_account_page() ) {
        $nm_body_class .= ' nm-woocommerce-account-login';
    }

    // Sticky footer class
	$sticky_footer_class = ' footer-sticky-' . $nm_theme_options['footer_sticky'];
?>
<!DOCTYPE html>

<html <?php language_attributes(); ?> class="<?php echo esc_attr( $sticky_footer_class ); ?>">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        
        <link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        	
		<?php wp_head(); ?>
    </head>
    
	<body <?php body_class( esc_attr( $nm_body_class ) ); ?>>
        <?php if ( $nm_theme_options['page_load_transition'] ) : ?>
        <div id="nm-page-load-overlay" class="nm-page-load-overlay"></div>
        <?php endif; ?>
        
        <div class="nm-page-overflow">
            <div class="nm-page-wrap">
                <?php
                    // Top bar
                    if ( $nm_theme_options['top_bar'] ) {
                        get_template_part( 'template-parts/header/header', 'top-bar' );
                    }
                ?>
                            
                <div class="nm-page-wrap-inner">
                    <?php
                        // Header
                        get_template_part( 'template-parts/header/header', 'content' );
                    ?>
