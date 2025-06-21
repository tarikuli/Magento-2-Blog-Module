<?php
namespace Studio3Marketing\Magento2Blog\Controller\Category;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Studio3Marketing\Magento2Blog\Block\Index as BlogIndex;
use Studio3Marketing\Magento2Blog\Helper\Data as HelperData;

class View extends Action
{
    protected $helperData;

    protected $pageFactory;
    protected $blogIndex;

    public function __construct(
        Context     $context,
        PageFactory $pageFactory,
        HelperData $helperData,
        BlogIndex   $blogIndex
    )
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->helperData = $helperData;
        $this->blogIndex = $blogIndex;
    }

    /**
     * Controller action to display blog posts by category.
     * Fetches posts for a given category and sets data for the template.
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $categoryName = $this->getRequest()->getParam('name', '');
        $currentPage = $this->getRequest()->getParam('page', 1);
        $limit = ($categoryName) ? 2 : 5; // Set different limit for category page
        $offset = ($currentPage - 1) * $limit;
        $posts = $this->blogIndex->fetchBlogIndexPostsByCategory($categoryName, $limit, $offset);
        $totalPosts = $this->blogIndex->getTotalPosts($categoryName);

//        $this->helperData->jdbg("categoryName = ", $categoryName);
//        $this->helperData->jdbg("currentPage = ", $currentPage);
//        $this->helperData->jdbg("limit = ", $limit);
//        $this->helperData->jdbg("offset = ", $offset);
//        $this->helperData->jdbg("posts = ", $posts);
//        $this->helperData->jdbg("totalPosts = ", $totalPosts);
//        exit();

        $page = $this->pageFactory->create();
        $page->getLayout()->getBlock('blog.list')
            ->setData('blogPosts', $posts)
            ->setData('categoryName', $categoryName)
            ->setData('currentPage', $currentPage) // Pass current page to the template
            ->setData('totalPosts', $totalPosts); // Pass total posts count to the template

        return $page;
    }

}
