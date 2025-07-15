document.addEventListener('DOMContentLoaded', function() {
    // Get the notification icon element
    const notificationIcon = document.getElementById('notificationIcon');
    // Get the clear notifications button
    const clearNotificationsBtn = document.getElementById('clear-notifications-btn');
    
    if (notificationIcon) {
        // Get data from server-side session (passed via data attributes)
        const currentNotificationCount = parseInt(notificationIcon.getAttribute('data-count') || '0');
        const lastNotificationCount = parseInt(notificationIcon.getAttribute('data-last-count') || '0');
        const notificationsSeen = notificationIcon.getAttribute('data-seen') === 'true';
        
        // Check if we have pending invitations
        const hasPendingInvitations = currentNotificationCount > 0;
        
        // Check if we have new notifications (more than before)
        const hasNewNotifications = currentNotificationCount > lastNotificationCount;
        
        // Get the current page URL to check if we're on the notifications page
        const isNotificationsPage = window.location.href.includes('/notifications');
        
        // Handle click event to stop pulsing
        notificationIcon.addEventListener('click', function() {
            // Remove the pulse animation immediately when clicked
            this.classList.remove('pulse-animation');
            
            // We don't need to update session here as the controller will do that
            // when the notifications page loads
        });
        
        // If we're on the notifications page, no need to pulse
        if (isNotificationsPage) {
            notificationIcon.classList.remove('pulse-animation');
        }
        // If we have new notifications or unseen notifications, make it pulse
        else if (hasPendingInvitations && (hasNewNotifications || !notificationsSeen)) {
            notificationIcon.classList.add('pulse-animation');
        }
        // Otherwise, don't pulse
        else {
            notificationIcon.classList.remove('pulse-animation');
        }
    }
    
    // Handle clear notifications button
    if (clearNotificationsBtn) {
        clearNotificationsBtn.addEventListener('click', function() {
            // Use SweetAlert2 for confirmation
            Swal.fire({
                title: 'Limpar notificações?',
                text: 'Tem certeza que deseja limpar todas as notificações? Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--link-color)',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, limpar tudo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Get the CSRF token from meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    // Create form data with CSRF token
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    
                    // Send AJAX request to clear all notifications
                    fetch('/notifications/clear-all', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData,
                        credentials: 'same-origin'
                    })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message with SweetAlert2
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Remove all notification items
                        const notificationsContainer = document.querySelector('.notifications-container');
                        notificationsContainer.innerHTML = '<div class="empty-state"><p>Não há notificações para exibir.</p></div>';
                        
                        // Update notification count if the icon exists on this page
                        if (notificationIcon) {
                            notificationIcon.setAttribute('data-count', '0');
                            notificationIcon.setAttribute('data-last-count', '0');
                            notificationIcon.setAttribute('data-seen', 'true');
                            notificationIcon.classList.remove('pulse-animation');
                            
                            // Update the badge counter to 0
                            const badge = notificationIcon.querySelector('.badge');
                            if (badge) {
                                badge.textContent = '0';
                                badge.style.display = 'none';
                            }
                        }
                        
                        // No need for timeout as SweetAlert handles this
                    }
                })
                .catch(error => {
                    console.error('Error clearing notifications:', error);
                    
                    // Show error message with SweetAlert2
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Ocorreu um erro ao limpar as notificações. Por favor, tente novamente.',
                        icon: 'error',
                        confirmButtonColor: 'var(--link-color)'
                    });
                });
                }
            });
        });
    }
});
