<?php
/**
 * Template Name: Budgetura AI Coach
 *
 * Template for displaying the AI Financial Coach interface.
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/page-templates
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[budgetura_ai_coach]'); ?>
    </main>
</div>

<?php
get_footer();
