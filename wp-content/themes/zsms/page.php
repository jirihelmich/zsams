<?php get_header(); ?>

<main role="main">
    <!-- section -->
    <section id="content" class="container-fluid">
        <div class="row others">

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <?php the_title(); ?>
                    </div>
                    <div class="card-block">

                        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

                            <!-- article -->
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                                <?php the_content(); ?>

                                <?php comments_template('', true); // Remove if you don't want comments ?>

                                <br class="clear">

                                <?php edit_post_link(); ?>

                            </article>
                            <!-- /article -->

                        <?php endwhile; ?>

                        <?php else: ?>

                            <!-- article -->
                            <article>

                                <h2><?php _e('Sorry, nothing to display.', 'zsms'); ?></h2>

                            </article>
                            <!-- /article -->

                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <?php get_sidebar(); ?>
            </div>

        </div>

    </section>
    <!-- /section -->
</main>

<?php get_footer(); ?>