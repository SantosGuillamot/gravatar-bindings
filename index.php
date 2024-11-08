<?php
/**
 * Plugin Name:       Gravatar Bindings
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      5.6
 * Description:       Plugin created for testing the block bindings API
 * Author:            Mario Santos
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       santosguillamot
 */

// Load Composer's autoload file.
require_once __DIR__ . '/vendor/autoload.php';
// Load Enviroment Variables.
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable( __DIR__ );
$dotenv->load();
