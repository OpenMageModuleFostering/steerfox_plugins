<?php

class Steerfox_Plugins_Model_Entity_Attribute_Backend_Profit extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterLoad($object)
    {
        $cost = $object->getCost() === null ? 0 : $object->getCost();

        $finalPrice = $object->getFinalPrice();
        $taxHelper = Mage::helper('tax');
        $price = $taxHelper->getPrice($object, $finalPrice, false);

        $profit = $price - $cost;
        $object->setProductProfit($profit);

        return $this;
    }

    public function beforeSave($object)
    {
        $cost = $object->getCost() === null ? 0 : $object->getCost();

        $finalPrice = $object->getFinalPrice();
        $taxHelper = Mage::helper('tax');
        $price = $taxHelper->getPrice($object, $finalPrice, false);

        $profit = $price - $cost;
        $object->setProductProfit($profit);

        return $this;
    }
}