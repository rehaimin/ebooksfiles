<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class AmazonCrawlerController extends Controller
{
    // Constants for CSS selectors
    const SELECTOR_TITLE = '#productTitle';
    const SELECTOR_PRICE = 'span>span.a-size-base.a-color-price.a-color-price';
    const SELECTOR_IMAGE = '#landingImage';
    const SELECTOR_DESCRIPTION = "[data-a-expander-name='book_description_expander'] .a-expander-content.a-expander-partial-collapse-content";

    /**
     * Scrape Amazon product data.
     *
     * @param  string  $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function scrapeAmazonProduct(Request $request)
    {
        // Validate the request
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $url = $request->input('url');
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $url);
            $html = $response->getContent();

            $crawler = new Crawler($html);

            // Extract the title of the product
            $title = $crawler->filter(self::SELECTOR_TITLE)->text();

            // Extract the price of the product
            $textPrice = str_replace("$", "", $crawler->filter(self::SELECTOR_PRICE)->text());
            if (strpos($textPrice, " - ") !== false) {
                $prices = explode(" - ", $textPrice);
                $price = (floatval($prices[0]) > floatval($prices[1])) ? floatval($prices[0]) : floatval($prices[1]);
            } else {
                $price = floatval($textPrice);
            }

            // Extract the image of the product
            $coverImage = $crawler->filter(self::SELECTOR_IMAGE)->attr('src');
            $coverLargeImage = $crawler->filter(self::SELECTOR_IMAGE)->attr('data-old-hires');
            $arrImage = explode(".", $coverImage);
            $image = $arrImage[0] . "." . $arrImage[1] . "." . $arrImage[2] . "." . $arrImage[4];

            // Extract the description of the product (optional)
            $description = $crawler->filter(self::SELECTOR_DESCRIPTION)->html();

            // Return the extracted data as an associative array
            $data = [
                'title' => $title,
                'price' => $price,
                'image' => $image,
                'largeimage' => $coverLargeImage,
                'description' => $description,
            ];

            // Return a JSON response with the extracted data and a success message
            return response()->json(['data' => $data, 'message' => 'Extraction réussie des données.']);
        } catch (\Exception $e) {
            // Return an error response with the error message
            return response()->json(['error' => 'Erreur lors de l\'extraction des données : ' . $e->getMessage(), 'message' => 'Erreur lors de l\'extraction des données.'], 500);
        }
    }
}
