$(function() {
    var initialColor = '3f3659';
    var currentSortBy = 'score';
    var currentSortOrder = 'desc';

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
        var queryParams = "color=" + color;
        if (currentSortBy !== 'score') {
            queryParams += "&sort=" + currentSortBy;
        }
        if (currentSortOrder === 'asc') {
            queryParams += "&order=asc";
        }

        $.ajax("/search?" + queryParams, {
            success: function(data) {
                console.log('Received JSON response from search server:');
                console.log(data);

                // Clear existing gallery
                $('#links').empty();

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
                    var size = data.matches[imageIndex].size;
                    
                    var sizeDisplay = size ? ' (' + Math.round(size/1024) + ' KB)' : '';
                    var a = '<a href="' + imagePath + '" title="' + name + sizeDisplay + '" data-gallery>';
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

        // Clear the image gallery
        $('#links').empty();

        // Perform color search.
        console.log('Searching for RGB color ' + color);
        $('#gallery-prompt').text('Searching...');
        search(color);
    });

    // Add sort controls event handlers
    $('.sort-option').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var sortBy = $this.data('sort');
        
        if (sortBy === currentSortBy) {
            // Toggle order if clicking same sort option
            currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortBy = sortBy;
            currentSortOrder = 'asc'; // Default to ascending for size
        }
        
        // Update active states
        $('.sort-option').removeClass('active');
        $this.addClass('active');
        
        // Perform new search with current color and sort settings
        var currentColor = $('#colorpicker').colorpicker('getValue').replace('#', '');
        search(currentColor);
    });

    search(initialColor);
});
