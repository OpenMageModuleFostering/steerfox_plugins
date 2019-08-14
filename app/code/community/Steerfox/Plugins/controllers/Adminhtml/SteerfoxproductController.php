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
 * Class Steerfox_Plugins_Adminhtml_SteerfoxproductController
 */
class Steerfox_Plugins_Adminhtml_SteerfoxproductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Display grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Steerfox'))
            ->_title($this->__('Manage exported products'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->_initAction();

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Publish in mass.
     */
    public function massPublishAction()
    {
        $entityIds = $this->getRequest()->getParam('entity_id');
        $logs = array();


        if (!is_array($entityIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('steerfox_plugins')->__('Please select product(s)')
            );
            $logs[] = Mage::getModel('steerfox_plugins/log')->setData(
                array(
                    'action' => 'steerfoxproduct::publish',
                    'type' => Steerfox_Plugins_Model_Log::STEERFOX_LOG_TYPE_WARNING,
                    'message' => json_encode(
                        array(
                            'entityIds' => json_encode($entityIds),
                        )
                    ),
                )
            );
        } else {
            try {
                $productModel = Mage::getModel('steerfox_plugins/product');
                $published = 0;
                foreach ($entityIds as $entityId) {
                    $product = $productModel->load($entityId);

                    if (!$product->getData('active')) {
                        $published++;
                        $productModel->load($entityId)->setData('active', 1)->save();
                        $store = Mage::getModel('core/store')->load($product->getData('id_shop'));
                        $logs[] = Mage::getModel('steerfox_plugins/log')
                            ->setData(
                                array(
                                    'action' => 'steerfoxproduct::publish',
                                    'type' => Steerfox_Plugins_Model_Log::STEERFOX_LOG_TYPE_INFO,
                                    'shop' => $store->getWebsiteId() . '-' . $store->getGroup()->getName(),
                                    'message' => json_encode(
                                        array(
                                            'entity_id' => $product->getData('entity_id'),
                                            'id_product' => $product->getData('id_product'),
                                            'id_lang' => $product->getData('id_lang'),
                                            'id_shop' => $product->getData('id_shop'),
                                        )
                                    ),
                                )
                            );
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('steerfox_plugins')->__('%d product(s) published', $published)
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('steerfox_plugins')->__($e->getMessage()));
                $logs[] = Mage::getModel('steerfox_plugins/log')->setData(
                    array(
                        'action' => 'steerfoxproduct::publish',
                        'type' => Steerfox_Plugins_Model_Log::STEERFOX_LOG_TYPE_ERROR,
                        'message' => json_encode(
                            array(
                                'exception' => $e->getMessage(),
                            )
                        ),
                    )
                );
            }
        }

        foreach ($logs as $log) {
            $log->save();
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Publish in mass.
     */
    public function massUnpublishAction()
    {
        $entityIds = $this->getRequest()->getParam('entity_id');
        $logs = array();
        if (!is_array($entityIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('steerfox_plugins')->__('Please select product(s)')
            );
            $logs[] = Mage::getModel('steerfox_plugins/log')->setData(
                array(
                    'action' => 'steerfoxproduct::unpublish',
                    'type' => Steerfox_Plugins_Model_Log::STEERFOX_LOG_TYPE_WARNING,
                    'message' => json_encode(
                        array(
                            'entityIds' => json_encode($entityIds),
                        )
                    ),
                )
            );
        } else {
            try {
                $productModel = Mage::getModel('steerfox_plugins/product');
                $published = 0;
                foreach ($entityIds as $entityId) {
                    $product = $productModel->load($entityId);
                    if ($product->getData('active')) {
                        $published++;
                        $productModel->load($entityId)->setData('active', 0)->save();
                        $store = Mage::getModel('core/store')->load($product->getData('id_shop'));
                        $logs[] = Mage::getModel('steerfox_plugins/log')
                            ->setData(
                                array(
                                    'action' => 'steerfoxproduct::publish',
                                    'type' => Steerfox_Plugins_Model_Log::STEERFOX_LOG_TYPE_INFO,
                                    'shop' => $store->getWebsiteId() . '-' . $store->getGroup()->getName(),
                                    'message' => json_encode(
                                        array(
                                            'entity_id' => $product->getData('entity_id'),
                                            'id_product' => $product->getData('id_product'),
                                            'id_lang' => $product->getData('id_lang'),
                                            'id_shop' => $product->getData('id_shop'),
                                        )
                                    ),
                                )
                            )->setOrigData();
                    }

                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('steerfox_plugins')->__('%d product(s) unpublished', $published)
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $logs[] = Mage::getModel('steerfox_plugins/log')->setData(
                    array(
                        'action' => 'steerfoxproduct::publish',
                        'type' => Steerfox_Plugins_Model_Log::STEERFOX_LOG_TYPE_ERROR,
                        'message' => json_encode(
                            array(
                                'exception' => $e->getMessage(),
                            )
                        ),
                    )
                );
            }
        }

        foreach ($logs as $log) {
            $log->save();
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Init module.
     *
     * @return Steerfox_Plugins_Adminhtml_ProductController
     */
    protected function _initAction()
    {
        // Add the version notity if necessary
        Mage::helper('steerfox_plugins')->notifyOldRelease();
        $this->loadLayout()
            ->_setActiveMenu('steerfox_product/manage')
            ->_addBreadcrumb(
                Mage::helper('steerfox_plugins')->__('Products'),
                Mage::helper('steerfox_plugins')->__('Products')
            )
            ->_addBreadcrumb(
                Mage::helper('steerfox_plugins')->__('Manage exported products'),
                Mage::helper('steerfox_plugins')->__('Manage exported products')
            );

        return $this;
    }

    /**
     * ACL
     * @return mixed
     */
    protected function _isAllowed()
    {
        $actionName = $this->getRequest()->getActionName();
        switch ($actionName) {
            case 'index':
            case 'edit':
            case 'delete':
            default:
                $adminSession = Mage::getSingleton('admin/session');
                $isAllowed = $adminSession->isAllowed('steerfox_product/manage');
                break;
        }

        return $isAllowed;
    }
}
