/**
 * External imports
 */
import { Component, Fragment } from '@wordpress/element';
import {
	ToggleControl,
	TextareaControl,
	TextControl,
	SelectControl,
} from '@wordpress/components';

import { InspectorControls } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { map } from 'lodash';

/**
 * Internal imports
 */
import ContentPreview from './content-preview';

/**
 * This is the component for the Version detection content block.
 * @return {Component}  A React Component
 */
export default class VersionDetectorContent extends Component {
	/**
	 * Toggles the previewOutdatedContent attribute
	 */
	setPreview = () => {
		const { setAttributes, attributes } = this.props;
		const { previewOutdatedContent } = attributes;
		setAttributes( { previewOutdatedContent: ! previewOutdatedContent } );
	};

	/**
	 * Updates the minimumUpToDateVersion attribute.
	 * @param {string} value
	 */
	setMinimumUpToDate = ( value ) => {
		this.props.setAttributes( { minimumUpToDateVersion: value } );
	};

	/**
	 * Updates the body attributes for either outOfDate or upToDate variations.
	 *
	 * @param {boolean} outdated
	 * @return {function} Callback for updating.
	 */
	setBodyContent = ( outdated = true ) => {
		const field = outdated ? 'outOfDateBody' : 'upToDateBody';
		return ( value ) => this.setFieldAndValue( field, value );
	};

	/**
	 * Updates the title attributes for either outOfDate or upToDate variations.
	 *
	 * @param {boolean} outdated
	 * @return {function} Callback for updating.
	 */
	setTitleContent = ( outdated = true ) => {
		const field = outdated ? 'outOfDateTitle' : 'upToDateTitle';
		return ( value ) => this.setFieldAndValue( field, value );
	};

	/**
	 * Updates the emphasis content attributes for either outOfDate or upToDate variations.
	 *
	 * @param {boolean} outdated
	 * @return {function} Callback for updating.
	 */
	setEmphasisContent = ( outdated = true ) => {
		const field = outdated ? 'outOfDateEmphasis' : 'upToDateEmphasis';
		return ( value ) => this.setFieldAndValue( field, value );
	};

	/**
	 * Handles updating the attributes for given field and value.
	 * @param {string} field
	 * @param {mixed}  value
	 */
	setFieldAndValue( field, value ) {
		this.props.setAttributes( { [ field ]: value } );
	}

	/**
	 * Returns the version options formatted for the SelectControl component.
	 * @return {Array} An array of options formatted for the SelectControl component.
	 */
	versionOptions() {
		const versions = [
			'5.3',
			'5.4',
			'5.5',
			'5.6',
			'7.0',
			'7.1',
			'7.2',
		];
		return map( versions, ( version ) => {
			return { label: version, value: version };
		} );
	}

	/**
	 * Returns the appropriate title content for the given flag.
	 *
	 * @param {boolean} outdated
	 * @return {string} Title Content.
	 */
	getContentTitle( outdated = true ) {
		return outdated ?
			__( 'Outdated PHP Version Content', 'update-php' ) :
			__( 'Up-to-date PHP Version Content', 'update-php' );
	}

	/**
	 * Returns the appropriate help text content for the given flag.
	 *
	 * @param {boolean} outdated
	 * @return {string} Help Text content.
	 */
	getContentHelpText( outdated = true ) {
		return outdated ?
			__(
				'When the detected php version is less than the "Minimum Up To Date Version", this is the content that gets shown.',
				'update-php'
			) :
			__(
				'When the detected php version is equal to or greater than the "Minimum Up To Date Version", this is the content that gets shown.',
				'update-php'
			);
	}

	/**
	 * Returns the controls for the version content for the given arguments.
	 *
	 * @param {boolean} outdated
	 * @param {string} title
	 * @param {string} body
	 * @param {string} emphasis
	 * @return {Array} an array of components.
	 */
	phpVersionContent( outdated = true, title, body, emphasis ) {
		return (
			<Fragment>
				<h4>{ this.getContentTitle( outdated ) }</h4>
				<p>{ this.getContentHelpText( outdated ) }</p>
				<TextControl
					label={ __( 'Title', 'update-php' ) }
					value={ title }
					onChange={ this.setTitleContent( outdated ) }
				/>
				<TextareaControl
					label={ __( 'Body', 'update-php' ) }
					value={ body }
					onChange={ this.setBodyContent( outdated ) }
				/>
				<TextareaControl
					label={ __( 'Emphasis', 'update-php' ) }
					value={ emphasis }
					onChange={ this.setEmphasisContent( outdated ) }
				/>
			</Fragment>
		);
	}

	/**
	 * Returns the content preview component for the given flag.
	 *
	 * @param {boolean} outdated
	 * @return {Component} The Content Preview jsx.
	 */
	getContentPreview( outdated = true ) {
		const field = outdated ? 'outOfDate' : 'upToDate';
		const previewProps = {
			body: this.props.attributes[ field + 'Body' ],
			title: this.props.attributes[ field + 'Title' ],
			emphasis: this.props.attributes[ field + 'Emphasis' ],
		};
		return <ContentPreview { ...previewProps } />;
	}

	/**
	 * Renders the content for the component.
	 * @return {*} Rendered content (jsx).
	 */
	render() {
		const {
			minimumUpToDateVersion,
			previewOutdatedContent,
			outOfDateBody,
			outOfDateTitle,
			outOfDateEmphasis,
			upToDateBody,
			upToDateTitle,
			upToDateEmphasis,
		} = this.props.attributes;
		return (
			<Fragment>
				<InspectorControls>
					<ToggleControl
						label={ __( 'Preview Outdated Content', 'update-php' ) }
						checked={ previewOutdatedContent }
						onChange={ this.setPreview }
						help={ __(
							'Use this to switch between previewing the outdated or up-to-date php version content',
							'update-php'
						) }
					/>
					<SelectControl
						label={ __( 'Minimum Up To Date Version:', 'update-php' ) }
						help={ __(
							'The minimum version that will trigger the up-to-date content to show.  Anything below this version will result in out-dated version content showing',
							'update-php'
						) }
						value={ minimumUpToDateVersion }
						options={ this.versionOptions() }
						onChange={ this.setMinimumUpToDate }
					/>
					{ this.phpVersionContent( true, outOfDateTitle, outOfDateBody, outOfDateEmphasis ) }
					{ this.phpVersionContent( false, upToDateTitle, upToDateBody, upToDateEmphasis ) }
				</InspectorControls>
				{ this.getContentPreview( previewOutdatedContent ) }
			</Fragment>
		);
	}
}
