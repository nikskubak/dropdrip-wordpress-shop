<?php 
	global $nm_theme_options, $nm_globals;
?>
                </div> <!-- .nm-page-wrap-inner -->
            </div> <!-- .nm-page-wrap -->
            
            <div id="nm-page-overlay" class="nm-page-overlay"></div>
            <div id="nm-widget-panel-overlay" class="nm-page-overlay"></div>
            
            <footer id="nm-footer" class="nm-footer">
                <?php
                    // Footer widgets
                    if ( is_active_sidebar( 'footer' ) ) {
                        get_template_part( 'template-parts/footer/footer', 'widgets' );
                    }
                ?>
                
                <?php 
                    // Footer bar
                    get_template_part( 'template-parts/footer/footer', 'bar' );
                ?>
            </footer>
            
            <?php 
                // Mobile menu
                get_template_part( 'template-parts/navigation/navigation', 'mobile' );
            ?>
            
            <?php
                // Cart panel
                if ( $nm_globals['cart_panel'] ) {
                    get_template_part( 'template-parts/woocommerce/cart-panel' );
                }
            ?>
            
            <?php
                // Login panel
                if ( $nm_globals['login_popup'] && ! is_user_logged_in() && ! is_account_page() ) {
                    get_template_part( 'template-parts/woocommerce/login' );
                }
			?>
            
            <div id="nm-quickview" class="clearfix"></div>
            
            <?php if ( strlen( $nm_theme_options['custom_js'] ) > 0 ) : ?>
            <script type="text/javascript">
                <?php echo $nm_theme_options['custom_js']; ?>
            </script>
            <?php endif; ?>
            
            <?php wp_footer(); // WordPress footer hook ?>
        
        </div> <!-- .nm-page-overflow -->
	</body>
</html>
