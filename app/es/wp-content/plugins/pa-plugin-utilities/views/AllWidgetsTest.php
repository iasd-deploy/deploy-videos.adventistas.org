<?php

get_header();

global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates;

$widget_list = $testable_widget_list = array();
/*$testable_widget_list[] = 'singlegallerywidget';
$testable_widget_list[] = 'gallerytaxonomydisplaywidget';
$testable_widget_list[] = 'videogallerytaxonomydisplaywidget';
$testable_widget_list[] = 'eventoswidget';
$testable_widget_list[] = 'videosrelacionadoswidget';
$testable_widget_list[] = 'materiaistop5widget';
$testable_widget_list[] = 'colunasartigoswidget';
$testable_widget_list[] = 'noticiasultimaswidget';
$testable_widget_list[] = 'noticiasmaislidaswidget';
$testable_widget_list[] = 'noticiasdestaqueswidget';
$testable_widget_list[] = 'noticiasregioessemexcerptwidget';
$testable_widget_list[] = 'noticiassedessemexcerptwidget';
//$testable_widget_list[] = 'noticias4itemswidget';
$testable_widget_list[] = 'servicossliderwidget';
$testable_widget_list[] = 'encontreigrejawidget';
$testable_widget_list[] = 'linksnovotempowidget';
$testable_widget_list[] = 'facebookwidget';*/
$testable_widget_list[] = 'widget_iasd_taglist';
/*$testable_widget_list[] = 'pordosolwidget';
$testable_widget_list[] = 'wp_nav_menu_widget_iasd';
$testable_widget_list[] = 'asn_widget_recent_posts';*/

if(isset($_GET['filter'])) {
	$filter = $_GET['filter'];
	foreach($testable_widget_list as $k => $testable_widget) {
		if(stripos($testable_widget, $filter) === false)
			$testable_widget_list[$k] = false;
	}
}
array_filter($testable_widget_list);
foreach($wp_registered_widgets as $wp_registered_widget) {
	if(in_array(strtolower($wp_registered_widget['classname']), $testable_widget_list))
		$widget_list[$wp_registered_widget['classname']] = $wp_registered_widget['callback'];
//	else
//		var_dump(strtolower($wp_registered_widget['classname']));
}


/**
	Modelo de código a ser aplicado em widgets para serem compativeis com a ferramenta.

	function demoWidget() {
		global $wpdb;

		$demo_query = 'SELECT pst.ID, count(att.ID) as atts FROM ' . $wpdb->posts . ' AS pst LEFT JOIN ' . $wpdb->posts . ' AS att ON pst.ID = att.post_parent WHERE pst.post_type = "' . PAImageGallery::$post_type_name . '" GROUP BY pst.ID ORDER BY atts DESC LIMIT 1';
		$post_id = $wpdb->get_var($demo_query);

		if(!$post_id) {
			echo '<span class="btn btn-warning">Sem dados para teste</span>';
		} else {
			$this->widget(array(), array('post_id' => $post_id));
		}
	}

	
**/

$container = (isset($_GET['container'])) ? $_GET['container'] : 'span4';

?>

<section class="widget-test-page">
	<div class="container">
<?php

foreach($widget_list as $widget_class => $widget_callback) {
	$widgetObject = @$widget_callback[0];
	if(!$widgetObject)
		continue;
?>
		<div class="row">
			<div class="sidebar <?php echo $container; ?>">
<?php
	echo '<span class="btn btn-inverse">',$widgetObject->name,'</span><hr />';

	if(!method_exists($widgetObject, 'demoWidget')) {
		echo '<span class="btn btn-danger">Não Compativel</span>';
	} else {
		call_user_func(array($widgetObject, 'demoWidget'));
	}

?>
				<hr class="" />
			</div>
		</div>
<?php

}

	/**
	Hardcoded Tests
	*/
?>
		<div class="row">
			<div class="sidebar <?php echo $container; ?>">
<?php
	echo '<span class="btn btn-inverse">Botão Voltar</span><hr />';

	ReferralPortalWidget::Init('http://www.google.com.br/');

?>
				<hr class="" />
			</div>
		</div>
	</div>
</section>

<?php


get_footer();
