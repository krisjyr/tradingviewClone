<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MarketDataController extends Controller
{
    
    public function fetch($symbol, $timespan, $timemultiplier)
    {
        $currentDate = Carbon::now()->toDateString();
        $lastDate = Carbon::now()->subYears(5)->toDateString();

        $apiKey = config('services.polygon.key');
        $url = "https://api.polygon.io/v2/aggs/ticker/$symbol/range/$timemultiplier/$timespan/$lastDate/$currentDate?adjusted=true&sort=asc&apiKey=$apiKey";
    
        $response = Http::get($url);
    
        return response()->json($response->json());
    }

    public function search($string)
    {
        $apiKey = config('services.polygon.key');
        $url1 = "https://api.polygon.io/v3/reference/tickers?search=$string&order=asc&limit=1000&sort=ticker&apiKey=$apiKey";
        $url2 = "https://api.polygon.io/v3/reference/tickers?search=C:$string&order=asc&limit=1000&sort=ticker&apiKey=$apiKey";
        
        $response1 = Http::get($url1)->json();
        $response2 = Http::get($url2)->json();
        
        // Properly merge the results from both responses
        $merged = [
            'status' => $response1['status'] ?? 'OK',
            'count' => ($response1['count'] ?? 0) + ($response2['count'] ?? 0),
            'results' => array_merge($response1['results'] ?? [], $response2['results'] ?? []),
        ];
        
        // If pagination info exists in the responses, we could handle it here
        if (isset($response1['next_url']) || isset($response2['next_url'])) {
            $merged['next_url'] = $response1['next_url'] ?? $response2['next_url'] ?? null;
        }
        
        return response()->json($merged);
    }

}
