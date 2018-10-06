<?php
/*
 *	WooCommerce admin: Product attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NM_Product_Attributes {
    
    protected $pa_types = array();
    
    
    /*
	 * Constructor
	 */
	public function __construct() {
        global $nm_globals;
        
        $this->pa_types = $nm_globals['pa_variation_controls'];
        
		add_action( 'admin_enqueue_scripts', array( $this, 'pa_assets' ) );
        add_action( 'admin_init', array( $this, 'pa_hooks' ) );
	}
    
    
    /*
     * Assets
     */
    public function pa_assets( $hook ) {
        if ( 'edit-tags.php' != $hook && 'term.php' != $hook ) {
            return;
        }
        
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'nm-wp-color-picker', NM_URI . '/assets/js/nm-wp-attributes-color-picker-init.js', array( 'jquery' ), false );
    }
    
    
	/*
	 * Actions and filters
	 */
	public function pa_hooks() {
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( ! empty( $attribute_taxonomies ) ) {
            foreach ( $attribute_taxonomies as $taxonomy ) {
                add_action( 'pa_' . $taxonomy->attribute_name . '_add_form_fields', array( $this, 'pa_term_add_form_fields' ) );
                add_action( 'pa_' . $taxonomy->attribute_name . '_edit_form_fields', array( $this, 'pa_term_edit_form_fields' ), 1, 2 );
            }
            add_action( 'created_term', array( $this, 'pa_term_save' ), 10, 2 );
            add_action( 'edit_term', array( $this, 'pa_term_save' ), 10, 2 );
        }
        
        add_filter( 'product_attributes_type_selector', array( $this, 'pa_add_types' ) );
	}
    
    
    /*
	 * Product attribute: Add/edit form - Add custom attribute types to existing "Type" option/select
     *
     * Note: The "Type" option only shows when multiple types/options are available
	 */
	public function pa_add_types( $pa_types ) {
        // Is this the product-attribute add/edit form?
        // - Custom attribute-types disables the default term (colors, sizes) selector when editing a product - see line 38 of the "../woocommerce/includes/admin/meta-boxes/views/html-product-attribute.php" file
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'product_attributes' ) {
		  $pa_types = array_merge( $pa_types, $this->pa_types );
        }
		return $pa_types;
	}
    
    
    /*
     * Product attribute - Term: Add form - Include custom fields
     */
    public function pa_term_add_form_fields( $taxonomy ) {
        $attr = nm_woocommerce_get_taxonomy_attribute( $taxonomy );
        $type = $attr->attribute_type;
        
        // Field: Color
        if ( $type == 'color' ) :
            ?>
            <div class="form-field term-nm_pa_color-wrap">
                <label for="nm_pa_color"><?php esc_html_e( 'Color' ); ?></label>
                <input type="text" id="nm_pa_color" name="nm_pa_color" class="nm_pa_color-picker" value="" size="40">
            </div>
            <?php
        endif;
    }
    
    
    /*
     * Product attribute - Term: Edit form - Include custom field
     */
    public function pa_term_edit_form_fields( $term, $taxonomy ) {
        $attr = nm_woocommerce_get_taxonomy_attribute( $taxonomy );
        
        // Field: Color
        if ( $attr->attribute_type == 'color' ) :
            $id = $term->term_id;
            $color = '';

            if ( $id ) {
                $saved_colors = get_option( 'nm_pa_colors' );
                $color = ( isset( $saved_colors[$id] ) ) ? $saved_colors[$id] : '';
            }
            ?>
            <tr class="form-field term-nm_pa_color-wrap">
                <th scope="row">
                    <label for="nm_pa_color"><?php esc_html_e( 'Color' ); ?></label>
                </th>
                <td>
                    <input type="text" id="nm_pa_color" name="nm_pa_color" class="nm_pa_color-picker" value="<?php echo $color; ?>" size="40">
                </td>
            </tr>
            <?php
        endif;
    }


    /*
     * Product attribute - Term: Save custom fields
     */
    public function pa_term_save( $term_id ) {
        // Field: Color
        if ( isset( $_POST['nm_pa_color'] ) ) {
            $color = sanitize_text_field( $_POST['nm_pa_color'] );
            $saved_colors = get_option( 'nm_pa_colors' );

            // Quick edit: Don't overwrite with empty value when saving via quick edit
            if ( isset( $_REQUEST['_inline_edit'] ) ) {
                return;
            }

            // Is there a color value?
            if ( $color && strlen( $color ) > 0 ) {
                $saved_colors[$term_id] = $color;
            } else if ( isset( $saved_colors[$term_id] ) ) {
                // Delete from array if color is empty
                unset( $saved_colors[$term_id] );
            }

            update_option( 'nm_pa_colors', $saved_colors );
        }
    }

}

$NM_Product_Attributes = new NM_Product_Attributes();
