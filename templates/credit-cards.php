<?php
/**
 * Credit Cards Manager Template
 *
 * This template displays and manages user's credit cards.
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
    echo '<p>' . __( 'Please log in to manage your credit cards.', 'budgetura' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Budgetura_Helpers::render_navigation( 'credit_cards' ); ?>

<div class="budgetura-dashboard budgetura-credit-cards-manager">

    <div class="budgetura-dashboard-header">
        <h1><?php _e( 'Credit Card Manager', 'budgetura' ); ?></h1>
        <p><?php _e( 'Track and manage your credit cards with payoff projections', 'budgetura' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="budgetura-form-container">
            <div class="budgetura-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Credit Card', 'budgetura' ) : __( 'Add New Credit Card', 'budgetura' ); ?></h2>
                <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Back to List', 'budgetura' ); ?></a>
            </div>

            <form id="budgetura-credit-card-form" class="budgetura-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'budgetura_credit_card_form', 'budgetura_nonce' ); ?>

                <div class="budgetura-form-row">
                    <div class="budgetura-form-group">
                        <label for="card_name" class="budgetura-form-label"><?php _e( 'Card Name', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="text" id="card_name" name="card_name" class="budgetura-form-input" required placeholder="e.g., Chase Freedom">
                        <span class="budgetura-form-help"><?php _e( 'Enter a name to identify this card', 'budgetura' ); ?></span>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="balance" class="budgetura-form-label"><?php _e( 'Current Balance ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="balance" name="balance" class="budgetura-form-input" required placeholder="3500.00">
                    </div>

                    <div class="budgetura-form-group">
                        <label for="credit_limit" class="budgetura-form-label"><?php _e( 'Credit Limit ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="credit_limit" name="credit_limit" class="budgetura-form-input" required placeholder="5000.00">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="interest_rate" class="budgetura-form-label"><?php _e( 'Interest Rate (APR %)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="interest_rate" name="interest_rate" class="budgetura-form-input" required placeholder="18.99">
                    </div>

                    <div class="budgetura-form-group">
                        <label for="minimum_payment" class="budgetura-form-label"><?php _e( 'Minimum Payment ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="minimum_payment" name="minimum_payment" class="budgetura-form-input" required placeholder="75.00">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="extra_payment" class="budgetura-form-label"><?php _e( 'Extra Payment ($)', 'budgetura' ); ?></label>
                        <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="budgetura-form-input" placeholder="200.00">
                        <span class="budgetura-form-help"><?php _e( 'Additional amount you plan to pay each month', 'budgetura' ); ?></span>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="due_date" class="budgetura-form-label"><?php _e( 'Due Date (Day of Month)', 'budgetura' ); ?></label>
                        <input type="number" min="1" max="31" id="due_date" name="due_date" class="budgetura-form-input" placeholder="15">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="status" class="budgetura-form-label"><?php _e( 'Status', 'budgetura' ); ?></label>
                        <select id="status" name="status" class="budgetura-form-select">
                            <option value="active"><?php _e( 'Active', 'budgetura' ); ?></option>
                            <option value="paid_off"><?php _e( 'Paid Off', 'budgetura' ); ?></option>
                            <option value="closed"><?php _e( 'Closed', 'budgetura' ); ?></option>
                        </select>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="auto_pay" class="budgetura-form-label">
                            <input type="checkbox" id="auto_pay" name="auto_pay" value="1">
                            <?php _e( 'Auto-Pay Enabled', 'budgetura' ); ?>
                        </label>
                    </div>
                </div>

                <!-- Payoff Projection (shown after entering data) -->
                <div id="budgetura-payoff-preview" class="budgetura-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Payoff Projection', 'budgetura' ); ?></h3>
                    <div class="budgetura-stats-grid">
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Utilization', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-utilization">0%</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Months to Payoff', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-months">0</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Total Interest', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-interest">$0</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Payoff Date', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-date" style="font-size: 1.2rem;">—</div>
                        </div>
                    </div>
                </div>

                <div class="budgetura-form-actions">
                    <button type="submit" class="budgetura-btn budgetura-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Card', 'budgetura' ) : __( 'Add Card', 'budgetura' ); ?>
                    </button>
                    <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Cancel', 'budgetura' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="budgetura-manager-header">
            <div class="budgetura-manager-stats" id="budgetura-cc-stats">
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Credit Card Debt:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="cc-total-debt">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Overall Utilization:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="cc-utilization">0%</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Monthly Payments:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="cc-monthly-payment">$0.00</span>
                </div>
            </div>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Credit Card', 'budgetura' ); ?></a>
        </div>

        <div class="budgetura-manager-controls">
            <div class="budgetura-filter-group">
                <label for="sort-by"><?php _e( 'Sort by:', 'budgetura' ); ?></label>
                <select id="sort-by" class="budgetura-form-select">
                    <option value="balance-high"><?php _e( 'Balance (High to Low)', 'budgetura' ); ?></option>
                    <option value="balance-low"><?php _e( 'Balance (Low to High)', 'budgetura' ); ?></option>
                    <option value="rate-high"><?php _e( 'Interest Rate (High to Low)', 'budgetura' ); ?></option>
                    <option value="rate-low"><?php _e( 'Interest Rate (Low to High)', 'budgetura' ); ?></option>
                    <option value="utilization-high"><?php _e( 'Utilization (High to Low)', 'budgetura' ); ?></option>
                </select>
            </div>

            <div class="budgetura-filter-group">
                <label for="filter-status"><?php _e( 'Status:', 'budgetura' ); ?></label>
                <select id="filter-status" class="budgetura-form-select">
                    <option value="all"><?php _e( 'All', 'budgetura' ); ?></option>
                    <option value="active"><?php _e( 'Active', 'budgetura' ); ?></option>
                    <option value="paid_off"><?php _e( 'Paid Off', 'budgetura' ); ?></option>
                    <option value="closed"><?php _e( 'Closed', 'budgetura' ); ?></option>
                </select>
            </div>
        </div>

        <div id="budgetura-credit-cards-list" class="budgetura-items-list">
            <div class="budgetura-loading">
                <div class="budgetura-spinner"></div>
                <p><?php _e( 'Loading credit cards...', 'budgetura' ); ?></p>
            </div>
        </div>

        <div class="budgetura-empty-state" id="empty-state" style="display: none;">
            <h3><?php _e( 'No Credit Cards Found', 'budgetura' ); ?></h3>
            <p><?php _e( 'Add your first credit card to start tracking your debt and see payoff projections.', 'budgetura' ); ?></p>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Your First Credit Card', 'budgetura' ); ?></a>
        </div>
    <?php endif; ?>

</div>
