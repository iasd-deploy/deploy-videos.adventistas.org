<?php


class IASD_Users {

	public static function add_custom_user_profile_fields( $user ) {
	?>

	<h3 id='info' ><?php _e('Informações adicionais', 'iasd'); ?></h3>
	
	<table class="form-table">
		<tr>
			<th>
				<label for="address"><?php _e('Sede proprietária', 'iasd'); ?></label>
			</th>
		</tr>
	<td></td>
<td>
<?php 

				$options = get_the_author_meta( 'owner', $user->ID );
				$terms = get_terms( 'xtt-pa-owner', array('hide_empty' => 0) );
				$i = 0;
	 			foreach ( $terms as $term ) {	
				?> 

					<input type="radio" name="owner[]" value="<?php echo $term->term_id; ?>"<?php checked( $term->term_id == $options[0] ); ?> /><?php echo $term->name; ?><br/>
				<?php if ($i % 20 == 0 && $i >= 20 ) { echo '</td><td>';} ?>

				<?php

				$i++;

				}
				?>
</td>

	</table>
<?php }

	public static function save_custom_user_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return FALSE;
		
		update_usermeta( $user_id, 'owner', $_POST['owner'] );
	}

	public static function reminder_set_xtt_owner() {
		$user 	 = wp_get_current_user();
		$options = get_the_author_meta( 'owner', $user->ID );
		if ( empty( $options ) ) { 
			$url = get_admin_url();
			?>

			<div id="update-nag"><?php _e('Você não está com sua sede proprietária configurada. Por favor selecione a sua sede proprietária <a href="'.$url.'profile.php/#info">clicando aqui.</a>', 'iasd'); ?></div>
		<?php
		}
	}

}

add_action( 'show_user_profile',  array( 'IASD_Users', 'add_custom_user_profile_fields' ) );
add_action( 'edit_user_profile',  array( 'IASD_Users', 'add_custom_user_profile_fields' ) );

add_action( 'personal_options_update', array( 'IASD_Users', 'save_custom_user_profile_fields' ) );
add_action( 'edit_user_profile_update', array('IASD_Users', 'save_custom_user_profile_fields') );

add_action( 'admin_notices', array('IASD_Users', 'reminder_set_xtt_owner') );


?>