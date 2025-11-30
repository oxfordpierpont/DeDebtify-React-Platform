<?php
/**
 * Template Name: Budgetura Snapshots
 * Description: Financial snapshots template
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[budgetura_snapshots]'); ?>
    </main>
</div>

<?php
get_footer();
