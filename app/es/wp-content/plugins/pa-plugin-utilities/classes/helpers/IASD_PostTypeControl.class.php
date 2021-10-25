<?php
try {
	
	if( is_array($_POST['restricted_menus'])  ) {

		$restricted_list 	= $_POST["restricted_menus"]; 

		if ($restricted_list != null && $restricted_list != undefined ){


			$json_restricted_list = json_encode($restricted_list);	
			$update_worked = false;

			if (update_option('xtt_restrict_post_types', $json_restricted_list)) {
				$update_worked = true;
				
			} else {
				if (add_option('xtt_restrict_post_types', $json_restricted_list)){
					$update_worked = true;
				}
			}		
		}
	} else if ( isset($_POST['atualizar'])) {

			$empty_array = array();
			$json_restricted_list = json_encode($empty_array);	
			$update_worked = false;

			if (update_option('xtt_restrict_post_types', $json_restricted_list)) {
				$update_worked = true;
				
			} else {
				if (add_option('xtt_restrict_post_types', $json_restricted_list)){
					$update_worked = true;
				}
			}		
	}
	
} catch (Exception $e) {

}
		

$restricted;

if (is_admin()){
	add_action( 'admin_menu', array('PostTypeControl', 'PostTypeAdminControl' ), 100);
	add_action('admin_menu', array('PostTypeControl', 'CreateAdminMenu'), 100);
	//add_action('admin_init', array('IASD_Languages', 'registerSettings'));
}




class PostTypeControl {

	public static function CreateAdminMenu(){

	$is_super_admin = is_super_admin( get_current_user_id() );
	
		if ($is_super_admin){
			add_submenu_page( 'pa-adventistas', __('Post Type Control', 'iasd'), __('Post Type Control', 'iasd'), 'edit_pages', 'pa-adv-pt-control', array(__CLASS__, 'renderPostTypeAdminControlPage'));
		
		}
	}	

	public static function PostTypeAdminControl() {
	 	$is_super_admin = is_super_admin( get_current_user_id() );

	 	// Don't restrict Administrator users.
		if ( $is_super_admin )
			return;
	 
	 	$json_list = get_option('xtt_restrict_post_types');
	 	$restricted = json_decode($json_list);
	 	
	 	global $menu;

	 	if (isset($json_list)) {
	 		if (!empty($json_list)) {
	 			foreach ( $menu as $item => $data ) {			 
					if ( ! isset( $data[0] ) ) {
						continue; // Move along if the current $item doesn't have a slug.
					} elseif ( in_array( $data[0], $restricted ) ) {
						unset( $menu[$item] ); // Remove the current $item from the $menu.
					}
				}
			}
	 	}
	}

	public static function renderPostTypeAdminControlPage(){
?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php _e('Post Types a serem restritos', 'iasd'); ?></h2>

   	<?php
   		$restricted_list = $_POST["restricted_menus"]; 
   		$atualizar = $_POST["atualizar"]; 


		if ($restricted_list != null && $restricted_list != undefined || isset($_POST['atualizar']) ){ 	
				echo '<div class="updated below-h2" id="sync_message" style="padding: 20px; margin-top: 10px; margin-bottom: 10px;">Opções atualizadas com sucesso.</div>';
		}
				
	?>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

	    <ul>	
		     <?php 
		     $pt_list = get_post_types(); 
		     $json_list = get_option('xtt_restrict_post_types');

		     $restricted = json_decode($json_list); 

		     foreach ( $pt_list as $pt_item ) {
		     	$pt_object = get_post_type_object( $pt_item );

		     	if ($pt_object->label != 'Navigation Menu Items' ) {
		     ?>

		     <li>
		     	<label>
		     		<input type="checkbox" name="restricted_menus[]" value="<?php 
		     		echo $pt_object->label;?>" <?php if(in_array($pt_object->label, $restricted)){
	 				 echo 'checked';} ?>> 
		     		<?php echo $pt_object->labels->name; ?>
	     		</label>
		 	 </li>

		     <?php }}; ?>

	     </ul>

	             	  <input type="hidden" name="atualizar" value="sim" />

     <?php submit_button(); ?>

    </form>
</div>
<?php
	}
	//add_action( 'admin_menu', 'PostTypeControl' );
}