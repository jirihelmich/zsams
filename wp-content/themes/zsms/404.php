<?php get_header(); ?>

<main role="main">
	<!-- section -->
	<section id="content" class="container-fluid">
		<div class="row others">

			<div class="col-lg-8">
				<div class="card">
					<div class="card-header">
						Stránka nenalezena.
					</div>
					<div class="card-block">
						<a href="<?php echo home_url(); ?>"><?php _e( 'Návrat na hlavní stránku', 'zsms' ); ?></a>
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


