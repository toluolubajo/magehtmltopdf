<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EdmondsCommerce_Sales_Block_Order_Htmlpdf_Mixturelabels extends Mage_Sales_Block_Items_Abstract {

    protected function _prepareItem(Mage_Core_Block_Abstract $renderer) {
        $renderer->getItem()->setOrder($this->getOrder());
        $renderer->getItem()->setSource($this->getInvoice());
        $renderer->getItem()->setParentItem($this->getItem());
    }

    public function getChilds() {
        $item = $this->getItem();
        $_itemsArray = array();
        if ($item instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $_items = $item->getInvoice()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Shipment_Item) {
            $_items = $item->getShipment()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Creditmemo_Item) {
            $_items = $item->getCreditmemo()->getAllItems();
        }

        if ($_items) {
            foreach ($_items as $_item) {
                $parentItem = $_item->getOrderItem()->getParentItem();
                if (($parentItem && ($parentItem->getItemId() == $item->getOrderItemId()))) {
                    $_itemsArray[$parentItem->getItemId()][$_item->getOrderItemId()] = $_item;
                }
            }
        }
        if (isset($_itemsArray[$item->getOrderItemId()])) {
            $itemChildren = $_itemsArray[$item->getOrderItemId()];
            uasort($itemChildren, array($this,'sortChildItems'));
            return $itemChildren;
        } else {
            return null;
        }
    }

    public function sortChildItems($itemA, $itemB) {
        if ($itemA->getQty() == $itemB->getQty()) {
            return 0;
        }
        return ($itemA->getQty() > $itemB->getQty()) ? -1 : 1;
    }

}

?>
