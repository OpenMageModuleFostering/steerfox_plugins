<?php
/**
 * Copyright 2015 Steerfox SAS.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * @author    Steerfox <tech@steerfox.com>
 * @copyright 2015 Steerfox SAS
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Class Steerfox_Plugins_Model_Currencies
 */
class Steerfox_Plugins_Model_Currencies
{
    public function toOptionArray()
    {
        $currencies = array();
        $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
        if (is_array($codes) && count($codes) >= 1) {
            $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                Mage::app()->getStore()->getBaseCurrency(),
                $codes
            );

            $i = 0;
            foreach ($codes as $code) {
                if (isset($rates[$code])) {
                    $currencies[$i]['value'] = $code;
                    $currencies[$i]['label'] = Mage::app()->getLocale()->getTranslation($code, 'nametocurrency');
                }
                $i++;
            }
        }

        return $currencies;
    }
}