<?php
class Mydons_Customerorders_Block_Adminhtml_Customerorders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_customerorders';
    $this->_blockGroup = 'customerorders';
    $this->_headerText = Mage::helper('customerorders')->__('Inactive Customers (No Orders Placed Since Past Three months)');
    $this->_addButtonLabel = Mage::helper('customerorders')->__('Add Item');    
    parent::__construct();
    $this->_removeButton('add');
  }
}