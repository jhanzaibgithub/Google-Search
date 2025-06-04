<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $searchType = $request->input('type', 'all'); // default is 'all'
        $results = [];

        if ($query) {
            $apiKey = config('services.google_search.api_key');
            $cx = config('services.google_search.cse_id');

            $params = [
                'key' => $apiKey,
                'cx' => $cx,
                'q' => $query,
            ];

            if ($searchType === 'image') {
                $params['searchType'] = 'image';
            }

            $response = Http::get('https://www.googleapis.com/customsearch/v1', $params);
            $results = $response->json('items') ?? [];
        }

        // Check if it's an AJAX request
        if ($request->ajax()) {
            return response()->json(['results' => $results]);
        }

        return view('google-search', compact('results', 'query', 'searchType'));
    }
}
