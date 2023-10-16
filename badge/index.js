/**
 * WP Sustainable Green Badge Block
 *
 * This script registers a custom Gutenberg block for the WP Sustainable plugin.
 * The block displays a badge from thegreenwebfoundation.org based on the current domain's
 * sustainability status.
 *
 * @module wpsustainable/greenbadge
 * @requires wp.blocks
 * @requires wp.element
 *
 * @package WPSustainable
 * @version 1.1.0
 */

(function (blocks, element) {
	var el              = element.createElement;
	const currentDomain = window.location.hostname;

	blocks.registerBlockType(
		'wpsustainable/greenbadge',
		{
			edit: function () {
				return el(
					'p',
					{ className: 'wpsustainable-greenbadge' },
					[
					el( 'img', { src: 'https://api.thegreenwebfoundation.org/greencheckimage/' + currentDomain + '?nocache=true' } ),
					]
				);
			},
			save: function () {
				return el(
					'figure',
					{ className: 'wpsustainable-greenbadge' },
					[
					el( 'img', { src: 'https://api.thegreenwebfoundation.org/greencheckimage/' + currentDomain + '?nocache=true' } ),
					]
				);
			},
		}
	);
})( window.wp.blocks, window.wp.element );