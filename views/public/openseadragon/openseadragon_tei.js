var OpenSeadragonTEIViewer = function(viewerSettings){

    this.name = viewerSettings['name'];
    this.buttonPath = viewerSettings['buttonPath'];
    this.tileSources = viewerSettings['tileSources'];
    this.xslURL = viewerSettings['xslURL'];
    this.xmlURL = viewerSettings['xmlURL'];
    this.metadata = viewerSettings['metadata'];
    this.imageCount = viewerSettings['imageCount'];
    this.osdViewerType = viewerSettings['osdViewer'];
    this.audioFile = viewerSettings['audio'];

  this.openSeadragonInit = function(){

    this.viewer = OpenSeadragon({
      id: this.name,
      prefixUrl: this.buttonPath,
      constrainDuringPan: true,
      visibilityRatio: 0.8,
      sequenceMode: true,
      showReferenceStrip: true,
      referenceStripPosition: "BOTTOM_LEFT",
      referenceStripScroll: 'vertical',
      referenceStripSizeRatio: 0.05,
      showNavigator:  true,
      navigatorAutoFade:  true,
      tileSources: this.tileSources,
      toolbar: "viewer-controls",
      zoomInButton:   "zoom-in",
      zoomOutButton:  "zoom-out",
      homeButton:     "home",
      nextButton:     "next",
      previousButton: "previous",
      navigatorPosition: "ABSOLUTE",
      navigatorTop:      "60px",
      navigatorLeft:     this.tileSources.length > 1 ? "100px" : "10px",
      navigatorHeight:   "12%",
      navigatorWidth:    "12%",
      gestureSettingsMouse: {
        pinchToZoom: true,
      }
    });

  }

  this.paginatorInit = function(imageCount){
    if(this.osdViewerType === 'tei') {
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
    } else {
      this.viewer.addHandler("page", function (data) {
        var pageCount = document.getElementById('page-count');
        pageCount.childNodes[0].innerHTML = 'Page ' + (data.page + 1) + ' of ' + imageCount;
      });
    }
  }

  this.addAudioPlayer = function() {
    var toolbar = document.getElementById('viewer-controls');
    var audioPlayer = document.createElement('audio');
    audioPlayer.className = 'rej-audio';
    audioPlayer.id = 'rej-audio-player';
    var src = document.createElement('source');
    src.src = this.audioFile.audioUrl;
    src.type = this.audioFile.audioMimeType;
    audioPlayer.controls = false;
    audioPlayer.appendChild(src);

    var playButton = document.createElement('div');
    playButton.className = 'toolbar-button';
    playButton.id = 'play-audio';
    playButton.innerHTML = '<i class="fa fa-play fa-2x" aria-hidden="true"></i>'

    var audioProgress = document.createElement('div');
    var audioProgressWidth = 150;
    audioProgress.className = 'toolbar-button';
    audioProgress.id = 'progress-audio';
    audioProgress.style.width = audioProgressWidth.toString() + 'px';
    audioProgress.style.height = '10px';
    var innerProgress = document.createElement('span');
    innerProgress.className = 'toolbar-button';
    innerProgress.id = 'progress-inner';
    audioProgress.appendChild(innerProgress);

    var audioDurationDisplay = document.createElement('div');
    audioDurationDisplay.className = 'toolbar-button';
    audioDurationDisplay.id = 'duration-audio';
    audioDurationDisplay.innerHTML = '00:00:00';

    togglePlay();
    updateProgress();
    seekProgress();
    toolbar.appendChild(playButton);
    toolbar.appendChild(audioProgress);
    toolbar.appendChild(audioDurationDisplay);
    toolbar.appendChild(audioPlayer);

    function togglePlay() {
      playButton.addEventListener('click', function() {
        if (!audioPlayer.paused) {
            audioPlayer.pause();
            playButton.innerHTML = '<i class="fa fa-play fa-2x" aria-hidden="true"></i>';
        } else {
            audioPlayer.play();
            playButton.innerHTML = '<i class="fa fa-pause fa-2x" aria-hidden="true"></i>';
        }
      }, false);
    }

    function updateProgress() {
      var value = 0;
      audioPlayer.addEventListener("timeupdate", function(){
        var progressValue = 0;
        var currentTime = audioPlayer.currentTime;
        var duration = audioPlayer.duration;
        if (currentTime > 0) {
          value = Math.floor((100 / duration) * currentTime);
        }
        if (currentTime === duration) {
          playButton.innerHTML = '<i class="fa fa-play fa-2x" aria-hidden="true"></i>';
          audioPlayer.pause();
          audioPlayer.currentTime = 0;
          value = 0;
        }
        innerProgress.style.width = value + '%';
        var currentTimeDisplay = displayCurrentTime(currentTime);
        audioDurationDisplay.innerHTML = currentTimeDisplay;
      }, false);
    }

    function seekProgress() {
      var width = audioProgressWidth;
      audioProgress.addEventListener("click", function(event){
        var value = Math.floor((event.offsetX / width) * 100);
        var seekTime = audioPlayer.duration * (value / 100);
        innerProgress.style.width = value + '%';
        audioPlayer.currentTime = seekTime;
      }, false);
    }

    function displayCurrentTime(currentTime) {
      var hours = Math.floor(currentTime / 3600);
      var minutes = Math.floor(currentTime / 60);
      var seconds = parseInt(currentTime - (hours * 3600) - (minutes * 60));

      var timecodeArray = [hours, minutes, seconds];
      var processedTimecodeArray = [];

      timecodeArray.forEach(function(time) {
        if(time < 10){
          var timeString = "0" + time;
          processedTimecodeArray.push(timeString);
        } else {
          var timeString = time.toString();
          processedTimecodeArray.push(timeString);
        }
      });
      return(processedTimecodeArray.join(':'));
    }
  }


/********* STATIC METHODS *********/
OpenSeadragonTEIViewer.saxonInit = function(xslURL, xmlURL, itemMetadata, viewerId){
  OpenSeadragonTEIViewer.setLoadingScreen();
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
      var toolbar = document.getElementById('viewer-controls');
      toolbar.appendChild(metadataPanel.metadataToggleButton);
      toolbar.appendChild(transcriptionPanel.transcriptionToggleButton);

      var viewer = document.getElementById('osd-flex-container');

      viewer.insertBefore(transcriptionPanel.transcriptionElement, viewer.firstChild);
      viewer.appendChild(metadataPanel.metadataElement);
      //var viewer = document.getElementsByClassName('openseadragon');
      //viewer.appendChild(container);
      //viewer.insertBefore(container, viewer.childNodes[0]);
      OpenSeadragonTEIViewer.prepareViewerFirstPage();
      var personElements = document.getElementsByClassName('persname-popover');
      var placeElements = document.getElementsByClassName('placename-popover');
      OpenSeadragonTEIViewer.formatModal(personElements, 'persname');
      OpenSeadragonTEIViewer.formatModal(placeElements, 'placename');
      OpenSeadragonTEIViewer.removeLoadingScreen();
    };
  }

  OpenSeadragonTEIViewer.formatModal = function(elements, displayType) {
    for(var i = 0; i < elements.length; i++) {
      var element = elements[i];
      element.onclick = function() {
        createModal(this);
      }
    }
    var createModal = function(element) {
      var modalDisplay = document.createElement('div');
      var modalTitle = document.createElement('h3');
      var modalContent = document.createElement('div');
      var closeButton = document.createElement('button');

      modalTitle.innerHTML = element.title;
      modalTitle.className = "modal-title";
      modalDisplay.className = "modal-display";
      closeButton.className = "modal-close";
      closeButton.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>';

      setTimeout(function() {
        modalDisplay.classList.add('show');
      }, 50);

      modalDisplay.classList.add(displayType);
      modalDisplay.addEventListener("transitionend", function(event) {
        var parent = modalDisplay.parentElement;
        if(modalDisplay.classList.contains('remove')){
           parent.removeChild(modalDisplay);
        }
      });

      closeButton.onclick = function() {
        modalDisplay.classList.remove('show');
        modalDisplay.classList.add('remove');
      }
      modalDisplay.appendChild(modalTitle);
      modalDisplay.appendChild(closeButton);

      if ('birth' && 'death' in element.dataset){
        var life = element.dataset.birth + '-' + element.dataset.death;
        element.removeAttribute('data-birth');
        element.removeAttribute('data-death');
        element.setAttribute('data-life', life);
      }

      for (var key in element.dataset){
          if (key == 'trigger' || key == 'toggle'){
          } else {
            var camelToHyphen = key.replace(/([a-z])([A-Z])/g, function(match1, match2, match3){return match2 + '-' + match3}).toLowerCase();
            var modalMetadata = document.createElement('div');
            modalMetadata.className = 'modal-metadata ' + camelToHyphen;
            if (camelToHyphen.indexOf('more-link') >= 0){
              var link = document.createElement('a');
              link.href = element.dataset[key];
              var linkText = document.createTextNode('More...');
              link.appendChild(linkText);
              link.target = 'blank';
              modalMetadata.appendChild(link);
            } else if (camelToHyphen.indexOf('tree-link') >= 0){
              var link = document.createElement('a');
              link.href = element.dataset[key];
              var linkText = document.createTextNode('Family Tree');
              link.appendChild(linkText);
              link.target = 'blank';
              modalMetadata.appendChild(link);
            } else if (camelToHyphen.indexOf('geo') >= 0){
              var iFrame = document.createElement('iframe');
              iFrame.width = 200;
              iFrame.height = 200;
              iFrame.frameborder = 0;
              var coords = element.dataset[key].split(' ');
              var lat = parseFloat(coords[0]).toFixed(7);
              var long = parseFloat(coords[1]).toFixed(7);
              iFrame.src = 'https://google.com/maps/embed/v1/place?key=AIzaSyAtYvdDAGkHB66xx6cHO3Dqxzwe1Dnz8-4&q=' + lat + ',' + long;
              console.log(iFrame.src);
              modalMetadata.appendChild(iFrame);
            } else {
              var span = document.createElement('span');
              span.innerHTML = element.dataset[key];
              modalMetadata.appendChild(span);
            }
            modalDisplay.appendChild(modalMetadata);
          }
      }
      element.parentElement.appendChild(modalDisplay);
    }
  }

  OpenSeadragonTEIViewer.imageViewerInit = function(itemMetadata, viewerId){
    var metadataPanel = OpenSeadragonTEIViewer.metadataPanelInit(itemMetadata);
    var container = document.getElementById('osd-flex-container');
    var toolbar = document.getElementById('viewer-controls');
    toolbar.appendChild(metadataPanel.metadataToggleButton);
    container.appendChild(metadataPanel.metadataElement);
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
    var metadataToggle = document.createElement('div');
    metadataToggle.id = 'toggle-metadata';
    metadataToggle.className = 'toolbar-button';
    metadataToggle.onclick = function() {OpenSeadragonTEIViewer.togglePanel('item-metadata','toggle-metadata', ['Hide Metadata ', 'Show Metadata '],
                                       ['<i class="fa fa-minus-square" aria-hidden="true"></i>',
                                       '<i class="fa fa-plus-square" aria-hidden="true"></i>']
                                       )};
    metadataToggle.innerHTML = 'Hide Metadata <i class="fa fa-minus-square" aria-hidden="true"></i>';
    var metadataPanel = {'metadataElement': metadata, 'metadataToggleButton': metadataToggle};
    return metadataPanel;
  }

  OpenSeadragonTEIViewer.transcriptionPanelInit = function(transformedDOM){
    // Set up viewer display
    var display = document.createElement('div');
    display.className = 'tei-viewer';
    display.id = 'item-transcription';
    display.innerHTML = transformedDOM;
    var transcriptionToggle = document.createElement('div');
    transcriptionToggle.id = 'toggle-transcription';
    transcriptionToggle.className = 'toolbar-button';
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
  OpenSeadragonTEIViewer.setLoadingScreen = function() {
    var primary = document.getElementById('viewer-fullpage-container');
    var loadingOverlay = document.createElement('div');
    var loadingMessage = document.createElement('div');
    loadingOverlay.id = 'loading-overlay-container';
    loadingOverlay.className = 'loading';
    loadingMessage.id = 'loading-overlay-message';
    loadingMessage.innerHTML = '<h3>Loading Viewer...</h3>';
    loadingOverlay.appendChild(loadingMessage);
    primary.appendChild(loadingOverlay);
    setTimeout(function() {
      loadingOverlay.classList.add('show');
    }, 50);
  }

  OpenSeadragonTEIViewer.removeLoadingScreen = function() {
    var loadingOverlay = document.getElementById('loading-overlay-container');
    loadingOverlay.addEventListener("transitionend", function(event) {
      var parent = loadingOverlay.parentElement;
      if(parent) {
        if(loadingOverlay.classList.contains('remove')){
           parent.removeChild(loadingOverlay);
        }
      }
    });
    loadingOverlay.classList.remove('show');
    loadingOverlay.classList.add('remove');
  }
}
