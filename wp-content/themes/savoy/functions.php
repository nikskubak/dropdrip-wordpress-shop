<?php

	/* Constants & Globals
	==================================================================================================== */
    
	// Uncomment to include un-minified JavaScript files
	//define( 'NM_SCRIPT_DEBUG', TRUE );
	
	// Constants: Folder directories/uri's
	define( 'NM_THEME_DIR', get_template_directory() );
	define( 'NM_DIR', get_template_directory() . '/includes' );
	define( 'NM_THEME_URI', get_template_directory_uri() );
	define( 'NM_URI', get_template_directory_uri() . '/includes' );
	
	// Constant: Framework namespace
	define( 'NM_NAMESPACE', 'nm-framework' );
	
	// Constant: Theme version
	define( 'NM_THEME_VERSION', '2.0.2' );
	
	// Global: Theme options
	global $nm_theme_options;
	
	// Global: Page includes
	global $nm_page_includes;
	$nm_page_includes = array();
	
	// Global: <body> class
	global $nm_body_class;
	$nm_body_class = '';
	
	// Global: Theme globals
	global $nm_globals;
	$nm_globals = array();
	
    // Globals: WooCommerce - Shop search (keep above "Includes")
    $nm_globals['shop_search_enabled']  = false;
    $nm_globals['shop_search_layout']   = 'shop';
    
    // Globals: WooCommerce - Shop header
    $nm_globals['shop_header_centered'] = false;

	// Global: WooCommerce - "Product Slider" shortcode loop
	$nm_globals['product_slider_loop'] = false;
	
	// Global: WooCOmmerce - Shop image lazy-loading
	$nm_globals['shop_image_lazy_loading'] = false;
	
    // Globals: WooCommerce - Custom variation controls
    $nm_globals['pa_color_slug'] = sanitize_title( apply_filters( 'nm_color_attribute_slug', 'color' ) );
    $nm_globals['pa_variation_controls'] = array(
        'color' => esc_html__( 'Color', 'nm-framework-admin' ),
        'size'  => esc_html__( 'Label', 'nm-framework-admin' )
    );
    $nm_globals['pa_cache'] = array();
    
    
    
    /* Admin localisation (must be placed before admin includes)
    ==================================================================================================== */
    
    if ( defined( 'NM_ADMIN_LOCALISATION' ) && is_admin() ) {
        //$language_dir = apply_filters( 'nm_admin_languages_dir', NM_DIR . '/options/ReduxCore/languages' );
        $language_dir = apply_filters( 'nm_admin_languages_dir', NM_THEME_DIR . '/languages/admin' );
        
        load_theme_textdomain( 'nm-framework-admin', $language_dir );
        load_theme_textdomain( 'redux-framework', $language_dir );
    }
    
    
    
    /* WP Rocket: Deactivate WooCommerce refresh cart fragments cache: https://docs.wp-rocket.me/article/1100-optimize-woocommerce-get-refreshed-fragments
	==================================================================================================== */
    
    $wpr_cart_fragments_cache = apply_filters( 'nm_wpr_cart_fragments_cache', false );
    if ( ! $wpr_cart_fragments_cache ) {
        add_filter( 'rocket_cache_wc_empty_cart', '__return_false' );
    }
    
    
    
    /* Redux theme options framework
	==================================================================================================== */
	
	if ( ! class_exists( 'ReduxFramework' ) ) {
		require_once( NM_DIR . '/options/ReduxCore/framework.php' );
        require_once( NM_DIR . '/options/customizer.php' );
        
        if ( is_admin() ) {
            // Remove dashboard widget
            function nm_redux_remove_dashboard_widget() {
                remove_meta_box( 'redux_dashboard_widget', 'dashboard', 'side' );
            }
            add_action( 'wp_dashboard_setup', 'nm_redux_remove_dashboard_widget', 100 );
        }
	}
		
	if ( ! isset( $redux_demo ) ) {
		require( NM_DIR . '/options/options-config.php' );
	}
	
	// Get theme options
	$nm_theme_options = get_option( 'nm_theme_options' );
	
	// Is the theme options array saved?
	if ( ! $nm_theme_options ) {
		// Save default options array
		require( NM_DIR . '/options/default-options.php' );
	}
    
    do_action( 'nm_theme_options_set' );
    
    
    
	/* Includes
	==================================================================================================== */        	
    
    // Custom CSS
    require( NM_DIR . '/custom-styles.php' );

	// Helper functions
	require( NM_DIR . '/helpers.php' );
	
	// Admin meta
	require( NM_DIR . '/admin-meta.php' );
	
	// Visual composer
	require( NM_DIR . '/visual-composer/init.php' );
	
	if ( nm_woocommerce_activated() ) {
        // WooCommerce: Wishlist
		$nm_globals['wishlist_enabled'] = class_exists( 'NM_Wishlist' );
        
		// WooCommerce: Functions
		include( NM_DIR . '/woocommerce/woocommerce-functions.php' );
        // WooCommerce: Template functions
		include( NM_DIR . '/woocommerce/woocommerce-template-functions.php' );
        // WooCommerce: Attribute functions
		if ( $nm_theme_options['shop_filters_custom_controls'] || $nm_theme_options['product_custom_controls'] ) {
            include( NM_DIR . '/woocommerce/woocommerce-attribute-functions.php' );
        }
		
		// WooCommerce: Quick view
		if ( $nm_theme_options['product_quickview'] ) {
			$nm_page_includes['quickview'] = true;
			include( NM_DIR . '/woocommerce/quickview.php' );
		}
		
		// WooCommerce: Shop search
        $nm_globals['shop_search_layout'] = ( isset( $_GET['search_layout'] ) ) ? $_GET['search_layout'] : $nm_theme_options['shop_search'];
        if ( $nm_globals['shop_search_layout'] !== '0' ) {
			$nm_globals['shop_search_enabled'] = true;
            
            include( NM_DIR . '/woocommerce/search.php' );
		} else {
            $nm_globals['shop_search_enabled'] = false;
        }
	}
    
    
    
    /* Admin includes
	==================================================================================================== */
    
	if ( is_admin() ) {
        // TGM plugin activation
		require( NM_DIR . '/tgmpa/config.php' );
        
        // Theme setup wizard
        require_once( NM_DIR . '/setup/nm-setup.php' );
        
        if ( nm_woocommerce_activated() ) {
			// WooCommerce: Product details
			include( NM_DIR . '/woocommerce/admin/admin-product-details.php' );
			// WooCommerce: Product categories
			include( NM_DIR . '/woocommerce/admin/admin-product-categories.php' );
            // WooCommerce: Product attributes
			if ( $nm_theme_options['shop_filters_custom_controls'] || $nm_theme_options['product_custom_controls'] ) {
                include( NM_DIR . '/woocommerce/admin/admin-product-attributes.php' );
            }
		}
	}
	
    
	
	/* Globals (requires includes)
	==================================================================================================== */
    
    // Globals: Login link
    $nm_globals['login_popup'] = false;
    
    // Globals: Cart link/panel
	$nm_globals['cart_link']   = false;
	$nm_globals['cart_panel']  = false;

    // Globals: Shop filters popup
    $nm_globals['shop_filters_popup'] = false;

	// Globals: Shop filters scrollbar
	$nm_globals['shop_filters_scrollbar'] = false;
	//$nm_globals['shop_filters_scrollbar_custom']   = false;
	
    // Globals: Shop search
    $nm_globals['shop_search_header']   = false;
    $nm_globals['shop_search']          = false;
    $nm_globals['shop_search_popup']    = false;

	if ( nm_woocommerce_activated() ) {
		// Global: Shop page id
		$nm_globals['shop_page_id'] = ( ! empty( $_GET['shop_page'] ) ) ? intval( $_GET['shop_page'] ) : wc_get_page_id( 'shop' );
		
		// Globals: Login link
		$nm_globals['login_popup'] = ( $nm_theme_options['menu_login_popup'] ) ? true : false;
		
		// Global: Cart link/panel
		if ( $nm_theme_options['menu_cart'] != '0' ) {
			$nm_globals['cart_link'] = true;
			
			// Is mini cart panel enabled?
			if ( $nm_theme_options['menu_cart'] != 'link' ) {
				$nm_globals['cart_panel'] = true;
			}
		}
		
        // Globals: Shop filters popup
        if ( isset( $_GET['filters_popup'] ) || $nm_theme_options['shop_filters'] == 'popup' ) {
            $nm_globals['shop_filters_popup'] = true;
        }
        
		// Globals: Shop filters scrollbar
        if ( $nm_theme_options['shop_filters_scrollbar'] && $nm_theme_options['shop_filters'] == 'header' ) { // Only enable scrollbars for shop-header filters
			$nm_globals['shop_filters_scrollbar'] = true;
		}
        
        // Globals: Shop search
        if ( $nm_globals['shop_search_enabled'] ) {
            if ( $nm_globals['shop_search_layout'] === 'header' ) {
                $nm_globals['shop_search_header'] = true;
            } else {
                if ( $nm_globals['shop_filters_popup'] ) {
                    $nm_globals['shop_search_popup'] = true; // Show search in filters pop-up
                } else {
                    $nm_globals['shop_search'] = true; // Show search in shop header
                }
            }
        }
        
        // Globals: Product gallery zoom
        $nm_globals['product_image_hover_zoom'] = ( $nm_theme_options['product_image_hover_zoom'] || isset( $_GET['zoom'] ) );
	}
	
	
	
	/* Theme Support
	==================================================================================================== */

	if ( ! function_exists( 'nm_theme_support' ) ) {
		function nm_theme_support() {
			global $nm_theme_options;
            
            // Let WordPress manage the document title (no hard-coded <title> tag in the document head)
            add_theme_support( 'title-tag' );
			
			// Add menu support
			add_theme_support( 'menus' );
			
			// Enables post and comment RSS feed links to head
			add_theme_support( 'automatic-feed-links' );
			
			// Add thumbnail theme support
			add_theme_support( 'post-thumbnails' );
            
            // WooCommerce
			add_theme_support( 'woocommerce' );
            add_theme_support( 'wc-product-gallery-slider' );
            if ( $nm_theme_options['product_image_zoom'] ) {
                add_theme_support( 'wc-product-gallery-lightbox' );
            }
            /*if ( $nm_globals['product_image_hover_zoom'] ) {
                add_theme_support( 'wc-product-gallery-zoom' );
            }*/
            
			// Add image sizes
			/*add_image_size( 'nm_large', 700, '', true );
			add_image_size( 'nm_medium', 220, '', true );
			add_image_size( 'nm_small', 140, '', true );
			add_image_size( 'nm_blog_list', 940, '', true );*/
            
			// Localisation
			// WordPress language directory: wp-content/languages/theme-name/en_US.mo
			load_theme_textdomain( 'nm-framework', trailingslashit( WP_LANG_DIR ) . 'nm-framework' );
			// Child theme language directory: wp-content/themes/child-theme-name/languages/en_US.mo
			load_theme_textdomain( 'nm-framework', get_stylesheet_directory() . '/languages' );
			// Theme language directory: wp-content/themes/theme-name/languages/en_US.mo
			load_theme_textdomain( 'nm-framework', NM_THEME_DIR . '/languages' );
		}
	}
	add_action( 'after_setup_theme', 'nm_theme_support' );
	
	// Maximum width for media
	if ( ! isset( $content_width ) ) {
		$content_width = 1220; // Pixels
	}
	
	
    
	/* Styles
	==================================================================================================== */
	
	function nm_styles() {
		global $nm_theme_options, $nm_globals;
		
        // Register third-party styles
        wp_register_style( 'nm-animate', NM_THEME_URI . '/assets/css/third-party/animate.css', array(), '1.0', 'all' );
        
		// Enqueue third-party styles
		wp_enqueue_style( 'normalize', NM_THEME_URI . '/assets/css/third-party/normalize.css', array(), '3.0.2', 'all' );
		wp_enqueue_style( 'slick-slider', NM_THEME_URI . '/assets/css/third-party/slick.css', array(), '1.5.5', 'all' );
		wp_enqueue_style( 'slick-slider-theme', NM_THEME_URI . '/assets/css/third-party/slick-theme.css', array(), '1.5.5', 'all' );
        wp_enqueue_style( 'magnific-popup', NM_THEME_URI . '/assets/css/third-party/magnific-popup.css', array(), '0.9.7', 'all' );
		if ( $nm_theme_options['font_awesome'] ) {
            wp_enqueue_style( 'font-awesome', '//stackpath.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', array(), false, 'all' );
		}
		
		// Theme styles: Grid (enqueue before shop styles)
		wp_enqueue_style( 'nm-grid', NM_THEME_URI . '/assets/css/grid.css', array(), NM_THEME_VERSION, 'all' );
		
		// WooCommerce styles		
		if ( nm_woocommerce_activated() ) {
            if ( is_cart() ) {
                // Widget panel: Disable on "Cart" page
                $nm_globals['cart_panel'] = false;
            } else if ( is_checkout() ) {
                // Widget panel: Disable on "Checkout" page
                $nm_globals['cart_panel'] = false;
            }
			
			wp_enqueue_style( 'selectod', NM_THEME_URI . '/assets/css/third-party/selectod.css', array(), '3.8.1', 'all' );
			wp_enqueue_style( 'nm-shop', NM_THEME_URI . '/assets/css/shop.css', array(), NM_THEME_VERSION, 'all' );
		}
		
		// Theme styles
		wp_enqueue_style( 'nm-icons', NM_THEME_URI . '/assets/css/font-icons/theme-icons/theme-icons.css', array(), NM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'nm-core', NM_THEME_URI . '/style.css', array(), NM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'nm-elements', NM_THEME_URI . '/assets/css/elements.css', array(), NM_THEME_VERSION, 'all' );
	}
	add_action( 'wp_enqueue_scripts', 'nm_styles', 99 );
	
	
    
	/* Scripts
	==================================================================================================== */
	
	function nm_scripts() {
		if ( ! is_admin() ) {
			global $nm_theme_options, $nm_globals, $nm_page_includes;
			
			
			// Script path and suffix setup (debug mode loads un-minified scripts)
			if ( defined( 'NM_SCRIPT_DEBUG' ) && NM_SCRIPT_DEBUG ) {
				$script_path = NM_THEME_URI . '/assets/js/dev/';
				$suffix = '';
			} else {
				$script_path = NM_THEME_URI . '/assets/js/';
				$suffix = '.min';
			}
            
            
            // Register scripts
            wp_register_script( 'nm-masonry', NM_THEME_URI . '/assets/js/plugins/masonry.pkgd.min.js', array(), '4.2.1', true ); // Note: Using "nm-" prefix so the included WP version isn't used (it doesn't support the "horizontalOrder" option)
            
            
			// Enqueue scripts
			wp_enqueue_script( 'modernizr', NM_THEME_URI . '/assets/js/plugins/modernizr.min.js', array( 'jquery' ), '2.8.3' );
            wp_enqueue_script( 'slick-slider', NM_THEME_URI . '/assets/js/plugins/slick.min.js', array( 'jquery' ), '1.5.5' );
			wp_enqueue_script( 'magnific-popup', NM_THEME_URI . '/assets/js/plugins/jquery.magnific-popup.min.js', array( 'jquery' ), '0.9.9' );
			wp_enqueue_script( 'nm-core', $script_path . 'nm-core' . $suffix . '.js', array( 'jquery' ), NM_THEME_VERSION );
			
			
			// Enqueue blog scripts
            wp_enqueue_script( 'nm-blog', $script_path . 'nm-blog' . $suffix . '.js', array( 'jquery' ), NM_THEME_VERSION );
			if ( isset( $nm_page_includes['blog-masonry'] ) ) {
                wp_enqueue_script( 'nm-masonry' );
            }
			
			
			// WP comments script
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
			
			
			if ( nm_woocommerce_activated() ) {
				// Register shop/product scripts
				wp_register_script( 'selectod', NM_THEME_URI . '/assets/js/plugins/selectod.custom.min.js', array( 'jquery' ), '3.8.1' );
				if ( $nm_theme_options['product_ajax_atc'] && get_option( 'woocommerce_cart_redirect_after_add' ) == 'no' ) {
                    wp_register_script( 'nm-shop-add-to-cart', $script_path . 'nm-shop-add-to-cart' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
                }
				wp_register_script( 'nm-shop', $script_path . 'nm-shop' . $suffix . '.js', array( 'jquery', 'nm-core', 'selectod' ), NM_THEME_VERSION );
				wp_register_script( 'nm-shop-quickview', $script_path . 'nm-shop-quickview' . $suffix . '.js', array( 'jquery', 'nm-shop', 'wc-add-to-cart-variation' ), NM_THEME_VERSION );
				wp_register_script( 'nm-shop-login', $script_path . 'nm-shop-login' . $suffix . '.js', array( 'jquery' ), NM_THEME_VERSION );
				
				
				// Enqueue login script
				if ( $nm_globals['login_popup'] ) {
					wp_enqueue_script( 'nm-shop-login' );
				}
				
                
                // Enqueue Product Categories script
                if ( isset( $nm_page_includes['product_categories_masonry'] ) ) {
                    wp_enqueue_script( 'nm-masonry' );
                }
                
				
				// Enqueue shop/product scripts
				if ( isset( $nm_page_includes['products'] ) ) {
					wp_enqueue_script( 'lazysizes', NM_THEME_URI . '/assets/js/plugins/lazysizes.min.js', array(), '4.0.1' );
                    wp_enqueue_script( 'selectod' );
					wp_enqueue_script( 'nm-shop-add-to-cart' );
					if ( $nm_theme_options['product_quickview'] ) {
						wp_enqueue_script( 'nm-shop-quickview' );
					}
				} else if ( isset( $nm_page_includes['wishlist-home'] ) ) {
					wp_enqueue_script( 'nm-shop-add-to-cart' );
				}
				
				
				// Register shop scripts
				wp_register_script( 'nm-shop-infload', $script_path . 'nm-shop-infload' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
				wp_register_script( 'nm-shop-filters', $script_path . 'nm-shop-filters' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
				wp_register_script( 'nm-shop-search', $script_path . 'nm-shop-search' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
				
				
				// WooCommerce page - Note: Does not include the Cart, Checkout or Account pages
				if ( is_woocommerce() ) {
					// Single product page
					if ( is_product() ) {
                        if ( $nm_theme_options['product_layout'] == 'scrolling' ) {
                            wp_enqueue_script( 'pin', NM_THEME_URI . '/assets/js/plugins/jquery.pin.min.js', array( 'jquery' ), '1.0.3' );
                        }
						if ( $nm_globals['product_image_hover_zoom'] ) {
							wp_enqueue_script( 'easyzoom', NM_THEME_URI . '/assets/js/plugins/easyzoom.min.js', array( 'jquery' ), '2.3.0' );
						}
						wp_enqueue_script( 'nm-shop-add-to-cart' );
						wp_enqueue_script( 'nm-shop-single-product', $script_path . 'nm-shop-single-product' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
					} 
					// Shop page (except Single product, Cart and Checkout)
					else {
						wp_enqueue_script( 'smartscroll', NM_THEME_URI . '/assets/js/plugins/jquery.smartscroll.min.js', array( 'jquery' ), '1.0' );
						wp_enqueue_script( 'nm-shop-infload' );
						wp_enqueue_script( 'nm-shop-filters' );
						wp_enqueue_script( 'nm-shop-search' );
					}
				} else {
					// Cart page
					if ( is_cart() ) {
						wp_enqueue_script( 'nm-shop-cart', $script_path . 'nm-shop-cart' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
					} 
					// Checkout page
					else if ( is_checkout() ) {
						wp_enqueue_script( 'nm-shop-checkout', $script_path . 'nm-shop-checkout' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
					}
					// Account page
					else if ( is_account_page() ) {
						wp_enqueue_script( 'nm-shop-login' );
					}
				}
			}
			
			
			// Add local Javascript variables
            $local_js_vars = array(
				'themeUri' 				    => NM_THEME_URI,
                'ajaxUrl' 				    => admin_url( 'admin-ajax.php', 'relative' ),
                'woocommerceAjaxUrl'        => ( class_exists( 'WC_AJAX' ) ) ? WC_AJAX::get_endpoint( "%%endpoint%%" ) : '',
				'searchUrl'				    => home_url( '?s=' ),
				'pageLoadTransition'        => intval( $nm_theme_options['page_load_transition'] ),
                'cartPanelQtyArrows'        => intval( $nm_theme_options['cart_panel_quantity_arrows'] ),
                'shopFiltersAjax'		    => isset( $_GET['ajax_filters'] ) ? esc_attr( $_GET['ajax_filters'] ) : esc_attr( $nm_theme_options['shop_filters_enable_ajax'] ),
				'shopAjaxUpdateTitle'	    => intval( $nm_theme_options['shop_ajax_update_title'] ),
				'shopImageLazyLoad'		    => intval( $nm_theme_options['product_image_lazy_loading'] ),
                'shopScrollOffset' 		    => intval( $nm_theme_options['shop_scroll_offset'] ),
				'shopScrollOffsetTablet'    => intval( $nm_theme_options['shop_scroll_offset_tablet'] ),
                'shopScrollOffsetMobile'    => intval( $nm_theme_options['shop_scroll_offset_mobile'] ),
                'shopSearch'			    => esc_attr( $nm_globals['shop_search_layout'] ),
				'shopSearchMinChar'		    => intval( $nm_theme_options['shop_search_min_char'] ),
				'shopSearchAutoClose'       => intval( $nm_theme_options['shop_search_auto_close'] ),
                'shopAjaxAddToCart'		    => ( $nm_theme_options['product_ajax_atc'] && get_option( 'woocommerce_cart_redirect_after_add' ) == 'no' ) ? 1 : 0,
                'shopRedirectScroll'        => intval( $nm_theme_options['product_redirect_scroll'] ),
                'shopCustomSelect'          => intval( $nm_theme_options['product_custom_select'] ),
                'productLayout'             => esc_attr( $nm_theme_options['product_layout'] ),
                'galleryZoom'               => intval( $nm_theme_options['product_image_zoom'] ),
                'galleryThumbnailsSlider'   => intval( $nm_theme_options['product_thumbnails_slider'] ),
                'shopYouTubeRelated'        => ( ! defined( 'NM_SHOP_YOUTUBE_RELATED' ) ) ? 1 : 0,
                'checkoutTacLightbox'       => intval( $nm_theme_options['checkout_tac_lightbox'] ),
                'wpGalleryPopup'            => intval( $nm_theme_options['wp_gallery_popup'] )
			);
    		wp_localize_script( 'nm-core', 'nm_wp_vars', $local_js_vars );
		}
	}
	add_action( 'wp_footer', 'nm_scripts' ); // Add footer scripts
	
	
	
	/* Admin Assets
	==================================================================================================== */
	
	function nm_admin_assets( $hook ) {
		// Styles
		wp_enqueue_style( 'nm-admin-styles', NM_URI . '/assets/css/nm-wp-admin.css', array(), NM_THEME_VERSION, 'all' );
		
		// Widgets page
		if ( 'widgets.php' == $hook ) {
			wp_enqueue_style( 'wp-color-picker' );
			
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'nm-wp-color-picker', NM_URI . '/assets/js/nm-wp-widgets-color-picker-init.js', array( 'jquery' ), false );
		}
	}
	add_action( 'admin_enqueue_scripts', 'nm_admin_assets' ); // Admin assets
	
	
	
	/* Web fonts
	==================================================================================================== */
	
	global $webfont_status;
	$webfont_status = array( 'typekit' => false );
	
	/* Web fonts: Enqueue scripts */
	function nm_webfonts() {
		global $nm_theme_options, $webfont_status;
		
        // Typekit: Main font kit
        if ( $nm_theme_options['main_font_source'] === '2' && isset( $nm_theme_options['main_font_typekit_kit_id'] ) ) {
            $webfont_status['typekit'] = true;
            wp_enqueue_script( 'nm_typekit_main', '//use.typekit.net/' . esc_attr( $nm_theme_options['main_font_typekit_kit_id'] ) . '.js' );
        }

        // Typekit: Secondary font kit
        if ( $nm_theme_options['secondary_font_source'] === '2' && isset( $nm_theme_options['secondary_font_typekit_kit_id'] ) ) {
            // Make sure typekit kit-id's are different (no need to include the same typekit file for both fonts)
            if ( $nm_theme_options['secondary_font_typekit_kit_id'] !== $nm_theme_options['main_font_typekit_kit_id'] ) {
                $webfont_status['typekit'] = true;
                wp_enqueue_script( 'nm_typekit_secondary', '//use.typekit.net/' . esc_attr( $nm_theme_options['secondary_font_typekit_kit_id'] ) . '.js' );
            }
        }
	};
	add_action( 'wp_enqueue_scripts', 'nm_webfonts' );
	
	
	/* Web fonts: Add inline scripts */
	function nm_webfonts_inline() {
		global $webfont_status, $nm_theme_options;
		
		if ( $webfont_status['typekit'] ) {
			//if ( wp_script_is( 'nm_typekit_main', 'done' ) ) {
			echo "\n" . '<script type="text/javascript">try{Typekit.load();}catch(e){}</script>';
			//}
		}
	};
	add_action( 'wp_head', 'nm_webfonts_inline' );
	
	
	
	/* Redux Framework
	==================================================================================================== */
	
	/* Remove redux sub-menu from "Tools" admin menu */
	function nm_remove_redux_menu() {
		remove_submenu_page( 'tools.php', 'redux-about' );
	}
	add_action( 'admin_menu', 'nm_remove_redux_menu', 12 );
	
	
	
	/* Theme Setup
	==================================================================================================== */
    
	/* Front-end WordPress admin bar */
	if ( ! $nm_theme_options['wp_admin_bar'] ) {
		function nm_remove_admin_bar() {		
			return false;
		}
		add_filter( 'show_admin_bar', 'nm_remove_admin_bar' );
	}
		
	/* Disable emoji icons - Source: https://wordpress.org/plugins/disable-emojis/ */
	if ( ! function_exists( 'nm_disable_emojis' ) ) {
		function nm_disable_emojis() {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );	
			
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			
			add_filter( 'tiny_mce_plugins', 'nm_disable_emojis_tinymce' );
		}
	}
	/* Filter function: Remove TinyMCE emoji plugin */
	function nm_disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
	// Hook: Disable emoji icons
	add_action( 'init', 'nm_disable_emojis' );
	
    
    /* Video embeds: Wrap video element in "div" container (to make them responsive) */
    function nm_wrap_video_embeds( $html ) {
        return '<div class="nm-wp-video-wrap">' . $html . '</div>';
    }
    add_filter( 'embed_oembed_html', 'nm_wrap_video_embeds', 10, 3 );
    add_filter( 'video_embed_html', 'nm_wrap_video_embeds' ); // Jetpack
	
    // Various filters
	add_filter( 'widget_text', 'do_shortcode' ); 					// Allow shortcodes in text-widgets
	add_filter( 'widget_text', 'shortcode_unautop' ); 				// Disable auto-formatting (line breaks) in text-widgets
	add_filter( 'the_excerpt', 'shortcode_unautop' ); 				// Remove auto <p> tags in Excerpt (Manual Excerpts only)
	add_filter( 'use_default_gallery_style', '__return_false' );	// Remove default inline WP gallery styles
    
    
    
    /* Menus
	==================================================================================================== */
    
	if ( ! function_exists( 'nm_register_menus' ) ) {
		function nm_register_menus() {
			register_nav_menus( array(
				'top-bar-menu'	=> __( 'Top Bar', 'nm-framework' ),
				'main-menu'		=> __( 'Header Main', 'nm-framework' ),
				'right-menu'	=> __( 'Header Secondary (Right side)', 'nm-framework' ),
				'mobile-menu'   => __( 'Mobile', 'nm-framework-admin' ),
                'footer-menu'	=> __( 'Footer Bar', 'nm-framework' )
			) );
		}
	}
	add_action( 'init', 'nm_register_menus' );
    
    
    
	/* Blog
	==================================================================================================== */
	
    /* AJAX: Get blog content */
	function nm_blog_get_ajax_content() {
        // Is content requested via AJAX?
        if ( isset( $_REQUEST['blog_load'] ) && nm_is_ajax_request() ) {
            // Include blog content only (no header or footer)
            get_template_part( 'template-parts/blog/content' );
            exit;
        }
    }
    
	/* Post excerpt brackets - [...] */
	function nm_excerpt_read_more( $excerpt ) {
		$excerpt_more = '&hellip;';
		$trans = array(
			'[&hellip;]' => $excerpt_more // WordPress >= v3.6
		);
		
		return strtr( $excerpt, $trans );
	}
	add_filter( 'wp_trim_excerpt', 'nm_excerpt_read_more' );
	
	
	/* Blog categories menu */
	function nm_blog_category_menu() {
		global $wp_query, $nm_theme_options;

		$current_cat = ( is_category() ) ? $wp_query->queried_object->cat_ID : '';
		
		// Categories order
		$orderby = 'slug';
		$order = 'asc';
		if ( isset( $nm_theme_options['blog_categories_orderby'] ) ) {
			$orderby = $nm_theme_options['blog_categories_orderby'];
			$order = $nm_theme_options['blog_categories_order'];
		}
		
		$args = array(
			'type'			=> 'post',
			'orderby'		=> $orderby,
			'order'			=> $order,
			'hide_empty'	=> ( $nm_theme_options['blog_categories_hide_empty'] ) ? 1 : 0,
			'hierarchical'	=> 1,
			'taxonomy'		=> 'category'
		); 
		
		$categories = get_categories( $args );
		
		$current_class_set = false;
		$categories_output = '';
		
		// Categories menu divider
		$categories_menu_divider = apply_filters( 'nm_blog_categories_divider', '<span>&frasl;</span>' );
		
		foreach ( $categories as $category ) {
			if ( $current_cat == $category->cat_ID ) {
				$current_class_set = true;
				$current_class = ' class="current-cat"';
			} else {
				$current_class = '';
			}
			$category_link = get_category_link( $category->cat_ID );
			
			$categories_output .= '<li' . $current_class . '>' . $categories_menu_divider . '<a href="' . esc_url( $category_link ) . '">' . esc_attr( $category->name ) . '</a></li>';
		}
		
		$categories_count = count( $categories );
		
		// Categories layout classes
		$categories_class = ' toggle-' . $nm_theme_options['blog_categories_toggle'];
		if ( $nm_theme_options['blog_categories_layout'] === 'columns' ) {
			$column_small = ( intval( $nm_theme_options['blog_categories_columns'] ) > 4 ) ? '3' : '2';
			$categories_ul_class = 'columns small-block-grid-' . $column_small . ' medium-block-grid-' . $nm_theme_options['blog_categories_columns'];
		} else {
			$categories_ul_class = $nm_theme_options['blog_categories_layout'];
		}
		
		// "All" category class attr
		$current_class = ( $current_class_set ) ? '' : ' class="current-cat"';
		
		$output = '<div class="nm-blog-categories-wrap ' . esc_attr( $categories_class ) . '">';
		$output .= '<ul class="nm-blog-categories-toggle"><li><a href="#" id="nm-blog-categories-toggle-link">' . esc_html__( 'Categories', 'nm-framework' ) . '</a> <em class="count">' . $categories_count . '</em></li></ul>';
		$output .= '<ul id="nm-blog-categories-list" class="nm-blog-categories-list ' . esc_attr( $categories_ul_class ) . '"><li' . $current_class . '><a href="' . esc_url( get_permalink( get_option( 'page_for_posts' ) ) ) . '">' . esc_html__( 'All', 'nm-framework' ) . '</a></li>' . $categories_output . '</ul>';
		$output .= '</div>';
		
		return $output;
	}
    
    
	/* WP gallery popup: Set page include value */
    if ( $nm_theme_options['wp_gallery_popup'] ) {
        function nm_wp_gallery_set_include() {
            nm_add_page_include( 'wp-gallery' );
            return ''; // Returning an empty string will output the default WP gallery
        }
		add_filter( 'post_gallery', 'nm_wp_gallery_set_include' );
	}
	
    
    
	/* Comments
	==================================================================================================== */
    
    /* Comments callback */
	function nm_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php esc_html_e( 'Pingback:', 'nm-framework' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'nm-framework' ), ' ' ); ?></p>
		<?php
			break;
			default :
		?>
		<li id="comment-<?php comment_ID() ?>" <?php comment_class(); ?>>
            <div class="comment-inner-wrap">
            	<?php if ( function_exists( 'get_avatar' ) ) { echo get_avatar( $comment, '60' ); } ?>
                
				<div class="comment-text">
                    <p class="meta">
                        <strong itemprop="author"><?php printf( '%1$s', get_comment_author_link() ); ?></strong>
                        <time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>"><?php printf( esc_html__( '%1$s at %2$s', 'nm-framework' ), get_comment_date(), get_comment_time() ); ?></time>
                    </p>
                
                    <div itemprop="description" class="description entry-content">
                        <?php if ( $comment->comment_approved == '0' ) : ?>
                            <p class="moderating"><em><?php esc_html_e( 'Your comment is awaiting moderation', 'nm-framework' ); ?></em></p>
                        <?php endif; ?>
                        
                        <?php comment_text(); ?>
                    </div>
                    
                    <div class="reply">
                        <?php 
                            edit_comment_link( esc_html__( 'Edit', 'nm-framework' ), '<span class="edit-link">', '</span><span> &nbsp;-&nbsp; </span>' );
                            
                            comment_reply_link( array_merge( $args, array(
                                'depth' 	=> $depth,
                                'max_depth'	=> $args['max_depth']
                            ) ) );
                        ?>
                    </div>
                </div>
            </div>
		<?php
			break;
		endswitch;
	}
	
    
    
	/* Sidebars & Widgets
	==================================================================================================== */
	
	/* Register/include sidebars & widgets */
	function nm_widgets_init() {
		global $nm_globals, $nm_theme_options;
		
        // Sidebar: Page
		register_sidebar( array(
			'name' 				=> __( 'Page', 'nm-framework' ),
			'id' 				=> 'page',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</div>',
			'before_title' 		=> '<h3 class="nm-widget-title">',
			'after_title' 		=> '</h3>'
		) );
        
		// Sidebar: Blog
		register_sidebar( array(
			'name' 				=> __( 'Blog', 'nm-framework' ),
			'id' 				=> 'sidebar',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</div>',
			'before_title' 		=> '<h3 class="nm-widget-title">',
			'after_title' 		=> '</h3>'
		) );
        
		// Sidebar: Shop
		if ( $nm_globals['shop_filters_scrollbar'] ) {
            register_sidebar( array(
				'name' 				=> __( 'Shop', 'nm-framework' ),
				'id' 				=> 'widgets-shop',
				'before_widget'		=> '<li id="%1$s" class="scroll-enabled scroll-type-default widget %2$s"><div class="nm-shop-widget-col">',
				'after_widget' 		=> '</div></div></li>',
				'before_title' 		=> '<h3 class="nm-widget-title">',
				'after_title' 		=> '</h3></div><div class="nm-shop-widget-col"><div class="nm-shop-widget-scroll">'
			));
		} else {
            register_sidebar( array(
				'name' 				=> __( 'Shop', 'nm-framework' ),
				'id' 				=> 'widgets-shop',
				'before_widget'		=> '<li id="%1$s" class="widget %2$s"><div class="nm-shop-widget-col">',
				'after_widget' 		=> '</div></li>',
				'before_title' 		=> '<h3 class="nm-widget-title">',
				'after_title' 		=> '</h3></div><div class="nm-shop-widget-col">'
			) );
		}
		
		// Sidebar: Footer
		register_sidebar( array(
			'name' 				=> __( 'Footer', 'nm-framework' ),
			'id' 				=> 'footer',
			'before_widget'		=> '<li id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</li>',
			'before_title' 		=> '<h3 class="nm-widget-title">',
			'after_title' 		=> '</h3>'
		) );
		
		// Sidebar: Visual Composer - Widgetised Sidebar
		register_sidebar( array(
			'name' 				=> __( '"Widgetised Sidebar" Element', 'nm-framework' ),
			'id' 				=> 'vc-sidebar',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</div>',
			'before_title' 		=> '<h3 class="nm-widget-title">',
			'after_title' 		=> '</h3>'
		) );
		
		
		// Custom WooCommerce widgets
		// NOTE: The custom WooCommerce -filter- widgets will not work without the widget-id fix (see "nm_add_woocommerce_widget_ids()" below)
		if ( class_exists( 'WC_Widget' ) ) {
			// Product sorting
			include_once( NM_DIR . '/woocommerce/widgets/woocommerce-product-sorting.php' );
			register_widget( 'NM_WC_Widget_Product_Sorting' );
			
			// Price filter list
			include_once( NM_DIR . '/woocommerce//widgets/woocommerce-price-filter.php' );
			register_widget( 'NM_WC_Widget_Price_Filter' );
			
			// Color filter list
			if ( ! $nm_theme_options['shop_filters_custom_controls'] || ! $nm_theme_options['product_custom_controls'] ) { // Only add if custom variation controls are disabled
                include_once( NM_DIR . '/woocommerce//widgets/woocommerce-color-filter.php' );
                register_widget( 'WC_Widget_Color_Filter' );
            }
		}
		
		
		// Unregister widgets
		unregister_widget( 'WC_Widget_Cart' );
		if ( ! defined( 'NM_ENABLE_PRICE_SLIDER' ) ) {
            unregister_widget( 'WC_Widget_Price_Filter' ); // Note: The price-slider doesn't work with Ajax currently (there's no JavaScript function available to re-init the price-slider)
        }
	}
	add_action( 'widgets_init', 'nm_widgets_init' ); // Register widget sidebars
	
	/* 
	 *	Add relevant WooCommerce widget-id's to "sidebars_widgets" option so the custom product filters will work
	 *
	 * 	Note: WooCommerce use "is_active_widget()" to check for active widgets in: "../includes/class-wc-query.php"
	 */
	function nm_add_woocommerce_widget_ids( $sidebars_widgets, $old_sidebars_widgets = array() ) {
		$shop_sidebar_id = 'widgets-shop';
		$shop_widgets = $sidebars_widgets[$shop_sidebar_id];
		
		if ( is_array( $shop_widgets ) ) {
			foreach ( $shop_widgets as $widget ) {
				$widget_id = _get_widget_id_base( $widget );
				
				if ( $widget_id === 'nm_woocommerce_price_filter' ) {
					$sidebars_widgets[$shop_sidebar_id][] = 'woocommerce_price_filter-12345';
				} else if ( $widget_id === 'nm_woocommerce_color_filter' ) {
					$sidebars_widgets[$shop_sidebar_id][] = 'woocommerce_layered_nav-12345';
				}
			}
		}
		
		return $sidebars_widgets;
	}
	add_action( 'pre_update_option_sidebars_widgets', 'nm_add_woocommerce_widget_ids' );
	
	/*function nm_check_sidebars_array() {
		global $sidebars_widgets;
		echo '<pre>';
		var_dump( $sidebars_widgets['widgets-shop'] );
		echo '</pre>';
	}
	add_action( 'init', 'nm_check_sidebars_array' );*/
	
	
	/* Page includes: Include element */
	function nm_include_page_includes_element() {
		global $nm_page_includes;
		
		$classes = '';
		
		foreach ( $nm_page_includes as $class => $value )
			$classes .= $class . ' ';
		
		echo '<div id="nm-page-includes" class="' . esc_attr( $classes ) . '" style="display:none;">&nbsp;</div>' . "\n\n";
	}
	add_action( 'wp_footer', 'nm_include_page_includes_element' ); // Include "page includes" element
	
    
    
	/* Contact Form 7
	==================================================================================================== */
	
    // Disable default CF7 CSS
    add_filter( 'wpcf7_load_css', '__return_false' );
    