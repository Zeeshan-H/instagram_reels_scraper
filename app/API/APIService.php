<?php


namespace App\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;


class APIService {


    function graphAPIPostVideoToGetID($videoUrl, $caption): String
    {
        $url = 'https://graph.facebook.com/v18.0/17841461509998509/media?video_url='. $videoUrl .'&media_type=REELS&caption='. $caption;
        $token = Config::get('api.token');

        $client = new Client();
        $response = $client->post($url, [
            'headers' => ['Authorization' => 'Bearer ' . $token],
        ]);
        $decodedResponse = json_decode($response->getBody()->getContents(), true);
        $videoID = $decodedResponse['id'];
        return $videoID;
    }

    function graphAPIPostVideoAsReel($videoID): bool
    {
        $url = 'https://graph.facebook.com/v18.0/17841461509998509/media_publish';
        try {
            $token = Config::get('api.token');

            $client = new Client();
            $response = $client->post($url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'form_params' => [
                    'creation_id' => $videoID
                ]
            ]);
            return true;
//            dd("Success");
        }
        catch (RequestException $e) {
            return false;
        }

    }
}
