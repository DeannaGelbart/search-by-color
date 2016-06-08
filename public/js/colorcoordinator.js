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
        $.ajax("/search?color=" + color, {
            success: function(data) {
                console.log('Received JSON response from search server:');
                console.log(data);

                if (data.match_count == 0) {
                    $('#gallery-prompt').text('No matching art found. You could try another color.');
                } else {
                    $('#gallery-prompt').text('Select a thumbnail to see a larger image:');

                }

                // Create the image gallery.
                var maxImagesToShow = 20;
                var count = data.match_count;
                if (count > maxImagesToShow) {
                    count = maxImagesToShow;
                }
                for (imageIndex = 0; imageIndex < count; imageIndex++) {
                    var name = data.matches[imageIndex].name;
                    var imagePath = '/img/art/' + encodeURIComponent(data.matches[imageIndex].filename);
                    var thumbnailPath = '/img/art/thumbs/' + encodeURIComponent(data.matches[imageIndex].filename);

                    var a = '<a href="' + imagePath + '" title="' + name + '" data-gallery>';
                    var img = '<img src="' + thumbnailPath + '" alt="' + name + '" class="gallery-thumbnail">';

                    $('#links').append(a + img + '</a>');
                }
            },
            error: function(err) {
                $('#gallery-prompt').text('The search server returned an error: "' + err.responseJSON.message + '"');
            }
        });
    }

    $('#colorpicker').colorpicker().on('changeColor', function (e) {
        var color = e.color.toHex().replace('#', '');

        // Clear the image gallery.
        $('#links').empty();

        // Perform color search.
        search(color);
    });

    search(initialColor);
});
