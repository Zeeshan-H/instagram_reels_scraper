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

        if ($file->move('Uploads', $uniqueFileName)) {
            // Use Laravel-FFMpeg to convert the video
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($outputPath);
            $format = new X264();
            $format->setAudioCodec("aac");
            $format->setVideoCodec("libx264");
            $format->setAudioKiloBitrate('128k');
            $format->setKiloBitrate(1500); // Adjust as needed
            $format->setAdditionalParameters(['-pix_fmt', 'yuv420p', '-profile:v', 'high', '-level', '4.2', '-movflags', '+faststart']);

            $uniqueFileName2 = 'ffmpeg-' . $uniqueFileName;
            $outputPath2 = public_path('Uploads/' . $uniqueFileName2);
            $video->save($format, $outputPath2);

            $videoUrl = url('Uploads/' . $uniqueFileName2);

            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl, $caption);
            sleep(10);
            $result = $apiService->graphAPIPostVideoAsReel($videoID);

            if ($result) {
                return response()->json(['success' => 'Video was successfully uploaded as an Instagram Reel.']);
            } else {
                return response()->json(['error' => 'Video does not meet Instagram Reel requirements.']);
            }
        } else {
            return response()->json(['error' => 'An error occurred while uploading video as a reel to Instagram.']);
        }

        return response()->json(['error' => 'An error occurred while uploading video as reel to InstagramService']);
    }
}
