<?php
namespace Studio3Marketing\Magento2Blog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
//use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\CatalogWidget\Block\Product\ProductsList;

class ProductCarousel implements ArgumentInterface
{
    protected $productRepository;
    protected $searchCriteriaBuilder;
//    protected $abstractProduct;
    protected $productsList;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductsList $productsList,
//        AbstractProduct $abstractProduct
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productsList = $productsList;
//        $this->abstractProduct = $abstractProduct;
    }

    /**
     * Retrieves a collection of products by their SKUs, only including enabled products.
     * @param string $skus Comma-separated SKUs
     * @return array
     */
    public function getProductBySkuCollection($skus)
    {
        $skuArray = explode(',', $skus);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('sku', $skuArray, 'in')
            ->addFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) // Only enabled products
            ->create();

        $products = $this->productRepository->getList($searchCriteria)->getItems();

        // Load additional attributes
        foreach ($products as $product) {
            $product->load('overlay_image');
        }

        return $products;
    }

    /**
     * Returns the HTML for a product image.
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @return string
     */
    public function getImage($product, $imageId)
    {
        return $this->productsList->getImage($product, $imageId)->toHtml();
    }

//    public function getImageUrl($product, $imageId)
//    {
//        return $this->abstractProduct->getImage($product, $imageId)->getImageUrl();
//    }

    /**
     * Returns the reviews summary HTML for a product.
     * @param mixed $_item
     * @param string $templateType
     * @return string
     */
    public function getReviewsSummaryHtml($_item, $templateType)
    {
        return $this->productsList->getReviewsSummaryHtml($_item, $templateType);
    }

//    public function getProductDetailsHtml($_item)
//    {
//        return $this->abstractProduct->getProductDetailsHtml($_item);
//    }

    /**
     * Returns the product price HTML.
     * @param \Magento\Catalog\Model\Product $product
     * @param string $type
     * @return string
     */
    public function getProductPriceHtml($product,$type)
    {
        return $this->productsList->getProductPriceHtml($product,$type);
    }

    /**
     * Returns the product details HTML.
     * @param mixed $_item
     * @return string
     */
    public function getProductDetailsHtml($_item)
    {
        return $this->productsList->getProductDetailsHtml($_item);
    }

    /**
     * Returns the image URL for a product image.
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @return string
     */
    public function getImageUrl($product, $imageId)
    {
        return $this->productsList->getImage($product, $imageId)->getImageUrl();
    }

    /**
     * Returns the parameters for the add to cart post action.
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToCartPostParams($product)
    {
        return $this->productsList->getAddToCartPostParams($product);
    }

    /**
     * Returns the product URL with optional additional parameters.
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = []){
        return $this->productsList->getProductUrl($product, $additional);
    }

    /**
     * Returns the parameters for the add to wishlist action.
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToWishlistParams($product)
    {
        return $this->productsList->getAddToWishlistParams($product);
    }

    /**
     * Returns the URL for the add to compare action.
     * @return string
     */
    public function getAddToCompareUrl()
    {
        return $this->productsList->getAddToCompareUrl();
    }
}
