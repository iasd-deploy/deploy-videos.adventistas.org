<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_Assets_test extends IASD_ListaDePosts__Widget_test {

	function test_AssetsAdmin() {
		$this->assertEquals(10, has_action('admin_head', array('IASD_ListaDePosts','EnqueueAdmin')));
		ob_start();
		do_action('admin_head');
		$enqueue = ob_get_contents();
		ob_end_clean();
		$this->assertContains('/css/iasdlistadeposts_admin.css', $enqueue);
		$this->assertContains('/js/iasdlistadeposts_admin.js', $enqueue);
	}
}