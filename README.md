# Tvw.me - Upload, Shorten, Share

Tvw.me is a self-hosted website to make sharing text, code, images, urls, or any other files a lot easier.
It works great in unison with [ShareX](https://getsharex.com/).

## What does it do exactly?
You can send either a json file or binary data to the site.
Upon receival, the site will generate a 6 character long path, and sends this back to the requester.
These links are prefixed to hint to the reader what will happen when going to the link.
This link will do one of the following things:
- Text: Displays the text on the website [prefixed with **t**]
- Code: Highlights code using [highlight.js](https://highlightjs.org/). Automatically tries to find the correct language. [prefixed with **t**]
- URL: When sent an URL, this site effectively shortens the URL and upon clicking the link, redirects to the original URL. [prefixed with **u**]
- Image: Shows the image inline on the page.
- Any other file: Starts a download of the file.

## That sounds nice, can I use tvw.me?
No. Well, not the domain tvw.me. Tvw.me actually also supports the usage of API keys. 
You CAN however, place the PHP files in this repository on your own server.
Also create a `upload/settings.php` file containing the following:
```php
<?php


class Settings
{
    public const API_KEYS = array("{YOUR API KEYS}");
}
```
Naturally, replace `Your API keys` with any keys you'd like to use, simply generate a long random text, as you only have to set them up once.

## Okay, I'll do that, and then?
I use ShareX together with this service, you can set it up as follows.
In ShareX, go to Destinations > Custom uploader settings.
In here, you should create 2 uploaders, one for text uploads (text, code and URLs) and one for files (images and files).
Also make sure to select the correct uploaders in the left bottom corner; so:
- Image uploader > Your File Uploader
- Text uploader > Your Text Uploader
- File uploader > Your File Uploader
- URL shortener > Your Text Uploader
- URL sharing service > Your Text Uploader

### The text uploader
Under Destination type, select `Text uploader` and `URL shortener`.
Under Method, select `POST`.
Under Request URL, type `{YOUR DOMAIN}/upload/index.php`.
Under Body, select `Form URL encoded`.
Under Body>Name type `input`, under Body>Value type `{input}`
Under Headers>Name type `API_KEY`, under Headers>Value type the API key you set up.
Under URL, type `{json:url}`

### The file uploader
Under Destination type, select `Image uploader` and `File uploader`.
Under Method, select `POST`.
Under Request URL, type `{YOUR DOMAIN}/upload/index.php`.
Under Body, select `Form data (multipart/form-data)`.
Under Headers>Name type `API_KEY`, under Headers>Value type the API key you set up.
Under URL, type `{json:url}`

## Finalizing
If you have done that, you should make two more changes to make it even easier to upload; set the shortcuts and automatically copy the url to clipboard.

### Set up the shortcuts
Easiest is to override the default `Prnt Scrn` behaviour, and add a shortcut `CTRL + SHIFT + C` to upload. This way, if you copy something, you can immediately upload it by also pressing the `shift` key.

You can set up your shortcuts under Hotkey settings

#### Override default Print Screen behaviour
Add a new hotkey.
Under Task > Task, select `Capture region`. 
Then close the window and change the hotkey from `None` to `Print Screen` by clicking the button and pressing the print screen button.

#### Add upload behaviour
Add a new hotkey.
Under Task > Task, select `Upload from clipboard`. 
Then close the window and change the hotkey from `None` to `CTRL + SHIFT + C` by clicking the button and pressing the respective buttons.
Now it will upload, but to the wrong destinations.
To fix this, right click the tray icon, select Destinations and change them all to the Custom Uploaders / Shorteners.

### Copy to clipboard
Right click the tray icon. Select `After capture tasks` > `Copy image to clipboard` and at `After upload tasks` > `Copy URL to clipboard`.
