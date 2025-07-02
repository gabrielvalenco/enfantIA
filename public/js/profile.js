/**
 * Profile page JavaScript functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize any existing social media links
    const socialInputs = document.querySelectorAll('.social-link-input');
    socialInputs.forEach(input => {
        if (input.value) {
            const iconId = input.getAttribute('data-icon-id') || input.id.replace('social-link', 'social-icon');
            detectSocialMedia(input, iconId);
        }
    });
});

/**
 * Detects the social media platform from a URL and displays the appropriate icon
 * @param {HTMLElement} inputElement - The input element containing the URL
 * @param {string} iconElementId - The ID of the element where the icon should be displayed
 */
function detectSocialMedia(inputElement, iconElementId) {
    const url = inputElement.value.trim().toLowerCase();
    const iconElement = document.getElementById(iconElementId);
    
    if (!url) {
        // Clear the icon if the input is empty
        iconElement.innerHTML = '';
        return;
    }
    
    // Define patterns for common social media platforms
    const socialPatterns = [
        { pattern: /(linkedin\.com|lnkd\.in)/i, icon: 'fab fa-linkedin', name: 'LinkedIn' },
        { pattern: /(github\.com|github\.io)/i, icon: 'fab fa-github', name: 'GitHub' },
        { pattern: /(twitter\.com|x\.com)/i, icon: 'fab fa-twitter', name: 'Twitter' },
        { pattern: /(instagram\.com|instagr\.am)/i, icon: 'fab fa-instagram', name: 'Instagram' },
        { pattern: /(facebook\.com|fb\.com|fb\.me)/i, icon: 'fab fa-facebook', name: 'Facebook' },
        { pattern: /(youtube\.com|youtu\.be)/i, icon: 'fab fa-youtube', name: 'YouTube' },
        { pattern: /(medium\.com)/i, icon: 'fab fa-medium', name: 'Medium' },
        { pattern: /(stackoverflow\.com)/i, icon: 'fab fa-stack-overflow', name: 'Stack Overflow' },
        { pattern: /(dribbble\.com)/i, icon: 'fab fa-dribbble', name: 'Dribbble' },
        { pattern: /(behance\.net)/i, icon: 'fab fa-behance', name: 'Behance' },
        { pattern: /(pinterest\.com)/i, icon: 'fab fa-pinterest', name: 'Pinterest' },
        { pattern: /(reddit\.com)/i, icon: 'fab fa-reddit', name: 'Reddit' },
        { pattern: /(twitch\.tv)/i, icon: 'fab fa-twitch', name: 'Twitch' },
        { pattern: /(discord\.com|discord\.gg)/i, icon: 'fab fa-discord', name: 'Discord' },
        { pattern: /(tiktok\.com)/i, icon: 'fab fa-tiktok', name: 'TikTok' }
    ];
    
    // Check if the URL matches any of the patterns
    for (const social of socialPatterns) {
        if (social.pattern.test(url)) {
            iconElement.innerHTML = `<i class="${social.icon}" title="${social.name}"></i>`;
            return;
        }
    }
    
    // Default icon for unrecognized URLs
    iconElement.innerHTML = '<i class="fas fa-link" title="Link"></i>';
}
