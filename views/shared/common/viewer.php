<div class="openseadragon" id="openseadragon-viewer-container">

      <?php if ($viewer['osdViewer'] === 'video') : ?>
        <?php
          $videoUrl = $viewer['videos'][0]->videoUrl;
          $videoMimeType = $viewer['videos'][0]->videoMimeType;
          if($viewer['poster']){
            $poster = $viewer['poster'];
          } else {
            $poster = 'false';
          }
        ?>
        <div id="video-viewer">
          <div class="toolbar" id="video-controls">
            <div class="toolbar-button" onClick="navBack()" alt="go back" id="back"><i class="fa fa-angle-double-left fa-3x" aria-hidden="false"></i></div>
          </div>
          <video id="display-video" class="video-js vjs-default-skin vjs-big-play-centered">
            <source src="<?=$videoUrl?>" type="<?=$videoMimeType?>">
          </video>
          <script>
            var player = videojs('display-video', {
              controls: true,
              fluid: true,
              inactivityTimeout: false,
              poster: '<?=$poster?>',
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
            metadataContainer.innerHTML = '<?=$viewer['metadata']?>';
            var videoViewer = document.getElementById('openseadragon-viewer-container');
            videoViewer.appendChild(metadataContainer);

            function navBack() {
              window.history.back();
            }
        </script>
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
          <div class="toolbar-button" id="page-count"><span>Page 1 of <?=$viewer['imageCount']?></span></div>
      </div>

      <div class="openseadragon_viewer" id="<?=$viewer['name']?>">

          <img src="<?=$viewer['tempImage']?>" class="openseadragon-image tmp-img" alt="">
      </div>
      <script type="text/javascript">
        //Viewer
        var osdViewer = new OpenSeadragonTEIViewer(<?=$jsonViewer?>);
        switch(osdViewer.osdViewerType){
          case 'tei':
            osdViewer.openSeadragonInit();
            OpenSeadragonTEIViewer.saxonInit(osdViewer.xslURL, osdViewer.xmlURL, osdViewer.metadata, osdViewer.name);
            osdViewer.paginatorInit(osdViewer.imageCount);
            break;
          case 'image':
            osdViewer.openSeadragonInit();
            if(osdViewer.audioFile !== null) {
              osdViewer.addAudioPlayer();
            }
            OpenSeadragonTEIViewer.imageViewerInit(osdViewer.metadata, osdViewer.name);
            osdViewer.paginatorInit(osdViewer.imageCount);
            break;
        }
        function navBack() {
          window.history.back();
        }
        osdViewer.viewer.goToPage(<?=$viewer['page'] - 1?>);
      </script>
    <?php endif; ?>
</div>
