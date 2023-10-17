<?php
/**
 * WP Sustainable Green Badge Block Asset Manifest
 *
 * Provides the dependencies and version details for the WP Sustainable Green Badge block.
 *
 * @package WPSustainable
 * @version 1.1.0
 *
 * @return array {
 *     An array containing the dependencies and version for the block asset.
 *
 *     @type array $dependencies The list of script dependencies for the block.
 *     @type string $version The version of the block asset.
 * }
 */

return array(
	'dependencies' =>
			array(
				'wp-blocks',
				'wp-element',
				'wp-polyfill',
			),
	'version'      => '1.0.0',
);
