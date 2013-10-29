<?php

class Video
{

    public static function getFeaturedVideo($field_article_video)
    {
        if (isset($field_article_video[LANGUAGE_NONE][0]['nid'])) {
            $video_data = reset(Node::getNodes(array($field_article_video[LANGUAGE_NONE][0]['nid'])));
            if ($video_data) {
                return $video_data;
            }
        }
        return null;
    }

    public static function getYouTubeImageURL($node = null) {
        if (isset($node)) {
            if (isset($node->field_youtube_id[$node->language][0]['value'])) {
                return 'http://img.youtube.com/vi/' . $node->field_youtube_id[$node->language][0]['value'] . '/0.jpg';
            }
        }
        return false;
    }

    public static function renderEmbed($embed = null) {
        if (isset($embed))  {
            return '<div class="embed-wrap">' . html_entity_decode($embed) . '</div>';
        }
        return false;
    }

    public static function renderYouTubeEmbed($id = null, $width = 640, $height = 390) {
        if (isset($id)) {
            return '<iframe width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $id  . '" frameborder="0" allowfullscreen></iframe>';
        }
        return false;
    }

}

