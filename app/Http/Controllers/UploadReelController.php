<?php

namespace App\Http\Controllers;

use App\API\APIService;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class UploadReelController extends Controller
{
    public function saveReelToHeroku(Request $request)
    {
        $caption = $request->caption;

        $uploadsDirectory = public_path('Uploads');
        File::cleanDirectory($uploadsDirectory);
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
            $format->setVideoCodec("libx264");
            $format->setAudioKiloBitrate('128k');
            $format->setKiloBitrate(4000); // Adjusted for recommended settings
            $format->setAdditionalParameters([
                '-pix_fmt', 'yuv420p',
                '-profile:v', 'baseline',
                '-level', '3.0',
                '-movflags', '+faststart',
                '-b:v', '800k', // Set the video bitrate (adjust as needed to meet the file size requirement)
            ]);
            $video->filters()->clip(\FFMpeg\Coordinate\TimeCode::fromSeconds(0), TimeCode::fromSeconds(50));

            $video->save($format, 'Uploads/ffmpeg-'. $uniqueFileName);

            $videoUrl2 = url('Uploads/ffmpeg-'. $uniqueFileName);

            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl2, $caption);
            sleep(10);
            $result = $apiService->graphAPIPostVideoAsReel($videoID);
            if($result) {
                return response()->json(['success' => 'Video was successfully uploaded as a InstagramService reel.']);
            }
            else
                return response()->json(['error' => 'Video doesnt meet InstagramService reel requirements.']);
        }
        return response()->json(['error' => 'An error occurred while uploading video as reel to InstagramService']);
    }
}
