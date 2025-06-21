<?php
namespace Studio3Marketing\Magento2Blog\Block\Categoryblog;

use Studio3Marketing\Magento2Blog\Model\ApiClient;
use Studio3Marketing\Magento2Blog\Helper\Data as HelperData;

class Categoryblog extends \Magento\Framework\View\Element\Template
{
    protected $helperData;
    protected $apiClient;

    public function __construct(
        ApiClient $apiClient,
        HelperData $helperData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->apiClient = $apiClient;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Fetches all categories from articles using a GraphQL query and helper.
     * @return array
     */
    public function fetchCategory(): array
    {
        // Fetch all categories using the helper
        $query = '{
    "query": "{  allArticles(filter: {categories: {matches: {pattern: \\"\\"}}}) {    categories   } }"
}';
        $result = $this->apiClient->fetchArticles($query);
        return $this->helperData->getAllCategories($result);
    }

}
