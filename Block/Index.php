<?php
namespace Studio3Marketing\Magento2Blog\Block;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Studio3Marketing\Magento2Blog\Helper\Data as HelperData;
use Studio3Marketing\Magento2Blog\Model\ApiClient;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
class Index extends \Magento\Framework\View\Element\Template
{
    const API_URL_CONFIG_PATH = 'segway_blog/general/api_url';
    const API_KEY_CONFIG_PATH = 'segway_blog/general/api_key';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        protected Curl                                   $curl,
        protected UrlRewriteFactory                      $urlRewriteFactory,
        protected ApiClient                              $apiClient,
        protected Json                                   $json,
        protected ScopeConfigInterface                   $scopeConfig,
        protected HelperData                             $helperData,
        array                                            $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * Returns the API URL from configuration or a default value if not set.
     * @return string
     */
    protected function getApiUrl(): string
    {
        $apiUrl = rtrim($this->scopeConfig->getValue(self::API_URL_CONFIG_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), '/');

        // Check if the API URL is empty, then assign the default value
        if (empty($apiUrl)) {
            $apiUrl = 'https://site-api.datocms.com/';
        }

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

    /**
     * Gets the number of posts to display per page from configuration.
     * @return int
     */
    public function getPostsPerPageLimit(): int
    {
        return (int)$this->scopeConfig->getValue(
            'segway_blog/general/posts_per_page_limit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * Fetches all blog posts for the blog index page from the API.
     * @return array
     */
    public function fetchBlogIndexPosts(): array
    {
        $url = $this->getApiUrl() . '/items?filter[type]=article';
        $apiKey = $this->getApiKey();
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
        $this->curl->get($url);
        $response = $this->curl->getBody();

        $posts = $this->json->unserialize($response);

        // Sort posts by published_date in descending order
        usort($posts['data'], function($a, $b) {
            return strtotime($b['attributes']['published_date']) - strtotime($a['attributes']['published_date']);
        });

        return $posts;
    }

//    public function fetchBlogIndexPostsByCategory($categoryName, $limit = 5, int $offset = 0): array
//    {
//        $url = $this->getApiUrl() . '/items?filter[type]=article&filter[fields][categories][eq]=' . urlencode($categoryName) . '&page[offset]=' . $offset . '&page[limit]=' . $limit;
//        $apiKey = $this->getApiKey();
//        $this->curl->addHeader("Accept", "application/json");
//        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
//        $this->curl->get($url);
//        $response = $this->curl->getBody();
//        return $this->json->unserialize($response);
//    }

    /**
     * Fetches blog posts by category using a GraphQL query.
     * @param string $categoryName
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchBlogIndexPostsByCategory($categoryName, $limit = 5, int $offset = 0): array
    {

        $query = '{
    "query": "{  allArticles(filter: {categories: {matches: {pattern: \\"'.$categoryName.'\\"}}}) { id title _status categories  slug publishedDate _publishedAt featuredImage { url alt title width height } content}  _allArticlesMeta(filter: {categories: {matches: {pattern: \\"'.$categoryName.'\\"}}}) {    count  }}"}';
        return $this->apiClient->fetchArticles($query);
//        return $this->json->unserialize($response);
    }

    /**
     * Generates a URL for a blog post, using a slug if available.
     * @param int|string $id
     * @param string $slug
     * @return string
     */
    public function getPostLink($id, $slug = '')
    {
        if(!empty($slug)){
            $this->addUrlRewrite($id, $slug);
            return $this->getUrl("blog/".$slug );
        }else{
            return $this->getUrl("blog/#");
        }
//        return $this->getUrl("blog/article/view", ['id' => $id, 'slug' => $slug]);
    }

    /**
     * Adds a custom URL rewrite for a blog post slug.
     * @param int|string $id
     * @param string $slug
     */
    public function addUrlRewrite($id, $slug)
    {
        try {
            $urlRewrite = $this->urlRewriteFactory->create();
            $urlRewrite->setStoreId(1) // Store ID
            ->setIsSystem(0) // 0 - Custom URL Rewrite
            ->setIdPath($slug) // Unique ID path
            ->setRequestPath('blog/'.$slug) // Request Path
            ->setTargetPath('blog/article/view/id/'.$id.'/slug/'.$slug.'/') // Target Path
            ->setRedirectType(0) // No redirect
            ->save();
        } catch (\Exception $e) {
//            throw new \Magento\Framework\Exception\NoSuchEntityException(__($e->getMessage()));
        }
    }

    /**
     * Fetches the feature image data for a given upload ID from the API.
     * @param string $uploadId
     * @return array
     */
    public function fetchFeatureImage($uploadId)
    {
        if (empty($uploadId)) {
            return [];
        }
        $url = $this->getApiUrl() . '/uploads/' . $uploadId;
        $apiKey = $this->getApiKey();
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
        $this->curl->get($url);
        $response = $this->curl->getBody();

        $data = $this->json->unserialize($response);
        return $data['data'] ?? [];
    }

    /**
     * Gets the total number of posts, optionally filtered by category.
     * @param string $categoryName
     * @return int
     */
    public function getTotalPosts($categoryName = ''): int
    {
        $url = $this->getApiUrl() . '/items?filter[type]=article' . ($categoryName ? '&filter[fields][categories][eq]=' . urlencode($categoryName) : '');
        $apiKey = $this->getApiKey();
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
        $this->curl->get($url);
        $response = $this->curl->getBody();

        // Parse the JSON response
        $data = $this->json->unserialize($response);

        // Check if the 'data' field exists and count the number of items
        if (isset($data['data'])) {
            return count($data['data']);
        } else {
            return 0; // Return 0 if 'data' is missing
        }
    }
}
