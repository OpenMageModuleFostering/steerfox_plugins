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
 * Tools adapter interface
 */
interface SteerfoxToolsAdapterInterface
{
    /**
     * Encode as json.
     *
     * @param mixed $value Value to encode.
     *
     * @return string
     */
    public function jsonEncode($value);

    /**
     * Decode JSON string
     *
     * @param string $json  Json value to décode
     * @param bool   $assoc Return an assoc array
     *
     * @return mixed
     */
    public function jsonDecode($json, $assoc = false);

    /**
     * Return string length.
     *
     * @param string $string
     *
     * @return int
     */
    public function strlen($string);

    /**
     * Return part of string.
     *
     * @param string   $string Source string.
     * @param int      $start  Start position.
     * @param int|bool $length Length.
     *
     * @return mixed
     */
    public function substr($string, $start, $length = false);

    /**
     * Return content file
     *
     * @param string $url            URL.
     * @param bool   $useIncludePath Use php include path.
     * @param null   $streamContext  Stream context
     *
     * @return mixed
     */
    public function fileGetContents($url, $useIncludePath = false, $streamContext = null);

    /**
     * Save export file on disk
     *
     * @param string $filename    File Name
     * @param string $fileContent File Content
     *
     * @return void
     */
    public function saveExportFile($filename, $fileContent);
}
