<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order shipment model
 *
 * @method Mage_Sales_Model_Resource_Order_Shipment _getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment getResource()
 * @method int getStoreId()
 * @method Mage_Sales_Model_Order_Shipment setStoreId(int $value)
 * @method float getTotalWeight()
 * @method Mage_Sales_Model_Order_Shipment setTotalWeight(float $value)
 * @method float getTotalQty()
 * @method Mage_Sales_Model_Order_Shipment setTotalQty(float $value)
 * @method int getEmailSent()
 * @method Mage_Sales_Model_Order_Shipment setEmailSent(int $value)
 * @method int getOrderId()
 * @method Mage_Sales_Model_Order_Shipment setOrderId(int $value)
 * @method int getCustomerId()
 * @method Mage_Sales_Model_Order_Shipment setCustomerId(int $value)
 * @method int getShippingAddressId()
 * @method Mage_Sales_Model_Order_Shipment setShippingAddressId(int $value)
 * @method int getBillingAddressId()
 * @method Mage_Sales_Model_Order_Shipment setBillingAddressId(int $value)
 * @method int getShipmentStatus()
 * @method Mage_Sales_Model_Order_Shipment setShipmentStatus(int $value)
 * @method string getIncrementId()
 * @method Mage_Sales_Model_Order_Shipment setIncrementId(string $value)
 * @method string getCreatedAt()
 * @method Mage_Sales_Model_Order_Shipment setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_Sales_Model_Order_Shipment setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class EdmondsCommerce_Sales_Model_Order_Shipment extends Mage_Sales_Model_Order_Shipment {

    public function getHtmlForPdf($officeCopy=false) {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        $templateId = 'sales_htmlpdf_shipment_template';
        // Retrieve specified view block from appropriate design package (depends on emulated store)
        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($storeId);
        $paymentBlockHtml = $paymentBlock->toHtml();
        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }

        $htmlgetter = Mage::getModel('core/htmlpdf_template_htmlpdfget');
        $htmlgetter->setStoreId($storeId);
        $htmlgetter->setTemplateId($templateId);
        $htmlgetter->setTemplateParams(array(
            'order' => $order,
            'shipment' => $this,
            'billing' => $order->getBillingAddress(),
            'payment_html' => $paymentBlockHtml
                )
        );
        $html = $htmlgetter->getHtmlForPdf();

        return $html;
    }

    public function arrangeShipmentItems() {
        $arrangedItems = array();
        $items = $this->getAllItems();
        foreach ($items as $item) {
            if ($item->getParentId() == $item->getEntityId()) {
                $arrangedItems[$item->getParentId()][] = $item;
            } else {
                $arrangedItems[$item->getParentId()][] = $item;
            }
        }
        return $arrangedItems;
    }
    
    public function getItemOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }
    public function getTrackingDetailsForPdf(){
        $trackingDetails="";
        foreach ($this->getAllTracks() as $_item){
           $trackingDetails.="{$_item->getTitle()} :: {$_item->getNumber()}<br/>";
        }
        return $trackingDetails;
    }

}
