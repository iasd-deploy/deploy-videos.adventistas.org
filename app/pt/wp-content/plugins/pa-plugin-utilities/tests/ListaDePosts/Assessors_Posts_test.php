<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_PostAssessors_test extends IASD_ListaDePosts__Widget_test {


	function testLocalPostsAssessors() {
		$this->addFakePosts();
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;
		$instance['title'] = 'lista fake_post_a';
		$instance['post_type'] = 'fake_post_a';
		$widget->_setInstance($instance);

		$query = $widget->query();

		while($query->have_posts()) {
			$query->the_post();

			$this->assertNotNull($widget->getPostTerms(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS));
			$this->assertNotNull($widget->getPostTerm(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS));
			$this->assertNotNull($widget->getPostAuthor());

			$this->assertNotNull($widget->getPostMeta('post_meta_title'));

			$this->assertNotNull($widget->getThumbnail('thumb_40x40'));
			$this->assertNotNull($widget->getThumbnailName());
		}
	}

	function testRemotePostsAssessors() {
		$args = array();
		$args['post_type'] = 'any';
		$args['posts_per_page'] = 1;
		$args['non_cacheable'] = true;

		global $http;
		$http = $this->getMock('WP_Http');

		$fakePost = new WP_Post($this->createNonSavedFakePost());
		$fakePost->taxonomies[IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS] = array();
		$fakePost->taxonomies[IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS]['dsa'] = new stdClass();

		$returnBase = array('request' => '', 'posts' => array($fakePost));
		$returnValue = array('body' => IASD_Query::Encode($returnBase), 
							'response' => array('code' => 200));

		$http->expects($this->once())
			 ->method('post')
			 ->will($this->returnValue($returnValue));

		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['source_id'] = 'outra';
		$instance['source_extra'] = 'http://noticias.adventistas.org/pt/';
		$instance['number'] = $widget->number;
		$instance['title'] = 'lista fake_post_a';
		$instance['post_type'] = 'fake_post_a';
		$widget->_setInstance($instance);

		$query = $widget->query();

		while($query->have_posts()) {
			$query->the_post();

			$this->assertNotNull($widget->getPostTerms(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS));
			$this->assertNotNull($widget->getPostTerm(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS));
			$this->assertNotNull($widget->getPostAuthor());

			$this->assertNotNull($widget->getPostMeta('post_meta_title'));

			$this->assertNotNull($widget->getThumbnail('thumb_40x40'));
			$this->assertNotNull($widget->getThumbnailName());
		}
	}
}