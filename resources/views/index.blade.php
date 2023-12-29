<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>
<body>

<h3 class="p-4">Welcome to Social Media Video Scraper</h3>


{{--<div class="embedsocial-hashtag" data-ref="27d967cab2282d3b4e7cfbe23bc20e0de9f05209"> <a class="feed-powered-by-es feed-powered-by-es-feed-img" href="https://embedsocial.com/social-media-aggregator/" target="_blank" title="Instagram widget"> <img src="https://embedsocial.com/cdn/images/embedsocial-icon.png" alt="EmbedSocial"> Instagram widget </a> </div>--}}
<div id="uploadReels">

    <div class="mb-3 p-3">
        <form method="post" action="{{ route('upload.heroku') }}" enctype="multipart/form-data">
            @csrf
        <label for="formFile" class="form-label">Upload Video to Instagram Reel</label>
        <input class="form-control" type="file" id="formFile" name="video" accept="video/mp4" required>
        <input type="text" class="form-control mt-2 mb-2" id="caption" name="caption" placeholder="Enter the caption..." required/>
        <input type="submit" value="Upload" class="btn btn-primary">
        </form>
    </div>
</div>

<div class="container">

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @if(session()->has('error'))
       <div class="alert alert-danger">
            {{ session()->get('error') }}
       </div>
    @endif

    @foreach($data as $record)
    <div class="row">
        <div class="col">
           <div class="card">
               <div class="card-content">
{{--                   <img src="{{ $record['displayUrl'] }}" height="300" />--}}
                   <blockquote class="instagram-media" data-instgrm-permalink="{{ $record['url'] }}" data-instgrm-version="13"></blockquote>
                   <script async src="https://www.instagram.com/embed.js"></script>
                   <h3>Download URL: <a href="{{ $record['videoUrl'] }}">Here</a></h3>
               </div>
           </div>
        </div>

    </div>
    @endforeach
</div>

@section("script")
    <script async src="https://www.instagram.com/embed.js"></script>


@show

</body>
</html>
