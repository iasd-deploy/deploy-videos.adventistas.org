<?php

class IASD_GlobalNav {
	public static function Show() {
?>
		<nav class="navbar navbar-default iasd-global_navbar-main" role="navigation">
			<!-- Begin Global Main Navigation -->
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle">
						<span class="sr-only"><?php _e('Navegação', 'iasd'); ?></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="http://adventistas.org/<?php echo strtolower(substr( WPLANG , 0, 2)); ?>" title="<?php _e('Clique aqui para visitar o Portal Adventista', 'iasd'); ?>">Adventistas.org</a>
				</div>
				<div class="collapse navbar-collapse navbar-global-navigation-collapse">
					<ul class="nav navbar-nav navbar-right">
						<?php echo apply_filters("IasdGlobalNav::GlobalNav", ''); ?>
					</ul>
				</div>
				<a class="search-link" href="#" title="<?php _e('Busca', 'iasd'); ?>"><?php _e('Busca', 'iasd'); ?></a>
			</div>
		</nav>
		<!-- End Global Main Navigation -->
		<!-- Begin More Panel -->
		<div class="iasd-global_navbar-more">
			<div class="container">
				<h2 class="hidden-xs"><?php _e('Mais...', 'iasd'); ?></h2>
				<?php
					$sub_menus = apply_filters( 'IasdGlobalNav::GlobalNavSubMenus', array());
					$sub_menu_names = array();
					foreach ($sub_menus as $sub_menu) {
						$sub_menu_names[$sub_menu] = apply_filters("IasdGlobalNav::GlobalNavSubMenuName", $sub_menu);
					}
				?>
				<ul class="nav nav-tabs more-panel">
					<?php
						foreach ($sub_menu_names as $sub_menu => $name) {
							echo '<li class="has-children"><a href="#'.$sub_menu.'">'.$name.'</a></li>';

						}
					?>
					<!--li class="has-children"><a href="#multisite_aba2">Sedes Regionais</a></li>
					<li class="has-children"><a href="#multisite_aba3">Serviços</a></li-->
				</ul>
				<div class="tab-content">
					<?php
						foreach ($sub_menu_names as $sub_menu => $name) {
							$smallClass = 'col-md-2';
							$largeClass = 'col-md-10';
							$extraHtmlTutorial = '';
							$extraHtmlMapa = '';
							if($sub_menu == 'multisite_aba2') {
								$smallClass = 'col-md-4';
								$largeClass = 'col-md-8 headquarters';
								$extraHtmlTutorial = '<div class="tutorial hidden-xs"></div>';
								$extraHtmlMapa = '<div class="headquarters-map map-region_01 visible-md visible-lg"></div>';
							}
					?>
						<div class="tab-pane row" id="<?php echo $sub_menu; ?>">
							<?php echo $extraHtmlTutorial; ?>
							<h3 class="<?php echo $smallClass; ?> hidden-xs"><?php echo $name ?></h3>
			 				<ul class="<?php echo $largeClass; ?>">
							<?php echo apply_filters("IasdGlobalNav::GlobalNavSubMenu", $sub_menu); ?>
			 				</ul>
			 				<?php echo $extraHtmlMapa; ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<!-- End More Panel -->
		<!-- Begin Search Box -->
		<div class="iasd-global_navbar-search">
		<div class="container">
			<form method="get" action="<?php echo site_url(); ?>/busca/?">
				<div class="input-group">
					<input type="text" name="q" class="form-control" placeholder="<?php _e('Insira as palavras-chave aqui', 'iasd'); ?>">
					<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><?php _e('Buscar', 'iasd'); ?></button>
					</span>
				</div>
			</form>
		</div>
	</div>
		<!-- End Search Box -->
		<?php
	}
}

class IasdGlobalNav extends IASD_GlobalNav {

}

add_action('global_nav_content', array('IASD_GlobalNav', 'Show'));
