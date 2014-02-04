<p>This form is only for overriding the YouTube thumbnail. Adding, removing, and editing posts should happen on Tumblr.</p>
<ul>
    <li><span class="label label-danger">Red</span> rows indicate a problem</li>
    <li><span class="label label-info">Blue</span> is the video that will display in the "Reel" slot.</li>
</ul>

<div class="modal fade" id="eugene-dashboard-edit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Edit Image</h4>
            </div>
            <div class="modal-body">
                <p>If you want to use an image already uploaded to your Wordpress instance, make sure you use the "File URL" and not the "Permalink".</p>
                <p><label>Image URL: <input type="text" id="eugene-dashboard-image-url"/></label></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Update</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="eugene-dashboard-upload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Upload Image</h4>
            </div>
            <div class="modal-body">
                <p>Images are expected to have a 4x3 aspect ratio (e.g. 480x360) and will be cropped down to a 16x9 aspect ratio (this deals with the fact that YouTube images are letterboxed).</p>
                <div id="post_thumbnail_loading" style="display:none;height: 200px;background:url(/wp-admin/images/wpspin_light.gif) 50% 50% no-repeat;"></div>
                <div id="post_thumbnail_media_upload_form">
                    <p>Uploading a new file will replace the old one.  This can not be undone.</p>
                    <?php media_upload_form(); ?>
                </div>
                <input type="hidden" id="eugene_dashboard_upload_nonce" value="<?php echo $ajax_nonce_update_meta; ?>" />

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="eugene-dashboard-remove" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Remove Image</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this image? This can't be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Remove</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<form id="eugene-dashboard" action="eugene-dashboard-update">
    <table class="table">
        <thead>
            <th>#</th>
            <th>Type</th>
            <th>Original Image</th>
            <th>Custom Image</th>
            <th></th>
        </thead>
        <tbody>
            <?php $index = 0; ?>
            <?php foreach ($posts as $post) : ?>
                <?php $index++; ?>
                <tr id="<?php echo $post->id; ?>" class="<?php echo (($post->type == 'video' && !$post->youtube) ? 'danger ' : ''); ?><?php echo (($post->type == 'video' && $post->is_reel) ? 'info ' : ''); ?>">
                    <?php if ( $post->type == 'photo' ) : ?>
                        <td><?php echo $index; ?></td>
                        <td><?php echo $post->type; ?></td>
                        <td class="image">
                            <img src="<?php echo $post->{'photo-url-500'}; ?>">
                        </td>
                        <td colspan="2">This image can't be overridden.</td>
                    <?php elseif ( $post->type == 'video' && !$post->youtube ) : ?>
                        <td><?php echo $index; ?></td>
                        <td><?php echo $post->type; ?></td>
                        <td colspan="3">This video (<strong>"<?php echo strip_tags( $post->{'video-caption'} ); ?>"</strong>) is private and won't display properly.</td>
                    <?php elseif ( $post->type == 'video' && $post->youtube ) : ?>
                        <td><?php echo $index; ?></td>
                        <td><?php echo $post->type; ?></td>
                        <td class="image default-image">
                            <img src="<?php echo $post->youtube->thumbnail; ?>">
                        </td>
                        <td class="image custom-image">
                            <?php if ( isset($post->custom_thumbnail) ) : ?>
                                <img src="<?php echo $post->custom_thumbnail; ?>">
                            <?php else : ?>
                                ---
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="#" class="edit-image btn btn-success" title="edit" data-id="<?php echo $post->id; ?>" data-target="#eugene-dashboard-edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="#" class="upload-image btn btn-success" title="upload" data-id="<?php echo $post->id; ?>" data-target="#eugene-dashboard-upload">
                                <i class="fa fa-upload"></i> Upload
                            </a>
                            <a href="#" class="upload-image btn btn-info" title="reel" data-id="<?php echo $post->id; ?>" <?php if ($post->is_reel) : ?>disabled="disabled"<?php endif; ?>>
                                <i class="fa fa-star"></i> Set as Reel
                            </a>
                            <a href="#" class="remove-image btn btn-danger" title="remove" data-id="<?php echo $post->id; ?>" data-target="#eugene-dashboard-remove">
                                <i class="fa fa-trash-o"></i> Remove
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>