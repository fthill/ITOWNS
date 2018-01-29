<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Globe</title>
        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="examples/GUI/dat.gui/dat.gui.min.js"></script>
        <link rel="stylesheet" type="text/css" href="itowns.css">
    </head>
    <body>


        <div class="Letout">
        <div id='formulaire'>

            <form id="searchthis" method="post">
              <fieldset id="test">
                <legend>Rechercher un lieu</legend>
              <select id="ville" action="">
                <option value="Champs-sur-Marne">ENSG</option>
                <option value='paris'>Paris</option>
                <option value='lyon'>Lyon</option>
                <option value='marseille'>Marseille</option>
              </select>
            <input id="search-btn" type="submit" value="Rechercher" name = "search" />
          </fieldset>
            </form>
            <form id="ajout" method="get">
            <fieldset> <legend>Ajouter un lieu</legend>
              <input id="search" name"p" type="text" placeholder="Entrer un nom"/>
              <input id="search" name"p" type="text" placeholder="Entrer une latitude"/>
              <input id="search" name"o" type="text" placeholder="Entrer une longitude"/>
              <input id="search-btn" type="submit" value="Valider" />
            </fieldset>
            </form>
          </div>
        <div id="viewerDiv"></div>
        </div>
        <script src="examples/GUI/GuiTools.js"></script>
        <script src="itowns.js"></script>
        <script type="text/javascript">
            /* global itowns,document,GuiTools*/

            // Initial position
            var positionOnGlobe = { longitude: 2.587316, latitude: 48.841063, altitude: 10000 };

            // iTowns namespace defined here
            // Creation of the HTML DOM where the view will be initialized
            var viewerDiv = document.getElementById('viewerDiv');
            // Creation of the globe
            var globeView = new itowns.GlobeView(viewerDiv, positionOnGlobe);
            // Creation of the menu
            var menuGlobe = new GuiTools('menuDiv', globeView);
            menuGlobe.view = globeView;
            function addLayerCb(layer) {
                return globeView.addLayer(layer);
            }

            // Adding Layers
            var promises = []
            promises.push(itowns.Fetcher.json('examples/layers/JSONLayers/Ortho.json').then(addLayerCb));
            promises.push(itowns.Fetcher.json('examples/layers/JSONLayers/WORLD_DTM.json').then(addLayerCb));
            promises.push(itowns.Fetcher.json('examples/layers/JSONLayers/IGN_MNT_HIGHRES.json').then(addLayerCb));

            menuGlobe.addGUI('RealisticLighting', false,
                function(newValue) { globeView.setRealisticLightingOn(newValue); });

            // Adding layers in the menu
            Promise.all(promises).then(function () {
                menuGlobe.addImageryLayersGUI(globeView.getLayers(function (l) { return l.type === 'color'; }));
                menuGlobe.addElevationLayersGUI(globeView.getLayers(function(l) { return l.type === 'elevation'; }));
                console.info('menuGlobe initialized');
            }).catch( function (e) { console.error(e) });

            // Wait that the globe is well initialized, you should put your function in this event
            globeView.addEventListener(itowns.GLOBE_VIEW_EVENTS.GLOBE_INITIALIZED, function () {
                // eslint-disable-next-line no-console
                console.info('Globe initialized');


                var form_selection = document.getElementById('searchthis')
                var liste_ville = document.getElementById('ville');


                function chang_lieu(event){
                  event.preventDefault();
                  var ajax = new XMLHttpRequest();
                  var lieu_choisi = liste_ville.options[liste_ville.selectedIndex].value;
                  ajax.open('GET', 'http://api.geonames.org/searchJSON?q='+lieu_choisi+'&maxRows=1&username=AlexFloProjetWeb',true);
                  ajax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
                  ajax.addEventListener('readystatechange',function(e){
                    if(ajax.readyState == 4 && ajax.status == 200){
                      console.log('entré dans if');
                      var result = JSON.parse(ajax.responseText);
                      var lng = result.geonames[0].lng;
                      var lat = result.geonames[0].lat;
                      globeView.controls.setCameraTargetGeoPosition({longitude:lng, latitude:lat}, true);
                    }

                  })
                ajax.send();

                }
                form_selection.addEventListener('submit',chang_lieu);
            });


            window.globeView = globeView;
</script>
        <div id='pieddepage'>
          <?php
          if (isset($_POST['search'])) {
            print_r($_POST['ville']);

          }
          if($_POST['search']= 'Paris'){
            echo "<img src='paris.jpg' alt='exteAlternatif' /><img src='ParisArc.jpg' alt='TexteAlternatif' /><img src='ParisLouvre.jpg' alt='TexteAlternatif' /><img src='ParisNotre_dame.jpg' alt='TexteAlternatif' />";
          }elseif ($_POST["search"]='Lyon') {
            echo "<img src='lyon.jpg' alt='TexteAlternatif' /><img src='LyonConfluences.jpg' alt='TexteAlternatif' /><img src='Lyon-header.jpg' alt='TexteAlternatif' /><img src='LyonShow.jpg' alt='TexteAlternatif' />";
          }elseif ($_POST["search"]='Marseille') {
            echo"<img src='marseille.jpg' alt='TexteAlternatif' /><img src='MarseillePort.jpg' alt='TexteAlternatif' /><img src='Marseillecathédrale.jpg' alt='TexteAlternatif' /><img src='MarseilleRues.jpg' alt='TexteAlternatif' />";
          }else{
            echo"<img src='enpc.jpg' alt='TexteAlternatif' /><img src='ENSG.jpg' alt='TexteAlternatif' /><img src='ensg2.jpg' alt='TexteAlternatif' /><img src='logo_ensg.png' alt='TexteAlternatif' />";
          } ?>
        </div>
    </body>
</html>
