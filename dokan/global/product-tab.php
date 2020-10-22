<?php
/**
 * Dokan Seller Single product tab Template
 *
 * @package dokani
 */
?>

<h2><?php esc_html_e( 'Vendor Info', 'dokani' ); ?></h2>

<div class="store-info-wrapper">
    <div class="store-banner">
		<?php
		$banner_id  = isset( $store_info['banner'] ) ? $store_info['banner'] : 0;
		$banner_url = ( $banner_id ) ? wp_get_attachment_image( $banner_id, 'single-vendor-thumb' ) : get_template_directory_uri() . '/assets/images/single-default-store-banner.png';

        if( $banner_id ) { ?>
            <a href="<?php echo esc_url( dokan_get_store_url( $author->ID ) ); ?>">
                <?php echo __( $banner_url ); ?>
            </a>
        <?php } else { ?>
            <a href="<?php echo esc_url( dokan_get_store_url( $author->ID ) ); ?>">
                <img src="<?php echo esc_url( $banner_url ); ?>" alt="<?php echo esc_html( $store_info['store_name'] ); ?>">
            </a>
        <?php } ?>
    </div>

    <ul class="store-info">
		<?php do_action( 'dokan_product_seller_tab_start', $author, $store_info ); ?>

		<?php if ( ! empty( $store_info['store_name'] ) ) { ?>
            <li class="store-name">
                <?php echo esc_html( $store_info['store_name'] ); ?>
            </li>
		<?php } ?>

        <li class="store-rating">
		    <?php dokan_get_readable_seller_rating( $author->ID ); ?>
        </li>

        <li class="seller-name">
            <span class="title"><?php esc_html_e( 'Vendor:', 'dokani' ); ?></span>

            <span class="details">
                <?php printf( '<a href="%s">%s</a>', esc_url( dokan_get_store_url( $author->ID ) ), esc_html( $author->display_name ) ); ?>
            </span>
        </li>
		<?php if ( ! empty( $store_info['address'] ) ) { ?>
            <li class="store-address">
                <span class="title"><?php esc_html_e( 'Address:', 'dokani' ); ?></span>
                <span class="details">
                    <?php echo esc_html( dokan_get_seller_address( $author->ID ) ); ?>
                </span>
            </li>
		<?php } ?>

		<?php do_action( 'dokan_product_seller_tab_end', $author, $store_info ); ?>
    </ul>
</div>

