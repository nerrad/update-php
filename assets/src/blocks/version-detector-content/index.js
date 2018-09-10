/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import VersionDetectorContent from './block';

export default () => registerBlockType(
	'update-php/version-detector-content',
	{
		title: __( 'PHP Version Detection Content', 'update-php' ),
		icon: 'list-view',
		category: 'widgets',
		keywords: [ 'php', 'detection', 'version' ],
		supports: {
			html: false,
		},
		edit: VersionDetectorContent,
		save() {
			return null;
		},
	}
);
