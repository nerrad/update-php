/**
 * Functional component returning the preview for the version specific content.
 *
 * @param {string} title
 * @param {string} body
 * @param {string} emphasis
 * @return {function} Returns functional component.
 * @constructor
 */
const ContentPreview = ( {
	title,
	body,
	emphasis,
} ) => {
	return (
		<div className={ 'detected-php-content' }>
			<h4>{ title }</h4>
			<p className={ 'detected-php-emphasis' }>{ emphasis }</p>
			<p>{ body }</p>
		</div>
	);
};
export default ContentPreview;
