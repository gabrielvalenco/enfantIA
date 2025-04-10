@extends('layouts.app')

@section('content')

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/profile-style.css') }}">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header rounded-0 bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">Meu Perfil</h5>
                    <a href="{{ route('dashboard') }}">
                        Voltar ao Dashboard
                    </a>
                </div>

                <div class="card-body mt-4">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="avatar-wrapper">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                                         alt="Avatar" class="img-fluid mb-3" style="object-fit: cover;">
                                    <div class="mt-2">
                                        <label for="avatar" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-camera me-2"></i>Alterar Foto
                                        </label>
                                        <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="profile-info col-md-8">
                                <div class="form-group mb-3">
                                    <label for="name">Nome</label>
                                    <input type="text" name="name" id="name" class="form-profile @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">E-mail</label>
                                    <input type="email" name="email" id="email" class="form-profile @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">Telefone</label>
                                    <input type="tel" name="phone" id="phone" class="form-profile @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="position">Cargo/Posição</label>
                                    <input type="text" name="position" id="position" class="form-profile @error('position') is-invalid @enderror" 
                                           value="{{ old('position', $user->position) }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="bio">Biografia</label>
                            <textarea name="bio" id="bio" rows="3" class="form-profile @error('bio') is-invalid @enderror" placeholder="Escreva sobre você aqui">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="activity-section mb-4">
                            <h5 class="mb-3">Atividade de Tarefas</h5>
                            
                            <div class="activity-filters mb-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm filter-btn active" data-filter="year">Ano</button>
                                    <button type="button" class="btn btn-sm filter-btn" data-filter="month">Mês</button>
                                    <button type="button" class="btn btn-sm filter-btn" data-filter="week">Semana</button>
                                </div>
                            </div>
                            
                            <div class="activity-stats mb-3">
                                <div class="stat-item">
                                    <span class="stat-value">{{ $user->tasks()->whereNotNull('completed_at')->count() }}</span>
                                    <span class="stat-label">Total de Tarefas</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $user->tasks()->whereNotNull('completed_at')->whereDate('completed_at', now()->format('Y-m-d'))->count() }}</span>
                                    <span class="stat-label">Hoje</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $user->tasks()->whereNotNull('completed_at')->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</span>
                                    <span class="stat-label">Esta Semana</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $user->tasks()->whereNotNull('completed_at')->whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()])->count() }}</span>
                                    <span class="stat-label">Este Mês</span>
                                </div>
                            </div>
                            
                            <div class="contribution-graph">
                                <div class="graph-legend">
                                    <span>Menos</span>
                                    <ul class="legend-squares">
                                        <li class="legend-square level-0"></li>
                                        <li class="legend-square level-1"></li>
                                        <li class="legend-square level-2"></li>
                                        <li class="legend-square level-3"></li>
                                        <li class="legend-square level-4"></li>
                                    </ul>
                                    <span>Mais</span>
                                </div>
                                
                                <div class="heatmap-container" id="heatmap-container">
                                    <!-- Heatmap will be generated here via JavaScript -->
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>        
    </div>

<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        
        // Verificar tamanho do arquivo (máximo 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('A imagem deve ter no máximo 2MB');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.avatar-wrapper img').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Task Completion Heatmap
document.addEventListener('DOMContentLoaded', function() {
    // Sample data - in a real app, this would come from the backend
    // Format: { 'YYYY-MM-DD': count }
    const taskCompletionData = {
        // This is sample data - in a real implementation, you would fetch this from your backend
        '2025-01-01': 2,
        '2025-01-02': 0,
        '2025-01-03': 1,
        '2025-01-15': 3,
        '2025-01-23': 5,
        '2025-02-05': 4,
        '2025-02-10': 2,
        '2025-02-15': 1,
        '2025-02-28': 3,
        '2025-03-01': 2,
        '2025-03-10': 7,
        '2025-03-15': 4,
        '2025-03-25': 3,
        '2025-04-01': 1,
        '2025-04-05': 6,
        '2025-04-08': 2,
        '2025-04-09': 3,
        '2025-04-10': 1,
    };
    
    // Generate the heatmap
    generateHeatmap(taskCompletionData);
    
    // Handle filter buttons
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Apply filter
            const filter = this.getAttribute('data-filter');
            applyFilter(filter, taskCompletionData);
        });
    });
});

function generateHeatmap(data) {
    const container = document.getElementById('heatmap-container');
    container.innerHTML = '';
    
    // Create month labels
    const monthLabels = document.createElement('div');
    monthLabels.className = 'month-labels';
    const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    months.forEach(month => {
        const label = document.createElement('div');
        label.textContent = month;
        monthLabels.appendChild(label);
    });
    container.appendChild(monthLabels);
    
    // Create grid
    const grid = document.createElement('div');
    grid.className = 'heatmap-grid';
    
    // Add day labels (Mon-Sun)
    const dayLabels = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    dayLabels.forEach(day => {
        const label = document.createElement('div');
        label.className = 'heatmap-label';
        label.textContent = day;
        grid.appendChild(label);
        
        // For each day of the week, create 53 cells (for 53 weeks in a year max)
        for (let week = 0; week < 53; week++) {
            const cell = document.createElement('div');
            cell.className = 'heatmap-day';
            
            // Calculate the date for this cell
            const currentDate = new Date();
            const startOfYear = new Date(currentDate.getFullYear(), 0, 1);
            const dayOfWeek = dayLabels.indexOf(day);
            
            // Adjust to get the right day of the week
            const dayOffset = dayOfWeek - startOfYear.getDay() + (startOfYear.getDay() === 0 ? -6 : 1);
            const cellDate = new Date(startOfYear);
            cellDate.setDate(startOfYear.getDate() + dayOffset + (week * 7));
            
            // Skip future dates
            if (cellDate > currentDate) {
                cell.style.visibility = 'hidden';
                grid.appendChild(cell);
                continue;
            }
            
            // Format date as YYYY-MM-DD for lookup
            const dateStr = cellDate.toISOString().split('T')[0];
            
            // Set cell color based on task count
            const count = data[dateStr] || 0;
            let level = 0;
            
            if (count > 0) {
                if (count <= 1) level = 1;
                else if (count <= 3) level = 2;
                else if (count <= 5) level = 3;
                else level = 4;
                
                cell.classList.add(`level-${level}`);
                
                // Add tooltip
                const tooltip = document.createElement('div');
                tooltip.className = 'heatmap-tooltip';
                tooltip.textContent = `${count} ${count === 1 ? 'tarefa' : 'tarefas'} em ${formatDate(cellDate)}`;
                cell.appendChild(tooltip);
            }
            
            grid.appendChild(cell);
        }
    });
    
    container.appendChild(grid);
}

function applyFilter(filter, data) {
    let filteredData = {};
    const currentDate = new Date();
    
    switch(filter) {
        case 'year':
            // Show full year data
            filteredData = data;
            break;
            
        case 'month':
            // Filter to current month only
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();
            
            Object.keys(data).forEach(dateStr => {
                const date = new Date(dateStr);
                if (date.getMonth() === currentMonth && date.getFullYear() === currentYear) {
                    filteredData[dateStr] = data[dateStr];
                }
            });
            break;
            
        case 'week':
            // Filter to current week only
            const startOfWeek = new Date(currentDate);
            startOfWeek.setDate(currentDate.getDate() - currentDate.getDay() + (currentDate.getDay() === 0 ? -6 : 1));
            startOfWeek.setHours(0, 0, 0, 0);
            
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            endOfWeek.setHours(23, 59, 59, 999);
            
            Object.keys(data).forEach(dateStr => {
                const date = new Date(dateStr);
                if (date >= startOfWeek && date <= endOfWeek) {
                    filteredData[dateStr] = data[dateStr];
                }
            });
            break;
    }
    
    // Regenerate heatmap with filtered data
    generateHeatmap(filteredData);
}

function formatDate(date) {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}
</script>
@endsection
