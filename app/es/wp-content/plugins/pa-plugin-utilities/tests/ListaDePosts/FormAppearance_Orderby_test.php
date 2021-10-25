<?php

require_once 'FormAppearance__test.php';

class IASD_ListaDePosts_FormAppearance_Orderby_test extends IASD_ListaDePosts_FormAppearance__test {

	function testBasic() {
		$this->addFakePosts();
		$widget = new IASD_ListaDePosts();
		$widget->number = 77;
		$orders = array('ASC', 'DESC');
		$orderBys = array('date', 'title');

		foreach($orderBys as $orderBy) {
			foreach($orders as $order) {
				$basicInstance = IASD_ListaDePosts::DefaultInstance();
				$basicInstance['title'] = $widget->slug();
				$basicInstance['posts_per_page'] = 1;
				$basicInstance['order'] = $order;
				$basicInstance['orderby'] = $orderBy;
				$savedInstance = $widget->update($basicInstance, array());

				$this->assertEquals($order, $basicInstance['order']);
				$this->assertEquals($order, $savedInstance['order']);
				$this->assertEquals($savedInstance['order'], $basicInstance['order']);

				$this->assertEquals($orderBy, $basicInstance['orderby']);
				$this->assertEquals($orderBy, $savedInstance['orderby']);
				$this->assertEquals($savedInstance['orderby'], $basicInstance['orderby']);

				$widget->_setInstance($savedInstance);
				ob_start();
				$widget->formAppearanceOrderBy();
				$savedInstanceFormAppearanceOrder = ob_get_contents();
				ob_end_clean();
				//remove line break
				$savedInstanceFormAppearanceOrder = trim(preg_replace('/\s\s+/', ' ', $savedInstanceFormAppearanceOrder));

				$this->assertContains($widget->get_field_id('order'), $savedInstanceFormAppearanceOrder);
				$this->assertContains($widget->get_field_name('order'), $savedInstanceFormAppearanceOrder);

				$this->assertContains($widget->get_field_id('orderby'), $savedInstanceFormAppearanceOrder);
				$this->assertContains($widget->get_field_name('orderby'), $savedInstanceFormAppearanceOrder);
				
				$expectToBeRendered = '<select class="widefat iasd-widget mandatory iasd-widget-appearance-order" id="'.$widget->get_field_id('order').'" name="'.$widget->get_field_name('order').'">';
				$this->assertContains($expectToBeRendered, $savedInstanceFormAppearanceOrder);

				$expectToBeRendered = '<option value="'.$order.'"';
				$this->assertContains($expectToBeRendered, $savedInstanceFormAppearanceOrder);

				$expectToBeRendered = '<select class="widefat iasd-widget mandatory iasd-widget-appearance-orderby" id="'.$widget->get_field_id('orderby').'" name="'.$widget->get_field_name('orderby').'">';
				$this->assertContains($expectToBeRendered, $savedInstanceFormAppearanceOrder);

				$expectToBeRendered = '<option value="'.$orderBy.'"';
				$this->assertContains($expectToBeRendered, $savedInstanceFormAppearanceOrder);

				$savedInstanceQuery = $widget->query($savedInstance);
				$this->assertEquals($savedInstanceQuery->query_vars['order'], $savedInstance['order']);
				$this->assertEquals($savedInstanceQuery->query_vars['order'], $basicInstance['order']);
				$this->assertEquals($savedInstanceQuery->query_vars['orderby'], $savedInstance['orderby']);
				$this->assertEquals($savedInstanceQuery->query_vars['orderby'], $basicInstance['orderby']);
			}

		}
	}

	function customOrderByOption($base, $name) {
			$base['customOption'] = 'Some Custom Option';

		return $base;
	}

	function testIfPostTypeHasCorrectOrderBy() {
		add_filter('local_post_types-postmeta', array(__CLASS__, 'customOrderByOption'), 10, 2);
		$rules = IASD_ListaDeposts::LoadRules(true);

		$post_types = IASD_ListaDeposts_Ajax::PostTypes();

		foreach($post_types as $post_type_name => $config) {
			$widget = new IASD_ListaDePosts();
			$basicInstance = IASD_ListaDePosts::DefaultInstance();
			$basicInstance['post_type'] = $post_type_name;
			$basicInstance['number'] = $widget->number = $this->getWidgetNumber();

			$widget->_setInstance($basicInstance);
			ob_start();
			$widget->formAppearanceOrderBy();
			$savedInstanceFormAppearanceOrderBy = ob_get_contents();
			ob_end_clean();
			//remove line break
			$post_type = $widget->getCurrentPostType();
			$orderByOptions = ($post_type) ? $post_type['postmeta'] : $widget->OrderByOptions();

			$count = substr_count($savedInstanceFormAppearanceOrderBy, '<option');

			$this->assertContains('Some Custom Option', $savedInstanceFormAppearanceOrderBy);

			$this->assertEquals(count($config['postmeta']) + 2, $count); //+2 for the ORDER options (ASC and DESC)

			
		}
		remove_filter('local_post_types-postmeta', array(__CLASS__, 'customOrderByOption'), 10, 2);
	}
}