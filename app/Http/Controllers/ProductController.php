<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;


class ProductController extends Controller
{
    public function create(Request $request)
    {
        $searchTerm = $request->search;
        $files = File::where('name', 'LIKE', '%' . $searchTerm . '%')->orderBy('created_at', 'desc')
            ->paginate(10);

        $woocommerce = new Client(
            env('WC_STORE_URL'),
            env('WC_CUSTOMER_KEY'),
            env('WOO_CUSTOMER_SECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false,
            ]
        );
        $categories = $woocommerce->get('products/categories');
        return view('products.create', compact('categories', 'files'));
    }
    public function addProduct(Request $request)
    {

        $woocommerce = new Client(
            env('WC_STORE_URL'),
            env('WC_CUSTOMER_KEY'),
            env('WOO_CUSTOMER_SECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false,
            ]
        );

        $categories = $request->categories;
        $categories_ids = [];
        foreach ($categories as $category) {
            $categories_ids[] = ['id' => $category];
        }

        $newProduct = [
            'name' => $request->product_title,
            'type' => 'simple',
            'virtual' => true,
            'downloadable' => true,
            'regular_price' => $request->product_regular_price,
            'sale_price' => $request->product_sale_price,
            'description' => $request->product_description,
            'images' => [
                [
                    'src' => $request->image
                ],
            ],
            'downloads' => [
                [
                    'name' => $request->virtual_file_name,
                    'file' => $request->virtual_file_url,
                ],
            ],
            'categories' => $categories_ids,
        ];

        // Créer le produit
        $createdProduct = $woocommerce->post('products', $newProduct);

        if ($createdProduct) {
            // Le produit a été créé avec succès
            return redirect()->back()->with('message', 'Aricle ajouté avec succès!');
        } else {
            // Une erreur s'est produite lors de la création du produit
            return redirect()->back()->with('error', 'L\'jout a echoué!');
        }
    }
}
