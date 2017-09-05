<?php
  $videoUrl = $this->videoUrl;
  $videoMimeType = $this->mime;
  if($this->poster){
    $poster = $this->poster;
  } else {
    $poster = 'false';
  }
?>
<div id="video-viewer">
  <div class="toolbar" id="video-controls">
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
  </script>
</div>
