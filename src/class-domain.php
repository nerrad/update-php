<?php
/**
 * Contains the Domain class.
 *
 * @package WP\update_php
 */

namespace WP\update_php;

/**
 * Domain
 *
 * This acts like a value object that simply contains Domain information for the plugin.  It can be passed around to use
 * for accessing paths, version and main file information.
 *
 * @package WP\update_php
 * @author  Darren Ethier
 * @since   0.1.0
 */
class Domain {

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * The path to the main file for the plugin.
	 *
	 * @var string
	 */
	private $file;


	/**
	 * The basename for the plugin's main file.
	 *
	 * @var string
	 */
	private $basename;


	/**
	 * This is a path to the top level directory for the plugin.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * This is the url to the top level directory for the plugin.
	 *
	 * @var string
	 */
	private $url;


	/**
	 * Domain constructor.
	 *
	 * @param string $version  Expected to the be the current version for the plugin.
	 * @param string $file   Expected to be the absolute path to the main file for the plugin.
	 */
	public function __construct( $version, $file ) {
		$this->version  = $version;
		$this->file     = $file;
		$this->basename = plugin_basename( $this->file );
		$this->path     = plugin_dir_path( $this->file );
		$this->url      = plugin_dir_url( $this->file );
	}

	/**
	 * Returns the path to the top level directory for the plugin.
	 *
	 * @return string
	 */
	public function path() {
		return $this->path;
	}

	/**
	 * Returns the url to the top level directory for the plugin.
	 *
	 * @return string
	 */
	public function url() {
		return $this->url;
	}

	/**
	 * Url to the assets folder for the plugin.
	 *
	 * @return string
	 */
	public function assets_url() {
		return $this->url . 'assets/dist/';
	}

	/**
	 * Path to the assets folder for the plugin.
	 *
	 * @return string
	 */
	public function assets_path() {
		return $this->path . 'assets/dist/';
	}

	/**
	 * The current version of the plugin.
	 *
	 * @return string
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * The path to the main file for the plugin.
	 *
	 * @return string
	 */
	public function file() {
		return $this->file;
	}

	/**
	 * The basename of the main plugin file.
	 *
	 * @return string
	 */
	public function basename() {
		return $this->basename;
	}

	/**
	 * Path to the templates for the plugin.
	 *
	 * @return string
	 */
	public function template_path() {
		return $this->path . 'templates/';
	}

	/**
	 * Url to the templates for the plugin.
	 *
	 * @return string
	 */
	public function template_url() {
		return $this->path . 'templates/';
	}
}
