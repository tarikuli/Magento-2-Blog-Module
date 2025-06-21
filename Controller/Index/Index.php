<?php
namespace Studio3Marketing\Magento2Blog\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    /**
     * Controller action to display the main blog index page.
     * Sets the page title and description.
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
//        return $this->_pageFactory->create();
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->set('Blog | Segway Official Store');
        $resultPage->getConfig()->setDescription('Welcome to the Segway official store blog. Explore the latest news, updates, and insights about Segway products, innovations, and lifestyle tips.');
        return $resultPage;
    }
}
