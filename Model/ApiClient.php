<?php

namespace Studio3Marketing\Magento2Blog\Model;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Studio3Marketing\Magento2Blog\Helper\Data as HelperData;

class ApiClient
{
    const API_URL_CONFIG_PATH = 'segway_blog/general/api_url';
    const API_KEY_CONFIG_PATH = 'segway_blog/general/api_key';

    public function __construct(
        protected Json $json,
        protected ScopeConfigInterface $scopeConfig,
        protected HelperData $helperData
    )
    {

    }
    /**
     * Fetches articles from the API using a GraphQL query string.
     * @param string $query
     * @return array
     */
    public function fetchArticles($query)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getApiUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $query,
//            CURLOPT_POSTFIELDS =>'{
//    "query": "{  allArticles(filter: {categories: {matches: {pattern: \\"\\"}}}) {    categories   } }"
//}',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'authorization: Bearer '.$this->getApiKey(),
                'content-type: application/json, application/json',
                'x-environment: main'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $this->json->unserialize($response);
//        return json_decode($response, true);
    }

    /**
     * Returns the API URL from configuration or a default value if not set.
     * @return string
     */
    protected function getApiUrl(): string
    {
//        $apiUrl = rtrim($this->scopeConfig->getValue(self::API_URL_CONFIG_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), '/');
//
//        // Check if the API URL is empty, then assign the default value
//        if (empty($apiUrl)) {
            $apiUrl = 'https://graphql.datocms.com/';
//        }

        return $apiUrl;
    }

    /**
     * Returns the API key from configuration.
     * @return string
     */
    protected function getApiKey(): string
    {
        return $this->scopeConfig->getValue(self::API_KEY_CONFIG_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
