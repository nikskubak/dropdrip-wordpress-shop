<?php
    global $nm_instagram_gallery;
    
    $atts = $nm_instagram_gallery['atts'];
    $items = $nm_instagram_gallery['items'];
    
    $gallery_columns_desktop = intval( $atts['images_per_row'] );
    $gallery_columns_mobile = $gallery_columns_desktop / 2;
    $gallery_columns_class = apply_filters( 'nm_instagram_gallery_columns_class', 'large-block-grid-' . $gallery_columns_desktop . ' medium-block-grid-' . $gallery_columns_mobile . ' small-block-grid-' . $gallery_columns_mobile . ' xsmall-block-grid-' . $gallery_columns_mobile );

    $gallery_class = $atts['image_spacing_class'];

    $count = 0;
?>

<div class="nm-instagram-gallery <?php echo esc_attr( $gallery_class ); ?>">
    <ul class="nm-instagram-gallery-ul <?php echo esc_attr( $gallery_columns_class ); ?>">
        <?php foreach ( $items['items'] as $item ) : ?>
            <li>
                <a href="<?php echo esc_url( $item['link'] ); ?>" target="_blank" title="<?php echo esc_attr( $item['image-caption'] ); ?>">
                    <span class="nm-instagram-gallery-overlay"><i class="nm-font nm-font-instagram"></i></span>
                    <img src="<?php echo esc_url( $item['image-url'] ); ?>" alt="<?php echo esc_attr( $item['image-caption'] ); ?>">
                </a>
            </li>
            <?php if ( ++$count === $atts['image_limit'] ) break; ?>
        <?php endforeach; ?>
    </ul>
    
    <?php if ( strlen( $atts['instagram_user_link'] ) > 0 ) : ?>
    <div class="nm-instagram-gallery-link">
        <span><?php esc_html_e( 'Instagram', 'nm-instagram' ); ?></span> <a href="<?php printf( 'http://instagram.com/%s?ref=badge', esc_attr( $items['username'] ) ); ?>" target="_blank">@<?php echo esc_attr( $items['username'] ); ?></a>
    </div>
    <?php endif; ?>
</div>