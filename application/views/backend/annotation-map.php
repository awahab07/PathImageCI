<link rel="stylesheet" href="<?echo site_url()?>html/OpenLayers/style.css" type="text/css">
<link rel="stylesheet" href="<?echo site_url()?>html/OpenLayers/map-theme.css" type="text/css">
<link rel="stylesheet" href="<?echo site_url()?>html/OpenLayers/BootstrapOverviewMap.css" type="text/css">

<script src="<?echo site_url()?>html/OpenLayers/OpenLayers.js"></script>

<!-- Custom OpenLayer Controls -->
<script src="<?echo site_url()?>html/OpenLayers/BootstrapOverviewMap.js"></script>
<script src="<?echo site_url()?>html/OpenLayers/OpenLayersDeleteFeature.js"></script>

<!-- For Dialogs -->
<script type="text/javascript" src="<?echo base_url()?>html/backend/js/bootstrap-dialog.min.js"></script>

<script type="text/javascript">
    var map, annotationDrawingControl, navigationControl, featureHighlightControl, imageBounds, baseImageLayer, overviewLayer, overviewControl, annotationLayer;
    OpenLayers.ImgPath = "<?echo base_url()?>html/OpenLayers/img/";
    OpenLayers.themeCSSFileLocation = "<?echo base_url()?>html/OpenLayers/theme/default/style.css";
    
    function initializeAnnotationMap(){
        /* PHP to JS Initilizations */
        var annotationLayerHTTPProtocolURL = '<?php echo base_url().'platform/annotate/layer_protocol/'.$image_id.'/'.$first_layer_id;?>';
        var imageWidth = <?echo $map_image_data['image_width']?>;
        var imageHeight = <?echo $map_image_data['image_height']?>;
        imageBounds = new OpenLayers.Bounds(0, 0, <?echo $map_image_data['image_width']?>, <?echo $map_image_data['image_height']?>);
        imageUrl = '<?echo base_url().'uploads/images/'.$map_image_data['file_name']?>';

        var mapOptions = {
            controls: [
                new OpenLayers.Control.Navigation({zoomWheelEnabled:false}),
                new OpenLayers.Control.PanZoomBar({zoomWheelEnabled:false, zoomWorldIcon:true, sliderAdjustTop:2})
            ], 
            numZoomLevels: 8,
            maxExtent: imageBounds,
            maxResolution: 0.08,
            theme: false,
            units: "m"
        };

        map = new OpenLayers.Map('map', mapOptions);

        baseImageLayer = new OpenLayers.Layer.Image(
            'Image',
            imageUrl,
            imageBounds,
            new OpenLayers.Size(imageWidth / 5, imageHeight / 5),
            {
                numZoomLevels: 8
            }
        );
        
        overviewLayer = baseImageLayer.clone();
        overviewLayer.numZoomLevels = 16;

        // Vector Layer to draw polygons
        OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '1';

        // allow testing of specific renderers via "?renderer=Canvas", etc
        var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
        renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;


        // Annotation Layer with Strategy
        var defaultFeatureStyle = {strokeColor: "red", strokeOpacity: "0.5", strokeWidth: 1, fillColor: "tan", fillOpacity: 0.2, pointRadius: 2, cursor: "pointer"};
        var customFeatuerStyle = OpenLayers.Util.applyDefaults(defaultFeatureStyle, OpenLayers.Feature.Vector.style["default"]);
        var customStyleMap = new OpenLayers.StyleMap({
            'default': customFeatuerStyle,
            'select': {strokeColor: "red", fillColor: "orange", fillOpacity: 0.1}
        });
        
        var saveStrategy = new OpenLayers.Strategy.Save();
        saveStrategy.events.on({
            'success': function(event) {
                 alert('Changes saved');
            },
            'fail': function(event) {
                 alert('Error! Changes not saved');
            },
            scope: this
        });
        
        annotationLayer = new OpenLayers.Layer.Vector("Annotation Layer", {
            styleMap: customStyleMap,
            eventListeners: {
                "featuresadded": function(event) {
                    // 'this' is layer
                    //this.map.zoomToExtent(this.getDataExtent());
                },
                "sketchstarted": function(event){
                    featureHighlightControl.deactivate();
                },
                "sketchcomplete": function(event){
                    // Asking for annotation text to be bound with the drawn polygon
                    var annotationText = window.prompt("Enter annotation text for this patch");
                    event.feature.attributes.annotationText = annotationText;
                    
                    // Deactivation Annotation Drawing Control on Sketch completion
                    annotationDrawingControl.deactivate();
                    featureHighlightControl.activate();
                }
            },
            projection: map.getProjection(),
            strategies: [
                new OpenLayers.Strategy.Fixed(),
                saveStrategy
            ],
            protocol: new OpenLayers.Protocol.HTTP({
                url: annotationLayerHTTPProtocolURL,
                format: new OpenLayers.Format.GeoJSON({
                    ignoreExtraDims: true
                })
            }),
            renderers: renderer
        });
        
        map.addLayers([baseImageLayer, annotationLayer]);
        
        /*// Hardcoded feature to demonstrate drawing feature from Database
        var leftMostApexVertices = [
            new OpenLayers.Geometry.Point(-60.5914078125, 60.0510109375),
            new OpenLayers.Geometry.Point(-52.96368125, 20.1094609375),
            new OpenLayers.Geometry.Point(-56.29214375, 19.693403125),
            new OpenLayers.Geometry.Point(-65.8614734375, 19.8320890625),
            new OpenLayers.Geometry.Point(-67.8030765625, 20.1094609375)
        ];
        var leftMostApexRing = new OpenLayers.Geometry.LinearRing(leftMostApexVertices);
        var leftMostApexPolygon = new OpenLayers.Geometry.Polygon([leftMostApexRing]);
        var leftMostApexAttributes = {annotationText: 'Left Most Apex'};
        var leftMostApexFeature = new OpenLayers.Feature.Vector(leftMostApexPolygon, leftMostApexAttributes);
        annotationLayer.addFeatures([leftMostApexFeature]);*/


        // Controls and Toolbar Panel
        var drawControlPanel = new OpenLayers.Control.Panel({
             displayClass: ' drawingPanel '
        });
        annotationDrawingControl = new OpenLayers.Control.DrawFeature(annotationLayer, OpenLayers.Handler.Polygon, {title:'Create Annotation'});
        var navControl = new OpenLayers.Control.Navigation({title: 'Pan/Zoom'});
        drawControlPanel.addControls([
            annotationDrawingControl,
            new OpenLayers.Control.ModifyFeature(annotationLayer, {title: 'Edit Shape of Annotation'}),
            new DeleteFeature(annotationLayer, {title: 'Delete Annotation'}),
            new OpenLayers.Control.Button({displayClass: 'saveButton', trigger: function() {saveStrategy.save()}, title: 'Save Changes' }),
            navControl,
            new OpenLayers.Control.ZoomBox({alwaysZoom:true, title: 'Zoom to selected area'}),
            new OpenLayers.Control.ZoomToMaxExtent({title: 'Zoom out to entire image'})
        ]);
        drawControlPanel.defaultControl = navControl;
        map.addControl(drawControlPanel);
        
        // Feature HighlightControl
        featureHighlightControl = new OpenLayers.Control.SelectFeature(annotationLayer, {
            hover: true,
            highlightOnly: true,
            renderIntent: "temporary",
            eventListeners: {
                //beforefeaturehighlighted: report,
                featurehighlighted: function(evt) {
                    if(!evt.feature.popup){
                        evt.feature.popup = new OpenLayers.Popup.FramedCloud("info", 
                            evt.feature.geometry.getBounds().getCenterLonLat(),
                            null,
                            "<div style='font-size:.8em'>"+evt.feature.attributes.annotationText+"</div>",
                            null,
                            true,
                            null);
                        
                        map.addPopup(evt.feature.popup);   
                    }
                },
                featureunhighlighted: function(evt) {
                    if(evt.feature.popup){
                        map.removePopup(evt.feature.popup);
                        evt.feature.popup.destroy();
                        evt.feature.popup = null;
                    }
                }
            }
        });
        map.addControl(featureHighlightControl);
        featureHighlightControl.activate();

        // create an overview map control with non-default options
        var controlOptions = {
            maximized: false,
            mapOptions: OpenLayers.Util.extend({}, {
                maxResolution: 1,
                minResolution: 1
            }),
            autPan: false,
            minRatio: 10,
            maxRatio: 10,
            layers: [overviewLayer]
        };
        overviewControl = new OpenLayers.Control.BootstrapOverviewMap(controlOptions);
        map.addControl(overviewControl);

        // Initially zooming the map to its max extent
        map.zoomToMaxExtent();
    }
</script>

<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Annotate slide capture
            <small>use the controls bellow </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Platform</a></li>
            <li class="active">Upload Image</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="right-side">
        <div class="row row-fluid text-center">

            <div class="col-md-2">
            </div>
            
            <div class="col-md-8">
                <div class="map-wrapper">
                    <div id="right-draw-controls-panel"></div>
                    <div id="map" class="map" style="width: 600px; height: 400px; margin: 0 auto;"></div>
                <div>
            </div>

            <div class="col-md-2">
            </div>
        </div>
    </section><!-- /.content -->
</aside><!-- /.right-side -->
</div><!-- ./wrapper -->

<!-- DOM dependent scripts -->
<script type="text/javascript">
    initializeAnnotationMap();
</script>