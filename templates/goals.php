<?php
/**
 * Goals Manager Template
 *
 * This template displays and manages user's financial goals.
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
    echo '<p>' . __( 'Please log in to manage your goals.', 'budgetura' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Budgetura_Helpers::render_navigation( 'goals' ); ?>

<div class="budgetura-dashboard budgetura-goals-manager">

    <div class="budgetura-dashboard-header">
        <h1><?php _e( 'Goals Manager', 'budgetura' ); ?></h1>
        <p><?php _e( 'Set and track your financial goals and savings milestones', 'budgetura' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="budgetura-form-container">
            <div class="budgetura-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Goal', 'budgetura' ) : __( 'Add New Goal', 'budgetura' ); ?></h2>
                <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Back to List', 'budgetura' ); ?></a>
            </div>

            <form id="budgetura-goal-form" class="budgetura-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'budgetura_goal_form', 'budgetura_nonce' ); ?>

                <div class="budgetura-form-row">
                    <div class="budgetura-form-group">
                        <label for="goal_name" class="budgetura-form-label"><?php _e( 'Goal Name', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="text" id="goal_name" name="goal_name" class="budgetura-form-input" required placeholder="e.g., Emergency Fund">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="goal_type" class="budgetura-form-label"><?php _e( 'Goal Type', 'budgetura' ); ?> <span class="required">*</span></label>
                        <select id="goal_type" name="goal_type" class="budgetura-form-select" required>
                            <option value=""><?php _e( 'Select Type', 'budgetura' ); ?></option>
                            <option value="savings"><?php _e( 'Savings', 'budgetura' ); ?></option>
                            <option value="emergency_fund"><?php _e( 'Emergency Fund', 'budgetura' ); ?></option>
                            <option value="debt_payoff"><?php _e( 'Debt Payoff', 'budgetura' ); ?></option>
                            <option value="investment"><?php _e( 'Investment', 'budgetura' ); ?></option>
                            <option value="purchase"><?php _e( 'Major Purchase', 'budgetura' ); ?></option>
                            <option value="other"><?php _e( 'Other', 'budgetura' ); ?></option>
                        </select>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="priority" class="budgetura-form-label"><?php _e( 'Priority', 'budgetura' ); ?></label>
                        <select id="priority" name="priority" class="budgetura-form-select">
                            <option value="medium"><?php _e( 'Medium', 'budgetura' ); ?></option>
                            <option value="low"><?php _e( 'Low', 'budgetura' ); ?></option>
                            <option value="high"><?php _e( 'High', 'budgetura' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="target_amount" class="budgetura-form-label"><?php _e( 'Target Amount ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="target_amount" name="target_amount" class="budgetura-form-input" required placeholder="10000.00">
                    </div>

                    <div class="budgetura-form-group">
                        <label for="current_amount" class="budgetura-form-label"><?php _e( 'Current Amount ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="current_amount" name="current_amount" class="budgetura-form-input" required placeholder="3500.00">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="monthly_contribution" class="budgetura-form-label"><?php _e( 'Monthly Contribution ($)', 'budgetura' ); ?></label>
                        <input type="number" step="0.01" id="monthly_contribution" name="monthly_contribution" class="budgetura-form-input" placeholder="250.00">
                        <span class="budgetura-form-help"><?php _e( 'Amount you plan to save each month', 'budgetura' ); ?></span>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="target_date" class="budgetura-form-label"><?php _e( 'Target Date', 'budgetura' ); ?></label>
                        <input type="date" id="target_date" name="target_date" class="budgetura-form-input">
                        <span class="budgetura-form-help"><?php _e( 'Optional: When you want to reach this goal', 'budgetura' ); ?></span>
                    </div>
                </div>

                <!-- Goal Progress Preview -->
                <div id="budgetura-goal-preview" class="budgetura-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Goal Progress', 'budgetura' ); ?></h3>

                    <div class="budgetura-progress-container">
                        <div class="budgetura-progress" style="height: 30px; margin-bottom: 10px;">
                            <div class="budgetura-progress-bar success" id="goal-progress-bar" style="width: 0%"></div>
                        </div>
                        <div style="text-align: center; font-size: 1.25rem; font-weight: bold; margin-bottom: 20px;">
                            <span id="goal-progress-percent">0%</span> Complete
                        </div>
                    </div>

                    <div class="budgetura-stats-grid">
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Remaining', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-remaining">$0</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Months to Goal', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-months">0</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Estimated Date', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-date" style="font-size: 1.2rem;">—</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'On Track?', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-status" style="font-size: 1.2rem;">—</div>
                        </div>
                    </div>
                </div>

                <div class="budgetura-form-actions">
                    <button type="submit" class="budgetura-btn budgetura-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Goal', 'budgetura' ) : __( 'Add Goal', 'budgetura' ); ?>
                    </button>
                    <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Cancel', 'budgetura' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="budgetura-manager-header">
            <div class="budgetura-manager-stats" id="budgetura-goal-stats">
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Goal Target:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="goal-total-target">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Saved:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="goal-total-saved">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Overall Progress:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="goal-overall-progress">0%</span>
                </div>
            </div>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Goal', 'budgetura' ); ?></a>
        </div>

        <div class="budgetura-manager-controls">
            <div class="budgetura-filter-group">
                <label for="filter-goal-type"><?php _e( 'Type:', 'budgetura' ); ?></label>
                <select id="filter-goal-type" class="budgetura-form-select">
                    <option value="all"><?php _e( 'All Types', 'budgetura' ); ?></option>
                    <option value="savings"><?php _e( 'Savings', 'budgetura' ); ?></option>
                    <option value="emergency_fund"><?php _e( 'Emergency Fund', 'budgetura' ); ?></option>
                    <option value="debt_payoff"><?php _e( 'Debt Payoff', 'budgetura' ); ?></option>
                    <option value="investment"><?php _e( 'Investment', 'budgetura' ); ?></option>
                    <option value="purchase"><?php _e( 'Major Purchase', 'budgetura' ); ?></option>
                    <option value="other"><?php _e( 'Other', 'budgetura' ); ?></option>
                </select>
            </div>

            <div class="budgetura-filter-group">
                <label for="filter-priority"><?php _e( 'Priority:', 'budgetura' ); ?></label>
                <select id="filter-priority" class="budgetura-form-select">
                    <option value="all"><?php _e( 'All', 'budgetura' ); ?></option>
                    <option value="high"><?php _e( 'High', 'budgetura' ); ?></option>
                    <option value="medium"><?php _e( 'Medium', 'budgetura' ); ?></option>
                    <option value="low"><?php _e( 'Low', 'budgetura' ); ?></option>
                </select>
            </div>

            <div class="budgetura-filter-group">
                <label for="sort-goals-by"><?php _e( 'Sort by:', 'budgetura' ); ?></label>
                <select id="sort-goals-by" class="budgetura-form-select">
                    <option value="progress"><?php _e( 'Progress', 'budgetura' ); ?></option>
                    <option value="target-high"><?php _e( 'Target (High to Low)', 'budgetura' ); ?></option>
                    <option value="target-low"><?php _e( 'Target (Low to High)', 'budgetura' ); ?></option>
                    <option value="priority"><?php _e( 'Priority', 'budgetura' ); ?></option>
                </select>
            </div>
        </div>

        <div id="budgetura-goals-list" class="budgetura-items-list">
            <div class="budgetura-loading">
                <div class="budgetura-spinner"></div>
                <p><?php _e( 'Loading goals...', 'budgetura' ); ?></p>
            </div>
        </div>

        <div class="budgetura-empty-state" id="goals-empty-state" style="display: none;">
            <h3><?php _e( 'No Goals Found', 'budgetura' ); ?></h3>
            <p><?php _e( 'Set your first financial goal to start tracking your progress towards financial freedom.', 'budgetura' ); ?></p>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Your First Goal', 'budgetura' ); ?></a>
        </div>
    <?php endif; ?>

</div>
