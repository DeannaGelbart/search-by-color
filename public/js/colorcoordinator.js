$(function() {

    var initialColor = '625194';

    // Set up Bootstrap colorpicker (http://mjolnic.com/bootstrap-colorpicker/)
    $('#colorpicker').colorpicker({
        color: '#' + initialColor,
        container: true,
        inline: true,
        format: 'hex',
        customClass: 'colorpicker',
        sliders: {
            saturation: {
                maxLeft: 200,
                maxTop: 200
            },
            hue: {
                maxTop: 200
            }
        }
    });

    // Perform a search for the chosen color.
    function search(color) {
        $.ajax("/tin-eye-search?color=" + color, {
            success: function(data) {
                console.log('Received JSON response from search server:');
                console.log(data);

                if (data.match_count == 0) {
                    $('#gallery-prompt').text('No matching art found. You could try another color.');
                } else {
                    $('#gallery-prompt').text('Select a thumbnail to see a larger image:');

                }

                // Loop over the image slots in the HTML.
                var counter = 0;
                $('#links > a').each(function () {
                    if (counter < data.match_count) {
                        var name = data.matches[counter].name;
                        var imagePath = '/img/art/' + encodeURIComponent(data.matches[counter].filename);
                        var thumbnailPath = '/img/art/thumbs/' + encodeURIComponent(data.matches[counter].filename);

                        $(this).attr('title', name);
                        $(this).attr('href', imagePath);
                        //$(this).html(data.matches[counter].name);
                        $(this).html('<img src="' + thumbnailPath + '" alt="' + name + '">');
                    } else {
                        $(this).attr('title', '');
                        $(this).attr('href', '');
                        $(this).html('<img src="" alt="">');
                    }

                    counter++;
                });

            },
            error: function(err) {
                $('#gallery-prompt').text('The search server returned an error: "' + err.responseJSON.message + '"');
            }
        });
    }

    $('#colorpicker').colorpicker().on('changeColor', function (e) {
        var color = e.color.toHex().replace('#', '');

        // Clear the gallery
        $('#links > a').each(function () {
            $(this).attr('title', '');
            $(this).attr('href', '');
            $(this).html('<img src="" alt="">');
        });

        // Perform color search
        search(color);
    });

    search(initialColor);
});
