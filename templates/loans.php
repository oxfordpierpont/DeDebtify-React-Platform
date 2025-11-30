<?php
/**
 * Loans Manager Template
 *
 * This template displays and manages user's loans.
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
    echo '<p>' . __( 'Please log in to manage your loans.', 'budgetura' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Budgetura_Helpers::render_navigation( 'loans' ); ?>

<div class="budgetura-dashboard budgetura-loans-manager">

    <div class="budgetura-dashboard-header">
        <h1><?php _e( 'Loan Manager', 'budgetura' ); ?></h1>
        <p><?php _e( 'Track personal loans, auto loans, student loans, and more', 'budgetura' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="budgetura-form-container">
            <div class="budgetura-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Loan', 'budgetura' ) : __( 'Add New Loan', 'budgetura' ); ?></h2>
                <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Back to List', 'budgetura' ); ?></a>
            </div>

            <form id="budgetura-loan-form" class="budgetura-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'budgetura_loan_form', 'budgetura_nonce' ); ?>

                <div class="budgetura-form-row">
                    <div class="budgetura-form-group">
                        <label for="loan_name" class="budgetura-form-label"><?php _e( 'Loan Name', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="text" id="loan_name" name="loan_name" class="budgetura-form-input" required placeholder="e.g., Auto Loan - Honda Civic">
                        <span class="budgetura-form-help"><?php _e( 'Enter a descriptive name for this loan', 'budgetura' ); ?></span>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="loan_type" class="budgetura-form-label"><?php _e( 'Loan Type', 'budgetura' ); ?> <span class="required">*</span></label>
                        <select id="loan_type" name="loan_type" class="budgetura-form-select" required>
                            <option value=""><?php _e( 'Select Type', 'budgetura' ); ?></option>
                            <option value="personal"><?php _e( 'Personal Loan', 'budgetura' ); ?></option>
                            <option value="auto"><?php _e( 'Auto Loan', 'budgetura' ); ?></option>
                            <option value="student"><?php _e( 'Student Loan', 'budgetura' ); ?></option>
                            <option value="other"><?php _e( 'Other', 'budgetura' ); ?></option>
                        </select>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="start_date" class="budgetura-form-label"><?php _e( 'Start Date', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="date" id="start_date" name="start_date" class="budgetura-form-input" required>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="principal" class="budgetura-form-label"><?php _e( 'Original Loan Amount ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="principal" name="principal" class="budgetura-form-input" required placeholder="25000.00">
                    </div>

                    <div class="budgetura-form-group">
                        <label for="current_balance" class="budgetura-form-label"><?php _e( 'Current Balance ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="current_balance" name="current_balance" class="budgetura-form-input" required placeholder="18500.00">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="interest_rate" class="budgetura-form-label"><?php _e( 'Interest Rate (APR %)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="interest_rate" name="interest_rate" class="budgetura-form-input" required placeholder="5.75">
                    </div>

                    <div class="budgetura-form-group">
                        <label for="term_months" class="budgetura-form-label"><?php _e( 'Original Term (Months)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" id="term_months" name="term_months" class="budgetura-form-input" required placeholder="60">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="monthly_payment" class="budgetura-form-label"><?php _e( 'Monthly Payment ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="monthly_payment" name="monthly_payment" class="budgetura-form-input" required placeholder="480.00">
                        <button type="button" id="calculate-loan-payment" class="budgetura-btn budgetura-btn-small budgetura-btn-secondary" style="margin-top: 10px;">
                            <?php _e( 'Auto-Calculate', 'budgetura' ); ?>
                        </button>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="extra_payment" class="budgetura-form-label"><?php _e( 'Extra Payment ($)', 'budgetura' ); ?></label>
                        <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="budgetura-form-input" placeholder="100.00">
                        <span class="budgetura-form-help"><?php _e( 'Additional amount you plan to pay each month', 'budgetura' ); ?></span>
                    </div>
                </div>

                <!-- Payoff Projection -->
                <div id="budgetura-loan-payoff-preview" class="budgetura-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Payoff Projection', 'budgetura' ); ?></h3>
                    <div class="budgetura-stats-grid">
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Months Remaining', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-months">0</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Payoff Date', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-date" style="font-size: 1.2rem;">—</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Total Interest', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-interest">$0</div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Total Paid', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-total">$0</div>
                        </div>
                    </div>
                </div>

                <div class="budgetura-form-actions">
                    <button type="submit" class="budgetura-btn budgetura-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Loan', 'budgetura' ) : __( 'Add Loan', 'budgetura' ); ?>
                    </button>
                    <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Cancel', 'budgetura' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="budgetura-manager-header">
            <div class="budgetura-manager-stats" id="budgetura-loan-stats">
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Loan Debt:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="loan-total-debt">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Monthly Payments:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="loan-monthly-payment">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Active Loans:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="loan-count">0</span>
                </div>
            </div>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Loan', 'budgetura' ); ?></a>
        </div>

        <div class="budgetura-manager-controls">
            <div class="budgetura-filter-group">
                <label for="filter-loan-type"><?php _e( 'Type:', 'budgetura' ); ?></label>
                <select id="filter-loan-type" class="budgetura-form-select">
                    <option value="all"><?php _e( 'All Types', 'budgetura' ); ?></option>
                    <option value="personal"><?php _e( 'Personal', 'budgetura' ); ?></option>
                    <option value="auto"><?php _e( 'Auto', 'budgetura' ); ?></option>
                    <option value="student"><?php _e( 'Student', 'budgetura' ); ?></option>
                    <option value="other"><?php _e( 'Other', 'budgetura' ); ?></option>
                </select>
            </div>

            <div class="budgetura-filter-group">
                <label for="sort-loans-by"><?php _e( 'Sort by:', 'budgetura' ); ?></label>
                <select id="sort-loans-by" class="budgetura-form-select">
                    <option value="balance-high"><?php _e( 'Balance (High to Low)', 'budgetura' ); ?></option>
                    <option value="balance-low"><?php _e( 'Balance (Low to High)', 'budgetura' ); ?></option>
                    <option value="rate-high"><?php _e( 'Interest Rate (High to Low)', 'budgetura' ); ?></option>
                    <option value="rate-low"><?php _e( 'Interest Rate (Low to High)', 'budgetura' ); ?></option>
                </select>
            </div>
        </div>

        <div id="budgetura-loans-list" class="budgetura-items-list">
            <div class="budgetura-loading">
                <div class="budgetura-spinner"></div>
                <p><?php _e( 'Loading loans...', 'budgetura' ); ?></p>
            </div>
        </div>

        <div class="budgetura-empty-state" id="loans-empty-state" style="display: none;">
            <h3><?php _e( 'No Loans Found', 'budgetura' ); ?></h3>
            <p><?php _e( 'Add your first loan to start tracking your debt and see payoff projections.', 'budgetura' ); ?></p>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Your First Loan', 'budgetura' ); ?></a>
        </div>
    <?php endif; ?>

</div>
