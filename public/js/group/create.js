document.addEventListener('DOMContentLoaded', function() {
    const memberEmailInput = document.getElementById('memberEmail');
    const addMemberBtn = document.getElementById('addMemberBtn');
    const emailTagsContainer = document.getElementById('email-tags-container');
    const membersInput = document.getElementById('membersInput');
    const groupForm = document.getElementById('groupForm');
    const maxMembers = 10;
    const currentUserEmail = document.querySelector('meta[name="user-email"]').content;
    
    // Array para armazenar os emails adicionados
    let memberEmails = [];
    
    // Função para adicionar um email à lista
    function addMemberEmail() {
        const email = memberEmailInput.value.trim();
        
        // Validações básicas
        if (email === '') {
            return;
        }
        
        // Verificar se o email já foi adicionado
        if (memberEmails.includes(email)) {
            showToast('Este email já foi adicionado', 'warning');
            return;
        }
        
        // Verificar se é o email do usuário atual
        if (email.toLowerCase() === currentUserEmail.toLowerCase()) {
            showToast('Você não pode adicionar seu próprio email', 'warning');
            return;
        }
        
        // Verificar limite de membros
        if (memberEmails.length >= maxMembers) {
            showToast(`Limite de ${maxMembers} membros atingido`, 'warning');
            return;
        }
        
        // Validar formato do email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            showToast('Por favor, insira um email válido', 'error');
            return;
        }
        
        // Verificar se o usuário existe (via AJAX)
        checkUserExists(email).then(result => {
            if (result.exists) {
                if (result.inGroup) {
                    showToast('Este usuário já é membro do grupo', 'warning');
                } else {
                    // Adicionar email à lista
                    memberEmails.push(email);
                    updateEmailTags();
                    updateMembersInput();
                    memberEmailInput.value = '';
                    showToast('Membro adicionado com sucesso', 'success');
                }
            } else {
                showToast('Este usuário não existe no sistema', 'error');
            }
        }).catch(error => {
            console.error('Erro ao verificar usuário:', error);
            showToast('Erro ao verificar usuário', 'error');
        });
    }
    
    // Função para verificar se o usuário existe
    async function checkUserExists(email) {
        try {
            const response = await fetch(`/api/check-user?email=${encodeURIComponent(email)}`);
            return await response.json();
        } catch (error) {
            console.error('Erro ao verificar usuário:', error);
            throw error;
        }
    }
    
    // Função para atualizar os tags de email
    function updateEmailTags() {
        emailTagsContainer.innerHTML = '';
        
        memberEmails.forEach(email => {
            const tag = document.createElement('div');
            tag.className = 'email-tag';
            tag.innerHTML = `
                <span class="email-text">${email}</span>
                <button type="button" class="email-remove-btn" data-email="${email}">×</button>
            `;
            emailTagsContainer.appendChild(tag);
        });
        
        // Adicionar event listeners para os botões de remover
        document.querySelectorAll('.email-remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const emailToRemove = this.getAttribute('data-email');
                removeMemberEmail(emailToRemove);
            });
        });
    }
    
    // Função para remover um email da lista
    function removeMemberEmail(email) {
        memberEmails = memberEmails.filter(e => e !== email);
        updateEmailTags();
        updateMembersInput();
        showToast('Membro removido', 'info');
    }
    
    // Função para atualizar o input oculto com os emails
    function updateMembersInput() {
        membersInput.value = JSON.stringify(memberEmails);
    }
    
    // Função para exibir mensagens toast
    function showToast(message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        // Ícone baseado no tipo de toast
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        if (type === 'error') icon = 'times-circle';
        
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-${icon}"></i>
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Adicionar event listener para fechar o toast
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.style.animation = 'slideOut 0.3s forwards';
            setTimeout(() => {
                toast.remove();
            }, 300);
        });
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'slideOut 0.3s forwards';
                setTimeout(() => {
                    if (toast.parentNode) toast.remove();
                }, 300);
            }
        }, 5000);
    }
    
    // Event listeners
    addMemberBtn.addEventListener('click', addMemberEmail);
    
    memberEmailInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addMemberEmail();
        }
    });
    
    // Validação do formulário antes do envio
    groupForm.addEventListener('submit', function(e) {
        const nameInput = document.getElementById('name');
        
        if (nameInput.value.trim() === '') {
            e.preventDefault();
            nameInput.classList.add('is-invalid');
            showToast('O nome do grupo é obrigatório', 'error');
        }
    });
});