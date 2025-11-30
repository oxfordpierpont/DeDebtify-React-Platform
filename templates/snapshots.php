<?php
/**
 * Snapshots Comparison Template
 *
 * This template displays financial snapshots and allows comparison over time.
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Check if user is logged in
if ( ! is_user_logged_in() ) {
    echo '<p>' . __( 'Please log in to view your financial snapshots.', 'budgetura' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
?>

<!-- Navigation -->
<?php Budgetura_Helpers::render_navigation( 'snapshots' ); ?>

<div class="budgetura-dashboard budgetura-snapshots">

    <div class="budgetura-dashboard-header">
        <h1><?php _e( 'Financial Snapshots', 'budgetura' ); ?></h1>
        <p><?php _e( 'Track your progress over time and compare snapshots to see how far you\'ve come', 'budgetura' ); ?></p>
    </div>

    <!-- Create Snapshot Action -->
    <div class="budgetura-snapshot-actions">
        <button id="create-snapshot" class="budgetura-btn budgetura-btn-success">
            <?php _e( 'Create New Snapshot', 'budgetura' ); ?>
        </button>
        <p class="budgetura-help-text"><?php _e( 'Snapshots capture your current financial state for future comparison', 'budgetura' ); ?></p>
    </div>

    <!-- Progress Overview -->
    <div id="budgetura-progress-overview" class="budgetura-section" style="display: none;">
        <h2><?php _e( 'Your Progress', 'budgetura' ); ?></h2>

        <div class="budgetura-stats-grid">
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'Total Debt Reduced', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value success" id="progress-debt-reduced">$0</div>
                <div class="budgetura-stat-subtext" id="progress-debt-percent">0% reduction</div>
            </div>
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'DTI Improvement', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value" id="progress-dti-change">0%</div>
                <div class="budgetura-stat-subtext"><?php _e( 'Since first snapshot', 'budgetura' ); ?></div>
            </div>
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'Months Tracked', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value" id="progress-months">0</div>
                <div class="budgetura-stat-subtext" id="progress-date-range">—</div>
            </div>
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'Average Monthly Reduction', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value" id="progress-avg-monthly">$0</div>
                <div class="budgetura-stat-subtext"><?php _e( 'Debt paydown rate', 'budgetura' ); ?></div>
            </div>
        </div>

        <!-- Debt Progress Chart -->
        <div class="budgetura-chart-container">
            <h3><?php _e( 'Debt Over Time', 'budgetura' ); ?></h3>
            <div id="debt-progress-chart" class="budgetura-chart">
                <!-- Chart will be rendered here -->
            </div>
        </div>
    </div>

    <!-- Snapshot Comparison -->
    <div id="budgetura-snapshot-comparison" class="budgetura-section">
        <h2><?php _e( 'Compare Snapshots', 'budgetura' ); ?></h2>

        <div class="budgetura-comparison-selector">
            <div class="budgetura-form-group">
                <label for="snapshot-select-1" class="budgetura-form-label"><?php _e( 'First Snapshot', 'budgetura' ); ?></label>
                <select id="snapshot-select-1" class="budgetura-form-select">
                    <option value=""><?php _e( 'Select a snapshot...', 'budgetura' ); ?></option>
                </select>
            </div>

            <div class="budgetura-comparison-vs">
                <span>VS</span>
            </div>

            <div class="budgetura-form-group">
                <label for="snapshot-select-2" class="budgetura-form-label"><?php _e( 'Second Snapshot', 'budgetura' ); ?></label>
                <select id="snapshot-select-2" class="budgetura-form-select">
                    <option value=""><?php _e( 'Select a snapshot...', 'budgetura' ); ?></option>
                </select>
            </div>

            <button id="compare-snapshots" class="budgetura-btn budgetura-btn-primary" disabled>
                <?php _e( 'Compare', 'budgetura' ); ?>
            </button>
        </div>

        <!-- Comparison Results -->
        <div id="comparison-results" style="display: none;">
            <div class="budgetura-comparison-header">
                <h3><?php _e( 'Comparison Results', 'budgetura' ); ?></h3>
                <button id="clear-comparison" class="budgetura-btn budgetura-btn-secondary budgetura-btn-small">
                    <?php _e( 'Clear', 'budgetura' ); ?>
                </button>
            </div>

            <div class="budgetura-comparison-grid">
                <!-- Snapshot 1 -->
                <div class="budgetura-snapshot-card">
                    <div class="budgetura-snapshot-header">
                        <h4 id="snapshot1-title"><?php _e( 'Snapshot 1', 'budgetura' ); ?></h4>
                        <span class="budgetura-snapshot-date" id="snapshot1-date">—</span>
                    </div>
                    <div class="budgetura-snapshot-metrics">
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Total Debt', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot1-debt">$0</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Monthly Payments', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot1-payments">$0</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'DTI Ratio', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot1-dti">0%</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Credit Utilization', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot1-util">0%</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Credit Cards', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot1-cards">0</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Loans', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot1-loans">0</span>
                        </div>
                    </div>
                </div>

                <!-- Change Indicators -->
                <div class="budgetura-snapshot-changes">
                    <div class="budgetura-change-item" id="change-debt">
                        <span class="change-label"><?php _e( 'Debt Change', 'budgetura' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                    <div class="budgetura-change-item" id="change-payments">
                        <span class="change-label"><?php _e( 'Payment Change', 'budgetura' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                    <div class="budgetura-change-item" id="change-dti">
                        <span class="change-label"><?php _e( 'DTI Change', 'budgetura' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                    <div class="budgetura-change-item" id="change-util">
                        <span class="change-label"><?php _e( 'Utilization Change', 'budgetura' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                </div>

                <!-- Snapshot 2 -->
                <div class="budgetura-snapshot-card">
                    <div class="budgetura-snapshot-header">
                        <h4 id="snapshot2-title"><?php _e( 'Snapshot 2', 'budgetura' ); ?></h4>
                        <span class="budgetura-snapshot-date" id="snapshot2-date">—</span>
                    </div>
                    <div class="budgetura-snapshot-metrics">
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Total Debt', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot2-debt">$0</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Monthly Payments', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot2-payments">$0</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'DTI Ratio', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot2-dti">0%</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Credit Utilization', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot2-util">0%</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Credit Cards', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot2-cards">0</span>
                        </div>
                        <div class="budgetura-metric">
                            <span class="metric-label"><?php _e( 'Loans', 'budgetura' ); ?></span>
                            <span class="metric-value" id="snapshot2-loans">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="budgetura-comparison-summary">
                <h4><?php _e( 'Summary', 'budgetura' ); ?></h4>
                <div id="comparison-summary-text">
                    <?php _e( 'Select snapshots to see comparison', 'budgetura' ); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Snapshot History List -->
    <div id="budgetura-snapshot-list" class="budgetura-section">
        <h2><?php _e( 'Snapshot History', 'budgetura' ); ?></h2>

        <div id="snapshots-list-container">
            <div class="budgetura-loading">
                <div class="budgetura-spinner"></div>
                <p><?php _e( 'Loading snapshots...', 'budgetura' ); ?></p>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div class="budgetura-empty-state" id="snapshots-empty-state" style="display: none;">
        <h3><?php _e( 'No Snapshots Yet', 'budgetura' ); ?></h3>
        <p><?php _e( 'Create your first financial snapshot to start tracking your debt payoff progress over time.', 'budgetura' ); ?></p>
        <button id="create-first-snapshot" class="budgetura-btn budgetura-btn-success">
            <?php _e( 'Create Your First Snapshot', 'budgetura' ); ?>
        </button>
    </div>

</div>
