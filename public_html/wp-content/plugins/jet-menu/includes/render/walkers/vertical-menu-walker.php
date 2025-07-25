<?php
namespace Jet_Menu\Render;
/**
 * Walker class
 */
class Vertical_Menu_Walker extends \Walker_Nav_Menu {

	protected $item_type = 'simple';
	private $item_settings = null;

	/**
	 * @var array
	 */
	public $styles_to_enqueue = [];

	/**
	 * [$depended_scripts description]
	 * @var array
	 */
	public $scripts_to_enqueue = [];

	/**
	 * Get styles dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_styles_to_enqueue() {
		return $this->styles_to_enqueue;
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_scripts_to_enqueue() {
		return $this->scripts_to_enqueue;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {

		if ( 'mega' === $this->get_item_type() ) {
			return;
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$indent = str_repeat( $t, $depth );

		// Default class.
		$classes     = array( 'jet-custom-nav__sub' );
		$class_names = join( ' ', $classes );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= "{$n}{$indent}<div $class_names>{$n}";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {

		if ( 'mega' === $this->get_item_type() ) {
			return;
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$indent  = str_repeat( $t, $depth );
		$output .= "$indent</div>{$n}";
	}

	/**
	 * Starts the element output.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		// Don't put any code before this!
		$this->item_settings = null;
		$this->set_item_type( $item->ID, $depth );

		if ( 'mega' === $this->get_item_type() && 0 < $depth ) {
			return;
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$settings = $this->get_settings( $item->ID );
		$indent   = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'jet-custom-nav__item';
		$classes[] = 'jet-custom-nav__item-' . $item->ID;

		jet_menu_tools()->add_menu_css( $item->ID, '.jet-custom-nav__item-' . $item->ID );

		if ( $this->is_mega_enabled( $item->ID ) ) {
			$classes[] = 'menu-item-has-children';

			if ( ! empty( $settings['custom_mega_menu_position'] ) ) {
				$classes[] = 'jet-custom-nav-mega-sub-position-' . esc_attr( $settings['custom_mega_menu_position'] );
			}
		}

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param WP_Post  $item  Menu item data object.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		$class_names = join( ' ', array_filter( $classes ) );

		$submenu_trigger = $args->settings['submenu_trigger'] ?? 'hover';
		$submenu_target  = $args->settings['submenu_target'] ?? 'item';

		$atts_wrapper = array();

		if ( ! empty( $class_names ) ) {
			$atts_wrapper['class'] = esc_attr( $class_names );
		}

		$is_dropdown_trigger = ( in_array( 'menu-item-has-children', $item->classes ) || $this->is_mega_enabled( $item->ID ) );

		if ( $is_dropdown_trigger && ( 'item' === $submenu_target || 'hover' === $submenu_trigger ) ) {
			$atts_wrapper['role']          = 'button';
			$atts_wrapper['tabindex']      = '0';
			$atts_wrapper['aria-haspopup'] = 'true';
			$atts_wrapper['aria-expanded'] = 'false';
			$atts_wrapper['aria-label']    = esc_attr( wp_strip_all_tags( $item->title ) );
		}

		$wrapper_attributes = '';

		foreach ( $atts_wrapper as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$wrapper_attributes .= sprintf( ' %s="%s"', $attr, $value );
			}
		}

		$output .= $indent . '<div' . $wrapper_attributes .'>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		$link_classes = array( 'jet-custom-nav__item-link' );

		if ( isset( $settings['hide_item_text'] ) && 'true' === $settings['hide_item_text'] ) {
			$link_classes[] = 'jet-custom-nav__item-link--hidden-label';
		}

		$atts['class'] = implode( ' ', $link_classes );

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';

		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		if ( isset( $settings['hide_item_text'] ) && 'true' === $settings['hide_item_text'] ) {
			$title = '';
		}

		$desc  = '';

		if ( ! empty( $item->description ) ) {
			$desc = sprintf(
				'<span class="jet-custom-item-desc %2$s">%1$s</span>',
				$item->description,
				( 0 === $depth ) ? 'top-level-desc' : 'sub-level-desc'
			);
		}

		$label = sprintf(
			'<span class="jet-custom-item-label %s">%s</span>',
			( 0 === $depth ) ? 'top-level-label' : 'sub-level-label',
			$title
		);

		$title = sprintf( '<span class="jet-menu-link-text">%s%s</span>', $label, $desc );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string   $title The menu item's title.
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';

		$item_icon = ! empty( $settings['menu_svg'] ) ? jet_menu_tools()->get_svg_html( $settings['menu_svg'] ) : '';

		if ( ! empty( $item_icon ) ) {
			$title = $item_icon . $title;
		}

		if ( ! empty( $settings['menu_badge'] ) || ! empty( $settings['badge_svg'] ) ) {
			$menu_badge_type = isset( $settings['menu_badge_type'] ) ? $settings['menu_badge_type'] : 'text';
			$badge_html = '';

			switch ( $menu_badge_type ) {
				case 'text':
					$badge_html = $settings['menu_badge'];
					break;
				case 'svg':
					$badge_html = jet_menu_tools()->get_svg_html( $settings['badge_svg'], false );
					break;
			}

			$title = $title . jet_menu_tools()->get_badge_html( $badge_html, $depth );
		}

		if ( in_array( 'menu-item-has-children', $item->classes ) || $this->is_mega_enabled( $item->ID ) ) {
			$arrow_icon = isset( $args->settings['dropdown_icon'] ) ? $args->settings['dropdown_icon'] : jet_menu()->svg_manager->get_svg_html( 'arrow-right' );

			if ( 'click' === $submenu_trigger && 'sub_icon' === $submenu_target ) {
				$arrow_icon_html = sprintf( '<div class="jet-dropdown-arrow" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-label="Expand submenu">%1$s</div>', $arrow_icon );
			} else {
				$arrow_icon_html = sprintf( '<div class="jet-dropdown-arrow">%1$s</div>', $arrow_icon );
			}

			$title = $title . $arrow_icon_html;
		}

		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$is_elementor = ( isset( $_GET['elementor-preview'] ) ) ? true : false;

		$mega_item = get_post_meta( $item->ID, jet_menu()->post_type_manager->meta_key(), true );

		if ( $this->is_mega_enabled( $item->ID ) ) {
			$content_type = $settings['content_type'];
			$render_instance = false;
			$mega_template_id = null;

			switch ( $content_type ) {
				case 'default':
					$mega_template_id = get_post_meta( $item->ID, 'jet-menu-item-block-editor', true );
					$mega_template_dependencies = get_post_meta( $mega_template_id, '_is_deps_ready', true );

					$is_content = empty( $mega_template_dependencies ) ? true : false;

					if ( ! empty( $mega_template_id ) ) {
						$render_instance = new \Jet_Menu\Render\Block_Editor_Content_Render( [
							'template_id' => $mega_template_id,
							'with_css'   => true,
							'is_content' => $is_content
						] );
					}

					break;
				case 'elementor':
					$mega_template_id = get_post_meta( $item->ID, 'jet-menu-item', true );

					if ( ! empty( $mega_template_id ) ) {
						$render_instance = new \Jet_Menu\Render\Elementor_Content_Render( [
							'template_id' => $mega_template_id,
							'with_css'   => true,
							'is_content' => true
						] );
					}

					break;
			}

			$template_content = __( 'Mega content is empty', 'jet-menu' );

			if ( $render_instance ) {

				$render_data = $render_instance->get_render_data();
				$render_data = apply_filters( 'jet-plugins/render/render-data', $render_data, $mega_template_id, $content_type );

				$mega_template_styles  = $render_data['styles'];
				$mega_template_scripts = $render_data['scripts'];

				$this->styles_to_enqueue = wp_parse_args( $mega_template_styles, $this->styles_to_enqueue );
				$this->scripts_to_enqueue = wp_parse_args( $mega_template_scripts, $this->scripts_to_enqueue );

				$template_content = $render_data['content'];

				$this->maybe_enqueue_styles();
				$this->maybe_enqueue_scripts();
			}

			do_action( 'jet-menu/widgets/custom-menu/mega-sub-menu/before-render', $item->ID );

			/*if ( class_exists( 'Elementor\Plugin' ) ) {
				$elementor = \Elementor\Plugin::instance();
				$content   = $elementor->frontend->get_builder_content_for_display( $mega_item );
			}*/

			do_action( 'jet-menu/widgets/custom-menu/mega-sub-menu/after-render', $item->ID );

			$item_output .= sprintf( '<div class="jet-custom-nav__mega-sub" data-template-id="%s" data-template-content="%s">%s</div>', $mega_template_id, $content_type, $template_content );

			// Fixed displaying mega and sub menu together.
			$this->set_item_type( $item->ID, $depth );
		}

		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $item        Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_el()
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Page data object. Not used.
	 * @param int      $depth  Depth of page. Not Used.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {

		if ( 'mega' === $this->get_item_type() && 0 < $depth ) {
			return;
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$output .= "</div>{$n}";
	}

	/**
	 * Store in WP Cache processed item type
	 *
	 * @param integer $item_id Current menu Item ID
	 * @param integer $depth   Current menu Item depth
	 */
	public function set_item_type( $item_id = 0, $depth = 0 ) {

		if ( 0 < $depth ) {
			return;
		}

		$item_type = 'simple';

		if ( $this->is_mega_enabled( $item_id ) ) {
			$item_type = 'mega';
		}

		wp_cache_set( 'item-type', $item_type, 'jet-custom-menu' );

	}

	/**
	 * Returns current item (for top level items) or parent item (for subs) type.
	 * @return [type] [description]
	 */
	public function get_item_type() {
		return wp_cache_get( 'item-type', 'jet-custom-menu' );
	}

	/**
	 * Check if mega menu enabled for passed item
	 *
	 * @param  int  $item_id Item ID
	 * @return boolean
	 */
	public function is_mega_enabled( $item_id = 0 ) {
		$item_settings = $this->get_settings( $item_id );

		return ( isset( $item_settings['enabled'] ) && 'true' == $item_settings['enabled'] );
	}

	/**
	 * Get item settings
	 *
	 * @param  integer $item_id Item ID
	 * @return array
	 */
	public function get_settings( $item_id = 0 ) {

		if ( null === $this->item_settings ) {
			$this->item_settings = jet_menu()->settings_manager->get_menu_item_settings( $item_id );
		}

		return $this->item_settings;
	}

	/**
	 * @return false|void
	 */
	public function maybe_enqueue_styles() {
		$style_depends = $this->get_styles_to_enqueue();

		if ( empty( $style_depends ) ) {
			return false;
		}

		foreach ( $style_depends as $key => $style_data ) {
			$style_handle = $style_data['handle'];

			if ( wp_style_is( $style_handle ) ) {
				continue;
			}

			$style_obj = $style_data['obj'];

			if ( ! isset( wp_styles()->registered[ $style_handle ] ) ) {
				wp_styles()->registered[ $style_handle ] = $style_obj;
			}

			wp_enqueue_style( $style_obj->handle, $style_obj->src, $style_obj->deps, $style_obj->ver );
		}
	}

	/**
	 * [page_menus_before_enqueue_scripts description]
	 * @return [type] [description]
	 */
	public function maybe_enqueue_scripts() {
		$script_depends = $this->get_scripts_to_enqueue();

		if ( empty( $script_depends ) ) {
			return false;
		}

		foreach ( $script_depends as $script => $script_data ) {
			$script_handle = $script_data['handle'];

			if ( wp_script_is( $script_handle ) ) {
				continue;
			}

			$script_obj = $script_data['obj'];

			if ( ! isset( wp_scripts()->registered[ $script_handle ] ) ) {
				wp_scripts()->registered[ $script_handle ] = $script_obj;
			}

			wp_enqueue_script( $script_obj->handle, $script_obj->src, $script_obj->deps, $script_obj->ver );
			wp_scripts()->print_extra_script( $script_obj->handle );
		}
	}

}
