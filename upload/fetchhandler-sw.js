importScripts("idb.js");

let idbPromise = null;

self.addEventListener('fetch', event => {

    // if (event.request.method !== 'POST') {
    //     event.respondWith(fetch(event.request));
    //     return;
    // }

    event.respondWith(
        findValue("API_KEY").then(function (result) {
            if(result === undefined){
                return fetch(event.request);
            }
            let h = new Headers(event.request.headers);
            h.append("API_KEY", result['value']);

            let request = new Request(event.request, {
                headers: h
            })

            console.log(request);
            request.headers.forEach((xk,xv) => console.log(xk + ": " + xv));
            return fetch(request);
        }));
});

self.addEventListener('install', function (e) {
    e.waitUntil(
        caches.open('tvw.me').then(function (cache) {
            return cache.addAll([
                '/upload/',
                '/upload/index.php',
                '/upload/uploader.php',
                '/upload/settings.php'
            ]);
        })
    );
});

self.addEventListener('activate', event => {
    getIdb();
});

function findValue(key) {
    return getIdb().then(function (db) {
        var tx = db.transaction('keys', 'readonly');
        var store = tx.objectStore('keys');
        return store.get(key);
    });
}

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

function openShare(url, password = null) {
    let text = url;
    if (password) {
        text = text + ", password: " + password;
    }

    if (navigator.share) {
        navigator.share({
            title: 'Uploaded to tvw.me',
            text: text,
        })
            .then(() => console.log('Successful share'))
            .catch((error) => console.log('Error sharing', error));
    }
}

