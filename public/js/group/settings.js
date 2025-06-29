/**
 * Script para gerenciar as configurações do grupo
 * Integração completa com o backend via AJAX
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos da interface
    const competitiveMode = document.getElementById('competitiveMode');
    const allowMembersInvite = document.getElementById('allowMembersInvite');
    const allowMembersCreateTasks = document.getElementById('allowMembersCreateTasks');
    const saveSettingsBtn = document.getElementById('saveSettings');
    const rankingList = document.querySelector('.ranking-list');
    
    // Obter ID do grupo atual da URL
    const groupId = getGroupIdFromUrl();
    
    // Se estamos na página de configurações de grupo, carregar as configurações
    if (competitiveMode && groupId) {
        loadGroupSettings(groupId);
    }
    
    // Função para mostrar toast de notificação
    function showToast(message, type = 'success') {
        const toastContainer = document.querySelector('.toast-container');
        
        const toastElement = document.createElement('div');
        toastElement.classList.add('toast', 'show', `bg-${type}`);
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');
        
        const toastBody = document.createElement('div');
        toastBody.classList.add('toast-body', 'd-flex', 'align-items-center');
        
        // Ícone baseado no tipo de toast
        let icon = 'check-circle';
        if (type === 'danger') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        if (type === 'info') icon = 'info-circle';
        
        toastBody.innerHTML = `
            <i class="fas fa-${icon} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        `;
        
        toastElement.appendChild(toastBody);
        toastContainer.appendChild(toastElement);
        
        // Remover o toast após 5 segundos
        setTimeout(() => {
            toastElement.classList.remove('show');
            setTimeout(() => {
                toastContainer.removeChild(toastElement);
            }, 300);
        }, 5000);
    }
    
    /**
     * Função para extrair o ID do grupo da URL
     * Se estivermos na página de criação, retorna null
     */
    function getGroupIdFromUrl() {
        // Se estamos na página de criação
        if (window.location.pathname.includes('/groups/create')) {
            return null;
        }
        
        // Se estamos na página de edição ou visualização de um grupo
        const matches = window.location.pathname.match(/\/groups\/([0-9]+)/);
        return matches ? matches[1] : null;
    }
    
    /**
     * Carrega as configurações do grupo do backend
     */
    function loadGroupSettings(groupId) {
        // Obter o token CSRF
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/groups/${groupId}/settings`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar os switches com os valores do backend
                competitiveMode.checked = data.data.competitive_mode;
                allowMembersInvite.checked = data.data.allow_member_invite;
                allowMembersCreateTasks.checked = data.data.allow_member_tasks;
                
                // Desabilitar edição se o usuário não for administrador
                if (!data.data.can_edit) {
                    competitiveMode.disabled = true;
                    allowMembersInvite.disabled = true;
                    allowMembersCreateTasks.disabled = true;
                    saveSettingsBtn.disabled = true;
                    
                    // Adicionar mensagem de informação
                    const settingsContainer = document.querySelector('.settings-container');
                    const infoAlert = document.createElement('div');
                    infoAlert.className = 'alert alert-info mt-3';
                    infoAlert.innerHTML = '<i class="fas fa-info-circle me-2"></i>Apenas administradores podem alterar configurações do grupo.';
                    settingsContainer.prepend(infoAlert);
                }
                
                // Atualizar o ranking se o modo competitivo estiver ativo
                if (data.data.competitive_mode && rankingList && data.data.top_members.length > 0) {
                    updateRankingDisplay(data.data.top_members);
                }
            } else {
                showToast(data.message || 'Erro ao carregar configurações', 'danger');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar configurações:', error);
            showToast('Erro ao carregar configurações do grupo', 'danger');
        });
    }
    
    /**
     * Salva as configurações do grupo no backend
     */
    function saveGroupSettings(groupId) {
        // Obter o token CSRF
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Preparar os dados para enviar
        const settings = {
            competitive_mode: competitiveMode.checked,
            allow_member_invite: allowMembersInvite.checked,
            allow_member_tasks: allowMembersCreateTasks.checked
        };
        
        fetch(`/groups/${groupId}/settings`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify(settings)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Configurações salvas com sucesso!', 'success');
                
                // Se o modo competitivo foi ativado, carregamos os dados novamente para atualizar o ranking
                if (settings.competitive_mode) {
                    loadGroupSettings(groupId);
                }
            } else {
                showToast(data.message || 'Erro ao salvar configurações', 'danger');
            }
        })
        .catch(error => {
            console.error('Erro ao salvar configurações:', error);
            showToast('Erro ao salvar configurações do grupo', 'danger');
        });
    }
    
    /**
     * Atualiza a exibição do ranking com dados reais
     */
    function updateRankingDisplay(members) {
        if (!rankingList || !members.length) return;
        
        // Limpar o conteúdo atual
        rankingList.innerHTML = '';
        
        // Adicionar cada membro ao ranking
        members.forEach((member, index) => {
            const position = index + 1;
            const rankItem = document.createElement('div');
            rankItem.className = 'ranking-item d-flex align-items-center mb-2';
            
            rankItem.innerHTML = `
                <div class="rank-badge rank-${position} me-2">${position}</div>
                <div class="rank-avatar me-2">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="rank-info">
                    <span class="rank-name">${member.name}</span>
                    <div class="rank-stats">
                        <span class="badge bg-success">${member.tasks_completed} tarefas</span>
                    </div>
                </div>
            `;
            
            rankingList.appendChild(rankItem);
        });
        
        // Se não houver membros suficientes, mostrar mensagem
        if (members.length === 0) {
            const emptyMessage = document.createElement('div');
            emptyMessage.className = 'text-muted text-center py-3';
            emptyMessage.innerHTML = 'Nenhuma tarefa completada ainda.';
            rankingList.appendChild(emptyMessage);
        }
    }
    
    // Event listeners para os switches - eventos temporarios até salvar
    if (competitiveMode) {
        competitiveMode.addEventListener('change', function() {
            const status = this.checked ? 'ativado' : 'desativado';
            showToast(`Modo competitivo ${status}. Clique em Salvar para aplicar.`, 'info');
        });
    }
    
    if (allowMembersInvite) {
        allowMembersInvite.addEventListener('change', function() {
            const status = this.checked ? 'ativada' : 'desativada';
            showToast(`Permissão para membros adicionarem novos membros ${status}. Clique em Salvar para aplicar.`, 'info');
        });
    }
    
    if (allowMembersCreateTasks) {
        allowMembersCreateTasks.addEventListener('change', function() {
            const status = this.checked ? 'ativada' : 'desativada';
            showToast(`Permissão para membros criarem tarefas ${status}. Clique em Salvar para aplicar.`, 'info');
        });
    }
    
    // Event listener para o botão de salvar
    if (saveSettingsBtn) {
        saveSettingsBtn.addEventListener('click', function() {
            const groupId = getGroupIdFromUrl();
            
            if (!groupId) {
                showToast('Erro: ID do grupo não encontrado', 'danger');
                return;
            }
            
            // Desabilitar o botão durante o salvamento
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
            
            // Chamar a função para salvar as configurações
            saveGroupSettings(groupId);
            
            // Animação visual do botão
            this.innerHTML = '<i class="fas fa-check me-2"></i>Configurações Salvas';
            this.classList.add('btn-success');
            this.classList.remove('btn-primary');
            
            // Reativar o botão após um tempo
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Configurações';
                this.classList.add('btn-primary');
                this.classList.remove('btn-success');
            }, 3000);
        });
    }
});
