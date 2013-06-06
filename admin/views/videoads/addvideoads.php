<?php
/*
  Name: Wordpress Video Gallery
  Plugin URI: http://www.apptha.com/category/extension/Wordpress/Video-Gallery
  Description: Add video ads view file.
  Version: 2.1
  Author: Apptha
  Author URI: http://www.apptha.com
  License: GPL2
 */
?>
<div class="apptha_gallery">
    <?php if (isset($videoadId)) {
 ?>
        <h2 class="option_title"><?php _e('Update Video Ad', 'digi'); ?></h2> <?php } else {
 ?> <h2  class="option_title"><?php echo "<img src='" . APPTHA_VGALLERY_BASEURL . "images/vid_ad.png' alt='move' width='30'/>"; ?><?php _e('Add a New Video Ad', 'digi'); ?></h2> <?php } ?>
<?php if (isset($msg)): ?>
        <div class="updated below-h2">
            <p>
            <?php
            echo $msg;
            $url = get_bloginfo('url') . '/wp-admin/admin.php?page=videoads';
            echo "<a href='$url' >Back to VideoAds</a>";
            ?>
                </p>
            </div>
<?php endif; ?>
 <?php
 if (!strstr($videoadEdit->file_path, 'wp-content/uploads')) {
                        $uploaded_video = 0;
                    }else{
                     $uploaded_video=1;
                    }

?>

            <div id="post-body" class="has-sidebar">
                <div id="post-body-content" class="has-sidebar-content">
                    <div class="stuffbox">


                        <h3 class="hndle videoform_title">
                            <span>
                                <input type="radio" name="videoad" id="filebtn" value="1" checked="checked" onClick="Videoadtype()" /> File
                            </span>
                            <span>
                                <input type="radio" name="videoad" id="urlbtn" value="2" onClick="Videoadtype()" />  URL
                            </span>
                        </h3>
                        <div id="upload2" class="form-table">

                    <table class="form-table">
                        <tr id="ffmpeg_disable_new1" name="ffmpeg_disable_new1">
                            <td  width="150"><?php _e('Upload Video', 'video_gallery') ?></td>
                            <td>
                                <div id="f1-upload-form" >
                                    <form name="normalvideoform" method="post" enctype="multipart/form-data" >
                                        <input type="file" name="myfile" onchange="enableUpload(this.form.name);" />
                                        <input type="button" class="button" name="uploadBtn" value="<?php _e('Upload Video', 'video_gallery') ?>" disabled="disabled" onclick="return addQueue(this.form.name,this.form.myfile.value);" />
                                        <input type="hidden" name="mode" value="video" />
                                        <label id="lbl_normal"><?php     $image_path = str_replace('plugins/contus-video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL); echo (isset($videoadEdit->file_path)  && $uploaded_video == 1) ? str_replace($image_path, '', $videoadEdit->file_path) : ""; ?></label>
                                    </form>
                                    <?php _e('<b>Supported video formats:</b>( MP4, M4V, M4A, MOV, Mp4v or F4V)', 'video_gallery') ?>
                                </div>
                                <span id="uploadmessage" style="display: block; margin-top:10px;margin-left:300px;color:red;font-size:12px;font-weight:bold;"></span>
                                <div id="f1-upload-progress" style="display:none">
                                    <div style="float:left"><img id="f1-upload-image" src="<?php echo get_option('siteurl') . '/wp-content/plugins/contus-video-gallery/images/empty.gif' ?>" alt="Uploading"  style="padding-top:2px"/>
                                        <label style="padding-top:0px;padding-left:4px;font-size:14px;font-weight:bold;vertical-align:top"  id="f1-upload-filename">PostRoll.flv</label></div>
                                    <div style="float:right"> <span id="f1-upload-cancel">
                                            <a style="float:right;padding-right:10px;" href="javascript:cancelUpload('normalvideoform');" name="submitcancel">Cancel</a>
                                        </span>
                                        <label id="f1-upload-status" style="float:right;padding-right:40px;padding-left:20px;">Uploading</label>
                                        <span id="f1-upload-message" style="float:right;font-size:10px;background:#FFAFAE;">
                                            <b><?php _e('Upload Failed:', 'video_gallery') ?></b> <?php _e('User Cancelled the upload', 'video_gallery') ?>
                                        </span></div>


                                </div>
                                <div id="nor"><iframe id="uploadvideo_target" name="uploadvideo_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe></div>
                            </td></tr>
                    </table>
                </div>
                <form action="" name="videoadsform" class="videoform" method="post" enctype="multipart/form-data"  >
                    <table class="form-table">
                        <tr>
                            <td scope="row"  width="150"><?php _e('Title / Name', 'ads') ?></td>
                            <td>
                                <input type="text" size="50" maxlength="200" name="videoadname" id="name" value="<?php echo (isset($videoadEdit->title)) ? $videoadEdit->title : ""; ?>"  />
                            </td>
                        </tr>
                    </table>
                    <div id="videoadurl" style="display: none;" >
                        <table class="form-table">
                            <tr>
                                <td scope="row"  width="150"><?php _e('URL to video file', 'hdflv') ?></td>
                                <td>
                                    <input type="text" size="50" onchange="clear_upload();" name="videoadfilepath" id="videoadfilepath"  value="<?php echo (isset($videoadEdit->file_path)) ? $videoadEdit->file_path : ""; ?>"  />&nbsp;&nbsp
                                    <br /><?php _e('Here you need to enter the URL to the ads video file', 'hdflv') ?>
                                    <br /><?php _e('It accept also a Youtube link: http://www.youtube.com/watch?v=tTGHCRUdlBs', 'ads') ?>
                                </td>
                            </tr>
                        </table>
                    </div>


                    <table class="form-table add_video_publish">
                        <tr>
                            <td scope="row" width="150"><?php _e('Publish', 'hdflvvideoshare') ?></td>
                            <td class="checkbox">

                                <?php //echo $act_feature;   ?>
                                <input type="radio" name="videoadpublish"  value="1" <?php
                                if (isset($videoadEdit->publish)) {
                                    echo "checked";
                                }
                                ?>><label>Yes</label>


                                <input type="radio" name="videoadpublish" value="0"  <?php
                                if (!isset($videoadEdit->publish)) {
                                    echo "checked";
                                }
                                ?>><label>No</label>

                            </td>
                        </tr>
                    </table>

<?php if (isset($videoadId)) { ?>
                                    <input type="submit" name="videoadsadd" class="button-primary"  value="<?php _e('Update Video Ad', 'ads'); ?>" class="button" /> <?php } else { ?> <input type="submit" name="videoadsadd" class="button-primary"  value="<?php _e('Add Video Ad', 'ads'); ?>" class="button" /> <?php } ?>
                    <input type="hidden" name="normalvideoform-value" id="normalvideoform-value" value="<?php echo (isset($videoadEdit->file_path) && $uploaded_video == 1) ? $videoadEdit->file_path : ""; ?>"  />
                </form>

            </div>
        </div>
    </div>
 <script type="text/javascript">
<?php

if (isset($videoadEdit->file_path) && $uploaded_video == 1) {
?>
document.getElementById("filebtn").checked = true;
Videoadtype();
<?php
} else {
?>
document.getElementById("urlbtn").checked = true;
Videoadtype();
<?php
                                                                                       }
?>
</script>
</div>