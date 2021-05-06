<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    DT_Pray4everywhere::instance();
}


add_filter('dt_network_dashboard_supported_public_links', function( $supported_links ){
    $supported_links[] = [
        'name' => 'Public Heatmap ( Pray4Everywhere )',
        'description' => 'Pray4Everywhere template',
        'key' => 'zume_app_pray4everywhere',
        'url' => 'zume_app/pray4everywhere'
    ];
    return $supported_links;
}, 10, 1 );


class DT_Pray4everywhere
{

    public $magic = false;
    public $parts = false;
    public $root = "zume_app";
    public $type = 'pray4everywhere';
    public $post_type = 'contacts';

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {

        // register type
        $this->magic = new DT_Magic_URL( $this->root );
        add_filter( 'dt_magic_url_register_types', [ $this, '_register_type' ], 10, 1 );

        // register REST and REST access
        add_filter( 'dt_allow_rest_access', [ $this, '_authorize_url' ], 100, 1 );
        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );


        // fail if not valid url
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
            return;
        }

        // fail to blank if not valid url
        $this->parts = $this->magic->parse_url_parts();
        if ( ! $this->parts ){
            // @note this returns a blank page for bad url, instead of redirecting to login
            add_filter( 'dt_templates_for_urls', function ( $template_for_url ) {
                $url = dt_get_url_path();
                $template_for_url[ $url ] = 'template-blank.php';
                return $template_for_url;
            }, 199, 1 );
            add_filter( 'dt_blank_access', function(){ return true;
            } );
            add_filter( 'dt_allow_non_login_access', function(){ return true;
            }, 100, 1 );
            return;
        }

        // fail if does not match type
        if ( $this->type !== $this->parts['type'] ){
            return;
        }

        // load if valid url
        add_filter( "dt_blank_title", [ $this, "_browser_tab_title" ] );
        add_action( 'dt_blank_head', [ $this, '_header' ] );
        add_action( 'dt_blank_footer', [ $this, '_footer' ] );
        add_action( 'dt_blank_body', [ $this, 'body' ] ); // body for no post key

        // load page elements
        add_action( 'wp_print_scripts', [ $this, '_print_scripts' ], 1500 );
        add_action( 'wp_print_styles', [ $this, '_print_styles' ], 1500 );

        // register url and access
        add_filter( 'dt_templates_for_urls', [ $this, '_register_url' ], 199, 1 );
        add_filter( 'dt_blank_access', [ $this, '_has_access' ] );
        add_filter( 'dt_allow_non_login_access', function(){ return true;
        }, 100, 1 );

    }

    public function _register_type( array $types ) : array {
        if ( ! isset( $types[$this->root] ) ) {
            $types[$this->root] = [];
        }
        $types[$this->root][$this->type] = [
            'name' => 'Magic',
            'root' => $this->root,
            'type' => $this->type,
            'meta_key' => 'public_key',
            'actions' => [
                '' => 'Manage',
            ],
            'post_type' => $this->post_type,
        ];
        return $types;
    }

    public function _register_url( $template_for_url ){
        $parts = $this->parts;

        // test 1 : correct url root and type
        if ( ! $parts ){ // parts returns false
            return $template_for_url;
        }

        // test 2 : only base url requested
        if ( empty( $parts['public_key'] ) ){ // no public key present
            $template_for_url[ $parts['root'] . '/'. $parts['type'] ] = 'template-blank.php';
            return $template_for_url;
        }

        // test 3 : no specific action requested
        if ( empty( $parts['action'] ) ){ // only root public key requested
            $template_for_url[ $parts['root'] . '/'. $parts['type'] . '/' . $parts['public_key'] ] = 'template-blank.php';
            return $template_for_url;
        }

        // test 4 : valid action requested
        $actions = $this->magic->list_actions( $parts['type'] );
        if ( isset( $actions[ $parts['action'] ] ) ){
            $template_for_url[ $parts['root'] . '/'. $parts['type'] . '/' . $parts['public_key'] . '/' . $parts['action'] ] = 'template-blank.php';
        }

        return $template_for_url;
    }
    public function _has_access() : bool {
        $parts = $this->parts;

        // test 1 : correct url root and type
        if ( $parts ){ // parts returns false
            return true;
        }

        return false;
    }
    public function _header(){
        wp_head();
        $this->header_style();
        $this->header_javascript();
    }
    public function _footer(){
        wp_footer();
    }
    public function _authorize_url( $authorized ){
        if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), $this->root . '/v1/'.$this->type ) !== false ) {
            $authorized = true;
        }
        return $authorized;
    }
    public function _print_scripts(){
        // @link /disciple-tools-theme/dt-assets/functions/enqueue-scripts.php
        $allowed_js = [
            'jquery',
            'lodash',
            'moment',
            'datepicker',
            'site-js',
            'shared-functions',
            'mapbox-gl',
            'mapbox-cookie',
            'mapbox-search-widget',
            'google-search-widget',
            'jquery-cookie',
        ];

        global $wp_scripts;

        if ( isset( $wp_scripts ) ){
            foreach ( $wp_scripts->queue as $key => $item ){
                if ( ! in_array( $item, $allowed_js ) ){
                    unset( $wp_scripts->queue[$key] );
                }
            }
        }
        unset( $wp_scripts->registered['mapbox-search-widget']->extra['group'] );
    }
    public function _print_styles(){
        // @link /disciple-tools-theme/dt-assets/functions/enqueue-scripts.php
        $allowed_css = [
            'foundation-css',
            'jquery-ui-site-css',
            'site-css',
            'datepicker-css',
            'mapbox-gl-css'
        ];

        global $wp_styles;
        if ( isset( $wp_styles ) ) {
            foreach ($wp_styles->queue as $key => $item) {
                if ( !in_array( $item, $allowed_css )) {
                    unset( $wp_styles->queue[$key] );
                }
            }
        }
    }
    public function _browser_tab_title( $title ){
        return __( "Zúme Trainings Map", 'disciple_tools' );
    }

    public function header_style(){
        ?>
        <style>
            body {
                background: white;
            }
            #email {
                display:none;
            }
            .redborder {
                border: 1px solid red;
            }
        </style>
        <?php
    }
    public function header_javascript(){
        ?>
        <script>
            let jsObject = [<?php echo json_encode([
                'map_key' => DT_Mapbox_API::get_key(),
                'mirror_url' => dt_get_location_grid_mirror( true ),
                'theme_uri' => trailingslashit( get_stylesheet_directory_uri() ),
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'parts' => $this->parts,
                'trans' => [
                    'add' => __( 'Add Magic', 'disciple_tools' ),
                ],
                'grid_data' => $this->grid_list(),
            ]) ?>][0]

            jQuery(document).ready(function(){
                clearInterval(window.fiveMinuteTimer)
            })

            window.get_grid_data = (grid_id) => {
                return jQuery.ajax({
                    type: "POST",
                    data: JSON.stringify({ action: 'POST', parts: jsObject.parts, grid_id: grid_id }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type + '/grid_totals',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
                    }
                })
                    .fail(function(e) {
                        console.log(e)
                        jQuery('#error').html(e)
                    })
            }
        </script>
        <?php
        return true;
    }
    public function body(){
        DT_Mapbox_API::geocoder_scripts();
        ?>
        <div id="custom-style"></div>
        <div id="wrapper">
            <div id="map-wrapper">
                <div class="hide-for-small-only" style="position:absolute; top: 10px; left:10px; z-index: 10;background-color:white; opacity: .9;padding:5px 10px; margin: 0 10px;">
                    <div class="grid-x">
                        <div class="cell" id="name-id">Hover and zoom for locations</div>
                    </div>
                </div>

                <div id='map'><span class="loading-spinner active"></span></div>
            </div>
        </div>
        <div class="off-canvas position-left is-closed" id="offCanvasNestedPush" data-transition-time=".3s" data-off-canvas>
            <div class="grid-x grid-padding-x " style="margin-top:1rem;">
                <div class="cell">
                    <h1 id="title">Title</h1>
                    <hr>
                </div>
                <div class="cell">
                    <h2>Goal: <span id="saturation-goal">0</span>%</h2>
                    <meter id="meter" style="height:3rem;width:100%;" value="30" min="0" low="33" high="66" optimum="100" max="100"></meter>
                </div>
                <div class="cell">
                    <h2>Population: <span id="population">0</span></h2>
                </div>
                <div class="cell">
                    <h2>New Churches Needed: <span id="needed">0</span></h2>
                </div>
                <div class="cell">
                    <h2>Churches Reported: <span id="reported">0</span></h2>
                </div>
                <div class="cell">
                    <hr>
                </div>
                <div class="cell center">
                    <button class="button" id="add-report">Add Report</button>
                </div>

            </div>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!-- Report modal -->
        <div class="reveal" id="report-modal" data-v-offset="10px" data-reveal>
            <div>
                <h1 id="title">Report New Simple Church <i class="fi-info primary-color small"></i> </h1>
                <h3 id="report-modal-title"></h3>
            </div>
            <div id="report-modal-content">
                <div class="grid-x">
                    <div class="cell">
                        <label for="name">Name</label>
                        <input type="text" id="name" class="required" placeholder="Name" />
                        <span id="name-error" class="form-error">
                            <?php esc_html_e( "You're name is required.", 'disciple_tools' ); ?>
                        </span>
                    </div>
                    <div class="cell">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email" />
                        <input type="email" id="e2" name="email" class="required" placeholder="Email" />
                        <span id="email-error" class="form-error">
                            <?php esc_html_e( "You're email is required.", 'disciple_tools' ); ?>
                        </span>
                    </div>
                    <div class="cell">
                        <label for="tel">Phone</label>
                        <input type="tel" id="phone" name="phone" class="required" placeholder="Phone" />
                        <span id="phone-error" class="form-error">
                            <?php esc_html_e( "You're phone is required.", 'disciple_tools' ); ?>
                        </span>
                    </div>
                    <div class="cell callout">
                        <div class="grid-x">
                            <div class="cell small-5">
                                Nickname of Simple Church
                            </div>
                            <div class="cell small-2">
                                Member Count
                            </div>
                            <div class="cell small-4">
                                Date Started
                            </div>
                            <div class="cell small-1">

                            </div>
                        </div>
                        <div id="church-list"><!-- church report rows --></div>
                        <div class="grid-x">
                            <div class="cell center">
                                <button type="button" class="button clear small" id="add-another">add another</button>
                            </div>
                        </div>
                    </div>

                    <div class="cell center">
                        <p><input type="checkbox" class="required" id="return-reporter" /> I have submitted a report before.</p>
                    </div>
                    <div class="cell center">
                        <input type="hidden" id="report-grid-id" />
                        <!--                        <button class="button" id="submit-report">Add Report</button> <span class="loading-spinner"></span>-->
                        <button class="button" onclick="alert('Add Report Disabled on Live Site')">Add Report</button>
                    </div>
                </div>
            </div>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <script>
            jQuery(document).ready(function($){

                /* set vertical size the form column*/
                $('#custom-style').append(`
                    <style>
                        #wrapper {
                            height: ${window.innerHeight}px !important;
                        }
                        #map-wrapper {
                            height: ${window.innerHeight}px !important;
                        }
                        #map {
                            height: ${window.innerHeight}px !important;
                        }
                        .off-canvas {
                        width:${window.innerWidth * .50}px;
                        background-color:white;
                        }
                    </style>`)

                // window.get_grid_data().then(function(grid_data){
                $('#map').empty()
                mapboxgl.accessToken = jsObject.map_key;
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/light-v10',
                    // style: 'mapbox://styles/mapbox/streets-v11',
                    center: [-98, 38.88],
                    minZoom: 2,
                    maxZoom: 8,
                    zoom: 3
                });

                map.addControl(
                    new MapboxGeocoder({
                        accessToken: mapboxgl.accessToken,
                        mapboxgl: mapboxgl,
                        marker: false
                    })
                );

                map.addControl(new mapboxgl.NavigationControl());
                map.dragRotate.disable();
                map.touchZoomRotate.disableRotation();

                window.previous_hover = false

                map.on('load', function() {

                    let asset_list = []
                    var i = 1;
                    while( i <= 46 ){
                        asset_list.push(i+'.geojson')
                        i++
                    }

                    jQuery.each(asset_list, function(i,v){

                        jQuery.ajax({
                            url: jsObject.mirror_url + 'tiles/world/saturation/' + v,
                            dataType: 'json',
                            data: null,
                            beforeSend: function (xhr) {
                                if (xhr.overrideMimeType) {
                                    xhr.overrideMimeType("application/json");
                                }
                            }
                        })
                            .done(function (geojson) {

                                jQuery.each(geojson.features, function (i, v) {
                                    if (jsObject.grid_data[v.id]) {
                                        geojson.features[i].properties.value = parseInt(jsObject.grid_data[v.id].percent)
                                    } else {
                                        geojson.features[i].properties.value = 0
                                    }
                                })

                                map.addSource(i.toString(), {
                                    'type': 'geojson',
                                    'data': geojson
                                });
                                map.addLayer({
                                    'id': i.toString()+'line',
                                    'type': 'line',
                                    'source': i.toString(),
                                    'paint': {
                                        'line-color': '#323A68',
                                        'line-width': .5
                                    }
                                });

                                /**************/
                                /* hover map*/
                                /**************/
                                map.addLayer({
                                    'id': i.toString() + 'fills',
                                    'type': 'fill',
                                    'source': i.toString(),
                                    'paint': {
                                        'fill-color': 'black',
                                        'fill-opacity': [
                                            'case',
                                            ['boolean', ['feature-state', 'hover'], false],
                                            .8,
                                            0
                                        ]
                                    }
                                })
                                /* end hover map*/

                                /**********/
                                /* heat map brown */
                                /**********/
                                map.addLayer({
                                    'id': i.toString() + 'fills_heat',
                                    'type': 'fill',
                                    'source': i.toString(),
                                    'paint': {
                                        'fill-color': [
                                            'interpolate',
                                            ['linear'],
                                            ['get', 'value'],
                                            0,
                                            'rgba(0,0,0,0)',
                                            1,
                                            'OrangeRed',
                                            // 10,
                                            // 'grey',
                                            // 50,
                                            // 'lightgreen',
                                            // 70,
                                            // 'yellow',
                                            100,
                                            'darkgreen',

                                        ],
                                        'fill-opacity': 0.7
                                    }
                                })
                                /**********/
                                /* end fill map */
                                /**********/

                                map.on('mousemove', i.toString()+'fills', function (e) {
                                    if ( window.previous_hover ) {
                                        map.setFeatureState(
                                            window.previous_hover,
                                            { hover: false }
                                        )
                                    }
                                    window.previous_hover = { source: i.toString(), id: e.features[0].id }
                                    if (e.features.length > 0) {
                                        jQuery('#name-id').html(e.features[0].properties.full_name)
                                        map.setFeatureState(
                                            window.previous_hover,
                                            {hover: true}
                                        );
                                    }
                                });
                                map.on('click', i.toString()+'fills', function (e) {

                                    $('#title').html(e.features[0].properties.full_name)
                                    $('#meter').val(jsObject.grid_data[e.features[0].properties.grid_id].percent)
                                    $('#saturation-goal').html(jsObject.grid_data[e.features[0].properties.grid_id].percent)
                                    $('#population').html(jsObject.grid_data[e.features[0].properties.grid_id].population)

                                    //report
                                    $('#report-modal-title').html(e.features[0].properties.full_name)
                                    $('#report-grid-id').val(e.features[0].properties.grid_id)

                                    let reported = jsObject.grid_data[e.features[0].properties.grid_id].reported
                                    $('#reported').html(reported)

                                    let needed = jsObject.grid_data[e.features[0].properties.grid_id].needed
                                    $('#needed').html(needed)

                                    $('#offCanvasNestedPush').foundation('toggle', e);

                                });
                            })
                    })
                })

                $('#add-report').on('click', function(e){
                    $('#church-list').empty()
                    append_report_row()

                    jQuery('#report-modal').foundation('open')
                })
                $('#add-another').on('click', function(e){
                    append_report_row()
                })
                let submit_button = $('#submit-report')
                function check_inputs(){
                    submit_button.prop('disabled', false)
                    $.each($('.required'), function(){
                        if ( $(this).val() === '' ) {
                            $(this).addClass('redborder')
                            submit_button.prop('disabled', true)
                        }
                        else {
                            $(this).removeClass('redborder')
                        }
                    })

                }
                function append_report_row(){
                    let id = Date.now()
                    $('#church-list').append(`
                    <div class="grid-x row-${id} list-row" data-id="${id}">
                        <div class="cell small-5">
                            <input type="text" name="${id}[name]" class="${id} name-${id} required" placeholder="Name of Simple Church" data-name="name" data-group-id="${id}" />
                        </div>
                        <div class="cell small-2">
                            <input type="number" name="${id}[members]" class="${id} members-${id} required" placeholder="#" data-name="members" data-group-id="${id}" />
                        </div>
                        <div class="cell small-4">
                            <input type="date" name="${id}[start]" class="${id} start-${id} required" placeholder="Started" data-name="start" data-group-id="${id}" />
                        </div>
                        <div class="cell small-1">
                            <button class="button expanded alert" style="border-radius: 0;" onclick="remove_row(${id})">X</button>
                        </div>
                    </div>
                    `)

                    $('.required').focusout(function(){
                        check_inputs()
                    })
                    check_inputs()
                }
                submit_button.on('click', function(){
                    let spinner = jQuery('.loading-spinner')
                    spinner.addClass('active')

                    let submit_button = jQuery('#submit-report')
                    submit_button.prop('disabled', true)

                    let honey = jQuery('#email').val()
                    if ( honey ) {
                        submit_button.html('Shame, shame, shame. We know your name ... ROBOT!').prop('disabled', true )
                        spinner.removeClass('active')
                        return;
                    }

                    let name_input = jQuery('#name')
                    let name = name_input.val()
                    if ( ! name ) {
                        jQuery('#name-error').show()
                        submit_button.removeClass('loading')
                        name_input.focus(function(){
                            jQuery('#name-error').hide()
                        })
                        submit_button.prop('disabled', false)
                        spinner.removeClass('active')
                        return;
                    }

                    let email_input = jQuery('#e2')
                    let email = email_input.val()
                    if ( ! email ) {
                        jQuery('#email-error').show()
                        submit_button.removeClass('loading')
                        email_input.focus(function(){
                            jQuery('#email-error').hide()
                        })
                        submit_button.prop('disabled', false)
                        spinner.removeClass('active')
                        return;
                    }

                    let phone_input = jQuery('#phone')
                    let phone = phone_input.val()
                    if ( ! phone ) {
                        jQuery('#phone-error').show()
                        submit_button.removeClass('loading')
                        email_input.focus(function(){
                            jQuery('#phone-error').hide()
                        })
                        submit_button.prop('disabled', false)
                        spinner.removeClass('active')
                        return;
                    }

                    let list = []
                    jQuery.each( jQuery('.list-row'), function(i,v){
                        let row_id = jQuery(this).data('id')
                        list.push({
                            name: jQuery('.name-'+row_id).val(),
                            members: jQuery('.members-'+row_id).val(),
                            start: jQuery('.start-'+row_id).val()
                        })
                    })


                    let grid_id = jQuery('#report-grid-id').val()
                    let return_reporter = jQuery('#return-reporter').is(':checked');

                    // if cookie contact_id
                    // if window contact_id
                    let contact_id = ''
                    if ( typeof window.contact_id !== 'undefined' && typeof window.contact_email !== 'undefined' ) {
                        if ( email === window.contact_email ) {
                            contact_id = window.contact_id
                        }
                    }

                    let form_data = {
                        name: name,
                        email: email,
                        phone: phone,
                        grid_id: grid_id,
                        contact_id: contact_id,
                        return_reporter: return_reporter,
                        list: list
                    }

                    jQuery.ajax({
                        type: "POST",
                        data: JSON.stringify({ action: 'new_report', parts: jsObject.parts, data: form_data }),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
                        }
                    })
                        .done(function(response){
                            jQuery('.loading-spinner').removeClass('active')
                            console.log(response)

                            window.contact_id = response.contact.ID
                            window.contact_email = email


                        })
                        .fail(function(e) {
                            console.log(e)
                            jQuery('#error').html(e)
                        })
                })
            })

            function remove_row( id ) {
                let submit_button = $('#submit-report')
                jQuery('.row-'+id).remove();
                submit_button.prop('disabled', true)
            }
            if (document.readyState === 'complete') {
                window.contact_id = Cookie.get('contact_id')
                window.contact_email = Cookie.get('contact_email')
            }

        </script>
        <?php
    }

    public function grid_list(){
        $list = DT_Zume_Public_Heatmap::query_saturation_list();
        $grid_list = Disciple_Tools_Mapping_Queries::query_church_location_grid_totals();

        $data = [];
        foreach( $list as $v ){
            $data[$v['grid_id']] = [
                'grid_id' => $v['grid_id'],
                'percent' => 0,
                'reported' => 0,
                'needed' => 1,
                'population' => number_format_i18n( $v['population'] ),
            ];

            $population_division = 25000;
            if ( in_array( $v['country_code'], ['US'])) {
                $population_division = 5000;
            }

            $needed = round( $v['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }

            if ( isset( $grid_list[$v['grid_id']] ) && ! empty($grid_list[$v['grid_id']]['count']) ){
                $count = $grid_list[$v['grid_id']]['count'];
                if ( ! empty($count) && ! empty($needed) ){
                    $percent = round($count / $needed * 100 );

                    $data[$v['grid_id']]['percent'] = $percent;
                    $data[$v['grid_id']]['reported'] = $grid_list[$v['grid_id']]['count'];
                    $data[$v['grid_id']]['needed'] = $needed;
                }
            }
            else {
                $data[$v['grid_id']]['percent'] = 0;
                $data[$v['grid_id']]['reported'] = 0;
                $data[$v['grid_id']]['needed'] = $needed;
            }
        }

        return $data;
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace, '/'.$this->type, [
                [
                    'methods'  => "POST",
                    'callback' => [ $this, 'endpoint' ],
                ],
            ]
        );

    }

    /**
     * @param WP_REST_Request $request
     * @return array|false|int|WP_Error|null
     */
    public function endpoint( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'new_report':
                return $this->endpoint_new_report( $params['data'] );
            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function endpoint_new_report( $form_data ) {
        global $wpdb;
        if ( ! isset( $form_data['grid_id'], $form_data['name'], $form_data['email'], $form_data['phone'], $form_data['list'] ) ) {
            return new WP_Error(__METHOD__, 'Missing params.', ['status' => 400 ] );
        }
        if ( ! is_array( $form_data['list'] ) || empty( $form_data['list'] ) ) {
            return new WP_Error(__METHOD__, 'List missing.', ['status' => 400 ] );
        }

        $contact_id = false;

        // try to get contact_id and contact
        if ( isset( $form_data['contact_id'] ) && ! empty( $form_data['contact_id'] ) ) {
            $contact_id = (int) $form_data['contact_id'];
            $contact = DT_Posts::get_post('contacts', $contact_id, false, false );
            if ( is_wp_error( $contact ) ){
                return $contact;
            }
        }
        else if ( isset( $form_data['return_reporter'] ) && $form_data['return_reporter'] ) {
            $email = sanitize_email( wp_unslash( $form_data['email'] ) );
            $contact_ids = $wpdb->get_results($wpdb->prepare( "
                SELECT DISTINCT pm.post_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->postmeta as pm1 ON pm.post_id=pm1.post_id AND pm1.meta_key LIKE 'contact_email%' AND pm1.meta_key NOT LIKE '%details'
                WHERE pm.meta_key = 'overall_status' AND pm.meta_value = 'active' AND pm1.meta_value = %s
            ", $email ), ARRAY_A );
            if ( ! empty( $contact_ids ) ){
                $contact_id = $contact_ids[0]['post_id'];
                $contact = DT_Posts::get_post('contacts', $contact_id, false, false );
                if ( is_wp_error( $contact ) ){
                    return $contact;
                }
            }
        }

        // create contact if not able to be found
        if ( ! $contact_id ) {
            // create contact
            $fields = [
                'title' => $form_data['name'],
                "overall_status" => "new",
                "type" => "access",
                "contact_email" => [
                    ["value" => $form_data['email']],
                ],
                "contact_phone" => [
                    ["value" => $form_data['phone']],
                ],
                'notes' => [
                    'source_note' => 'Submitted from public heatmap.'
                ]

            ];
            if ( DT_Mapbox_API::get_key() ) {
                $fields["location_grid_meta"] = [
                    "values" => [
                        [ "grid_id" => $form_data['grid_id'] ]
                    ]
                ];
            } else {
                $fields["location_grid"] = [
                    "values" => [
                        [ "value" => $form_data['grid_id'] ]
                    ]
                ];
            }

            $contact = DT_Posts::create_post( 'contacts', $fields, true, false );
            if ( is_wp_error( $contact ) ){
                return $contact;
            }
            $contact_id = $contact['ID'];
        }

        // create groups
        $group_ids = [];
        $groups = [];
        foreach( $form_data['list'] as $group ) {
            $fields = [
                'title' => $group['name'],
                'member_count' => $group['members'],
                'start_date' => $group['start'],
                'church_start_date' => $group['start'],
                'group_status' => 'active',
                'leader_count' => 1,
                'group_type' => 'church',
                'members' => [
                    "values" => [
                        [ "value" => $contact_id ],
                    ],
                ],
                'leaders' => [
                    "values" => [
                        [ "value" => $contact_id ],
                    ],
                ],
                'notes' => [
                    'source_note' => 'Submitted from public heatmap.'
                ]
            ];
            if ( DT_Mapbox_API::get_key() ) {
                $fields["location_grid_meta"] = [
                    "values" => [
                        [ "grid_id" => $form_data['grid_id'] ]
                    ]
                ];
            } else {
                $fields["location_grid"] = [
                    "values" => [
                        [ "value" => $form_data['grid_id'] ]
                    ]
                ];
            }

            $g = DT_Posts::create_post( 'groups', $fields, true, false );
            if ( is_wp_error( $g ) ){
                $groups[] = $g;
                continue;
            }
            $group_id = $g['ID'];
            $group_ids[] = $group_id;
            $groups[$group_id] = $g;
        }

        // create connections
        $connection_ids = [];
        if ( ! empty( $group_ids ) ) {
            foreach( $group_ids as $gid ) {
                $fields = [
                    "peer_groups" => [
                        "values" => [],
                    ]
                ];
                foreach( $group_ids as $subid ) {
                    if ( $gid === $subid ) {
                        continue;
                    }
                    $fields['peer_groups']['values'][] = [ "value" => $subid ];
                }

                $c = DT_Posts::update_post( 'groups', $gid, $fields, true, false );
                $connection_ids[] = $c;
            }
        }

        $data = [
            'contact' => $contact,
            'groups' => $groups,
            'connections' => $connection_ids
        ];

        return $data;
    }
}
