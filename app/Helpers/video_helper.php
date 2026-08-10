<?php
/**
 * EduCMS Video Helper (RC5-005 — Modul Video)
 *
 * Centralizes YouTube ID extraction so the same logic isn't duplicated
 * between the admin Videos module (auto-thumbnail preview) and the
 * portal Home controller (existing single video_profile_url embed).
 * No external API call is ever made — YouTube's public thumbnail CDN
 * (img.youtube.com) accepts a predictable URL pattern for any public
 * video ID, so "auto-fetch" here just means "compute a predictable URL",
 * not an HTTP request at save/render time.
 */

if (!function_exists('extract_youtube_id')) {
    function extract_youtube_id($url) {
        if (empty($url)) {
            return '';
        }
        if (preg_match('/youtu\.be\/([A-Za-z0-9_-]{6,})/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/[?&]v=([A-Za-z0-9_-]{6,})/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtube\.com\/embed\/([A-Za-z0-9_-]{6,})/', $url, $m)) {
            return $m[1];
        }
        return '';
    }
}

if (!function_exists('youtube_embed_url')) {
    /**
     * Turn any recognized YouTube URL format into a valid <iframe> embed
     * src. Returns the original value unchanged for non-YouTube /
     * unrecognized links so it can still be dropped into a generic
     * <iframe> (Vimeo/Facebook/TikTok already provide their own embed
     * markup/URLs when the admin pastes them).
     */
    function youtube_embed_url($url) {
        $id = extract_youtube_id($url);
        if (empty($id)) {
            return $url;
        }
        return 'https://www.youtube.com/embed/' . $id;
    }
}

if (!function_exists('resolve_video_thumbnail')) {
    /**
     * Hybrid thumbnail resolution (RC5-005):
     *   1. Admin-set override (`thumbnail` column, a Media Library path)
     *      always wins when present.
     *   2. Otherwise, for platform=youtube, auto-derive the public
     *      hqdefault.jpg thumbnail from the video URL — no API/key
     *      needed, no upload, nothing stored.
     *   3. Otherwise (Vimeo/Facebook/TikTok/other with no override),
     *      return '' and let the view show a generic placeholder icon.
     *
     * $thumbnail_path: value of videos.thumbnail (may be empty)
     * $platform: value of videos.platform
     * $video_url: value of videos.video_url
     * Returns an absolute, ready-to-use <img src> URL, or '' .
     */
    function resolve_video_thumbnail($thumbnail_path, $platform, $video_url) {
        if (!empty($thumbnail_path)) {
            return base_url($thumbnail_path);
        }
        if ($platform === 'youtube') {
            $id = extract_youtube_id($video_url);
            if (!empty($id)) {
                return 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg';
            }
        }
        return '';
    }
}
