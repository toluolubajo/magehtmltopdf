<?php

class EdmondsCommerce_Sales_Block_Order_Htmlpdf_Items_Mixturelabel extends Mage_Core_Block_Template {

    public function getProductOptions() {
        $item = $this->getItem();
        $productOptions = json_decode($item->getProductOptions());
    }

    public function getParentItem() {
        $parentItem = null;
        $item = $this->getItem();
        $productId = $item->getProductId();
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getTypeId() == 'simple' && $item->getParentItem()) {
            $parentItem = $item->getOrderItem()->getParentItem();
        }

        return $parentItem;
    }

    public function getParentItemProduct() {
        $parent_product = null;
        $parentItem = $this->getParentItem();
        if ($parentItem) {
            $productId = $parentItem->getProductId();
            $parent_product = Mage::getModel('catalog/product')->load($productId);
        }
        return $parent_product;
    }

    public function getMixtureType() {
        $mixture_type = "";
        $parentProduct = $this->getParentItemProduct();
        if ($parentProduct) {
            $_mixture_type = $parentProduct->getMixtureType();
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'mixture_type');
            if ($attribute->getFrontendInput() == 'select') {
                $_mixture_type = $parentProduct->getAttributeText('mixture_type');
                if ($_mixture_type)
                    $mixture_type = str_replace(' ', '', strtolower($_mixture_type));
            }
        }

        return $mixture_type;
    }

    public function getItemWeight() {
        $weight = '';
        $productOptions = $this->getItemProductOptions();
        $weightToPrint = $this->getRequest()->getParam('weight');
        if ($weightToPrint && is_numeric($weightToPrint)) {
            $mixture_type = $this->getMixtureType();
            if ($mixture_type == "amenityuse") {
                if ($parentProduct = $this->getParentItemProduct()) {
                    $parentWeight = $parentProduct->getWeight();
                    if($parentWeight<$productOptions['qty'])$parentWeight=1;
                    $weight = (round(($productOptions['qty'] / $parentWeight ) * 100)); ///the fixed quantity for the simple products that constitutes the bundle products are regarded as the weight
                }
            } else {
                if ($parentProduct = $this->getParentItemProduct()) {
                    $parentWeight = $parentProduct->getWeight();
                    $mutiples=intval($weightToPrint/$parentWeight);
                    if($mutiples<=0)$mutiples=1;
                    $weight = round(($productOptions['qty'] * $mutiples)); ///the fixed quantity for the simple products that constitutes the bundle products are regarded as the weight
                }
            }
        }
        return $weight;
    }

    public function getItemProductOptions() {
        $productOptions = array();
        $item = $this->getItem();
        $item_options = $item->getOrderItem()->getProductOptions();
        $bds = $item_options['bundle_selection_attributes'];
        $productOptions = unserialize($bds);
        return $productOptions;
    }

    public function getItemBundleOptionTitle() {
        $title = "";
        $productOptions = $this->getItemProductOptions();
        $title = $productOptions['option_label'];
        return $title;
    }

}

