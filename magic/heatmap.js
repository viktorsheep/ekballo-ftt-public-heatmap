var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
  || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
  isMobile = true;
}

/* Rest APIs */
window.get_grid_data = ( action, grid_id) => {
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: action, parts: jsObject.parts, grid_id: grid_id }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
    }
  })
    .fail(function(e) {
      jQuery('#error').html(e)
    })
}
window.get_activity_data = (grid_id) => {
  let offset = new Date().getTimezoneOffset();
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: 'activity_data', parts: jsObject.parts, grid_id: grid_id, offset: offset }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
    }
  })
    .fail(function(e) {
      jQuery('#error').html(e)
    })
}

/* Document Ready && Precache */
jQuery(document).ready(function($){

  clearInterval(window.fiveMinuteTimer)

  let slider_width = window.innerWidth * .70
  if ( isMobile ) {
    slider_width = window.innerWidth * .95
  }

  /* set vertical size the form column*/
  $('#custom-style').empty().append(`
    #wrapper {
      height: ${window.innerHeight}px !important;
    }
    #map-wrapper {
      height: ${window.innerHeight}px !important;
    }
    #map {
      height: ${window.innerHeight}px !important;
    }
    .off-canvas.position-right {
      width: 25%;
      background-color:white;
    }
    #initialize-screen {
      height: ${window.innerHeight}px !important;
    }
    #welcome-modal {
      height: ${window.innerHeight - 30}px !important;
    }
    #map-sidebar-wrapper {
      height: ${window.innerHeight}px !important;
    }
  `)

  $('#custom-style-portal').empty().append(`
    #wrapper {
      height: ${window.innerHeight-60}px !important;
    }
    #map-wrapper {
      height: ${window.innerHeight-60}px !important;
    }
    #map {
      height: ${window.innerHeight-60}px !important;
    }
    .off-canvas.position-right {
      width: 25%;
      background-color:white;
    }
    #initialize-screen {
      height: ${window.innerHeight-60}px !important;
    }
    #welcome-modal {
      height: ${window.innerHeight - 30}px !important;
    }
    #map-sidebar-wrapper {
      height: ${window.innerHeight-60}px !important;
    }
  `)

  let initialize_screen = jQuery('.initialize-progress')

  // preload all geojson
  let asset_list = []
  var i = 1;
  while( i <= 45 ){
    asset_list.push(i+'.geojson')
    i++
  }

  let loop = 0
  let list = 0
  window.load_map_triggered = 0
  window.get_grid_data( 'grid_data', 0)
    .done(function(x){
      list = 1
      jsObject.grid_data = x
      if ( loop > 44 && list > 0 && window.load_map_triggered !== 1 ){
        window.load_map_triggered = 1
        load_map()
      }
    })
    .fail(function(){
      console.log('Error getting grid data')
      jsObject.grid_data = {'data': {}, 'highest_value': 1 }
    })
  jQuery.each(asset_list, function(i,v) {
    jQuery.ajax({
      url: jsObject.mirror_url + 'tiles/world/saturation/' + v,
      dataType: 'json',
      data: null,
      cache: true,
      beforeSend: function (xhr) {
        if (xhr.overrideMimeType) {
          xhr.overrideMimeType("application/json");
        }
      }
    })
      .done(function(x){
        loop++
        initialize_screen.val(loop)

        if ( 5 === loop ) {
          jQuery('#initialize-people').show()
        }

        if ( 15 === loop ) {
          jQuery('#initialize-activity').show()
        }

        if ( 22 === loop ) {
          jQuery('#initialize-coffee').show()
        }

        if ( 40 === loop ) {
          jQuery('#initialize-dothis').show()
        }

        if ( loop > 44 && list > 0 && window.load_map_triggered !== 1 ){
          window.load_map_triggered = 1
          load_map()
        }
      })
      .fail(function(){
        loop++
      })
  })
}) /* .ready() */

/**************************
 * Load map when precache is complete
 **************************/
function load_map() {
  jQuery('#initialize-screen').hide()

  // set title
  $('#panel-type-title').html(jsObject.translation.title)

  $('.loading-spinner').removeClass('active')

  let center = [-98, 38.88]
  let maxzoom = jsObject.zoom
  // if ( 'contacts' === jsObject.post_type ) {
  //   maxzoom = 13
  // }

  mapboxgl.accessToken = jsObject.map_key;
  let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/discipletools/cl1qp8vuf002l15ngm5a7up59',
    center: center,
    minZoom: 2,
    maxZoom: maxzoom,
    zoom: 3
  });
  if ( ! isMobile ) {
    map.addControl(
      new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        mapboxgl: mapboxgl,
        marker: false,
        position: 'top-left'
      })
    );
    map.addControl(
      new mapboxgl.GeolocateControl({
        positionOptions: {
          enableHighAccuracy: true
        },
        trackUserLocation: true,
        position: 'top-left'
      })
    );
    map.addControl(
      new mapboxgl.NavigationControl({
        position: 'bottom-right',
        showCompass: false
      })
    );
  }
  map.dragRotate.disable();
  map.touchZoomRotate.disableRotation();


  if ( get_map_start( 'heatmap_zoom_memory' ) ) {
    map.fitBounds(get_map_start( 'heatmap_zoom_memory' ))
  }
  map.on('zoomend', function(){
    set_map_start( 'heatmap_zoom_memory', map.getBounds() )
  })

  window.previous_hover = false

  let asset_list = []
  var i = 1;
  while( i <= 45 ){
    asset_list.push(i+'.geojson')
    i++
  }

  jQuery.each(asset_list, function(i,v){

    jQuery.ajax({
      url: jsObject.mirror_url + 'tiles/world/saturation/' + v,
      dataType: 'json',
      data: null,
      cache: true,
      beforeSend: function (xhr) {
        if (xhr.overrideMimeType) {
          xhr.overrideMimeType("application/json");
        }
      }
    })
      .done(function (geojson) {

        map.on('load', function() {

          jQuery.each(geojson.features, function (i, v) {
            if (typeof jsObject.grid_data.data[v.id] !== 'undefined' ) {
              geojson.features[i].properties.value = parseInt(jsObject.grid_data.data[v.id].percent)
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
              // 'line-color': '#323A68',
              'line-color': 'grey',
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
              'fill-color': {
                property: 'value',
                stops: [
                  [0, '#ffffff'],
                  [20, '#ba1200'],
                  [40, '#cc441c'],
                  [60, '#f7c162'],
                  [80, '#78945f'],
                  [100, '#4a7c59']
                ]
              },
              'fill-opacity': 0.75,
              'fill-outline-color': '#777777'
            }
          })
          /**********/
          /* end fill map */
          /**********/

          if ( jsObject.custom_marks.length > 0 ) {
            let lnglat
            jQuery.each( jsObject.custom_marks, function(i,v) {
              // lnglat = new mapboxgl.LngLat( v.lng, v.lat )
              // console.log(lnglat)
              new mapboxgl.Marker()
                .setLngLat([v.lng, v.lat])
                .addTo(map);
            })
          }

          map.on('mousemove', i.toString()+'fills', function (e) {
            if ( window.previous_hover ) {
              map.setFeatureState(
                window.previous_hover,
                { hover: false }
              )
              hide_details_panel()
            }
            window.previous_hover = { source: i.toString(), id: e.features[0].id }
            if (e.features.length > 0) {
              show_details_panel()
              map.setFeatureState(
                window.previous_hover,
                {hover: true}
              );
              $('#title').html(e.features[0].properties.full_name)
              $('#meter').val(jsObject.grid_data.data[e.features[0].properties.grid_id].percent)
              $('#saturation-goal').html(jsObject.grid_data.data[e.features[0].properties.grid_id].percent)
              $('#population').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)

              //report
              $('#report-modal-title').val(e.features[0].properties.full_name)
              $('#report-grid-id').val(e.features[0].properties.grid_id)

              let reported = jsObject.grid_data.data[e.features[0].properties.grid_id].reported
              $('#reported').html(reported)

              let needed = jsObject.grid_data.data[e.features[0].properties.grid_id].needed
              $('#needed').html(needed)
            }
          });
          map.on('click', i.toString()+'fills', function (e) {
            $('#modal_tile').html(e.features[0].properties.full_name)
            $('#modal_population').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)

            jQuery('.temp-spinner').html(`<span class="loading-spinner active"></span>`)

            window.get_grid_data( 'self', e.features[0].properties.grid_id )
              .done(function(data){
                load_self_content( data )
              })
            window.get_grid_data( 'a0', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a0' )
              })
            window.get_grid_data( 'a1', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a1' )
              })
            window.get_grid_data( 'a2', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a2' )
              })
            window.get_grid_data( 'a3', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a3' )
              })
            window.get_grid_data( 'world', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'world' )
              })

            // let ac = $('#activity-content')
            // ac.html('<span class="loading-spinner active"></span>')
            // window.get_activity_data(e.features[0].properties.grid_id)
            //   .done(function(data){
            //     ac.empty()
            //     if ( data.length < 1 ) {
            //       ac.append(`<div>No Movement Activity</div>`)
            //     } else {
            //       $.each(data, function(i,v){
            //         if ( typeof v.message !== 'undefined' ){
            //           ac.append(`<div><div style="float:left;width:180px;"><strong>${v.formatted_time}</strong></div> <span>${v.message}</span></div>`)
            //         }
            //       })
            //     }
            //   })

            $('#offCanvasNestedPush').foundation('toggle', e);
          });

        })

      }) /* ajax call */
  }) /* for each loop */

} /* .preCache */

/**************************
Support Functions
***************************/

function show_details_panel(){
  $('#details-panel').show()
  $('#training-start-screen').hide()
}
function hide_details_panel(){
  $('#details-panel').hide()
}



