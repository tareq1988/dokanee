<?php

$store_user    = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info    = $store_user->get_shop_info();
$social_info   = $store_user->get_social_profiles();
$phone         = $store_user->get_phone();
$email         = $store_user->get_email();
$store_tabs    = dokan_get_store_tabs( $store_user->get_id() );
$social_fields = dokan_get_social_profile_fields();

$dokan_appearance = get_option( 'dokan_appearance' );
$profile_layout   = empty( $dokan_appearance['store_header_template'] ) ? 'default' : $dokan_appearance['store_header_template'];
$store_address    = dokan_get_seller_short_address( $store_user->get_id(), false );

$general_settings = get_option( 'dokan_general', [] );
$banner_width     = ! empty( $general_settings['store_banner_width'] ) ? $general_settings['store_banner_width'] : 625;

$featured_seller = get_user_meta( $store_user->get_id(), 'dokan_feature_seller', true );
$seller_rating  = dokan_get_seller_rating( $store_user->get_id() );

if ( ( 'default' === $profile_layout ) || ( 'layout2' === $profile_layout ) ) {
    $profile_img_class = 'profile-img-circle';
} else {
    $profile_img_class = 'profile-img-square';
}

if ( 'layout3' === $profile_layout ) {
    unset( $store_info['banner'] );

    $no_banner_class = ' profile-frame-no-banner';
    $no_banner_class_tabs = ' dokan-store-tabs-no-banner';

} else {
    $no_banner_class = '';
    $no_banner_class_tabs = '';
}

?>
<div class="profile-frame<?php echo esc_attr( $no_banner_class . ' ' . $profile_layout ); ?>">
    <div class="store-banner">
        <div class="profile-info-img-wrapper">
            <?php if ( $store_user->get_banner() ) : ?>
                <img src="<?php echo esc_attr( $store_user->get_banner() ); ?>"
                     alt="<?php echo esc_attr( $store_user->get_shop_name() ); ?>"
                     title="<?php echo esc_attr( $store_user->get_shop_name() ); ?>"
                     class="profile-info-img">
            <?php else : ?>
                <div class="profile-info-img-default">&nbsp</div>
            <?php endif; ?>
        </div> <!-- .profile-info-img-wrapper -->

        <div class="profile-info-box profile-layout-<?php echo esc_attr( $profile_layout ); ?>">
            <div class="grid-container">
                <div class="profile-info-summery">
                    <div class="profile-info-head">
                        <div class="profile-img <?php echo esc_attr( $profile_img_class ); ?>">
						    <?php echo get_avatar( $store_user->get_id(), 100, '', $store_user->get_shop_name() ); ?>
                        </div>
                    </div>

                    <div class="profile-info">
                        <div class="store-info-column">
                            <?php if ( ! empty( $featured_seller ) && 'yes' == $featured_seller ): ?>
                                <span class="featured-label"><?php esc_html_e( 'Featured', 'dokani' ); ?></span>
                            <?php endif ?>

                            <?php if ( ! empty( $store_user->get_shop_name() ) ) { ?>
                                <h1 class="store-name"><?php echo esc_html( $store_user->get_shop_name() ); ?></h1>
                            <?php } ?>

                            <ul class="dokan-store-info">
                                <li class="dokan-store-rating">

                                    <?php if ( ! empty( $seller_rating['count'] ) ): ?>
                                        <div class="star-rating dokan-seller-rating" title="<?php echo sprintf( esc_html( 'Rated %s out of 5', 'dokani' ), esc_html( $seller_rating['rating'] ) ) ?>">
                                            <span style="width: <?php echo esc_attr( ( ( $seller_rating['rating'] / 5 ) * 100 - 1 ) ); ?>%">
                                                <strong class="rating"><?php echo esc_html( $seller_rating['rating'] ); ?></strong> <?php echo esc_html( 'out of 5', 'dokani' ); ?>
                                            </span>
                                        </div>
                                    <?php else:
                                        echo esc_html( 'No Rating Found', 'dokani' );
                                    endif ?>

                                </li>

                                <?php do_action( 'dokan_store_header_info_fields',  $store_user->get_id() ); ?>
                            </ul>
                        </div>

                        <div class="store-info-column">
                            <ul class="store-meta-info">
		                        <?php if ( ! dokan_is_vendor_info_hidden( 'address' ) && isset( $store_address ) && !empty( $store_address ) ) { ?>
                                    <li>
                                        <i class="fa fa-map-marker"></i>
				                        <?php echo wp_kses_post( $store_address ); ?>
                                    </li>
		                        <?php } ?>

		                        <?php if ( ! dokan_is_vendor_info_hidden( 'phone' ) && ! empty( $store_user->get_phone() ) ) { ?>
                                    <li>
                                        <a href="tel:<?php echo esc_attr( $phone ); ?>">
                                            <i class="fa fa-phone"></i>
                                            <?php echo esc_html( $phone ); ?>
                                        </a>
                                    </li>
		                        <?php } ?>


		                        <?php if ( ! dokan_is_vendor_info_hidden( 'email' ) && $store_user->show_email() == 'yes' ) { ?>
                                    <li>
                                        <a href="mailto:<?php echo esc_attr( $email ); ?>">
                                            <i class="fa fa-envelope"></i>
                                            <?php echo esc_html( $email ); ?>
                                        </a>
                                    </li>
		                        <?php } ?>
                            </ul>

                            <ul class="store-social-profiles">
	                            <?php foreach( $social_fields as $key => $field ) { ?>
		                            <?php if ( ! empty( $social_info[ $key ] ) ) {
		                                $icon_class = str_replace( '-square', '', $field['icon'] );
                                    ?>
                                        <li class="<?php echo esc_attr( $icon_class ); ?>">
                                            <a href="<?php echo esc_url( $social_info[ $key ] ); ?>" target="_blank"><i class="fa fa-<?php echo esc_attr( $icon_class ); ?>"></i></a>
                                        </li>
		                            <?php } ?>
	                            <?php } ?>
                            </ul>
                        </div>
                    </div> <!-- .profile-info -->
                </div><!-- .profile-info-summery -->
            </div><!-- .profile-info-summery-wrapper -->
        </div> <!-- .profile-info-box -->
    </div>
</div> <!-- .profile-frame -->

<?php if ( $store_tabs ) { ?>
    <div class="dokan-store-tab-wrapper <?php echo esc_attr( $profile_layout ); ?>">
        <div class="grid-container">
            <div class="dokan-store-tabs<?php echo esc_attr( $no_banner_class_tabs ); ?>">
                <ul class="dokan-list-inline">
                    <?php foreach( $store_tabs as $key => $store_tab ) {
                        if ( ! empty( $store_tab['url'] ) ) {
                        ?>
                        <li><a href="<?php echo esc_url( $store_tab['url'] ); ?>"><?php echo esc_html( $store_tab['title'] ); ?></a></li>
                    <?php } } ?>
                    <?php do_action( 'dokan_after_store_tabs', $store_user->get_id() ); ?>
                </ul>
            </div>
        </div>
    </div>
<?php } ?>
