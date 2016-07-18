<?php $jsonViewer = json_encode($viewer); ?>
<div class="openseadragon">
    <div class="navigator-container">
      <div id="viewer-navigator"></div>
    </div>
    <div class="toolbar" id="viewer-controls">
        <div id="zoom-in"><i class="fa fa-search-plus fa-2x" aria-hidden="false"></i></div>
        <div id="zoom-out"><i class="fa fa-search-minus fa-2x" aria-hidden="true"></i></div>
        <div id="home"><i class="fa fa-compress fa-2x" aria-hidden="true"></i></div>
        <div id="full-page"><i class="fa fa-arrows-alt fa-2x" aria-hidden="true"></i></div>
        <div id="previous"><i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i></div>
        <div id="next"><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></div>
        <div id="page-count"><h4>Page 1 of <?=$viewer['imageCount']?></h4></div>
    </div>


    <div class="openseadragon_viewer" id="<?=$viewer['name']?>">

        <img src="<?=$viewer['tempImage']?>" class="openseadragon-image tmp-img" alt="">
    </div>
    <script type="text/javascript">
      //Viewer
      var osdViewer = new OpenSeadragonTEIViewer(<?=$jsonViewer?>);
      if (osdViewer.osdViewerType === 'tei'){
          osdViewer.setViewerDimensions();
          osdViewer.openSeadragonInit();
          OpenSeadragonTEIViewer.saxonInit(osdViewer.xslURL, osdViewer.xmlURL, osdViewer.metadata, osdViewer.name);
          osdViewer.paginatorInit(osdViewer.imageCount, mode='tei');
      } else {
        osdViewer.setViewerDimensions();
        osdViewer.openSeadragonInit();
        OpenSeadragonTEIViewer.imageViewerInit(osdViewer.metadata, osdViewer.name);
        osdViewer.paginatorInit(osdViewer.imageCount, mode='image');
      }
    </script>
</div>
