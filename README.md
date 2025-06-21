# Magento 2 Blog  Module

## Overview
The **Magento2 Blog** module integrates a dynamic blog system into your Magento 2 store. It is built using a Headless CMS (DatoCMS) and leverages GraphQL for efficient data retrieval. Blog articles, categories, and featured images are fetched from DatoCMS using GraphQL queries calls, allowing you to manage content externally while seamlessly presenting it within your Magento storefront.

## Features
- Display latest blog posts on your Magento frontend
- Filter blog posts by category
- Fetch and display feature images for articles
- SEO-friendly blog URLs and meta information
- Category listing and navigation
- Product carousel integration for related products
- Easy configuration via Magento admin
- Built with Headless CMS (DatoCMS) for flexible content management
- Uses GraphQL for fast and flexible data queries

## Installation
1. **Copy the module files** into `app/code/Studio3Marketing/Magento2Blog` (or your custom path).
2. Run the following Magento CLI commands:
   ```sh
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento cache:flush
   ```
3. Ensure the module is enabled:
   ```sh
   php bin/magento module:status Studio3Marketing_Magento2Blog
   ```

## Configuration
1. Log in to the Magento Admin Panel.
2. Go to **Stores > Configuration > Magento2 Blog > General**.
3. Set the following options:
   - **API Key**: Your DatoCMS API key
   - **API URL**: (Optional) Override the default API endpoint
   - **Posts Per Page Limit**: Number of posts to show per page
4. Save the configuration and clear the cache if needed.

## Usage
- The blog will be accessible at `/blog` on your storefront.
- Article detail pages are available at `/blog/article/view?id=ARTICLE_ID` or SEO-friendly URLs if configured.
- Categories and navigation are automatically generated from your DatoCMS content.
- To customize templates, edit the `.phtml` files in `view/frontend/templates/`.
- Product carousels for related products can be managed via the `ProductCarousel` ViewModel.

## File Structure
- `Block/` - Magento blocks for fetching and preparing blog data
- `Controller/` - Controllers for blog, article, and category routes
- `Helper/` - Helper functions for category and debug utilities
- `Model/ApiClient.php` - Handles API requests to DatoCMS using GraphQL
- `ViewModel/` - ViewModels for advanced frontend features (e.g., product carousel)
- `view/frontend/` - Layout XML, templates, CSS, and JS for the frontend
- `etc/` - Module configuration files

## Customization
- Update layout or templates in `view/frontend/` as needed
- Extend blocks or controllers for additional features
- Use the helper and ViewModel classes for custom logic

## Support
For issues or feature requests, please contact the module author or your Magento development team.

---
**Studio3Marketing Magento2 Blog** — Seamlessly integrate a headless blog into your Magento 2 store using GraphQL and DatoCMS!
