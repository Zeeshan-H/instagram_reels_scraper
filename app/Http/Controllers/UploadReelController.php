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
        $caption = $request->caption;

        $file = $request->file('video');

        $uniqueFileName = $file->getClientOriginalName();
        if($file->move('Uploads', $uniqueFileName))
        {
            $videoUrl = url('Uploads/' . $uniqueFileName);

            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl, $caption);
            sleep(10);
            $apiService->graphAPIPostVideoAsReel($videoID);
            return redirect()->back()->with('success', 'Reel has been uploaded');
        }
        else
        return redirect()->back()->with('error', 'There was an error uploading video to Instagram as Reel');
    }
}
