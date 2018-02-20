<?php

class OpenSeadragonTEI_View_Helper_Viewer extends Zend_View_Helper_Abstract
{

  public $_supportedImageExtensions = array('bmp', 'gif', 'ico', 'jpeg', 'jpg',
                                          'png', 'tiff', 'tif', );
  public $_supportedVideoExtensions = array('webm', 'mp4', 'ogv', 'ogg');

  public $_supportedAudioExtensions = array('mp3');

  public $_supportedDocExtensions = array('xml');


  /**
   * Return a OpenSeadragon image viewer for the provided files.
   *
   * @param File|array $files A File record or an array of File records.
   * @return string|null
   */
  public function viewer($files, $item_type_id, $item, $page, $anchor)
  {
      if (!is_array($files)) {
          $files = array($files);
      }
      // Filter out invalid images.
      $validFiles = array();
      $validFiles['images'] = array();
      $validFiles['xml'] = array();
      foreach ($files as $file) {
          // A valid image must be a File record.
          if (!($file instanceof File)) {
              continue;
          }

          $extension = pathinfo($file->original_filename, PATHINFO_EXTENSION);
          // Catch XML files first
          if (strtolower($extension) == 'xml'){
            $validFiles['xml'] = $file;
          }
          // A valid image must have a supported extension.
          if (in_array(strtolower($extension), $this->_supportedImageExtensions)) {
              $validFiles['images'][] = $file;
          }

          if(in_array(strtolower($extension), $this->_supportedVideoExtensions)) {
              $validFiles['video'][] = $file;
          }

          if(in_array(strtolower($extension), $this->_supportedAudioExtensions)) {
              $validFiles['audio'][] = $file;
          }

      }
      // Return if there are no valid images.
      if (!$validFiles) {
          return;
      }
      $viewer = $this->getViewer($item_type_id);

      if($viewer){
        if (array_key_exists('video', $validFiles)) {
          $videoViewer = array();
          $videoViewer['osdViewer'] = 'video';
          $i = 0;
          foreach($validFiles['video'] as $videoFile){
            $videoViewer['videos'][$i] = $this->getVideoSourceInfo($videoFile);
            $i++;
          }
          if (array_key_exists('images', $validFiles)) {
            $videoViewer['poster'] = html_escape($validFiles['images'][0]->getWebPath('fullsize'));
          }
          $videoViewer['metadata'] = $this->getMetadata($item);
          return $this->view->partial('common/viewer.php', array(
              'viewer' => $videoViewer,
            ));
        } else {
          $viewerName = strtolower($viewer['viewer_name']);
          $viewerNameClean = str_replace(' ', '-', $viewerName);
          $openSeadragonViewer = array(
            'name' => $viewerNameClean,
            'buttonPath' => src('images/', 'openseadragon'),
            'tileSources' => $this->getTileSources($validFiles['images']),
            'imageCount' => sizeof($validFiles['images']),
            'audio' => array_key_exists('audio', $validFiles) ? $this->getAudioSourceInfo($validFiles['audio'][0]) : NULL,
            'metadata' => $this->getMetadata($item),
            'tempImage' => html_escape($validFiles['images'][0]->getWebPath('original')),
            'osdViewer' => 'image',
            'page' => $page,
          );
          if($viewer['xsl_viewer_option'] == 1){
            $openSeadragonViewer['xslURL'] = html_escape(open_seadragon_tei_generate_upload_web_path($viewer['xsl_url']));
            $openSeadragonViewer['osdViewer'] = 'tei';
            $openSeadragonViewer['anchor'] = $anchor;
            if($validFiles['xml']){
              $xml = $validFiles['xml'];
              $openSeadragonViewer['xmlURL'] = html_escape($xml->getWebPath('original'));
            }
          } elseif(array_key_exists('video', $validFiles)) {
            $openSeadragonViewer['osdViewer'] = 'video';
          } else {
            $openSeadragonViewer['osdViewer'] = 'image';
          }

          return $this->view->partial('common/viewer.php', array(
              'viewer' => $openSeadragonViewer,
            ));
        }
      } else {
        return;
      }
    }

    public function getViewer($item_type_id)
    {
      $viewer = open_seadragon_tei_get_viewer($item_type_id);
      if(!$viewer){
        return;
      } else {
        return $viewer[0];
      }
    }

    public function getTileSources($imageArray){
      $imageCount = sizeof($imageArray);
      if($imageCount > 1){
        $tileSources = array();
        foreach($imageArray as $image){
          $pyramid = openseadragon_create_pyramid($image, $sequence = TRUE);
          $tileSources[] = $pyramid;
        }
          return $tileSources;
      } else {
          $pyramid = openseadragon_create_pyramid($imageArray[0], $sequence = FALSE);
          return $pyramid;
      }
    }
    public function getMetadata($item){
      $escapedMetadata = '';
      // Add the title
      $escapedMetadata .= '<h2 class="item-meta-head" id="item-meta-title">' . metadata($item, array('Dublin Core', 'Title')) . '</h2>';
      $meta = all_element_texts($item, array('return_type' => 'array'));
      foreach($meta as $key=>$value){
        foreach($value as $title=>$meta){
          $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">' . $title . '</h4><div class="item-meta-value">' . $meta[0] . '</div></span>';
        }
      }
      // The tags
      if (metadata($item, 'has tags')) {
        $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">Tags</h4><div class="item-meta-value">' .  tag_string($item) . '</div></span>';
      }
      // The citation
      $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">Citation</h4><div class="item-meta-value">' . $item->getCitation() . '</div></span>';
      return $escapedMetadata;
    }
    public function getVideoSourceInfo($videoFile) {
      $videoSource = new stdClass();
      $videoSource->videoUrl = file_display_url($videoFile, $format="original");
      $videoSource->videoMimeType = $videoFile->mime_type;
      return $videoSource;
    }
    public function getAudioSourceInfo($audioFile) {
      $audioSource = new stdClass();
      $audioSource->audioUrl = file_display_url($audioFile, $format="original");
      $audioSource->audioMimeType = $audioFile->mime_type;
      return $audioSource;
    }
}

 ?>
