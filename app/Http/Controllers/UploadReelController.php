<?php

namespace App\Http\Controllers;

use App\API\APIService;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;


class UploadReelController extends Controller
{
    public function saveReelToHeroku(Request $request)
    {
        $validated = $request->validate([
            'video' => 'max:10240'
        ]);
        $caption = $request->caption;

        $file = $request->file('video');

        $uniqueFileName = $file->getClientOriginalName();
        if($file->move('Uploads', $uniqueFileName))
        {
            $videoUrl = url('Uploads/' . $uniqueFileName);

            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl, $caption);
            sleep(10);
            $result = $apiService->graphAPIPostVideoAsReel($videoID);
            if($result)
             return redirect()->back()->with('success', 'Reel has been uploaded');
            else
             return redirect()->back()->with('error', 'There was an error uploading video to Instagram as Reel');
        }
        else
        return redirect()->back()->with('error', 'No video is selected');
    }
}
