<?php

namespace App\Http\Controllers;

use App\API\APIService;
use GuzzleHttp\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\API\InstagramService\Api;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class HomeController extends Controller
{
    public function index()
    {
        $client = new Client();
//        $response = $client->get(Config::get('api.url'));
        $cachePool = new FilesystemAdapter('InstagramService', 0, __DIR__ . '/../cache');
        $api = new Api($cachePool);
        $api->login('babybara', 'BloomingToe!!');
        $reelsFeed = null;
        $data = [];

//        $reelsFeed = $api->getReels(25288085);
//        dd($reelsFeed->getReels());
        $userNames = ['hiropyon1116', 'serious.bara', 'minokapi', 'capisabara', 'bapybara',
            'capybara.worlds', 'ilove.capybara', 'capybarasenthusiasts'];

        foreach($userNames as $userName) {
            $profile = $api->getProfile($userName);
            if ($profile) {
                $reelsFeed = $api->getReels($profile->getId());
                foreach ($reelsFeed->getReels() as $reels) {
//            echo 'ID        : ' . $reels->getId() . "\n";
//            echo 'Code      : ' . $reels->getShortCode() . "\n";
//                    echo 'Caption   : ' . $reels->getCaption() . "\n";
                    $data[] = [
                        'caption' => $reels->getCaption(),
                        'videoUrl' => $reels->getVideos()[0]->url,
                        'link' => $reels->getLink()
                    ];
                }
            }
        }

//        foreach ($reelsFeed->getReels() as $reels) {
////            echo 'ID        : ' . $reels->getId() . "\n";
////            echo 'Code      : ' . $reels->getShortCode() . "\n";
//            echo 'Caption   : ' . $reels->getCaption() . "\n";
//            dd($reels->getCaption());
////            echo 'Link      : ' . $reels->getVideos()[0]->url . "\n";
////            echo 'Likes     : ' . $reels->getLikes() . "\n";
////            echo 'Date      : ' . $reels->getDate()->format('Y-m-d h:i:s') . "\n\n";
//        }
//        $data = json_decode($response->getBody(), true);

        return view('index', compact('data'));
    }

}
