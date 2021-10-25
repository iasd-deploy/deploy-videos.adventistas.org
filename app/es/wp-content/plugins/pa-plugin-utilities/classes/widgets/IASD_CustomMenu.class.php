<?php

add_action('widgets_init', array('IASD_CustomMenu', 'Init'));
require_once( ABSPATH . 'wp-includes/default-widgets.php');

 class IASD_CustomMenu extends WP_Nav_Menu_Widget {

	function __construct() {
		$widget_ops = array(
			'classname' => 'IASD_CustomMenu',
			'description' => __('Use this widget to add one of your custom menus as a widget.'),
		);
		WP_Widget::__construct('IASD_CustomMenu', __('IASD: Menu Personalizado', 'iasd'), $widget_ops);
	}

	static function Init() {
		register_widget(__CLASS__);
	}

	function demoWidget() {
		$nav_menus = wp_get_nav_menus();
		$count = -1;
		$chosen = false;
		foreach($nav_menus as $navMenu) {
			if($navMenu->count > $count) {
				$count = $navMenu->count;
				$chosen = $navMenu->slug;
			}
		}
		if($chosen)
			$this->widget(array(), array('nav_menu' => $chosen));
		else
			echo '<span class="btn btn-warning">Não existe nenhum menu para testar</span>';
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';

		// Get menus
		$menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

		// If no menus exists, direct the user to go and create some.
		if ( !$menus ) {
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
			<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
		<?php
			foreach ( $menus as $menu ) {
				echo '<option value="' . $menu->term_id . '"'
					. selected( $nav_menu, $menu->term_id, false )
					. '>'. $menu->name . '</option>';
			}
		?>
			</select>
		</p>
		<?php
	}

	function widget($args, $instance) {
		$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

		if ( !$nav_menu )
			return;

		echo '<div class="iasd-widget iasd-widget-menu col-md-4">';

		$menuOpts = array( 'fallback_cb' => '', 'menu' => $nav_menu, 'container_class' => 'iasd-page-links' );

		if (class_exists("MainNavMenuWalker")){
			$menuOpts['walker'] = new MainNavMenuWalker(TRUE);
			$menuOpts['items_wrap']  = '<ul>%3$s</ul>';
		}

		wp_nav_menu( $menuOpts );
		echo '<div class="alert alert-danger">
				<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
			</div></div>';
	}


}
