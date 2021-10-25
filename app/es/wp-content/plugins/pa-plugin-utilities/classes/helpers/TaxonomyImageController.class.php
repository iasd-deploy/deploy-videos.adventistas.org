<?php


class TaxonomyImageController
{
	static function Enqueue()
	{
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
	}

	static function UploaderAdd($taxonomy_slug = false)
	{
		return self::UploaderEdit(false, $taxonomy_slug);
	}

	static function UploaderEdit($term, $taxonomy_slug = false)
	{
		$t_id = ($term) ? $term->term_id : '';
		$category_info = get_option('xtt_cat_info_' . $t_id);
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="category_info_capa_revista_upload_button"><?php _e('Image'); ?></label></th>
			<td>
				<div class="form-field">
					<div id="category_container">
						<?php
						$no_image_message = '<p>'.__('Nenhuma imagem selecionada', 'iasd').'</p>';

						if (!empty($category_info['thumbnail_id'])) {
							echo wp_get_attachment_image($category_info['thumbnail_id'], array(140, 185));
							$add_image_button_label = __('Selecione outra imagem', 'iasd');
						} else {
							$add_image_button_label = __('Selecione uma imagem', 'iasd');
							echo $no_image_message;
						}
						?>
					</div>
					<input type="hidden" id="category_info-thumbnail_id"
						   name="category_info[thumbnail_id]"
						   value="<?php echo $category_info['thumbnail_id'] ? $category_info['thumbnail_id'] : ''; ?>" />
					<input type="button" id="category_info_upload_button"
						   value="<?php echo $add_image_button_label; ?>" />

					<script>
						jQuery(document).ready(function () {

							jQuery('#category_info_upload_button').click(function () {
								tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
								return false;
							});

							window.send_to_editor = function (html, b) {
								var imgUrl = jQuery('img', html).attr('src');
								var imgClass = jQuery('img', html).attr('class');
								var postId = imgClass.substring(imgClass.lastIndexOf('wp-image-') + 9);

								jQuery('#category_info-thumbnail_id').val(postId);
								jQuery('#category_container').html('<img src="' + imgUrl + '" border="0" width"140" />');
								jQuery('#category_info_upload_button').val('<?php _e('Selecione outra imagem', 'iasd'); ?>')
								tb_remove();
							}

							jQuery(document).ajaxSuccess(function (event, xhr) {
								if (!jQuery('wp_error', xhr.responseXML).size()) {
									jQuery('#category_container').html('<?php echo $no_image_message; ?>');
								}
							});
						});
					</script>

				</div>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label
					for="category_info-more_info"><?php _e('Link'); ?> 1</label></th>
			<td>
				<input type="text" name="category_info[more_info_1]" id="category_info-more_info_1"
					   size="25" style="width:60%;"
					   value="<?php echo $category_info['more_info_1'] ? $category_info['more_info_1'] : ''; ?>"><br />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label
					for="category_info-more_info"><?php _e('Link'); ?> 2</label></th>
			<td>
				<input type="text" name="category_info[more_info_2]" id="category_info-more_info_2"
					   size="25" style="width:60%;"
					   value="<?php echo $category_info['more_info_2'] ? $category_info['more_info_2'] : ''; ?>"><br />
			</td>
		</tr>
	<?php
	}

	public static function InfoSave($term_id)
	{
		if (isset($_POST['category_info'])) {
			$t_id = $term_id;
			$category_info = get_option('xtt_cat_info_' . $term_id);
			$cat_keys = array_keys($_POST['category_info']);
			foreach ($cat_keys as $key) {
				if (isset($_POST['category_info'][$key])) {
					$category_info[$key] = $_POST['category_info'][$key];
				}
			}
			//save the option array
			delete_option('xtt_cat_info_' . $term_id);
			add_option('xtt_cat_info_' . $term_id, $category_info, null, 'no');
		}
	}

	public static function AddToTaxonomy($taxonomy) {
		add_action($taxonomy.'_add_form_fields', array('TaxonomyImageController', 'UploaderAdd'));
		add_action($taxonomy.'_edit_form_fields', array('TaxonomyImageController', 'UploaderEdit'));
		add_action('edited_'.$taxonomy, array('TaxonomyImageController', 'InfoSave'));
		add_action('created_'.$taxonomy, array('TaxonomyImageController', 'InfoSave'));
	}
}

add_action('admin_menu', array('TaxonomyImageController', 'Enqueue'));




