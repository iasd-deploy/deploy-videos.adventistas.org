<?php

add_action('widgets_init', array('IASD_ImageWidget', 'Init'));

class IASD_ImageWidget extends WP_Widget {

	static function Init() {
		register_widget(__CLASS__);
	}

	function __construct() {

		$widget_ops = array('classname' => __CLASS__, 'description' => __('Widget de imagem da IASD'));
		parent::__construct(__CLASS__, __('IASD: Imagem', 'iasd'), $widget_ops);
	}


	function widget($args, $instance) {
		$title = $instance['title'];
		$link = $instance['link'];
		$target = $instance['target'];
		$image_id = $instance['image_id'];
		$image_url = '';
		if(!empty($image_id)) $image_url = wp_get_attachment_image($image_id, 'full');
	
?>
		<div class="iasd-widget iasd-widget-img<?php if($title != '') {echo '_title';} ?> <?php if($instance['width'] == 'col-md-12'){ echo 'col-md-12'; }else{ if($instance['width'] == 'col-md-8'){ echo 'col-md-8'; } else { echo 'col-md-4'; }}; ?>">
			<div>
				<?php if($title != '') echo '<h1>' . $title . '</h1>'; ?>
			</div>

			<?php if ($link != '') echo '<a href="'.$link.'" target="'.$target.'" >'; ?>
				<?php if($image_url != '') {echo $image_url;} else 
				{ echo '<img src="http://placehold.it/1280x800">';} ?>
			<?php if ($link != '') echo '</a>'; ?> 

			<div class="alert alert-danger">
				<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
			</div>
		</div>
<?php
	}

	function update($new_instance, $old_instance) {
		
		$instance = $old_instance;
        
        $instance['sidebar'] = $new_instance['sidebar'];
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['saved'] = strip_tags($new_instance['saved']);
		$instance['target'] = strip_tags($new_instance['target']);


		$instance['title'] = strip_tags($new_instance['title']);
		$instance['link'] = strip_tags($new_instance['link']);
		$instance['filter'] = isset($new_instance['filter']);
		$instance['image_id'] = strip_tags($new_instance['image_id']);

		return $instance;
	}

	function form($instance) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'link' => '', 'image_id' => '', 'sidebar' => false, 'width' => '', 'saved' => '', 'target' => ''));

		$title = strip_tags($instance['title']);
		$link = $instance['link'];
		$width = $instance['width'];

		$id = $this->id;

		$image_id = $instance['image_id'];
		$instance = wp_parse_args($instance, array( 'width' => 'col-md-4') );

		$saved = $instance['saved'];

		wp_enqueue_media();

		if ($saved == '1') {
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('link'); ?><?php echo $id; ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr($link); ?>" /></p>

		<div <?php if($instance['link'] == null || $instance['link'] == '') {?> style="display:none;" <?php } ?> id="linktarget_<?php echo $id; ?>">
			<p><label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Abrir link:'); ?></label>
				<select name="<?php echo $this->get_field_name('target'); ?>" id="<?php echo $this->get_field_id('target'); ?>" class="widefat">
					<option value="_self"<?php selected( $instance['target'], '_self' ); ?>><?php _e( 'Mesma aba' ); ?></option>
					<option value="_blank"<?php selected( $instance['target'], '_blank' ); ?>><?php _e( 'Nova aba' ); ?></option>
				</select>
			</p>
		</div>

		<div class="hidden">
			<input class="widefat" id="<?php echo $this->get_field_id('image_id'); ?><?php echo $id; ?>" name="<?php echo $this->get_field_name('image_id'); ?>" type="text" value="<?php echo esc_attr($image_id); ?>" />
			<input class="widefat" id="<?php echo $this->get_field_id('width'); ?><?php echo $id; ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr($width); ?>" />
			<input class="widefat" id="<?php echo $this->get_field_id('saved'); ?><?php echo $id; ?>" name="<?php echo $this->get_field_name('saved'); ?>" type="text" value="1" />

		</div>
	
		<div style="text-align: center" id="thumb_prev_<?php echo $id; ?>">
			<?php if ($image_id != null && $image_id != '') { ?><?php echo wp_get_attachment_image($image_id, 'thumb_220x220'); ?><?php } ?>
		</div>
		
		<div style="text-align: center">
			<input type="button" value="<?php echo __('Selecionar imagem'); ?>" class="button btn-info" id="iasd_upload_image_btn<?php echo $id; ?>" name="iasd_upload_image_btn<?php echo $id; ?>">
		</div>

		<div>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Disposição:' ); ?></label>
			<select name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
				<option value="col-md-4"<?php selected( $instance['width'], 'col-md-4' ); ?>><?php _e( '1/3 de coluna' ); ?></option>
				<option value="col-md-8"<?php selected( $instance['width'], 'col-md-8' ); ?>><?php _e( '2/3 de coluna' ); ?></option>
				<option value="col-md-12"<?php selected( $instance['width'], 'col-md-12' ); ?>><?php _e( 'Largura total' ); ?></option>
			</select>
		</div>
		<br>

		<script>
			
			// Uploading files
			var file_frame<?php echo intval(array_pop(explode('-', $this->id)));?>;

			jQuery('#iasd_upload_image_btn<?php echo $id; ?>').live('click', function( event ){

				event.preventDefault();
				
				if ( file_frame<?php echo intval(array_pop(explode('-', $this->id)));?> ) {
            		file_frame<?php echo intval(array_pop(explode('-', $this->id)));?>.open();
            	return;
        		}

				// Create the media frame.
				file_frame<?php echo intval(array_pop(explode('-', $this->id)));?> = wp.media.frames.file_frame<?php echo intval(array_pop(explode('-', $this->id)));?> = wp.media({
					title: jQuery( this ).data( 'uploader_title' ),
					button: {
					text: jQuery( this ).data( 'uploader_button_text' ),
					},
					multiple: false // Set to true to allow multiple files to be selected
				});
				 
				// When an image is selected, run a callback.
				file_frame<?php echo intval(array_pop(explode('-', $this->id)));?>.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame<?php echo intval(array_pop(explode('-', $this->id)));?>.state().get('selection').first().toJSON();
					att_id = jQuery('#<?php echo $this->get_field_id('image_id'); ?><?php echo $id; ?>').val(attachment.id);
					var new_image =  attachment.sizes.full.url;

					jQuery('#thumb_prev_<?php echo $id; ?>').html('<img height="220" width="220" src="'+new_image+'">');
					$('.widget-control-save').trigger('change');
 			  
				});

				file_frame<?php echo intval(array_pop(explode('-', $this->id)));?>.on('open',function() {
					  var selection = file_frame<?php echo intval(array_pop(explode('-', $this->id)));?>.state().get('selection');
					  id = jQuery('#<?php echo $this->get_field_id('image_id'); ?><?php echo $id; ?>').val();
					  attachment = wp.media.attachment(id);
					  attachment.fetch();
					  selection.add( attachment ? [ attachment ] : [] );
				});		 					

				// Finally, open the modal
				file_frame<?php echo intval(array_pop(explode('-', $this->id)));?>.open();
			});

			jQuery( document ).ready(function() {
				jQuery( "#widget-iasd_imagewidget-<?php echo intval(array_pop(explode('-', $this->id)));?>-width" ).ready(function() {
					var value = jQuery ('#widget-iasd_imagewidget-<?php echo intval(array_pop(explode('-', $this->id)));?>-width').val();
					jQuery('#<?php echo $this->get_field_id('width'); ?><?php echo $id; ?>').val(value);
				});
				jQuery( "#widget-iasd_imagewidget-<?php echo intval(array_pop(explode('-', $this->id)));?>-width" ).change(function() {
					var value = jQuery ('#widget-iasd_imagewidget-<?php echo intval(array_pop(explode('-', $this->id)));?>-width').val();
					jQuery('#<?php echo $this->get_field_id('width'); ?><?php echo $id; ?>').val(value);
				});
			});


			jQuery( document ).ready(function() {
				jQuery( "#<?php echo $this->get_field_id('link'); ?><?php echo $id; ?>" ).blur(function() {
					var value = jQuery ('#<?php echo $this->get_field_id('link'); ?><?php echo $id; ?>').val();
					if (value != null && value != '' && value != undefined){
						jQuery('#linktarget_<?php echo $id; ?>').show();
					} else {
						jQuery('#linktarget_<?php echo $id; ?>').hide();
					}
				});
			});


		</script>
<?php } else { 	echo '<p class="no-options-widget">' . __('Clique em "Salvar" para mostrar as opções iniciais', 'iasd') . '</p>';  ?>

	<div class="hidden">
					<input class="widefat" id="<?php echo $this->get_field_id('saved'); ?><?php echo $id; ?>" name="<?php echo $this->get_field_name('saved'); ?>" type="text" value="1" />

		</div>
<?php
		}
	}

    function widgetWidthClass() {
        $widgets = get_option('widget_iasd_ImageWidget');
        return $widgets[$this->number]['width'];
    }

}

