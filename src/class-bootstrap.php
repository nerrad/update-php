<?php
/**
 * Contains the main bootstrap class.
 *
 * @package WP\update_php
 */

namespace WP\update_php;

/**
 * Bootstrap
 *
 * This class takes care of bootstrapping the plugin.
 *
 * @package WP\update_php
 * @author  Darren Ethier
 * @since   0.1.0
 */
class Bootstrap {

	/**
	 * Holds instance of Bootstrap
	 *
	 * @var Bootstrap
	 */
	private static $instance;

	/**
	 * Holds instance of Asset_Registration
	 *
	 * @var Asset_Registration
	 */
	private $asset_registration;

	/**
	 * Holds instance of Block_Registration
	 *
	 * @var Block_Registration
	 */
	private $block_registration;

	/**
	 * Holds instance of Domain
	 *
	 * @var Domain
	 */
	private $domain;

	/**
	 * Bootstrap constructor.
	 *
	 * @param Domain $domain Instance of domain.
	 */
	public function __construct( Domain $domain ) {
		// bail early if gutenberg not detected.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$this->domain             = $domain;
		$this->asset_registration = new Asset_Registration(
			$this->domain,
			new I18n_Registry( $this->domain )
		);
		$this->block_registration = new Block_Registration(
			$this->domain,
			new Renderer( $this->domain )
		);
	}

	/**
	 * Used for getting an instance of Asset_Registration (typically if needed for unsetting hooks).
	 *
	 * @return Asset_Registration
	 */
	public function asset_registration() {
		return $this->asset_registration;
	}

	/**
	 * Used for getting an instance of Block_Registration (typically if needed for unsetting hooks).
	 *
	 * @return Block_Registration
	 */
	public function block_registration() {
		return $this->block_registration;
	}


	/**
	 * Used for getting an instance of Domain.
	 *
	 * @return Domain
	 */
	public function domain() {
		return $this->domain;
	}


	/**
	 * For constructing an instance of Bootstrap and bootstrapping the plugin.
	 *
	 * Can also be used for retrieving the Bootstrap instance for getting any main classes that set hooks so they can
	 * be unset if necessary.
	 *
	 * The two passed in parameters are used for constructing an instance of `Domain`
	 *
	 * @param string $version  The current version of the plugin.
	 * @param string $file     The current path (including file name) to the main file of the plugin.
	 *
	 * @return Bootstrap
	 */
	public static function instance( $version, $file ) {
		if ( ! ( self::$instance instanceof self ) ) {
			$domain         = new Domain( $version, $file );
			self::$instance = new Bootstrap( $domain );
		}

		return self::$instance;
	}
}
