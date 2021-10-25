<?php

require_once 'FormAppearance__test.php';

class IASD_ListaDePosts_FormAppearance_PostsPerPage_test extends IASD_ListaDePosts_FormAppearance__test{

	function test_formAppearancePostsPerPage() {
		$this->addFakePosts();
		$widget = new IASD_ListaDePosts();
		$counts = array(1, 3, 5);

		foreach($counts as $count) {
			$widget->number = $this->getWidgetNumber();
			$basicInstance = $widget::DefaultInstance();
			$basicInstance['title'] = $widget->slug();
			$basicInstance['posts_per_page'] = $count;
			$basicInstance['post_type'] = 'fake_post_a';
			$savedInstance = $widget->update($basicInstance, array());
			$widget->_setInstance($savedInstance);

			ob_start();
			$widget->formAppearancePostsPerPage();
			$savedInstanceFormAppearanceCount = ob_get_contents();
			ob_end_clean();
			//remove line break
			$savedInstanceFormAppearanceCount = trim(preg_replace('/\s\s+/', ' ', $savedInstanceFormAppearanceCount));

			$this->assertContains('value="' . $savedInstance['posts_per_page'] . '"', $savedInstanceFormAppearanceCount);
			$this->assertContains($widget->get_field_id('posts_per_page'), $savedInstanceFormAppearanceCount);
			$this->assertContains($widget->get_field_name('posts_per_page'), $savedInstanceFormAppearanceCount);
			$expectToBeRendered = '<input class="widefat iasd-widget mandatory iasd-widget-appearance-posts_per_page" id="'.$widget->get_field_id('posts_per_page').'" name="'.$widget->get_field_name('posts_per_page').'" type="text" value="' . $savedInstance['posts_per_page'] . '" />';
			$this->assertContains($expectToBeRendered, $savedInstanceFormAppearanceCount);

			$savedInstanceQuery = $widget->query($savedInstance);
			$query_args = IASD_ListaDePosts::BuildQuery($widget->getInstance());


			$this->assertTrue($savedInstanceQuery->have_posts());
			$this->assertEquals($count, $savedInstanceQuery->post_count);
			wp_reset_query();
		}
	}
}