<?php
if( ! is_admin() ) {
	return;
}

/**
 * Load Admin files.
 */
function astro_sb_load_admin_files() {
    if(!is_admin_bar_showing()) return;

	/**
	 * Main admin styles and scripts
	 */
	wp_enqueue_style ( 'astro-sticky-buttons-admin-styles', plugins_url('/css/astro-sticky-buttons-admin.css', __FILE__), array(), astro_sb_plugin_data('Version') );

	wp_register_script( 'astro-sticky-buttons-admin-scripts', plugins_url('/js/astro-sticky-buttons-admin.js', __FILE__), array('jquery'), astro_sb_plugin_data('Version'), true );
	wp_enqueue_script( 'astro-sticky-buttons-admin-scripts' );

	/**
	 * Include WP Iris color picker.
	 */
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script(
		'iris',
		admin_url( 'js/iris.min.js' ),
		array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
		false,
		1
	);
	wp_enqueue_script(
		'wp-color-picker',
		admin_url( 'js/color-picker.min.js' ),
		array( 'iris' ),
		false,
		1
	);
	$colorpicker_l10n = array(
		'clear' => __( 'Clear', 'astro-sticky-buttons' ),
		'defaultString' => __( 'Default', 'astro-sticky-buttons' ),
		'pick' => __( 'Select Color', 'astro-sticky-buttons' ),
		'current' => __( 'Current Color', 'astro-sticky-buttons' ),
	);
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

	/**
     * Include the plugin navigation.
     */
    include(plugin_dir_path(__FILE__) . 'includes/tabs/tabs-nav.php');
}
add_action( 'admin_enqueue_scripts', 'astro_sb_load_admin_files' );

/**
 * Register options settings.
 */
function astro_sb_register_settings() {

    if (isset($_REQUEST['option_page']) && !empty($_REQUEST['option_page'])) {

		if (strpos($_REQUEST['option_page'], ASTRO_SB_PREFIX) === 0) {

            $option_page = sanitize_text_field($_REQUEST['option_page']);
            $tab = explode('_', $option_page);
            $option_group = $option_page;
            $option_names = astro_sb_option_names(end($tab));

            if (!empty($option_names)) {

                foreach ($option_names as $option_name) {
                    $arr = array();
                    if (strpos($option_name, '_options')) {
                        $arr = array('type' => 'array');
                    }
                    register_setting( $option_group, $option_name, array($arr) );
                }

            }

		}

	}else{
		$tab = 'settings';
		$option_group = ASTRO_SB_PREFIX . '_' . $tab;
		$option_names = astro_sb_option_names($tab);

		if (!empty($option_names)) {
			foreach ($option_names as $option_name) {
				$arr = array();
				if (strpos($option_name, '_options')) {
					$arr = array('type' => 'array');
				}
				register_setting($option_group, $option_name, array($arr));
			}
		}

		$tab = 'layout';
		$option_group = ASTRO_SB_PREFIX . '_' . $tab;
		$option_names = astro_sb_option_names($tab);

		if (!empty($option_names)) {

            foreach ($option_names as $option_name) {
                $arr = array();
                if (strpos($option_name, '_options')) {
                    $arr = array('type' => 'array');
                }
                register_setting( $option_group, $option_name, array($arr) );
            }

		}
    }

}
add_action( 'admin_init', 'astro_sb_register_settings' );

/**
 * Display the plugin pages in menu.
 */
if (class_exists('Astro_Plugin_Panel')) {

	add_action('astro_plugin_panel_pages', 'astro_sb_plugin_panel_submenu');

	function astro_sb_plugin_panel_submenu() {
		add_submenu_page(
			'astro-plugin-panel',
			__('Sticky buttons', 'astro-sticky-buttons'),
			__('Sticky buttons', 'astro-sticky-buttons'),
			'manage_options',
			'astro-sticky-buttons',
			'astro_sb_options'
		);
	}

}

/**
 * Display the plugin panel to do define the settings.
 */
function astro_sb_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'astro-sticky-buttons' ) );
    }

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(astro_sb_plugin_data('Name')); ?></h1>
        <?php

        $tab  = 'settings'; // default panel
        if (isset($_REQUEST['tab']) && !empty($_REQUEST['tab'])) {
			$tab  = sanitize_text_field($_REQUEST['tab']);
            if (str_contains('-',$tab)) {
				$tab = explode('-', $tab);
				$tab = end($tab);
			}
        }

		astro_sb_tabs_nav($tab);

        $tab_file = plugin_dir_path( __FILE__ ) . 'includes/tabs/tab-' . $tab .'.php';
        if (file_exists($tab_file)) {
			include( plugin_dir_path( __FILE__ ) . 'includes/tabs/tab-' . $tab .'.php' );
		}

        ?>
    </div>
    <?php
}