<?php
	global $nm_theme_options;
			
	// Logo URL
	if ( isset( $nm_theme_options['logo'] ) && strlen( $nm_theme_options['logo']['url'] ) > 0 ) {
		$logo_href = ( is_ssl() ) ? str_replace( 'http://', 'https://', $nm_theme_options['logo']['url'] ) : $nm_theme_options['logo']['url'];
	} else {
		$logo_href = NM_THEME_URI . '/assets/img/logo@2x.png';
	}
    
    // Alternative logo
    $has_alt_logo = false;    
    if ( $nm_theme_options['alt_logo'] ) {
        $has_alt_logo = true;
        
        // Logo URL
        if ( isset( $nm_theme_options['alt_logo_image'] ) && strlen( $nm_theme_options['alt_logo_image']['url'] ) > 0 ) {
            $alt_logo_href = ( is_ssl() ) ? str_replace( 'http://', 'https://', $nm_theme_options['alt_logo_image']['url'] ) : $nm_theme_options['alt_logo_image']['url'];
        } else {
            $alt_logo_href = NM_THEME_URI . '/assets/img/logo-light@2x.png';
        }
    }
?>
<div class="nm-header-logo">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <img src="<?php echo esc_url( $logo_href ); ?>" class="nm-logo" alt="<?php bloginfo( 'name' ); ?>">
        <?php if ( $has_alt_logo ) : ?>
        <img src="<?php echo esc_url( $alt_logo_href ); ?>" class="nm-alt-logo" alt="<?php bloginfo( 'name' ); ?>">
        <?php endif; ?>
    </a>
</div>