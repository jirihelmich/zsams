

				<footer class="container-fluid">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-lg-4 contact">
							<?php dynamic_sidebar('footer-widget'); ?>
						</div>
						<div class="col-xs-12 col-lg-4 logo hidden-sm-down">
							<span class="img-container logo">
								<img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="">
							</span>
						</div>
						<div class="col-xs-12 col-sm-12 col-lg-4 admin">
							<p>
								Správa obsahu: Mgr. Andrea Smolíková <br/>
								Webmaster: <a href="http://helmich.cz/">RNDr. Jiří Helmich</a>
							</p>
						</div>
					</div>
				</footer>

			</div>
			<!-- /wrapper -->
		</main>

		<?php wp_footer(); ?>

		<!-- analytics -->
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-82283730-1', 'auto');
			ga('send', 'pageview');

		</script>

	</body>
</html>
