<?php
/**
* Plugin Name: Interfaces Invisíveis
* Author: leogermani
* Description: Plugin de exemplo para desenvolver APIs com WordPress
* License: GPL2+
*/

namespace Wordcamp;

require 'class-singleton.php';
require 'class-movies.php';
require 'class-filmes-endpoint.php';

Movies::get_instance();

new Filmes_Api_Endpoint();

register_activation_hook( __FILE__, 'Wordcamp\plugin_activation' );

function plugin_activation() {
	flush_rewrite_rules();
}