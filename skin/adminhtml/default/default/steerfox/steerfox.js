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
document.addEventListener("DOMContentLoaded",
    function (e) {

        // Enable only on welcome page
        if(null != $('already_customer')){

            if($('steerfox_plugins_account').up('div.section-config') != undefined){
                $('steerfox_plugins_account').up('div.section-config').hide();
            }else{
                // Case for Older Magento
                $('steerfox_plugins_account').hide();
                $('steerfox_plugins_account-head').up('div.entry-edit-head').hide();
            }


            $('already_customer').observe('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                if($('steerfox_plugins_account').up('div.section-config') != undefined){
                    $('steerfox_plugins_account').up('div.section-config').show();
                }else{
                    // Case for Older Magento
                    $('steerfox_plugins_account').show();
                    $('steerfox_plugins_account-head').up('div.entry-edit-head').show();
                }

                $('already_customer').scrollTo();

                // Focus
                document.getElementById('steerfox_plugins_account_api_key').focus();
            });
        }
    }
);