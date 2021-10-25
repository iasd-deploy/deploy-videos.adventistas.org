<?php



function carrega_style_f7(){
	wp_register_style( 'f7-style',  plugin_dir_url( __FILE__ ) . 'f7-style.css' );
	wp_enqueue_style( 'f7-style' );

}
add_action( 'wp_enqueue_scripts', 'carrega_style_f7' );

add_action('widgets_init', array('IASD_F7_Widget', 'Init'));

// Creating the widget 
class IASD_F7_Widget extends WP_Widget {

	static function Init() {
		register_widget(__CLASS__);
	}

	function __construct() {

		$widget_ops = array('classname' => __CLASS__, 'description' => __('Widget com slider do conteúdos recentes do feliz7play.com.'));
		parent::__construct(__CLASS__, __('IASD: Slider F7', 'iasd'), $widget_ops);

		//Verifica se a tarrefa ja esta agendada, se ñ estiver entao faz a agenda. 
		if (!wp_next_scheduled( 'wp_update_f7' ) ) {
	  	wp_schedule_event( time(), 'five_minutes', 'wp_update_f7');
		}
		// Action que é executada de hora em hora. 
		add_action( 'wp_update_f7', 'wp_cron_banner_f7' );
	
	}

	// Creating widget front-end
	function widget( $args, $instance ) {

		$title = $instance['title'];			
		$json = get_values_f7();
		$lang = substr(get_locale(), 0, 2);
		if($lang == "pt"){
			$lang = "pt";
		} else {
			$lang = "es";
		}

	
		if($json){
	
		?>

		<div class="iasd-widget f7 <?php if($instance['width'] == 'col-md-12'){ echo 'col-md-12'; }else{ if($instance['width'] == 'col-md-8'){ echo 'col-md-8'; } else { echo 'col-md-4'; }}; ?>">
				
			<div class="row">
				<div class="col-md-3 col-xs-6">
        	<img class="img-responsive" src="<?php echo plugin_dir_url( __FILE__ ) . 'icone-f7.svg'; ?>" alt="<?php _e('Ir para o Feliz 7  Play', 'iasd'); ?>">
				</div>
				<div class="hidden-md hidden-lg clear "></div>
				<div class="col-md-6 col-sm-12 col-xs-12 text">
        	<h4><?php _e('Você ja conhece o Feliz 7 Play?', 'iasd'); ?></h4>
				</div>
				<div class="col-md-3 hidden-xs hidden-sm">
						<a href="https://www.feliz7play.com/<?php echo $lang; ?>/?utm_source=<?php echo get_site_url(); ?>&utm_medium=Widgets%20Feliz%207%20Play&utm_campaign=BTN%20do%20banner" class="btn btn-lg btn-block"> <?php _e('ASSISTA GRÁTIS', 'iasd'); ?></a>
				</div>
			</div>

			<div class="slider-f7 owl-carousel owl-theme" id="owl-bannerF7">
				<?php foreach($json as $index){ 
					$url = $index['VideoUrl'] . "?utm_source=" . get_site_url() . "&utm_medium=Widgets%20Feliz%207%20Play&utm_campaign=" . $index['Title'];
					?>
					<a class="item" href="<?php echo $url; ?>" alt="<?php echo $index['Title']; ?>" title="<?php echo $index['Title']; ?>"><img class="img-responsive" src="<?php echo $index['ThumbUrl']; ?>" alt="<?php echo $index['Title']; ?>"></a>
				<?php } ?>
			</div>
			
			<div class="row">
				<div class="col-md-2 hidden-md hidden-lg">
					<a href="https://www.feliz7play.com/<?php echo $lang; ?>/?utm_source=<?php echo get_site_url(); ?>&utm_medium=Widgets%20Feliz%207%20Play&utm_campaign=BTN%20do%20banner" class="btn btn-lg btn-block"><?php _e('ASSISTA GRÁTIS', 'iasd'); ?></a>
				</div>
			</div>
				
		</div>
	
		<?php

		}else{
			?>
				<div class="iasd-widget f7 <?php if($instance['width'] == 'col-md-12'){ echo 'col-md-12'; }else{ if($instance['width'] == 'col-md-8'){ echo 'col-md-8'; } else { echo 'col-md-4'; }}; ?>">
					<p>Nada na varievel</p>
				</div>

			<?php
		}
	}
				
	// Widget Backend 
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'width' => 'col-md-12', 'sidebar' => false));
		$width = $instance['width'];

		$id = $this->id;

		?>

			<p><?php _e('Banner publicitário automático do Feliz 7 Play', 'iasd'); ?></p>

			<input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="hidden" value="col-md-12" />
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['sidebar'] = $new_instance['sidebar'];	
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['title'] = strip_tags($new_instance['title']);
		
		return $instance;
	}

	function widgetWidthClass() {
		$widgets = get_option('widget_iasd_f7_widget');
		return $widgets[$this->number]['width'];
	}
}

function add_this_script_footer(){
	echo '
<script type="text/javascript">
	(function($){

		$(document).ready(function () {
			$("#owl-bannerF7").owlCarousel({
					autoPlay: 3000, //Set AutoPlay to 3 seconds
					items : 4,
					itemsDesktop : [1199,4],
					itemsDesktopSmall : [979,4],
					itemsMobile : [2,6],
					autoPlay: true,
					lazyLoad: false
			});
	
		});
	
	})(jQuery);
</script>'
	;
		}

add_action('wp_footer', 'add_this_script_footer', 999);


