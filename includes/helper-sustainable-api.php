<?php
/**
 * API Class
 *
 * @package    WPSustainable
 * @author     Javier Casares <javier@casares.org>
 * @version    1.1.0
 */

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die( 'Sorry!' );


/**
 * Retrieves the hostname of the current site.
 *
 * Parses the site's URL to extract the hostname.
 *
 * @since 1.1.0
 * @return string|null The hostname of the site or null if not found.
 */
function wpsustainable_get_hostname() {
	$site_url = get_site_url();
	if ( $site_url ) {
		$hostname = wp_parse_url( $site_url, PHP_URL_HOST );
		if ( ! $hostname ) {
			$hostname = null;
		}
	} else {
		$hostname = null;
	}
	return $hostname;
}

/**
 * Gets Green Check from The Green Web Foundation API
 *
 * @param string $hostname HostName.
 * @return array
 */
function wpsustainable_get_greencheck( $hostname ) {

	$args          = array(
		'timeout'   => 3000,
		'sslverify' => false,
	);
	$key           = 'wpsustainable_tgwf';
	$wpsustainable = get_transient( $key );
	if ( ! $wpsustainable ) {
		$url      = 'https://admin.thegreenwebfoundation.org/api/v3/greencheck/' . $hostname;
		$response = wp_remote_get( $url, $args );
		if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
			$body = wp_remote_retrieve_body( $response );
			set_transient( $key, $body, HOUR_IN_SECONDS * 24 );
		}
	}
	return json_decode( $wpsustainable, true );
}

/**
 * Gets CO2 Intensity from The Green Web Foundation API
 *
 * @param string $hostname HostName.
 * @return array
 */
function wpsustainable_get_co2intensity( $hostname ) {

	$args          = array(
		'timeout'   => 3000,
		'sslverify' => false,
	);
	$key           = 'wpsustainable_co2i';
	$wpsustainable = get_transient( $key );
	if ( ! $wpsustainable ) {

		$hostip = gethostbynamel( $hostname );

		if ( isset( $hostip[0] ) ) {
			$hostipmain = trim( (string) $hostip[0] );
		}

		unset( $hostip );

		if ( $hostipmain ) {
			$url      = 'https://admin.thegreenwebfoundation.org/api/v3/ip-to-co2intensity/' . $hostipmain;
			$response = wp_remote_get( $url, $args );
			if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body = wp_remote_retrieve_body( $response );
				set_transient( $key, $body, HOUR_IN_SECONDS * 24 );
			}
		} else {
			$wpsustainable = false;
		}

		unset( $hostipmain );
	}

	return json_decode( $wpsustainable, true );
}

/**
 * Retrieves sustainability and carbon intensity data for the given hostname.
 *
 * This function uses the greencheck API to fetch sustainability metrics for the
 * specified hostname and then combines it with carbon intensity data.
 *
 * @package WPSustainable
 * @since 1.1.0
 *
 * @param string $hostname The hostname for which to retrieve the sustainability and carbon intensity data.
 * @return array An array containing 'green' and 'co2intensity' data for the given hostname.
 */
function wpsustainable_get( $hostname ) {

	$wpsustainable = array(
		'green'        => array(
			'url'         => null,
			'hosting'     => null,
			'hosting_url' => null,
			'is_green'    => null,
			'docs'        => array(),
		),
		'co2intensity' => array(
			'country'        => null,
			'country_iso_2'  => null,
			'country_iso_3'  => null,
			'intensity'      => null,
			'intensity_type' => null,
			'fossil'         => null,
			'year'           => null,
			'ip'             => null,
		),
	);

	$response_greencheck = wpsustainable_get_greencheck( $hostname );

	if ( isset( $response_greencheck['modified'] ) && $response_greencheck['modified'] ) {

		if ( isset( $response_greencheck['url'] ) && $response_greencheck['url'] ) {
			$wpsustainable['green']['url'] = wp_filter_nohtml_kses( $response_greencheck['url'] );
		}

		if ( isset( $response_greencheck['hosted_by'] ) && $response_greencheck['hosted_by'] ) {
			$wpsustainable['green']['hosting'] = wp_filter_nohtml_kses( $response_greencheck['hosted_by'] );
		}

		if ( isset( $response_greencheck['hosted_by_website'] ) && $response_greencheck['hosted_by_website'] ) {
			$wpsustainable['green']['hosting_url'] = wp_filter_nohtml_kses( $response_greencheck['hosted_by_website'] );
		}

		if ( isset( $response_greencheck['green'] ) && $response_greencheck['green'] ) {
			$wpsustainable['green']['is_green'] = true;
		} elseif ( isset( $response_greencheck['green'] ) && ! $response_greencheck['green'] ) {
			$wpsustainable['green']['is_green'] = false;
		}

		if ( isset( $response_greencheck['modified'] ) && $response_greencheck['modified'] ) {
			$wpsustainable['green']['modified'] = wp_filter_nohtml_kses( gmdate( 'Y-m-d', strtotime( $response_greencheck['modified'] ) ) );
		}

		if ( isset( $response_greencheck['supporting_documents'] ) && is_array( $response_greencheck['supporting_documents'] ) ) {
			foreach ( $response_greencheck['supporting_documents'] as $supporting_documents ) {
				$wpsustainable['green']['docs'][] = array(
					'name' => wp_filter_nohtml_kses( $supporting_documents['title'] ),
					'url'  => wp_filter_nohtml_kses( $supporting_documents['link'] ),
				);
				unset( $supporting_documents );
			}
		}
	}

	unset( $response_greencheck );

	$response_co2intensity = wpsustainable_get_co2intensity( $hostname );

	if ( is_array( $response_co2intensity ) ) {

		if ( isset( $response_co2intensity['country_name'] ) && $response_co2intensity['country_name'] ) {
			$wpsustainable['co2intensity']['country']       = wp_filter_nohtml_kses( $response_co2intensity['country_name'] );
			$wpsustainable['co2intensity']['country_iso_2'] = strtoupper( wp_filter_nohtml_kses( $response_co2intensity['country_code_iso_2'] ) );
			$wpsustainable['co2intensity']['country_iso_3'] = strtoupper( wp_filter_nohtml_kses( $response_co2intensity['country_code_iso_3'] ) );
		}

		if ( isset( $response_co2intensity['carbon_intensity_type'] ) && $response_co2intensity['carbon_intensity_type'] && isset( $response_co2intensity['carbon_intensity'] ) && $response_co2intensity['carbon_intensity'] ) {
			$wpsustainable['co2intensity']['intensity_type'] = wp_filter_nohtml_kses( $response_co2intensity['carbon_intensity_type'] );
			$wpsustainable['co2intensity']['intensity']      = number_format( (float) wp_filter_nohtml_kses( $response_co2intensity['carbon_intensity'] ), 3, '.', '' );
		}

		if ( isset( $response_co2intensity['generation_from_fossil'] ) && $response_co2intensity['generation_from_fossil'] ) {
			$wpsustainable['co2intensity']['fossil'] = number_format( (float) wp_filter_nohtml_kses( $response_co2intensity['generation_from_fossil'] ), 2, '.', '' ) . ' %';
		}

		if ( isset( $response_co2intensity['year'] ) && $response_co2intensity['year'] ) {
			$wpsustainable['co2intensity']['year'] = wp_filter_nohtml_kses( $response_co2intensity['year'] );
		}

		if ( isset( $response_co2intensity['checked_ip'] ) && $response_co2intensity['checked_ip'] ) {
			$wpsustainable['co2intensity']['ip'] = wp_filter_nohtml_kses( $response_co2intensity['checked_ip'] );
		}
	}

	unset( $response_co2intensity );

	return $wpsustainable;
}
