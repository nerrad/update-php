<?php
/**
 * Contains the block registration class.
 *
 * @package WP\update_php
 */

namespace WP\update_php;

use WP_Block_Type;

/**
 * Block_Registration
 * Takes care of registering blocks for Gutenberg.
 *
 * @package WP\update_php
 * @author  Darren Ethier
 * @since   0.1.0
 */
class Block_Registration {

	const OUT_OF_DATE = 'outOfDate';
	const UP_TO_DATE  = 'upToDate';

	/**
	 * Holds an instance of Domain.
	 *
	 * @var Domain
	 */
	private $domain;

	/**
	 * Holds an instance of Renderer.
	 *
	 * @var Renderer
	 */
	private $renderer;

	/**
	 * Block_Registration constructor.
	 *
	 * @param Domain   $domain An instance of Domain.
	 * @param Renderer $renderer An instance of Renderer.
	 */
	public function __construct( Domain $domain, Renderer $renderer ) {
		$this->domain   = $domain;
		$this->renderer = $renderer;
		$this->init();
	}

	/**
	 * This initializes and registers all the blocks with the Gutenberg api.
	 */
	private function init() {
		register_block_type(
			new WP_Block_Type(
				'update-php/version-detector-content',
				array(
					'editor_script'   => Asset_Registration::JS_HANDLE_BLOCKS,
					'editor_style'    => Asset_Registration::JS_HANDLE_BLOCKS,
					'render_callback' => array( $this, 'render_version_detected_content' ),
					'attributes'      => array(
						'minimumUpToDateVersion'       => array(
							'type'    => 'string',
							'default' => '5.3',
						),
						'previewOutdatedContent'       => array(
							'type'    => 'boolean',
							'default' => false,
						),
						self::OUT_OF_DATE . 'Body'     => array(
							'type'    => 'string',
							'default' => $this->default_out_of_date_body(),
						),
						self::OUT_OF_DATE . 'Title'    => array(
							'type'    => 'string',
							'default' => $this->default_out_of_date_title(),
						),
						self::OUT_OF_DATE . 'Emphasis' => array(
							'type'    => 'string',
							'default' => $this->default_out_of_date_emphasis(),
						),
						self::UP_TO_DATE . 'Body'      => array(
							'type'    => 'string',
							'default' => $this->default_up_to_date_body(),
						),
						self::UP_TO_DATE . 'Title'     => array(
							'type'    => 'string',
							'default' => $this->default_up_to_date_title(),
						),
						self::UP_TO_DATE . 'Emphasis'  => array(
							'type'    => 'string',
							'default' => $this->default_up_to_date_emphasis(),
						),
					),
				)
			)
		);
	}

	/**
	 * The callback for returning the version detected content wherever the block is used.
	 *
	 * @param array $attributes The attributes extracted from post_content for the block.
	 * @return string
	 */
	public function render_version_detected_content( array $attributes = [] ) {
		$detected_php_version       = $this->get_php_version( $attributes );
		$minimum_up_to_date_version = isset( $attributes['minimumUpToDateVersion'] )
			? $attributes['minimumUpToDateVersion']
			: '';
		if ( ! $detected_php_version ) {
			return '';
		}

		return version_compare( $detected_php_version, $minimum_up_to_date_version, '>=' )
			? $this->render_content( $attributes, self::UP_TO_DATE )
			: $this->render_content( $attributes );
	}


	/**
	 * Gets the rendered content for the given arguments.
	 *
	 * @param array  $attributes  Attributes saved with block.
	 * @param string $template_type What type of template to return.
	 *
	 * @return string
	 */
	private function render_content( array $attributes, $template_type = self::OUT_OF_DATE ) {
		return $this->renderer->render(
			$this->get_template_data( $attributes, $template_type ),
			'update-php-block'
		);
	}

	/**
	 * Returns an array of template data derived from provided attributes for the given template type.
	 *
	 * @param array  $attributes Attributes saved with block.
	 * @param string $template_type What type of template to return.
	 *
	 * @return array
	 */
	private function get_template_data( array $attributes, $template_type = self::OUT_OF_DATE ) {
		$title_index    = $template_type . 'Title';
		$body_index     = $template_type . 'Body';
		$emphasis_index = $template_type . 'Emphasis';

		return [
			'title'    => isset( $attributes[ $title_index ] ) ?
				$attributes[ $title_index ] :
				'',
			'body'     => isset( $attributes[ $body_index ] ) ?
				$attributes[ $body_index ] :
				'',
			'emphasis' => isset( $attributes[ $emphasis_index ] ) ?
				$attributes[ $emphasis_index ] :
				'',
		];
	}

	/**
	 * Default content for version out of date body.
	 *
	 * @return string
	 */
	private function default_out_of_date_body() {
		return 'The PHP version on your server is out-of-date. This negatively impacts speed and security on your '
			. 'site, and needs to be fixed. The below tutorial will show you how to fix this today. Please follow '
			. 'these instructions to protect your website.';
	}

	/**
	 * Default content for version out of date title.
	 *
	 * @return string
	 */
	private function default_out_of_date_title() {
		return 'WARNING';
	}

	/**
	 * Default content for version out of date emphasis text.
	 *
	 * @return string
	 */
	private function default_out_of_date_emphasis() {
		return 'Your WordPress site is slower and less secure than it can be.';
	}

	/**
	 * Default content for version up to date body text
	 *
	 * @return string
	 */
	private function default_up_to_date_body() {
		return 'This means you’re enjoying speed and security benefits already. You don’t need to update your '
			. 'server’s PHP version at the moment, but the tutorial below will show you how to do so in future.';
	}

	/**
	 * Default content for version up to date title text.
	 *
	 * @return string
	 */
	private function default_up_to_date_title() {
		return 'GREAT NEWS!';
	}

	/**
	 * Default content for version up to date emphasis text.
	 *
	 * @return string
	 */
	private function default_up_to_date_emphasis() {
		return 'The PHP version on your server is up-to-date.';
	}

	/**
	 * Detect if request has incoming php version set.
	 *
	 * @return string
	 */
	private function get_php_version() {
		return isset( $_REQUEST['php_version'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['php_version'] ) )
			: '';
	}
}
