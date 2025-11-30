<?php
/**
 * Template Name: Budgetura Dashboard
 * Description: Main dashboard template for Budgetura
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[budgetura_dashboard]'); ?>
    </main>
</div>

<?php
get_footer();
