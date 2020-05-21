<?php
require_once("uploader.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-type: application/json');

    if (!Uploader::validateAccess($_SERVER['HTTP_API_KEY'])) {
        die(json_encode(
            array(
                "success" => false,
                "error" => "forbidden"
            )
        ));
    }
    $tvwUrl = false;
    if (!empty($_FILES['file'])) {
        $tvwUrl = Uploader::uploadFile($_FILES['file'], $_FILES['file']['tmp_name']);
    }
    if (!empty($_POST['input'])) {
        $tvwUrl = Uploader::uploadText($_POST['input']);
    }

    if ($tvwUrl) {
        die(json_encode(array("success" => true, "url" => $tvwUrl)));
    } else {
        die(json_encode(array("success" => false, "error" => "Something went wrong.")));
    }
    die();
}
?>
<html>
<head>
    <title>Test</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/upload/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/upload/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/upload/favicon-16x16.png">
    <link rel="manifest" href="/upload/manifest.webmanifest?v=2">
    <meta name="msapplication-TileColor" content="#003e07">
    <meta name="theme-color" content="#003e07">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script type="application/javascript">
        window.addEventListener('load', () => {
            if (navigator.standalone) {
                console.log('Launched: Installed (iOS)');
            } else if (matchMedia('(display-mode: standalone)').matches) {
                console.log('Launched: Installed');
            } else {
                console.log('Launched: Browser Tab');
            }
        });
    </script>
</head>
<body>
Test
<button onclick="copy(location.href)">Copy url</button>
<script type="application/javascript">
    async function copy(text) {
        try {
            await navigator.clipboard.writeText(text);
        } catch (err) {
        }
    }
</script>
<script type="module">
    import 'https://cdn.jsdelivr.net/npm/@pwabuilder/pwaupdate';

    const el = document.createElement('pwa-update');
    document.body.appendChild(el);
</script>
</body>
</html>
