<?php

add_action('widgets_init', array('IASD_FacebookWidget', 'Init'));

class IASD_FacebookWidget extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Plug-in social do facebook que apresenta os likes para uma página.', 'iasd') );
		parent::__construct(__CLASS__, __('IASD: Facebook Box', 'iasd'), $widget_ops);
	}

	function form($instance)
	{
        if(!isset($instance['url']) || !$instance['url'])
            $instance['url'] = get_option('pa-plugin-miscellaneous-widget-facebook', 'https://www.facebook.com/IgrejaAdventistadoSetimoDia');

        if(!isset($instance['height']) || !$instance['height'])
            $instance['height'] = 208;

?>
        <p>
            <label for="<?php echo $this->get_field_id('url'); ?>"> <?php _e("URL do post ou página", 'iasd'); ?></label>
		    <input type="text" class="widefat" id="<?php echo $this->get_field_id('url'); ?>"
                   name="<?php echo $this->get_field_name('url'); ?>" value="<?php echo $instance['url']; ?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>"> <?php _e("Altura do box", 'iasd'); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>"
                   name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" />
        </p>

<?php
	}
	static function Init() {
		register_widget(__CLASS__);
	}
	function update($new_instance, $old_instance)
	{
		if(!count($new_instance))
			$new_instance = $old_instance;

		return $new_instance;
	}
	function demoWidget() {
		$this->widget(array(), array());
	}
	function widget($args, $instance)
	{
?>
<div class="iasd-widget iasd-widget-social_media facebook col-md-4">
	<h1><?php _e('Facebook', 'iasd'); ?></h1>
	<div class="widget-box">
		<div id="fb-root"></div>

		 <script>

		 function getContentByMetaTagName(c) {
		  for (var b = document.getElementsByTagName("meta"), a = 0; a < b.length; a++) {
		    if (c == b[a].name || c == b[a].getAttribute("property")) { return b[a].content; }
		  } return false;
		}
	(function(d, s, id) {
			 var js, fjs = d.getElementsByTagName(s)[0];
			 if (d.getElementById(id)) return;
			 js = d.createElement(s); js.id = id;
			 js.src = "//connect.facebook.net/"+ getContentByMetaTagName("og:locale") +"/all.js#xfbml=1";
			 fjs.parentNode.insertBefore(js, fjs);
		 }(document, 'script', 'facebook-jssdk'));</script>
		<div class="fb-page" data-href="<?php echo isset($instance['url']) ? $instance['url'] : get_option('pa-plugin-miscellaneous-widget-facebook', 'https://www.facebook.com/IgrejaAdventistadoSetimoDia'); ?>" data-tabs="timeline" data-width="290" data-height="<?php if ($instance['height'] > 0) { echo $instance['height']; } else { echo 208; }; ?>" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/IgrejaAdventistadoSetimoDia/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/IgrejaAdventistadoSetimoDia/">Igreja Adventista do Sétimo Dia</a></blockquote></div>
	</div>
	<div class="alert alert-danger">
		<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
	</div>
</div>
<?php
	}
}
