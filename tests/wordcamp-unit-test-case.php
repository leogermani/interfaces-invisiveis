<?php

namespace Wordcamp\Tests;

use Wordcamp\Movies;

/**
 * Classe base para nossos testes.
 */
class Wordcamp_UnitTestCase extends \WP_UnitTestCase {
	protected $user_id;

	public function setUp(){
		parent::setUp();
		
		// Antes de cada teste, cria um novo admin e faz login com ele.
		$new_admin_user = $this->factory()->user->create(array( 'role' => 'administrator' ));
		wp_set_current_user($new_admin_user);
		$this->user_id = $new_admin_user;
		
		// Isso aqui contorna um bug no esquema de testes do WordPress que não persiste os metadados registrados. 
		// Então temos que re-registrar tudo a cada teste.
		// workaround for https://core.trac.wordpress.org/ticket/48300
		Movies::get_instance()->register_post_type();
	}
}