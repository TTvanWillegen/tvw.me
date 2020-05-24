<?php
require_once("uploader.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-type: application/json');

    if (!Uploader::validateAccess($_SERVER['HTTP_API_KEY'])) {
        die(json_encode(
            array(
                "success" => false,
                "error" => "forbidden",
                "key_used" => $_SERVER['HTTP_API_KEY']
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test</title>

    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
    <link rel="manifest" href="../manifest.webmanifest?v=2">
    <meta name="msapplication-TileColor" content="#003e07">
    <meta name="theme-color" content="#003e07">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script type="application/javascript">
        window.addEventListener('load', () => {
            if (navigator.standalone) {
                // console.log('Launched: Installed (iOS)');
            } else if (matchMedia('(display-mode: standalone)').matches) {
                // console.log('Launched: Installed');
            } else {
                // console.log('Launched: Browser Tab');
            }
        });
    </script>
    <script type="application/javascript" src="idb.js"></script>
</head>
<body>
<label>
    API Key:
    <input id="apiKeyInput" type="text"/>
</label>
<button onclick="storeApiKey('API_KEY', document.getElementById('apiKeyInput').value)"> click me!</button>
<div id="apiKeyUsed"></div>
<p>
<form action="./" method="post">
    <label for="w3mission">Type text:</label>
    <textarea id="w3mission" name="input" rows="4" cols="50"></textarea>
    <button type="submit">Submit!</button>
</form>
</p>

<script type="application/javascript">
    function storeApiKey(key, value) {
        getIdb().then(function (db) {
                var tx = db.transaction('keys', 'readwrite');
                var store = tx.objectStore('keys');
                var item = {
                    name: key,
                    value: value
                }
                store.put(item);
                return tx.complete;
            }
        ).catch(function (e) {
            tx.abort();
            console.log(e);
        }).then(function () {
            alert('API key stored successfully!');
        });
    }

    idbPromise = null;

    function getIdb() {
        if (idbPromise == null) {
            idbPromise = idb.open('tvw.me', 2, function (upgradeDb) {
                switch (upgradeDb.oldVersion) {
                    case 0:
                    // a placeholder case so that the switch block will
                    // execute when the database is first created
                    // (oldVersion is 0)
                    case 1:
                        console.log('Creating the keys object store');
                        upgradeDb.createObjectStore('keys', {keyPath: 'name', unique: true});
                }
            });
        }
        return idbPromise;
    }

    function findValue(key) {
        return getIdb().then(function (db) {
            var tx = db.transaction('keys', 'readonly');
            var store = tx.objectStore('keys');
            return store.get(key);
        });
    }
</script>

<script type="application/javascript">
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('fetchhandler-sw.js')
                .then(function (registration) {
                })
                .catch(function (error) {
                    console.log('Service worker registration failed, error:', error);
                });

            let apiKey = "";
            findValue("API_KEY").then(function (object) {
                if (!object) {
                    return;
                }

                apiKey += '<h2>' + object.name + '</h2><p>';
                for (var field in object) {
                    apiKey += field + ' = ' + object[field] + '<br/>';
                }
                apiKey += '</p>';

            }).then(function () {
                if (apiKey === '') {
                    apiKey = '<p>No results.</p>';
                }
                document.getElementById('apiKeyUsed').innerHTML = apiKey;
            });
        });
    }
</script>
</body>
</html>
