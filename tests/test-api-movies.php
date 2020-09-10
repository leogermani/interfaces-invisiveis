<?php

namespace Wordcamp\Tests;

class ApiMovies extends Wordcamp_UnitApiTestCase {

	function test_movies() {

		$request = new \WP_REST_Request('POST', '/wp/v2/movie');

		$request_body = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'ano' => 1999
			]
		];

		$request->set_query_params($request_body);

		$response = $this->server->dispatch($request);

		$data = $response->get_data();

		$this->assertEquals(201, $response->get_status());

		$this->assertEquals( 'Matrix', $data['title']['raw'] );
		$this->assertEquals( 1999, $data['meta']['ano'] );
		$this->assertEquals( 'movie', $data['type'] );

	}

	function test_ano_validation() {

		$request = new \WP_REST_Request('POST', '/wp/v2/movie');

		$request_body = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'ano' => 'mil novecentos e noventa e nove'
			]
		];

		$request->set_query_params($request_body);

		$response = $this->server->dispatch($request);

		$this->assertEquals(400, $response->get_status()); // 400 significa erro!

	}

	function test_ano_validation_minimum_value() {

		$request = new \WP_REST_Request('POST', '/wp/v2/movie');

		$request_body = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'ano' => 1888 // menos do que o mínimo declarado no schema
			]
		];

		$request->set_query_params($request_body);

		$response = $this->server->dispatch($request);

		$this->assertEquals(400, $response->get_status()); // 400 significa erro!

	}

	function test_preco_sanitization() {

		$request = new \WP_REST_Request('POST', '/wp/v2/movie');

		$request_body = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'preco' => '1000'
			]
		];

		$request->set_query_params($request_body);

		$response = $this->server->dispatch($request);

		$data = $response->get_data();

		$this->assertEquals( 'R$ 1000', $data['meta']['preco'] );

	}


}