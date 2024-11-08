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

/**
 * Register block bindings source.
 */
function gravatar_register_block_bindings_source() {
	register_block_bindings_source(
		'santosguillamot/gravatar',
		array(
			'label'              => __( 'Gravatar', 'santosguillamot' ),
			'get_value_callback' => function ( $source_args ) {
				// Fetch data from Gravatar API.
				$args = array();
				// Add auth headers if API key is set.
				if ( isset( $_ENV['GRAVATAR_API_KEY'] ) ) {
					$args['headers'] = array(
						'Authorization' => 'Bearer ' . $_ENV['GRAVATAR_API_KEY'],
					);
				}
				$response = wp_remote_get( 'https://api.gravatar.com/v3/profiles/' . $source_args['id'], $args );
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body );
				$field = $source_args['field'];
				return $data->$field;
			},
		)
	);
}
add_action( 'init', 'gravatar_register_block_bindings_source' );
