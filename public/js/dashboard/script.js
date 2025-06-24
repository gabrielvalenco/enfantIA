function confirmComplete(taskId, taskTitle) {
    if (confirm(`Deseja marcar a tarefa "${taskTitle}" como concluída?`)) {
        document.getElementById(`complete-form-${taskId}`).submit();
    }
}

function completeTask(taskId) {
    if (confirm('Deseja marcar esta tarefa como concluída?')) {
        document.getElementById(`complete-form-${taskId}`).submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const themeIcon = themeToggle.querySelector('i');
        
        // Check if user previously set a theme preference
        const currentTheme = localStorage.getItem('theme') || 'dark';
        
        // Apply the saved theme or default to dark
        document.documentElement.setAttribute('data-theme', currentTheme);
        updateThemeIcon(currentTheme);
        
        // Toggle theme when button is clicked
        themeToggle.addEventListener('click', function() {
            // Get current theme
            const currentTheme = document.documentElement.getAttribute('data-theme');
            
            // Switch to the opposite theme
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Update the theme
            document.documentElement.setAttribute('data-theme', newTheme);
            
            // Save the theme preference
            localStorage.setItem('theme', newTheme);
            
            // Update the icon
            updateThemeIcon(newTheme);
        });
    }
    
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            events: calendarEvents,
            eventDidMount: function(info) {
                info.el.style.borderLeft = '8px solid ' + info.event.backgroundColor;
                info.el.style.backgroundColor = 'rgba(' + 
                    hexToRgb(info.event.backgroundColor).r + ',' +
                    hexToRgb(info.event.backgroundColor).g + ',' +
                    hexToRgb(info.event.backgroundColor).b + ', 0.1)';
                
                // Adicionar categorias ao título para exibição no tooltip
                const categories = info.event.extendedProps.categories;
                let tooltipTitle = info.event.title + '\n';
                
                if (categories && categories.length > 0) {
                    tooltipTitle += '\nCategorias: ' + categories.join(', ');
                } else {
                    tooltipTitle += '\nCategoria: Sem categoria';
                }
                
                tooltipTitle += '\nUrgência: ' + info.event.extendedProps.urgency;
                tooltipTitle += '\nData: ' + new Date(info.event.start).toLocaleDateString('pt-BR');
                info.el.title = tooltipTitle;
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                const event = info.event;
                const props = event.extendedProps;
                
                document.getElementById('taskModalLabel').textContent = event.title;
                
                // Exibir todas as categorias da tarefa com cores
                const categoryElement = document.getElementById('taskCategory');
                categoryElement.innerHTML = '';
                
                if (props.categories && props.categories.length > 0) {
                    // Buscar as cores das categorias no evento
                    const categoryColors = window.categoryColors || {};
                    
                    props.categories.forEach((category, index) => {
                        const categorySpan = document.createElement('span');
                        categorySpan.textContent = category;
                        categorySpan.style.color = categoryColors[category] || 'var(--link-color)';
                        categorySpan.style.fontWeight = 'bold';
                        
                        categoryElement.appendChild(categorySpan);
                        
                        // Adicionar vírgula se não for o último item
                        if (index < props.categories.length - 1) {
                            categoryElement.appendChild(document.createTextNode(', '));
                        }
                    });
                } else {
                    categoryElement.textContent = 'Sem categoria';
                    categoryElement.style.color = '#6c757d';
                    categoryElement.style.fontWeight = 'bold';
                }
                
                document.getElementById('taskDate').textContent = new Date(event.start).toLocaleDateString('pt-BR');
                document.getElementById('taskDescription').textContent = props.description;
                
                // Exibir urgência com cor correspondente
                const urgencyElement = document.getElementById('taskUrgency');
                urgencyElement.textContent = props.urgency;
                
                // Definir a cor do texto de acordo com a urgência
                if (props.urgency === 'Alta') {
                    urgencyElement.style.color = '#dc3545';
                } else if (props.urgency === 'Média') {
                    urgencyElement.style.color = '#ffc107';
                } else {
                    urgencyElement.style.color = '#20ac82';
                }
                urgencyElement.style.fontWeight = 'bold';
                
                // Update view task button with the proper task ID
                const viewTaskBtn = document.querySelector('.view-task-btn');
                if (viewTaskBtn) {
                    viewTaskBtn.setAttribute('data-task-id', event.id);
                    viewTaskBtn.href = viewTaskBtn.href.replace(':taskId', event.id);
                }
                
                new bootstrap.Modal(document.getElementById('taskModal')).show();
            }
        });
        calendar.render();
    }

    const completeTaskBtn = document.getElementById('completeTaskBtn');
    if (completeTaskBtn) {
        completeTaskBtn.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            if (taskId) {
                completeTask(taskId);
            }
        });
    }
});

function deleteTask(taskId, deleteUrl) {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;
        form.style.display = 'none';

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

// Function to update the theme icon
function updateThemeIcon(theme) {
    const themeIcon = document.querySelector('#theme-toggle i');
    if (!themeIcon) return;
    
    if (theme === 'dark') {
        themeIcon.className = 'fas fa-sun';
    } else {
        themeIcon.className = 'fas fa-moon';
    }
}