This webapp lets the user search a collection of images by color, using a color picker. Search results are shown as a thumbnail gallery and the user can select an individual image for a larger view.

[Screenshot](http://i.imgur.com/zyaHYq9.png)

[Try your own searches with a running installation of this webapp](http://www.colorcoordinator.focalfilter.com)

The user interface is a single-page app ([html](https://github.com/dgelbart/colorcoordinator-zf2/blob/master/module/Application/view/application/index/index.phtml), [js](https://github.com/dgelbart/colorcoordinator-zf2/blob/master/public/js/colorcoordinator.js)) built using jQuery and Bootstrap. 

The UI performs searches using a [JSON web service built as a Zend Framework 2 controller](https://github.com/dgelbart/colorcoordinator-zf2/blob/master/module/Application/src/Application/Controller/SearchController.php), using [this collection of color handling logic](https://github.com/dgelbart/colorcoordinator-zf2/blob/master/module/Application/src/Application/Service/ImageService.php).

The search index is created ahead of time using a [command line tool built as a ZF2 console controller](https://github.com/dgelbart/colorcoordinator-zf2/blob/master/module/Application/src/Application/Controller/ConsoleController.php) 

PHPUnit tests for the color handling logic are [here](https://github.com/dgelbart/colorcoordinator-zf2/blob/master/module/Application/test/ApplicationTest/Service/ImageServiceTest.php). Better test coverage for the controllers is coming soon. 

The JSON web service that performs the user's searches doesn't rely on anything external, but the above-mentioned command line tool uses the TinEye color extraction API to create the search index, which is basically a list of the dominant colors in each image. That tool only needs to be used once, to build the search index when you first set up this webapp.

Customizing this project for your needs
---------------------------------------

Customize the UI text by editing module/Application/view/application/index/index.phtml.

The images to be searched must be placed under public/img/art/ 

Thumbnails must be placed under public/img/art/thumbs, with the same filenames as the full size images. The thumbnails can be created using ImageMagick:
```
$ cd public/img/art
$ mkdir thumbs
$ mogrify -resize 200x200 -background '#eeeeee' -gravity center -extent 200x200 -format jpg -quality 75 -path thumbs *.jpg *.JPG
```

The score and distance thresholds passed to scoreImageSet() need to be tuned for your data set. 

This webapp assumes both the original image files and the thumbnails have a .jpg suffix. 

This webapp uses its own search code, but it relies on TinEye to extract the dominant colors from the images ahead of time. There is a command line console tool in this project to do that (ConsoleController). You must have a TinEye API subscription to use it. Rename config/autoload/local.php.dist to config/autoload/local.php, and place your TinEye API username and password there. (If you are using IP address based authentication for the API, just modify TinEyeService.php to not pass the username and password.) You can then extract the colors with the following command. This data is stored in a CSV file in the ZF2 data folder.
```
for i in public/img/art/thumbs/*jpg ;  do php public/index.php console extract-colors $i >> data/extracted-colors.csv; done
```

You also have the option of using TinEye to handle the user's searches. TinEye's search code is probably more powerful than ours. Using TinEye is not the default but there is already a controller (TinEyeSearchController) in the project that will do this. You have to change public/js/colorcoordinator.js to call /tin-eye-search instead of /search.  And you'll need to put the images in the TinEye index which you can do like this:
```
$ cd public/img/art/thumbs
$ for f in *jpg *JPG ; do curl http://USERNAME:PASSWORD@multicolorengine.tineye.com/USERNAME/rest/add/ -F "image=@$f;filename=$f" ; done
```

Getting the project code running on your server
-----------------------------------------------

Composer and web server setup are the same as in the installation instructions for the [Zend Skeleton Application] (https://github.com/zendframework/ZendSkeletonApplication), which was the starting point for this app.

After you've followed those, you can install the colorpicker UI library:
```
$ php composer.phar require mjolnic/bootstrap-colorpicker
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/js/* public/js/
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/css/* public/css/
$ cp -r vendor/mjolnic/bootstrap-colorpicker/dist/img/bootstrap-colorpicker public/img/
```

You then have to place the [TinEye PHP  client library](https://services.tineye.com/developers/multicolorengine/libraries.html) in vendor/tineyeservices_php. To meet ZF2 autoloader conventions, rename the filenames to match their class name. For example, rename metadata_request.php to MetadataRequest.php since the class name is MetadataRequest. And add "namespace TinEye;" at the beginning of each file.

Legal
-----

TinEye is owned by Idée Inc. and any uses of the TinEye API through the colorcoordinator-zf2 project must respect Idée's terms of service.

All third-party code used in the colorcoordinator-zf2 project is under the license terms of the copyright holders.

The code written specifically for the colorcoordinator-zf2 project is copyright David Gelbart and can be used under the MIT License. 






