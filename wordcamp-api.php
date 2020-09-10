<?php
/**
* Plugin Name: Interfaces Invisíveis
* Author: leogermani
* License: GPL2+
*/

namespace Wordcamp;

require 'class-singleton.php';
require 'class-movies.php';
require 'class-filmes-endpoint.php';

Movies::get_instance();

new Filmes_Api_Endpoint();
