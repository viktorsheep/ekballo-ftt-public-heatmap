<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Zume_App_Heatmap {


    /**
     * This query returns the 50k saturation list of locations with population and country code.
     *
     * Returns
     * grid_id, population, country_code
     *
     * @return array
     */
    public static function query_saturation_list() : array {

        if ( false !== ( $value = get_transient( __METHOD__ ) ) ) { // phpcs:ignore
            return $value;
        }

        // 44141 records

        global $wpdb;
        $results = $wpdb->get_results("

            SELECT
            lg1.grid_id, lg1.population, lg1.country_code
            FROM $wpdb->dt_location_grid lg1
            WHERE lg1.level = 0
			AND lg1.grid_id NOT IN ( SELECT lg11.admin0_grid_id FROM $wpdb->dt_location_grid lg11 WHERE lg11.level = 1 AND lg11.admin0_grid_id = lg1.grid_id )
 			#'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg1.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg1.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
			# above admin 0 (22)

			UNION ALL
            --
            # admin 1 locations that have no level 2 (768)
            --
            SELECT
            lg2.grid_id, lg2.population, lg2.country_code
            FROM $wpdb->dt_location_grid lg2
            WHERE lg2.level = 1
			AND lg2.grid_id NOT IN ( SELECT lg22.admin1_grid_id FROM $wpdb->dt_location_grid lg22 WHERE lg22.level = 2 AND lg22.admin1_grid_id = lg2.grid_id )
             #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg2.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg2.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL
			--
            # admin 2 all countries (37100)
            --
			SELECT
            lg3.grid_id, lg3.population,  lg3.country_code
            FROM $wpdb->dt_location_grid lg3
            WHERE lg3.level = 2
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg3.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg3.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL
            --
            # admin 1 for little highly divided countries (352)
            --
            SELECT
            lg4.grid_id, lg4.population,  lg4.country_code
            FROM $wpdb->dt_location_grid lg4
            WHERE lg4.level = 1
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg4.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg4.admin0_grid_id IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL

 			--
            # admin 3 for big countries (6153)
            --
            SELECT
            lg5.grid_id, lg5.population, lg5.country_code
            FROM $wpdb->dt_location_grid as lg5
            WHERE
            lg5.level = 3
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg5.admin0_grid_id IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg5.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			# Total Records (44395)

       ", ARRAY_A );

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                $list[$result['grid_id']] = $result;
            }
        }

        set_transient( __METHOD__, $list, MONTH_IN_SECONDS );

        return $list;
    }

    public static function query_saturation_list_full() : array {

        if ( false !== ( $value = get_transient( __METHOD__ ) ) ) { // phpcs:ignore
            return $value;
        }

        // 44141 records

        global $wpdb;
        $results = $wpdb->get_results("

            SELECT
                lg1.grid_id, lg1.population, lg1.country_code, lg1.longitude, lg1.latitude,
                CONCAT_WS(', ',
                          IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                          IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                          IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                          IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                          IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                    ) as full_name
            FROM $wpdb->dt_location_grid lg1
                     LEFT JOIN $wpdb->dt_location_grid as gc ON lg1.admin0_grid_id=gc.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg1.admin1_grid_id=ga1.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg1.admin2_grid_id=ga2.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg1.admin3_grid_id=ga3.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg1.admin4_grid_id=ga4.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg1.admin5_grid_id=ga5.grid_id
            WHERE lg1.level = 0
              AND lg1.grid_id NOT IN ( SELECT lg11.admin0_grid_id FROM $wpdb->dt_location_grid lg11 WHERE lg11.level = 1 AND lg11.admin0_grid_id = lg1.grid_id )
              #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
              AND lg1.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
              #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
              AND lg1.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

              # above admin 0 (22)

            UNION ALL
            --
            # admin 1 locations that have no level 2 (768)
            --
            SELECT
                lg2.grid_id, lg2.population, lg2.country_code, lg2.longitude, lg2.latitude,
                CONCAT_WS(', ',
                          IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                          IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                          IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                          IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                          IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                    ) as full_name
            FROM $wpdb->dt_location_grid lg2
                     LEFT JOIN $wpdb->dt_location_grid as gc ON lg2.admin0_grid_id=gc.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg2.admin1_grid_id=ga1.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg2.admin2_grid_id=ga2.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg2.admin3_grid_id=ga3.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg2.admin4_grid_id=ga4.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg2.admin5_grid_id=ga5.grid_id
            WHERE lg2.level = 1
              AND lg2.grid_id NOT IN ( SELECT lg22.admin1_grid_id FROM $wpdb->dt_location_grid lg22 WHERE lg22.level = 2 AND lg22.admin1_grid_id = lg2.grid_id )
              #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
              AND lg2.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
              #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
              AND lg2.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)


            UNION ALL
            --
            # admin 2 all countries (37100)
            --
            SELECT
                lg3.grid_id, lg3.population,  lg3.country_code, lg3.longitude, lg3.latitude,
                CONCAT_WS(', ',
                          IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                          IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                          IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                          IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                          IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                    ) as full_name
            FROM $wpdb->dt_location_grid lg3
                     LEFT JOIN $wpdb->dt_location_grid as gc ON lg3.admin0_grid_id=gc.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg3.admin1_grid_id=ga1.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg3.admin2_grid_id=ga2.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg3.admin3_grid_id=ga3.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg3.admin4_grid_id=ga4.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg3.admin5_grid_id=ga5.grid_id
            WHERE lg3.level = 2
              #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
              AND lg3.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
              #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
              AND lg3.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

            UNION ALL
            --
            # admin 1 for little highly divided countries (352)
            --
            SELECT
                lg4.grid_id, lg4.population,  lg4.country_code, lg4.longitude, lg4.latitude,
                CONCAT_WS(', ',
                          IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                          IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                          IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                          IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                          IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                    ) as full_name
            FROM $wpdb->dt_location_grid lg4
                     LEFT JOIN $wpdb->dt_location_grid as gc ON lg4.admin0_grid_id=gc.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg4.admin1_grid_id=ga1.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg4.admin2_grid_id=ga2.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg4.admin3_grid_id=ga3.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg4.admin4_grid_id=ga4.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg4.admin5_grid_id=ga5.grid_id
            WHERE lg4.level = 1
              #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
              AND lg4.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
              #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
              AND lg4.admin0_grid_id IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

            UNION ALL

            --
            # admin 3 for big countries (6153)
            --
            SELECT
                lg5.grid_id, lg5.population, lg5.country_code, lg5.longitude, lg5.latitude,
                CONCAT_WS(', ',
                          IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                          IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                          IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                          IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                          IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                    ) as full_name
            FROM $wpdb->dt_location_grid as lg5
                     LEFT JOIN $wpdb->dt_location_grid as gc ON lg5.admin0_grid_id=gc.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg5.admin1_grid_id=ga1.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg5.admin2_grid_id=ga2.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg5.admin3_grid_id=ga3.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg5.admin4_grid_id=ga4.grid_id
                     LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg5.admin5_grid_id=ga5.grid_id
            WHERE
                    lg5.level = 3
              #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
              AND lg5.admin0_grid_id IN (100050711,100219347, 100089589,100074576,100259978,100018514)
              #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
              AND lg5.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

       ", ARRAY_A );

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                $list[$result['grid_id']] = $result;
            }
        }

        set_transient( __METHOD__, $list, MONTH_IN_SECONDS );

        return $list;
    }

    public static function query_saturation_list_with_filters( $filters ) : array {

        $additional_where = '';
        if ( ! empty( $filters['bounds'] ) && is_array( $filters['bounds'] ) && $filters['zoom'] > 1.5 ) {
            if ( isset( $filters['bounds']['n_lat'] )
                && isset( $filters['bounds']['s_lat'] )
                && isset( $filters['bounds']['e_lng'] )
                && isset( $filters['bounds']['w_lng'] )
            ) {
                $additional_where .= "
                WHERE tb.longitude < ".$filters['bounds']['e_lng']."
                AND tb.longitude > ".$filters['bounds']['w_lng']."
                AND tb.latitude > ".$filters['bounds']['s_lat']."
                AND tb.latitude < ".$filters['bounds']['n_lat']."
                ";
            }
        }


        // 44141 records
        global $wpdb;
        $results = $wpdb->get_results("
            SELECT *
            FROM (
                SELECT
                    lg1.grid_id, lg1.population, lg1.country_code, lg1.longitude, lg1.latitude,
                    CONCAT_WS(', ',
                              IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                              IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                              IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                              IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                              IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                        ) as full_name
                FROM $wpdb->dt_location_grid lg1
                         LEFT JOIN $wpdb->dt_location_grid as gc ON lg1.admin0_grid_id=gc.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg1.admin1_grid_id=ga1.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg1.admin2_grid_id=ga2.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg1.admin3_grid_id=ga3.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg1.admin4_grid_id=ga4.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg1.admin5_grid_id=ga5.grid_id
                WHERE lg1.level = 0
                  AND lg1.grid_id NOT IN ( SELECT lg11.admin0_grid_id FROM $wpdb->dt_location_grid lg11 WHERE lg11.level = 1 AND lg11.admin0_grid_id = lg1.grid_id )
                  #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
                  AND lg1.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
                  #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
                  AND lg1.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

                  # above admin 0 (22)

                UNION ALL
                --
                # admin 1 locations that have no level 2 (768)
                --
                SELECT
                    lg2.grid_id, lg2.population, lg2.country_code, lg2.longitude, lg2.latitude,
                    CONCAT_WS(', ',
                              IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                              IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                              IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                              IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                              IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                        ) as full_name
                FROM $wpdb->dt_location_grid lg2
                         LEFT JOIN $wpdb->dt_location_grid as gc ON lg2.admin0_grid_id=gc.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg2.admin1_grid_id=ga1.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg2.admin2_grid_id=ga2.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg2.admin3_grid_id=ga3.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg2.admin4_grid_id=ga4.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg2.admin5_grid_id=ga5.grid_id
                WHERE lg2.level = 1
                  AND lg2.grid_id NOT IN ( SELECT lg22.admin1_grid_id FROM $wpdb->dt_location_grid lg22 WHERE lg22.level = 2 AND lg22.admin1_grid_id = lg2.grid_id )
                  #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
                  AND lg2.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
                  #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
                  AND lg2.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)


                UNION ALL
                --
                # admin 2 all countries (37100)
                --
                SELECT
                    lg3.grid_id, lg3.population,  lg3.country_code, lg3.longitude, lg3.latitude,
                    CONCAT_WS(', ',
                              IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                              IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                              IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                              IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                              IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                        ) as full_name
                FROM $wpdb->dt_location_grid lg3
                         LEFT JOIN $wpdb->dt_location_grid as gc ON lg3.admin0_grid_id=gc.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg3.admin1_grid_id=ga1.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg3.admin2_grid_id=ga2.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg3.admin3_grid_id=ga3.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg3.admin4_grid_id=ga4.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg3.admin5_grid_id=ga5.grid_id
                WHERE lg3.level = 2
                  #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
                  AND lg3.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
                  #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
                  AND lg3.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

                UNION ALL
                --
                # admin 1 for little highly divided countries (352)
                --
                SELECT
                    lg4.grid_id, lg4.population,  lg4.country_code, lg4.longitude, lg4.latitude,
                    CONCAT_WS(', ',
                              IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                              IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                              IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                              IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                              IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                        ) as full_name
                FROM $wpdb->dt_location_grid lg4
                         LEFT JOIN $wpdb->dt_location_grid as gc ON lg4.admin0_grid_id=gc.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg4.admin1_grid_id=ga1.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg4.admin2_grid_id=ga2.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg4.admin3_grid_id=ga3.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg4.admin4_grid_id=ga4.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg4.admin5_grid_id=ga5.grid_id
                WHERE lg4.level = 1
                  #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
                  AND lg4.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
                  #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
                  AND lg4.admin0_grid_id IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

                UNION ALL

                --
                # admin 3 for big countries (6153)
                --
                SELECT
                    lg5.grid_id, lg5.population, lg5.country_code, lg5.longitude, lg5.latitude,
                    CONCAT_WS(', ',
                              IF(LENGTH(ga4.alt_name),ga4.alt_name,NULL),
                              IF(LENGTH(ga3.alt_name),ga3.alt_name,NULL),
                              IF(LENGTH(ga2.alt_name),ga2.alt_name,NULL),
                              IF(LENGTH(ga1.alt_name),ga1.alt_name,NULL),
                              IF(LENGTH(gc.alt_name),gc.alt_name,NULL)
                        ) as full_name
                FROM $wpdb->dt_location_grid as lg5
                         LEFT JOIN $wpdb->dt_location_grid as gc ON lg5.admin0_grid_id=gc.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga1 ON lg5.admin1_grid_id=ga1.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga2 ON lg5.admin2_grid_id=ga2.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga3 ON lg5.admin3_grid_id=ga3.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga4 ON lg5.admin4_grid_id=ga4.grid_id
                         LEFT JOIN $wpdb->dt_location_grid as ga5 ON lg5.admin5_grid_id=ga5.grid_id
                WHERE
                        lg5.level = 3
                  #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
                  AND lg5.admin0_grid_id IN (100050711,100219347, 100089589,100074576,100259978,100018514)
                  #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
                  AND lg5.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
              ) as tb
              $additional_where

              ORDER BY latitude ASC

       ", ARRAY_A );

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                $list[$result['grid_id']] = $result;
            }
        }

        set_transient( __METHOD__, $list, MONTH_IN_SECONDS );

        return $list;
    }

    public static function query_flat_grid_by_level( $administrative_level, $us_div = 5000, $global_div = 50000 ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level . $us_div . $global_div ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;
        $wpdb->us_div = $us_div;
        $wpdb->global_div = $global_div;
        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY country
                    SELECT tb0.admin0_grid_id as grid_id, loc.name,loc.country_code, SUM(tb0.population) as population, SUM(tb0.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb0
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb0.admin0_grid_id=loc.grid_id
                    GROUP BY tb0.admin0_grid_id
                ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY state level
                    SELECT tb1.admin1_grid_id as grid_id, loc.name, loc.country_code, SUM(tb1.population) as population, SUM(tb1.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb1
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb1.admin1_grid_id=loc.grid_id
                    GROUP BY tb1.admin1_grid_id
                ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY county level
                    SELECT tb2.admin2_grid_id as grid_id, loc.name, loc.country_code, SUM(tb2.population) as population, SUM(tb2.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                 FROM $wpdb->dt_location_grid lg11
                                 WHERE lg11.level = 1
                               AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                 FROM $wpdb->dt_location_grid lg22
                                 WHERE lg22.level = 2
                               AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                    ) as tb2
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb2.admin2_grid_id=loc.grid_id
                    GROUP BY tb2.admin2_grid_id
                ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY sub-county level
                    SELECT tb3.admin3_grid_id as grid_id, loc.name, loc.country_code, SUM(tb3.population) as population, SUM(tb3.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb3
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb3.admin3_grid_id=loc.grid_id
                    WHERE tb3.admin3_grid_id IS NOT NULL
                    GROUP BY tb3.admin3_grid_id
                ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results("
                    # World
                    SELECT 1 as grid_id, 'World' as name,'' as country_code, SUM(tbw.population) as population, SUM(tbw.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 'World',
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tbw
                    LEFT JOIN $wpdb->dt_location_grid loc ON 1=loc.grid_id
                    GROUP BY 'World';
                ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results("
                    # 48367 Records
                    # 'Needs' GROUPED BY sub-county level
                    SELECT tb3.admin3_grid_id as grid_id, loc.name, loc.country_code, SUM(tb3.population) as population, SUM(tb3.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb3
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb3.admin3_grid_id=loc.grid_id
                    WHERE tb3.admin3_grid_id IS NOT NULL
                    GROUP BY tb3.admin3_grid_id

                    UNION ALL

                    # 'Needs' GROUPED BY county level
                    SELECT tb2.admin2_grid_id as grid_id, loc.name, loc.country_code, SUM(tb2.population) as population, SUM(tb2.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                 FROM $wpdb->dt_location_grid lg11
                                 WHERE lg11.level = 1
                               AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                 FROM $wpdb->dt_location_grid lg22
                                 WHERE lg22.level = 2
                               AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                    ) as tb2
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb2.admin2_grid_id=loc.grid_id
                    GROUP BY tb2.admin2_grid_id

                    UNION ALL

                    # 'Needs' GROUPED BY state level
                    SELECT tb1.admin1_grid_id as grid_id, loc.name, loc.country_code, SUM(tb1.population) as population, SUM(tb1.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb1
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb1.admin1_grid_id=loc.grid_id
                    GROUP BY tb1.admin1_grid_id

                    UNION ALL

                    # 'Needs' GROUPED BY country
                    SELECT tb0.admin0_grid_id as grid_id, loc.name,loc.country_code, SUM(tb0.population) as population, SUM(tb0.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb0
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb0.admin0_grid_id=loc.grid_id
                    GROUP BY tb0.admin0_grid_id

                    UNION ALL

                    # World
                    SELECT 1 as grid_id, 'World','' as country_code, SUM(tbw.population) as population, SUM(tbw.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 'World',
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tbw
                    LEFT JOIN $wpdb->dt_location_grid loc ON 1=loc.grid_id
                    GROUP BY 'World';
                ", ARRAY_A );
        }

        if ( empty( $results ) ) {
            return [];
        }

        set_transient( __METHOD__ . $administrative_level . $us_div . $global_div, $results, MONTH_IN_SECONDS );

        return $results;
    }

    public static function query_activity_grid_totals( $administrative_level = null ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t3
                    GROUP BY t3.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->dt_movement_log ml
                        LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id != 0
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION
                    SELECT t4.admin4_grid_id as grid_id, count(t4.admin4_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t4
                    GROUP BY t4.admin4_grid_id
                    UNION
                    SELECT t5.admin5_grid_id as grid_id, count(t5.admin5_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t5
                    GROUP BY t5.admin5_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->dt_movement_log ml
                        LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id != 0
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION
                    SELECT t4.admin4_grid_id as grid_id, count(t4.admin4_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t4
                    GROUP BY t4.admin4_grid_id
                    UNION
                    SELECT t5.admin5_grid_id as grid_id, count(t5.admin5_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t5
                    GROUP BY t5.admin5_grid_id

                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS );

        return $list;
    }

    public static function query_activity_location_grid_totals() {

        global $wpdb;

        $results = $wpdb->get_results( "
        SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t0
            GROUP BY t0.admin0_grid_id
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
            SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t3
            GROUP BY t3.admin3_grid_id;
        ", ARRAY_A );

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                $list[$result['grid_id']] = $result;
            }
        }

        return $list;
    }

    public static function query_training_grid_totals( $administrative_level = null ) {
        global $wpdb;

        // @note temp removed caching
//        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
//            return $value;
//        }

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function query_streams_grid_totals( $administrative_level = null ) {
//        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
//            return $value;
//        }
        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'streams'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function clear_practitioner_grid_totals() {
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totals' );
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totalsa0' );
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totalsa1' );
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totalsa2' );
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totalsa3' );
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totalsa4' );
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totalsworld' );
        delete_transient( 'Zume_App_Heatmap::query_practitioner_grid_totalsfull' );
    }

    public static function query_practitioner_grid_totals( $administrative_level = null ) {

//        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
//            return $value;
//        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id

                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                            SELECT 'World'
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                            JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                         ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full': // full query including world
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                        JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                             SELECT 'World'
                                FROM $wpdb->postmeta as pm
                                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                                JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                                WHERE pm.meta_key = 'location_grid'
                         ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                        SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                        FROM (
                         SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                            JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t0
                        GROUP BY t0.admin0_grid_id
                        UNION ALL
                        SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                            JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t1
                        GROUP BY t1.admin1_grid_id
                        UNION ALL
                        SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                            JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t2
                        GROUP BY t2.admin2_grid_id
                        UNION ALL
                        SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'overall_status' AND pm2.meta_value != 'closed'
                            JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'leader_milestones' AND pm3.meta_value = 'practicing'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t3
                        GROUP BY t3.admin3_grid_id
                        ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function clear_church_grid_totals() {
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totals' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa0' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa1' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa2' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa3' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa4' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsworld' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsfull' );
    }

    public static function query_church_grid_totals( $administrative_level = null ) {

//        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
//            return $value;
//        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id

                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                             SELECT 'World'
                             FROM $wpdb->postmeta as pm
                                      JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                                      JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                             WHERE pm.meta_key = 'location_grid'
                         ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full': // full query including world
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                             SELECT 'World'
                             FROM $wpdb->postmeta as pm
                                      JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                                      JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                             WHERE pm.meta_key = 'location_grid'
                         ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                        SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                        FROM (
                         SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t0
                        GROUP BY t0.admin0_grid_id
                        UNION ALL
                        SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t1
                        GROUP BY t1.admin1_grid_id
                        UNION ALL
                        SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t2
                        GROUP BY t2.admin2_grid_id
                        UNION ALL
                        SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t3
                        GROUP BY t3.admin3_grid_id
                        ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function query_grid_elements( $grid_id ) {
        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare( "
            SELECT
                   lg.admin3_grid_id as a3,
                   lg.admin2_grid_id as a2,
                   lg.admin1_grid_id as a1,
                   lg.admin0_grid_id as a0,
                   1 as world,
                   lg3.population as a3_population,
                   lg2.population as a2_population,
                   lg1.population as a1_population,
                   lg0.population as a0_population,
                   lgw.population as world_population,
                   lg.country_code
            FROM $wpdb->dt_location_grid lg
            LEFT JOIN $wpdb->dt_location_grid lg0 ON lg.admin0_grid_id=lg0.grid_id
            LEFT JOIN $wpdb->dt_location_grid lg1 ON lg.admin1_grid_id=lg1.grid_id
            LEFT JOIN $wpdb->dt_location_grid lg2 ON lg.admin2_grid_id=lg2.grid_id
            LEFT JOIN $wpdb->dt_location_grid lg3 ON lg.admin3_grid_id=lg3.grid_id
            LEFT JOIN $wpdb->dt_location_grid lgw ON 1=lgw.grid_id
            WHERE lg.grid_id = %s
        ", $grid_id ), ARRAY_A );

        return $result;
    }

    /**
     * Shared heatmap functions
     */
    public static function _header(){
        ?>
        <link rel="dns-prefetch" href="https://storage.googleapis.com/" >
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/1.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/2.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/3.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/4.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/5.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/6.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/7.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/8.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/9.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/10.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/11.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/12.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/13.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/14.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/15.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/16.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/17.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/18.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/19.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/20.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/21.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/22.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/23.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/24.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/25.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/26.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/27.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/28.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/29.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/30.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/31.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/32.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/33.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/34.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/35.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/36.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/37.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/38.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/39.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/40.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/41.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/42.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/43.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/44.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/45.geojson">
        <style>
            #initialize-screen {
                background-image: url("<?php echo esc_url( plugin_dir_url( __FILE__ ) ) ?>/images/initialize-background.jpg");
                background-size:cover;
            }
        </style>
        <?php
        wp_head();
    }

    /**
     * Grid list build initial map list of elements and drives sidebar
     * @return array
     */
    public static function _initial_polygon_value_list( $grid_totals, $global_div, $us_div ){
        $flat_grid = self::query_saturation_list();

        $data = [];
        $highest_value = 1;
        foreach ( $flat_grid as $i => $v ){
            $data[$i] = [
                'grid_id' => $i,
                'population' => number_format_i18n( $v['population'] ),
                'needed' => 1,
                'reported' => 0,
                'percent' => 0,
            ];

            $population_division = self::_get_population_division( $v['country_code'], $global_div, $us_div );

            $needed = round( $v['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }

            if ( isset( $grid_totals[$v['grid_id']] ) && ! empty( $grid_totals[$v['grid_id']] ) ){
                $reported = $grid_totals[$v['grid_id']];

                if ( ! empty( $reported ) && ! empty( $needed ) ){
                    $data[$v['grid_id']]['needed'] = $needed;

                    $data[$v['grid_id']]['reported'] = $reported;
                    $percent = ceil( $reported / $needed * 100 );
                    if ( 100 < $percent ) {
                        $percent = 100;
                    } else {
                        $percent = number_format_i18n( $percent, 2 );
                    }

                    $data[$v['grid_id']]['percent'] = $percent;
                }
            }
            else {
                $data[$v['grid_id']]['percent'] = 0;
                $data[$v['grid_id']]['reported'] = 0;
                $data[$v['grid_id']]['needed'] = $needed;
            }

            if ( $highest_value < $data[$v['grid_id']]['reported'] ){
                $highest_value = $data[$v['grid_id']]['reported'];
            }
        }

        return [
            'highest_value' => (int) $highest_value,
            'data' => $data
        ];
    }

    public static function _wp_enqueue_scripts(){
        wp_enqueue_script( 'lodash' );
        wp_enqueue_script( 'jquery-ui' );
        wp_enqueue_script( 'jquery-touch-punch' );

        wp_enqueue_script( 'heatmap-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.js', [
            'jquery',
            'mapbox-cookie',
            'jquery-cookie'
        ], filemtime( plugin_dir_path( __FILE__ ) .'heatmap.js' ), true );

        wp_enqueue_style( 'heatmap-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.css', [], filemtime( plugin_dir_path( __FILE__ ) .'heatmap.css' ) );

        wp_enqueue_script( 'jquery-cookie', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js.cookie.min.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'js.cookie.min.js' ), true );

        wp_enqueue_script( 'mapbox-cookie', trailingslashit( get_stylesheet_directory_uri() ) . 'dt-mapping/geocode-api/mapbox-cookie.js', [ 'jquery', 'jquery-cookie' ], '3.0.0' );
    }

    public static function get_self( $grid_id, $global_div, $us_div ) {
        global $wpdb;

        // get grid elements for design
        $grid = $wpdb->get_row( $wpdb->prepare( "
            SELECT
              g.grid_id,
              g.level,
              g.alt_name as name,
              gn.alt_name as parent_name,
              g.country_code,
              g.population,
              IF(ROUND(g.population / IF(g.country_code = 'US', %d, %d)) < 1, 1,
                 ROUND(g.population / IF(g.country_code = 'US', %d, %d))) as needed,
              (SELECT COUNT(prs.grid_id) FROM $wpdb->dt_location_grid as prs WHERE prs.parent_id = g.parent_id ) as peers
            FROM $wpdb->dt_location_grid as g
            LEFT JOIN $wpdb->dt_location_grid as gn ON g.parent_id=gn.grid_id
            WHERE g.grid_id = %s
        ", $us_div, $global_div, $us_div, $global_div, $grid_id ), ARRAY_A );

        // set array
        $population_division = self::_get_population_division( $grid['country_code'], $global_div, $us_div );
        $data = [
            'level' => $grid['level'],
            'parent_level' => $grid['level'] - 1, // one level higher than current
            'population_division' => number_format_i18n( $population_division ), // label for content not calculation
            'population_division_int' => $population_division, // label for content not calculation
            'name' => $grid['name'],
            'parent_name' => $grid['parent_name'],
            'peers' => number_format_i18n( $grid['peers'] ),
            'population' => number_format_i18n( $grid['population'] ),
            'needed' => number_format_i18n( $grid['needed'] ),
        ];

        return $data;
    }

    public static function endpoint_get_level( $grid_id, $administrative_level, $list, $global_div, $us_div ) {
        // add levels
        $flat_grid = self::query_flat_grid_by_level( $administrative_level, $us_div, $global_div );
        $flat_grid_limited = self::_limit_counts( $flat_grid, $list ); // limit counts to no larger than needed per location.

        $grid = self::query_grid_elements( $grid_id ); // get level ids for grid_id

        if ( isset( $flat_grid_limited[$grid[$administrative_level]] ) && ! empty( $flat_grid_limited[$grid[$administrative_level]] ) ) {
            $level = $flat_grid_limited[$grid[$administrative_level]];
        }
        else {
            return false;
        }

        $percent = $level['reported'] / $level['needed'] * 100;
        if ( 100 < $percent ) {
            $percent = 100;
        } else {
            $percent = number_format_i18n( $percent, 2 );
        }

        if ( isset( $flat_grid[$grid[$administrative_level]] ) && ! empty( $flat_grid[$grid[$administrative_level]] ) ) {
            $raw_level = $flat_grid[$grid[$administrative_level]];
            $raw_reported = $raw_level['reported'];
        } else {
            $raw_reported = $level['reported'];
        }

        /**
         * @todo temp cover for populations
         */
        if ( isset( $grid[$administrative_level . '_population'] )
            && ! empty( $grid[$administrative_level . '_population'] )
            && in_array( $administrative_level, [ 'a0', 'world' ] ) ) {
            $level['population'] = $grid[$administrative_level . '_population'];

            $population_division = self::_get_population_division( $grid['country_code'], $global_div, $us_div );
            $needed = round( $level['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }
            $level['needed'] = $needed;
            if ( $administrative_level === 'world' ) {
                $world_population = 7974493405;
                $us_population = 335701430;
                $global_pop_block = $global_div;
                $us_pop_block = $us_div;
                $world_population_without_us = $world_population - $us_population;
                $needed_without_us = $world_population_without_us / $global_pop_block;
                $needed_in_the_us = $us_population / $us_pop_block;
                $level['needed'] = $needed_without_us + $needed_in_the_us;
                $percent = $level['reported'] / $level['needed'] * 100;
            }
        }
        // @todo end temp cover for populations

        if ( empty( $level['name'] ) ) {
            return false;
        }

        $data = [
            'name' => $level['name'],
            'grid_id' => (int) $level['grid_id'],
            'population' => number_format_i18n( $level['population'] ),
            'needed' => number_format_i18n( $level['needed'] ),
            'reported' => number_format_i18n( $raw_reported ),
            'percent' => number_format_i18n( $percent, 2 ),
        ];

        return $data;
    }

    public static function endpoint_get_activity_level( $grid_id, $administrative_level, $list, $global_div, $us_div ) {

        $flat_grid = self::query_flat_grid_by_level( $administrative_level, $us_div, $global_div );
//        $flat_grid_limited = self::_limit_counts( $flat_grid, $list ); // limit counts to no larger than needed per location.
        $flat_grid_limited = [];
        foreach ( $flat_grid as $value ) {
            $flat_grid_limited[$value['grid_id']] = $value;

            if ( isset( $list[$value['grid_id']] ) && ! empty( $list[$value['grid_id']] ) ) {
                $flat_grid_limited[$value['grid_id']]['reported'] = $list[$value['grid_id']];
//                if ( $list[$value['grid_id']] <= $value['needed'] ) {
//                    $flat_grid_limited[$value['grid_id']]['reported'] = $list[$value['grid_id']];
//                } else {
//                    $flat_grid_limited[$value['grid_id']]['reported'] = $value['needed'];
//                }
            }
        }

        $grid = self::query_grid_elements( $grid_id ); // get level ids for grid_id

        if ( isset( $flat_grid_limited[$grid[$administrative_level]] ) && ! empty( $flat_grid_limited[$grid[$administrative_level]] ) ) {
            $level = $flat_grid_limited[$grid[$administrative_level]];
        }
        else {
            return false;
        }

//        $percent = $level['reported'] / $level['needed'] * 100;
//        if ( 100 < $percent ) {
//            $percent = 100;
//        } else {
//            $percent = number_format_i18n( $percent, 2 );
//        }

        if ( isset( $flat_grid[$grid[$administrative_level]] ) && ! empty( $flat_grid[$grid[$administrative_level]] ) ) {
            $raw_level = $flat_grid[$grid[$administrative_level]];
            $raw_reported = $raw_level['reported'];
        } else {
            $raw_reported = $level['reported'];
        }

        /**
         * @todo temp cover for populations
         */
        if ( isset( $grid[$administrative_level . '_population'] )
            && ! empty( $grid[$administrative_level . '_population'] )
            && in_array( $administrative_level, [ 'a0', 'world' ] ) ) {
            $level['population'] = $grid[$administrative_level . '_population'];

            $population_division = self::_get_population_division( $grid['country_code'], $global_div, $us_div );
            $needed = round( $level['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }
            $level['needed'] = $needed;
            if ( $administrative_level === 'world' ) {
                $world_population = 7974493405;
                $us_population = 335701430;
                $global_pop_block = $global_div;
                $us_pop_block = $us_div;
                $world_population_without_us = $world_population - $us_population;
                $needed_without_us = $world_population_without_us / $global_pop_block;
                $needed_in_the_us = $us_population / $us_pop_block;
                $level['needed'] = $needed_without_us + $needed_in_the_us;
                $percent = $level['reported'] / $level['needed'] * 100;
            }
        }
        // @todo end temp cover for populations

        if ( empty( $level['name'] ) ) {
            return false;
        }

        $data = [
            'name' => $level['name'],
            'grid_id' => (int) $level['grid_id'],
            'population' => number_format_i18n( $level['population'] ),
//            'needed' => number_format_i18n( $level['needed'] ),
            'reported' => number_format_i18n( $raw_reported ),
//            'percent' => number_format_i18n( $percent, 2 ),
        ];

        return $data;
    }

    /**
     * Function limits counts to no higher than the location need. This keeps from inflating the counts up the levels.
     * @param $flat_grid
     * @param $list
     * @return array
     */
    public static function _limit_counts( $flat_grid, $list ) {
        $flat_grid_limited = [];
        foreach ( $flat_grid as $value ) {
            $flat_grid_limited[$value['grid_id']] = $value;

            if ( isset( $list[$value['grid_id']] ) && ! empty( $list[$value['grid_id']] ) ) {
                if ( $list[$value['grid_id']] <= $value['needed'] ) {
                    $flat_grid_limited[$value['grid_id']]['reported'] = $list[$value['grid_id']];
                } else {
                    $flat_grid_limited[$value['grid_id']]['reported'] = $value['needed'];
                }
            }
        }
        return $flat_grid_limited;
    }

    public static function _get_population_division( $country_code, $global_div, $us_div ){
        $population_division = $global_div;
        if ( $country_code === 'US' ){
            $population_division = $us_div;
        }
        return $population_division;
    }

    public static function query_activity_data( $grid_id, $offset ) {
        global $wpdb;
        $ids = [];
        $ids[] = $grid_id;
        $children = Disciple_Tools_Mapping_Queries::get_children_by_grid_id( $grid_id );
        if ( ! empty( $children ) ) {
            foreach ( $children as $child ){
                $ids[] = $child['grid_id'];
            }
        }
        $prepared_list = dt_array_to_sql( $ids );
        // phpcs:disable
        $list = $wpdb->get_results("
                SELECT
                       id,
                       action,
                       category,
                       lng,
                       lat,
                       label,
                       grid_id,
                       payload,
                       timestamp,
                       'A Zúme partner' as site_name
                FROM $wpdb->dt_movement_log
                WHERE grid_id IN ($prepared_list)
                ORDER BY timestamp DESC", ARRAY_A);
        // phpcs:enable
        if ( empty( $list ) ){
            return [];
        }

        foreach ( $list as $index => $item ){
            $list[$index]['payload'] = maybe_unserialize( $item['payload'] );
            $list[$index]['formatted_time'] = gmdate( 'M, d Y, g:i a', $item['timestamp'] + $offset );
        }

        if ( function_exists( 'zume_log_actions' ) ) {
            $list = zume_log_actions( $list );
        }
        if ( function_exists( 'dt_network_dashboard_translate_log_generations' ) ) {
            $list = dt_network_dashboard_translate_log_generations( $list );
        }
        if ( function_exists( 'dt_network_dashboard_translate_log_new_posts' ) ) {
            $list = dt_network_dashboard_translate_log_new_posts( $list );
        }

        $list = apply_filters( 'dt_network_dashboard_build_message', $list );

        foreach ( $list as $index => $item ){
            if ( ! isset( $item['message'] ) ) {
                $list[$index]['message'] = 'Non-public movement event reported.';
            }
        }

        return $list;
    }

    public static function query_activity_list() {
        global $wpdb;
        $timestamp = strtotime( '-100 hours' );
        $results = $wpdb->get_results( $wpdb->prepare( "
                SELECT ml.action, ml.category, ml.lng, ml.lat, ml.label, ml.payload, ml.timestamp, lga0.name as country_name, lga0.country_code
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                LEFT JOIN $wpdb->dt_location_grid lga0 ON lga0.grid_id=lg.admin0_grid_id
                WHERE ml.timestamp > %d
                ORDER BY ml.timestamp DESC
                ", $timestamp), ARRAY_A );

        foreach ( $results as $i => $v ) {
            $t = self::_get_type( $v['action'] );
            if ( empty( $t ) ) {
                unset( $results[$i] );
            } else {
                $results[$i]['type'] = $t;
            }
        }

        return $results;
    }

    public static function create_initials( $longitude, $latitude, $payload ) : string {
        $letters = [
            'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'N',
        'S',
        'M',
        'N',
        'O',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'E',
        'A',
        'R',
        'I',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'D',
        'E',
        'M',
        'N',
        'O',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'E',
        'A',
        'R',
        'I',
        'E',
        'A',
        'R',
        'I',
        'T',
        'N',
        'S',
        'L',
        'E',
        'A',
        'R',
        'I',
        'N',
        'S',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'C',
        'D',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'A',
        'B',
        'M',
        'N',
        'O',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'E',
        'A',
        'R',
        'I',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'A',
        'B',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'E',
        'A',
        'R',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'N',
        'S',
        'M',
        'N',
        'O',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'E',
        'A',
        'R',
        'I',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'D',
        'E',
        'M',
        'N',
        'O',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'E',
        'A',
        'R',
        'I',
        'E',
        'A',
        'R',
        'I',
        'T',
        'N',
        'S',
        'L',
        'E',
        'A',
        'R',
        'I',
        'N',
        'S',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'C',
        'D',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'A',
        'B',
        'E',
        'A',
        'R',
        'I',
        'T',
        'N',
        'S',
        'L',
        'E',
        'A',
        'R',
        'I',
        'N',
        'S',
        'M',
        'N',
        'O',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'E',
        'A',
        'R',
        'I',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'N',
        'S',
        ];
        $fnum = abs( round( $longitude ) );
        $lnum = abs( round( $latitude ) );
        $list = str_split( hash( 'sha256', maybe_serialize( $payload ) ) );
        foreach ( $list as $character ){
            if ( is_numeric( $character ) ) {
                $fnum = $fnum + $character;
                $lnum = $lnum + $character;
                break;
            }
        }
        return strtoupper( $letters[$fnum] . $letters[$lnum] );
    }

    public static function create_time_string( $timestamp, $timezone_offset ) : string {
        $adjusted_time = $timestamp + $timezone_offset;
        if ( $timestamp > strtotime( '-1 hour' ) ) {
            $time_string = self::_time_elapsed_string( '@'.$timestamp );
        }
        else if ( $timestamp > strtotime( 'today+00:00' ) + $timezone_offset ) {
            $time_string = date( 'g:i a', $adjusted_time ); // @phpcs:ignore
        }
        else {
            $time_string = date( 'D g:i a', $adjusted_time ); // @phpcs:ignore
        }
        return $time_string;
    }

    public static function create_in_language_string( $payload ) : string {
        $in_language = '';
        if ( isset( $payload['language_name'] ) && ! empty( $payload['language_name'] ) && 'English' !== $payload['language_name'] ) {
            $in_language = ' in ' . $payload['language_name'];
        }
        return $in_language;
    }

    public static function create_location_precision( $lng, $lat, $label, $payload ) : array {
        $location = [
            'lng' => $lng,
            'lat' => $lat,
            'label' => $label
        ];

        // @todo remove the precision reduction

//        $restricted = self::_persecuted_countries();
//
//        if ( ! isset( $payload['country'] ) ) { // if country is not set, reduce precision to 111km
//            $location['lng'] = round( $location['lng'], 0 );
//            $location['lat'] = round( $location['lat'], 0 );
//            $location['label'] = '';
//        }
//        else if ( in_array( $payload['country'], $restricted ) ) { // if persecuted country, reduce precision to 111km
//            $location['label'] = ' (' . $payload['country'] . ')';
//            $location['lng'] = round( $location['lng'], 0 );
//            $location['lat'] = round( $location['lat'], 0 );
//        } else { // if non-persecuted country, reduce precision to 11km
//            $location['label'] = ' (' . $location['label'] . ')';
//            $location['lng'] = round( $location['lng'], 3 );
//            $location['lat'] = round( $location['lat'], 3 );
//        }

        return $location;
    }

    public static function _persecuted_countries() : array {

        // Top 50 most persecuted countries
        // @link https://www.opendoorsusa.org/christian-persecution/world-watch-list/

        return [
            'North Korea',
            'Afghanistan',
            'Somolia',
            'Libya',
            'Pakistan',
            'Eritrea',
            'Sudan',
            'Yemen',
            'Iran',
            'India',
            'Syria',
            'Nigeria',
            'Saudi Arabia',
            'Maldives',
            'Iraq',
            'Egypt',
            'Algeria',
            'Uzbekistan',
            'Myanmar',
            'Laos',
            'Vietnam',
            'Turkmenistan',
            'China',
            'Mauritania',
            'Central African Republic',
            'Morocco',
            'Qatar',
            'Burkina Faso',
            'Mali',
            'Sri Lanka',
            'Tajikistan',
            'Nepal',
            'Jordan',
            'Tunisia',
            'Kazakhstan',
            'Turkey',
            'Brunei',
            'Bangladesh',
            'Ethiopia',
            'Malaysia',
            'Colombia',
            'Oman',
            'Kuwait',
            'Kenya',
            'Bhutan',
            'Russian Federation',
            'United Arab Emirates',
            'Cameroon',
            'Indonesia',
            'Niger'
        ];
    }

    public static function create_note_data( $action, $string_elements, $payload ) : array {
        // learning
        // joining
        // producing

        $initials = $string_elements['initials'] ?? '';
        $in_language = $string_elements['in_language'] ?? '';
        $location_label =' (' . $string_elements['location_label'] . ') ' ?? '';

        $data = [
            'note' => '',
            'type' => 'learning',
        ];

        switch ( $action ) {
            case 'starting_group':
                $data['note'] = $initials . ' is starting a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
                break;
            case 'building_group':
                $data['note'] = $initials . ' is growing a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
                break;
            case 'leading_1':
            case 'leading_2':
            case 'leading_3':
            case 'leading_4':
            case 'leading_5':
            case 'leading_6':
            case 'leading_7':
            case 'leading_8':
            case 'leading_9':
            case 'leading_10':
                if ( isset( $payload['group_size'] ) && $payload['group_size'] > 1 ) {
                    $data['note'] = $initials . ' is leading a group of '. $payload['group_size'] .' through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                } else {
                    $data['note'] = $initials . ' is leading a group through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                }
                $data['type'] = 'producing';
                break;
            case 'zume_training':
                $data['note'] = $initials . ' is registering for Zúme training' . $in_language . '! ' . $location_label;
                $data['type'] = 'joining';
                break;
            case 'zume_vision':
                $data['note'] = $initials . ' is joining the Zúme community to engage in Disciple Making Movements' . $in_language . '! ' . $location_label;
                $data['type'] = 'joining';
                break;
            case 'coaching':
                $data['note'] = $initials . ' is requesting coaching from Zúme coaches' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
                break;
            case 'studying_1':
            case 'studying_2':
            case 'studying_3':
            case 'studying_4':
            case 'studying_5':
            case 'studying_6':
            case 'studying_7':
            case 'studying_8':
            case 'studying_9':
            case 'studying_10':
            case 'studying_11':
            case 'studying_12':
            case 'studying_13':
            case 'studying_14':
            case 'studying_15':
            case 'studying_16':
            case 'studying_17':
            case 'studying_18':
            case 'studying_19':
            case 'studying_20':
            case 'studying_21':
            case 'studying_22':
            case 'studying_23':
            case 'studying_24':
            case 'studying_25':
            case 'studying_26':
            case 'studying_27':
            case 'studying_28':
            case 'studying_29':
            case 'studying_30':
            case 'studying_31':
            case 'studying_32':
            case 'studying_offline_1':
            case 'studying_offline_2':
            case 'studying_offline_3':
            case 'studying_offline_4':
            case 'studying_offline_5':
            case 'studying_offline_6':
            case 'studying_offline_7':
            case 'studying_offline_8':
            case 'studying_offline_9':
            case 'studying_offline_10':
            case 'studying_offline_11':
            case 'studying_offline_12':
            case 'studying_offline_13':
            case 'studying_offline_14':
            case 'studying_offline_15':
            case 'studying_offline_16':
            case 'studying_offline_17':
            case 'studying_offline_18':
            case 'studying_offline_19':
            case 'studying_offline_20':
            case 'studying_offline_21':
            case 'studying_offline_22':
            case 'studying_offline_23':
            case 'studying_offline_24':
            case 'studying_offline_25':
            case 'studying_offline_26':
            case 'studying_offline_27':
            case 'studying_offline_28':
            case 'studying_offline_29':
            case 'studying_offline_30':
            case 'studying_offline_31':
            case 'studying_offline_32':
                $title = ' disciple making movement principles';
                if ( isset( $payload['title'] ) && ! empty( $payload['title'] ) ) {
                    $title = ' "' . $payload['title'] . '"';
                }
                $data['note'] = $initials . ' is studying' . $title . $in_language . '! ' . $location_label;
                $data['type'] = 'learning';
                break;
            case 'updated_3_month':
                $data['note'] = $initials . '  made a three month plan to multiply disciples' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
                break;
            default:
                break;
        }

        return $data;
    }

    public static function _time_elapsed_string( $datetime, $full = false ) {
        $now = new DateTime();
        $ago = new DateTime( $datetime );
        $diff = $now->diff( $ago );

        $diff->w = floor( $diff->d / 7 );
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ( $diff->$k > 1 ? 's' : '' );
            } else {
                unset( $string[$k] );
            }
        }

        if ( !$full) { $string = array_slice( $string, 0, 1 );
        }
        return $string ? implode( ', ', $string ) . ' ago' : 'just now';
    }

    public static function _get_type( $action ) {

        switch ( $action ) {
            case 'coaching':
            case 'updated_3_month':
            case 'starting_group':
            case 'building_group':
            case 'leading_1':
            case 'leading_2':
            case 'leading_3':
            case 'leading_4':
            case 'leading_5':
            case 'leading_6':
            case 'leading_7':
            case 'leading_8':
            case 'leading_9':
            case 'leading_10':
                return 'producing';
            case 'zume_training':
            case 'zume_vision':
                return 'joining';
            case 'studying_1':
            case 'studying_2':
            case 'studying_3':
            case 'studying_4':
            case 'studying_5':
            case 'studying_6':
            case 'studying_7':
            case 'studying_8':
            case 'studying_9':
            case 'studying_10':
            case 'studying_11':
            case 'studying_12':
            case 'studying_13':
            case 'studying_14':
            case 'studying_15':
            case 'studying_16':
            case 'studying_17':
            case 'studying_18':
            case 'studying_19':
            case 'studying_20':
            case 'studying_21':
            case 'studying_22':
            case 'studying_23':
            case 'studying_24':
            case 'studying_25':
            case 'studying_26':
            case 'studying_27':
            case 'studying_28':
            case 'studying_29':
            case 'studying_30':
            case 'studying_31':
            case 'studying_32':
            case 'studying_offline_1':
            case 'studying_offline_2':
            case 'studying_offline_3':
            case 'studying_offline_4':
            case 'studying_offline_5':
            case 'studying_offline_6':
            case 'studying_offline_7':
            case 'studying_offline_8':
            case 'studying_offline_9':
            case 'studying_offline_10':
            case 'studying_offline_11':
            case 'studying_offline_12':
            case 'studying_offline_13':
            case 'studying_offline_14':
            case 'studying_offline_15':
            case 'studying_offline_16':
            case 'studying_offline_17':
            case 'studying_offline_18':
            case 'studying_offline_19':
            case 'studying_offline_20':
            case 'studying_offline_21':
            case 'studying_offline_22':
            case 'studying_offline_23':
            case 'studying_offline_24':
            case 'studying_offline_25':
            case 'studying_offline_26':
            case 'studying_offline_27':
            case 'studying_offline_28':
            case 'studying_offline_29':
            case 'studying_offline_30':
            case 'studying_offline_31':
            case 'studying_offline_32':
                return 'learning';
            default:
                return false;
        }
    }

    public static function get_activity_list( $filters, $limit = false ) {
        global $wpdb;

        $utc_time = new DateTime( 'now', new DateTimeZone( $filters['timezone'] ) );
        $timezone_offset = $utc_time->format( 'Z' );

        $additional_where = '';
        if ( ! empty( $filters['bounds'] ) && is_array( $filters['bounds'] ) && $filters['zoom'] > 1.5 ) {
            if ( isset( $filters['bounds']['n_lat'] )
                && isset( $filters['bounds']['s_lat'] )
                && isset( $filters['bounds']['e_lng'] )
                && isset( $filters['bounds']['w_lng'] )
            ) {
                $additional_where .= "
                AND lng < ".$filters['bounds']['e_lng']."
                AND lng > ".$filters['bounds']['w_lng']."
                AND lat > ".$filters['bounds']['s_lat']."
                AND lat < ".$filters['bounds']['n_lat']."
                ";
            }
        }

        if ( 'none' !== $filters['country'] ) {
            $additional_where .= " AND lga0.country_code = '" .$filters['country']. "'";
        }

        $timestamp = strtotime( '-100 hours' );
        // @phpcs:disable
        $results = $wpdb->get_results( "
                SELECT ml.action, ml.category, ml.lng, ml.lat, ml.label, ml.payload, ml.timestamp, lga0.name as country_name, lga0.country_code
                FROM $wpdb->dt_movement_log as ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                LEFT JOIN $wpdb->dt_location_grid lga0 ON lga0.grid_id=lg.admin0_grid_id
                WHERE ml.timestamp > $timestamp
                $additional_where
                ORDER BY ml.timestamp DESC
                ", ARRAY_A );
        // @phpcs:enable

        foreach ( $results as $i => $v ) {
            $t = self::_get_type( $v['action'] );
            if ( empty( $t ) ) {
                unset( $results[$i] );
            } else {
                $results[$i]['type'] = $t;
            }
        }

        foreach ( $results as $result ) {

            $payload = maybe_unserialize( $result['payload'] );

            // BUILD NOTE
            $string_elements = [];

            // time string
            $time_string = self::create_time_string( $result['timestamp'], $timezone_offset );

            // language
            $string_elements['in_language'] = self::create_in_language_string( $payload );

            // initials string
            $string_elements['initials'] = self::create_initials( $result['lng'], $result['lat'], $payload );

            // location string
            $location = self::create_location_precision( $result['lng'], $result['lat'], $result['label'], $payload );
            $string_elements['location_label'] = $location['label'];

            // note and type data
            $data = self::create_note_data( $result['action'], $string_elements, $payload );

            $prepared_array = array(
                "note" => esc_html( $data['note'] ),
                "time" => esc_attr( $time_string ),
                "type" => esc_attr( $result['type'] ),
                "language" => esc_attr( $payload['language_code'] ?? '' ),
                "country" => esc_attr( $result['country_code'] )
            );

            // filter out non selected country
            // no filter set
            if ( 'none' === $filters['country'] && 'none' === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country set
            else if ( $prepared_array['country'] === $filters['country'] && 'none' === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // language set
            else if ( 'none' === $filters['country'] && $prepared_array['language'] === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // type set
            else if ( 'none' === $filters['country'] && 'none' === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // language & type set
            else if ( 'none' === $filters['country'] && $prepared_array['language'] === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country & type set
            else if ( $prepared_array['country'] === $filters['country'] && 'none' === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country & language set
            else if ( $prepared_array['country'] === $filters['country'] && $prepared_array['language'] === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country & language & type set
            else if ( $prepared_array['country'] === $filters['country'] && $prepared_array['language'] === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }
        } // end foreach loop

        if ( empty( $list ) ) {
            return [
                'list' => [],
                'count' => 0
            ];
        }

        $c = array_chunk( $list, 250 );
        return [
            'list' => $c[0] ?? $list,
            'count' => count( $list )
        ];
    }

    public static function get_activity_geojson() {
        $results = self::query_activity_list();
        if ( empty( $results ) ) {
            $results = [];
        }

        $countries = [];
        $languages = [];
        $types = [];




        $features = [];
        foreach ( $results as $result ) {

            $payload = maybe_unserialize( $result['payload'] );

            // count country
            if ( isset( $result['country_code'] ) && !empty( $result['country_code'] ) && ! isset( $countries[$result['country_name']] ) ) {
                $countries[$result['country_name']] = [
                    'code' => $result['country_code'],
                    'name' => $result['country_name'],
                    'count' => 0
                ];
            }
            if ( isset( $result['country_code'] ) ) {
                $countries[$result['country_name']]['count']++;
            }

            // count language
            if ( isset( $payload['language_name'] )
                && isset( $payload['language_code'] )
                && ! isset( $languages[$payload['language_name']] )
            ) {
                $languages[$payload['language_name']] = [
                    'code' => $payload['language_code'],
                    'name' => $payload['language_name'],
                    'count' => 0
                ];
            }
            if ( isset( $payload['language_name'] ) ) {
                $languages[$payload['language_name']]['count']++;
            }

            // count types
            if ( isset( $result['type'] ) && ! empty( $result['type'] ) )
            {
                if ( ! isset( $types[$result['type']] ) ) {
                    $types[$result['type']] = [
                        'code' => $result['type'],
                        'name' => ucwords( $result['type'] ),
                        'count' => 0
                    ];
                }
                $types[$result['type']]['count']++;
            }

            // reduce lng to 1.1 km
            $lng = round( $result['lng'], 2 );
            $lat = round( $result['lat'], 2 );

            $features[] = array(
                'type' => 'Feature',
                'properties' => [
                    'type' => $result['type'] ?? '',
                    'language' => $payload['language_code'] ?? '',
                    'country' => $result['country_code']
                ],
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => array(
                        $lng,
                        $lat,
                        1
                    ),
                ),
            );

        } // end foreach loop

        ksort( $countries );
        ksort( $languages );

        $new_data = array(
            'type' => 'FeatureCollection',
            'countries' => $countries,
            'countries_count' => count( $countries ),
            'languages' => $languages,
            'languages_count' => count( $languages ),
            'types' => $types,
            'total' => count( $results ),
            'features' => $features,
        );

        return $new_data;
    }
}


/**
 * REGISTER ACTIONS (AND CATEGORIES)
 */
add_action( 'dt_network_dashboard_register_actions', 'dt_network_dashboard_register_action_zume_ftt_public_keys', 30, 1 );
function dt_network_dashboard_register_action_zume_ftt_public_keys( $actions ){

    $actions['studying_1'] = [
        'key' => 'studying_1',
        'label' => 'Studying 1',
        'message_pattern' => []
    ];
    $actions['studying_2'] = [
        'key' => 'studying_2',
        'label' => 'Studying 2',
        'message_pattern' => []
    ];
    $actions['studying_3'] = [
        'key' => 'studying_3',
        'label' => 'Studying 3',
        'message_pattern' => []
    ];
    $actions['studying_4'] = [
        'key' => 'studying_4',
        'label' => 'Studying 4',
        'message_pattern' => []
    ];
    $actions['studying_5'] = [
        'key' => 'studying_5',
        'label' => 'Studying 5',
        'message_pattern' => []
    ];
    $actions['studying_6'] = [
        'key' => 'studying_6',
        'label' => 'Studying 6',
        'message_pattern' => []
    ];
    $actions['studying_7'] = [
        'key' => 'studying_7',
        'label' => 'Studying 7',
        'message_pattern' => []
    ];
    $actions['studying_8'] = [
        'key' => 'studying_8',
        'label' => 'Studying 8',
        'message_pattern' => []
    ];
    $actions['studying_9'] = [
        'key' => 'studying_9',
        'label' => 'Studying 9',
        'message_pattern' => []
    ];
    $actions['studying_10'] = [
        'key' => 'studying_10',
        'label' => 'Studying 10',
        'message_pattern' => []
    ];
    $actions['studying_11'] = [
        'key' => 'studying_11',
        'label' => 'Studying 11',
        'message_pattern' => []
    ];
    $actions['studying_12'] = [
        'key' => 'studying_12',
        'label' => 'Studying 12',
        'message_pattern' => []
    ];
    $actions['studying_13'] = [
        'key' => 'studying_13',
        'label' => 'Studying 13',
        'message_pattern' => []
    ];
    $actions['studying_14'] = [
        'key' => 'studying_14',
        'label' => 'Studying 14',
        'message_pattern' => []
    ];
    $actions['studying_15'] = [
        'key' => 'studying_15',
        'label' => 'Studying 15',
        'message_pattern' => []
    ];
    $actions['studying_16'] = [
        'key' => 'studying_16',
        'label' => 'Studying 16',
        'message_pattern' => []
    ];
    $actions['studying_17'] = [
        'key' => 'studying_17',
        'label' => 'Studying 17',
        'message_pattern' => []
    ];
    $actions['studying_18'] = [
        'key' => 'studying_18',
        'label' => 'Studying 18',
        'message_pattern' => []
    ];
    $actions['studying_19'] = [
        'key' => 'studying_19',
        'label' => 'Studying 19',
        'message_pattern' => []
    ];
    $actions['studying_20'] = [
        'key' => 'studying_20',
        'label' => 'Studying 20',
        'message_pattern' => []
    ];
    $actions['studying_21'] = [
        'key' => 'studying_21',
        'label' => 'Studying 21',
        'message_pattern' => []
    ];
    $actions['studying_22'] = [
        'key' => 'studying_22',
        'label' => 'Studying 22',
        'message_pattern' => []
    ];
    $actions['studying_23'] = [
        'key' => 'studying_23',
        'label' => 'Studying 23',
        'message_pattern' => []
    ];
    $actions['studying_24'] = [
        'key' => 'studying_24',
        'label' => 'Studying 24',
        'message_pattern' => []
    ];
    $actions['studying_25'] = [
        'key' => 'studying_25',
        'label' => 'Studying 25',
        'message_pattern' => []
    ];
    $actions['studying_26'] = [
        'key' => 'studying_26',
        'label' => 'Studying 26',
        'message_pattern' => []
    ];
    $actions['studying_27'] = [
        'key' => 'studying_27',
        'label' => 'Studying 27',
        'message_pattern' => []
    ];
    $actions['studying_28'] = [
        'key' => 'studying_28',
        'label' => 'Studying 28',
        'message_pattern' => []
    ];
    $actions['studying_29'] = [
        'key' => 'studying_29',
        'label' => 'Studying 29',
        'message_pattern' => []
    ];
    $actions['studying_30'] = [
        'key' => 'studying_30',
        'label' => 'Studying 30',
        'message_pattern' => []
    ];
    $actions['studying_31'] = [
        'key' => 'studying_31',
        'label' => 'Studying 31',
        'message_pattern' => []
    ];
    $actions['studying_32'] = [
        'key' => 'studying_32',
        'label' => 'Studying 32',
        'message_pattern' => []
    ];

    // offline
    $actions['studying_offline_1'] = [
        'key' => 'studying_offline_1',
        'label' => 'Studying Offline 1',
        'message_pattern' => []
    ];
    $actions['studying_offline_2'] = [
        'key' => 'studying_offline_2',
        'label' => 'Studying Offline 2',
        'message_pattern' => []
    ];
    $actions['studying_offline_3'] = [
        'key' => 'studying_offline_3',
        'label' => 'Studying Offline 3',
        'message_pattern' => []
    ];
    $actions['studying_offline_4'] = [
        'key' => 'studying_offline_4',
        'label' => 'Studying Offline 4',
        'message_pattern' => []
    ];
    $actions['studying_offline_5'] = [
        'key' => 'studying_offline_5',
        'label' => 'Studying Offline 5',
        'message_pattern' => []
    ];
    $actions['studying_offline_6'] = [
        'key' => 'studying_offline_6',
        'label' => 'Studying Offline 6',
        'message_pattern' => []
    ];
    $actions['studying_offline_7'] = [
        'key' => 'studying_offline_7',
        'label' => 'Studying Offline 7',
        'message_pattern' => []
    ];
    $actions['studying_offline_8'] = [
        'key' => 'studying_offline_8',
        'label' => 'Studying Offline 8',
        'message_pattern' => []
    ];
    $actions['studying_offline_9'] = [
        'key' => 'studying_offline_9',
        'label' => 'Studying Offline 9',
        'message_pattern' => []
    ];
    $actions['studying_offline_10'] = [
        'key' => 'studying_offline_10',
        'label' => 'Studying Offline 10',
        'message_pattern' => []
    ];
    $actions['studying_offline_11'] = [
        'key' => 'studying_offline_11',
        'label' => 'Studying Offline 11',
        'message_pattern' => []
    ];
    $actions['studying_offline_12'] = [
        'key' => 'studying_offline_12',
        'label' => 'Studying Offline 12',
        'message_pattern' => []
    ];
    $actions['studying_offline_13'] = [
        'key' => 'studying_offline_13',
        'label' => 'Studying Offline 13',
        'message_pattern' => []
    ];
    $actions['studying_offline_14'] = [
        'key' => 'studying_offline_14',
        'label' => 'Studying Offline 14',
        'message_pattern' => []
    ];
    $actions['studying_offline_15'] = [
        'key' => 'studying_offline_15',
        'label' => 'Studying Offline 15',
        'message_pattern' => []
    ];
    $actions['studying_offline_16'] = [
        'key' => 'studying_offline_16',
        'label' => 'Studying Offline 16',
        'message_pattern' => []
    ];
    $actions['studying_offline_17'] = [
        'key' => 'studying_offline_17',
        'label' => 'Studying Offline 17',
        'message_pattern' => []
    ];
    $actions['studying_offline_18'] = [
        'key' => 'studying_offline_18',
        'label' => 'Studying Offline 18',
        'message_pattern' => []
    ];
    $actions['studying_offline_19'] = [
        'key' => 'studying_offline_19',
        'label' => 'Studying Offline 19',
        'message_pattern' => []
    ];
    $actions['studying_offline_20'] = [
        'key' => 'studying_offline_20',
        'label' => 'Studying Offline 20',
        'message_pattern' => []
    ];
    $actions['studying_offline_21'] = [
        'key' => 'studying_offline_21',
        'label' => 'Studying Offline 21',
        'message_pattern' => []
    ];
    $actions['studying_offline_22'] = [
        'key' => 'studying_offline_22',
        'label' => 'Studying Offline 22',
        'message_pattern' => []
    ];
    $actions['studying_offline_23'] = [
        'key' => 'studying_offline_23',
        'label' => 'Studying Offline 23',
        'message_pattern' => []
    ];
    $actions['studying_offline_24'] = [
        'key' => 'studying_offline_24',
        'label' => 'Studying Offline 24',
        'message_pattern' => []
    ];
    $actions['studying_offline_25'] = [
        'key' => 'studying_offline_25',
        'label' => 'Studying Offline 25',
        'message_pattern' => []
    ];
    $actions['studying_offline_26'] = [
        'key' => 'studying_offline_26',
        'label' => 'Studying Offline 26',
        'message_pattern' => []
    ];
    $actions['studying_offline_27'] = [
        'key' => 'studying_offline_27',
        'label' => 'Studying Offline 27',
        'message_pattern' => []
    ];
    $actions['studying_offline_28'] = [
        'key' => 'studying_offline_28',
        'label' => 'Studying Offline 28',
        'message_pattern' => []
    ];
    $actions['studying_offline_29'] = [
        'key' => 'studying_offline_29',
        'label' => 'Studying Offline 29',
        'message_pattern' => []
    ];
    $actions['studying_offline_30'] = [
        'key' => 'studying_offline_30',
        'label' => 'Studying Offline 30',
        'message_pattern' => []
    ];
    $actions['studying_offline_31'] = [
        'key' => 'studying_offline_31',
        'label' => 'Studying Offline 31',
        'message_pattern' => []
    ];
    $actions['studying_offline_32'] = [
        'key' => 'studying_offline_32',
        'label' => 'Studying Offline 32',
        'message_pattern' => []
    ];



    $actions['leading_1'] = [
        'key' => 'leading_1',
        'label' => 'Leading 1',
        'message_pattern' => []
    ];
    $actions['leading_2'] = [
        'key' => 'leading_2',
        'label' => 'Leading 2',
        'message_pattern' => []
    ];
    $actions['leading_3'] = [
        'key' => 'leading_3',
        'label' => 'Leading 3',
        'message_pattern' => []
    ];
    $actions['leading_4'] = [
        'key' => 'leading_4',
        'label' => 'Leading 4',
        'message_pattern' => []
    ];
    $actions['leading_5'] = [
        'key' => 'leading_5',
        'label' => 'Leading 5',
        'message_pattern' => []
    ];
    $actions['leading_6'] = [
        'key' => 'leading_6',
        'label' => 'Leading 6',
        'message_pattern' => []
    ];
    $actions['leading_7'] = [
        'key' => 'leading_7',
        'label' => 'Leading 7',
        'message_pattern' => []
    ];
    $actions['leading_8'] = [
        'key' => 'leading_8',
        'label' => 'Leading 8',
        'message_pattern' => []
    ];
    $actions['leading_9'] = [
        'key' => 'leading_9',
        'label' => 'Leading 9',
        'message_pattern' => []
    ];
    $actions['leading_10'] = [
        'key' => 'leading_10',
        'label' => 'Leading 10',
        'message_pattern' => []
    ];

    $actions['zume_training'] = [
        'key' => 'zume_training',
        'label' => 'Registered for Training',
        'message_pattern' => []
    ];
    $actions['zume_vision'] = [
        'key' => 'zume_vision',
        'label' => 'Joined Vision',
        'message_pattern' => []
    ];
    $actions['updated_3_month'] = [
        'key' => 'updated_3_month',
        'label' => 'Updated 3 Month Plan',
        'message_pattern' => []
    ];

    return $actions;
}

/**
 * READ LOG
 */
add_filter( 'dt_network_dashboard_build_message', 'zume_ftt_public_log_actions', 10, 1 );
function zume_ftt_public_log_actions( $activity_log ){

    foreach ( $activity_log as $index => $log ){

        /* new_baptism */
        if ( 'studying' === substr( $log['action'], 0, 8 ) ) {
            $initials = Zume_App_Heatmap::create_initials( $log['lng'], $log['lat'], $log['payload'] );
            $activity_log[$index]['message'] = $initials . ' is studying "' . $log['payload']['title'] . '"';
        }

        if ( 'leading' === substr( $log['action'], 0, 7 ) ) {
            $initials = Zume_App_Heatmap::create_initials( $log['lng'], $log['lat'], $log['payload'] );
            $activity_log[$index]['message'] = $initials . ' is leading a group through session '. str_replace( '_', '', substr( $log['action'], -2, 2 ) ).'!';
        }

        if ( 'zume_training' === $log['action'] && 'joining' === $log['category'] ) {
            $initials = Zume_App_Heatmap::create_initials( $log['lng'], $log['lat'], $log['payload'] );
            $activity_log[$index]['message'] = $initials . ' is registering for Zúme Training! ';
        }

        if ( 'zume_vision' === $log['action'] && 'joining' === $log['category'] ) {
            $initials = Zume_App_Heatmap::create_initials( $log['lng'], $log['lat'], $log['payload'] );
            $activity_log[$index]['message'] = $initials . ' is registering for Zúme Community! ';
        }

        if ( 'updated_3_month' === $log['action'] ) {
            $initials = Zume_App_Heatmap::create_initials( $log['lng'], $log['lat'], $log['payload'] );
            $activity_log[$index]['message'] = $initials . ' is updating there Zúme Training 3 month plan! ';
        }
    }

    return $activity_log;
}
