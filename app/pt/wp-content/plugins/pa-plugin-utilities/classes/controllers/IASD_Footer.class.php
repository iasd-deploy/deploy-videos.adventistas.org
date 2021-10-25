<?php


class IASD_Footer {

	public static function Show() {
?>
		<footer>
			<div class="container">
				<div class="row">
					<?php
						self::RenderLeft();
						self::RenderRight();
					?>
				</div>
			</div>
		</footer>
<?php
	}

	public static function RenderLeft() {
?>
						<div class="col-md-2 visible-md visible-lg">
							<?php echo apply_filters("IASD_Footer::LeftMenu", '', 'footer_1'); ?>
						</div>
						<div class="col-md-2 visible-md visible-lg">
							<?php echo apply_filters("IASD_Footer::LeftMenu", '', 'multisite_aba1'); ?>
						</div>
						<div class="col-md-2 visible-md visible-lg">
							<?php echo apply_filters("IASD_Footer::LeftMenu", '', 'multisite_aba2'); ?>
						</div>
						<div class="col-md-2 visible-md visible-lg">
<?php
	$location = apply_filters('IASD_Footer::LeftMenu4Location', 'footer_4');
	$locations = get_nav_menu_locations();
	if(isset($locations[$location]) && $locations[$location]) {
		$menu_id = $locations[$location];
		$menu = wp_get_nav_menu_object( $menu_id );
		echo '<h1>', $menu->name, '</h1>';
		wp_nav_menu( array( 'theme_location'  => $location, 'menu_class' => 'unstyled', 'depth' => 1) );
	}
?>
						</div>
<?php
	}

	public static function RenderRight() {
?>

						<div class="col-md-3 col-xs-4 col-md-offset-1 info">
<?php

							self::RenderRightAddress();
							self::RenderRightExtraAddress();
?>


							<p class="copyright">
								<?php _e('Copyright © 2013-2017', 'iasd'); ?><br />
								<?php _e('Igreja Adventista do Sétimo Dia', 'iasd'); ?><br/>
								<?php _e('Todos os direitos reservados', 'iasd'); ?>
							</p>
							<a class="back-top hidden-md hidden-lg"><?php _e('Voltar ao topo', 'iasd'); ?></a>
						</div>
<?php
	}

	public static function RenderRightAddress() {
		$local_address = get_option('local_information_address');
?>
						<address>
							<h1><?php _e('Divisão Sul-Americana', 'iasd'); ?></h1>
							<?php
								if($endereco = get_option('pafooter_master_endereco')):
									$endereco = explode("\r\n", $endereco);
									$endereco = implode('</p><p>', $endereco);
									echo '<p>'.$endereco.'</p>';
								endif;
							?>
							<ul class="social-media">
								<?php if($link = get_option('pafooter_master_facebook')): ?><li><a target="_blank" title="<?php _e('Acesse a fanpage no Facebook', 'iasd'); ?>" class="facebook" href="<?php echo $link;; ?>"></a></li><?php endif; ?>
								<?php if($link = get_option('pafooter_master_twitter')): ?><li><a target="_blank" title="<?php _e('Acesse o perfil no Twitter', 'iasd'); ?>" class="twitter" href="<?php echo $link; ?>"></a></li><?php endif; ?>
								<?php if($link = get_option('pafooter_master_google')): ?><li><a target="_blank" title="<?php _e('Acesse o perfil no Google+', 'iasd'); ?>" class="google" href="<?php echo $link; ?>"></a></li><?php endif; ?>
								<?php if($link = get_option('pafooter_master_youtube')): ?><li><a target="_blank" title="<?php _e('Acesse o canal no Youtube', 'iasd'); ?>" class="youtube" href="<?php echo $link; ?>"></a></li><?php endif; ?>
								<?php if($link = get_option('pafooter_master_rss')): ?><li><a title="<?php _e('Assine o RSS', 'iasd'); ?>" class="rss" href="<?php echo $link; ?>"></a></li><?php endif; ?>
							</ul>
						</address>
<?php
	}

	public static function RenderRightExtraAddress() {
		$output_a = '';
		if($titulo = get_option('pafooter_titulo')) $output_a .= '<h1>'.$titulo.'</h1>';
		if($endereco = get_option('pafooter_endereco')):
			$endereco = explode("\r\n", $endereco);
			$endereco = implode('</p><p>', $endereco);
			$output_a .= '<p>'.$endereco.'</p>';
		endif;

		ob_start();
			if($link = get_option('pafooter_facebook')): ?><li><a target="_blank" title="<?php _e('Acesse a fanpage no Facebook', 'iasd'); ?>" class="facebook" href="<?php echo $link; ?>"></a></li><?php endif;
			if($link = get_option('pafooter_twitter')): ?><li><a target="_blank" title="<?php _e('Acesse o perfil no Twitter', 'iasd'); ?>" class="twitter" href="<?php echo $link; ?>"></a></li><?php endif;
			if($link = get_option('pafooter_google')): ?><li><a target="_blank" title="<?php _e('Acesse o perfil no Google+', 'iasd'); ?>" class="google" href="<?php echo $link; ?>"></a></li><?php endif;
			if($link = get_option('pafooter_youtube')): ?><li><a target="_blank" title="<?php _e('Acesse o canal no Youtube', 'iasd'); ?>" class="youtube" href="<?php echo $link; ?>"></a></li><?php endif;
			if($link = get_option('pafooter_rss')): ?><li><a title="<?php _e('Assine o RSS', 'iasd'); ?>" class="rss" href="<?php echo $link; ?>"></a></li><?php endif;
		$output_b = ob_get_contents();
		ob_end_clean();
		if($output_b)
			$output_b = '<ul class="social-media">' . $output_b . '</ul>';
		if($output_a || $output_b)
			echo '<address>'. $output_a . $output_b .'</address>';
	}

	/**
		Informações da Sede
*/

	public static function AdminMenu() {
		add_submenu_page( 'pa-adventistas', 'Rodapé', 'Rodapé', 'edit_pages', 'pa-adv-footer', array('IASD_Taxonomias', 'SettingsRender'));

		register_setting('pa-adv-footer', 'pafooter_titulo');
		register_setting('pa-adv-footer', 'pafooter_facebook');
		register_setting('pa-adv-footer', 'pafooter_twitter');
		register_setting('pa-adv-footer', 'pafooter_google');
		register_setting('pa-adv-footer', 'pafooter_youtube');
		register_setting('pa-adv-footer', 'pafooter_rss');
		register_setting('pa-adv-footer', 'pafooter_resumo');
		register_setting('pa-adv-footer', 'pafooter_endereco');

		add_settings_section('pa-adv-footer-default', __('Configurações do Rodapé', 'iasd'), array(__CLASS__, 'AdminMenuInfoSection'), 'pa-adv-footer');
		add_settings_field('pafooter_titulo',   __('Titulo', 'iasd'),   array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-footer', 'pa-adv-footer-default', 'pafooter_titulo');
		add_settings_field('pafooter_facebook', __('Facebook', 'iasd'), array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-footer', 'pa-adv-footer-default', 'pafooter_facebook');
		add_settings_field('pafooter_twitter',  __('Twitter', 'iasd'),  array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-footer', 'pa-adv-footer-default', 'pafooter_twitter');
		add_settings_field('pafooter_google',   __('Google+', 'iasd'),  array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-footer', 'pa-adv-footer-default', 'pafooter_google');
		add_settings_field('pafooter_youtube',  __('Youtube', 'iasd'),  array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-footer', 'pa-adv-footer-default', 'pafooter_youtube');
		add_settings_field('pafooter_rss',      __('Feed RSS', 'iasd'), array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-footer', 'pa-adv-footer-default', 'pafooter_rss');
		add_settings_field('pafooter_endereco', __('Endereço', 'iasd'), array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-footer', 'pa-adv-footer-default', 'pafooter_endereco');
	}
	public static function AdminMenuInfoSection() {
		echo '<p>' . __('Use os campos abaixo para configurar o rodapé global', 'iasd') . '</p>';
	}
	public static function AdminMenuFieldSetting($setting_name) {
		switch ($setting_name) {
			case 'pafooter_endereco':
				echo '<textarea name="'.$setting_name.'" id="'.$setting_name.'" class="widefat">'. get_option($setting_name) .'</textarea>';
				break;
			default:
				echo '<input name="'.$setting_name.'" id="'.$setting_name.'" type="input" value="'. get_option($setting_name) .'" class="widefat" />';
				break;
		}
	}
}

class iasdFooter extends IASD_Footer {

}

add_action('admin_menu', array('IASD_Footer', 'AdminMenu'), 100);
add_action('network_admin_menu', array('IASD_Footer', 'AdminMenu'), 100);
add_action('footer_content', array('IASD_Footer', 'Show'));
