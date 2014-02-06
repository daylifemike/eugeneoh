;(function($) {
    $(document).ready(function () {
        // for some reason this breaks everything when it's missing
        window.post_id = 0;

        var index = window.location.href.indexOf('flush=1');
        if ( index > -1 ) {
            window.location.href = window.location.href.substring(0, index - 1)
        }

        $dashboard = $('#eugene-dashboard');
        $upload = $('#eugene-dashboard-upload');
        $remove = $('#eugene-dashboard-remove');
        $edit = $('#eugene-dashboard-edit');
        $items = $dashboard.find('tr');

        $dashboard.on('click','.edit-image, .upload-image, .remove-image', function (event) {
            event.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            var target = $this.data('target');
            EUGENE_DASHBOARD.current_id = id;
            EUGENE_DASHBOARD.current_modal = target;
            $(target).modal();
        });

        $dashboard.parents('#eugene_dashboard_widget').on('click', '.refresh-data', function (event) {
            event.preventDefault();
            var url = window.location.href;
            if (url.indexOf('?') > -1){
               url += '&flush=1'
            }else{
               url += '?flush=1'
            }
            window.location.href = url;
        });

        $dashboard.on('click', '.btn-info', function (event) {
            event.preventDefault();
            var id = $(this).data('id');
            setReel(id);
        });

        $remove.on('click', '.btn-danger', function (event) {
            event.preventDefault();
            deleteImage();
        });

        $edit.on('click', '.btn-primary', function (event) {
            event.preventDefault();
            var image_url = $('#eugene-dashboard-image-url').val();
            updateImage(image_url);
        });

        function addImage (attachment_id) {
            save({
                type: 'add',
                post_id: EUGENE_DASHBOARD.current_id,
                attachment_id: attachment_id
            });
        };

        function updateImage (image_url) {
            save({
                type: 'edit',
                post_id: EUGENE_DASHBOARD.current_id,
                image_url: image_url
            });
        };

        function deleteImage () {
            save({
                type: 'delete',
                post_id: EUGENE_DASHBOARD.current_id
            });
        };

        function setReel (id) {
            EUGENE_DASHBOARD.current_id = id;

            var callback = function () {
                $items.each(function () {
                    var $this = $(this);

                    $this.removeClass('info');
                    $this.find('.btn-info').removeAttr('disabled');

                    if ($this.attr('id') === id.toString()) {
                        $this.addClass('info');
                        $this.find('.btn-info').attr('disabled', 'disabled');
                    }
                });
            };

            save({
                type: 'reel',
                post_id: id
            }, callback);
        };

        function save (data, func) {
            var ajax_params = {
                action: "eugene_dashboard_update",
                _wpnonce: $('#eugene_dashboard_upload_nonce').val(),
                cookie: encodeURIComponent(document.cookie)
            };
            var params = $.extend({}, ajax_params, data);

            var callback = function (resp) {
                var json = JSON.parse(resp);
                var $current = $('#' + EUGENE_DASHBOARD.current_id).find('.custom-image');
                
                $current.empty()
                
                if (json.data) {
                    $current.append('<img src="'+ json.data +'">');
                } else {
                    $current.text('---');
                }

                $(EUGENE_DASHBOARD.current_modal).modal('hide');
            };

            $.post(EUGENE_DASHBOARD.admin_ajax, params, func || callback);
        }

        //based on uploadSuccess in /wp-includes/plupload/handels.dev.js
        if (window.uploadSuccess !== 'undefined') {
            window.uploadSuccess = function (fileObj, serverData) {
                // on success serverData should be numeric, fix bug in html4 runtime returning the serverData wrapped in a <pre> tag
                serverData = serverData.replace(/^<pre>(\d+)<\/pre>$/, '$1');

                // if async-upload returned an error message, place it in the media item div and return
                if (serverData.match('media-upload-error')) {
                    alert(serverData);
                    return;
                }

                addImage(serverData);
            }
        }

    });
})(jQuery);