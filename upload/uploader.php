<?php
require_once("settings.php");

class Uploader
{
    private const BASE_FOLDER = "../";

    public static function validateAccess($apiKey)
    {
        return !empty($apiKey) && in_array($apiKey, Settings::API_KEYS);
    }

    private static function getRandomName()
    {
        return substr(md5(uniqid(mt_rand(), true)), 0, 5);
    }

    private static function isImageFile($tempFileName)
    {
        return exif_imagetype($tempFileName);
    }

    private static function isUrl($text)
    {
        return filter_var($text, FILTER_VALIDATE_URL);
    }

    public static function uploadFile($file)
    {
        if (empty($file)) {
            return false;
        }

        $randomName = Uploader::getRandomName();
        $fileName = $file['name'];
        $tempFileName = $file['tmp_name'];

        if (Uploader::isImageFile($tempFileName)) {
            $prefix = "i";
            $php = '
                <html>
                    <head>
                        <title>tvw.me | upload, shorten, share</title>
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                    </head>
                    <body style="background: #2b2b2b;">
                        <img src="%1$s" alt="%2$s"/>
                    </body>
                </html>';
        } else {
            $prefix = "f";
            $php = '
                <?php
                header(\'Content-Type: application/octet-stream\'); 
                header(\'Content-Transfer-Encoding: Binary\'); 
                header(\'Content-disposition: attachment; filename="%2$s"\'); 
                readfile(\'%1$s\'); 
            ';
        }

        $randomName = $prefix . $randomName;
        mkdir(self::BASE_FOLDER . $randomName);
        move_uploaded_file($tempFileName, self::BASE_FOLDER . $randomName . DIRECTORY_SEPARATOR . $randomName);
        $file = fopen(self::BASE_FOLDER . $randomName . DIRECTORY_SEPARATOR . "index.php", "w");
        fwrite($file, sprintf($php, $randomName, $fileName));
        fclose($file);
        return "https://tvw.me/" . $randomName;
    }

    public static function uploadText($text)
    {
        if (empty($text)) {
            return false;
        }
        $randomName = Uploader::getRandomName();
        $prefix = "";
        $php = "";

        if (Uploader::isUrl($text)) {
            $prefix = "u";
            $php = '<?php header("Location: %1$s");';
        } else {
            $prefix = "t";
            $php = '
                <html>
                    <head>
                        <title>tvw.me | upload, shorten, share</title>
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <link rel="stylesheet"
                            href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.17.1/build/styles/darcula.min.css">
                        <script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.17.1/build/highlight.min.js"></script>
                    </head>
                    <body style="background: #2b2b2b;">
                        <pre><code>%1$s</code></pre>
                        <script>hljs.initHighlightingOnLoad();</script>
                    </body>
                </html>';
            $text = htmlentities($text);
        }
        $randomName = $prefix . $randomName;
        mkdir(self::BASE_FOLDER . $randomName);
        $file = fopen(self::BASE_FOLDER . $randomName . "/index.php", "w");
        fwrite($file, sprintf($php, $text));
        fclose($file);
        return "https://tvw.me/" . $randomName;
    }
}
