<?php
/**
 * Reports Page
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div class="wrap">
    <h1><?php _e( 'Budgetura Reports', 'budgetura' ); ?></h1>
    <p class="description"><?php _e( 'Analytics and reports for your debt management system', 'budgetura' ); ?></p>

    <!-- Summary Statistics -->
    <div class="budgetura-admin-section">
        <h2><?php _e( 'Summary Statistics', 'budgetura' ); ?></h2>

        <div class="budgetura-stats-grid">
            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count_users()['total_users']; ?></div>
                    <div class="stat-label"><?php _e( 'Total Users', 'budgetura' ); ?></div>
                </div>
            </div>

            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value">$<?php
                        // Calculate total debt across all users
                        global $wpdb;
                        $total_debt = 0;

                        // Get all credit card balances
                        $cards = get_posts(array(
                            'post_type' => 'dd_credit_card',
                            'posts_per_page' => -1,
                            'post_status' => 'publish'
                        ));
                        foreach ($cards as $card) {
                            $balance = get_post_meta($card->ID, 'balance', true);
                            $total_debt += floatval($balance);
                        }

                        // Get all loan balances
                        $loans = get_posts(array(
                            'post_type' => 'dd_loan',
                            'posts_per_page' => -1,
                            'post_status' => 'publish'
                        ));
                        foreach ($loans as $loan) {
                            $balance = get_post_meta($loan->ID, 'balance', true);
                            $total_debt += floatval($balance);
                        }

                        echo number_format($total_debt, 2);
                    ?></div>
                    <div class="stat-label"><?php _e( 'Total Debt Tracked', 'budgetura' ); ?></div>
                </div>
            </div>

            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-camera"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo wp_count_posts('dd_snapshot')->publish; ?></div>
                    <div class="stat-label"><?php _e( 'Total Snapshots', 'budgetura' ); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Credit Cards Report -->
    <div class="budgetura-admin-section">
        <h2><?php _e( 'Credit Cards Report', 'budgetura' ); ?></h2>

        <?php
        $cards = get_posts(array(
            'post_type' => 'dd_credit_card',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));

        if ($cards) :
        ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Card Name', 'budgetura' ); ?></th>
                        <th><?php _e( 'User', 'budgetura' ); ?></th>
                        <th><?php _e( 'Balance', 'budgetura' ); ?></th>
                        <th><?php _e( 'Credit Limit', 'budgetura' ); ?></th>
                        <th><?php _e( 'Interest Rate', 'budgetura' ); ?></th>
                        <th><?php _e( 'Utilization', 'budgetura' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cards as $card) :
                        $balance = floatval(get_post_meta($card->ID, 'balance', true));
                        $credit_limit = floatval(get_post_meta($card->ID, 'credit_limit', true));
                        $interest_rate = floatval(get_post_meta($card->ID, 'interest_rate', true));
                        $utilization = $credit_limit > 0 ? ($balance / $credit_limit) * 100 : 0;
                        $author = get_userdata($card->post_author);
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($card->post_title); ?></strong></td>
                            <td><?php echo esc_html($author->display_name); ?></td>
                            <td>$<?php echo number_format($balance, 2); ?></td>
                            <td>$<?php echo number_format($credit_limit, 2); ?></td>
                            <td><?php echo number_format($interest_rate, 2); ?>%</td>
                            <td><?php echo number_format($utilization, 1); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e( 'No credit cards found.', 'budgetura' ); ?></p>
        <?php endif; ?>
    </div>

    <!-- Loans Report -->
    <div class="budgetura-admin-section">
        <h2><?php _e( 'Loans Report', 'budgetura' ); ?></h2>

        <?php
        $loans = get_posts(array(
            'post_type' => 'dd_loan',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));

        if ($loans) :
        ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Loan Name', 'budgetura' ); ?></th>
                        <th><?php _e( 'User', 'budgetura' ); ?></th>
                        <th><?php _e( 'Type', 'budgetura' ); ?></th>
                        <th><?php _e( 'Balance', 'budgetura' ); ?></th>
                        <th><?php _e( 'Interest Rate', 'budgetura' ); ?></th>
                        <th><?php _e( 'Monthly Payment', 'budgetura' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan) :
                        $balance = floatval(get_post_meta($loan->ID, 'balance', true));
                        $interest_rate = floatval(get_post_meta($loan->ID, 'interest_rate', true));
                        $monthly_payment = floatval(get_post_meta($loan->ID, 'monthly_payment', true));
                        $loan_type = get_post_meta($loan->ID, 'loan_type', true);
                        $author = get_userdata($loan->post_author);
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($loan->post_title); ?></strong></td>
                            <td><?php echo esc_html($author->display_name); ?></td>
                            <td><?php echo esc_html(ucfirst(str_replace('_', ' ', $loan_type))); ?></td>
                            <td>$<?php echo number_format($balance, 2); ?></td>
                            <td><?php echo number_format($interest_rate, 2); ?>%</td>
                            <td>$<?php echo number_format($monthly_payment, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e( 'No loans found.', 'budgetura' ); ?></p>
        <?php endif; ?>
    </div>

    <!-- Export Options -->
    <div class="budgetura-admin-section">
        <h2><?php _e( 'Export Data', 'budgetura' ); ?></h2>
        <p><?php _e( 'Export your data in various formats:', 'budgetura' ); ?></p>

        <div class="budgetura-quick-actions">
            <button class="budgetura-action-card" id="export-csv">
                <span class="dashicons dashicons-media-spreadsheet"></span>
                <h3><?php _e( 'Export to CSV', 'budgetura' ); ?></h3>
                <p><?php _e( 'Download all data as CSV', 'budgetura' ); ?></p>
            </button>

            <button class="budgetura-action-card" id="export-pdf">
                <span class="dashicons dashicons-media-document"></span>
                <h3><?php _e( 'Export to PDF', 'budgetura' ); ?></h3>
                <p><?php _e( 'Generate PDF report', 'budgetura' ); ?></p>
            </button>

            <button class="budgetura-action-card" id="export-json">
                <span class="dashicons dashicons-media-code"></span>
                <h3><?php _e( 'Export to JSON', 'budgetura' ); ?></h3>
                <p><?php _e( 'Download as JSON format', 'budgetura' ); ?></p>
            </button>
        </div>
    </div>

</div>

<script>
jQuery(document).ready(function($) {
    // Export handlers will be implemented in admin.js
    $('#export-csv, #export-pdf, #export-json').on('click', function() {
        alert('Export functionality will be implemented in a future update.');
    });
});
</script>
