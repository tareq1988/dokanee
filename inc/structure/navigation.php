<?php
/**
 * Navigation elements.
 *
 * @package dokani
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'dokani_navigation_position' ) ) {
	/**
	 * Build the navigation.
	 *
	 * @since 1.0.0
	 */
	function dokani_navigation_position() {
		?>
		<nav itemtype="https://schema.org/SiteNavigationElement" itemscope="itemscope" id="site-navigation" <?php dokani_navigation_class(); ?>>
			<div <?php dokani_inside_navigation_class(); ?>>
				<?php
				/**
				 * dokani_inside_navigation hook.
				 *
				 * @since 1.0.0
				 *
				 * @hooked dokani_responsive_user_menu - 5
				 * @hooked dokani_navigation_search - 10
				 * @hooked dokani_mobile_menu_search_icon - 10
				 */
				do_action( 'dokani_inside_navigation' );
				?>
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<?php  do_action( 'dokani_inside_mobile_menu' ); ?>
					<span class="mobile-menu screen-reader-text"><?php echo esc_html( apply_filters( 'dokani_mobile_menu_label', __( 'Menu', 'dokani' ) ) ); ?></span>
				</button>

                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'container' => 'div',
                            'container_class' => 'main-nav',
                            'container_id' => 'primary-menu',
                            'menu_class' => '',
                            'fallback_cb' => 'dokani_menu_fallback',
                            'items_wrap' => '<ul id="%1$s" class="%2$s ' . join( ' ', dokani_get_menu_class() ) . '">%3$s</ul>'
                        )
                    );

	                if ( has_nav_menu( 'responsive_menu' ) ) {
		                wp_nav_menu(
			                array(
				                'theme_location'  => 'responsive_menu',
				                'container'       => 'div',
				                'container_class' => 'main-nav',
				                'container_id'    => 'responsive-menu',
				                'menu_class'      => '',
				                'fallback_cb'     => 'dokani_menu_fallback',
				                'items_wrap'      => '<ul id="%1$s" class="%2$s ' . join( ' ', dokani_get_menu_class() ) . '">%3$s</ul>'
			                )
		                );
                    } else {
	                    echo '<div id="responsive-menu" class="main-nav"><ul class=" menu sf-menu"><li class="menu-item">';
		                echo '<li><a class="add_responsive_menu_label" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add Responsive Menu', 'dokani' ) . '</a></li>';
                        echo '</ul></div>';
                    }

                } else {
                    echo '<a class="add_primary_menu_label" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add Primary Menu', 'dokani' ) . '</a>';
                }
                ?>

			</div><!-- .inside-navigation -->
		</nav><!-- #site-navigation -->
		<?php
	}
}

if ( ! function_exists( 'dokani_menu_fallback' ) ) {
	/**
	 * Menu fallback.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args
	 * @return string
	 */
	function dokani_menu_fallback( $args ) {
		$dokani_settings = wp_parse_args(
			get_option( 'dokani_settings', array() ),
			dokani_get_defaults()
		);
		?>
		<div id="primary-menu" class="main-nav">
			<ul <?php dokani_menu_class(); ?>>
				<?php
				$args = array(
					'sort_column' => 'menu_order',
					'title_li' => '',
					'walker' => new dokani_Page_Walker()
				);

				wp_list_pages( $args );

				if ( 'cart-nav' == $dokani_settings['cart_position_setting'] ){
					echo wp_kses_post( dokani_cart_position() );
				}

				?>
			</ul>
		</div><!-- .main-nav -->
		<?php
	}
}

/**
 * Generate the navigation based on settings
 *
 * It would be better to have all of these inside one action, but these
 * are kept this way to maintain backward compatibility for people
 * un-hooking and moving the navigation/changing the priority.
 *
 * @since 1.0.0
 */

if ( ! function_exists( 'dokani_add_navigation_after_header' ) ) {
	add_action( 'dokani_after_header', 'dokani_add_navigation_after_header', 5 );
	function dokani_add_navigation_after_header() {
		if ( 'nav-below-header' == dokani_get_navigation_location() ) {
			dokani_navigation_position();
		}
	}
}

if ( ! function_exists( 'dokani_add_navigation_float_right' ) ) {
	add_action( 'dokani_after_header_right', 'dokani_add_navigation_float_right', 5 );
	function dokani_add_navigation_float_right() {
		if ( 'nav-float-right' == dokani_get_navigation_location() ) {
			dokani_navigation_position();
		}
	}
}

if ( ! class_exists( 'dokani_Page_Walker' ) && class_exists( 'Walker_Page' ) ) {
	/**
	 * Add current-menu-item to the current item if no theme location is set
	 * This means we don't have to duplicate CSS properties for current_page_item and current-menu-item
	 *
	 * @since 1.0.0
	 */
	class dokani_Page_Walker extends Walker_Page {
		function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
			$css_class = array( 'page_item', 'page-item-' . $page->ID );
			$button = '';

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				$css_class[] = 'menu-item-has-children';
				$button = '<span role="button" class="dropdown-menu-toggle" aria-expanded="false"></span>';
			}

			if ( ! empty( $current_page ) ) {
				$_current_page = get_post( $current_page );
				if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
					$css_class[] = 'current-menu-ancestor';
				}
				if ( $page->ID == $current_page ) {
					$css_class[] = 'current-menu-item';
				} elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
					$css_class[] = 'current-menu-parent';
				}
			} elseif ( $page->ID == get_option('page_for_posts') ) {
				$css_class[] = 'current-menu-parent';
			}

			$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

			$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
			$args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

			$output .= sprintf(
				'<li class="%s"><a href="%s">%s%s%s%s</a>',
				$css_classes,
				get_permalink( $page->ID ),
				$args['link_before'],
				apply_filters( 'the_title', $page->post_title, $page->ID ),
				$args['link_after'],
				$button
			);
		}
	}
}

if ( ! function_exists( 'dokani_dropdown_icon_to_menu_link' ) ) {
	add_filter( 'nav_menu_item_title', 'dokani_dropdown_icon_to_menu_link', 10, 4 );
	/**
	 * Add dropdown icon if menu item has children.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title The menu item title.
	 * @param WP_Post $item All of our menu item data.
	 * @param stdClass $args All of our menu item args.
	 * @param int $dept Depth of menu item.
	 * @return string The menu item.
	 */
	function dokani_dropdown_icon_to_menu_link( $title, $item, $args, $depth ) {

		$role = 'presentation';
		$tabindex = '';

		if ( 'click-arrow' === dokani_get_setting( 'nav_dropdown_type' ) ) {
			$role = 'button';
			$tabindex = ' tabindex="0"';
		}

		// Loop through our menu items and add our dropdown icons.
		if ( 'main-nav' === $args->container_class ) {
			foreach ( $item->classes as $value ) {
				if ( 'menu-item-has-children' === $value  ) {
					$title = $title . '<span role="' . $role . '" class="dropdown-menu-toggle"' . $tabindex .'></span>';
				}
			}
		}

		// Return our title.
		return $title;
	}
}

if ( ! function_exists( 'dokani_menu_cart' ) ) {
	add_filter( 'wp_nav_menu_items', 'dokani_menu_cart', 5, 2 );
	/**
	 * Add cart to primary menu if set
	 *
	 * @since 1.0.0
	 *
	 * @param string $nav The HTML list content for the menu items.
	 * @param stdClass $args An object containing wp_nav_menu() arguments.
	 * @return string The cart menu item.
	 */
	function dokani_menu_cart( $nav, $args ) {
		$dokani_settings = wp_parse_args(
			get_option( 'dokani_settings', array() ),
			dokani_get_defaults()
		);

		// If the cart isn't enabled, return the regular nav.
		if ( 'cart-nav' !== $dokani_settings['cart_position_setting'] ) {
			return $nav;
		}

		// If our primary menu is set, add the cart.
		if ( $args->theme_location == 'primary' ) {
			return $nav . dokani_cart_position();
		}

		// Our primary menu isn't set, return the regular nav.
		// In this case, the cart is added to the dokani_menu_fallback() function in navigation.php.
		return $nav;
	}
}

add_action( 'wp_footer', 'dokani_clone_sidebar_navigation' );
/**
 * Clone our sidebar navigation and place it below the header.
 * This places our mobile menu in a more user-friendly location.
 *
 * We're not using wp_add_inline_script() as this needs to happens
 * before menu.js is enqueued.
 *
 * @since 1.0.0
 */
function dokani_clone_sidebar_navigation() {
	if ( 'nav-left-sidebar' !== dokani_get_navigation_location() && 'nav-right-sidebar' !== dokani_get_navigation_location() ) {
		return;
	}
	?>
	<script>
		var target, nav, clone;
		nav = document.getElementById( 'site-navigation' );
		if ( nav ) {
			clone = nav.cloneNode( true );
			clone.className += ' sidebar-nav-mobile';
			clone.setAttribute( 'aria-label', '<?php esc_attr_e( 'Mobile Menu', 'dokani' ); ?>' );
			target = document.getElementById( 'masthead' );
			if ( target ) {
				target.insertAdjacentHTML( 'afterend', clone.outerHTML );
			} else {
				document.body.insertAdjacentHTML( 'afterbegin', clone.outerHTML )
			}
		}
	</script>
	<?php
}

if ( ! function_exists( 'dokani_menu_responsive_search' ) ) {
    if ( function_exists( 'dokan_pro' ) ) {
	    add_filter( 'wp_nav_menu_items', 'dokani_menu_responsive_search', 10, 2 );
    }
	/**
	 * Build responsive search
	 *
	 * @since 1.0.0
	 */
	function dokani_menu_responsive_search( $nav, $args ) {

		if ( $args->theme_location == 'responsive_menu' ) {
			return $nav . '<li class="menu-item">' . the_widget( 'Dokan_Live_Search_Widget' ) . '</li>';
		}

		return $nav;
	}
}

if ( ! function_exists( 'dokani_responsive_vendor_menu' ) ) {
	add_filter( 'wp_nav_menu_items', 'dokani_responsive_vendor_menu', 5, 2 );
	/**
	 * Build responsive vendor menu
	 *
	 * @since 1.0.0
	 */
	function dokani_responsive_vendor_menu( $nav, $args ) {
		global $current_user;
		$user_id = $current_user->ID;
		$nav_urls = function_exists( 'dokan_get_dashboard_nav' ) ? dokan_get_dashboard_nav() : false;

		if ( $args->theme_location == 'responsive_menu' ) {

            if ( function_exists( 'dokan_is_user_seller' ) && dokan_is_user_seller( $user_id ) ) {
                $vendor = '';
                $vendor .= '<li class="menu-item menu-item-has-children"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'. __( 'Vendor Dashboard', 'dokani' ) .'<span role="presentation" class="dropdown-menu-toggle" aria-expanded="false"></span></a>';

                $vendor .= '<ul class="sub-menu"><li><a href="<?php echo dokan_get_store_url( $user_id ); ?>" target="_blank">'. __( 'Visit your store', 'dokani' ) .'<i class="fa fa-external-link"></i></a></li>
                            <li class="divider"></li>';
                           foreach ( $nav_urls as $key => $item ) {;
                                $vendor .= '<li><a href="'.$item['url'].'">'.$item['icon'].' &nbsp;'.$item['title'].'</a></li>';
                            }
	            $vendor .='</ul></li>';

			    return $nav . $vendor;
			}
		}

		return $nav;
	}
}
