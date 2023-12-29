<?php

namespace App\Http\Controllers;

use App\API\APIService;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use FFMpeg\FFMpeg;

class UploadReelController extends Controller
{
    public function saveReelToHeroku(Request $request)
    {
        $validated = $request->validate([
            'video' => 'max:10240'
        ]);
        $caption = $request->caption;

        $file = $request->file('video');

        $uniqueFileName = uniqid() . '-' . $file->getClientOriginalName();
        if($file->move('Uploads', $uniqueFileName))
        {
            $videoUrl = url('Uploads/' . $uniqueFileName);

            // Use Laravel-FFMpeg to convert the video
            FFMpeg::fromDisk('local')
                ->open($file->getPathname())
                ->export()
                ->toDisk('local')
                ->inFormat(new X264('aac'))
                ->onProgress(function ($percentage) {
                    // Handle progress updates if needed
                })
                ->save(storage_path('app/public/Uploads/' . $uniqueFileName));


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
