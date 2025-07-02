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
    
    // Initialize tag input for languages
    initializeTagInput();
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

/**
 * Initializes the tag input functionality for the languages field
 */
function initializeTagInput() {
    const container = document.getElementById('languages-container');
    if (!container) return;
    
    const input = document.getElementById('language-input');
    const tagsContainer = document.getElementById('language-tags');
    const hiddenInput = document.getElementById('languages-hidden');
    
    // Initialize with existing values if any
    if (hiddenInput.value) {
        console.log('Existing languages:', hiddenInput.value);
        const existingLanguages = hiddenInput.value.split(',').map(lang => lang.trim()).filter(lang => lang);
        existingLanguages.forEach(language => {
            if (language) addTag(language);
        });
    }
    
    // Add event listener for the Enter key
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const value = input.value.trim();
            
            if (value && getTagsCount() < 3) {
                addTag(value);
                input.value = '';
            }
        }
    });
    
    // Function to add a new tag
    function addTag(text) {
        // Check if we already have 3 tags
        if (getTagsCount() >= 3) return;
        
        const tag = document.createElement('div');
        tag.className = 'language-tag';
        tag.innerHTML = `
            <span class="tag-text">${text}</span>
            <span class="tag-remove" title="Remover">×</span>
        `;
        
        // Add event listener to remove tag
        tag.querySelector('.tag-remove').addEventListener('click', function() {
            tagsContainer.removeChild(tag);
            updateHiddenInput();
            
            // Show the input if we have less than 3 tags
            if (getTagsCount() < 3) {
                input.style.display = 'block';
            }
        });
        
        tagsContainer.appendChild(tag);
        updateHiddenInput();
        
        // Hide the input if we have 3 tags
        if (getTagsCount() >= 3) {
            input.style.display = 'none';
        }
    }
    
    // Function to update the hidden input with all tags
    function updateHiddenInput() {
        const tags = Array.from(tagsContainer.querySelectorAll('.language-tag .tag-text'))
            .map(tag => tag.textContent.trim());
        hiddenInput.value = tags.join(',');
    }
    
    // Function to get the current number of tags
    function getTagsCount() {
        return tagsContainer.querySelectorAll('.language-tag').length;
    }
}
