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

document.addEventListener('DOMContentLoaded', function() {
    // Processa botões de completar tarefa na dashboard
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
