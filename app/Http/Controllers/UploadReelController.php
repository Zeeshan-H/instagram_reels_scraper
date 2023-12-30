<?php

namespace App\Http\Controllers;

use App\API\APIService;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;;
use FFMpeg\FFMpeg;
use RealRashid\SweetAlert\Facades\Alert;

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
        $outputPath = public_path('Uploads/' . $uniqueFileName);

        if($file->move('Uploads', $uniqueFileName))
        {

            $videoUrl = url('Uploads/' . $uniqueFileName);

//            // Use Laravel-FFMpeg to convert the video
//            $ffmpeg = FFMpeg::create();
//            $video = $ffmpeg->open($file->getPathname());
//
//            $video
//                ->filters()
//                ->resize(new \FFMpeg\Coordinate\Dimension(640, 480))
//                ->synchronize();
//
//            $video
//                ->save(new X264(), $outputPath . '.mp4');


            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl, $caption);
            sleep(10);
            $result = $apiService->graphAPIPostVideoAsReel($videoID);
            if($result) {
                return response()->json(['success' => 'Video was successfully uploaded as a Instagram reel.']);
            }
        }
        else
        return redirect()->back()->with('error', 'No video is selected');
    }
}
