<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->page_name ?? 'Privacy Policy' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        p {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>{{ $page->page_name ?? 'Privacy Policy' }}</h1>
    {!! $page->description ?? '' !!}
</body>
</html>