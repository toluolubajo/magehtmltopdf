<?php

/*
 * To change this template, choose Tools | Templates
 * and open the 
 * 
 */

class EdmondsCommerce_Sales_Model_Order_Invoice_Item extends Mage_Sales_Model_Order_Invoice_Item {

    function getMixtureType() {
        $mixture_type = "";
        $product = $this->getItemProduct();
        if ($product) {
            $mixture_type = $product->getMixtureType();
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'mixture_type');
            if ($attribute->getFrontendInput() == 'select') {
                $mixture_type = $product->getAttributeText('mixture_type');
            }
        }
        return $mixture_type;
    }

    function getUnit() {
        $unit = "Kg";
        $_mixture_type = $this->getMixtureType();
        $mixture_type = str_replace(' ', '', strtolower($_mixture_type));
        if ($mixture_type == "amenityuse")
            $unit = "%";
        return $unit;
    }

    public function getTotalWeight() {
        $weight = 0;
        $totalWeight = 0;
        $product = $this->getItemProduct();
        $weight = $product->getWeight();
        $qty = $this->getQty();
        $totalWeight = $weight * $qty;
        return $totalWeight;
    }

    public function getItemProduct() {
        $productId = "";
        $product = NULL;
        if ($parentItem = $this->getParentItem()) {
            $productId = $parentItem->getProductId();
        } else {
            $productId = $this->getProductId();
        }
        if (is_numeric($productId))
            $product = Mage::getModel('catalog/product')->load($productId);
        return $product;
    }

    public function getAcreUnits() {
        $unit = "";
        $kgPerAcre = 1;
        $storeKgPerAcre = Mage::getStoreConfig('sales_pdf/admin/kg_per_acre');
        if ($storeKgPerAcre && is_numeric($storeKgPerAcre))
            $kgPerAcre = $storeKgPerAcre;
        $parentItemKgPerAcre = $this->getItemProduct()->getWeight();
        if ($parentItemKgPerAcre && is_numeric($parentItemKgPerAcre))
            $kgPerAcre = $parentItemKgPerAcre;
        $weightToPrint = Mage::app()->getRequest()->getParam('weight');
        if ($weightToPrint && is_numeric($weightToPrint)) {
            $mixture_type = $this->getMixtureType();
            if ($mixture_type != "amenityuse") {
                $val = round(($weightToPrint / $kgPerAcre), 2);
                $s = $this->addS($val);
                $unit = sprintf("(%.2f Acre%s)", $val, $s);
            }
        }
        return $unit;
    }

    protected function addS($val) {
        $s = '';
        if (is_numeric($val) && $val > 1)
            $s = 's';
        return $s;
    }

    function getWeight() {
        $weight = "";
        $product = $this->getItemProduct();
        $_weight=$product->getWeight();
        $weight = number_format($_weight,3);
        return $weight;
    }

}

?>
