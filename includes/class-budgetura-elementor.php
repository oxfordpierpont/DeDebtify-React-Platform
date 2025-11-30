<?php
/**
 * Elementor Integration Class
 *
 * Handles Elementor integration for Budgetura widgets.
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/includes
 */

class Budgetura_Elementor {

    /**
     * Initialize the class.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Register Elementor widgets
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

        // Register widget categories
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_categories' ) );
    }

    /**
     * Register widget categories.
     *
     * @since    1.0.0
     * @param    object    $elements_manager
     */
    public function register_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'budgetura',
            array(
                'title' => __( 'Budgetura', 'budgetura' ),
                'icon' => 'fa fa-money-bill',
            )
        );
    }

    /**
     * Register Elementor widgets.
     *
     * @since    1.0.0
     * @param    object    $widgets_manager
     */
    public function register_widgets( $widgets_manager ) {
        // Widget files will be loaded here in future updates
        // For now, users can use shortcodes in Elementor's shortcode widget

        // Example for future implementation:
        // require_once BUDGETURA_PLUGIN_DIR . 'includes/elementor/widgets/dashboard-widget.php';
        // $widgets_manager->register( new \Budgetura_Dashboard_Widget() );
    }
}

// Initialize Elementor integration
new Budgetura_Elementor();
