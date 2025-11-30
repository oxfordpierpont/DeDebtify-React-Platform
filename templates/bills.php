<?php
/**
 * Bills Manager Template
 *
 * This template displays and manages user's recurring bills.
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
    echo '<p>' . __( 'Please log in to manage your bills.', 'budgetura' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Budgetura_Helpers::render_navigation( 'bills' ); ?>

<div class="budgetura-dashboard budgetura-bills-manager">

    <div class="budgetura-dashboard-header">
        <h1><?php _e( 'Bills Manager', 'budgetura' ); ?></h1>
        <p><?php _e( 'Track all your recurring monthly expenses and subscriptions', 'budgetura' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="budgetura-form-container">
            <div class="budgetura-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Bill', 'budgetura' ) : __( 'Add New Bill', 'budgetura' ); ?></h2>
                <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Back to List', 'budgetura' ); ?></a>
            </div>

            <form id="budgetura-bill-form" class="budgetura-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'budgetura_bill_form', 'budgetura_nonce' ); ?>

                <div class="budgetura-form-row">
                    <div class="budgetura-form-group">
                        <label for="bill_name" class="budgetura-form-label"><?php _e( 'Bill Name', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="text" id="bill_name" name="bill_name" class="budgetura-form-input" required placeholder="e.g., Netflix Subscription">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="category" class="budgetura-form-label"><?php _e( 'Category', 'budgetura' ); ?> <span class="required">*</span></label>
                        <select id="category" name="category" class="budgetura-form-select" required>
                            <option value=""><?php _e( 'Select Category', 'budgetura' ); ?></option>
                            <option value="housing"><?php _e( 'Housing', 'budgetura' ); ?></option>
                            <option value="transportation"><?php _e( 'Transportation', 'budgetura' ); ?></option>
                            <option value="utilities"><?php _e( 'Utilities', 'budgetura' ); ?></option>
                            <option value="food"><?php _e( 'Food', 'budgetura' ); ?></option>
                            <option value="healthcare"><?php _e( 'Healthcare', 'budgetura' ); ?></option>
                            <option value="insurance"><?php _e( 'Insurance', 'budgetura' ); ?></option>
                            <option value="entertainment"><?php _e( 'Entertainment', 'budgetura' ); ?></option>
                            <option value="subscriptions"><?php _e( 'Subscriptions', 'budgetura' ); ?></option>
                            <option value="other"><?php _e( 'Other', 'budgetura' ); ?></option>
                        </select>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="amount" class="budgetura-form-label"><?php _e( 'Amount ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="amount" name="amount" class="budgetura-form-input" required placeholder="15.99">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="frequency" class="budgetura-form-label"><?php _e( 'Frequency', 'budgetura' ); ?> <span class="required">*</span></label>
                        <select id="frequency" name="frequency" class="budgetura-form-select" required>
                            <option value="monthly"><?php _e( 'Monthly', 'budgetura' ); ?></option>
                            <option value="weekly"><?php _e( 'Weekly', 'budgetura' ); ?></option>
                            <option value="bi-weekly"><?php _e( 'Bi-weekly', 'budgetura' ); ?></option>
                            <option value="quarterly"><?php _e( 'Quarterly', 'budgetura' ); ?></option>
                            <option value="annually"><?php _e( 'Annually', 'budgetura' ); ?></option>
                        </select>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="due_date" class="budgetura-form-label"><?php _e( 'Due Date (Day of Month)', 'budgetura' ); ?></label>
                        <input type="number" min="1" max="31" id="due_date" name="due_date" class="budgetura-form-input" placeholder="15">
                        <span class="budgetura-form-help"><?php _e( 'Optional: Day when bill is due', 'budgetura' ); ?></span>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="auto_pay" class="budgetura-form-label">
                            <input type="checkbox" id="auto_pay" name="auto_pay" value="1">
                            <?php _e( 'Auto-Pay Enabled', 'budgetura' ); ?>
                        </label>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="is_essential" class="budgetura-form-label">
                            <input type="checkbox" id="is_essential" name="is_essential" value="1">
                            <?php _e( 'Essential Bill', 'budgetura' ); ?>
                        </label>
                        <span class="budgetura-form-help"><?php _e( 'Mark if this is a necessary expense', 'budgetura' ); ?></span>
                    </div>
                </div>

                <!-- Monthly Equivalent Preview -->
                <div id="budgetura-bill-preview" class="budgetura-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Monthly Equivalent', 'budgetura' ); ?></h3>
                    <div class="budgetura-stats-grid">
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Monthly Cost', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-monthly">$0.00</div>
                            <div class="budgetura-stat-subtext" id="preview-calculation"></div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Annual Cost', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-annual">$0.00</div>
                        </div>
                    </div>
                </div>

                <div class="budgetura-form-actions">
                    <button type="submit" class="budgetura-btn budgetura-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Bill', 'budgetura' ) : __( 'Add Bill', 'budgetura' ); ?>
                    </button>
                    <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Cancel', 'budgetura' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="budgetura-manager-header">
            <div class="budgetura-manager-stats" id="budgetura-bill-stats">
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Monthly Bills:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="bill-total-monthly">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Essential:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="bill-essential">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Discretionary:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="bill-discretionary">$0.00</span>
                </div>
            </div>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Bill', 'budgetura' ); ?></a>
        </div>

        <div class="budgetura-manager-controls">
            <div class="budgetura-filter-group">
                <label for="filter-category"><?php _e( 'Category:', 'budgetura' ); ?></label>
                <select id="filter-category" class="budgetura-form-select">
                    <option value="all"><?php _e( 'All Categories', 'budgetura' ); ?></option>
                    <option value="housing"><?php _e( 'Housing', 'budgetura' ); ?></option>
                    <option value="transportation"><?php _e( 'Transportation', 'budgetura' ); ?></option>
                    <option value="utilities"><?php _e( 'Utilities', 'budgetura' ); ?></option>
                    <option value="food"><?php _e( 'Food', 'budgetura' ); ?></option>
                    <option value="healthcare"><?php _e( 'Healthcare', 'budgetura' ); ?></option>
                    <option value="insurance"><?php _e( 'Insurance', 'budgetura' ); ?></option>
                    <option value="entertainment"><?php _e( 'Entertainment', 'budgetura' ); ?></option>
                    <option value="subscriptions"><?php _e( 'Subscriptions', 'budgetura' ); ?></option>
                    <option value="other"><?php _e( 'Other', 'budgetura' ); ?></option>
                </select>
            </div>

            <div class="budgetura-filter-group">
                <label for="filter-essential"><?php _e( 'Type:', 'budgetura' ); ?></label>
                <select id="filter-essential" class="budgetura-form-select">
                    <option value="all"><?php _e( 'All', 'budgetura' ); ?></option>
                    <option value="essential"><?php _e( 'Essential', 'budgetura' ); ?></option>
                    <option value="discretionary"><?php _e( 'Discretionary', 'budgetura' ); ?></option>
                </select>
            </div>

            <div class="budgetura-filter-group">
                <label for="sort-bills-by"><?php _e( 'Sort by:', 'budgetura' ); ?></label>
                <select id="sort-bills-by" class="budgetura-form-select">
                    <option value="amount-high"><?php _e( 'Amount (High to Low)', 'budgetura' ); ?></option>
                    <option value="amount-low"><?php _e( 'Amount (Low to High)', 'budgetura' ); ?></option>
                    <option value="due-date"><?php _e( 'Due Date', 'budgetura' ); ?></option>
                    <option value="name"><?php _e( 'Name', 'budgetura' ); ?></option>
                </select>
            </div>
        </div>

        <div id="budgetura-bills-list" class="budgetura-items-list">
            <div class="budgetura-loading">
                <div class="budgetura-spinner"></div>
                <p><?php _e( 'Loading bills...', 'budgetura' ); ?></p>
            </div>
        </div>

        <div class="budgetura-empty-state" id="bills-empty-state" style="display: none;">
            <h3><?php _e( 'No Bills Found', 'budgetura' ); ?></h3>
            <p><?php _e( 'Add your first bill to start tracking your monthly expenses and see where your money goes.', 'budgetura' ); ?></p>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Your First Bill', 'budgetura' ); ?></a>
        </div>
    <?php endif; ?>

</div>
