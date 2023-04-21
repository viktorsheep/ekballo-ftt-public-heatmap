<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_FTT_Public_Heatmap_Churches_1000::instance();
}

class Zume_FTT_Public_Heatmap_Churches_1000 extends DT_Magic_Url_Base
{
    public $page_title = 'Zúme Churches Map';
    public $root = "zume_app";
    public $type = 'heatmap_1000';
    public $type_name = '';
    public $post_type = 'groups';
    private $meta_key = '';
    public $global_div = 1000;

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        $this->meta_key = $this->root . '_' . $this->type . '_magic_key';
        parent::__construct();

        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );
        add_action( 'dt_post_created', [ $this, 'clear_group_cache' ] );


        // fail if not valid url
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
            return;
        }

        if ( !$this->check_parts_match( false ) ){
            return;
        }

        add_action( 'dt_blank_body', [ $this, 'body' ] );
        add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );
        add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ], 99 );

    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {
        $allowed_js[] = 'jquery-touch-punch';
        $allowed_js[] = 'mapbox-gl';
        $allowed_js[] = 'jquery-cookie';
        $allowed_js[] = 'mapbox-cookie';
        $allowed_js[] = 'heatmap-js';
        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {
        $allowed_css[] = 'mapbox-gl-css';
        $allowed_css[] = 'introjs-css';
        $allowed_css[] = 'heatmap-css';
        $allowed_css[] = 'site-css';
        return $allowed_css;
    }

    public function _header(){
        Zume_App_Heatmap::_header();
    }

    public static function _wp_enqueue_scripts(){
        Zume_App_Heatmap::_wp_enqueue_scripts();
    }

    public function body(){
        DT_Mapbox_API::geocoder_scripts();
        include( 'heatmap.html' );
    }

    public function clear_group_cache( $post_id ){
        if ( $this->post_type === get_post_type( $post_id ) ){
            Zume_App_Heatmap::clear_church_grid_totals();
        }
    }

    public function footer_javascript(){
        ?>
        <script>
            let jsObject = [<?php echo json_encode([
                'map_key' => DT_Mapbox_API::get_key(),
                'mirror_url' => dt_get_location_grid_mirror( true ),
                'theme_uri' => trailingslashit( get_stylesheet_directory_uri() ),
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'parts' => $this->parts,
                'post_type' => $this->post_type,
                'translation' => [
                    'add' => __( 'Zume', 'disciple_tools' ),
                    'title' => 'Churches'
                ],
                'grid_data' => ['data' => [], 'highest_value' => 1 ],
                'custom_marks' => [],
                'zoom' => 8
            ]) ?>][0]

            /* custom content */
            function load_self_content( data ) {
                jQuery('#custom-paragraph').html(`
                  <span class="self_name ucwords temp-spinner bold">${data.name}</span> is one of <span class="self_peers  bold">${data.peers}</span>
                  administrative divisions in <span class="parent_name ucwords bold">${data.parent_name}</span> and it has a population of
                  <span class="self_population  bold">${data.population}</span>.
                  In order to reach the community goal of 1 church for every <span class="population_division  bold">${data.population_division}</span> people,
                  <span class="self_name ucwords  bold">${data.name}</span> needs
                  <span class="self_needed bold">${data.needed}</span> new churches.
                `)
            }
            /* custom level content */
            function load_level_content( data, level ) {
                let gl = jQuery('#'+level+'-list-item')
                gl.empty()
                if ( false !== data ) {
                    gl.append(`
                        <div class="cell">
                          <strong>${data.name}</strong><br>
                          Population: <span>${data.population}</span><br>
                          Churches Needed: <span>${data.needed}</span><br>
                          Churches Reported: <span class="reported_number">${data.reported}</span><br>
                          Goal Reached: <span>${data.percent}</span>%
                          <meter class="meter" value="${data.percent}" min="0" low="33" high="66" optimum="100" max="100"></meter>
                        </div>
                    `)
                }
            }
        </script>
        <?php

        $this->customized_welcome_script();
        return true;
    }

    public function customized_welcome_script(){
        ?>
        <script>
            jQuery(document).ready(function($){
                let asset_url = '<?php echo esc_url( trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/' ) ?>'
                $('.training-content').append(`
                <div class="grid-x grid-padding-x" >
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'search.svg'}" alt="search icon" />
                        <h2>Search</h2>
                        <p>Search for any city or place with the search input.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'zoom.svg'}" alt="zoom icon"  />
                        <h2>Zoom</h2>
                        <p>Scroll zoom with your mouse or pinch zoom with track pads and phones to focus on sections of the map.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'drag.svg'}" alt="drag icon"  />
                        <h2>Drag</h2>
                        <p>Click and drag the map any direction to look at a different part of the map.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'click.svg'}" alt="click icon" />
                        <h2>Click</h2>
                        <p>Click a single section and reveal a details panel with more information about the location.</p>
                    </div>
                </div>
                `)

            })
        </script>
        <?php
    }

    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace,
            '/'.$this->type,
            [
                [
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => [ $this, 'endpoint' ],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    public function endpoint( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'self':
                return Zume_App_Heatmap::get_self( $params['grid_id'], $this->global_div);
            case 'a3':
            case 'a2':
            case 'a1':
            case 'a0':
            case 'world':
                $list = Zume_App_Heatmap::query_church_grid_totals( $action, $params['grid_id'] );
                return Zume_App_Heatmap::endpoint_get_level( $params['grid_id'], $action, $list, $this->global_div );
            case 'activity_data':
                return [];
            case 'grid_data':
                return $this->get_grid_data();
            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function get_grid_data() {

        $grid_totals = Zume_App_Heatmap::query_church_grid_totals();
        $flat_grid = Zume_App_Heatmap::query_saturation_list();

        $data = [];
        $highest_value = 1;
        foreach ( $flat_grid as $v ){
            $data[$v['grid_id']] = [
                'grid_id' => $v['grid_id'],
                'population' => number_format_i18n( $v['population'] ),
                'needed' => 1,
                'reported' => 0,
                'percent' => 0,
            ];

            $needed = round( $v['population'] / $this->global_div );
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
            'data' => $data,
            'count' => count( $data ),
        ];
    }

}
