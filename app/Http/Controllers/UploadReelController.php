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
        $uniqueFileName2 = "ffmpeg-" . $uniqueFileName;
        $outputPath = public_path('Uploads/' . $uniqueFileName);

        if($file->move('Uploads', $uniqueFileName))
        {


            $ffmpegCommand = "ffmpeg -i \"$outputPath\" -c:v libx264 -profile:v high -level 4.2 -pix_fmt yuv420p -movflags +faststart -c:a aac -b:a 128k -ar 44100 -strict -2 -r 30 -s 640x1136 -t 15 -b:v 1500k -maxrate 1500k -bufsize 10240k -preset slow -crf 22 -f mp4 -y Uploads/\"$uniqueFileName2\"";

            exec($ffmpegCommand, $output, $returnCode);

            if ($returnCode === 0) {
                $videoUrl1 = url('Uploads/' . $uniqueFileName2);
                $apiService = new APIService();
                $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl1, $caption);
                sleep(10);
                $result = $apiService->graphAPIPostVideoAsReel($videoID);
                if($result) {
                    return response()->json(['success' => 'Video was successfully uploaded as a InstagramService reel.']);
                }
                else
                    return response()->json(['error' => 'Video doesnt meet InstagramService reel requirements.']);
                // Successfully executed FFmpeg command
                echo "Video compressed and saved successfully.";
            } else {
                // Failed to execute FFmpeg command
                echo "Error while compressing video.";
            }


        }
        return response()->json(['error' => 'An error occurred while uploading video as reel to InstagramService']);
    }
}
