<?php

namespace Modules\MainPage\App\Http\AbstractClasses;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

abstract class MainPageAbstract extends Controller
{
    protected function fetchIphonesAndSave()
    {
        $response = Http::get('https://dummyjson.com/products/category/smartphones');
        $data = $response->json();

        $fillableAttributes = (new Product)->getFillable();

        collect($data['products'] ?? [])
            ->filter(fn($product) => stripos($product['title'], 'iPhone') !== false)
            ->each(function ($product) use ($fillableAttributes) {

                $filteredProductData = array_filter(
                    $product,
                    fn($key) => in_array($key, $fillableAttributes),
                    ARRAY_FILTER_USE_KEY
                );

                $jsonData = array_map(
                    fn($value) => is_array($value) ? json_encode($value) : $value,
                    $filteredProductData
                );

                Product::updateOrCreate(
                    $jsonData
                );
            });
    }
}
