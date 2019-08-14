<?php
/**
 *  Copyright 2015 SteerFox SAS.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License"); you may
 *  not use this file except in compliance with the License. You may obtain
 *  a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 *  WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 *  License for the specific language governing permissions and limitations
 *  under the License.
 *
 * @author    SteerFox <tech@steerfox.com>
 * @copyright 2015 SteerFox SAS
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

require_once Mage::getModuleDir('', 'Steerfox_Plugins').'/lib/SteerfoxContainer.php';

class Steerfox_Plugins_Model_Adapter_Product implements SteerfoxProductAdapterInterface
{
    /**
     * Store Magento.
     *
     * @var Steerfox_Plugins_Model_Product
     */
    private $steerfoxProduct;

    /**
     * Store Magento.
     *
     * @var Mage_Catalog_Model_Product
     */
    private $product;

    public function __construct(Steerfox_Plugins_Model_Product $steerfoxProduct)
    {
        $this->$steerfoxProduct = $steerfoxProduct;
        $this->product = Mage::getModel('catalog/product')->load($steerfoxProduct->getIdProduct());
    }

    /**
     * Return id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->product->getId();
    }

    /**
     * Return Title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->product->getName();
    }

    /**
     * Return Description
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->product->getDescription();
    }

    /**
     * Return ProductType
     *
     * @return mixed
     */
    public function getProductType()
    {
        $type = null;
        if (null != $this->product->getApparelType()) {
            //Cas pour le jeu produit des vêtements
            $type = $this->product->getResource()
                ->getAttribute('apparel_type')
                ->getSource()
                ->getOptionText($this->product->getApparelType());
        } else {
            if (null != $this->product->getAttributeSetId()) {
                // Cas par défaut
                $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
                $attributeSetModel->load($this->product->getAttributeSetId());
                $type = $attributeSetModel->getAttributeSetName();
            }
        }

        return $type;
    }

    /**
     * Return Link
     *
     * @return mixed
     */
    public function getLink()
    {
        return $this->product->getProductUrl();
    }

    /**
     * Return Image Link
     *
     * @return mixed
     */
    public function getImageLink()
    {
        return $this->product->getImageUrl();
    }

    /**
     * Return Condition
     *
     * @return mixed
     */
    public function getCondition()
    {
        $currentDate = Mage::getModel('core/date')->date('Y-m-d');
        $fromDate = substr($this->product->getNewsFromDate(), 0, 10);
        $toDate = substr($this->product->getNewsToDate(), 0, 10);

        $isNew = ($currentDate >= $fromDate && $currentDate <= $toDate) || ($fromDate == '' && $currentDate <= $toDate && $toDate != '') || ($fromDate != '' && $currentDate >= $fromDate && $toDate == '');

        return ($isNew) ? 'new' : null;
    }

    /**
     * Return Availability
     *
     * @return mixed
     */
    public function getAvailability()
    {
        $availability = 'out of stock';
        $productStock = $this->product->getStockItem();
        if (null != $productStock) {
            if ($productStock->getIsInStock()) {
                $availability = 'in stock';
            }
        }

        return $availability;
    }

    /**
     * Return Availability Date
     *
     * @return mixed
     */
    public function getAvailabilityDate()
    {
        return null;
    }

    /**
     * Return Price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->product->getPrice();
    }

    /**
     * Return Sale Price
     *
     * @return mixed
     */
    public function getSalePrice()
    {
        return $this->product->getFinalPrice();
    }

    /**
     * Return Sale Price Effective Date
     *
     * @return mixed
     */
    public function getSalePriceEffectiveDate()
    {
        $salesPriceAvailabilityDates = null;

        // si le produit à un prix et des dates de promotion
        if (null != $this->product->getSpecialPrice() && null != $this->product->getSpecialFromDate(
            ) && null != $this->product->getSpecialToDate()
        ) {
            $fromDate = new DateTime($this->product->getSpecialFromDate());
            $toDate = new DateTime($this->product->getSpecialToDate());
            $salesPriceAvailabilityDates = $fromDate->format(DateTime::ISO8601).'/'.$toDate->format(DateTime::ISO8601);
        }

        return $salesPriceAvailabilityDates;
    }

    /**
     * Return Gtin
     *
     * @return mixed
     */
    public function getGtin()
    {
        return $this->product->getSku();
    }

    /**
     * Return Brand
     *
     * @return mixed
     */
    public function getBrand()
    {
        $manufacturer = null;
        if (null != $this->product->getManufacturer()) {
            $manufacturer = $this->product->getResource()
                ->getAttribute('manufacturer')
                ->getSource()
                ->getOptionText($this->product->getManufacturer());
        }

        return $manufacturer;
    }

    /**
     * Return Mpn
     *
     * @return mixed
     */
    public function getMpn()
    {
        // Not available in Magento
        return null;
    }

    /**
     * Return Identifier Exists
     *
     * @return mixed
     */
    public function getIdentifierExists()
    {
        // Not available in Magento
        return null;
    }

    /**
     * Return Item Group Id
     *
     * @return mixed
     */
    public function getItemGroupId()
    {
        // Not available in Magento
        return null;
    }

    /**
     * Return Gender
     *
     * @return mixed
     */
    public function getGender()
    {
        $gender = null;
        if (null != $this->product->getGender()) {
            $gender = $this->product->getResource()
                ->getAttribute('gender')
                ->getSource()
                ->getOptionText($this->product->getGender());
        }

        return $gender;
    }

    /**
     * Return Age Group
     *
     * @return mixed
     */
    public function getAgeGroup()
    {
        // Not available in Magento
        return null;
    }

    /**
     * Return Color
     *
     * @return mixed
     */
    public function getColor()
    {
        $color = null;
        if (null != $this->product->getColor()) {
            $color = $this->product->getResource()
                ->getAttribute('color')
                ->getSource()
                ->getOptionText($this->product->getColor());
        }

        return $color;
    }

    /**
     * Return Size
     *
     * @return mixed
     */
    public function getSize()
    {
        $size = null;
        if (null != $this->product->getSize()) {
            $size = $this->product->getResource()
                ->getAttribute('size')
                ->getSource()
                ->getOptionText($this->product->getSize());
        }

        return $size;
    }

    /**
     * Return Shipping
     *
     * @return mixed
     */
    public function getShipping()
    {
        // Not implement because it's heavy treatment on Magento for each product.
        return null;
    }

    /**
     * Return Shipping Weight
     *
     * @return mixed
     */
    public function getShippingWeight()
    {
        return $this->product->getWeight();
    }

    /**
     * Return GrossProfit
     *
     * @return mixed
     */
    public function getGrossProfit()
    {
        return $this->product->getProductProfit();
    }

    /**
     * Return Gross Margin
     *
     * @return mixed
     */
    public function getGrossMargin()
    {
        $margin = null;
        if (null != $this->product->getProductCost()) {
            $margin = $this->product->getFinalPrice() - $this->product->getProductCost();
        }

        return $margin;
    }


}
