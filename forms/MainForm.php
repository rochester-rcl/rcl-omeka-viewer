<?php

class OpenSeadragonTEI_Form_Main extends Omeka_Form_Admin
{
  public $xslFileDir;
  protected $_xslUrl;
  private $_requiredMimeTypes = array('application/xml');
  private $_requiredExtensions = array('xsl');
  protected $_record = 'OpenSeadragonTEIViewer';
  protected $_type = 'OpenSeadragonTEIViewer';


  public function init()
  {
    parent::init();
    $this->_addNameElement();
    $this->_addCheckboxElement();
    $this->_addFileElement();
    $this->_addWidthElement();
    $this->_addHeightElement();
    $this->_addItemTypeDropdown();
    $this->_addSubmit();
    $this->_addSave();
    $this->applyOmekaStyles();
    $this->setAutoApplyOmekaStyles(false);

  }

  protected function _addFileElement()
  {
    $fileValidators = array();
    //$mimeValidator = new Omeka_Validate_File_MimeType('application/xml');
    $extensionValidator = new Omeka_Validate_File_Extension('xsl');
    $fileValidators[] = $extensionValidator;

    $this->addElement('file', 'xsl_file', array(
        'label' => __('Upload XSLT Transformation'),
        'required' => false,
        'validators' => $fileValidators,
        'description' => 'Upload a file to transform associated TEI files.',
        'destination' => TRANSFORMATION_DIRECTORY_SYSTEM,
    ));

  }

  protected function _addCheckboxElement(){
    $this->addElement('checkbox', 'xsl_viewer_option', array(
      'label' => __('Use an XSLT File for Rendering'),
      'description' => 'Check this box to add a rendering component for attached XML files.',
      'checkedValue' => 'true',
      'uncheckedValue' => 'false',
    ));
  }

  protected function _addNameElement(){
    $this->addElement('text', 'viewer_name', array(
      'label' => 'Viewer Name',
      'description' => 'Viewer name',
    ));
  }

  protected function _addWidthElement()
  {
    $this->addElement('text', 'viewer_width', array(
      'label' => 'Viewer Width',
      'description' => 'Viewer width in pixels',
      'validators' => array(
        array('validator' => 'Int',
              'breakChainOnFailure' => true,
              'options' => array('messages' => array(
                  Zend_Validate_Int::NOT_INT => __('Width value must be an integer.')
              )),
            ),
      ),
    ));
  }

  protected function _addHeightElement()
  {
    $this->addElement('text', 'viewer_height', array(
      'label' => 'Viewer Height',
      'description' => 'Viewer height in pixels',
      'validators' => array(
        array('validator' => 'Int',
              'breakChainOnFailure' => true,
              'options' => array('messages' => array(
                  Zend_Validate_Int::NOT_INT => __('Height value must be an integer.')
              )),
            ),
      ),
    ));
    }
   protected function _addItemTypeDropdown()
   {
     $db = get_db();
     $itemTypeTable = $db->ItemType;
     $sql = $db->query("SELECT DISTINCT id, name FROM `{$db->prefix}item_types`");
     $results = $sql->fetchAll();
     $itemTypes = array();
     foreach($results as $result){
       $itemTypes[$result['id']] = $result['name'];
     }

     // Add ability to select item type
     $this->addElement('select', 'item_type', array(
       'label' => 'Select Item Type to Apply the Viewer to.',
       'multiOptions' => $itemTypes
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
