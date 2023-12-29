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

        $file = $request->file('video');

//        $uniqueFileName = uniqid() . '-' . $file->getClientOriginalName();
        $uniqueFileName = $file->getClientOriginalName();
        if($file->move('Uploads', $uniqueFileName))
        {
            $videoUrl = url('Uploads/' . $uniqueFileName);
            dd($videoUrl);
            // Print the URL or use it as needed
            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl);
//            dd($videoID, $videoUrl);
            $apiService->graphAPIPostVideoAsReel($videoID);

        }
        else
        dd("Not saved");
    }
}
