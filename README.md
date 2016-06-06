This webapp lets a user search a collection of images by color.

You can see it in action at: ...

Screenshot: ...

Behind the scenes this app makes heavy use of the [TinEye MulticolorEngine](https://services.tineye.com/MulticolorEngine) API.

Installation
------------

Composer and web server setup are the same as in the installation instructions for the [Zend Skeleton Application] (https://github.com/zendframework/ZendSkeletonApplication).

After you've followed those, you can install the colorpicker UI library:
```
$ php composer.phar require mjolnic/bootstrap-colorpicker
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/js/* public/js/
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/css/* public/css/
$ cp -r vendor/mjolnic/bootstrap-colorpicker/dist/img/bootstrap-colorpicker public/img/
```

If you are using TinEye based search then you have to place the [TinEye PHP  client library](https://services.tineye.com/developers/multicolorengine/libraries.html) in vendor/tineyeservices_php. To meet ZF2 autoloader conventions, rename the filenames to match their class name. For example, rename metadata_request.php to MetadataRequest.php since the class name is MetadataRequest. And add "namespace TinEye;" at the beginning of each file.

Customization
-------------

You have to rename config/autoload/local.php.dist to config/autoload/local.php. If you are using TinEye based search, place your TinEye API username and password there.

You can customize the UI text by editing module/Application/view/application/index/index.phtml.

The art images must be placed under public/img/art/ 

The thumbnails must be placed under public/img/art/thumbs, with the same filenames as the full size images. The thumbnails can be created using ImageMagick:
```
$ cd public/img/art
$ mkdir thumbs
$ mogrify -resize 200x200 -background '#eeeeee' -gravity center -extent 200x200 -format jpg -quality 75 -path thumbs *.jpg *.JPG *.jpeg *.JPEG
```

You can add images to the TinEye search index as follows
```
$ cd public/img/art
$ for f in *jpg *JPG  *jpeg ; do curl http://USERNAME:PASSWORD@multicolorengine.tineye.com/USERNAME/rest/add/ -F "image=@$f;filename=$f" ; done
```








