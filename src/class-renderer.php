<?php
/**
 * Contains the Renderer class.
 *
 * @package WP\update_php
 */

namespace WP\update_php;

use Mustache_Engine;
use Mustache_Exception_InvalidArgumentException;
use Mustache_Exception_RuntimeException;
use Mustache_Loader_FilesystemLoader;

/**
 * Renderer
 *
 * This is a class that handles returning rendered content for a provided template and template data.
 *
 * @package WP\update_php
 * @author  Darren Ethier
 * @since   0.1.0
 */
class Renderer {

	/**
	 * An instance of Domain.
	 *
	 * @var Domain
	 */
	private $domain;

	/**
	 * An instance of Mustache_Engine
	 *
	 * @var Mustache_Engine
	 */
	private $mustache;

	/**
	 * Renderer constructor.
	 *
	 * @param Domain $domain An instance of Domain.
	 *
	 * @throws Mustache_Exception_InvalidArgumentException  Throws an invalid Argument.
	 * @throws Mustache_Exception_RuntimeException Throws a runtime exception.
	 */
	public function __construct( Domain $domain ) {
		$this->domain   = $domain;
		$this->mustache = new Mustache_Engine(
			[
				'loader' => new Mustache_Loader_FilesystemLoader(
					$this->domain->template_path()
				),
			]
		);
	}

	/**
	 * Returns the rendered content for the provided template values and template file name.
	 *
	 * You only have to provide the template file name (without extension).  It is expected that the template resides
	 * in: "/path/to/wp-content/plugins/update-php/templates/{your-template-file.mustache".
	 *
	 * @param array  $template_values  Key value pairs that will be replaced in the template (where keys are the tokens).
	 * @param string $template_file_name  This should just be the file name without the extension for the template.
	 *
	 * @return string
	 */
	public function render( $template_values, $template_file_name ) {
		return $this->mustache->loadTemplate( $template_file_name )->render( $template_values );
	}
}
