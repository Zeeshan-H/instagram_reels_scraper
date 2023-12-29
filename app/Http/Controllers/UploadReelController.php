<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadReelController extends Controller
{
    public function saveReelToHeroku(Request $request)
    {
        $request->validate([
            'video' => 'max:10240', // Assuming a maximum file size of 10 MB
        ]);

        $file = $request->file('video');
//        dd($file);
//        dd($file->getFilename());
        $uniqueFileName = uniqid() . '-' . $file->getClientOriginalName();
        if($file->move('Uploads', $uniqueFileName))
        {
            $fileUrl = url('Uploads/' . $uniqueFileName);

            // Print the URL or use it as needed
            dd('Upload Success. File URL: ' . $fileUrl);
        }
        else
        dd("Not saved");
    }
}
