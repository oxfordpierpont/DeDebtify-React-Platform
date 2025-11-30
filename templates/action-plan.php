<?php
/**
 * Debt Action Plan Template
 *
 * This template displays a comprehensive debt payoff action plan.
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
    echo '<p>' . __( 'Please log in to view your debt action plan.', 'budgetura' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
?>

<!-- Navigation -->
<?php Budgetura_Helpers::render_navigation( 'action_plan' ); ?>

<div class="budgetura-dashboard budgetura-action-plan">

    <div class="budgetura-dashboard-header">
        <h1><?php _e( 'Debt Action Plan', 'budgetura' ); ?></h1>
        <p><?php _e( 'Create a strategic plan to pay off your debts efficiently', 'budgetura' ); ?></p>
    </div>

    <!-- Strategy Selection -->
    <div class="budgetura-form-container">
        <div class="budgetura-form-header">
            <h2><?php _e( 'Configure Your Plan', 'budgetura' ); ?></h2>
        </div>

        <form id="budgetura-action-plan-form" class="budgetura-form">
            <div class="budgetura-form-row budgetura-form-row-2col">
                <div class="budgetura-form-group">
                    <label for="payoff_strategy" class="budgetura-form-label"><?php _e( 'Payoff Strategy', 'budgetura' ); ?> <span class="required">*</span></label>
                    <select id="payoff_strategy" name="payoff_strategy" class="budgetura-form-select" required>
                        <option value="avalanche"><?php _e( 'Avalanche (Highest Interest First)', 'budgetura' ); ?></option>
                        <option value="snowball"><?php _e( 'Snowball (Lowest Balance First)', 'budgetura' ); ?></option>
                    </select>
                    <span class="budgetura-form-help" id="strategy-help">
                        <?php _e( 'Avalanche saves the most on interest. Snowball provides quick wins.', 'budgetura' ); ?>
                    </span>
                </div>

                <div class="budgetura-form-group">
                    <label for="extra_payment" class="budgetura-form-label"><?php _e( 'Extra Monthly Payment ($)', 'budgetura' ); ?></label>
                    <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="budgetura-form-input" value="0" placeholder="0.00">
                    <span class="budgetura-form-help"><?php _e( 'Additional amount you can apply to debt each month', 'budgetura' ); ?></span>
                </div>
            </div>

            <div class="budgetura-form-actions">
                <button type="submit" class="budgetura-btn budgetura-btn-success">
                    <?php _e( 'Generate Action Plan', 'budgetura' ); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Plan Summary -->
    <div id="budgetura-plan-summary" class="budgetura-section" style="display: none;">
        <h2><?php _e( 'Plan Summary', 'budgetura' ); ?></h2>

        <div class="budgetura-stats-grid">
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'Total Debt', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value" id="plan-total-debt">$0</div>
            </div>
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'Total Interest', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value" id="plan-total-interest">$0</div>
            </div>
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'Time to Debt Freedom', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value" id="plan-time-to-freedom">0 months</div>
            </div>
            <div class="budgetura-stat-card">
                <div class="budgetura-stat-label"><?php _e( 'Debt-Free Date', 'budgetura' ); ?></div>
                <div class="budgetura-stat-value" id="plan-freedom-date" style="font-size: 1.2rem;">—</div>
            </div>
        </div>

        <!-- Strategy Comparison -->
        <div class="budgetura-comparison-card">
            <h3><?php _e( 'Strategy Comparison', 'budgetura' ); ?></h3>
            <div class="budgetura-comparison-grid">
                <div class="budgetura-comparison-item">
                    <strong><?php _e( 'Avalanche Method', 'budgetura' ); ?></strong>
                    <p id="avalanche-summary"><?php _e( 'Calculating...', 'budgetura' ); ?></p>
                </div>
                <div class="budgetura-comparison-item">
                    <strong><?php _e( 'Snowball Method', 'budgetura' ); ?></strong>
                    <p id="snowball-summary"><?php _e( 'Calculating...', 'budgetura' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payoff Timeline -->
    <div id="budgetura-payoff-timeline" class="budgetura-section" style="display: none;">
        <h2><?php _e( 'Payoff Timeline', 'budgetura' ); ?></h2>
        <p class="budgetura-section-description"><?php _e( 'Follow this order to maximize your debt payoff efficiency', 'budgetura' ); ?></p>

        <div id="budgetura-timeline-items" class="budgetura-timeline-list">
            <!-- Timeline items will be inserted here by JavaScript -->
        </div>
    </div>

    <!-- Monthly Payment Schedule -->
    <div id="budgetura-payment-schedule" class="budgetura-section" style="display: none;">
        <div class="budgetura-section-header">
            <h2><?php _e( 'Monthly Payment Schedule', 'budgetura' ); ?></h2>
            <button id="toggle-schedule" class="budgetura-btn budgetura-btn-secondary budgetura-btn-small">
                <?php _e( 'Show Details', 'budgetura' ); ?>
            </button>
        </div>

        <div id="budgetura-schedule-details" style="display: none;">
            <div class="budgetura-table-container">
                <table class="budgetura-table" id="payment-schedule-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Month', 'budgetura' ); ?></th>
                            <th><?php _e( 'Debt', 'budgetura' ); ?></th>
                            <th><?php _e( 'Payment', 'budgetura' ); ?></th>
                            <th><?php _e( 'Principal', 'budgetura' ); ?></th>
                            <th><?php _e( 'Interest', 'budgetura' ); ?></th>
                            <th><?php _e( 'Remaining', 'budgetura' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Schedule rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Items -->
    <div id="budgetura-action-items" class="budgetura-section" style="display: none;">
        <h2><?php _e( 'Next Steps', 'budgetura' ); ?></h2>
        <div class="budgetura-action-list">
            <div class="budgetura-action-item">
                <span class="budgetura-action-number">1</span>
                <div class="budgetura-action-content">
                    <h4><?php _e( 'Make Minimum Payments', 'budgetura' ); ?></h4>
                    <p><?php _e( 'Continue making minimum payments on all debts to avoid penalties and maintain good credit.', 'budgetura' ); ?></p>
                </div>
            </div>
            <div class="budgetura-action-item">
                <span class="budgetura-action-number">2</span>
                <div class="budgetura-action-content">
                    <h4><?php _e( 'Focus Extra Payments', 'budgetura' ); ?></h4>
                    <p id="action-focus-text"><?php _e( 'Apply all extra payments to your target debt according to your chosen strategy.', 'budgetura' ); ?></p>
                </div>
            </div>
            <div class="budgetura-action-item">
                <span class="budgetura-action-number">3</span>
                <div class="budgetura-action-content">
                    <h4><?php _e( 'Roll Over Payments', 'budgetura' ); ?></h4>
                    <p><?php _e( 'When you pay off a debt, add that payment amount to the next debt in your plan.', 'budgetura' ); ?></p>
                </div>
            </div>
            <div class="budgetura-action-item">
                <span class="budgetura-action-number">4</span>
                <div class="budgetura-action-content">
                    <h4><?php _e( 'Track Your Progress', 'budgetura' ); ?></h4>
                    <p><?php _e( 'Update your balances monthly and celebrate each milestone along the way!', 'budgetura' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print and Export -->
    <div id="budgetura-plan-actions" class="budgetura-section" style="display: none;">
        <div class="budgetura-form-actions">
            <button id="print-plan" class="budgetura-btn budgetura-btn-secondary">
                <?php _e( 'Print Plan', 'budgetura' ); ?>
            </button>
            <button id="regenerate-plan" class="budgetura-btn budgetura-btn-secondary">
                <?php _e( 'Regenerate Plan', 'budgetura' ); ?>
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="plan-loading" class="budgetura-loading" style="display: none;">
        <div class="budgetura-spinner"></div>
        <p><?php _e( 'Generating your action plan...', 'budgetura' ); ?></p>
    </div>

    <!-- Empty State -->
    <div class="budgetura-empty-state" id="plan-empty-state" style="display: none;">
        <h3><?php _e( 'No Debts Found', 'budgetura' ); ?></h3>
        <p><?php _e( 'You need to add credit cards or loans before generating an action plan.', 'budgetura' ); ?></p>
        <a href="?page=credit-cards&action=add" class="budgetura-btn budgetura-btn-success"><?php _e( 'Add Credit Card', 'budgetura' ); ?></a>
        <a href="?page=loans&action=add" class="budgetura-btn budgetura-btn-secondary"><?php _e( 'Add Loan', 'budgetura' ); ?></a>
    </div>

</div>
