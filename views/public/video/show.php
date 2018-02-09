<?php echo $this->partial('common/header-video.php'); ?>
    <div class="video-container">
      <div class="video-centered">
      <video id="display-video" class="video-js vjs-default-skin vjs-big-play-centered">
        <source src="<?=$this->videoUrl?>" type="<?=$this->videoMimeType?>">
      </video>
      <script>
        var player = videojs('display-video', {
          controls: true,
          fluid: true,
          inactivityTimeout: 1500,
          poster: '<?=$this->poster ? $this->poster : 'false' ?>',
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
    </div>
  </div>
  </body>
</html>
