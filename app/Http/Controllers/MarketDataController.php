<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class MarketDataController extends Controller
{
    
    public function fetch($symbol)
    {
        $apiKey = config('services.polygon.key');
        $url = "https://api.polygon.io/v2/aggs/ticker/$symbol/range/1/day/2023-01-01/2023-12-31?adjusted=true&sort=asc&limit=500&apiKey=$apiKey";
    
        $response = Http::get($url);
    
        return response()->json($response->json());
    }

}
