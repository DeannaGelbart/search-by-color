*2025 note: This is a decade old project that was used by visitors at an art show in 2016! Tech is out of date, if I revive it a lot will need to change.*

This webapp lets the user search a collection of images by color, using a color picker. Search results are shown as a thumbnail gallery. The user can select an individual image for a larger view.

![zyaHYq9](https://github.com/user-attachments/assets/61134851-d1c8-41bb-a84d-79ce62176427)

The UI is a single-page app with a jQuery front end and a Zend Framework 2 back end.

The back end is normally self contained while running without any API dependencies, but the search index, which is a CSV file listing the dominant colors in each image, needs to be built ahead of time using this [TinEye-based command line tool](https://github.com/dgelbart/colorcoordinator-zf2/blob/master/module/Application/src/Application/Controller/ConsoleController.php).   There is also the option using the TinEye API to handle the user's searches instead of using the PHP search implementation inside this project. Using TinEye is not the default but there is an alternative controller (TinEyeSearchController) in the project that will do this.

The images to be searched must be placed under public/img/art and thumbnails must be placed under public/img/art/thumbs with the same filenames as the corresponding full size images. 

Running the project
-------------------

Composer and web server setup are the same as in the installation instructions for the [Zend Skeleton Application] (https://github.com/zendframework/ZendSkeletonApplication), which was the starting point for this app.

The colorpicker UI library can be installed using Composer.

The [TinEye PHP  client library](https://services.tineye.com/developers/multicolorengine/libraries.html) must be placed in the vendor directory manually.


