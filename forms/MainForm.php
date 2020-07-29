<?php

class OpenSeadragonTEI_Form_Main extends Omeka_Form_Admin
{
  public $xslFileDir;
  protected $_xslUrl;
  private $_requiredMimeTypes = array('application/xml');
  private $_requiredExtensions = array('sef');
  protected $_record = 'OpenSeadragonTEIViewer';
  protected $_type = 'OpenSeadragonTEIViewer';


  public function init()
  {
    parent::init();
    $this->_addNameElement();
    $this->_overrideItemsShowElement();
    $this->_addCheckboxElement();
    $this->_addFileElement();
    $this->_addItemTypeDropdown();
    $this->_addTranscriptionFieldDropdown();
    $this->_addSubmit();
    $this->_addSave();
    $this->applyOmekaStyles();
    $this->setAutoApplyOmekaStyles(false);
  }

  protected function _addFileElement()
  {
    $fileValidators = array();
    //$mimeValidator = new Omeka_Validate_File_MimeType('application/xml');
    $extensionValidator = new Omeka_Validate_File_Extension('sef');
    $fileValidators[] = $extensionValidator;

    $this->addElement('file', 'xsl_file', array(
      'label' => __('Upload SEF Transformation'),
      'required' => false,
      'validators' => $fileValidators,
      'description' => 'Upload a file to transform associated TEI files.',
      'destination' => TRANSFORMATION_DIRECTORY_SYSTEM,
    ));
  }
  protected function _overrideItemsShowElement()
  {
    $this->addElement('checkbox', 'override_items_show_option', array(
      'label' => __('Override Items Show Template'),
      'description' => 'Check this box to override the current theme\'s items show template.
                        Leaving it unchecked will create views at mysite/viewer/itemtype/itemid.',
      'checked_value' => 1,
      'unchecked_value' => 0,
    ));
  }
  protected function _addCheckboxElement()
  {
    $this->addElement('checkbox', 'xsl_viewer_option', array(
      'label' => __('Use an SEF File for Rendering (Optional)'),
      'description' => 'Check this box to add a rendering component for attached XML files.',
      'checked_value' => 1,
      'unchecked_value' => 0,
    ));
  }

  protected function _addNameElement()
  {
    $this->addElement('text', 'viewer_name', array(
      'label' => 'Viewer Name',
      'description' => 'Viewer name',
    ));
  }

  protected function _addItemTypeDropdown()
  {
    $db = get_db();
    $itemTypeTable = $db->ItemType;
    $sql = $db->query("SELECT DISTINCT id, name FROM `{$db->prefix}item_types`");
    $results = $sql->fetchAll();
    $itemTypes = array();
    foreach ($results as $result) {
      $itemTypes[$result['id']] = $result['name'];
    }

    // Add ability to select item type
    $this->addElement('select', 'item_type', array(
      'label' => 'Select Item Type to Apply the Viewer to.',
      'multiOptions' => $itemTypes,
    ));
  }

  protected function _addTranscriptionFieldDropdown()
  {
    $fields = open_seadragon_tei_get_all_fieldnames();
    $this->addElement('select', 'transcriptions_field_id', array(
      'label' => 'Transcriptions Field (Optional)',
      'multiOptions' => $fields,
    ));
  }

  protected function _addSubmit()
  {
    $this->addElement('submit', 'save', array('label' => 'Save'));
  }

  protected function _addSave()
  {
    $this->setAction(url('open-seadragon-tei/index/save'))
      ->setMethod('post');
  }
}
