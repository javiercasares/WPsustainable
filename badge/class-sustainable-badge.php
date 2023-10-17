<?php
/**
 * Green Web Foundation Badge Class
 *
 * @package    WPSustainable
 * @author     Jamie Blomerus <jamie.blomerus@protonmail.com>
 * @version    1.1.0
 */

defined( 'ABSPATH' ) || die( 'Sorry!' );

/**
 * WP Sustainable Badge Class
 *
 * This class is responsible for registering a custom Gutenberg block for the WP Sustainable plugin.
 * It hooks into WordPress' init action to ensure the block type is registered during the initialization phase.
 *
 * @package WPSustainable
 * @version 1.1.0
 * @since 1.1.0
 */
class Sustainable_Badge {

	/**
	 * Constructor
	 *
	 * Sets up the necessary action hooks for the block registration.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register the block type
	 *
	 * Utilizes WordPress core's register_block_type() function to register the block using the directory path.
	 */
	public function register_block() {
		register_block_type( __DIR__ );
	}
}

/**
 * Instantiate the Sustainable_Badge class
 */
new Sustainable_Badge();
