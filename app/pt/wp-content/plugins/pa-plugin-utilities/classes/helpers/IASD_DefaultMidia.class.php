<?php


try {

	if (isset($_POST['default_audio'])) {

		$defaultAudioOption = $_POST["default_audio"]; 

		if ($defaultAudioOption != null && $defaultAudioOption != undefined ){
			update_option('xtt_default_radio', $defaultAudioOption); 
		}
		
	} 
	
	if (isset($_POST['default_video'])) {

		$defaultVideoOption = $_POST["default_video"]; 

		if ($defaultVideoOption != null && $defaultVideoOption != undefined ){
			update_option('xtt_default_tv', $defaultVideoOption); 
		}
		
	} 

} catch (Exception $e) {

}
		

if (is_admin()){
	add_action('admin_menu', array('DefaultMidia', 'DefaultMidiaCreateAdminMenu'), 100);
	//add_action('admin_init', array('IASD_Languages', 'registerSettings'));
}


class DefaultMidia {

	public static function DefaultMidiaCreateAdminMenu(){

	$is_super_admin = is_super_admin( get_current_user_id() );
	
		if ($is_super_admin){
			add_submenu_page( 'pa-adventistas', __('Novo Tempo', 'iasd'), __('Novo Tempo', 'iasd'), 'edit_pages', 'pa-adv-pt-novaera', array(__CLASS__, 'renderDefaultMidiasAdminControlPage'));
		
		}
	}	

public static function renderDefaultMidiasAdminControlPage(){
?>


    <h2><?php _e('Fontes de conteúdo padrão', 'iasd'); ?></h2>
   	
   	<?php
   		$defaultAudioOption = $_POST["default_audio"]; 
   		$radioUpdated = $_POST["updated"]; 

   		$defaultAudioOption = $_POST["default_video"]; 

   		$msg = __('Opções salvas com sucesso.', 'iasd');

		if ( isset($_POST['updated']) ){ 	
				echo '<div class="updated below-h2" id="sync_message" style="padding: 20px; margin-top: 10px; margin-bottom: 10px;">'.$msg.'</div>';
		}		

	?>

    <!-- Radio Settings -->


    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

    <h3><?php _e('Rádio Novo Tempo', 'iasd'); ?></h3>

	<ul>	
	     <?php 

	     $optionRadio = get_option('xtt_default_radio');

	     ?>

	     <li>
	     	<label>
	     		<input type="radio" name="default_audio" value="portugues" 
	     		<?php if($optionRadio == 'portugues'){echo 'checked';} ?>> 
	     		<?php _e('Brasil (Principal)', 'iasd') ?>
	 		</label>
	 	 </li>
	 	 <li>
	     	<label>
	     		<input type="radio" name="default_audio" value="espanhol" 
	     		<?php if($optionRadio == 'espanhol'){ echo 'checked';} ?>> 
	     		<?php _e('Espanhol (Principal)', 'iasd') ?>
	 		</label>
	 	 </li>
	 	  <li>
	     	<label>
	     		<input type="radio" name="default_audio" value="bolivia" 
	     		<?php if($optionRadio == 'bolivia'){echo 'checked';} ?>> 
	     		<?php _e('Bolivia', 'iasd') ?>
	 		</label>
	 	 </li>
	 	  <li>
	     	<label>
	     		<input type="radio" name="default_audio" value="equador" 
	     		<?php if($optionRadio == 'equador'){ echo 'checked';} ?>> 
	     		<?php _e('Equador', 'iasd') ?>
	 		</label>
	 	 </li>
	 	  <li>
	     	<label>
	     		<input type="radio" name="default_audio" value="uruguai" 
	     		<?php if($optionRadio == 'uruguai'){ echo 'checked';} ?>> 
	     		<?php _e('Uruguai', 'iasd') ?>
	 		</label>
	 	 </li>

	</ul>
	
	<br/>

    <!-- Video Settings -->

    <h3><?php _e('TV Novo Tempo', 'iasd'); ?></h3>

	<ul>	
	     <?php 

	     $otionVideo = get_option('xtt_default_tv');

	     ?>

	     <li>
	     	<label>
	     		<input type="radio" name="default_video" value="portugues" 
	     		<?php if($otionVideo == 'portugues'){ echo 'checked';} ?>> 
	     		<?php _e('Brasil (Principal)', 'iasd') ?>
	 		</label>
	 	 </li>
	 	  <li>
	     	<label>
	     		<input type="radio" name="default_video" value="espanhol" 
	     		<?php if($otionVideo == 'espanhol'){ echo 'checked';} ?>> 
	     		<?php _e('Espanhol (Principal)', 'iasd') ?>
	 		</label>
	 	 </li>
	 	  <li>
	     	<label>
	     		<input type="radio" name="default_video" value="chile" 
	     		<?php if($otionVideo == 'chile'){ echo 'checked';} ?>> 
	     		<?php _e('Chile', 'iasd') ?>
	 		</label>
	 	 </li>

	</ul>

	<input type="hidden" name="updated" value="sim" />

    <?php submit_button(); ?>

</form>

<?php
	}
	//add_action( 'admin_menu', 'DefaultMidia' );
}