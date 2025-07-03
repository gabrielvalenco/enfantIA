<?php

namespace App\Helpers;

/**
 * Helper functions for social media links
 */
class SocialMediaHelper
{
    /**
     * Get the appropriate Font Awesome icon class for a social media URL
     * 
     * @param string $url The social media URL
     * @return string The Font Awesome icon class
     */
    public static function getSocialIcon($url)
    {
        $url = strtolower($url);
        
        if (strpos($url, 'linkedin.com') !== false || strpos($url, 'lnkd.in') !== false) {
            return 'fab fa-linkedin';
        } elseif (strpos($url, 'github.com') !== false || strpos($url, 'github.io') !== false) {
            return 'fab fa-github';
        } elseif (strpos($url, 'twitter.com') !== false || strpos($url, 'x.com') !== false) {
            return 'fab fa-twitter';
        } elseif (strpos($url, 'instagram.com') !== false || strpos($url, 'instagr.am') !== false) {
            return 'fab fa-instagram';
        } elseif (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.com') !== false || strpos($url, 'fb.me') !== false) {
            return 'fab fa-facebook';
        } elseif (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'fab fa-youtube';
        } elseif (strpos($url, 'medium.com') !== false) {
            return 'fab fa-medium';
        } elseif (strpos($url, 'stackoverflow.com') !== false) {
            return 'fab fa-stack-overflow';
        } elseif (strpos($url, 'dribbble.com') !== false) {
            return 'fab fa-dribbble';
        } elseif (strpos($url, 'behance.net') !== false) {
            return 'fab fa-behance';
        } elseif (strpos($url, 'pinterest.com') !== false) {
            return 'fab fa-pinterest';
        } elseif (strpos($url, 'reddit.com') !== false) {
            return 'fab fa-reddit';
        } elseif (strpos($url, 'twitch.tv') !== false) {
            return 'fab fa-twitch';
        } elseif (strpos($url, 'discord.com') !== false || strpos($url, 'discord.gg') !== false) {
            return 'fab fa-discord';
        } elseif (strpos($url, 'tiktok.com') !== false) {
            return 'fab fa-tiktok';
        }
        
        // Default icon for unrecognized URLs
        return 'fas fa-link';
    }
    
    /**
     * Get a display name for a social media URL
     * 
     * @param string $url The social media URL
     * @return string A display name for the URL
     */
    public static function getSocialName($url)
    {
        $url = strtolower($url);
        
        if (strpos($url, 'linkedin.com') !== false || strpos($url, 'lnkd.in') !== false) {
            return 'LinkedIn';
        } elseif (strpos($url, 'github.com') !== false || strpos($url, 'github.io') !== false) {
            return 'GitHub';
        } elseif (strpos($url, 'twitter.com') !== false) {
            return 'Twitter';
        } elseif (strpos($url, 'x.com') !== false) {
            return 'X';
        } elseif (strpos($url, 'instagram.com') !== false || strpos($url, 'instagr.am') !== false) {
            return 'Instagram';
        } elseif (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.com') !== false || strpos($url, 'fb.me') !== false) {
            return 'Facebook';
        } elseif (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'YouTube';
        } elseif (strpos($url, 'medium.com') !== false) {
            return 'Medium';
        } elseif (strpos($url, 'stackoverflow.com') !== false) {
            return 'Stack Overflow';
        } elseif (strpos($url, 'dribbble.com') !== false) {
            return 'Dribbble';
        } elseif (strpos($url, 'behance.net') !== false) {
            return 'Behance';
        } elseif (strpos($url, 'pinterest.com') !== false) {
            return 'Pinterest';
        } elseif (strpos($url, 'reddit.com') !== false) {
            return 'Reddit';
        } elseif (strpos($url, 'twitch.tv') !== false) {
            return 'Twitch';
        } elseif (strpos($url, 'discord.com') !== false || strpos($url, 'discord.gg') !== false) {
            return 'Discord';
        } elseif (strpos($url, 'tiktok.com') !== false) {
            return 'TikTok';
        }
        
        // For unrecognized URLs, extract the domain
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['host'])) {
            return $parsedUrl['host'];
        }
        
        return 'Link';
    }
}
