<?php

namespace Wordcamp;

class Filmes_Api_Endpoint extends \WP_REST_Controller {

	public function __construct() {
		add_action('rest_api_init', array($this, 'register_routes'));
	}

	public function register_routes() {
		register_rest_route('wordcamp/v1', 'filmes', array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array($this, 'get_items'),
				'permission_callback' => array($this, 'get_items_permissions_check'),
				'args'                => array(
					'page' => array(
						'description' => 'Página de resultados para retornar',
						'default'     => 1,
						'type'        => 'integer',
					),
					'formato' => array(
						'description' => 'Filtrar resultados por esse formato',
						'type' => 'string',
						'enum' => array(
							'VHS',
							'DVD',
							'LaserDisc',
							'16mm',
							'35mm',
						),
					),
				),
			),
			'schema' => [$this, 'get_schema'],
		));
	}

	public function get_items_permissions_check( $request ) {
		return true; // Qualquer pessoa pode ler
	}

	public function get_items( $request ) {

		$args = array(
			'post_type' => 'movie',
			'paged' => $request['page']
		);
		
		// Se receber formato na query, filtra os posts por esse metadado
		if ( isset( $request['formato'] ) ) {
			$args['meta_query'] = array(
				array(
					'key' => 'formato',
					'value' => $request['formato'],
				)
			);
		}

		// faz a query
		$posts = new \WP_Query( $args );

		// O array que vai ser retornado
		$results = array();

		// O Loop que conhecemos
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$results[] = array(
					'titulo'    => get_the_title(),
					'sinopse' => get_the_content(),
					'ano'     => get_post_meta( get_the_ID(), 'ano', true ),
					'formato' => get_post_meta( get_the_ID(), 'formato', true ),
				);
			}
		}

		$total_collections  = $posts->found_posts;
		$max_pages = ceil($total_collections / (int) $posts->query_vars['posts_per_page']);

		$rest_response = new \WP_REST_Response($results, 200);

		// Esses cabeçalhos permitem ao cliente montar uma paginação.
		$rest_response->header('X-WP-Total', (int) $total_collections);
		$rest_response->header('X-WP-TotalPages', (int) $max_pages);

		return $rest_response;

	}

	public function get_schema() {
		$schema = [
			'$schema'  => 'http://json-schema.org/draft-04/schema#',
			'title' => 'filmes',
			'type' => 'object',
			'properties' => array(
				'titulo' => array(
					'type' => 'string',
					'description' => 'O título do filme'
				),
				'sinopse'  => array(
					'type' => 'string',
					'description' => 'A sinopse do filme'
				),
				'ano'  => array(
					'type' => 'number',
					'description' => 'O ano de lançamento do filme'
				),
				'formato'  => array(
					'type' => 'string',
					'description' => 'O formato da mídia do filme',
					'enum' => array(
						'VHS',
						'DVD',
						'LaserDisc',
						'16mm',
						'35mm',
					),
				),
			),
		];

		return $schema;

	}

}