<?php
/**
 * Green Web Foundation Badge Class
 *
 * @package    WordPress
 * @author     Jamie Blomerus <jamie.blomerus@protonmail.com>
 * @version    1.0.0
 */

defined( 'ABSPATH' ) || die( 'Sorry!' );

/**
 * Class Badge Block
 */
class wpsustainable_badge {

    public function __construct()
    {
        add_action( 'init', [$this, 'register_block']);
    }

    public function register_block() {
        register_block_type( __DIR__ );
    }
}

new wpsustainable_badge;