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

// Função para converter cores hexadecimais para RGB
function hexToRgb(hex) {
    // Remove o # se estiver presente
    hex = hex.replace(/^#/, '');
    
    // Converte para valores RGB
    let bigint = parseInt(hex, 16);
    return {
        r: (bigint >> 16) & 255,
        g: (bigint >> 8) & 255,
        b: bigint & 255
    };
}

document.addEventListener('DOMContentLoaded', function() {
    
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            events: window.calendarEvents || [],  // Use window.calendarEvents if it exists, otherwise use empty array
            headerToolbar: {
                left: '',
                center: 'title',
                right: 'prev,next today' // Movido os botões de navegação para a direita
            },
            dayMaxEvents: true, // Permite "mais" link quando há muitos eventos
            eventDidMount: function(info) {
                // Estilização do evento no calendário
                info.el.style.borderLeft = '8px solid ' + info.event.backgroundColor;
                info.el.style.backgroundColor = 'rgba(' + 
                    hexToRgb(info.event.backgroundColor).r + ',' +
                    hexToRgb(info.event.backgroundColor).g + ',' +
                    hexToRgb(info.event.backgroundColor).b + ', 0.1)';
                
                // Adicionar ícone de urgência baseado na prioridade da tarefa
                const urgencyIcon = document.createElement('i');
                const urgency = info.event.extendedProps.urgency;
                
                if (urgency === 'Alta') {
                    urgencyIcon.className = 'fas fa-exclamation-circle me-1';
                    urgencyIcon.style.color = '#dc3545';
                } else if (urgency === 'Média') {
                    urgencyIcon.className = 'fas fa-exclamation me-1';
                    urgencyIcon.style.color = '#ffc107';
                } else {
                    urgencyIcon.className = 'fas fa-check-circle me-1';
                    urgencyIcon.style.color = '#20ac82';
                }
                
                // Adicionar o ícone ao título do evento se possível
                const titleEl = info.el.querySelector('.fc-event-title');
                if (titleEl) {
                    titleEl.prepend(urgencyIcon);
                }
                
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
                
                // Exibir subtarefas se existirem
                const subtasksList = document.getElementById('subtasksList');
                const noSubtasks = document.getElementById('noSubtasks');
                const subtasksContainer = document.getElementById('subtasksContainer');
                
                // Limpar lista de subtarefas
                subtasksList.innerHTML = '';
                
                // Verificar se existem subtarefas
                if (props.subtasks && props.subtasks.length > 0) {
                    subtasksContainer.classList.remove('d-none');
                    noSubtasks.classList.add('d-none');
                    
                    // Adicionar cada subtarefa à lista
                    props.subtasks.forEach((subtask, index) => {
                        const subtaskItem = document.createElement('div');
                        subtaskItem.className = 'subtask-item';
                        
                        const subtaskTitle = document.createElement('div');
                        subtaskTitle.className = 'subtask-title';
                        subtaskTitle.textContent = `Subtarefa #${index + 1}`;
                        
                        const subtaskDescription = document.createElement('p');
                        subtaskDescription.className = 'subtask-description';
                        subtaskDescription.textContent = subtask.description || 'Sem descrição';
                        
                        subtaskItem.appendChild(subtaskTitle);
                        subtaskItem.appendChild(subtaskDescription);
                        subtasksList.appendChild(subtaskItem);
                    });
                } else {
                    subtasksContainer.classList.remove('d-none');
                    noSubtasks.classList.remove('d-none');
                }
                
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
        
        // Função para substituir todos os links "mais" no documento
        function replaceMoreLinks() {
            // Encontrar todos os links "mais"
            const moreLinks = document.querySelectorAll('.fc-daygrid-more-link, .fc-more-link');
            
            moreLinks.forEach(function(link) {
                // Extrair o número do texto
                const text = link.textContent || link.innerText;
                const matches = text.match(/\d+/);
                if (matches && matches[0]) {
                    const number = matches[0];
                    
                    // Criar um elemento para o número de tarefas
                    const taskCountSpan = document.createElement('span');
                    taskCountSpan.className = 'task-count';
                    taskCountSpan.textContent = number;
                    taskCountSpan.style.position = 'absolute';
                    taskCountSpan.style.top = '-8px';
                    taskCountSpan.style.right = '-8px';
                    taskCountSpan.style.backgroundColor = '#dc3545';
                    taskCountSpan.style.color = 'white';
                    taskCountSpan.style.borderRadius = '50%';
                    taskCountSpan.style.width = '18px';
                    taskCountSpan.style.height = '18px';
                    taskCountSpan.style.fontSize = '0.75rem';
                    taskCountSpan.style.display = 'flex';
                    taskCountSpan.style.alignItems = 'center';
                    taskCountSpan.style.justifyContent = 'center';
                    taskCountSpan.style.fontWeight = 'bold';
                    taskCountSpan.style.boxShadow = '0 1px 3px rgba(0,0,0,0.3)';
                    
                    // Limpar o link e adicionar o símbolo + e o contador
                    link.innerHTML = '+';
                    link.appendChild(taskCountSpan);
                    
                    // Adicionar um atributo de dados para o número de tarefas
                    link.setAttribute('data-task-count', number);
                    
                    // Adicionar um tooltip
                    link.setAttribute('title', number + ' tarefas nesta data');
                }
            });
        }
        
        // Função para corrigir os títulos dos popovers
        function fixPopoverTitles() {
            setTimeout(function() {
                // Encontrar todos os popovers abertos
                const popovers = document.querySelectorAll('.fc-popover');
                popovers.forEach(function(popover) {
                    const popoverTitle = popover.querySelector('.fc-popover-title');
                    if (popoverTitle) {
                        // Extrair apenas a data e remover a palavra "mais"
                        const fullText = popoverTitle.textContent;
                        const dateText = fullText.replace(/mais/gi, '').trim();
                        
                        // Adicionar o atributo data-date para uso no CSS
                        popoverTitle.setAttribute('data-date', dateText);
                        
                        // Também atualizar o texto diretamente
                        popoverTitle.textContent = dateText;
                    }
                });
            }, 10);
        }
        
        // Observar o DOM para detectar quando novos elementos são adicionados
        const observer = new MutationObserver(function(mutations) {
            let needsUpdate = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    // Verificar se algum dos nós adicionados é relevante para nós
                    for (let i = 0; i < mutation.addedNodes.length; i++) {
                        const node = mutation.addedNodes[i];
                        if (node.nodeType === 1) { // Elemento
                            if (node.classList && 
                                (node.classList.contains('fc-popover') || 
                                 node.classList.contains('fc-daygrid-more-link') || 
                                 node.classList.contains('fc-more-link') ||
                                 node.querySelector('.fc-popover-title') ||
                                 node.querySelector('.fc-daygrid-more-link'))) {
                                needsUpdate = true;
                                break;
                            }
                        }
                    }
                }
            });
            
            if (needsUpdate) {
                replaceMoreLinks();
                fixPopoverTitles();
            }
        });
        
        // Iniciar a observação do documento
        observer.observe(document.body, { childList: true, subtree: true });
        
        // Adicionar listeners para os botões de navegação
        document.addEventListener('click', function(e) {
            if (e.target.closest('.fc-prev-button, .fc-next-button, .fc-today-button')) {
                setTimeout(function() {
                    replaceMoreLinks();
                    fixPopoverTitles();
                }, 100);
            }
        });
        
        // Executar as funções inicialmente
        replaceMoreLinks();
        fixPopoverTitles();
        
        // Executar novamente após um curto período para garantir que todos os elementos foram carregados
        setTimeout(function() {
            replaceMoreLinks();
            fixPopoverTitles();
        }, 500);
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
