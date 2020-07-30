<?php

class OpenSeadragonTEI_View_Helper_Viewer extends Zend_View_Helper_Abstract
{

  public $_supportedImageExtensions = array(
    'bmp', 'gif', 'ico', 'jpeg', 'jpg',
    'png', 'tiff', 'tif',
  );
  public $_supportedVideoExtensions = array('webm', 'mp4', 'ogv', 'ogg');

  public $_supportedAudioExtensions = array('mp3');

  public $_supportedDocExtensions = array('xml');

  private $_vimeoItemTypeKey = 'vimeo_url';
  private $_viewer = null;

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

    $validFiles['xml'] = array();
    foreach ($files as $file) {
      // A valid image must be a File record.
      if (!($file instanceof File)) {
        continue;
      }

      $extension = pathinfo($file->original_filename, PATHINFO_EXTENSION);
      // Catch XML files first
      if (strtolower($extension) == 'xml') {
        $validFiles['xml'] = $file;
      }
      // A valid image must have a supported extension.
      if (in_array(strtolower($extension), $this->_supportedImageExtensions)) {
        $validFiles['images'][] = $file;
      }

      if (in_array(strtolower($extension), $this->_supportedVideoExtensions)) {
        $validFiles['video'][] = $file;
      }

      if (in_array(strtolower($extension), $this->_supportedAudioExtensions)) {
        $validFiles['audio'][] = $file;
      }
    }
    // check for vimeo urls
    $vimeo = $this->checkForVimeoURL($item);
    // Return if there are no valid images.
    if (!$validFiles && !$vimeo) {
      return;
    }
    $viewer = $this->getViewer($item_type_id);
    if ($viewer) {
      $meta = $this->getMetadata($item);
      if ($vimeo) {
        $videoViewer = array();
        $videoViewer['osdViewer'] = 'video';
        if (array_key_exists('images', $validFiles)) {
          $videoViewer['poster'] = html_escape($validFiles['images'][0]->getWebPath('fullsize'));
        }
        $videoViewer['metadata'] = $meta;
        $videoViewer['vimeoURL'] = $vimeo;
        return $this->view->partial('common/viewer.php', array(
          'viewer' => $videoViewer,
        ));
      }
      if (array_key_exists('video', $validFiles)) {
        $videoViewer = array();
        $videoViewer['osdViewer'] = 'video';
        $i = 0;
        foreach ($validFiles['video'] as $videoFile) {
          $videoViewer['videos'][$i] = $this->getVideoSourceInfo($videoFile);
          $i++;
        }
        if (array_key_exists('images', $validFiles)) {
          $videoViewer['poster'] = html_escape($validFiles['images'][0]->getWebPath('fullsize'));
        }
        $videoViewer['metadata'] = $meta;
        return $this->view->partial('common/viewer.php', array(
          'viewer' => $videoViewer,
        ));
      } else {
        $viewerName = strtolower($viewer->viewer_name);
        $viewerNameClean = str_replace(' ', '-', $viewerName);
        $openSeadragonViewer = array(
          'name' => $viewerNameClean,
          'buttonPath' => src('images/', 'openseadragon'),
          'tileSources' => $this->getTileSources($validFiles['images']),
          'imageCount' => sizeof($validFiles['images']),
          'audio' => array_key_exists('audio', $validFiles) ? $this->getAudioSourceInfo($validFiles['audio'][0]) : NULL,
          'metadata' => $meta,
          'tempImage' => html_escape($validFiles['images'][0]->getWebPath('original')),
          'osdViewer' => 'image',
          'page' => $page,
          'title' => $this->getTitle($item),
        );
        if ($viewer->xsl_viewer_option == 1) {
          $openSeadragonViewer['xslURL'] = html_escape(open_seadragon_tei_generate_upload_web_path($viewer->xsl_url));
          $openSeadragonViewer['osdViewer'] = 'tei';
          $openSeadragonViewer['anchor'] = $anchor;
          if ($validFiles['xml']) {
            $xml = $validFiles['xml'];
            $openSeadragonViewer['xmlURL'] = html_escape($xml->getWebPath('original'));
          }
        } elseif (array_key_exists('video', $validFiles)) {
          $openSeadragonViewer['osdViewer'] = 'video';
        } else {
          $openSeadragonViewer['osdViewer'] = 'image';
          $openSeadragonViewer['transcription'] = $this->getTranscription($item);
        }
        $openSeadragonViewer['item'] = $item;
        return $this->view->partial('common/viewer.php', array(
          'viewer' => $openSeadragonViewer,
        ));
      }
    } else {
      return;
    }
  }

  public function getViewer($item_type_id = null)
  {
    if (!$this->_viewer) {
      if ($item_type_id === null) return;
      $viewer = open_seadragon_tei_get_viewer($item_type_id);
      if (!$viewer) {
        return null;
      } else {
        $this->_viewer = $viewer;
      }
    }
    return $this->_viewer;
  }

  public function getTileSources($imageArray)
  {
    $imageCount = sizeof($imageArray);
    if ($imageCount > 1) {
      $tileSources = array();
      foreach ($imageArray as $image) {
        $pyramid = openseadragon_create_pyramid($image, $sequence = TRUE);
        $tileSources[] = $pyramid;
      }
      return $tileSources;
    } else {
      $pyramid = openseadragon_create_pyramid($imageArray[0], $sequence = FALSE);
      return $pyramid;
    }
  }
  public function checkForVimeoURL($item)
  {
    $itemTypeTexts = item_type_elements($item);
    if (array_key_exists($this->_vimeoItemTypeKey, $itemTypeTexts)) {
      return $itemTypeTexts['vimeo_url'];
    }
    return false;
  }

  public function getTranscription($item)
  {
    $viewer = $this->getViewer();
    $element_name = get_element_name($viewer->transcriptions_field_id);
    // try for Dublin Core first
    try {
      $transcription = metadata($item, ['Dublin Core', $element_name]);
      return $transcription;
    } catch(Exception $e) {
      $transcription = metadata($item, ['Item Type Metadata', $element_name], ['all' => true]);
      return $transcription;
    } catch(Exception $e) {
      return null;
    }
    return null;
  }

  public function getTitle($item) {
    return metadata($item, array('Dublin Core', 'Title'));
  }

  public function getMetadata($item)
  {
    $viewer = $this->getViewer();
    $escapedMetadata = '';
    // Add the title
    $escapedMetadata .= '<h2 class="item-meta-head" id="item-meta-title">' . $this->getTitle($item) . '</h2>';
    $meta = all_element_texts($item, array('return_type' => 'array'));
    foreach ($meta as $key => $value) {
      foreach ($value as $title => $meta) {
        if ($title === get_element_name($viewer->transcriptions_field_id)) continue;
        $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">'
          . $title . '</h4>';
        foreach ($meta as $md) {
          $escapedMetadata .= '<div class="item-meta-value">' . $md . '</div>';
        }
        $escapedMetadata .= '</span>';
      }
    }
    // The tags
    if (metadata($item, 'has tags')) {
      $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">Tags</h4><div class="item-meta-value">' .  tag_string($item) . '</div></span>';
    }
    // The collection 
    $collection = get_collection_for_item($item);
    if ($collection) {
      $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">Collection</h4><div class="item-meta-value">' . link_to_collection(NULL, [], 'show', $collection) . '</div></span>';
    }
    // The citation
    $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">Citation</h4><div class="item-meta-value">' . $item->getCitation() . '</div></span>';

    return $escapedMetadata;
  }
  public function getVideoSourceInfo($videoFile)
  {
    $videoSource = new stdClass();
    $videoSource->videoUrl = file_display_url($videoFile, $format = "original");
    $videoSource->videoMimeType = $videoFile->mime_type;
    return $videoSource;
  }
  public function getAudioSourceInfo($audioFile)
  {
    $audioSource = new stdClass();
    $audioSource->audioUrl = file_display_url($audioFile, $format = "original");
    $audioSource->audioMimeType = $audioFile->mime_type;
    return $audioSource;
  }
}
