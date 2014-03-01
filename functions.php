<?php
/**
 * Eugene Young functions and definitions.
 */

$EYO_TRANSIENTS_ID = 'eyo_transients';
$EYO_OPTIONS_ID = 'eyo_options';

// header.php
function eyo_pageMetaTags() {
    $post_id = eyo_getPermalinkVideo();
    $output = '';

    if ( $post_id ) {
        $posts = eyo_getData();

        foreach ($posts as $p) {
            if ( $p->id == $post_id ) {
                $post = $p;
            }
        }
    }

    $site_name   = get_bloginfo('name');

    if ( !$post_id ) {
        $title = get_bloginfo('name');
        $image = get_template_directory_uri() . '/img/eugene.jpg';
        $url = get_bloginfo('url');

    } elseif ( $post->type == 'photo' ) {
        $title = addslashes(strip_tags( $post->{'photo-caption'} ));
        $image = $post->{'photo-url-500'};
        $url = $post->wordpress_url;

    } elseif ( $post->type == 'video' && $post->youtube ) {
        $title = addslashes(strip_tags( $post->{'video-caption'} ));
        if ( !empty($post->custom_thumbnail) ) {
            $image = $post->custom_thumbnail;
        } else {
            $image = $post->youtube->thumbnail;
        }
        $url = $post->wordpress_url;
    }

    // // Facebook & Google+
    $output .= '<!-- Facebook & Google+ -->';
    $output .= '<meta property="og:site_name" content="'. $site_name .'"/>';
    $output .= '<meta property="og:title" content="'. $title .'"/>';
    $output .= '<meta property="og:image" content="'. $image . '"/>';
    // $output .= '<meta property="og:description" content="'. $description .'"/>';
    $output .= '<meta property="og:url" content="'. trailingslashit($url) .'"/>';

    // // Twitter
    // $output .= '<!-- Twitter -->';
    // $output .= '<meta name="twitter:site" content="@goodcopgreatcop">';
    // $output .= '<meta name="twitter:creator" content="@goodcopgreatcop">';
    // $output .= '<meta name="twitter:card" content="summary_large_image">';
    // $output .= '<meta name="twitter:title" content="'. $title .'"/>';
    // $output .= '<meta name="twitter:image" content="'. $image . '"/>';
    // $output .= '<meta name="twitter:description" content="'. $description .'"/>';

    return $output;
}

function eyo_getData() {
    global $EYO_TRANSIENTS_ID;

    // refresh all saved YoutTube data
    if ( isset($_GET['flush']) ) {
        $data = eyo_refreshData();
        return $data;
    }

    $data = get_transient( $EYO_TRANSIENTS_ID );
    // $data = false;

    // transient has expired, time to look for new data
    if ( $data === false ) {
        $data = eyo_updateData();
    }

    return $data;
}

function eyo_updateData() {
    global $EYO_OPTIONS_ID;
    global $EYO_TRANSIENTS_ID;

    $db_data = get_option( $EYO_OPTIONS_ID );
    $tumblr_data = eyo_getTumblrData();
    $custom_thumbnails = array();
    $reel = null;

    if ( !$tumblr_data ) {
        set_transient( $EYO_TRANSIENTS_ID, $db_data, 60*60*24 );
        return $db_data;
    }

    if ( !empty($tumblr_data) ) {
        // save any custom thumbnails and the reel
        foreach ($db_data as $post) {
            if ( isset($post->custom_thumbnail) ) {
                $custom_thumbnails[$post->id] = $post->custom_thumbnail;
            }
            if ( $post->is_reel ) {
                $reel = $post->id;
            }
        }

        $db_data = $tumblr_data;

        foreach ( $tumblr_data as $post ) {
            if ( $post->type == 'photo' ) {
                $post->aspect = ($post->width > $post->height ? 'landscape' : 'portrait');
            } elseif ( $post->type == 'video' ) {
                $pattern_full = "[?|&]v=([a-zA-Z0-9_-]+)";
                $pattern_short = "youtu.be\/([a-zA-Z0-9_-]+)";
                preg_match( "#$pattern_full#i", $post->{'video-source'}, $matches);

                if ( !$matches ) {
                    preg_match( "#$pattern_short#i", $post->{'video-source'}, $matches);
                }

                $yt_data = eyo_getYouTubeData( $matches[1] );
                $post->youtube = $yt_data;
            }

            $post->is_reel = false;
            $title = (!empty($post->slug) ? $post->slug : '');
            $post->wordpress_url = eyo_makeWpUrl($post->id, $title);
        }

        // put back any custom thumbnails and the reel
        foreach ($db_data as $post) {
            if ( !empty($custom_thumbnails[$post->id]) ) {
                $post->custom_thumbnail = $custom_thumbnails[$post->id];
            }
            if ( $post->id == $reel ) {
                $post->is_reel = true;
            }
        }

        update_option( $EYO_OPTIONS_ID, $db_data );
    }

    set_transient( $EYO_TRANSIENTS_ID, $db_data, 60*60*24 );

    return $db_data;
}

function eyo_updatePost($id, $custom_thumbnail, $reel) {
    global $EYO_OPTIONS_ID;
    global $EYO_TRANSIENTS_ID;

    $data = eyo_getData();

    if ($reel) {
        foreach ($data as $post) {
            if ( $post->id === $id ) {
                $post->is_reel = true;
            } else {
                $post->is_reel = false;
            }
        }
    } else {
        foreach ($data as $post) {
            if ( $post->id === $id ) {
                if ( isset($custom_thumbnail) ) {
                    $post->custom_thumbnail = $custom_thumbnail;
                } else {
                    if (isset($post->custom_thumbnail)) {
                        unset($post->custom_thumbnail);
                    }
                }
            }
        }
    }

    update_option( $EYO_OPTIONS_ID, $data );
    set_transient( $EYO_TRANSIENTS_ID, $data, 60*60*24 );

    return;
}

function eyo_getTumblrData() {
    // text, photo, photoset, quote, link, chat, audio, video, answer
    $url = 'http://eugeneyoung.tumblr.com/api/read/json?debug=1&num=50';
    $response = wp_remote_get( $url );
    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code != 200 ) {
        return false;
    }
    $response = json_decode( $response['body'] );
    $response = $response->posts;
    return $response;
}


function eyo_getYouTubeData($video_id) {
    $url = 'http://gdata.youtube.com/feeds/api/videos/' . $video_id . '?v=2&alt=jsonc';
    $response = wp_remote_get( $url );
    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code != 200 ) {
        return false;
    }
    $response = json_decode( $response['body'] );
    $response = $response->data;
    return eyo_parseYouTubeResponse($response);
}


function eyo_makeWpUrl($id, $title) {
    $slug = sanitize_title_with_dashes( remove_accents( $title ) );
    return get_bloginfo('url') . "/post/" . $id . "/" . $slug;
}


function eyo_parseYouTubeResponse($video) {
    $response = (object) array(
        'id' => $video->id,
        'title' => $video->title,
        'description' => $video->description,
        'duration' => eyo_prettyDuration($video->duration),
        'thumbnail' => $video->thumbnail->hqDefault,
        'youtube_url' => '//www.youtube.com/watch?v=' . $video->id . '',
        'embed_url' => $video->content->{'5'} . '&enablejsapi=0&iv_load_policy=3&showinfo=0'
        // ,'wordpress_url' => eyo_makeWpUrl( $video->id, $video->title )
    );

    return $response;
}


function eyo_refreshData() {
    global $EYO_OPTIONS_ID;
    global $EYO_TRANSIENTS_ID;

    $data = eyo_updateData();

    delete_option( $EYO_OPTIONS_ID );
    delete_transient( $EYO_TRANSIENTS_ID );

    update_option( $EYO_OPTIONS_ID, $data );
    set_transient( $EYO_TRANSIENTS_ID, $data, 60*60*24 );

    return $data;
}


function eyo_prettyDuration($seconds) {
    $h = $seconds / 3600 % 24;
    $m = $seconds / 60 % 60; 
    $s = $seconds % 60;

    $output = array();

    // only print hours when present
    if ($h >= 1) $output[] = "{$h}";

    // always print minutes (even 0)
    // only add the leading 0 when there are hours
    if ( $h >= 1 && strlen(strval("{$m}")) < 2 ) {
        $output[] = "0{$m}";
    } else {
        $output[] = "{$m}";
    }

    // always print seconds
    // always be 2 digits long (leading 0)
    if ( strlen(strval("{$s}")) < 2 ) {
        $output[] = "0{$s}";
    } else {
        $output[] = "{$s}";
    }

    return implode(':', $output);
}

function eyo_getPermalinkVideo() {
    global $wp_query;
    global $wp_rewrite;
 
    $id = 0;

    // WordPress using Pretty Permalink structure
    if ( $wp_rewrite->using_permalinks() ) {
        if ( isset($wp_query->query_vars['post']) ) {
            $id = $wp_query->query_vars['post'];
        }
    } else { 
        // WordPress using default pwrmalink structure like www.site.com/wordpress/?p=123
        $id = $_GET['post'];
    }

    return $id;
}


function eyo_addUrlRules() {
    add_rewrite_tag('%post%','([^&]+)', 'post=');
    add_rewrite_rule(  
        '^post/([a-zA-Z0-9_-]{11})/?',
        'index.php?post=$matches[1]',
        'top'
    );
}


function query_vars($public_query_vars) {
    $public_query_vars[] = "post";
    return $public_query_vars;
}

function eyo_dashboard_columns() {
    add_screen_option(
        'layout_columns',
        array(
            'max'     => 2,
            'default' => 1
        )
    );
}

// ------------
// WP_Rewrite info:
// http://www.prodeveloper.org/create-your-own-rewrite-rules-in-wordpress.html
// http://www.hongkiat.com/blog/wordpress-url-rewrite/
// set permalinks to something other than "default"
// ------------
add_action( 'init', 'eyo_addUrlRules' );
add_filter( 'query_vars', 'query_vars' );

add_action( 'admin_head-index.php', 'eyo_dashboard_columns' );

add_action('wp_dashboard_setup', array('EugeneDashboard', 'setup'));
add_action("wp_ajax_eugene_dashboard_update", array('EugeneDashboard', 'update'));

class EugeneDashboard {

    public static function init() {
        $theme_url = get_template_directory_uri();
        $posts = eyo_getData();
        $ajax_nonce_update_meta = wp_create_nonce('eugene_dashboard_upload_nonce');
        include('admin_dashboard_widget_template.php');
    }

    public static function setup() {
        $theme_url = get_template_directory_uri();
        
        wp_add_dashboard_widget('eugene_dashboard_widget', 'Video Images', array('EugeneDashboard', 'init'));

        wp_enqueue_style('eugene_dashboard_bootstrap_css', $theme_url . '/vendor/bootstrap.css');
        wp_enqueue_style('eugene_dashboard_bootstrap_theme_css', '//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css');
        wp_enqueue_style('eugene_dashboard_fontawesome_css', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css');
        wp_enqueue_style('eugene_dashboard_css', $theme_url . '/css/eugene-dashboard-style.css');
        wp_enqueue_style('imgareaselect');

        wp_enqueue_script('plupload-handlers');
        wp_enqueue_script('image-edit');

        wp_register_script('eugene_dashboard_jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
        wp_register_script('eugene_dashboard_bootstrap_js', '//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js', array('eugene_dashboard_jquery') );
        wp_register_script('eugene_dashboard_js', $theme_url . '/js/dashboard.js', array('eugene_dashboard_jquery', 'eugene_dashboard_bootstrap_js') );
        wp_localize_script('eugene_dashboard_js', 'EUGENE_DASHBOARD', array('admin_ajax' => admin_url('admin-ajax.php')));

        wp_enqueue_script('eugene_dashboard_js');
    }

    public static function update() {
        if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], "eugene_dashboard_upload_nonce")) {
            exit("Unauthorized");
        }

        $post_id = $_REQUEST['post_id'];
        $image_url = null;
        $reel = false;

        switch ($_REQUEST['type']) {
            case 'add':
                $image_url = wp_get_attachment_url( $_REQUEST['attachment_id'] );
                break;
            
            case 'edit':
                $image_url = $_REQUEST['image_url'];
                break;
            
            case 'delete':
                $image_url = null;
                break;
            
            case 'reel':
                $reel = true;
                break;
        }

        eyo_updatePost($post_id, $image_url, $reel);
        
        $result = array();
        $result['type'] = "success";
        $result['response'] = "200";
        $result['data'] = $image_url;

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = strip_tags(json_encode($result));
            echo $result;
        } else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();
    }
}