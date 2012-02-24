<?php

/**
 * Copyright 2012 Realestate.co.nz Ltd
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

interface RealestateCoNz_Api_Http_Adapter
{
    
    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array());

    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     */
    public function connect($host, $port = 80);

    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param string        $url
     * @param array         $headers
     * @param string        $body
     * @return string Request as text
     */
    public function write($method, $url, $headers = array(), $body = '');

    /**
     * Read response from server
     *
     * @return RealestateCoNz_Api_Http_Response
     */
    public function read();

    /**
     * Close the connection to the server
     *
     */
    public function close();
    
    
}