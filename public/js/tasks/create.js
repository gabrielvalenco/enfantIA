// Validação de categorias
function validateCategorySelection(checkbox) {
    const maxCategories = 3;
    const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
    const warningElement = document.querySelector('.category-warning');
    
    if (checkedBoxes.length > maxCategories) {
        checkbox.checked = false;
        if (warningElement) {
            warningElement.classList.remove('d-none');
        } else {
            // Create warning if it doesn't exist
            const warning = document.createElement('div');
            warning.className = 'category-warning text-danger mt-2';
            warning.textContent = 'Você pode selecionar no máximo 3 categorias.';
            
            const categoryContainer = document.querySelector('.category-container');
            if (categoryContainer) {
                categoryContainer.appendChild(warning);
            }
        }
    } else if (warningElement) {
        warningElement.classList.add('d-none');
    }
}

// Customize date input and add validation for preventing past dates
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('due_date');
    
    if (dateInput) {
        // Set default datetime to current time + 1 hour
        const tomorrow = new Date();
        tomorrow.setHours(tomorrow.getHours() + 1);
        tomorrow.setMinutes(Math.ceil(tomorrow.getMinutes() / 5) * 5); // Round to nearest 5 min
        
        // Format to YYYY-MM-DDThh:mm
        const year = tomorrow.getFullYear();
        const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const day = String(tomorrow.getDate()).padStart(2, '0');
        const hours = String(tomorrow.getHours()).padStart(2, '0');
        const minutes = String(tomorrow.getMinutes()).padStart(2, '0');
        
        // Set as default value
        dateInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        // Set min attribute to today's date
        const today = new Date();
        const todayYear = today.getFullYear();
        const todayMonth = String(today.getMonth() + 1).padStart(2, '0');
        const todayDay = String(today.getDate()).padStart(2, '0');
        const todayHours = String(today.getHours()).padStart(2, '0');
        const todayMinutes = String(today.getMinutes()).padStart(2, '0');
        
        // Set min attribute to current datetime
        dateInput.min = `${todayYear}-${todayMonth}-${todayDay}T${todayHours}:${todayMinutes}`;
        
        // Add event listener to validate date
        dateInput.addEventListener('change', validateDateTime);
        
        // Validate the date on form submission
        function validateDateTime() {
            const selectedDate = new Date(dateInput.value);
            const currentDate = new Date();
            
            if (selectedDate < currentDate) {
                dateInput.setCustomValidity('Não é possível criar tarefas com datas passadas');
            } else {
                dateInput.setCustomValidity('');
            }
        }
    }

    // Form validation
    const taskForm = document.getElementById('task-form');
    if (taskForm) {
        taskForm.addEventListener('submit', function(event) {
            if (!taskForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            taskForm.classList.add('was-validated');
        });
    }
});

// Character counter functionality
function updateCharCount(inputElement, countElement, maxLength) {
    const currentLength = inputElement.value.length;
    countElement.textContent = currentLength;
    
    if (currentLength >= maxLength) {
        countElement.classList.add('text-danger');
    } else {
        countElement.classList.remove('text-danger');
    }
}

// Category selection and subtask functionality
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for title and description
    const titleInput = document.getElementById('title');
    const titleCount = document.getElementById('title-count');
    const descriptionInput = document.getElementById('description');
    const descriptionCount = document.getElementById('description-count');
    
    if (titleInput && titleCount) {
        titleInput.addEventListener('input', function() {
            updateCharCount(this, titleCount, 90);
        });
    }
    
    if (descriptionInput && descriptionCount) {
        descriptionInput.addEventListener('input', function() {
            updateCharCount(this, descriptionCount, 200);
        });
    }
    
    // Subtask functionality
    const addSubtaskBtn = document.getElementById('add-subtask-btn');
    const subtasksContainer = document.getElementById('subtasks-container');
    
    if (addSubtaskBtn && subtasksContainer) {
        let subtaskCounter = 0;
        
        addSubtaskBtn.addEventListener('click', function() {
            addNewSubtask();
        });
        
        function updateSubtaskNumbers() {
            const subtasks = subtasksContainer.querySelectorAll('.subtask-item');
            subtasks.forEach((subtask, index) => {
                const number = index + 1;
                const header = subtask.querySelector('h4');
                header.textContent = `Subtarefa #${number}`;
                
                // Update input IDs and names
                const titleInput = subtask.querySelector('.subtask-title input');
                const descTextarea = subtask.querySelector('.subtask-description textarea');
                
                titleInput.id = `subtask_title_${number}`;
                titleInput.name = `subtasks[${number}][title]`;
                
                descTextarea.id = `subtask_description_${number}`;
                descTextarea.name = `subtasks[${number}][description]`;
                
                // Update data-id attribute
                subtask.dataset.id = number;
            });
        }
        
        function addNewSubtask() {
            subtaskCounter++;
            
            const subtaskDiv = document.createElement('div');
            subtaskDiv.className = 'subtask-item mb-3';
            subtaskDiv.dataset.id = subtaskCounter;
            
            subtaskDiv.innerHTML = `
                <div class="subtask-item-container">
                    <div class="subtask-header">
                        <h4>Subtarefa #${subtaskCounter}</h4>
                        <button type="button" class="remove-subtask">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="subtask-title">
                        <input type="text" 
                               class="form-control" 
                               id="subtask_title_${subtaskCounter}" 
                               name="subtasks[${subtaskCounter}][title]" 
                               placeholder="Título da Subtarefa"
                               required>
                    </div>
                    <div class="subtask-description">
                        <textarea class="form-control" 
                                  id="subtask_description_${subtaskCounter}" 
                                  name="subtasks[${subtaskCounter}][description]" 
                                  placeholder="Descrição da Subtarefa" 
                                  style="height: 80px"></textarea>
                    </div>
                </div>
            `;
            
            subtasksContainer.appendChild(subtaskDiv);
            
            // Add event to remove subtask
            const removeBtn = subtaskDiv.querySelector('.remove-subtask');
            removeBtn.addEventListener('click', function() {
                subtaskDiv.remove();
                updateSubtaskNumbers(); // Renumber subtasks after removal
            });
            
            updateSubtaskNumbers(); // Update numbers after adding a new subtask
        }
    }
});
