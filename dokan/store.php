<?php
/**
 * The Template for displaying single vendor.
 *
 * @package dokani
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$store_user   = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info   = $store_user->get_shop_info();
$map_location = $store_user->get_location();

get_header(); ?>

<?php dokan_get_template_part( 'store-header' ); ?>

    <div class="grid-container">

        <div id="primary" <?php dokani_content_class( 'dokan-single-store' ); ?>>

            <main id="main" <?php dokani_main_class(); ?>>

				<?php

				/**
				 * dokani_before_main_content hook.
				 *
				 * @since 1.0.0
				 */
				do_action( 'dokani_before_main_content' );

				?>

				<?php do_action( 'dokan_store_profile_frame_after', $store_user->data, $store_info ); ?>

				<?php if ( have_posts() ) { ?>

                    <div class="seller-items">

						<?php
						woocommerce_product_loop_start();
						while ( have_posts() ) :
							the_post();
							wc_get_template_part( 'content', 'product' );
						endwhile; // end of the loop.
						woocommerce_product_loop_end();
						?>

                    </div>

					<?php dokan_content_nav( 'nav-below' ); ?>

				<?php } else { ?>

                    <p class="dokan-info"><?php esc_html_e( 'No products were found of this vendor!', 'dokani' ); ?></p>

				<?php }

				/**
				 * dokani_after_main_content hook.
				 *
				 * @since 1.0.0
				 */
				do_action( 'dokani_after_main_content' );

				?>

            </main><!-- #main -->

        </div><!-- #primary -->

		<?php

		/**
		 * dokani_after_primary_content_area hook.
		 *
		 * @since 1.0.0
		 */
		do_action( 'dokani_after_primary_content_area' );

		dokani_construct_sidebars();

		?>

    </div>

<?php get_footer();
