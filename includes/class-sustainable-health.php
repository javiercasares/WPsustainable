<?php
/**
 * Health Check Class
 *
 * @package    WPSustainable
 * @author     Javier Casares <javier@casares.org>
 * @version    1.1.0
 */

defined( 'ABSPATH' ) || die( 'Sorry!' );

/**
 * Class HealthCheck
 */
class Sustainable_Health {

	/**
	 * Construct Class for Health Kit
	 */
	public function __construct() {
		add_filter( 'site_status_tests', array( $this, 'wpsustainable_add_tests' ) );
	}

	/**
	 * Add sustainability tests in Health
	 *
	 * @param array $tests Actual tests.
	 * @return array
	 */
	public function wpsustainable_add_tests( $tests ) {

		$tests['direct']['wpsustainable_greencheck'] = array(
			'label' => __( 'Hosting Green Check', 'wpsustainable' ),
			'test'  => array( $this, 'wpsustainable_test_greencheck' ),
		);

		$tests['direct']['wpsustainable_co2intensity'] = array(
			'label' => __( 'Site CO2 intensity', 'wpsustainable' ),
			'test'  => array( $this, 'wpsustainable_test_co2intensity' ),
		);

		return $tests;
	}

	/**
	 * Tests for Greencheck
	 *
	 * @return array
	 */
	public function wpsustainable_test_greencheck() {

		$hostname = wpsustainable_get_hostname();

		$result = array(
			'label'       => __( 'There is no sustainability information about your hosting company.', 'wpsustainable' ),
			'status'      => 'recommended',
			'badge'       => array(
				'label' => __( 'Sustainability', 'wpsustainable' ),
				'color' => 'green',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Your Hosting company is not in <a href="https://www.thegreenwebfoundation.org/directory/" target="_blank">The Green Web Foundation directory</a>.', 'wpsustainable' )
			),
			'actions'     => '',
			'test'        => 'wpsustainable_greencheck',
		);

		$wpsustainable = wpsustainable_get( $hostname );

		if ( is_array( $wpsustainable['green'] ) ) {

			if ( isset( $wpsustainable['green']['is_green'] ) && ! is_null( $wpsustainable['green']['is_green'] ) && $wpsustainable['green']['is_green'] ) {

				$documents = '<ul>';
				foreach ( $wpsustainable['green']['docs'] as $wpsustainable_documents ) {
					if ( isset( $wpsustainable_documents['name'] ) && isset( $wpsustainable_documents['url'] ) ) {
						$documents .= '<a href="' . $wpsustainable_documents['url'] . '" target="_blank">' . $wpsustainable_documents['name'] . '</a>';
					}
				}
				$documents .= '</ul>';

				$result['status']      = 'good';
				$result['label']       = __( 'Your Hosting company is using green energy.', 'wpsustainable' );
				$result['description'] = sprintf(
					'<p>%1$s, %2$s, %3$s</p><p>%4$s %5$s</p>',
					__( 'Your hosting company', 'wpsustainable' ),
					$wpsustainable['green']['hosting'],
					__( 'is using green energy for your site.', 'wpsustainable' ),
					__( 'Documentation:', 'wpsustainable' ),
					$documents
				);

			} elseif ( isset( $wpsustainable['is_green'] ) && ! is_null( $wpsustainable['is_green'] ) && ! $wpsustainable['is_green'] ) {
				$result['status']      = 'critical';
				$result['label']       = __( 'Your Hosting company is not using green energy.', 'wpsustainable' );
				$result['description'] = sprintf(
					'<p>%1$s, %2$s, %3$s</p>',
					__( 'Your hosting company', 'wpsustainable' ),
					$wpsustainable['green']['hosting'],
					__( 'is not using green energy for your site. <a href="https://www.thegreenwebfoundation.org/support/why-does-my-website-show-up-as-grey-in-the-green-web-checker/">Why am I seeing this result?</a>', 'wpsustainable' )
				);
			}
		}

		return $result;
	}

	/**
	 * Tests for Vulnerability in Themes
	 *
	 * @return array
	 */
	public function wpsustainable_test_co2intensity() {

		$hostname = wpsustainable_get_hostname();

		$result = array(
			'label'       => __( 'There is no CO2 intensity information about your site.', 'wpsustainable' ),
			'status'      => 'recommended',
			'badge'       => array(
				'label' => __( 'Sustainability', 'wpsustainable' ),
				'color' => 'green',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Your site has not informatiom about the average annual grid intensity based on its real-world location.', 'wpsustainable' )
			),
			'actions'     => '',
			'test'        => 'wpsustainable_themes',
		);

		$wpsustainable = wpsustainable_get( $hostname );

		if ( is_array( $wpsustainable['co2intensity'] ) ) {

			if ( isset( $wpsustainable['co2intensity']['intensity'] ) && ! is_null( $wpsustainable['co2intensity']['intensity'] ) ) {

				$result['status']      = 'good';
				$result['label']       = sprintf(
					'%1$s %2$s %3$s',
					__( 'Your average annual grid intensity is', 'wpsustainable' ),
					$wpsustainable['co2intensity']['intensity'],
					__( 'grams per kilowatt-hour (g/kWh).', 'wpsustainable' ),
				);
				$result['description'] = sprintf(
					'<p>%1$s %2$s. %3$s %4$s %5$s %6$s %7$s</p><p>%8$s %9$s %10$s',
					__( 'Your site is in', 'wpsustainable' ),
					$wpsustainable['co2intensity']['country'],
					__( 'In year', 'wpsustainable' ),
					$wpsustainable['co2intensity']['year'],
					__( 'the country produced', 'wpsustainable' ),
					$wpsustainable['co2intensity']['fossil'],
					__( 'from fossil energies.', 'wpsustainable' ),
					__( 'Your average annual grid intensity is', 'wpsustainable' ),
					$wpsustainable['co2intensity']['intensity'],
					__( 'grams per kilowatt-hour (g/kWh).', 'wpsustainable' )
				);
			}
		}

		return $result;
	}
}

new Sustainable_Health();
