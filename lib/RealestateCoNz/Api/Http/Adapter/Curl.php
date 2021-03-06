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

/**
 * Curl adapter
 */
class RealestateCoNz_Api_Http_Adapter_Curl implements RealestateCoNz_Api_Http_Adapter
{

    protected $config = array();

    protected $curl;

    protected $response;

    /**
     * Set the config for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array())
    {
        $this->config = $config;
    }

    /**
     * Connect to the remote server
     *
     * @throws RealestateCoNz_Api_Http_Adapter_Exception
     * @param string  $host
     * @param int     $port
     */
    public function connect($host, $port = 80)
    {
        if(null !== $this->curl) {
            $this->close();
        }

        // Do the actual connection
        $this->curl = curl_init();
        
        // Make sure we're properly connected
        if (false === $this->curl) {
            throw new RealestateCoNz_Api_Http_Adapter_Exception("Could not initialise curl");
        }
        
        if ($port != 80) {
            curl_setopt($this->curl, CURLOPT_PORT, intval($port));
        }

        // Set timeout
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->config['timeout']);

        // Set Max redirects
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, $this->config['maxredirects']);

    }

    /**
     * Send request to the remote server
     *
     * @throws RealestateCoNz_Api_Http_Adapter_Exception
     * @param string        $method
     * @param string        $url
     * @param array         $headers
     * @param string        $body
     * @return string Request as text
     */
    public function write($method, $url, $headers = array(), $body = null)
    {
        // ensure correct method name
        $method = strtoupper($method);

        // set URL
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $curlValue = true;

        switch ($method) {
            case 'GET':
                $curlMethod = CURLOPT_HTTPGET;
                break;

            case 'POST':
                $curlMethod = CURLOPT_POST;
                break;

            case 'PUT':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "PUT";
                break;

            case 'DELETE':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "DELETE";
                break;

            default:
                // Unsupported method
                throw new RealestateCoNz_Api_Http_Adapter_Exception("Method not supported: " . $method);
        }

        curl_setopt($this->curl, $curlMethod, $curlValue);


        // don't return headers
        curl_setopt($this->curl, CURLOPT_HEADER, true);

        // ensure actual response is returned
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        // set headers
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

        //
        curl_setopt($this->curl, CURLINFO_HEADER_OUT, true);


        // set request body
        if(null !== $body) {
            if ($method == 'POST' || $method == 'PUT' || $method == 'DELETE') {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
            }
        }

        // send the request
        $response_body = curl_exec($this->curl);
        
        // check for connection errors
        if(false === $response_body) {
            throw new RealestateCoNz_Api_Http_Adapter_Exception("Error occured while executing request: " . curl_error($this->curl), curl_errno($this->curl));
        }

        $response_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        $this->response = new RealestateCoNz_Api_Http_Response($response_code, RealestateCoNz_Api_Http_Response::extractHeaders($response_body), RealestateCoNz_Api_Http_Response::extractBody($response_body));

        $request = array();

        $request['uri'] = $url;
        $request['method'] = $method;
        $request['headers'] = curl_getinfo($this->curl, CURLINFO_HEADER_OUT);
        $request['body'] = $body;

        return $request;
    }

    /**
     * Return read response from server
     *
     * @return RealestateCoNz_Api_Http_Response
     */
    public function read()
    {
        return $this->response;
    }


    /**
     * Close the connection to the server
     *
     */
    public function close()
    {
        if(is_resource($this->curl)) {
            curl_close($this->curl);
        }

        $this->curl         = null;
    }


}
