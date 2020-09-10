<?php

namespace Wordcamp;

/**
 * Essa classe registra o post type Movies e seus metadados
 */
class Movies {

	use Singleton;

	public $post_type = 'movie';

	protected function init() {
		add_action( 'init', [$this, 'register_post_type'] );

	}

	public function register_post_type() {

		$labels = array(
			'name' => __('movies', 'wordcamp'),
			'singular_name' => __('movie', 'wordcamp'),
			'add_new' => __('Add new movie', 'wordcamp'),
			'add_new_item' => __('Add new movie', 'wordcamp'),
			'edit_item' => __('Edit movie', 'wordcamp'),
			'new_item' => __('New movie', 'wordcamp'),
			'view_item' => __('View movie', 'wordcamp'),
			'search_items' => __('Search movies', 'wordcamp'),
			'not_found' => __('No movie found', 'wordcamp'),
			'not_found_in_trash' => __('No movie found in the trash', 'wordcamp'),
			'menu_name' => __('movies', 'wordcamp'),
			'item_published' => __('movie published.', 'wordcamp'),
			'item_published_privately' => __('movie published privately.', 'wordcamp'),
			'item_reverted_to_draft' => __('movie reverted to draft.', 'wordcamp'),
			'item_scheduled' => __('movie scheduled.', 'wordcamp'),
			'item_updated' => __('movie updated.', 'wordcamp'),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'description' => __('Movies', 'wordcamp'),
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields'), // custom-fields é necessário pra habilitar os post_meta na API
			'rewrite' => array('slug' => 'movies'),
			'public' => true,
			'show_in_menu' => false, // Não vai aparecer no admin!
			'show_in_rest' => true, // Habilita esse post type na API.
			'has_archive' => true,
			'exclude_from_search' => true,
		);

		register_post_type($this->post_type, $args);

		register_post_meta($this->post_type, 'idioma', [
			'show_in_rest' => true,
			'single' => true,
			'auth_callback' => '__return_true',
			'type' => 'string',
			'description' => __('The language spoken in the movie', 'wordcamp')
		]);

		register_post_meta($this->post_type, 'ano', [
			'show_in_rest' => array(
				'schema' => array( 
					'type' => 'number',
					'minimum' => 1900,
				),
			),
			'single' => true,
			'auth_callback' => '__return_true',
			'description' => __('The year the movie was released', 'wordcamp')
		]);

		register_post_meta($this->post_type, 'formato', [
			'show_in_rest' => array(
				'schema' => array(
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
			'single' => true,
			'auth_callback' => '__return_true',
			'type' => 'string', 
			'description' => __('The format of the media', 'wordcamp')
		]);

		register_post_meta($this->post_type, 'ficha', [
			'show_in_rest' => [
				'schema' => [
					'type' => 'object',
					'properties' => [
						'pais' => [
							'description' => 'País de produção (pode ser mais de um)', // se você vai usar só em português, não precisa internacionalizar.
							'type' => 'array', // vai ser um array de países
							'items' => [
								'type' => 'string' // cada item dentro do array vai ser uma string
							]
						],
						'diretor' => [
							'description' => 'Diretor (pode ser mais de um)',
							'type' => 'array',
							'items' => [
								'type' => 'string'
							]
						],
					]
				]

			],
			'single' => true,
			'auth_callback' => '__return_true',
			'type' => 'object',
			'description' => 'Países e diretores'
		]);

		register_post_meta($this->post_type, 'preco', [
			'show_in_rest' => true,
			'single' => true,
			'auth_callback' => [ $this, 'permission_check' ], // callback pra checar a permissão pra editar esse metadado
			'sanitize_callback' => [ $this, 'sanitize_preco' ], // callback pra filtrar o valor do metadado antes de inserir
			'type' => 'string',
			'description' => __('Price', 'wordcamp')
		]);

	}

	/**
	 * Sanitizes the preco metadata value
	 * @param  mixed $value The value to be validated
	 * @return string
	 */
	public function sanitize_preco($value) {

		if ( strpos( $value, 'R$ ' ) !== 0 ) {
			$value = 'R$ ' . $value;
		}

		return $value;

	}

	/**
	 * Só admins podem editar o preco
	 */
	public function permission_check() {
		return current_user_can('manage_options'); // uma permissão que só admins tem
	}

}
