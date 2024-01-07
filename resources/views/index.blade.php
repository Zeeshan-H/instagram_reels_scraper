<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Social Media Video Scraper</title>
    <style>
        .blue-text {
            color: blue;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="
    https://cdn.jsdelivr.net/npm/sweetalert2@11.10.2/dist/sweetalert2.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>
<body>

<h3 class="p-4">Welcome to Social Media Video Scraper</h3>


{{--<div class="embedsocial-hashtag" data-ref="27d967cab2282d3b4e7cfbe23bc20e0de9f05209"> <a class="feed-powered-by-es feed-powered-by-es-feed-img" href="https://embedsocial.com/social-media-aggregator/" target="_blank" title="InstagramService widget"> <img src="https://embedsocial.com/cdn/images/embedsocial-icon.png" alt="EmbedSocial"> InstagramService widget </a> </div>--}}
<div id="uploadReels">

    <div class="mb-3 p-3">
        <form method="post" id="form-upload" enctype="multipart/form-data">
            @csrf
            <label for="formFile" class="form-label">Upload Video to Instagram Reel</label>
            <input class="form-control" type="file" id="formFile" name="video" accept="video/mp4" required onchange="handleInput2(this)">
            <input type="text" class="form-control mt-2 mb-2" id="caption" name="caption" placeholder="Enter the caption..."
                   oninput="handleInput(this)" required/>
            <div id="displayText" class="d-none"></div>

            <button type="button" onclick="uploadVideo()" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>

<div class="container">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


{{--    @foreach($data as $record)--}}
{{--        <div class="row">--}}
{{--            <div class="col">--}}
{{--                <div class="card">--}}
{{--                    <div class="card-content">--}}
{{--                        --}}{{--                   <img src="{{ $record['displayUrl'] }}" height="300" />--}}
{{--                        <blockquote class="instagram-media" data-instgrm-permalink="{{ $record['url'] }}" data-instgrm-version="13"></blockquote>--}}
{{--                        <script async src="https://www.instagram.com/embed.js"></script>--}}
{{--                        <strong class="caption p-2">{{ strtok($record['caption'], '.') }} <br /><br />--}}
{{--                            &nbsp; &nbsp; #babybara #capybara #capybaras #capy #capybaralove #capybaralife--}}
{{--                        </strong>--}}
{{--                        <h3>Download URL: <a href="{{ $record['videoUrl'] }}">Here</a></h3>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    @endforeach--}}

        @foreach($data as $reel)
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-content">
                            {{--                   <img src="{{ $record['displayUrl'] }}" height="300" />--}}
                            <blockquote class="instagram-media" data-instgrm-permalink="{{ $reel['link'] }}" data-instgrm-version="13"></blockquote>
                            <script async src="https://www.instagram.com/embed.js"></script>
                            <strong class="caption p-2">{{ $reel['caption'] }}
                            </strong>
                            <h3>Download URL: <a target="_blank" href="{{ $reel['videoUrl'] }}">Here</a></h3>
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
</div>

@section("script")
    <script async src="https://www.instagram.com/embed.js"></script>


    <script type="text/javascript">
        let textWithMentions = '';
        var fileInput = document.getElementById('formFile');

        function handleInput(inputElement) {
            // console.log(text);
            let text = inputElement.value;
            const displayText = document.getElementById('displayText');

            // Process hashtags and mentions and change color
            const coloredText = text.replace(/(#\w+|@\w+)/g, '<span class="blue-text">$1</span>');


            // Set the processed text in the display area
            displayText.innerHTML = coloredText;

            // Set the processed text as the value of the input field
            inputElement.setAttribute('value', text);

            displayText.classList.toggle('d-none', !(/#|@/.test(text)));



        }

        function handleInput2(fileInput) {
            const maxSizeMB = 50; // Maximum allowed size in megabytes
            const maxSizeBytes = maxSizeMB * 1024 * 1024; // Convert to bytes
            const fileSize = fileInput.files[0]?.size;

            if (fileSize && fileSize > maxSizeBytes) {
                // Show a SweetAlert indicating that the file size is too large
                Swal.fire({
                    title: "File Size Too Large",
                    text: `The uploaded file is too large. Please upload a file smaller than ${maxSizeMB} MB.`,
                    icon: "error",
                    confirmButtonText: "OK"
                });

                // Reset the file input to clear the selected file
                fileInput.value = "";
            }
        }

        function uploadVideo()
        {
            const form = document.getElementById('form-upload');
            const formData = new FormData(form);

            if(fileInput.files.length === 0) {
                alert('Please upload a video');
            }


            showLoadingDialog();


            fetch("{{ route('upload.heroku') }}", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: "Success!",
                            text: data.success,
                            icon: "success",
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            allowOutsideClick: false
                        });
                    }
                    else {
                        Swal.fire({
                            title: "Error",
                            text: data.error,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    console.log(error);
                        Swal.fire({
                            title: "Error",
                            text: 'Error occured while uploading video as reel to InstagramService',
                            icon: "error",
                            confirmButtonText: "OK"
                        });

            });
        }

        function showLoadingDialog(event) {
            // event.preventDefault();

            Swal.fire({
                title: "Uploading...",
                text: "Please wait...",
                icon: "waiting",
                showConfirmButton: false,
                allowEscapeKey: false,
                allowOutsideClick: false
            });
        }

    </script>


@show

</body>
</html>
