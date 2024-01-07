<?php

namespace App\Console\Commands;

use App\API\APIService;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Instagram\Api;

class PostInstagramReels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:insta-reels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = "https://scontent-iad3-2.cdninstagram.com/o1/v/t16/f1/m69/GICWmADTpOUy-_ADALuZg0YKaexnbpR1AAAF.mp4?efg=eyJxZV9ncm91cHMiOiJbXCJpZ193ZWJfZGVsaXZlcnlfdnRzX290ZlwiXSIsInZlbmNvZGVfdGFnIjoidnRzX3ZvZF91cmxnZW4uY2xpcHMuYzIuMTA4MC5oaWdoIn0&_nc_ht=scontent-iad3-2.cdninstagram.com&_nc_cat=100&vs=620160590098422_1292736953&_nc_vs=HBksFQIYOnBhc3N0aHJvdWdoX2V2ZXJzdG9yZS9HSUNXbUFEVHBPVXktX0FEQUx1WmcwWUthZXhuYnBSMUFBQUYVAALIAQAVAhg6cGFzc3Rocm91Z2hfZXZlcnN0b3JlL0dJTDJzQW5LOWpVQk1CUVlBRmhRT2ZTcGRsODNicFIxQUFBRhUCAsgBACgAGAAbABUAACay7Oj%2BrLz%2FPxUCKAJDMywXQD8zMzMzMzMYEmRhc2hfaGlnaF8xMDgwcF92MREAdf4HAA%3D%3D&ccb=9-4&oh=00_AfCwh2GJcKB33jrBAl-xJV9m7deVxafl5zWDJiwKGIXx9A&oe=659DEBE1&_nc_sid=c024bc";
        $uploadsDirectory = public_path('Reels');
        $uniqueFileName = uniqid() . '-downloaded-video.mp4';
        $outputPath = "$uploadsDirectory/$uniqueFileName";

        // Download the MP4 video
        $fileContent = file_get_contents($url);

        // Save the content to a file
        file_put_contents($outputPath, $fileContent);

        echo "Video downloaded and saved successfully.";

        // Compress the video using ffmpeg command
        $ffmpegCommand = "ffmpeg -i \"$outputPath\" " .
            "-c:v libx264 -aspect 9:16 -crf 28 " .  // Adjusted CRF for higher compression
            "-vf \"scale=iw*min(720/iw\\,1280/ih):ih*min(720/iw\\,1280/ih),pad=720:1280:(720-iw)/2:(1280-ih)/2\" " .
            "-maxrate 500k -bufsize 500k -preset ultrafast " .  // Adjusted maxrate and bufsize for lower bitrate
            "-c:a aac -b:a 64k -ac 1 -pix_fmt yuv420p -movflags +faststart -b:v 150k -t 59 -y \"$uploadsDirectory/ffmpeg-$uniqueFileName\"";


        // Execute ffmpeg command
        exec($ffmpegCommand, $output, $returnCode);

        if ($returnCode === 0) {
            // Successfully executed FFmpeg command
            echo "Video converted and saved successfully.";

            $videoUrl = url('Reels/ffmpeg-' . $uniqueFileName);
            dd($videoUrl);

            $apiService = new APIService();
            $videoID = $apiService->graphAPIPostVideoToGetID($videoUrl, "Test");
            sleep(10);
            $result = $apiService->graphAPIPostVideoAsReel($videoID);

            if ($result) {
                echo "Video was successfully uploaded as an Instagram reel";
            } else {
                echo "Video doesn't meet Instagram reel requirements.";
            }
        } else {
            // Failed to execute FFmpeg command
            echo "Error while converting video.";
        }
    }

}
