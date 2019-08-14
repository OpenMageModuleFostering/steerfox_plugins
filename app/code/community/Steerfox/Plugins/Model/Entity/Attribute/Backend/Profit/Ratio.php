<?php

class Steerfox_Plugins_Model_Entity_Attribute_Backend_Profit_Ratio
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterLoad($object)
    {
        $cost = $object->getCost() === null ? 0 : $object->getCost();

        $finalPrice = $object->getFinalPrice();
        $taxHelper = Mage::helper('tax');
        $price = $taxHelper->getPrice($object, $finalPrice, false);

        if (0 < $price) {
            $profit = $price - $cost;
            $ratio = ($profit / $price) * 100;
            $object->setProductProfitRatio($ratio);
        }

        return $this;
    }

    public function beforeSave($object)
    {
        $cost = $object->getCost() === null ? 0 : $object->getCost();

        $finalPrice = $object->getFinalPrice();
        $taxHelper = Mage::helper('tax');
        $price = $taxHelper->getPrice($object, $finalPrice, false);

        if (0 < $price) {
            $profit = $price - $cost;
            $ratio = ($profit / $price) * 100;
            $object->setProductProfitRatio($ratio);
        }

        return $this;
    }

}