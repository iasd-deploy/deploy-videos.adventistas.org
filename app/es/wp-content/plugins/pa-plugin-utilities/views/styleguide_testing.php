
<!DOCTYPE html>
<html lang="pt">
	<head>
		<link href="http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">
<link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="author" content="">
<meta name="keywords" content="">
<meta name="robots" content="index, follow">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Favicons --
<link rel="shortcut icon" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/favicon.ico">
<link rel="apple-touch-icon" sizes="57x57" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="114x114" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="72x72" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="144x144" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="60x60" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon" sizes="120x120" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="76x76" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="152x152" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/favicon-160x160.png" sizes="160x160" />
<link rel="icon" type="image/png" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/favicon-16x16.png" sizes="16x16" />
<meta name="msapplication-TileColor" content="#145351" />
<meta name="msapplication-TileImage" content="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/mstile-144x144.png" />
<meta name="msapplication-square70x70logo" content="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/mstile-70x70.png" />
<meta name="msapplication-square144x144logo" content="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/mstile-144x144.png" />
<meta name="msapplication-square150x150logo" content="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/mstile-150x150.png" />
<meta name="msapplication-square310x310logo" content="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/mstile-310x310.png" />
<meta name="msapplication-wide310x150logo" content="http://192.168.1.111/institucional/wp-content/themes/pa-thema-capa/static/img/favicons/mstile-310x150.png" /> -->
<!--[if lt IE 9]>
	<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/html5shiv.js"></script>
	<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/respond.min.js"></script>
<![endif]--><!-- Head -->
<?php wp_head(); ?>
	</head>
	<body>

		<!-- *************************** -->
		<!-- ********* Content ********* -->
		<!-- *************************** -->

		<div class="container">
			<div class="row widgets-list">
				<?php iasd_dynamic_sidebar('styleguide-banner'); ?>
			</div>
			<div class="row widgets-list" style="border-top: 1px solid #F00;">
				<div class="col-md-8">
					<div class="row">
						<?php iasd_dynamic_sidebar('styleguide-article'); ?>
					</div>
				</div>
				<aside class="col-md-4 visible-md visible-lg" style="border-left: 1px solid #00f;">
					<div class="row widgets-list">
						<?php iasd_dynamic_sidebar('styleguide-aside'); ?>
					</div>
				</aside>
			</div>
		</div>

		<!-- *************************** -->
		<!-- ******* End Content ******* -->
		<!-- *************************** -->

<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/modernizr.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/bootstrap.min.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/iasd_global_nav.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/iasd_main_nav.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/iasd_dropdown_nav.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/iasd_footer.js"></script>		
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/owl.carousel.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/iasd_widgets.js"></script>
<script src="http://lab.nextt.com.br/iasd/styleguide/static/lib/iasd_plugins.js"></script><!-- Scripts -->
<?php wp_footer(); ?>
	</body>
</html>