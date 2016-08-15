<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?>

		</title><meta http-equiv="x-ua-compatible" content="ie=edge">
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;subset=latin-ext" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">

		<link href="//www.google-analytics.com" rel="dns-prefetch">
        <link href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico" rel="shortcut icon">
        <link href="<?php echo get_template_directory_uri(); ?>/img/touch.png" rel="apple-touch-icon-precomposed">

		<meta name="description" content="<?php bloginfo('description'); ?>">

		<?php wp_head(); ?>
		<script>
        // conditionizr.com
        // configure environment tests
        conditionizr.config({
            assets: '<?php echo get_template_directory_uri(); ?>',
            tests: {}
        });
        </script>

	</head>
	<body <?php body_class(); ?>>

		<main id="homepage" class="<?php body_class(); ?>">
            <div class="container-fluid">
                <header>
					<a class="home-link" href="<?php echo home_url(); ?>">ZŠ a MŠ Zličín</a>
					<nav role="navigation">
						<?php zsms_nav(); ?>
					</nav>
					<section id="search">
						<?php get_template_part('searchform'); ?>
					</section>
					<section class="teaser-image">
                        <span class="img-container">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/teaser.jpg" alt="" />
                            <span class="img-container logo">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" class="logo" alt="" />
                            </span>
                        </span>
					</section>
				</header>
