<?php

namespace Wordcamp\Tests;

class ApiFilmes extends Wordcamp_UnitApiTestCase {

	/**
	 * A cada teste o WordPress é zerado e volta ao seu estado de logo após a instalação.
	 * 
	 * O método SetUp vai rodar toda vez antes de cada teste (método que começa com test_).
	 * 
	 * Vamos criar uns posts aqui pra não precisar ficar criando de novo a cada teste.
	 *
	 * @return void
	 */
	function setUp() {
		parent::setUp();

		// Vamos definir alguns valores pros metadados e criar todos os posts de uma vez:
		$metas = array(
			array(
				'ano' => 1987,
				'formato' => 'VHS',
			),
			array(
				'ano' => 1999,
				'formato' => 'DVD',
			),
			array(
				'ano' => 1976,
				'formato' => 'DVD',
			),
			array(
				'ano' => 2001,
				'formato' => 'LaserDisc',
			),
			array(
				'ano' => 2017,
				'formato' => 'DVD',
			),
			array(
				'ano' => 1979,
				'formato' => '16mm',
			),
		);

		$i = 1;
		foreach ( $metas as $meta ) {
			$request = new \WP_REST_Request('POST', '/wp/v2/movie');

			$request_body = [
				'title' => 'Filme ' . $i,
				'content' => 'A sinopse do filme ' . $i,
				'meta' => [
					'ano'     => $meta['ano'],
					'formato' => $meta['formato'],
				],
				'status' => 'publish' // importante setar o status, se não ele é criado como rascunho e não vai retornar nas buscas.
			];

			$request->set_query_params($request_body);

			$this->server->dispatch($request);

			$i++;
		}

	}
	
	
	function test_get_all() {

		$request = new \WP_REST_Request('GET', '/wordcamp/v1/filmes');

		$response = $this->server->dispatch($request);

		$data = $response->get_data();
		$headers = $response->get_headers();

		$this->assertEquals(200, $response->get_status()); // retornou com sucesso!
		$this->assertCount( 6, $data ); // Retornou os 6 posts criados no setUp

		// Verifica as informações de paginação
		$this->assertEquals( 6, $headers['X-WP-Total'] );
		$this->assertEquals( 1, $headers['X-WP-TotalPages'] );

	}

	function test_filter() {

		$request = new \WP_REST_Request('GET', '/wordcamp/v1/filmes');

		$request_query = [
			'formato' => 'DVD',
		];

		$request->set_query_params($request_query);

		$response = $this->server->dispatch($request);

		$data = $response->get_data();
		$headers = $response->get_headers();

		$this->assertEquals(200, $response->get_status()); // retornou com sucesso!
		$this->assertCount( 3, $data ); // Retornou os 3 posts criados no setUp que tem formato DVD

		$this->assertEquals( 3, $headers['X-WP-Total'] );
		$this->assertEquals( 1, $headers['X-WP-TotalPages'] );

		// Verifica que todos os posts que vieram tem o formato que a gente filtrou.
		foreach ( $data as $post ) {
			$this->assertEquals( 'DVD', $post['formato'] );
		}

	}

}