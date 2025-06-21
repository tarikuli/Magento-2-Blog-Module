<?php
namespace Studio3Marketing\Magento2Blog\Controller\Article;

use Magento\Framework\App\Action\Context as Context;
use Magento\Framework\App\Request\Http as Http;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory as PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;


class View extends \Magento\Framework\App\Action\Action
{
    const API_KEY_CONFIG_PATH = 'segway_blog/general/api_key'; // Config path to retrieve API key

    public function __construct(
        protected Context          $context,
        protected PageFactory $pageFactory,
        protected Http        $request,
        protected Curl             $curl,
        protected ScopeConfigInterface $scopeConfig,
        protected Json             $json
    )
    {
        return parent::__construct($context);
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
     * Controller action to display a single blog article page.
     * Fetches article data by ID and sets page meta information.
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id= $this->request->getParam('id');
        if(!empty($id)){
            $articleData = $this->fetchBlogIndexPostById($id);
        }
        if(empty($id) || empty($articleData)  ){
            //TODO : Redirect to /blog/
        }

        $page = $this->pageFactory->create();

        /** @var Template $block */
        $block = $page->getLayout()->getBlock('magento2blog.article');
        $block->setData('articleData', $articleData);

        $page->getConfig()->getTitle()->set($articleData['data']['attributes']['seo']['title']??'');
        $page->getConfig()->setDescription($articleData['data']['attributes']['seo']['description']??'');
        $page->getConfig()->setKeywords($articleData['data']['attributes']['seo']['keywords']??'');

        return  $page ;
    }

    /**
     * Fetches a single blog post by its ID from the DatoCMS API.
     * @param int|string $id
     * @return array
     */
    public function fetchBlogIndexPostById($id)
    {
        $url = 'https://site-api.datocms.com/items/'.$id;
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("X-Api-Version", "3");
        $apiKey = $this->getApiKey();
        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
        $this->curl->get($url);
        $response = $this->curl->getBody();

        return $this->json->unserialize($response);
    }
}
