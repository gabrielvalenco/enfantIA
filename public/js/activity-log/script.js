document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const activityLogModal = document.getElementById('activityLogModal');
    const activityLogList = document.getElementById('activity-log-list');
    const activityLogLoading = document.getElementById('activity-log-loading');
    const activityLogEmpty = document.getElementById('activity-log-empty');
    
    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('pt-BR', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit', 
            minute: '2-digit'
        });
    }
    
    // Get appropriate icon for activity
    function getActivityIcon(action) {
        const icons = {
            'create': 'fa-plus-circle text-success',
            'update': 'fa-edit text-info',
            'delete': 'fa-trash-alt text-danger',
            'complete': 'fa-check-circle text-success',
            'uncomplete': 'fa-times-circle text-warning',
            'login': 'fa-sign-in-alt text-primary',
            'logout': 'fa-sign-out-alt text-secondary'
        };
        
        return icons[action] || 'fa-history';
    }
    
    // Add event listener to modal
    if (activityLogModal) {
        activityLogModal.addEventListener('show.bs.modal', function() {
            // Reset state
            activityLogList.innerHTML = '';
            activityLogList.classList.add('d-none');
            activityLogEmpty.classList.add('d-none');
            activityLogLoading.classList.remove('d-none');
            
            // Fetch activity logs
            fetch('/activity-logs')
                .then(response => response.json())
                .then(data => {
                    activityLogLoading.classList.add('d-none');
                    
                    if (data.success && data.data && data.data.length > 0) {
                        // Display activity logs
                        activityLogList.classList.remove('d-none');
                        
                        data.data.forEach(activity => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item activity-log-item';
                            
                            const iconClass = getActivityIcon(activity.action);
                            const formattedDate = formatDate(activity.created_at);
                            
                            li.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <div class="activity-icon me-3">
                                        <i class="fas ${iconClass}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-description">
                                            ${activity.description}
                                        </div>
                                        <div class="activity-timestamp text-muted small">
                                            <i class="fas fa-clock me-1"></i>${formattedDate}
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            activityLogList.appendChild(li);
                        });
                    } else {
                        // Display empty message
                        activityLogEmpty.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error fetching activity logs:', error);
                    activityLogLoading.classList.add('d-none');
                    activityLogEmpty.classList.remove('d-none');
                    activityLogEmpty.innerHTML = '<p class="text-danger">Erro ao carregar o histórico de atividades.</p>';
                });
        });
    }
});
