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

}
