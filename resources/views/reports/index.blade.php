@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
<!-- Chart.js para os gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<header class="dashboard-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="dashboard-title">Relatórios de Desempenho</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>
</header>

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtrar por período</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Diário (última semana)</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Semanal (último mês)</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Mensal (últimos 6 meses)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards resumo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="status-card">
                <div class="status-card-icon pending">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="status-card-info">
                    <h3>{{ array_sum($createdByPeriod) }}</h3>
                    <p>Tarefas Criadas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="status-card">
                <div class="status-card-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="status-card-info">
                    <h3>{{ array_sum($completedByPeriod) }}</h3>
                    <p>Tarefas Concluídas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="status-card">
                <div class="status-card-icon urgent">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="status-card-info">
                    <h3>{{ array_sum($completedByPeriod) > 0 ? round((array_sum($completedByPeriod) / array_sum($createdByPeriod) * 100)) : 0 }}%</h3>
                    <p>Taxa de Conclusão</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="status-card">
                <div class="status-card-icon" style="background-color: #8e44ad;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="status-card-info">
                    @php
                        $totalHours = 0;
                        $totalCount = 0;
                        foreach ($avgCompletionTimes as $data) {
                            $totalHours += $data['total_hours'];
                            $totalCount += $data['count'];
                        }
                        $avgTime = $totalCount > 0 ? round($totalHours / $totalCount) : 0;
                    @endphp
                    <h3>{{ $avgTime }}h</h3>
                    <p>Tempo Médio</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Tarefas Criadas vs Concluídas -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Tarefas Criadas vs Concluídas</h5>
                </div>
                <div class="card-body">
                    <canvas id="tasksChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Distribuição por Categoria -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribuição por Categoria</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Taxa de Conclusão -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-percentage"></i> Taxa de Conclusão (%)</h5>
                </div>
                <div class="card-body">
                    <canvas id="completionRateChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tempo Médio de Conclusão -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Tempo Médio de Conclusão (horas)</h5>
                </div>
                <div class="card-body">
                    <canvas id="completionTimeChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Dicas de Produtividade -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Sugestões para Melhorar sua Produtividade</h5>
        </div>
        <div class="card-body">
            <div id="productivity-tips" class="p-3">
                <!-- Dicas dinâmicas baseadas nos dados -->
                @php
                    $completedTasksTotal = array_sum($completedByPeriod);
                    $createdTasksTotal = array_sum($createdByPeriod);
                    $completionRateTotal = $createdTasksTotal > 0 ? ($completedTasksTotal / $createdTasksTotal) * 100 : 0;
                @endphp
                
                <ul class="list-group">
                    @if($completionRateTotal < 50)
                        <li class="list-group-item">
                            <i class="fas fa-exclamation-circle text-warning"></i> 
                            <strong>Sua taxa de conclusão está abaixo de 50%.</strong> Tente quebrar tarefas grandes em subtarefas menores e mais gerenciáveis.
                        </li>
                    @elseif($completionRateTotal >= 80)
                        <li class="list-group-item">
                            <i class="fas fa-star text-success"></i>
                            <strong>Parabéns!</strong> Você tem uma excelente taxa de conclusão de tarefas. Continue assim!
                        </li>
                    @endif
                    
                    @if($avgTime > 48)
                        <li class="list-group-item">
                            <i class="fas fa-clock text-warning"></i>
                            Seu tempo médio para concluir tarefas é de <strong>{{ $avgTime }} horas</strong>. Considere priorizar melhor suas tarefas ou dividir projetos grandes em partes menores.
                        </li>
                    @elseif($avgTime < 24 && $completedTasksTotal > 5)
                        <li class="list-group-item">
                            <i class="fas fa-thumbs-up text-success"></i>
                            <strong>Eficiência!</strong> Você conclui tarefas rapidamente (média de {{ $avgTime }} horas). Um excelente ritmo!
                        </li>
                    @endif
                    
                    @if(count($categoriesData) === 1)
                        <li class="list-group-item">
                            <i class="fas fa-folder text-info"></i>
                            Você está trabalhando apenas em uma categoria. <strong>Que tal diversificar suas atividades?</strong>
                        </li>
                    @endif

                    <li class="list-group-item">
                        <i class="fas fa-calendar-check text-primary"></i>
                        <strong>Dica BIRDU:</strong> Reserve blocos específicos de tempo em seu dia para trabalhar em tarefas focadas, sem distrações.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Configuração dos gráficos assim que a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        // Dados para o gráfico de tarefas
        const labels = [@foreach($periods as $key => $label) "{{ $label }}", @endforeach];
        const createdData = [@foreach($periods as $key => $label) {{ $createdByPeriod[$key] ?? 0 }}, @endforeach];
        const completedData = [@foreach($periods as $key => $label) {{ $completedByPeriod[$key] ?? 0 }}, @endforeach];
        
        // Configuração do gráfico de tarefas criadas vs concluídas
        const tasksChart = new Chart(document.getElementById('tasksChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Tarefas Criadas',
                        data: createdData,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Tarefas Concluídas',
                        data: completedData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: '{{ $chartLabel }}'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Gráfico de categorias - Pie Chart
        const categoryColors = [@foreach($categoriesData as $cat) "{{ $cat['color'] }}", @endforeach];
        const categoryNames = [@foreach($categoriesData as $cat) "{{ $cat['name'] }}", @endforeach];
        const categoryValues = [@foreach($categoriesData as $cat) {{ $cat['count'] }}, @endforeach];
        
        const categoryChart = new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: categoryNames,
                datasets: [{
                    data: categoryValues,
                    backgroundColor: categoryColors,
                    borderWidth: 1,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Tarefas Concluídas por Categoria'
                    }
                }
            }
        });
        
        // Taxa de conclusão - Line Chart
        const completionRates = [@foreach($periods as $key => $label) {{ $completionRate[$key] ?? 0 }}, @endforeach];
        
        const completionRateChart = new Chart(document.getElementById('completionRateChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Taxa de Conclusão (%)',
                    data: completionRates,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Taxa de Conclusão de Tarefas'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Tempo médio de conclusão - Line Chart
        const avgTimeData = [];
        @foreach($periods as $key => $label)
            @if(isset($avgCompletionTimes[$key]) && $avgCompletionTimes[$key]['count'] > 0)
                avgTimeData.push({{ round($avgCompletionTimes[$key]['total_hours'] / $avgCompletionTimes[$key]['count']) }});
            @else
                avgTimeData.push(null);
            @endif
        @endforeach
        
        const completionTimeChart = new Chart(document.getElementById('completionTimeChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tempo Médio (horas)',
                    data: avgTimeData,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(255, 159, 64, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Tempo Médio para Conclusão de Tarefas'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + 'h';
                            }
                        }
                    }
                }
            }
        });
    });
</script>

@include('layouts.footer')

@endsection
