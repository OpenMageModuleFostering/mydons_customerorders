<?php

class Mydons_Customerorders_Block_Adminhtml_Customerorders_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('customerordersGrid');
        $this->setDefaultSort('customerorders_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $todaysDate = date('Y-m-d H:i:s', strtotime(Mage::app()->getLocale()->date()));
        $fromDate = date('Y-m-d H:i:s', strtotime('-90 days', strtotime(Mage::app()->getLocale()->date())));

        $orderedCustomers = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect("customer_id")
                ->addAttributeToFilter("customer_id", array('neq' => NULL))
                ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $todaysDate));
        
        if($orderedCustomers->getSize()) {
        
        $orderedCustomerIds = array();

        foreach ($orderedCustomers as $orderedCustomer) {
            $orderedCustomerIds[$orderedCustomer->customer_id] = $orderedCustomer->customer_id;
        }

        $collection = Mage::getModel("customer/customer")->getCollection()
                ->addAttributeToFilter("entity_id", array('nin' => array($orderedCustomerIds)));
        $sql = 'SELECT MAX(o.created_at)'
                . ' FROM ' . Mage::getSingleton('core/resource')->getTableName('sales/order') . ' AS o'
                . ' WHERE o.customer_id = e.entity_id ';
        $expr = new Zend_Db_Expr('(' . $sql . ')');

        $collection->getSelect()->from(null, array('last_order_date' => $expr));
        }
        else {
            $collection = Mage::getModel("customer/customer")->getCollection()
                ->addAttributeToFilter("entity_id", array('nin' => array()));
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {


        $this->addColumn('entity_id', array(
            'header' => Mage::helper('customerorders')->__('Customer Id'),
            'align' => 'left',
            'index' => 'entity_id',
            'type' => 'number',
            'default' => '--'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('customerorders')->__('Customer Email'),
            'align' => 'left',
            'index' => 'email',
            'default' => '--'
        ));
        
         $this->addColumn('last_order_date', array(
          'header'    => Mage::helper('customerorders')->__('Last Purchased On'),
          'align'     =>'left',
          'index'     => 'last_order_date',
          'type'      => 'datetime',
          'default'   => '--',
          'filter' => false
          ));
 

        $this->addExportType('*/*/exportCsv', Mage::helper('customerorders')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customerorders')->__('XML'));

        return parent::_prepareColumns();
    }

   

}
