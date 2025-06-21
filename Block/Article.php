<?php
namespace Studio3Marketing\Magento2Blog\Block;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Article extends \Magento\Framework\View\Element\Template
{
    const API_KEY_CONFIG_PATH = 'segway_blog/general/api_key';

    protected ScopeConfigInterface $scopeConfig;
    protected Curl $curl;
    protected Json $json;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Curl $curl,
        Json $json,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->curl = $curl;
        $this->json = $json;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Fetches the feature image data for a given upload ID from the DatoCMS API.
     * @param string $uploadId
     * @return array
     */
    public function fetchFeatureImage($uploadId)
    {
        if (empty($uploadId)) {
            return [];
        }
        $url = 'https://site-api.datocms.com/uploads/' . $uploadId;
        $apiKey = $this->getApiKey();
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
        $this->curl->get($url);
        $response = $this->curl->getBody();

        $data = $this->json->unserialize($response);
        return $data['data'] ?? [];
    }

    /**
     * Retrieves the latest blog posts, optionally filtered by category, from the DatoCMS API.
     * @param int $limit
     * @param string $category
     * @return array
     */
    public function getLatestBlogPosts($limit, $category = '')
    {
        $url = 'https://site-api.datocms.com/items?filter[type]=article';

        // Add category filter if provided
        if (!empty($category)) {
            // Split the comma-separated categories into an array
            $categories = explode(',', $category);
            // Trim whitespace and build the category filter part of the URL
            $categoryFilters = [];
            foreach ($categories as $cat) {
                $categoryFilters[] = 'filter[fields][categories][eq]=' . urlencode(trim($cat));
            }
            // Join the category filters with '&' and append to the URL
            $url .= '&' . implode('&', $categoryFilters);
        }

        // Add limit and sorting
        $url .= '&page[limit]=' . $limit . '&sort=-published_date';

        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("X-Api-Version", "3");
        $apiKey = $this->getApiKey();
        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
        $this->curl->get($url);
        $response = $this->curl->getBody();

        $data = $this->json->unserialize($response);
        return $data['data'] ?? [];
    }

    /**
     * Retrieves the API key from Magento configuration.
     * @return string
     */
    protected function getApiKey(): string
    {
        return $this->scopeConfig->getValue(self::API_KEY_CONFIG_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Generates a URL for a blog post based on its ID and optional slug.
     * @param int|string $id
     * @param string $slug
     * @return string
     */
    public function getPostLink($id, $slug = '')
    {
        return $this->getUrl("blog/article/view", ['id' => $id, 'slug' => $slug]);
    }


}
