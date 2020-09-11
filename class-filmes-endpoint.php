<?php

namespace Wordcamp;

/**
 * Essa classe cria um novo endpoint para a API do WordPress.__resizable_base__
 * 
 * Documentação de como fazer isso: https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
 */
class Filmes_Api_Endpoint {

	/**
	 * Registramos um hook em rest_api_init para registrar as rotas que a gente quer na nossa API
	 * 
	 * Nota: isso aqui está numa classe pra fins de organização, mas poderia ser um hook e uma função normal.
	 */
	public function __construct() {
		add_action('rest_api_init', array($this, 'register_routes'));
	}

	/**
	 * Vamos registrar nos novos endpoints
	 */
	public function register_routes() {
		/**
		 * register_rest_route registra o endpoint
		 * 
		 * O primeiro argumento é o namespace. Podem ter vários endpoints dentro do seu namespace. Os 
		 * endpoints nativos do WP ficam em wp/v2. Aqui você cria o que quiser. É recomendado ter um espaço pra versão,
		 * pra você poder mudar no futuro se for preciso.
		 * 
		 * O segundo argumento é o endpoint que você vai registrar. No fim a URL vai ficar wp-json/$dominio/$endpoint 
		 * O $endpoint pode ter pedaços variáveis. Por exemplo, pra editar um filme poderia ser 'filems/(?P<id>\d+)'. Isso
		 * faria com que você tivesse um $request['id'] disponível nos seus callbacks. 
		 * 
		 * O último parâmetro é um array de arrays. Cada enpoint pode ter vários métodos diferentes. Por exmeplo,
		 * um mesmo endpoint pode fazer uma coisa se você acessá-lo com POST (criar algo) e outra coisa se acessar com GET (ler algo)
		 */
		register_rest_route('wordcamp/v1', 'filmes', array(
			array(
				'methods'             => \WP_REST_Server::READABLE, // o método que esse endpoint aceita (GET, POST, PATCH, etc...) esse caso READABLE é GET
				'callback'            => array($this, 'get_items'), // a função q vai ser chamada quando esse endpoint for acessado
				'permission_callback' => array($this, 'get_items_permissions_check'), // a função que vai ser chamada pra determinar se o usuário tem permissão pra acessar esse endpoint
				'args'                => array( // a declaração dos argumentos que esse endpoint aceita
					'page' => array( // cada argumento descrito em JSON Schema
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
			// aqui poderia ter outro array com a declaração do método POST por exemplo
			'schema' => [$this, 'get_schema'], // isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
		));
	}

	public function get_items_permissions_check( $request ) {
		return true; // Qualquer pessoa pode ler. Aqui você pode usar o current_user_can() normalmente.
	}

	public function get_items( $request ) {

		/**
		 * Argumentos base para a query
		 * $request['page'] traz o valor do argumento aceito pelo endpoint. Como ele tem um valor padrão declarado lá no json schema, eu sei que sempre vai ter alguma coisa.
		 */
		$args = array(
			'post_type' => 'movie',
			'paged' => $request['page']
		);
		
		// Se receber formato na query, filtra os posts por esse metadado
		if ( isset( $request['formato'] ) ) {
			// Monta a meta_query pra ser usado ali embaixo
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
				// Montamos um array simplificado do post, que é exatamente no formato que declaramos no schema.
				$results[] = array(
					'titulo'    => get_the_title(),
					'sinopse' => get_the_content(),
					'ano'     => get_post_meta( get_the_ID(), 'ano', true ),
					'formato' => get_post_meta( get_the_ID(), 'formato', true ),
				);
			}
		}

		// calculamos as informações pra paginação
		// Se tem mais de 10 posts (que é o numero de posts que o WP traz por padrão em cada página)
		// o found_posts vai trazer a informação de quantos posts existem no total.
		$total_collections  = $posts->found_posts;
		// calculamos quantas páginas de itens temos
		$max_pages = ceil($total_collections / (int) $posts->query_vars['posts_per_page']);

		// setamos o código de resposta pra 200, que significa sucesso
		$rest_response = new \WP_REST_Response($results, 200);

		// Esses cabeçalhos permitem ao cliente montar uma paginação.
		$rest_response->header('X-WP-Total', (int) $total_collections);
		$rest_response->header('X-WP-TotalPages', (int) $max_pages);

		return $rest_response;

	}

	/**
	 * O schema do nosso endpoint. Um post simplificado.
	 */
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