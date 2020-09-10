<?php

namespace Wordcamp\Tests;

/**
 * Basic test case for api calls
 * @author medialab
 *
 */
class Wordcamp_UnitApiTestCase extends Wordcamp_UnitTestCase {
	/**
	 * Test REST Server
	 * @var \WP_REST_Server
	 */
	protected $server;
	
	/**
	 * Aqui a gente inicializa tudo o que precisa para poder fazer chamadas para a API
	 */
	public function setUp(){
		parent::setUp();

		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;

		do_action( 'rest_api_init' );
	}
}