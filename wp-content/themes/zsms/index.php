<?php get_header(); ?>

	<main role="main">
		<!-- section -->
		<section>

			<?php

			$source = array(
				2 => "Základní škola",
				3 => "Mateřská škola",
				4 => "Školní družina"
			);

			foreach($source as $key => $label)
			{
				?>
				<div>
					<div><?php echo $label; ?></div>
					<div>
						<?php latest_article($key); ?>
					</div>
				</div>
			<?php
			}

			?>

		</section>
		<!-- /section -->
	</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
