<?php get_header(); ?>

<main role="main">
	<!-- section -->
	<section id="content" class="container-fluid">
		<div class="row others">

			<div class="col-lg-8">
				<div class="card">
					<div class="card-header">
						Kategorie <?php single_cat_title(); ?>
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
