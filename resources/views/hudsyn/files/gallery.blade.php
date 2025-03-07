<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Image Gallery</title>
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
        }
        .gallery-item {
            margin: 10px;
            text-align: center;
        }
        .gallery-item img {
            max-width: 150px;
            max-height: 150px;
            display: block;
            cursor: pointer;
            margin-bottom: 5px;
        }
        .gallery-item a {
            text-decoration: none;
            color: blue;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Image Gallery</h1>
    <div class="gallery">
        @foreach($images as $image)
            <div class="gallery-item">
                <img src="{{ asset($image->file_path) }}" alt="{{ $image->original_name }}" onclick="copyAndClose('{{ asset($image->file_path) }}')">
                <a onclick="copyAndClose('{{ asset($image->file_path) }}')">{{ asset($image->file_path) }}</a>
            </div>
        @endforeach
    </div>
    <script>
        function copyAndClose(url) {
            if (navigator.clipboard && window.isSecureContext) {
                // Modern clipboard API method
                navigator.clipboard.writeText(url).then(function() {
                    alert('Copied to clipboard');
                    window.close();
                }, function(err) {
                    alert('Error copying to clipboard: ' + err);
                });
            } else {
                // Fallback method for older browsers
                var textArea = document.createElement("textarea");
                textArea.value = url;
                textArea.style.position = "fixed"; // Prevent scrolling to bottom
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('Copied to clipboard');
                    window.close();
                } catch (err) {
                    alert('Unable to copy');
                }
                document.body.removeChild(textArea);
            }
        }
    </script>
</body>
</html>
