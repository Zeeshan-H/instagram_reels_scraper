<?php

namespace App\Http\Controllers;

use App\API\APIService;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;;
use FFMpeg\FFMpeg;
use RealRashid\SweetAlert\Facades\Alert;

class UploadReelController extends Controller
{
    public function saveReelToHeroku(Request $request)
    {
        $caption = $request->caption;

        $file = $request->file('video');

        $uniqueFileName = uniqid() . '-' . $file->getClientOriginalName();
        $outputPath = public_path('Uploads/' . $uniqueFileName);

        if($file->move('Uploads', $uniqueFileName))
        {

            $videoUrl1 = url('Uploads/' . $uniqueFileName);

//            // Use Laravel-FFMpeg to convert the video
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($outputPath);
            $format = new X264();
            $format->setAudioCodec("aac");
            $format->setAdditionalParameters(explode(' ', '-pix_fmt yuv420p -b:v 4000k'));
            $video->save($format, 'Uploads/ffmpeg-'. $uniqueFileName);

            $videoUrl2 = url('Uploads/ffmpeg-'. $uniqueFileName);

            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl2, $caption);
            sleep(10);
            $result = $apiService->graphAPIPostVideoAsReel($videoID);
            if($result) {
                return response()->json(['success' => 'Video was successfully uploaded as a Instagram reel.']);
            }
            else
                return response()->json(['error' => 'Video doesnt meet Instagram reel requirements.']);
        }
        return response()->json(['error' => 'An error occurred while uploading video as reel to Instagram']);
    }
}
