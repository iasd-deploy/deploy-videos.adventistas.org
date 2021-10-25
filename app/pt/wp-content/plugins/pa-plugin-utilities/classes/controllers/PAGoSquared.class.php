<?php

add_action('wp_footer', array('PAGoSquared', 'Render'));

class PAGoSquared {
	static function Render() {
		echo "
<script>
	!function(g,s,q,r,d){r=g[r]=g[r]||function(){(r.q=r.q||[]).push(
	arguments)};d=s.createElement(q);q=s.getElementsByTagName(q)[0];
	d.src='//d1l6p2sc9645hc.cloudfront.net/tracker.js';q.parentNode.
	insertBefore(d,q)}(window,document,'script','_gs');

	_gs('GSN-569730-R');
</script>\n";
	}
}

