<?php get_header(); ?>

<main role="main">
    <!-- section -->
    <section id="content" class="container-fluid">
        <div class="row others">

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                    </div>
                    <div class="card-block">

                        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

                            <!-- article -->
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                                <!-- post thumbnail -->
                                <?php if (has_post_thumbnail()) : // Check if Thumbnail exists ?>
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                        <?php the_post_thumbnail(array(400)); // Fullsize image for the single post ?>
                                    </a>
                                <?php endif; ?>
                                <!-- /post thumbnail -->

                                <?php the_content(); // Dynamic Content ?>

                                <?php the_tags(__('Tags: ', 'zsms'), ', ', '<br>'); // Separated by commas with a line break at the end ?>

                                <p class="text-xs-right">
                                    Zařazeno v kategorii: <?php the_category(', '); ?>, <span class="date"><?php the_time('j. n. Y'); ?> <?php the_time('H:i'); ?></span>
                                </p>

                                <?php edit_post_link(); // Always handy to have Edit Post Links available ?>

                            </article>
                            <!-- /article -->

                        <?php endwhile; ?>

                        <?php else: ?>

                            <!-- article -->
                            <article>

                                <h1><?php _e('Sorry, nothing to display.', 'zsms'); ?></h1>

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
