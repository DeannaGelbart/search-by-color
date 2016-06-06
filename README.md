
Installation
------------

This project was started from the Zend Skeleton Application so start by following the installation instructors for that: https://github.com/zendframework/ZendSkeletonApplication

If you are using TinEye based search then you have to place the [TinEye PHP  client library](https://services.tineye.com/developers/multicolorengine/libraries.html) in vendor/tineyeservices_php. To meet ZF2 autoloader conventions, rename the filenames to match their class name. For example, rename metadata_request.php to MetadataRequest.php since the class name is MetadataRequest. And add "namespace TinEye;" at the beginning of each file.

You also have to rename config/autoload/local.php.dist to config/autoload/local.php. If you are using TinEye based search, place your TinEye API username and password there.

Then install the colorpicker:
```
$ php composer.phar require mjolnic/bootstrap-colorpicker
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/js/* public/js/
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/css/* public/css/
$ cp -r vendor/mjolnic/bootstrap-colorpicker/dist/img/bootstrap-colorpicker public/img/
```

The art images must be placed under public/img/art/

The thumbnails must be placed under public/img/art/thumbs, with the same filenames as the full size images. The thumbnails can be created using ImageMagick:
```
$ cd public/img/art
$ mkdir thumbs
$ mogrify -resize 200x200 -background '#eeeeee' -gravity center -extent 200x200 -format jpg -quality 75 -path thumbs *.jpg *.JPG *.jpeg *.JPEG
```






