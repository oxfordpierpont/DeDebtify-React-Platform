<?php
/**
 * Page Templates Handler
 *
 * Registers custom page templates
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/includes
 */

class Budgetura_Page_Templates {

    /**
     * Templates array
     *
     * @var array
     */
    protected $templates;

    /**
     * Initialize the class
     */
    public function __construct() {
        $this->templates = array(
            'page-templates/template-dashboard.php'     => 'Budgetura Dashboard',
            'page-templates/template-credit-cards.php'  => 'Budgetura Credit Cards',
            'page-templates/template-loans.php'         => 'Budgetura Loans',
            'page-templates/template-mortgages.php'     => 'Budgetura Mortgages',
            'page-templates/template-bills.php'         => 'Budgetura Bills',
            'page-templates/template-goals.php'         => 'Budgetura Goals',
            'page-templates/template-action-plan.php'   => 'Budgetura Action Plan',
            'page-templates/template-snapshots.php'     => 'Budgetura Snapshots',
        );

        // Add filters
        add_filter( 'theme_page_templates', array( $this, 'add_page_templates' ) );
        add_filter( 'template_include', array( $this, 'load_page_template' ) );
    }

    /**
     * Add custom templates to the page template dropdown
     *
     * @param array $templates
     * @return array
     */
    public function add_page_templates( $templates ) {
        $templates = array_merge( $templates, $this->templates );
        return $templates;
    }

    /**
     * Load the custom page template
     *
     * @param string $template
     * @return string
     */
    public function load_page_template( $template ) {
        global $post;

        if ( ! $post ) {
            return $template;
        }

        $page_template = get_post_meta( $post->ID, '_wp_page_template', true );

        if ( ! isset( $this->templates[ $page_template ] ) ) {
            return $template;
        }

        $file = BUDGETURA_PLUGIN_DIR . $page_template;

        if ( file_exists( $file ) ) {
            return $file;
        }

        return $template;
    }
}
