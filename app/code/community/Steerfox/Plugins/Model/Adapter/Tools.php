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

require_once Mage::getModuleDir('', 'Steerfox_Plugins') . '/lib/SteerfoxContainer.php';

/**
 * Tool adapter.
 */
class Steerfox_Plugins_Model_Adapter_Tools implements SteerfoxToolsAdapterInterface
{
    /**
     * Encode as json.
     *
     * @param mixed $value Value to encode.
     *
     * @return string
     */
    public function jsonEncode($value)
    {
        return json_encode($value);
    }

    /**
     * Decode JSON string
     *
     * @param string $json  Json value to decode
     * @param bool   $assoc Return an assoc array
     *
     * @return mixed
     */
    public function jsonDecode($json, $assoc = false)
    {
        return json_decode($json, $assoc);
    }

    /**
     * Return sting length.
     *
     * @param string $string
     *
     * @return bool|int
     */
    public function strlen($string)
    {
        return strlen($string);
    }

    /**
     * Return part of string.
     *
     * @param string   $string Source string.
     * @param int      $start  Start position.
     * @param int|bool $length Length.
     *
     * @return mixed
     */
    public function substr($string, $start, $length = false)
    {
        return substr($string, $start, $length);
    }

    /**
     * Return content file
     *
     * @param string $url            URL.
     * @param bool   $useIncludePath Use php include path.
     * @param null   $streamContext  Stream context
     *
     * @return mixed
     */
    public function fileGetContents($url, $useIncludePath = false, $streamContext = null)
    {
        return file_get_contents($url, $useIncludePath, $streamContext);
    }

    /**
     * Save export file on disk
     *
     * @param $filename File Name
     * @param $fileContent File Content
     *
     * @return void
     */
    public function saveExportFile($filename, $fileContent)
    {
        $folderPath = Mage::getBaseDir('var') . DS . Mage::getStoreConfig('steerfox_plugins/export/folder_name');
        $file = new Varien_Io_File();

        //Si le dossier n'existe pas on le crée
        if(!is_dir($folderPath)) {
            $folderCreate = $file->mkdir($folderPath);
            if (!$folderCreate) {
                Mage::throwException('Can\'t create folder');
            }
        }

        $filePath = $folderPath . DS . $filename;

        //Si un ancien fichier existe on le supprime.
        if(file_exists($filePath)){
            unlink($filePath);
        }

        //On écrit le fichier sur disque.
        if(!$file->write($filePath,$fileContent)){
            Mage::throwException('Can\'t create file');
        }
    }


}