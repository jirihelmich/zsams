<?php
/*
Plugin Name: Template plugin
Description: Custom ZS a MS template plugin
*/

/* Start Adding Functions Below this Line */

class GalleryWidget extends WP_Widget
{
    /** constructor */
    function __construct()
    {
        $widget_ops = array('classname' => 'gallery_widget', 'description' => __('WPPA+ Last Ten Uploaded Photos', 'wp-photo-album-plus'));
        parent::__construct('gallery_widget', __('Last Ten Photos', 'wp-photo-album-plus'), $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance)
    {
        global $wpdb;

        require_once(dirname(__FILE__) . '/../wp-photo-album-plus/wppa-links.php');
        require_once(dirname(__FILE__) . '/../wp-photo-album-plus/wppa-styles.php');
        require_once(dirname(__FILE__) . '/../wp-photo-album-plus/wppa-functions.php');
        require_once(dirname(__FILE__) . '/../wp-photo-album-plus/wppa-thumbnails.php');
        require_once(dirname(__FILE__) . '/../wp-photo-album-plus/wppa-boxes-html.php');
        require_once(dirname(__FILE__) . '/../wp-photo-album-plus/wppa-slideshow.php');
        wppa_initialize_runtime();

        wppa('in_widget', 'lasten');
        wppa_bump_mocc();

        extract($args);

        $instance = wp_parse_args((array)$instance, array(
            'title' => '',
            'album' => '',
            'albumenum' => '',
            'timesince' => 'yes',
            'display' => 'thumbs',
            'includesubs' => 'no',
        ));
        $widget_title = apply_filters('widget_title', $instance['title']);
        $page = in_array(wppa_opt('lasten_widget_linktype'), wppa('links_no_page')) ?
            '' :
            wppa_get_the_landing_page('lasten_widget_linkpage', __('Last Ten Uploaded Photos', 'wp-photo-album-plus'));
        $max = wppa_opt('lasten_count');
        $album = $instance['album'];
        $timesince = $instance['timesince'];
        $display = $instance['display'];
        $albumenum = $instance['albumenum'];
        $subs = $instance['includesubs'] == 'yes';

        switch ($album) {
            case '-99': // 'Multiple see below' is a list of id, seperated by comma's
                $album = str_replace(',', '.', $albumenum);
                if ($subs) {
                    $album = wppa_expand_enum(wppa_alb_to_enum_children($album));
                }
                $album = str_replace('.', ',', $album);
                break;
            case '0': // ---all---
                break;
            case '-2': // ---generic---
                $albs = $wpdb->get_results("SELECT `id` FROM `" . WPPA_ALBUMS . "` WHERE `a_parent` = '0'", ARRAY_A);
                $album = '';
                foreach ($albs as $alb) {
                    $album .= '.' . $alb['id'];
                }
                $album = ltrim($album, '.');
                if ($subs) {
                    $album = wppa_expand_enum(wppa_alb_to_enum_children($album));
                }
                $album = str_replace('.', ',', $album);
                break;
            default:
                if ($subs) {
                    $album = wppa_expand_enum(wppa_alb_to_enum_children($album));
                    $album = str_replace('.', ',', $album);
                }
                break;
        }
        $album = trim($album, ',');

        // Eiter look at timestamp or at date/time modified
        $order_by = wppa_switch('lasten_use_modified') ? 'modified' : 'timestamp';

        // If you want only 'New' photos in the selection, the period must be <> 0;
        if (wppa_switch('lasten_limit_new') && wppa_opt('max_photo_newtime')) {
            $newtime = " `" . $order_by . "` >= " . (time() - wppa_opt('max_photo_newtime'));
            if ($album) {
                $q = "SELECT * FROM `" . WPPA_PHOTOS . "` WHERE (" . $newtime . ") AND `album` IN ( " . $album . " ) AND ( `status` <> 'pending' AND `status` <> 'scheduled' ) ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
            } else {
                $q = "SELECT * FROM `" . WPPA_PHOTOS . "` WHERE (" . $newtime . ") AND `status` <> 'pending' AND `status` <> 'scheduled' ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
            }
        } else {
            if ($album) {
                $q = "SELECT * FROM `" . WPPA_PHOTOS . "` WHERE `album` IN ( " . $album . " ) AND ( `status` <> 'pending' AND `status` <> 'scheduled' ) ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
            } else {
                $q = "SELECT * FROM `" . WPPA_PHOTOS . "` WHERE `status` <> 'pending' AND `status` <> 'scheduled' ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
            }
        }

        $thumbs = $wpdb->get_results($q, ARRAY_A);

        $widget_content = "\n" . '<!-- WPPA+ LasTen Widget start -->';
        $maxw = wppa_opt('lasten_size');
        $maxh = $maxw;
        $lineheight = wppa_opt('fontsize_widget_thumb') * 1.5;
        $maxh += $lineheight;

        if ($timesince == 'yes') $maxh += $lineheight;

        $count = '0';

        if ($thumbs) foreach ($thumbs as $image) {

            $thumb = $image;
            $widget_content .= "\n" . '<div class="wppa-widget" >';
            if ($image) {
                $no_album = !$album;
                if ($no_album) $tit = __('View the most recent uploaded photos', 'wp-photo-album-plus'); else $tit = esc_attr(__(stripslashes($image['description'])));
                $link = wppa_get_imglnk_a('lasten', $image['id'], '', $tit, '', $no_album, str_replace(',', '.', $album));
                $file = wppa_get_thumb_path($image['id']);
                $imgstyle_a = wppa_get_imgstyle_a($image['id'], $file, $maxw, 'center', 'ltthumb');
                $imgurl = wppa_get_thumb_url($image['id'], '', $imgstyle_a['width'], $imgstyle_a['height']);
                $imgevents = wppa_get_imgevents('thumb', $image['id'], true);
                $title = $link ? esc_attr(stripslashes($link['title'])) : '';

                $imgstyle_a["width"] = "100%";
                $imgstyle_a["height"] = "auto";

                $widget_content .= "<a href=\"".$link['url']."\"><img src=\"$imgurl\"></a><br /><br />";

                $widget_content .= "\n\t" . '<div style="font-size:' . wppa_opt('fontsize_widget_thumb') . 'px; line-height:' . $lineheight . 'px;">';
                $widget_content .= '</div>';
            } else {    // No image
                $widget_content .= __('Photo not found', 'wp-photo-album-plus');
            }
            $widget_content .= "\n" . '</div>';
            $count++;
            if ($count == wppa_opt('lasten_count')) break;

        }
        else $widget_content .= __('There are no uploaded photos (yet)', 'wp-photo-album-plus');

        $widget_content .= '<div style="clear:both"></div>';
        $widget_content .= "\n" . '<!-- WPPA+ LasTen Widget end -->';

        echo "\n" . $before_widget;
        if (!empty($widget_title)) {
            echo $before_title . $widget_title . $after_title;
        }
        echo $widget_content . $after_widget;

        wppa('in_widget', false);
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['album'] = strval(intval($new_instance['album']));
        $instance['albumenum'] = $new_instance['albumenum'];
        if ($instance['album'] != '-99') $instance['albumenum'] = '';
        $instance['timesince'] = $new_instance['timesince'];
        $instance['display'] = $new_instance['display'];
        $instance['includesubs'] = $new_instance['includesubs'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance)
    {
    }

} // class LasTenWidget

// register LasTenWidget widget
add_action('widgets_init', 'zsms_template_register_gallery_widget');

function zsms_template_register_gallery_widget()
{
    register_widget("GalleryWidget");
}

/* Stop Adding Functions Below this Line */
?>