<?php

/**
 * Template Name: Home Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package justg
 */

get_header();
$container         = velocitytheme_option('justg_container_type', 'container');
?>

<div class="wrapper" id="page-wrapper">

    <div class="<?php echo esc_attr($container); ?>" id="content">

        <div class="row">

            <?php do_action('justg_before_content'); ?>

            <div class="content-area col order-2" id="primary">

            <main class="site-main" id="main" role="main">

                <?php

                while (have_posts()) {
                    the_post();

                ?>

                    <div class="entry-content">

                        <?php the_content(); ?>

                    </div><!-- .entry-content -->

                <?php
                }
                ?>

            </main><!-- #main -->

            </div>

            <?php do_action('justg_after_content'); ?>

        </div>

    </div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
