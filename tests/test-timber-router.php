<?php

class TimberTestRouter extends WP_UnitTestCase {

	function testThemeRoute(){
		$template = Timber::load_template('single.php');
		$this->assertTrue($template);
	}

	function testThemeRouteDoesntExist(){
		$template = Timber::load_template('singlefoo.php');
		$this->assertFalse($template);
	}

	function testFullPathRoute(){
		$hello = WP_CONTENT_DIR.'/plugins/hello.php';
		$template = Timber::load_template($hello);
		$this->assertTrue($template);
	}

	function testFullPathRouteDoesntExist(){
		$hello = WP_CONTENT_DIR.'/plugins/hello-foo.php';
		$template = Timber::load_template($hello);
		$this->assertFalse($template);
	}

	function testRouterClass(){
		$this->assertTrue(class_exists('AltoRouter'));
	}

	function testAppliedRoute(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		Timber::add_route('foo', function() use ($phpunit) {
			global $matches;
			$phpunit->assertTrue(true);
			$matches[] = true;
		});
		$this->go_to(home_url('foo'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteAgainstPostName(){
		$post_name = 'jared';
		$post = $this->factory->post->create(array('post_title' => 'Jared', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		Timber::add_route('randomthing/'.$post_name, function() use ($phpunit) {
			global $matches;
			$phpunit->assertTrue(true);
			$matches[] = true;
		});
		$this->go_to(home_url('/randomthing/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testFailedRoute(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		Timber::add_route('foo', function() use ($phpunit){
			$phpunit->assertTrue(false);
			$matches[] = true;
		});
		$this->go_to(home_url('bar'));
		$this->matchRoutes();
		$this->assertEquals(0, count($matches));
	}

	function testRouteWithVariable() {
		$post_name = 'ziggy';
		$post = $this->factory->post->create(array('post_title' => 'Ziggy', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		Timber::add_route('mything/:slug', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ('ziggy' == $params['slug']) {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/mything/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteWithAltoVariable() {
		$post_name = 'ziggy';
		$post = $this->factory->post->create(array('post_title' => 'Ziggy', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		Timber::add_route('mything/[*:slug]', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ('ziggy' == $params['slug']) {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/mything/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteWithMultiArguments() {
		$phpunit = $this;
		Timber::add_route('artist/[:artist]/song/[:song]', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ($params['artist'] == 'smashing-pumpkins') {
				$matches[] = true;
			}
			if ($params['song'] == 'mayonaise') {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/artist/smashing-pumpkins/song/mayonaise'));
		$this->matchRoutes();
		global $matches;
		$this->assertEquals(2, count($matches));
	}

	function testRouteWithMultiArgumentsOldStyle() {
		$phpunit = $this;
		global $matches;
		Timber::add_route('studio/:studio/movie/:movie', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ($params['studio'] == 'universal') {
				$matches[] = true;
			}
			if ($params['movie'] == 'brazil') {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/studio/universal/movie/brazil/'));
		$this->matchRoutes();
		$this->assertEquals(2, count($matches));
	}


	function matchRoutes() {
        global $upstatement_routes;
        $upstatement_routes->init();
    }
}
