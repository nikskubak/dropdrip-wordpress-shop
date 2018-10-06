<?php
	
/* 
 * WooCommerce - Attribute functions
=============================================================== */

global $nm_theme_options, $nm_globals;



/*
 * Product attribute: Get properties
 *
 * Note: Code from "get_tax_attribute()" function in the "../variation-swatches-for-woocommerce.php" file of the "Variation Swatches for WooCommerce" plugin
 */
function nm_woocommerce_get_taxonomy_attribute( $taxonomy ) {
    global $wpdb, $nm_globals;

    // Returned cached data if available
    if ( isset( $nm_globals['pa_cache'][$taxonomy] ) ) {
        return $nm_globals['pa_cache'][$taxonomy];
    }

    $attr = substr( $taxonomy, 3 );
    $attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'" );

    // Save data to avoid multiple database calls
    $nm_globals['pa_cache'][$taxonomy] = $attr;

    return $attr;
}



/*
 *  Widget: Layered nav (color) - Include color element
 */
if ( $nm_theme_options['shop_filters_custom_controls'] ) {
    function nm_woocommerce_layered_nav_count( $term_html, $term, $link, $count ) {
        global $nm_globals;

        // Get attribute type
        $attr = nm_woocommerce_get_taxonomy_attribute( $term->taxonomy );
        $attr_type = ( $attr ) ? $attr->attribute_type : '';

        if ( 'color' == $attr_type || 'pa_' . $nm_globals['pa_color_slug'] == $term->taxonomy ) {
            // Save data in global variable to avoid getting the "nm_pa_colors" option multiple times
            if ( ! isset( $nm_globals['pa_colors'] ) ) {
                $nm_globals['pa_colors'] = get_option( 'nm_pa_colors' );
            }

            $id = $term->term_id;

            $color = ( isset( $nm_globals['pa_colors'][$id] ) ) ? $nm_globals['pa_colors'][$id] : '#c0c0c0';
            $color_html = '<i style="background:' . esc_attr( $color ) . ';" class="nm-pa-color nm-pa-color-' . esc_attr( strtolower( $term->slug ) ) . '"></i>';
            
            // Code from "layered_nav_list()" function in "../plugins/woocommerce/includes/widgets/class-wc-widget-layered-nav.php" file
            if ( $count > 0 ) {
                $term_html = '<a rel="nofollow" href="' . $link . '">' . $color_html . esc_html( $term->name ) . '</a>';
			} else {
				$term_html = '<span>' . $color_html . esc_html( $term->name ) . '</span>';
			}
        }

        return $term_html;
    }
    add_filter( 'woocommerce_layered_nav_term_html', 'nm_woocommerce_layered_nav_count', 1, 4 );
}



/*
 *  Widget: Layered nav (color) - Add class to widget container
 */
/*Can be used to add a custom class to the widget container: if ( $nm_theme_options['shop_filters_custom_controls'] && ! is_admin() ) {
    global $nm_widget_instance;
    $nm_widget_instance = array();

    function nm_dynamic_sidebar_params( $params ) {
        $widget_id = $params[0]['widget_id'];

        // Is this a "woocommerce_layered_nav" (and not a "woocommerce_layered_nav_filters") widget?
        if ( strpos( $widget_id, 'woocommerce_layered_nav' ) !== false && strpos( $widget_id, 'filters' ) == false ) {
                global $wp_registered_widgets;

                if ( ! empty( $wp_registered_widgets[$widget_id]['callback'] ) ) {
                    global $nm_widget_instance, $nm_globals;

                    // Is the widget instance/settings saved to global? (saving to avoid getting it multiple times)
                    if ( empty( $nm_widget_instance ) ) {
                        $widget = $wp_registered_widgets[$widget_id]['callback'][0]; // Get widget instance
                        $nm_widget_instance['settings'] = $widget->get_settings(); // Get settings for -all- widget instances
                    }

                    $widget_instance_id       = $params[1]['number'];
                    $widget_instance_settings = $nm_widget_instance['settings'][$widget_instance_id];

                    if ( ! empty( $widget_instance_settings ) ) {
                        // Get attribute type
                        $attr = nm_woocommerce_get_taxonomy_attribute( 'pa_' . $widget_instance_settings['attribute'] );
                        $attr_type = ( $attr ) ? $attr->attribute_type : '';

                        // Is the "color" attribute displayed?
                        if ( 'color' == $attr_type || $nm_globals['pa_color_slug'] == $widget_instance_settings['attribute'] ) {
                            $custom_class_attr = 'class="nm-widget-color ';
                            $params[0]['before_widget'] = str_replace( 'class="', $custom_class_attr, $params[0]['before_widget'] );
                        }
                    }
                }
        }

        return $params;
    }
    add_filter( 'dynamic_sidebar_params', 'nm_dynamic_sidebar_params' );
}*/



/*
 *  Product page: Variation controls - Code from "wc_dropdown_variation_attribute_options()" function in "../woocommerce/includes/wc-template-functions.php"
 */
if ( $nm_theme_options['product_custom_controls'] ) {
    function nm_variation_attribute_options( $html, $args ) {
        global $nm_globals;

        $attr = nm_woocommerce_get_taxonomy_attribute( $args['attribute'] );
        $variation_type = ( $attr ) ? $attr->attribute_type : null;

        // Is this a custom variation-control attribute?
        if ( ! $variation_type || ! array_key_exists( $variation_type, $nm_globals['pa_variation_controls'] ) ) {
            return $html;
        }

        $options      = $args['options'];
        $product      = $args['product'];
        $attribute    = $args['attribute'];

        if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
            $attributes = $product->get_variation_attributes();
            $options    = $attributes[ $attribute ];
        }

        // Hide default select-box
        $html = '<div class="nm-select-hidden">' . $html . '</div>';

        $html .= '<ul class="nm-variation-control nm-variation-control-'. esc_attr( $variation_type ) .'">';

        if ( ! empty( $options ) ) {
            $terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

            switch ( $variation_type ) {
                case 'color' :

                    // Save data in global variable to avoid getting the "nm_pa_colors" option multiple times
                    if ( ! isset( $nm_globals['pa_colors'] ) ) {
                        $nm_globals['pa_colors'] = get_option( 'nm_pa_colors' );
                    }

                    foreach ( $terms as $term ) {
                        if ( in_array( $term->slug, $options, true ) ) {
                            $selected_class_attr = ( $args['selected'] === $term->slug ) ? ' class="selected"' : '';
                            $color = ( isset( $nm_globals['pa_colors'][$term->term_id] ) ) ? $nm_globals['pa_colors'][$term->term_id] : '#ccc';

                            $html .= '<li'. $selected_class_attr .' data-value="' . esc_attr( $term->slug ) . '">';
                            $html .= '<i style="background:' . esc_attr( $color ) . ';" class="nm-pa-color nm-pa-color-' . esc_attr( strtolower( $term->slug ) ) . '"></i>';
                            $html .= '<span>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</span>';
                            $html .= '</li>';
                        }
                    }

                    break;
                default :

                    foreach ( $terms as $term ) {
                        if ( in_array( $term->slug, $options, true ) ) {
                            $selected_class_attr = ( $args['selected'] === $term->slug ) ? ' class="selected"' : '';

                            $html .= '<li'. $selected_class_attr .' data-value="' . esc_attr( $term->slug ) . '">';
                            $html .= '<span>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</span>';
                            $html .= '</li>';
                        }
                    }
            }
        }

        $html .= '</ul>';

        return $html;
    }
    add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'nm_variation_attribute_options', 10, 2 );
}
