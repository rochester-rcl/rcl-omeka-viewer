<div class="openseadragon" id="openseadragon-viewer-container">
  <?php if ($viewer['osdViewer'] === 'video') : ?>
    <?php
    $vimeo = array_key_exists('vimeoURL', $viewer);
    $videoUrl = ($vimeo ? $viewer['vimeoURL'] : $viewer['videos'][0]->videoUrl);
    $videoMimeType = ($vimeo ? "video/vimeo" : $viewer['videos'][0]->videoMimeType);
    if (array_key_exists('poster', $viewer)) {
      $poster = $viewer['poster'];
    } else {
      $poster = 'false';
    }
    ?>
    <div class="openseadragon-flex-container" id="osd-flex-container">
      <div id="video-viewer">
        <div class="toolbar" id="video-controls">
          <div class="toolbar-button" onClick="navBack()" alt="go back" id="back"><i class="fa fa-angle-double-left fa-3x" aria-hidden="false"></i></div>
        </div>
        <video id="display-video" class="video-js vjs-default-skin vjs-big-play-centered" data-setup='{"techOrder": ["html5", "vimeo"], "sources": [{"type": "<?= $videoMimeType ?>", "src": "<?= $videoUrl ?>"}]}'>
        </video>
        <script>
          var player = videojs('display-video', {
            controls: true,
            fluid: true,
            inactivityTimeout: false,
            poster: '<?= $poster ?>',
            plugins: {
              nleControls: {
                smpteTimecode: true,
                frameControls: true,
                framerate: 24.0,
              },
              framerate: {
                origFramerate: 24.0,
              }
            }
          });

          var metadataContainer = document.createElement('div');
          metadataContainer.id = "item-metadata";
          metadataContainer.className = "tei-viewer";
          metadataContainer.innerHTML = '<?= $viewer['metadata'] ?>';
          var videoViewer = document.getElementById('osd-flex-container');
          videoViewer.appendChild(metadataContainer);

          function navBack() {
            window.history.back();
          }
        </script>
      </div>
    </div>
  <?php else : ?>
    <?php $jsonViewer = json_encode($viewer); ?>
    <div class="toolbar" id="viewer-controls">
      <div class="toolbar-button" onClick="navBack()" alt="go back" id="back"><i class="fa fa-angle-double-left fa-2x" aria-hidden="false"></i></div>
      <div class="toolbar-button" id="zoom-in"><i class="fa fa-search-plus fa-2x" aria-hidden="false"></i></div>
      <div class="toolbar-button" id="zoom-out"><i class="fa fa-search-minus fa-2x" aria-hidden="true"></i></div>
      <div class="toolbar-button" id="home"><i class="fa fa-compress fa-2x" aria-hidden="true"></i></div>
      <div class="toolbar-button" id="full-page"><i class="fa fa-arrows-alt fa-2x" aria-hidden="true"></i></div>
      <div class="toolbar-button" id="previous"><i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i></div>
      <div class="toolbar-button" id="next"><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></div>
      <div class="toolbar-button" id="page-count"><span>Page 1 of <?= $viewer['imageCount'] ?></span></div>
    </div>
    <div class="openseadragon-flex-container" id="osd-flex-container">
      <div class="openseadragon_viewer" id="<?= $viewer['name'] ?>">
        <img src="<?= $viewer['tempImage'] ?>" class="openseadragon-image tmp-img" alt="">
      </div>
      <script type="text/javascript">
        //Viewer
        var osdViewer = new OpenSeadragonTEIViewer(<?= $jsonViewer ?>);
        switch (osdViewer.osdViewerType) {
          case 'tei':
            osdViewer.openSeadragonInit();
            if (osdViewer.xmlURL !== undefined) {
              OpenSeadragonTEIViewer.saxonInit(osdViewer.xslURL, osdViewer.xmlURL, osdViewer.metadata, osdViewer.name, function() {
                osdViewer.viewer.goToPage(<?= $viewer['page'] - 1 ?>);
                if (osdViewer.anchor) {
                  location.hash = "#" + osdViewer.anchor;
                }
              });
              osdViewer.paginatorInit(osdViewer.imageCount);
            } else {
              OpenSeadragonTEIViewer.imageViewerInit(osdViewer.metadata, osdViewer.name);
              osdViewer.paginatorInit(osdViewer.imageCount);
              osdViewer.viewer.goToPage(<?= $viewer['page'] - 1 ?>);
            }
            break;
          case 'image':
            osdViewer.openSeadragonInit();
            if (osdViewer.audioFile !== null) {
              osdViewer.addAudioPlayer();
            }
            if (osdViewer.transcription && osdViewer.transcription.length > 0) {
              OpenSeadragonTEIViewer.simpleTranscriptionInit(osdViewer.transcription, osdViewer.title);
            }
            OpenSeadragonTEIViewer.imageViewerInit(osdViewer.metadata, osdViewer.name);
            osdViewer.paginatorInit(osdViewer.imageCount);
            osdViewer.viewer.goToPage(<?= $viewer['page'] - 1 ?>);
            break;
        }

        function navBack() {
          window.history.back();
        }

        function patchPublicItemsShow() {
          // put everything that gets rendered via public_items_show hook in the metadata panel
          var publicItems = document.getElementById("public-items-show-container");
          var cloned = publicItems.cloneNode(true);
          var md = document.getElementById("item-metadata");
          md.appendChild(cloned);
          publicItems.parentElement.removeChild(publicItems);
        }
        window.addEventListener('load', patchPublicItemsShow);
      </script>
    </div>
    <div id="public-items-show-container">
      <?php fire_plugin_hook('public_items_show', ['view' => $this, 'item' => $viewer['item']]); ?>
    </div>
  <?php endif; ?>
</div>