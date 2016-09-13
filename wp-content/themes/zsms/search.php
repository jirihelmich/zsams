<?php get_header(); ?>

<main role="main">
	<!-- section -->
	<section id="content" class="container-fluid">
		<div class="row others">

			<div class="col-lg-8">
				<div class="card">
					<div class="card-header">
						<?php echo sprintf( __( '%s výsledků pro ', 'zsms' ), $wp_query->found_posts ); echo get_search_query(); ?>
					</div>
					<div class="card-block">

						<?php get_template_part('loop'); ?>

						<?php get_template_part('pagination'); ?>

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
