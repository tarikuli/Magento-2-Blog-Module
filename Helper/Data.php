<?php

namespace Studio3Marketing\Magento2Blog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    public function __construct( Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Returns an array of all unique categories found in the provided articles data.
     * @param array $data
     * @return array
     */
    public function getAllCategories(array $data): array {
        $uniqueCategories = [];
//        $this->jdbg(__LINE__, $data);
//        exit();

        // Check if the 'allArticles' array exists
        if (isset($data['data']['allArticles']) && is_array($data['data']['allArticles'])) {
            $allArticles = array_reverse($data['data']['allArticles']);
//            foreach ($data['data']['allArticles'] as $article) {
            foreach ($allArticles as $article) {
                if (isset($article['categories'])) {
                    // Split categories by comma and normalize to lowercase
                    $categories = array_map('trim', explode(',', $article['categories']));
                    foreach ($categories as $category) {
                        $normalizedCategory = strtolower($category);
                        // Use the category as the key to ensure uniqueness
                        $uniqueCategories[$normalizedCategory] = ucfirst($normalizedCategory);
                    }
                }
            }
        }

        // Return the values of the associative array (unique categories)
        return array_values($uniqueCategories);
    }


//    public function getAllCategories(array $data): array
//    {
//        $categories = [];
////        $this->jdbg(__LINE__, $data);
////        exit();
//        // Check if the 'allArticles' array exists
//        if (isset($data['data']['allArticles']) && is_array($data['data']['allArticles'])) {
//            foreach ($data['data']['allArticles'] as $article) {
//                if (isset($article['categories'])) {
//                    // Check if categories contain a comma
//                    if (strpos($article['categories'], ',') !== false) {
//                        // If categories are listed together, treat them as a single category
//                        $categories[] = trim($article['categories']);
//                    } else {
//                        // Otherwise, split categories by comma and add them individually
//                        $categoryList = array_map('trim', explode(',', $article['categories']));
//                        $categories = array_merge($categories, $categoryList);
//                    }
//                }
//            }
//        }
//        // Remove duplicates
//        $uniqueCategories = array_unique($categories);
//
//        return array_values($uniqueCategories);
//    }


    /**
     * Capitalizes the category name (private helper).
     * @param string $category
     * @return string
     */
    private function capitalizeCategoryName(string $category): string
    {
        return ucwords(strtolower($category));
    }


    /**
     * Debug/log helper for development (prints/logs variable info).
     * @param string $label
     * @param mixed $obj
     */
    public function jdbg($label, $obj)
    {
        $fileName = strtolower(str_replace('\\', '_', get_class($this))) . '.log';
        // $fileName =’jdebug.log';
        $filePath = BP . '/var/log/debug_' . $fileName;
        $writer = new \Zend_Log_Writer_Stream($filePath);
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logStr = "{$label}:";
        switch (gettype($obj)) {
            case 'boolean':
                if ($obj) {
                    $logStr .= "(bool) -> TRUE";
                } else {
                    $logStr .= "(bool) -> FALSE";
                }
                break;
            case 'integer':
            case 'double':
            case 'string':
                $logStr .= "(" . gettype($obj) . ") -> {$obj}";
                break;
            case 'array':
                $logStr .= "(array) -> " . print_r($obj, true);
                break;
            case 'object':
                try {
                    if (method_exists($obj, 'debug')) {
                        $logStr .= "(" . get_class($obj) . ") -> " . print_r($obj->debug(), true);
                    } else {
                        $this->jdbg($label,print_r($obj,true));
                        $logStr .= "NULL";
                        break;
                        $logStr .= "Don't know how to log object of class " . get_class($obj);
                    }
                } catch (Exception $e) {
                    $logStr .= "Don't know how to log object of class " . get_class($obj);
                }
                break;
            case 'NULL':
                $logStr .= "NULL";
                break;
            default:
                $logStr .= "Don't know how to log type " . gettype($obj);
        }
        echo "<pre>";
            print_r($logStr);
        echo "</pre>";
//        $logger->info($logStr);
    }

}
