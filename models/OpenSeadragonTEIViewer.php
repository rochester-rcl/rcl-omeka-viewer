<?php

class OpenSeadragonTEIViewer extends Omeka_Record_AbstractRecord
{
  public $xsl_url;
  public $viewer_name;
  public $item_type_id;
  public $xsl_viewer_option;
  public $override_items_show_option;
  public $transcriptions_field_id;

  public function getRecordUrl($action = 'show')
    {
        if ('show' == $action) {
            return public_url($this->slug);
        }
        return array('module' => 'open-seadragon-tei', 'controller' => 'index',
                     'action' => $action, 'id' => $this->id);
    }
}
 ?>
