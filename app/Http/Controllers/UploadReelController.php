<?php

namespace App\Http\Controllers;

use App\API\APIService;
use Illuminate\Http\Request;

class UploadReelController extends Controller
{
    public function saveReelToHeroku(Request $request)
    {
        $request->validate([
            'video' => 'max:10240', // Assuming a maximum file size of 10 MB
        ]);
        $config = array( // instantiation config params
            'user_id' => '17841461509998509',
            'access_token' => 'EABojrRw4lYoBO6tB2m5iG3YJ7mSeTZAMAAlpk4TbYQUG9ehQOk8SyZBLvKdHghZA9t8NKnvXrd90OJpwMo5XiTWUGrR0pUllawVGH0CYHOKnC693xNqPr9s5pnxdxkEUXbTtpoLZBZCarLxqOyFBWQ2rvngJZAL2rdHUuyd2F2CHhzwEo7WjRoHnSZBD8SyfW8kuy1aHnZBlungS38BX',
        );

        $file = $request->file('video');

//        $uniqueFileName = uniqid() . '-' . $file->getClientOriginalName();
        $uniqueFileName = $file->getClientOriginalName();
        if($file->move('Uploads', $uniqueFileName))
        {
            $videoUrl = url('Uploads/' . $uniqueFileName);
            $media = new Media( $config );

            $videoContainerParams = array( // container parameters for the video post
                                'caption' => 'Another test', // caption for the post
                                'video_url' => $videoUrl, // url to the video must be on a public server
                                'media_type' => 'REELS', // specifying this should be set to VIDEO
                                'is_carousel_item' => false, // is this in a carousel
                'location_id' => '', // can be left blank otherwise the id of the facebook page associated with the location you want tagged
                'thumb_offset' => 2 // number of milliseconds in the video to grab the thumbnail
            );

            // create video container
            $videoContainer = $media->create( $videoContainerParams );

            // get id of the video container
            $videoContainerId = $videoContainer['id'];

            dd($videoContainerId, $videoUrl);

//            dd($videoUrl);
//            // Print the URL or use it as needed
//            $apiService = new APIService();
//            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl);
////            dd($videoID, $videoUrl);
//            $apiService->graphAPIPostVideoAsReel($videoID);

        }
        else
        dd("Not saved");
    }
}
