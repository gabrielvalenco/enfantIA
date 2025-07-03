<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/reports/index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>{{ env('APP_NAME') }} - Relatórios de Desempenho</title>
</head>
<body>
    <div class="container">
        <div class="table-header">
            <h1>Relatórios de Desempenho</h1>
            <div class="table-actions">
                <a href="{{ route('dashboard') }}" class="back-button">
                    <span class="back-text">Voltar ao Dashboard</span>
                    <i class="fas fa-sign-out-alt mobile-icon"></i>
                </a>
                <button class="add-button" id="export-report-btn">
                    <i class="fas fa-download"></i>
                    Exportar Relatório
                </button>
            </div>
        </div>

        <div class="filter-container">
            <button class="filter-button {{ $period == 'day' ? 'active' : '' }}" data-period="day">Últimos 7 dias</button>
            <button class="filter-button {{ $period == 'week' ? 'active' : '' }}" data-period="week">Últimas 5 semanas</button>
            <button class="filter-button {{ $period == 'month' ? 'active' : '' }}" data-period="month">Últimos 6 meses</button>
        </div>

        <div class="report-content">
            <!-- Cards de resumo -->
            <div class="report-section">
                <div class="report-section-header">
                    <h3 class="report-section-title"><i class="fas fa-chart-line"></i> Visão Geral</h3>
                </div>
                
                <div class="report-cards">
                    <div class="report-card">
                        <div class="report-card-icon" style="background-color: rgba(32, 172, 130, 0.2); color: var(--link-color);">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="report-card-value" id="total-tasks">0</div>
                        <div class="report-card-label">Tarefas Criadas</div>
                    </div>
                    
                    <div class="report-card">
                        <div class="report-card-icon" style="background-color: rgba(32, 172, 130, 0.2); color: var(--link-color);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="report-card-value" id="completed-tasks">0</div>
                        <div class="report-card-label">Tarefas Concluídas</div>
                    </div>
                    
                    <div class="report-card">
                        <div class="report-card-icon" style="background-color: rgba(205, 161, 65, 0.2); color: var(--edit-color);">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="report-card-value" id="completion-rate">0%</div>
                        <div class="report-card-label">Taxa de Conclusão</div>
                    </div>
                    
                    <div class="report-card">
                        <div class="report-card-icon" style="background-color: rgba(181, 46, 41, 0.2); color: var(--delete-color);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="report-card-value" id="avg-completion-time">0h</div>
                        <div class="report-card-label">Tempo Médio de Conclusão</div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos -->
            <div class="report-section">
                <div class="report-section-header">
                    <h3 class="report-section-title"><i class="fas fa-chart-bar"></i> Análise de Produtividade</h3>
                </div>
                
                <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1; min-width: 300px;">
                        <div class="chart-container">
                            <canvas id="tasks-chart"></canvas>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 300px;">
                        <div class="chart-container">
                            <canvas id="completion-rate-chart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <div style="flex: 1; min-width: 300px;">
                        <div class="chart-container">
                            <canvas id="categories-chart"></canvas>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 300px;">
                        <div class="chart-container">
                            <canvas id="completion-time-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Insights -->
            <div class="report-section">
                <div class="report-section-header">
                    <h3 class="report-section-title"><i class="fas fa-lightbulb"></i> Insights e Recomendações</h3>
                </div>
                
                <div class="insights-container">
                    <div class="insight-item">
                        <div class="insight-icon insight-positive">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="insight-content">
                            <h4 class="insight-title">Aumento na Produtividade</h4>
                            <p class="insight-description">Sua taxa de conclusão de tarefas aumentou em <strong>15%</strong> em comparação com o período anterior. Continue mantendo esse ritmo!</p>
                        </div>
                    </div>
                    
                    <div class="insight-item">
                        <div class="insight-icon insight-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="insight-content">
                            <h4 class="insight-title">Categoria com Maior Atraso</h4>
                            <p class="insight-description">Tarefas da categoria <strong>"Trabalho"</strong> têm o maior tempo médio de conclusão (3.5 dias). Considere dividir essas tarefas em subtarefas menores.</p>
                        </div>
                    </div>
                    
                    <div class="insight-item">
                        <div class="insight-icon insight-positive">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="insight-content">
                            <h4 class="insight-title">Categoria Mais Eficiente</h4>
                            <p class="insight-description">Tarefas da categoria <strong>"Pessoal"</strong> têm a maior taxa de conclusão (92%). Você está gerenciando bem suas tarefas pessoais!</p>
                        </div>
                    </div>
                    
                    <div class="insight-item">
                        <div class="insight-icon insight-negative">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="insight-content">
                            <h4 class="insight-title">Horário de Pico</h4>
                            <p class="insight-description">Você completa a maioria das tarefas entre <strong>14h e 16h</strong>. Considere programar suas tarefas mais importantes para este período do dia.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <script>
        // Dados do controller para o JavaScript
        window.periods = {!! json_encode(array_values($periods)) !!};
        window.createdByPeriod = {!! json_encode(array_values($createdByPeriod)) !!};
        window.completedByPeriod = {!! json_encode(array_values($completedByPeriod)) !!};
        window.completionRate = {!! json_encode(array_values($completionRate)) !!};
        window.reportRoute = '{{ route("reports.index") }}';
        
        // Processar dados de tempo de conclusão
        window.avgTimeData = [];
        @foreach($periods as $key => $label)
            @if(isset($avgCompletionTimes[$key]))
                window.avgTimeData.push({{ round($avgCompletionTimes[$key]['total_hours'] / $avgCompletionTimes[$key]['count']) }});
            @else
                window.avgTimeData.push(0);
            @endif
        @endforeach
        
        // Dados de categorias
        window.categoriesLabels = [];
        window.categoriesData = [];
        window.categoriesColors = [];
        
        @foreach($categoriesData as $category)
            window.categoriesLabels.push('{{ $category["name"] }}');
            window.categoriesData.push({{ $category["count"] }});
            window.categoriesColors.push('{{ $category["color"] }}');
        @endforeach
    </script>
    
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/report/script.js') }}"></script>
    
    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>
</body>
</html>