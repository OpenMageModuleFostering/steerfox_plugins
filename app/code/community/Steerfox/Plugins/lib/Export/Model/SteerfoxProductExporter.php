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


/**
 * Export CMS data for Steerfox Api
 */
class SteerfoxProductExporter
{
    /**
     * Length limtation for description
     */
    const GOOGLE_SHOPPING_DESC_LIMIT = 5000;

    /**
     * Authorize export formats.
     */
    public static $AUTHORIZED_FORMATS = array('gz', 'txt');

    /**
     * Export file name.
     */
    const EXPORT_SAVE_FILENAME = 'feed_';

    /**
     * Export mimetype for download.
     *
     * @var string
     */
    private $mimeType;

    /**
     * File extension.
     *
     * @var  string.
     */
    private $extension;

    /**
     * File name.
     *
     * @var  string.
     */
    private $fileName;

    /**
     * Export content.
     *
     * @var array
     */
    private $content;

    /**
     * SteerfoxProductExporter constructor.
     *
     * @param       $shopId
     * @param array $products
     * @param       $format
     */
    public function __construct($shopId, array $products, $format)
    {
        $this->mimeType = null;
        $this->extension = null;
        $this->content = null;
        $this->render($shopId, $products, $format);
    }

    /**
     * @param       $shopId
     * @param array $products
     * @param       $format
     *
     * @throws Exception
     */
    protected function render($shopId, array $products, $format)
    {

        switch ($format) {
            case 'txt':
                $this->renderPlainText($shopId, $products);
                break;
            case 'gz':
                $this->renderGz($shopId, $products);
                break;
            default:
                throw new Exception('Unavailable export format');
                break;
        }
    }

    /**
     * Render Plaintext format.
     *
     * @param       $shopId
     * @param array $products
     * @param       $format
     */
    protected function renderPlainText($shopId, array $products)
    {
        $renderContent = '';
        $this->mimeType = 'text/plain';
        $this->extension = 'txt';
        $this->fileName = self::EXPORT_SAVE_FILENAME.$shopId.'.'.$this->extension;
        $contentArray = $this->generateContentArray($products);

        foreach ($contentArray as $contentRow) {
            $renderContent .= implode("\t", $contentRow)."\n";
        }

        $this->content = $renderContent;
    }

    /**
     * Render Gz format.
     *
     * @param       $shopId
     * @param array $products
     */
    protected function renderGz($shopId, array $products)
    {
        $this->renderPlainText($shopId, $products);
        $this->content = gzencode($this->content);
        $this->mimeType = 'application/x-gzip';
        $this->extension = 'txt.gz';
        $this->fileName = self::EXPORT_SAVE_FILENAME.$shopId.'.'.$this->extension;
    }

    /**
     * @param array $products
     *
     * @return array
     */
    protected function generateContentArray(array $products)
    {
        $contentArray = array();
        $contentArray[] = $this->addHeaderContentRow();
        foreach ($products as $product) {
            /** @var SteerfoxProductAdapterInterface $product */

            // Clean title and description
            $title = $product->getTitle();

            $description = $product->getDescription();

            $description = $this->cleanAndTruncateText($description);
            $contentArray[] = $this->getContentRow(
                $product->getId(),
                $title,
                $description,
                $product->getProductType(),
                $product->getLink(),
                $product->getImageLink(),
                $product->getCondition(),
                $product->getAvailability(),
                $product->getAvailabilityDate(),
                $product->getPrice(),
                $product->getSalePrice(),
                $product->getSalePriceEffectiveDate(),
                $product->getGtin(),
                $product->getBrand(),
                $product->getMpn(),
                $product->getIdentifierExists(),
                $product->getItemGroupId(),
                $product->getGender(),
                $product->getAgeGroup(),
                $product->getColor(),
                $product->getSize(),
                $product->getShipping(),
                $product->getShippingWeight(),
                $product->getGrossProfit(),
                $product->getGrossMargin()
            );
        }

        return $contentArray;
    }

    /**
     * Clean and truncate text for google shopping integration.
     *
     * @param $text
     *
     * @return string
     */
    private function cleanAndTruncateText($text)
    {
        $cleanedText = html_entity_decode(strip_tags($text));

        $tool = SteerfoxContainer::getInstance()->get('tools');
        //Limit description to 5000 char for Google Shopping compliant.
        if($tool->strlen($cleanedText) >= self::GOOGLE_SHOPPING_DESC_LIMIT){
            $cleanedText = $tool->substr($cleanedText, 0, (self::GOOGLE_SHOPPING_DESC_LIMIT - 4));
            $cleanedText .= ' ...';
        }

        $cleanedText = str_replace("\"", "\"\"", $cleanedText);

        return '"'.$cleanedText.'"';
    }

    /**
     * Add header for data export.
     */
    private function addHeaderContentRow()
    {
        return $this->getContentRow(
            'id',
            'title',
            'description',
            'product type',
            'link',
            'image link',
            'condition',
            'availability',
            'availability date',
            'price',
            'sale price',
            'sale price effective date',
            'gtin',
            'brand',
            'mpn',
            'identifier exists',
            'item group id',
            'gender',
            'age group',
            'color',
            'size',
            'shipping',
            'shipping weight',
            'c:gross_profit',
            'c:gross_margin'
        );
    }

    /**
     * Add line to content.
     *
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     * @param mixed $productType
     * @param mixed $link
     * @param mixed $imageLink
     * @param null  $condition
     * @param mixed $availability
     * @param mixed $availabilityDate
     * @param mixed $price
     * @param mixed $salePrice
     * @param mixed $salePriceEffectiveDate
     * @param mixed $gtin
     * @param mixed $brand
     * @param mixed $mpn
     * @param mixed $identifierExists
     * @param mixed $itemGroupId
     * @param mixed $gender
     * @param mixed $ageGroup
     * @param mixed $color
     * @param mixed $size
     * @param mixed $shipping
     * @param null  $shippingWeight
     * @param mixed $grossProfit
     * @param mixed $grossMargin
     *
     * @return array
     */
    private function getContentRow(
        $id = null,
        $title = null,
        $description = null,
        $productType = null,
        $link = null,
        $imageLink = null,
        $condition = null,
        $availability = null,
        $availabilityDate = null,
        $price = null,
        $salePrice = null,
        $salePriceEffectiveDate = null,
        $gtin = null,
        $brand = null,
        $mpn = null,
        $identifierExists = null,
        $itemGroupId = null,
        $gender = null,
        $ageGroup = null,
        $color = null,
        $size = null,
        $shipping = null,
        $shippingWeight = null,
        $grossProfit = null,
        $grossMargin = null
    ) {
        return array(
            $id,
            $title,
            $description,
            $productType,
            $link,
            $imageLink,
            $condition,
            $availability,
            $availabilityDate,
            $price,
            $salePrice,
            $salePriceEffectiveDate,
            $gtin,
            $brand,
            $mpn,
            $identifierExists,
            $itemGroupId,
            $gender,
            $ageGroup,
            $color,
            $size,
            $shipping,
            $shippingWeight,
            $grossProfit,
            $grossMargin,
        );
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }
}
