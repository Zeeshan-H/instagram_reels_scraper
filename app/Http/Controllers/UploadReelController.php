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

            $ffmpegCommand = "ffmpeg -i \"$outputPath\" -c:v libx264 -preset slow -crf 22 -c:a aac -b:a 192k -movflags +faststart -y Uploads/ffmpeg-\"$uniqueFileName\"";


            exec($ffmpegCommand, $output, $returnCode);

            if ($returnCode === 0) {
                // Successfully executed FFmpeg command
                echo "Video converted and saved successfully.";

                $videoUrl2 = url('Uploads/ffmpeg-'. $uniqueFileName);
                echo "URL is ". $videoUrl2;

                $apiService = new APIService();
                $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl2, $caption);
                sleep(10);
                $result = $apiService->graphAPIPostVideoAsReel($videoID);
                if($result) {
                    return response()->json(['success' => 'Video was successfully uploaded as a InstagramService reel.']);
                }
                else
                    return response()->json(['error' => 'Video doesnt meet InstagramService reel requirements.']);
            } else {
                // Failed to execute FFmpeg command
                echo "Error while converting video.";
            }


        }
        return response()->json(['error' => 'An error occurred while uploading video as reel to InstagramService']);
    }
}
