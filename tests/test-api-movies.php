<?php

namespace Wordcamp\Tests;

class ApiMovies extends Wordcamp_UnitApiTestCase {

	function test_movies() {

		// Cria um novo request para o nosso endpoint
		$request = new \WP_REST_Request('POST', '/wp/v2/movies');

		// Os parâmetros que vamos mandar na requisição.
		$request_query = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'ano' => 1999
			]
		];

		// Adiciona os parâmetros a requisição.
		$request->set_query_params($request_query);

		// Faz a requisição.
		$response = $this->server->dispatch($request);

		// O objeto response tem vários métodos. Traz os cabeçalhos e tudo que veio na resposta
		// Usamos ->get_data() pra pegar o corpo da resposta.
		$data = $response->get_data();

		// O status da requisição, quando se cria um novo item, tem que ser 201.
		$this->assertEquals(201, $response->get_status());

		// Verificamos que o post retornado pela requisição tem as informações que mandamos.
		$this->assertEquals( 'Matrix', $data['title']['raw'] );
		$this->assertEquals( 1999, $data['meta']['ano'] );
		$this->assertEquals( 'movie', $data['type'] );

	}

	function test_ano_validation() {

		$request = new \WP_REST_Request('POST', '/wp/v2/movies');

		$request_query = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'ano' => 'mil novecentos e noventa e nove'
			]
		];

		$request->set_query_params($request_query);

		$response = $this->server->dispatch($request);

		$this->assertEquals(400, $response->get_status()); // 400 significa erro!

	}

	function test_ano_validation_minimum_value() {

		$request = new \WP_REST_Request('POST', '/wp/v2/movies');

		$request_query = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'ano' => 1888 // menos do que o mínimo declarado no schema
			]
		];

		$request->set_query_params($request_query);

		$response = $this->server->dispatch($request);

		$this->assertEquals(400, $response->get_status()); // 400 significa erro!

	}

	function test_preco_sanitization() {

		$request = new \WP_REST_Request('POST', '/wp/v2/movies');

		$request_query = [
			'title' => 'Matrix',
			'content' => 'A sinopse do matrix...',
			'meta' => [
				'preco' => '1000'
			]
		];

		$request->set_query_params($request_query);

		$response = $this->server->dispatch($request);

		$data = $response->get_data();

		// Verificamos que o valor do preco foi alterado.
		$this->assertEquals( 'R$ 1000', $data['meta']['preco'] );

	}

	// Desafio: Escreva um teste que prove que só admins podem editar o metadado "preco"


}