jQuery(function($) {
    var $attrForm = $('#attrForm'),
        $imageContainer = $('#imagesContainer');

    $attrForm.on("change", 'input, select', function() {
        var name = $(this).attr('name').match(/\[image_([^\]]+)/)[1],
            value = $(this).val();

        if (name) {
            $('.selected input[data-attr="'+name+'"]').val(value);
        }
    });

    $attrForm.find('input').prop('disabled', true);

    $imageContainer.on('click', 'li', function() {
        $imageContainer.find('li.selected').removeClass('selected');
        $attrForm.find('input').prop('disabled', false);

        $(this)
            .addClass('selected')
            .find('input.editable[type=hidden]')
            .each(function(i, el){
            var $el         = $(el),
                $update_el  = $('#jform_image_' + $el.data('attr'));

            if ($update_el.prop("tagName") == 'SELECT') {
                $update_el.val($el.val().split(','));
                $update_el.trigger("liszt:updated");
            } else {
                $update_el.val($el.val());
            }
        });

    });

    window.initDropzone = function(url, files_json) {
        Dropzone.options.imagesContainer = {
            url: url,
            previewTemplate: document.querySelector('#template-container').innerHTML,
            paramName: "file",
            createImageThumbnails: "true",
            maxFilesize: 1,
            thumbnailWidth: 128,
            thumbnailHeight: 90,
            acceptedFiles: 'image/*',

            init: function() {
                var dropper = this;

                dropper.on("addedfile", function(file) {
                    console.log(file);
                    $(file.previewElement)
                        .find(".filename").val(file.name).end()
                        .find(".filesize").val(file.size).end()
                        .find(".title").val(file.title ? file.title : "").end()
                        .find(".alt").val(file.alt ? file.alt : "").end()
                        .find(".color").val(file.color ? file.color : "").end()
                        .find(".color_name").val(file.color_name ? file.color_name : "").end()
                        .parent().sortable();
                });

                var files = JSON.parse(files_json);

                $.each(files, function(i, file) {
                    dropper
                        .emit("addedfile", file)
                        .emit("thumbnail", file, file.url)
                        .emit("complete", file)
                        .files.push(file);
                });
            },

            success: function(file, response, e){
                var data = JSON.parse(response);

                console.log(data, file, response, e);

                if (data.status !== 0) {
                    alert('Error! Status: ' + data.status + '. Message: ' + data.msg);
                } else {
                    if (file.name !== data.filename) {
                        $(file.previewElement)
                            .find('[name="jform[images][name][]"]').val(data.filename).end()
                            .find('[data-dz-name]').text(data.filename);
                    }
                }
            }
        };
    };
});