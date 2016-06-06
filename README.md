
Installation
------------

Follow the installation instructions from https://github.com/zendframework/ZendSkeletonApplication

Then install the colorpicker:
```
$ php composer.phar require mjolnic/bootstrap-colorpicker
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/js/* public/js/
$ cp  vendor/mjolnic/bootstrap-colorpicker/dist/css/* public/css/
$ cp -r vendor/mjolnic/bootstrap-colorpicker/dist/img/bootstrap-colorpicker public/img/
```

The searchable art must be placed under public/img/art/

The thumbnails must be placed under public/img/art/thumbs. They can be created using ImageMagick:
```
$ cd public/img/art
$ mkdir thumbs
$ mogrify -resize 200x200 -background '#eeeeee' -gravity center -extent 200x200 -format jpg -quality 75 -path thumbs *.jpg *.JPG *.jpeg *.JPEG
```






