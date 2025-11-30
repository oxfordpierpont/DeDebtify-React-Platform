<?php
/**
 * Admin Dashboard Page
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

<div class="wrap budgetura-admin-dashboard">
    <h1><?php _e( 'Budgetura Dashboard', 'budgetura' ); ?></h1>

    <div class="budgetura-admin-header">
        <p class="description"><?php _e( 'Overview of your debt management system', 'budgetura' ); ?></p>
    </div>

    <!-- Dashboard Widgets Grid -->
    <div class="budgetura-dashboard-widgets">

        <!-- Total Users Widget -->
        <div class="budgetura-widget">
            <div class="budgetura-widget-header">
                <h2><?php _e( 'Total Users', 'budgetura' ); ?></h2>
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="budgetura-widget-content">
                <div class="budgetura-widget-stat">
                    <span class="stat-value" id="total-users">0</span>
                    <span class="stat-label"><?php _e( 'Active Users', 'budgetura' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Total Debt Tracked Widget -->
        <div class="budgetura-widget">
            <div class="budgetura-widget-header">
                <h2><?php _e( 'Total Debt Tracked', 'budgetura' ); ?></h2>
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="budgetura-widget-content">
                <div class="budgetura-widget-stat">
                    <span class="stat-value" id="total-debt">$0</span>
                    <span class="stat-label"><?php _e( 'Across All Users', 'budgetura' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Credit Cards Widget -->
        <div class="budgetura-widget">
            <div class="budgetura-widget-header">
                <h2><?php _e( 'Credit Cards', 'budgetura' ); ?></h2>
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="budgetura-widget-content">
                <div class="budgetura-widget-stat">
                    <span class="stat-value" id="total-cards">0</span>
                    <span class="stat-label"><?php _e( 'Total Cards', 'budgetura' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Loans Widget -->
        <div class="budgetura-widget">
            <div class="budgetura-widget-header">
                <h2><?php _e( 'Loans', 'budgetura' ); ?></h2>
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="budgetura-widget-content">
                <div class="budgetura-widget-stat">
                    <span class="stat-value" id="total-loans">0</span>
                    <span class="stat-label"><?php _e( 'Total Loans', 'budgetura' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Goals Widget -->
        <div class="budgetura-widget">
            <div class="budgetura-widget-header">
                <h2><?php _e( 'Financial Goals', 'budgetura' ); ?></h2>
                <span class="dashicons dashicons-flag"></span>
            </div>
            <div class="budgetura-widget-content">
                <div class="budgetura-widget-stat">
                    <span class="stat-value" id="total-goals">0</span>
                    <span class="stat-label"><?php _e( 'Active Goals', 'budgetura' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Snapshots Widget -->
        <div class="budgetura-widget">
            <div class="budgetura-widget-header">
                <h2><?php _e( 'Snapshots', 'budgetura' ); ?></h2>
                <span class="dashicons dashicons-camera"></span>
            </div>
            <div class="budgetura-widget-content">
                <div class="budgetura-widget-stat">
                    <span class="stat-value" id="total-snapshots">0</span>
                    <span class="stat-label"><?php _e( 'Total Snapshots', 'budgetura' ); ?></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Activity Section -->
    <div class="budgetura-admin-section">
        <div class="budgetura-section-header">
            <h2><?php _e( 'Recent Activity', 'budgetura' ); ?></h2>
            <button id="refresh-activity" class="button button-secondary">
                <span class="dashicons dashicons-update"></span> <?php _e( 'Refresh', 'budgetura' ); ?>
            </button>
        </div>

        <div id="recent-activity-container">
            <div class="budgetura-loading">
                <span class="spinner is-active"></span>
                <p><?php _e( 'Loading recent activity...', 'budgetura' ); ?></p>
            </div>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="budgetura-admin-section">
        <h2><?php _e( 'System Statistics', 'budgetura' ); ?></h2>

        <div class="budgetura-stats-grid">
            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-admin-page"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-credit-cards">0</div>
                    <div class="stat-label"><?php _e( 'Total Credit Cards', 'budgetura' ); ?></div>
                </div>
            </div>

            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-loans">0</div>
                    <div class="stat-label"><?php _e( 'Total Loans', 'budgetura' ); ?></div>
                </div>
            </div>

            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-clipboard"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-bills">0</div>
                    <div class="stat-label"><?php _e( 'Total Bills', 'budgetura' ); ?></div>
                </div>
            </div>

            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-flag"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-goals">0</div>
                    <div class="stat-label"><?php _e( 'Total Goals', 'budgetura' ); ?></div>
                </div>
            </div>

            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-camera"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-snapshots">0</div>
                    <div class="stat-label"><?php _e( 'Total Snapshots', 'budgetura' ); ?></div>
                </div>
            </div>

            <div class="budgetura-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-database"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-total-items">0</div>
                    <div class="stat-label"><?php _e( 'Total Items', 'budgetura' ); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="budgetura-admin-section">
        <h2><?php _e( 'Quick Actions', 'budgetura' ); ?></h2>

        <div class="budgetura-quick-actions">
            <a href="<?php echo admin_url( 'admin.php?page=budgetura-settings' ); ?>" class="budgetura-action-card">
                <span class="dashicons dashicons-admin-settings"></span>
                <h3><?php _e( 'Settings', 'budgetura' ); ?></h3>
                <p><?php _e( 'Configure plugin settings', 'budgetura' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'edit.php?post_type=dd_credit_card' ); ?>" class="budgetura-action-card">
                <span class="dashicons dashicons-admin-page"></span>
                <h3><?php _e( 'View Credit Cards', 'budgetura' ); ?></h3>
                <p><?php _e( 'Manage all credit cards', 'budgetura' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'edit.php?post_type=dd_loan' ); ?>" class="budgetura-action-card">
                <span class="dashicons dashicons-money-alt"></span>
                <h3><?php _e( 'View Loans', 'budgetura' ); ?></h3>
                <p><?php _e( 'Manage all loans', 'budgetura' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'admin.php?page=budgetura-reports' ); ?>" class="budgetura-action-card">
                <span class="dashicons dashicons-chart-bar"></span>
                <h3><?php _e( 'Reports', 'budgetura' ); ?></h3>
                <p><?php _e( 'View analytics and reports', 'budgetura' ); ?></p>
            </a>
        </div>
    </div>

</div>
