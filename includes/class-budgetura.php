<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/includes
 */

class Budgetura {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->version = BUDGETURA_VERSION;
        $this->plugin_name = 'budgetura';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_cpt_hooks();
        $this->define_api_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Load helper functions
        require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-helpers.php';

        // Load CPT registration class
        require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-cpt.php';

        // Load calculations class
        require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-calculations.php';

        // Load REST API class
        require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-api.php';

        // Load page templates class
        require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-page-templates.php';

        // Load dummy data class
        require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-dummy-data.php';

        // Load PWA class
        require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-pwa.php';

        // Load Elementor integration class (if Elementor is active)
        if ( did_action( 'elementor/loaded' ) ) {
            require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-elementor.php';
        }

        // Initialize page templates
        new Budgetura_Page_Templates();

        // Initialize PWA
        new Budgetura_PWA();
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        // Enqueue admin styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Add admin menu pages
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Add settings link to plugins page
        add_filter( 'plugin_action_links_' . BUDGETURA_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        // Enqueue public styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );

        // Add dynamic custom CSS
        add_action( 'wp_head', array( $this, 'output_custom_css' ) );

        // Add shortcode support
        add_shortcode( 'budgetura_dashboard', array( $this, 'render_dashboard_shortcode' ) );
        add_shortcode( 'budgetura_credit_cards', array( $this, 'render_credit_cards_shortcode' ) );
        add_shortcode( 'budgetura_loans', array( $this, 'render_loans_shortcode' ) );
        add_shortcode( 'budgetura_mortgages', array( $this, 'render_mortgages_shortcode' ) );
        add_shortcode( 'budgetura_bills', array( $this, 'render_bills_shortcode' ) );
        add_shortcode( 'budgetura_goals', array( $this, 'render_goals_shortcode' ) );
        add_shortcode( 'budgetura_action_plan', array( $this, 'render_action_plan_shortcode' ) );
        add_shortcode( 'budgetura_snapshots', array( $this, 'render_snapshots_shortcode' ) );
        add_shortcode( 'budgetura_ai_coach', array( $this, 'render_ai_coach_shortcode' ) );
    }

    /**
     * Register all hooks related to Custom Post Types.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_cpt_hooks() {
        $cpt = new Budgetura_CPT();

        // Register CPTs
        add_action( 'init', array( $cpt, 'register_post_types' ) );

        // Add meta boxes
        add_action( 'add_meta_boxes', array( $cpt, 'add_meta_boxes' ) );

        // Save meta data
        add_action( 'save_post', array( $cpt, 'save_meta_data' ), 10, 2 );

        // Modify post type columns
        add_filter( 'manage_dd_credit_card_posts_columns', array( $cpt, 'set_custom_columns' ) );
        add_action( 'manage_dd_credit_card_posts_custom_column', array( $cpt, 'custom_column_content' ), 10, 2 );
    }

    /**
     * Register all hooks related to the REST API.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_api_hooks() {
        $api = new Budgetura_API();

        // Register REST API endpoints
        add_action( 'rest_api_init', array( $api, 'register_routes' ) );
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // Plugin is now running
    }

    /**
     * Enqueue admin-specific styles and scripts.
     *
     * @since    1.0.0
     */
    public function enqueue_admin_assets() {
        // Enqueue design system first (base components)
        wp_enqueue_style(
            $this->plugin_name . '-design-system',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-design-system.css',
            array(),
            $this->version,
            'all'
        );

        // Enqueue admin styles (depends on design system)
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-admin.css',
            array( $this->plugin_name . '-design-system' ),
            $this->version,
            'all'
        );

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-admin.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name . '-admin',
            'budgeturaAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'budgetura_admin_nonce' ),
                'restUrl' => rest_url( 'budgetura/v1/' ),
                'restNonce' => wp_create_nonce( 'wp_rest' ),
            )
        );
    }

    /**
     * Enqueue public-facing styles and scripts.
     *
     * @since    1.0.0
     */
    public function enqueue_public_assets() {
        // Enqueue design system first (base components)
        wp_enqueue_style(
            $this->plugin_name . '-design-system',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-design-system.css',
            array(),
            $this->version,
            'all'
        );

        // Enqueue public styles (depends on design system)
        wp_enqueue_style(
            $this->plugin_name . '-public',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-public.css',
            array( $this->plugin_name . '-design-system' ),
            $this->version,
            'all'
        );

        // Enqueue enhanced styles (integrates design system with components)
        wp_enqueue_style(
            $this->plugin_name . '-enhanced',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-enhanced.css',
            array( $this->plugin_name . '-design-system', $this->plugin_name . '-public' ),
            $this->version,
            'all'
        );

        // Enqueue mobile app styles (modern Shadcn-inspired UI)
        wp_enqueue_style(
            $this->plugin_name . '-mobile-app',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-mobile-app.css',
            array( $this->plugin_name . '-enhanced' ),
            $this->version,
            'all'
        );

        // Enqueue sidebar navigation styles
        wp_enqueue_style(
            $this->plugin_name . '-sidebar',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-sidebar.css',
            array( $this->plugin_name . '-mobile-app' ),
            $this->version,
            'all'
        );

        // Enqueue exact design styles (from React source)
        wp_enqueue_style(
            $this->plugin_name . '-exact-design',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-exact-design.css',
            array( $this->plugin_name . '-sidebar' ),
            $this->version,
            'all'
        );

        // Enqueue AI Coach styles
        wp_enqueue_style(
            $this->plugin_name . '-ai-coach',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-ai-coach.css',
            array( $this->plugin_name . '-exact-design' ),
            $this->version,
            'all'
        );

        // Enqueue PWA styles
        wp_enqueue_style(
            $this->plugin_name . '-pwa',
            BUDGETURA_PLUGIN_URL . 'assets/css/budgetura-pwa.css',
            array( $this->plugin_name . '-mobile-app' ),
            $this->version,
            'all'
        );

        wp_enqueue_script(
            $this->plugin_name . '-public',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-public.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-calculator',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-calculator.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-managers',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-managers.js',
            array( 'jquery', $this->plugin_name . '-calculator' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-action-plan',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-action-plan.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-mortgages',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-mortgages.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-ai-coach',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-ai-coach.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-pwa',
            BUDGETURA_PLUGIN_URL . 'assets/js/budgetura-pwa.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        // Localize PWA script
        wp_localize_script(
            $this->plugin_name . '-pwa',
            'budgeturaPWA',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'budgetura_nonce' ),
                'serviceWorkerUrl' => site_url( '/budgetura-sw.js' ),
                'pushEnabled' => get_option( 'budgetura_pwa_push_enabled', false ),
                'vapidPublicKey' => get_option( 'budgetura_pwa_vapid_public_key', '' ),
            )
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name . '-public',
            'budgetura',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'budgetura_nonce' ),
                'restUrl' => rest_url( 'budgetura/v1/' ),
                'restNonce' => wp_create_nonce( 'wp_rest' ),
                'userId' => get_current_user_id(),
            )
        );

        // Localize script for managers with translations
        wp_localize_script(
            $this->plugin_name . '-managers',
            'budgeturaL10n',
            array(
                'edit' => __( 'Edit', 'budgetura' ),
                'delete' => __( 'Delete', 'budgetura' ),
                'confirm_delete' => __( 'Are you sure you want to delete this item?', 'budgetura' ),
                'saving' => __( 'Saving...', 'budgetura' ),
                'loading' => __( 'Loading...', 'budgetura' ),
                'error' => __( 'An error occurred. Please try again.', 'budgetura' ),
            )
        );
    }

    /**
     * Output custom CSS from settings.
     *
     * @since    1.0.0
     */
    public function output_custom_css() {
        $primary_color = get_option( 'budgetura_primary_color', '#3b82f6' );
        $success_color = get_option( 'budgetura_success_color', '#10b981' );
        $warning_color = get_option( 'budgetura_warning_color', '#f59e0b' );
        $danger_color = get_option( 'budgetura_danger_color', '#ef4444' );
        $font_family = get_option( 'budgetura_font_family', 'System Default' );
        $border_radius = get_option( 'budgetura_border_radius', 8 );

        // Convert hex to HSL for CSS custom properties
        $primary_hsl = $this->hex_to_hsl( $primary_color );
        $success_hsl = $this->hex_to_hsl( $success_color );
        $warning_hsl = $this->hex_to_hsl( $warning_color );
        $danger_hsl = $this->hex_to_hsl( $danger_color );

        $font_css = '';
        if ( $font_family !== 'System Default' ) {
            $font_css = "font-family: {$font_family};";
        }

        echo '<style id="budgetura-custom-css">';
        echo ':root {';
        echo "--dd-primary: {$primary_hsl};";
        echo "--dd-success: {$success_hsl};";
        echo "--dd-warning: {$warning_hsl};";
        echo "--dd-destructive: {$danger_hsl};";
        echo "--dd-radius: {$border_radius}px;";
        echo '}';
        if ( ! empty( $font_css ) ) {
            echo ".budgetura-dashboard,";
            echo ".budgetura-card,";
            echo ".budgetura-form,";
            echo ".dd-btn,";
            echo ".budgetura-stat-card {";
            echo $font_css;
            echo '}';
        }
        echo '</style>';
    }

    /**
     * Convert hex color to HSL.
     *
     * @since    1.0.0
     * @param    string    $hex
     * @return   string
     */
    private function hex_to_hsl( $hex ) {
        $hex = str_replace( '#', '', $hex );
        $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
        $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
        $b = hexdec( substr( $hex, 4, 2 ) ) / 255;

        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $l = ( $max + $min ) / 2;

        if ( $max === $min ) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );

            switch ( $max ) {
                case $r:
                    $h = ( $g - $b ) / $d + ( $g < $b ? 6 : 0 );
                    break;
                case $g:
                    $h = ( $b - $r ) / $d + 2;
                    break;
                case $b:
                    $h = ( $r - $g ) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        $h = round( $h * 360 );
        $s = round( $s * 100 );
        $l = round( $l * 100 );

        return "{$h} {$s}% {$l}%";
    }

    /**
     * Add admin menu pages.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Budgetura', 'budgetura' ),
            __( 'Budgetura', 'budgetura' ),
            'manage_options',
            'budgetura',
            array( $this, 'render_admin_dashboard' ),
            'dashicons-money-alt',
            30
        );

        add_submenu_page(
            'budgetura',
            __( 'Dashboard', 'budgetura' ),
            __( 'Dashboard', 'budgetura' ),
            'manage_options',
            'budgetura',
            array( $this, 'render_admin_dashboard' )
        );

        // Show setup page if not hidden
        if ( ! get_option( 'budgetura_hide_setup_page', false ) ) {
            add_submenu_page(
                'budgetura',
                __( 'Setup Guide', 'budgetura' ),
                __( 'Setup Guide', 'budgetura' ) . ' <span class="dashicons dashicons-star-filled" style="color: #f0b849; font-size: 14px;"></span>',
                'manage_options',
                'budgetura-setup',
                array( $this, 'render_setup_page' )
            );
        }

        add_submenu_page(
            'budgetura',
            __( 'Reports', 'budgetura' ),
            __( 'Reports', 'budgetura' ),
            'manage_options',
            'budgetura-reports',
            array( $this, 'render_reports_page' )
        );

        add_submenu_page(
            'budgetura',
            __( 'Settings', 'budgetura' ),
            __( 'Settings', 'budgetura' ),
            'manage_options',
            'budgetura-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Render admin dashboard.
     *
     * @since    1.0.0
     */
    public function render_admin_dashboard() {
        require_once BUDGETURA_PLUGIN_DIR . 'admin/dashboard.php';
    }

    /**
     * Render setup guide page.
     *
     * @since    1.0.0
     */
    public function render_setup_page() {
        require_once BUDGETURA_PLUGIN_DIR . 'admin/setup-page.php';
    }

    /**
     * Render reports page.
     *
     * @since    1.0.0
     */
    public function render_reports_page() {
        require_once BUDGETURA_PLUGIN_DIR . 'admin/reports-page.php';
    }

    /**
     * Render settings page.
     *
     * @since    1.0.0
     */
    public function render_settings_page() {
        require_once BUDGETURA_PLUGIN_DIR . 'admin/settings-page.php';
    }

    /**
     * Add settings link to plugins page.
     *
     * @since    1.0.0
     */
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=budgetura-settings' ) . '">' . __( 'Settings', 'budgetura' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Render dashboard shortcode.
     *
     * @since    1.0.0
     */
    public function render_dashboard_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to view your dashboard.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/dashboard.php';
        return ob_get_clean();
    }

    /**
     * Render credit cards manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_credit_cards_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your credit cards.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/credit-cards.php';
        return ob_get_clean();
    }

    /**
     * Render loans manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_loans_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your loans.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/loans.php';
        return ob_get_clean();
    }

    /**
     * Render mortgages manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_mortgages_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your mortgage.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/mortgages.php';
        return ob_get_clean();
    }

    /**
     * Render bills manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_bills_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your bills.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/bills.php';
        return ob_get_clean();
    }

    /**
     * Render goals manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_goals_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your goals.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/goals.php';
        return ob_get_clean();
    }

    /**
     * Render action plan shortcode.
     *
     * @since    1.0.0
     */
    public function render_action_plan_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to view your debt action plan.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/action-plan.php';
        return ob_get_clean();
    }

    /**
     * Render snapshots shortcode.
     *
     * @since    1.0.0
     */
    public function render_snapshots_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to view your financial snapshots.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/snapshots.php';
        return ob_get_clean();
    }

    /**
     * Render AI Coach shortcode
     *
     * @since    1.0.0
     */
    public function render_ai_coach_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to chat with your AI Financial Coach.', 'budgetura' ) . '</p>';
        }

        ob_start();
        require_once BUDGETURA_PLUGIN_DIR . 'templates/ai-coach.php';
        return ob_get_clean();
    }
}
