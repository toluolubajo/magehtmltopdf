<?php

class EdmondsCommerce_Sales_Model_Order_Invoice extends Mage_Sales_Model_Order_Invoice {

    protected $itemWeightForMixtureLabel;

    public function getHtmlForPdf($officeCopy = false) {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if ($officeCopy == true) {
            $templateId = 'sales_htmlpdf_invoice_office_template';
        } else {
            $templateId = 'sales_htmlpdf_invoice_template';
        }
        // Retrieve specified view block from appropriate design package (depends on emulated store)
        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($storeId);
        $paymentBlockHtml = $paymentBlock->toHtml();
        $htmlgetter = Mage::getModel('core/htmlpdf_template_htmlpdfget');
        $htmlgetter->setStoreId($storeId);
        $htmlgetter->setTemplateId($templateId);
        $htmlgetter->setTemplateParams(array(
            'order' => $order,
            'invoice' => $this,
            'billing' => $order->getBillingAddress(),
            'payment_html' => $paymentBlockHtml
                )
        );
        $html = $htmlgetter->getHtmlForPdf();

        return $html;
    }

    function getPaymentMethodName() {
        $order = $this->getOrder();
        $paymentName = "";
        $payment = $order->getPayment();
        $paymentName = $payment->getMethodInstance()->getTitle();
        return $paymentName;
    }

    function getBillingTelephone() {
        $tel = "";
        $address = $this->getBillingAddress();
        $tel = $address->getTelephone();
        return $tel;
    }

    function getBillingFax() {
        $fax = "";
        $address = $this->getBillingAddress();
        $fax = $address->getFax();
        return $fax;
    }

    function getShippingTelephone() {
        $tel = "";
        $address = $this->getShippingAddress();
        $tel = $address->getTelephone();
        return $tel;
    }

    function getShippingFax() {
        $fax = "";
        $address = $this->getShippingAddress();
        $fax = $address->getFax();
        return $fax;
    }

    function getMixturelabelHtml($itemId, $weight) {
        $order = $this->getOrder();
        $this->setItemWeightForMixturelabel($weight);
        $storeId = $order->getStore()->getId();
        $item = $this->getItemById($itemId);
        $templateId = 'sales_htmlpdf_invoice_item_mixturelabel';
        $htmlgetter = Mage::getModel('core/htmlpdf_template_htmlpdfget');
        $htmlgetter->setStoreId($storeId);
        $htmlgetter->setTemplateId($templateId);
        $htmlgetter->setTemplateParams(array(
            'order' => $order,
            'invoice' => $this,
            'item' => $item
                )
        );
        $html = $htmlgetter->getHtmlForPdf();

        return $html;
    }

    function getMixturelabelId() {
        $id="";
        $feraRefNo=Mage::getStoreConfig('general/store_information/fera_reg_num');
        $id = $this->getIncrementId() .'-'.$feraRefNo;
        return $id;
    }

    function getMixturelabelDate() {
        $today = date("n/Y");
        return $today;
    }
   

}

