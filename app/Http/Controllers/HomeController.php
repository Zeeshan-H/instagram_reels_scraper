<?php

namespace App\Http\Controllers;

use App\API\APIService;
use GuzzleHttp\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    public function index()
    {
        $client = new Client();
        $response = $client->get(Config::get('api.url'));

        $data = json_decode($response->getBody(), true);

        return view('index', compact('data'));
    }
}
