<?php
/*
Name: Wordpress Video Gallery
Plugin URI: http://www.apptha.com/category/extension/Wordpress/Video-Gallery
Description: Video more page view file.
Version: 2.0
Author: Apptha
Author URI: http://www.apptha.com
License: GPL2
*/

if (class_exists('ContusMoreView') != true) {

    class ContusMoreView extends ContusMoreController { //CLASS FOR HOME PAGE STARTS

        public $_settingsData;
        public $_vId;
        public $_playid;
        public $_pagenum;

        public function __construct() {//contructor starts
            parent::__construct();
            $this->_settingsData    = $this->settings_data();
            $this->_mPageid         = $this->More_pageid();
            $this->_feaMore         = $this->Video_count();
            $this->_vId             = filter_input(INPUT_GET, 'vid');
            $this->_pagenum         = filter_input(INPUT_GET, 'pagenum');
            $this->_playid          = filter_input(INPUT_GET, 'playid');
            $video_search    = filter_var(filter_input(INPUT_POST, 'video_search'), FILTER_SANITIZE_STRING);
            $video_search1    = filter_var(filter_input(INPUT_GET, 'video_search'), FILTER_SANITIZE_STRING);
            if(empty($video_search))
               $this->_video_search= $video_search1;
            else
                $this->_video_search= $video_search;
            $this->_showF           = 5;
            $this->_site_url        = get_bloginfo('url');
            $this->_imagePath       = APPTHA_VGALLERY_BASEURL . 'images' . DS;
        } //contructor ends

        function video_more_pages($type) {// More PAGE FEATURED VIDEOS STARTS
            if (function_exists('homeVideo') != true) {

                switch ($type) {
                    case 'pop'://GETTING POPULAR VIDEOS STARTS
                        $rowF           = $this->_settingsData->rowsPop; //row field of popular videos
                        $colF           = $this->_settingsData->colPop; //column field of popular videos
                        $dataLimit      = $rowF * $colF;
                        $where = '';
                        $thumImageorder = 'w.hitcount DESC';
                        $TypeOFvideos   = $this->home_thumbdata($thumImageorder, $where,$this->_pagenum, $dataLimit);
                        $CountOFVideos  = $this->Countof_Videos($thumImageorder);
                        $typename       = __('Popular', 'video_gallery');
                        $type_name      = 'popular';
                        $morePage       = '&more=pop';
                        break; //GETTING POPULAR VIDEOS ENDS

                    case 'rec':
                        $rowF           = $this->_settingsData->rowsRec;
                        $where = '';
                        $colF           = $this->_settingsData->colRec;
                        $dataLimit      = $rowF * $colF;
                        $thumImageorder = 'w.vid DESC';
                        $TypeOFvideos   = $this->home_thumbdata($thumImageorder, $where,$this->_pagenum, $dataLimit);
                        $CountOFVideos  = $this->Countof_Videos($thumImageorder);
                        $typename       = __('Recent', 'video_gallery');
                        $type_name      = 'recent';
                        $morePage       = '&more=rec';
                        break;

                    case 'fea':
                        $thumImageorder = 'w.ordering ASC';
                        $where = 'AND w.featured=1';
                        $rowF           = $this->_settingsData->rowsFea;
                        $colF           = $this->_settingsData->colFea;
                        $dataLimit      = $rowF * $colF;
                        $TypeOFvideos   = $this->home_thumbdata($thumImageorder, $where,$this->_pagenum, $dataLimit);
                        $CountOFVideos  = $this->Countof_Videos($thumImageorder);
                        $typename       = __('Featured', 'video_gallery');
                        $type_name      = 'featured';
                        $morePage       = '&more=fea';
                        break;
                    case 'cat':
                        $thumImageorder = $this->_playid;
                        $rowF           = $this->_settingsData->rowCat;
                        $colF           = $this->_settingsData->colCat;
                        $dataLimit      = $rowF * $colF;
                        $TypeOFvideos   = $this->home_catthumbdata($thumImageorder, $this->_pagenum, $dataLimit);
                        $CountOFVideos  = $this->Countof_Videos($thumImageorder);
                        $typename       = __('Category', 'video_gallery');
                        $morePage       = '&playid=' . $thumImageorder;
                        break;
                    case 'categories':
                        $rowF           = $this->_settingsData->rowCat;
                        $colF           = $this->_settingsData->colCat;
                        $dataLimit      = $rowF * $colF;
                        $TypeOFvideos   = $this->home_categoriesthumbdata($this->_pagenum, $dataLimit);
                        $CountOFVideos  = $this->Countof_Videocategories();
                        $typename       = __('Video Categories', 'video_gallery');
                        return $this->categoryList($CountOFVideos, $TypeOFvideos, $this->_pagenum, $dataLimit);
                        break;
                    case 'search':
                        $thumImageorder = "( t4.tags_name REGEXP '[[:<:]]$this->_video_search [[:>:]]' || t1.description REGEXP '[[:<:]]$this->_video_search [[:>:]]' || t1.name LIKE '%" . $this->_video_search . "%')";
                        $TypeSet = $this->_settingsData->feature;
                        $rowF = $this->_settingsData->rowsFea;
                        $colF = $this->_settingsData->colFea;
                        $dataLimit = $rowF * $colF;
                        $TypeOFvideos   = $this->home_searchthumbdata($thumImageorder,$this->_pagenum, $dataLimit);
                        $CountOFVideos  = $this->Countof_Videosearch($thumImageorder);
                        return $this->searchList($this->_video_search,$CountOFVideos, $TypeOFvideos, $this->_pagenum, $dataLimit);
                        break;
                }

                $class = $div = '';
?>

<?php
$pagenum    = isset($this->_pagenum) ? absint($this->_pagenum) : 1;
                $div = '<div class="video_wrapper" id="'.$type_name.'_video">';
                $div .= '<style type="text/css"> .video-block {  padding-right:' . $this->_settingsData->gutterspace . 'px} </style>';
                if (!empty($TypeOFvideos)) {
                    $div .='<h2 >' . $typename . ' '.__('Videos', 'video_gallery').' </h2>';
                    $j          = 0;
                    $clearwidth = 0;
                    $clear      = $fetched[$j] = '';
                    $image_path = str_replace('plugins/video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
                    foreach ($TypeOFvideos as $video) {
                        $duration[$j] = $video->duration; //VIDEO DURATION
                        $imageFea[$j] = $video->image; //VIDEO IMAGE
                        $file_type    = $video->file_type; // Video Type
                       $guid[$j] = $video->guid; //guid
                        if ($imageFea[$j] == '') {  //If there is no thumb image for video
                            $imageFea[$j] = $this->_imagePath . 'nothumbimage.jpg';
                        } else {
                            if ($file_type == 2) {          //For uploaded image
                                $imageFea[$j] = $image_path . $imageFea[$j];
                            }
                        }
                        $vidF[$j]        = $video->vid; //VIDEO ID
                        $nameF[$j]       = $video->name; //VIDEI NAME
                        $hitcount[$j]    = $video->hitcount; //VIDEO HITCOUNT
                        if (!empty($this->_playid)) {
                            $fetched[$j] = $video->playlist_name;
                            $playlist_id = $this->_playid;
                        } else {
                            $getPlaylist     = $this->_wpdb->get_row("SELECT playlist_id FROM " . $this->_wpdb->prefix . "hdflvvideoshare_med2play WHERE media_id='$vidF[$j]'");
                            if (isset($getPlaylist->playlist_id)) {
                                $playlist_id = $getPlaylist->playlist_id; //VIDEO CATEGORY ID
                                $fetPlay[$j] = $this->_wpdb->get_row("SELECT playlist_name FROM " . $this->_wpdb->prefix . "hdflvvideoshare_playlist WHERE pid='$playlist_id'");
                                $fetched[$j] = $fetPlay[$j]->playlist_name; //CATEOGORY NAME
                            }
                        }
                        $j++;
                    }
                    $div .= '<div>';
                    for ($j = 0; $j < count($TypeOFvideos); $j++) {
                        $class = '<div class="clear"></div>';
                        if (strlen($nameF[$j]) > 25) { // Displaying Video Title
                                $videoname = substr($nameF[$j], 0, 25) . '';
                            }
                            else {
                                $videoname = $nameF[$j];
                            }
                        if (($j % $colF) == 0) {//COLUMN COUNT
                            $div .= '<div class="clear"></div>';
                        } 
                            $div .= '<div class="video-block">';
                            $div .='<div  class="video-thumbimg"><a href="' . $guid[$j] . '"> <img src="' . $imageFea[$j] . '" alt="' . $nameF[$j] . '" class="imgHome" title="' . $nameF[$j] . '" /></a>';
                            if ($duration[$j] != 0.00) {
                                $div .= '<span class="video_duration">'.$duration[$j] . '</span>';
                            }
                            $div .='</div>';
                            $div .='<h5><a href="' . $guid[$j] . '" class="videoHname">';
                            $div .=$videoname;
                            $div .='</a></h5>';      
                            $div .='<div class="vid_info">
                                    <span class="video_views">';
                            $div .= $hitcount[$j] . ' '.__('Views', 'video_gallery');
                            $div .= '</span>';
                            
                                                  
                            
                            if (!empty($fetched[$j])) {
                                $div .='<span class="playlistName"><a href="' . $this->_site_url . '/?page_id=' . $this->_mPageid . '&playid=' . $playlist_id . '">' . $fetched[$j] . '</a></span>';
                            }
                            $div .= '</div>';
                            $div .='</div>';
                        //ELSE ENDS
                    }//FOR EACH ENDS
                    $div .='</div>';
                    $div .='<div class="clear"></div>';
                }
                else
                    $div .=__('No', 'video_gallery').' ' . $typename . ' '.__('Videos', 'video_gallery');
                $div     .='</div>';

                //PAGINATION STARTS
                $total          = $CountOFVideos;
                $num_of_pages   = ceil($total / $dataLimit);
                $page_links     = paginate_links(array(
                            'base'      => add_query_arg('pagenum', '%#%'),
                            'format'    => '',
                            'prev_text' => __('&laquo;', 'aag'),
                            'next_text' => __('&raquo;', 'aag'),
                            'total'     => $num_of_pages,
                            'current'   => $pagenum
                        ));

                if ($page_links) {
                    $div .='<div class="tablenav"><div class="tablenav-pages" >' . $page_links . '</div></div>';
                }
                //PAGINATION ENDS
                return $div;
            }
        }

        function categoryList($CountOFVideos, $TypeOFvideos, $pagenum, $dataLimit) {

            global $wpdb;
            $div        = '';
            $pagenum    = isset($pagenum) ? absint($pagenum) : 1; // Calculating page number
            $start      = ( $pagenum - 1 ) * $dataLimit;     // Video starting from
            $limit      = $dataLimit;                        // Video Limit
?>          

<?php
            $div .='<div><h1 class="entry-title">'.__('Video Categories', 'video_gallery').'</h1></div>';
            $div .= '<style> .video-block { padding-right:' . $this->_settingsData->gutterspace . 'px } </style>';
            foreach ($TypeOFvideos as $catList) {
// Fetch videos for every category
               $sql            = "SELECT s.guid,w.* FROM " . $wpdb->prefix . "hdflvvideoshare as w
                    INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_med2play as m ON m.media_id = w.vid
                    INNER JOIN " . $wpdb->prefix . "hdflvvideoshare_playlist as p on m.playlist_id = p.pid
                     INNER JOIN " . $this->_wpdb->prefix . "posts s ON s.ID=w.slug 
WHERE w.publish='1' and p.is_publish='1' and m.playlist_id=" . $catList->pid . " GROUP BY w.vid";
                $playLists      = $wpdb->get_results($sql);
                $playlistCount  = count($playLists);

                $div .='<div> <h4 class="clear more_title">' . $catList->playlist_name . '</h4></div>';
                if (!empty($playlistCount)) {
                    $i          = 0;
                    $catL       = 4;
                    $inc        = 1;
                    $image_path = str_replace('plugins/video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
                    foreach ($playLists as $playList) {

                        $duration   = $playList->duration;
                        $imageFea   = $playList->image; //VIDEO IMAGE
                        $file_type  = $playList->file_type; // Video Type
                        $guid = $playList->guid; //guid
                        if ($imageFea == '') {  //If there is no thumb image for video
                            $imageFea = $this->_imagePath . 'nothumbimage.jpg';
                        } else {
                            if ($file_type == 2) {          //For uploaded image
                                $imageFea = $image_path . $imageFea;
                            }
                        }
                        if (strlen($playList->name) > 25) {
                            $playListName = substr($playList->name, 0, 25) . "";
                        } else {
                            $playListName = $playList->name;
                        }

                        $div .='<div class="video-block"><div class="video-thumbimg"><a href="' . $guid . '"><img src="' . $imageFea . '" alt="" class="imgHome" title=""></a>';
                        if ($duration != 0.00) {
                            $div .='<span class="video_duration">' . $duration . '</span>';
                        }
                        $div .='</div><h5><a href="' . $guid . '" class="videoHname">' . $playListName . '</a></h5><div class="views">';
                        
                            $div .='<span class="views">' . $playList->hitcount . ' '.__('Views', 'video_gallery') . '</span>';
                       
                        $div .='</div></div>';

                        if ($i > 8) {
                            break;
                        } else {
                            $i = $i + 1;
                        }

                        if ($inc % ($catL) == 0) {
                            $div .= '<div class="clear"></div>';
                        }
                        $inc++;
                    }
                    if (($playlistCount > 8)) {

                        $div .='<a class="video-more" href="' . $this->_site_url . '/?page_id=' .  $this->_mPageid . '&playid=' . $catList->pid . '">'.__('More Videos', 'video_gallery').'</a>';
                    } else {
                        $div .='<div align="right"> </div>';
                    }
                } else { // If there is no video for category
                    $div .='<div>'.__('No Videos For this Category', 'video_gallery').'</div>';
                }
            }

            $div .='<div class="clear"></div>';

            //PAGINATION STARTS
            $total          = $CountOFVideos;
            $num_of_pages   = ceil($total / $dataLimit);
            $page_links     = paginate_links(array(
                        'base'      => add_query_arg('pagenum', '%#%'),
                        'format'    => '',
                        'prev_text' => __('&laquo;', 'aag'),
                        'next_text' => __('&raquo;', 'aag'),
                        'total'     => $num_of_pages,
                        'current'   => $pagenum
                    ));

            if ($page_links) {
                $div .='<div class="tablenav"><div class="tablenav-pages" >' . $page_links . '</div></div>';
            }

            //PAGINATION ENDS
            return $div;
        }

        function searchList($video_search,$CountOFVideos, $TypeOFvideos, $pagenum, $dataLimit) {

            global $wpdb;
            $div        = '';
            $pagenum    = isset($pagenum) ? absint($pagenum) : 1; // Calculating page number
            $start      = ( $pagenum - 1 ) * $dataLimit;     // Video starting from
            $limit      = $dataLimit;                        // Video Limit
?>


<?php
            $div .='<div class="video_wrapper" id="video_search_result"><h3 class="entry-title">'.__('Search Results', 'video_gallery').' - '.$video_search.'</h3>';
            $div .= '<style> .video-block { padding-right:' . $this->_settingsData->gutterspace . 'px } </style>';

// Fetch videos for every category
                if (!empty($TypeOFvideos)) {
                    $i          = 0;
                    $catL       = 4;
                    $inc        = 1;
                    $image_path = str_replace('plugins/video-gallery/', 'uploads/videogallery/', APPTHA_VGALLERY_BASEURL);
                    foreach ($TypeOFvideos as $playList) {

                        $duration   = $playList->duration;
                        $imageFea   = $playList->image; //VIDEO IMAGE
                        $file_type  = $playList->file_type; // Video Type
                        $guid = $video->guid; //guid
                        if ($imageFea == '') {  //If there is no thumb image for video
                            $imageFea = $this->_imagePath . 'nothumbimage.jpg';
                        } else {
                            if ($file_type == 2) {          //For uploaded image
                                $imageFea = $image_path . $imageFea;
                            }
                        }
                        if (strlen($playList->name) > 25) {
                            $playListName = substr($playList->name, 0, 25) . "";
                        } else {
                            $playListName = $playList->name;
                        }

                        $div .='<div class="video-block"><div class="video-thumbimg"><a href="' . $guid . '"><img src="' . $imageFea . '" alt="" class="imgHome" title=""></a>';
                        if ($duration == 0.00) {
                            $div .='<span class="video_duration">' . $duration. '</span>';
                        }
                        $div .='</div><h5><a href="' . $guid . '" class="videoHname">' . $playListName . '</a></h5><div class="vid_info">';
                        $div .='<span class="video_views">' . $playList->hitcount . ' '.__('Views', 'video_gallery') . '</span>';
                        
                        $div .='</div></div>';

                        if ($i > 8) {
                            break;
                        } else {
                            $i = $i + 1;
                        }

                        if ($inc % ($catL) == 0) {
                            $div .= '<div class="clear"></div>';
                        }
                        $inc++;
                    }

                } else { // If there is no video for category
                    $div .='<div>'.__('No Videos For this Category', 'video_gallery').'</div>';
                }
            

            $div .='<div class="clear"></div>';

            //PAGINATION STARTS
            $total          = $CountOFVideos;
            $num_of_pages   = ceil($total / $dataLimit);
            $arr_params = array ( 'pagenum' => '%#%', 'video_search' => $video_search );
            $page_links     = paginate_links(array(
                        'base'      => add_query_arg($arr_params),
                        'format'    => '',
                        'prev_text' => __('&laquo;', 'aag'),
                        'next_text' => __('&raquo;', 'aag'),
                        'total'     => $num_of_pages,
                        'current'   => $pagenum
                    ));

            if ($page_links) {
                $div .='<div class="tablenav"><div class="tablenav-pages" >' . $page_links . '</div></div></div>';
            }

            //PAGINATION ENDS
            return $div;
        }
        //CATEGORY FUNCTION ENDS
    }
    //class over
} else {
    echo 'class contusMore already exists';
}
?>

