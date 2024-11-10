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
			'uses_context'       => array( 'postId', 'postType' ),
			'get_value_callback' => function ( $source_args, $block_instance ) {
				$post_id = $block_instance->context['postId'];
				$user_id = get_post_meta( $post_id, 'gravatar_id', true );
				$field = $source_args['field'];
				// Read from JSON file.
				$file = file_get_contents( plugin_dir_url( __FILE__ ) . '/simulate-data.json' );
				$api_response = json_decode( $file );
				return $api_response->$user_id->$field;

				// Fetch data from Gravatar API.
				// $args = array();
				// // Add auth headers if API key is set.
				// if ( isset( $_ENV['GRAVATAR_API_KEY'] ) ) {
				// $args['headers'] = array(
				// 'Authorization' => 'Bearer ' . $_ENV['GRAVATAR_API_KEY'],
				// );
				// }
				// $response = wp_remote_get( 'https://api.gravatar.com/v3/profiles/' . $source_args['id'], $args );
				// $body = wp_remote_retrieve_body( $response );
				// $data = json_decode( $body );
				// $field = $source_args['field'];
				// return $data->$field;
			},
		)
	);
}
add_action( 'init', 'gravatar_register_block_bindings_source' );

/**
 * Register gravatar id custom field.
 */
function gravatar_register_custom_field() {
	register_meta(
		'post',
		'gravatar_id',
		array(
			'label'          => 'Gravatar ID',
			'object_subtype' => 'page',
			'show_in_rest'   => true,
			'single'         => true,
			'type'           => 'string',
			'default'        => 'santosguillamot',
		)
	);
}
add_action( 'init', 'gravatar_register_custom_field' );

/**
 * Enqueue JS files.
 */
function gravatar_editor_assets() {
	$asset = include plugin_dir_path( __FILE__ ) . '/build/index.asset.php';

	wp_enqueue_script(
		'gravatar-editor-bindings',
		plugin_dir_url( __FILE__ ) . '/build/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);

	// Pass JSON data to script.
	// This shouldn't be needed if working with Gravatar API.
	$json_data = file_get_contents( plugin_dir_path( __FILE__ ) . 'simulate-data.json' );
	$response  = json_decode( $json_data, true );
	wp_localize_script(
		'gravatar-editor-bindings',
		'gravatarData',
		array(
			'apiResponse' => $response,
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'gravatar_editor_assets' );

/**
 * Add way to edit the JSON file.
 * This shouldn't be needed if working with Gravatar API.
 */
function update_gravatar_json_data() {
	$api_response = stripslashes( $_REQUEST['api_response'] );
	file_put_contents( plugin_dir_path( __FILE__ ) . 'simulate-data.json', $api_response );
	wp_send_json_success();
}
add_action( 'wp_ajax_update_gravatar_json_data', 'update_gravatar_json_data' );
add_action( 'wp_ajax_nopriv_update_gravatar_json_data', 'update_gravatar_json_data' );
