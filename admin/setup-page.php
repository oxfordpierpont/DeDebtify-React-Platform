<?php
/**
 * Setup/Welcome Page
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get page IDs
$page_ids = get_option( 'budgetura_page_ids', array() );
$dashboard_page_id = get_option( 'budgetura_dashboard_page_id', 0 );
?>

<div class="wrap">
    <h1><?php _e( 'Welcome to Budgetura!', 'budgetura' ); ?></h1>
    <p class="about-text"><?php _e( 'Thank you for installing Budgetura! Your debt management system is ready to use.', 'budgetura' ); ?></p>

    <div class="budgetura-admin-section">
        <h2><?php _e( 'Setup Complete!', 'budgetura' ); ?></h2>

        <?php if ( ! empty( $page_ids ) ) : ?>
            <p><?php _e( 'The following pages have been created automatically:', 'budgetura' ); ?></p>

            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e( 'Page', 'budgetura' ); ?></th>
                        <th><?php _e( 'URL', 'budgetura' ); ?></th>
                        <th><?php _e( 'Actions', 'budgetura' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $page_ids as $key => $page_id ) : ?>
                        <?php $page = get_post( $page_id ); ?>
                        <?php if ( $page ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $page->post_title ); ?></strong></td>
                                <td><code><?php echo esc_html( get_permalink( $page_id ) ); ?></code></td>
                                <td>
                                    <a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>" class="button" target="_blank"><?php _e( 'View', 'budgetura' ); ?></a>
                                    <a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>" class="button"><?php _e( 'Edit', 'budgetura' ); ?></a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="notice notice-warning inline">
                <p><?php _e( 'No pages were created. Please deactivate and reactivate the plugin to create the pages.', 'budgetura' ); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="budgetura-admin-section">
        <h2><?php _e( 'Next Steps', 'budgetura' ); ?></h2>

        <div class="budgetura-setup-steps">
            <div class="budgetura-step">
                <span class="budgetura-step-number">1</span>
                <div class="budgetura-step-content">
                    <h3><?php _e( 'Create a Navigation Menu', 'budgetura' ); ?></h3>
                    <p><?php _e( 'Add the Budgetura pages to your WordPress menu so users can navigate between them.', 'budgetura' ); ?></p>
                    <a href="<?php echo admin_url( 'nav-menus.php' ); ?>" class="button button-primary"><?php _e( 'Go to Menus', 'budgetura' ); ?></a>
                </div>
            </div>

            <div class="budgetura-step">
                <span class="budgetura-step-number">2</span>
                <div class="budgetura-step-content">
                    <h3><?php _e( 'Configure Settings', 'budgetura' ); ?></h3>
                    <p><?php _e( 'Set your currency, default interest rates, and notification preferences.', 'budgetura' ); ?></p>
                    <a href="<?php echo admin_url( 'admin.php?page=budgetura-settings' ); ?>" class="button button-primary"><?php _e( 'Go to Settings', 'budgetura' ); ?></a>
                </div>
            </div>

            <div class="budgetura-step">
                <span class="budgetura-step-number">3</span>
                <div class="budgetura-step-content">
                    <h3><?php _e( 'Test the User Experience', 'budgetura' ); ?></h3>
                    <p><?php _e( 'Visit the dashboard page and start adding your financial data.', 'budgetura' ); ?></p>
                    <?php if ( $dashboard_page_id ) : ?>
                        <a href="<?php echo get_permalink( $dashboard_page_id ); ?>" class="button button-primary" target="_blank"><?php _e( 'View Dashboard', 'budgetura' ); ?></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="budgetura-step">
                <span class="budgetura-step-number">4</span>
                <div class="budgetura-step-content">
                    <h3><?php _e( 'Restrict Access (Optional)', 'budgetura' ); ?></h3>
                    <p><?php _e( 'Consider using a membership plugin to restrict pages to logged-in users only.', 'budgetura' ); ?></p>
                    <p class="description"><?php _e( 'Recommended plugins: MemberPress, Restrict Content Pro, or WP Members.', 'budgetura' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="budgetura-admin-section">
        <h2><?php _e( 'Available Shortcodes', 'budgetura' ); ?></h2>
        <p><?php _e( 'You can use these shortcodes in any page or post:', 'budgetura' ); ?></p>

        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e( 'Shortcode', 'budgetura' ); ?></th>
                    <th><?php _e( 'Description', 'budgetura' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>[budgetura_dashboard]</code></td>
                    <td><?php _e( 'Display the complete user dashboard with financial overview', 'budgetura' ); ?></td>
                </tr>
                <tr>
                    <td><code>[budgetura_credit_cards]</code></td>
                    <td><?php _e( 'Credit card management interface', 'budgetura' ); ?></td>
                </tr>
                <tr>
                    <td><code>[budgetura_loans]</code></td>
                    <td><?php _e( 'Loans management interface', 'budgetura' ); ?></td>
                </tr>
                <tr>
                    <td><code>[budgetura_mortgages]</code></td>
                    <td><?php _e( 'Mortgage management with payoff projections', 'budgetura' ); ?></td>
                </tr>
                <tr>
                    <td><code>[budgetura_bills]</code></td>
                    <td><?php _e( 'Bills and recurring expenses tracking', 'budgetura' ); ?></td>
                </tr>
                <tr>
                    <td><code>[budgetura_goals]</code></td>
                    <td><?php _e( 'Financial goals management', 'budgetura' ); ?></td>
                </tr>
                <tr>
                    <td><code>[budgetura_action_plan]</code></td>
                    <td><?php _e( 'Debt payoff action plan generator', 'budgetura' ); ?></td>
                </tr>
                <tr>
                    <td><code>[budgetura_snapshots]</code></td>
                    <td><?php _e( 'Progress tracking and snapshot comparison', 'budgetura' ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="budgetura-admin-section">
        <h2><?php _e( 'Need Help?', 'budgetura' ); ?></h2>
        <div class="budgetura-quick-actions">
            <a href="<?php echo admin_url( 'admin.php?page=budgetura' ); ?>" class="budgetura-action-card">
                <span class="dashicons dashicons-dashboard"></span>
                <h3><?php _e( 'Admin Dashboard', 'budgetura' ); ?></h3>
                <p><?php _e( 'View system statistics', 'budgetura' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'admin.php?page=budgetura-reports' ); ?>" class="budgetura-action-card">
                <span class="dashicons dashicons-chart-bar"></span>
                <h3><?php _e( 'Reports', 'budgetura' ); ?></h3>
                <p><?php _e( 'View analytics and reports', 'budgetura' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'admin.php?page=budgetura-settings' ); ?>" class="budgetura-action-card">
                <span class="dashicons dashicons-admin-settings"></span>
                <h3><?php _e( 'Settings', 'budgetura' ); ?></h3>
                <p><?php _e( 'Configure plugin options', 'budgetura' ); ?></p>
            </a>
        </div>
    </div>

    <div class="budgetura-admin-section">
        <h2><?php _e( 'Hide This Page', 'budgetura' ); ?></h2>
        <p><?php _e( 'Once you\'ve completed the setup, you can hide this welcome page.', 'budgetura' ); ?></p>
        <form method="post">
            <?php wp_nonce_field( 'budgetura_hide_setup' ); ?>
            <button type="submit" name="budgetura_hide_setup" class="button"><?php _e( 'Hide Setup Page', 'budgetura' ); ?></button>
        </form>
    </div>
</div>

<style>
.budgetura-setup-steps {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}

.budgetura-step {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: #fff;
    border: 1px solid #dcdcde;
    border-radius: 4px;
}

.budgetura-step-number {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background: #2271b1;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: bold;
}

.budgetura-step-content {
    flex: 1;
}

.budgetura-step-content h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.budgetura-step-content p {
    margin: 0 0 15px 0;
}
</style>

<?php
// Handle hide setup page
if ( isset( $_POST['budgetura_hide_setup'] ) && check_admin_referer( 'budgetura_hide_setup' ) ) {
    update_option( 'budgetura_hide_setup_page', true );
    wp_redirect( admin_url( 'admin.php?page=budgetura' ) );
    exit;
}
?>
