<?php
/**
 * Contains the asset registration class.
 *
 * @package WP\update_php
 */

namespace WP\update_php;

use Exception;

/**
 * Asset_Registration
 *
 * This class takes care of registering all assets and managing the hash versioning mechanism
 *
 * @package WP\update_php
 * @author  Darren Ethier
 * @since   0.1.0
 */
class Asset_Registration {

	const JS_HANDLE_BLOCKS         = 'wp-update-php-blocks-js';
	const CSS_HANDLE_BLOCKS        = 'wp-update-php-blocks-css';
	const FILE_NAME_BUILD_MANIFEST = 'build-manifest.json';
	const TYPE_JS                  = 'js';
	const TYPE_CSS                 = 'css';

	/**
	 * Holds an instance of Domain.
	 *
	 * @var Domain
	 */
	private $domain;

	/**
	 * Holds an instance of I18n_Registry.
	 *
	 * @var I18n_Registry;
	 */
	private $i18n_registry;

	/**
	 * Holds the cached manifest data array.
	 *
	 * This array is a map of asset chunk name to its actual file as read from the build-manifest.js file prepared by
	 * the webpack build process for assets.  It is used to derive the path to an asset when it is registered with WP.
	 *
	 * @var array
	 */
	private $manifest_data;


	/**
	 * Asset_Registration constructor.
	 *
	 * @param Domain        $domain        An instance of Domain.
	 * @param I18n_Registry $i18n_registry An instance of I18n_Registry.
	 */
	public function __construct( Domain $domain, I18n_Registry $i18n_registry ) {
		$this->domain        = $domain;
		$this->i18n_registry = $i18n_registry;
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );
	}

	/**
	 * Callback on the `wp_enqueue_scripts` and `admin_enqueue_scripts` hooks.  This is the main method kicking off
	 * registration of all assets.
	 */
	public function register_scripts_and_styles() {
		try {
			$this->register_manifest();
			$this->register_scripts();
			$this->register_styles();
		} catch ( Exception $exception ) {
			wp_die( esc_html( $exception->getMessage() ) );
		}
	}

	/**
	 * Takes care of reading from the `build-manifest.js` file prepared by the javascript build process and assigning it
	 * to the `$manifest_data` property.
	 */
	private function register_manifest() {
		$this->manifest_data             = $this->decode_manifest_file(
			$this->domain->assets_path() . self::FILE_NAME_BUILD_MANIFEST
		);
		$this->manifest_data['url_base'] = $this->domain->assets_url();
	}

	/**
	 * Decodes the build-manifest file and returns as an array.
	 *
	 * @param string $manifest_file_path Path to the build-manifest.js file (expected to be json).
	 *
	 * @return array
	 */
	public function decode_manifest_file( $manifest_file_path ) {
		return json_decode( file_get_contents( $manifest_file_path ), true );
	}

	/**
	 * This registers javascript assets.
	 */
	private function register_scripts() {
		wp_register_script(
			self::JS_HANDLE_BLOCKS,
			$this->get_js_url( 'update-php-blocks' ),
			array( 'wp-blocks' ),
			null,
			true
		);
		$this->register_translation( self::JS_HANDLE_BLOCKS );
	}

	/**
	 * This registers css assets
	 */
	private function register_styles() {
		// no css yet.
	}

	/**
	 * Return the url to a js file for the given namespace and chunk name.
	 *
	 * @param string $chunk_name The chunk_name should match the name given the entry for the chunk in webpack config.
	 *
	 * @return string
	 */
	public function get_js_url( $chunk_name ) {
		return $this->get_asset_url( $chunk_name, self::TYPE_JS );
	}


	/**
	 * Return the url to a css file for the given namespace and chunk name.
	 *
	 * @param string $chunk_name The chunk_name should match the name given the entry for the chunk in webpack config.
	 *
	 * @return string
	 */
	public function get_css_url( $chunk_name ) {
		return $this->get_asset_url( $chunk_name, self::TYPE_CSS );
	}

	/**
	 * Get the actual asset path for asset manifests.
	 * If there is no asset path found for the given $chunk_name, then the $chunk_name is returned.
	 *
	 * @param string $chunk_name The chunk_name should match the name given the entry for the chunk in webpack config.
	 * @param string $asset_type What type (self::TYPE_JS | self::TYPE_CSS ).
	 *
	 * @return string
	 */
	public function get_asset_url( $chunk_name, $asset_type ) {
		return isset(
			$this->manifest_data[ $chunk_name . '.' . $asset_type ],
			$this->manifest_data['url_base']
		)
			? $this->manifest_data['url_base'] . $this->manifest_data[ $chunk_name . '.' . $asset_type ]
			: $chunk_name;
	}

	/**
	 * Used to register a script as having translations with i18n_registry.
	 *
	 * @param string $script_handle  The handle used to register the script.
	 */
	public function register_translation( $script_handle ) {
		$this->i18n_registry->register_script_i18n( $script_handle );
	}
}
