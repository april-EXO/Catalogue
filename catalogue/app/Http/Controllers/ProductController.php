<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class ProductController extends Controller
{
    public function fetchProducts()
    {
        $client = new Client();

        $url = 'https://mangomart-autocount.myboostorder.com/wp-json/wc/v1/products';

        $allProducts = [];
        $page = 1;
        $totalPages = 1;

        do {
            $response = $client->request('GET', $url, [
                'auth' => ['ck_2682b35c4d9a8b6b6effac126ac552e0bfb315a0', 'cs_cab8c9a729dfb49c50ce801a9ea41b577c00ad71'],
                'query' => ['page' => $page]
            ]);

            $products = json_decode($response->getBody(), true);

            $allProducts = array_merge($allProducts, $products);

            $totalPages = $response->getHeaderLine('X-WP-TotalPages');

            $page++;
        } while ($page <= $totalPages);

        return $allProducts;
    }
}
