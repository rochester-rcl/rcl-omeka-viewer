


var OpenSeadragonTEIViewer = function(viewerSettings){

    this.width = viewerSettings['width'] + 'px';
    this.height = viewerSettings['height'] + 'px';
    this.name = viewerSettings['name'];
    this.buttonPath = viewerSettings['buttonPath'];
    this.tileSources = viewerSettings['tileSources'];
    this.xslURL = viewerSettings['xslURL'];
    this.xmlURL = viewerSettings['xmlURL'];
    this.metadata = viewerSettings['metadata'];
    this.imageCount = viewerSettings['imageCount'];
    this.osdViewerType = viewerSettings['osdViewer'];


  this.setViewerDimensions = function(){
    var viewerContainer = document.getElementsByClassName('openseadragon');
    for(var i = 0; i < viewerContainer.length; i++){
      viewerContainer[i].style.width = this.width;
      viewerContainer[i].style.height = this.height;
    }
    document.getElementById(this.name).style.width = this.width;
  }

  this.openSeadragonInit = function(){
    this.viewer = OpenSeadragon({
      id: this.name,
      prefixUrl: this.buttonPath,
      minZoomImageRatio: 0.7,
      defaultZoomLevel: 0.7,
      maxZoomPixelRatio: 2,
      animationTime: 1.5,
      blendTime: 0.5,
      constrainDuringPan: true,
      springStiffness: 5,
      visibilityRatio: 0.8,
      sequenceMode: true,
      showReferenceStrip: true,
      showNavigator:  true,
      navigatorAutoFade:  true,
      tileSources: this.tileSources,
      toolbar: "viewer-controls",
      zoomInButton:   "zoom-in",
      zoomOutButton:  "zoom-out",
      homeButton:     "home",
      fullPageButton: "full-page",
      nextButton:     "next",
      previousButton: "previous",
      navigatorPosition: "TOP_RIGHT",
    });
  }
  this.paginatorInit = function(imageCount){
    this.viewer.addHandler("page", function (data) {
      var pages = document.getElementsByClassName('pb');
      var pageCount = document.getElementById('page-count');
      for(var i = 0; i < pages.length; i++){
        var pageNumber = pages[i].dataset.pageNumber;
        pageCount.childNodes[0].innerHTML = 'Page ' + (data.page + 1) + ' of ' + imageCount;
        if (pageNumber == data.page){
          pages[i].style.display = 'block';
        } else {
          pages[i].style.display = 'none';
        }
      }
    });

  }
/********* STATIC METHODS *********/
OpenSeadragonTEIViewer.saxonInit = function(xslURL, xmlURL, itemMetadata, viewerId){
  self.onSaxonLoad = function() {
    // Parse Attached XML
    var xsl = Saxon.requestXML(xslURL);
    var xml = Saxon.requestXML(xmlURL);
    var transformed = OpenSeadragonTEIViewer.transformToHTML(xml, xsl);

    // Set up metadata display
    var metadataPanel = OpenSeadragonTEIViewer.metadataPanelInit(itemMetadata);
    // Set up TEI display
    var transcriptionPanel = OpenSeadragonTEIViewer.transcriptionPanelInit(transformed);

    var container = document.createElement('div');
    container.className = 'tei-container';
    container.appendChild(transcriptionPanel.transcriptionToggleButton);
    container.appendChild(metadataPanel.metadataToggleButton);
    container.appendChild(transcriptionPanel.transcriptionElement);
    container.appendChild(metadataPanel.metadataElement);
    //var viewer = document.getElementById(viewerId);
    var viewer = document.getElementsByClassName('openseadragon');
    viewer[0].appendChild(container);
    //viewer.insertBefore(container, viewer.childNodes[0]);
    OpenSeadragonTEIViewer.prepareViewerFirstPage();
    };
  }

  OpenSeadragonTEIViewer.imageViewerInit = function(itemMetadata, viewerId){
    var metadataPanel = OpenSeadragonTEIViewer.metadataPanelInit(itemMetadata);
    var container = document.createElement('div');
    container.className = 'tei-container';
    container.appendChild(metadataPanel.metadataToggleButton);
    container.appendChild(metadataPanel.metadataElement);
    /*var viewer = document.getElementById(viewerId);
    viewer.insertBefore(container, viewer.childNodes[0]);
    */
    var viewer = document.getElementsByClassName('openseadragon');
    viewer[0].appendChild(container);
  }

  OpenSeadragonTEIViewer.transformToHTML = function(xml, xsl){
    var processor = Saxon.newXSLT20Processor();
    processor.importStylesheet(xsl);
    var transformed = processor.transformToFragment(xml);
    var serialized = Saxon.serializeXML(transformed);
    return serialized;
  }

  OpenSeadragonTEIViewer.prepareViewerFirstPage = function(){
    var pages = document.getElementsByClassName('pb');
    for(var i = 0; i < pages.length; i++){
      var pageNumber = pages[i].dataset.pageNumber;
      if (pageNumber == 0){
        pages[i].style.display = 'block';
      } else {
        pages[i].style.display = 'none';
      }
    }
  }

  OpenSeadragonTEIViewer.metadataPanelInit = function(itemMetadata){
    // Set up metadata display
    var metadata = document.createElement('div');
    metadata.id = 'item-metadata';
    metadata.className = 'tei-viewer';
    metadata.innerHTML = itemMetadata;

    var metadataToggle = document.createElement('span');
    metadataToggle.id = 'toggle-metadata';
    metadataToggle.onclick = function() {OpenSeadragonTEIViewer.togglePanel('item-metadata','toggle-metadata', ['Hide Metadata ', 'Show Metadata '],
                                       ['<i class="fa fa-minus-square" aria-hidden="true"></i>',
                                       '<i class="fa fa-plus-square" aria-hidden="true"></i>']
                                       )};
    metadataToggle.innerHTML = '<span id="metadata-dialogue">Hide Metadata </span><i class="fa fa-minus-square" aria-hidden="true"></i>';
    var metadataPanel = {'metadataElement': metadata, 'metadataToggleButton': metadataToggle};
    return metadataPanel;
  }

  OpenSeadragonTEIViewer.transcriptionPanelInit = function(transformedDOM){
    // Set up viewer display
    var display = document.createElement('div');
    display.className = 'tei-viewer';
    display.id = 'item-transcription';
    display.innerHTML = transformedDOM;
    var transcriptionToggle = document.createElement('span');
    transcriptionToggle.id = 'toggle-transcription';
    transcriptionToggle.onclick = function() {OpenSeadragonTEIViewer.togglePanel("item-transcription", 'toggle-transcription',
                                            ['Hide Transcription ', 'Show Transcription '],
                                            ['<i class="fa fa-minus-square" aria-hidden="true"></i>',
                                            '<i class="fa fa-plus-square" aria-hidden="true"></i>']
                                            )};

    transcriptionToggle.innerHTML = '<span id="transcription-dialogue">Hide Transcription </span><i class="fa fa-minus-square" aria-hidden="true"></i>';
    var transcriptionPanel = {'transcriptionElement': display, 'transcriptionToggleButton': transcriptionToggle};
    return transcriptionPanel;

  }

  OpenSeadragonTEIViewer.togglePanel = function(panelId, toggleId, dialogString, fontAwesomeTags){
    panel = document.getElementById(panelId);
    toggleDialogue = document.getElementById(toggleId);
    if (panel.classList.contains('hidden')){
      //panel.style.visibility = 'visible';
      panel.classList.remove('hidden');
      panel.classList.add('show');
      toggleDialogue.innerHTML = '<span id="transcription-dialogue">' + dialogString[0] + '</span>' + fontAwesomeTags[0];
    } else {
      //panel.style.visibility = 'hidden';
      panel.classList.add('hidden');
      panel.classList.remove('show');
      toggleDialogue.innerHTML = '<span id="transcription-dialogue">' + dialogString[1] + '</span>' + fontAwesomeTags[1];
    }
  }

}
