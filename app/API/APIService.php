<?php


namespace App\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;


class APIService {


    function graphAPIPostVideoToGetID($videoUrl): String
    {
        $url = 'https://graph.facebook.com/v18.0/17841461509998509/media?video_url='. $videoUrl .'&media_type=REELS&caption=Testing#test';
        $token = Config::get('api.token');

        $client = new Client();
        $response = $client->post($url, [
            'headers' => ['Authorization' => 'Bearer ' . $token],
        ]);
        $decodedResponse = json_decode($response->getBody()->getContents(), true);
        $videoID = $decodedResponse['id'];
        return $videoID;
//        dd($response->getBody()->getContents(), $videoUrl);
    }

    function graphAPIPostVideoAsReel($videoID)
    {
        $url = 'https://graph.facebook.com/v18.0/17841461509998509/media_publish?creation_id='. $videoID;
        try {
            $token = Config::get('api.token');

            $client = new Client();
            $response = $client->post($url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);
        }
        catch (RequestException $e) {
            dd($e->getMessage());
        }

        dd($response->getBody()->getContents());
    }
}
