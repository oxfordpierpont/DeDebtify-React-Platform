<?php
/**
 * Mortgages Manager Template
 *
 * This template displays and manages user's mortgage.
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
    echo '<p>' . __( 'Please log in to manage your mortgage.', 'budgetura' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Budgetura_Helpers::render_navigation( 'mortgages' ); ?>

<div class="budgetura-dashboard budgetura-mortgages-manager">

    <div class="budgetura-dashboard-header">
        <h1><?php _e( 'Mortgage Manager', 'budgetura' ); ?></h1>
        <p><?php _e( 'Track your home mortgage with detailed payment projections', 'budgetura' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="budgetura-form-container">
            <div class="budgetura-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Mortgage', 'budgetura' ) : __( 'Add Mortgage', 'budgetura' ); ?></h2>
                <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Back to List', 'budgetura' ); ?></a>
            </div>

            <form id="budgetura-mortgage-form" class="budgetura-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'budgetura_mortgage_form', 'budgetura_nonce' ); ?>

                <div class="budgetura-form-row">
                    <div class="budgetura-form-group">
                        <label for="mortgage_name" class="budgetura-form-label"><?php _e( 'Property Name', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="text" id="mortgage_name" name="mortgage_name" class="budgetura-form-input" required placeholder="e.g., Primary Residence">
                        <span class="budgetura-form-help"><?php _e( 'Enter a descriptive name for this mortgage', 'budgetura' ); ?></span>
                    </div>
                </div>

                <div class="budgetura-form-row">
                    <div class="budgetura-form-group">
                        <label for="property_address" class="budgetura-form-label"><?php _e( 'Property Address', 'budgetura' ); ?></label>
                        <input type="text" id="property_address" name="property_address" class="budgetura-form-input" placeholder="123 Main Street, City, State 12345">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="loan_amount" class="budgetura-form-label"><?php _e( 'Original Loan Amount ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="loan_amount" name="loan_amount" class="budgetura-form-input" required placeholder="300000.00">
                    </div>

                    <div class="budgetura-form-group">
                        <label for="current_balance" class="budgetura-form-label"><?php _e( 'Current Balance ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="current_balance" name="current_balance" class="budgetura-form-input" required placeholder="285000.00">
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="interest_rate" class="budgetura-form-label"><?php _e( 'Interest Rate (APR %)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="interest_rate" name="interest_rate" class="budgetura-form-input" required placeholder="3.75">
                    </div>

                    <div class="budgetura-form-group">
                        <label for="term_years" class="budgetura-form-label"><?php _e( 'Term (Years)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <select id="term_years" name="term_years" class="budgetura-form-select" required>
                            <option value=""><?php _e( 'Select Term', 'budgetura' ); ?></option>
                            <option value="15">15 <?php _e( 'Years', 'budgetura' ); ?></option>
                            <option value="20">20 <?php _e( 'Years', 'budgetura' ); ?></option>
                            <option value="30">30 <?php _e( 'Years', 'budgetura' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="start_date" class="budgetura-form-label"><?php _e( 'Start Date', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="date" id="start_date" name="start_date" class="budgetura-form-input" required>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="monthly_payment" class="budgetura-form-label"><?php _e( 'Monthly P&I Payment ($)', 'budgetura' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="monthly_payment" name="monthly_payment" class="budgetura-form-input" required placeholder="1350.00">
                        <button type="button" id="calculate-mortgage-payment" class="budgetura-btn budgetura-btn-small budgetura-btn-secondary" style="margin-top: 10px;">
                            <?php _e( 'Auto-Calculate', 'budgetura' ); ?>
                        </button>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="extra_payment" class="budgetura-form-label"><?php _e( 'Extra Principal Payment ($)', 'budgetura' ); ?></label>
                        <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="budgetura-form-input" placeholder="100.00">
                        <span class="budgetura-form-help"><?php _e( 'Additional amount towards principal each month', 'budgetura' ); ?></span>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="property_tax" class="budgetura-form-label"><?php _e( 'Annual Property Tax ($)', 'budgetura' ); ?></label>
                        <input type="number" step="0.01" id="property_tax" name="property_tax" class="budgetura-form-input" placeholder="3600.00">
                        <span class="budgetura-form-help"><?php _e( 'Estimated annual property taxes', 'budgetura' ); ?></span>
                    </div>
                </div>

                <div class="budgetura-form-row budgetura-form-row-2col">
                    <div class="budgetura-form-group">
                        <label for="homeowners_insurance" class="budgetura-form-label"><?php _e( 'Annual Home Insurance ($)', 'budgetura' ); ?></label>
                        <input type="number" step="0.01" id="homeowners_insurance" name="homeowners_insurance" class="budgetura-form-input" placeholder="1200.00">
                        <span class="budgetura-form-help"><?php _e( 'Annual homeowners insurance premium', 'budgetura' ); ?></span>
                    </div>

                    <div class="budgetura-form-group">
                        <label for="pmi" class="budgetura-form-label"><?php _e( 'Monthly PMI ($)', 'budgetura' ); ?></label>
                        <input type="number" step="0.01" id="pmi" name="pmi" class="budgetura-form-input" placeholder="75.00">
                        <span class="budgetura-form-help"><?php _e( 'Private Mortgage Insurance (if applicable)', 'budgetura' ); ?></span>
                    </div>
                </div>

                <!-- Payoff Projection -->
                <div id="budgetura-mortgage-payoff-preview" class="budgetura-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Mortgage Summary', 'budgetura' ); ?></h3>
                    <div class="budgetura-stats-grid">
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Total Monthly Payment', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-total-payment">$0</div>
                            <div class="budgetura-stat-subtext"><?php _e( 'P&I + Taxes + Insurance + PMI', 'budgetura' ); ?></div>
                        </div>
                        <div class="budgetura-stat-card">
                            <div class="budgetura-stat-label"><?php _e( 'Years Remaining', 'budgetura' ); ?></div>
                            <div class="budgetura-stat-value" id="preview-years">0</div>
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
                        <?php echo $action === 'edit' ? __( 'Update Mortgage', 'budgetura' ) : __( 'Add Mortgage', 'budgetura' ); ?>
                    </button>
                    <a href="?action=list" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Cancel', 'budgetura' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="budgetura-manager-header">
            <div class="budgetura-manager-stats" id="budgetura-mortgage-stats">
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Mortgage Balance:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="mortgage-total-debt">$0.00</span>
                </div>
                <div class="budgetura-stat-summary">
                    <span class="stat-label"><?php _e( 'Monthly Payment:', 'budgetura' ); ?></span>
                    <span class="stat-value" id="mortgage-monthly-payment">$0.00</span>
                </div>
            </div>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Mortgage', 'budgetura' ); ?></a>
        </div>

        <div id="budgetura-mortgages-list" class="budgetura-items-list">
            <div class="budgetura-loading">
                <div class="budgetura-spinner"></div>
                <p><?php _e( 'Loading mortgage...', 'budgetura' ); ?></p>
            </div>
        </div>

        <div class="budgetura-empty-state" id="mortgages-empty-state" style="display: none;">
            <h3><?php _e( 'No Mortgage Found', 'budgetura' ); ?></h3>
            <p><?php _e( 'Add your mortgage to track your home loan and see detailed payoff projections.', 'budgetura' ); ?></p>
            <a href="?action=add" class="budgetura-btn budgetura-btn-success"><?php _e( '+ Add Your Mortgage', 'budgetura' ); ?></a>
        </div>
    <?php endif; ?>

</div>
