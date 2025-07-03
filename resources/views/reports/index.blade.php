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
            <button class="filter-button active" data-period="week">Última Semana</button>
            <button class="filter-button" data-period="month">Último Mês</button>
            <button class="filter-button" data-period="quarter">Último Trimestre</button>
            <button class="filter-button" data-period="year">Último Ano</button>
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
    <script>
        // Configuração das cores do tema para os gráficos
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--link-color').trim();
        const primaryColorLight = 'rgba(32, 172, 130, 0.2)';
        const warningColor = getComputedStyle(document.documentElement).getPropertyValue('--edit-color').trim();
        const warningColorLight = 'rgba(205, 161, 65, 0.2)';
        const dangerColor = getComputedStyle(document.documentElement).getPropertyValue('--delete-color').trim();
        const dangerColorLight = 'rgba(181, 46, 41, 0.2)';
        const textColor = getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim();
        
        // Dados simulados para os gráficos
        const weekLabels = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        const monthLabels = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'];
        const quarterLabels = ['Jan', 'Fev', 'Mar'];
        const yearLabels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        
        // Dados simulados para tarefas criadas e concluídas
        const weekData = {
            created: [5, 7, 3, 8, 6, 2, 4],
            completed: [3, 5, 2, 7, 4, 2, 3],
            rate: [60, 71, 67, 88, 67, 100, 75],
            time: [24, 18, 36, 12, 24, 8, 16]
        };
        
        const monthData = {
            created: [18, 22, 15, 25],
            completed: [12, 18, 10, 20],
            rate: [67, 82, 67, 80],
            time: [24, 18, 30, 16]
        };
        
        const quarterData = {
            created: [45, 60, 55],
            completed: [35, 48, 45],
            rate: [78, 80, 82],
            time: [22, 20, 18]
        };
        
        const yearData = {
            created: [30, 35, 45, 50, 55, 60, 65, 60, 55, 50, 45, 40],
            completed: [25, 28, 35, 40, 45, 50, 55, 50, 45, 40, 35, 30],
            rate: [83, 80, 78, 80, 82, 83, 85, 83, 82, 80, 78, 75],
            time: [20, 22, 24, 22, 20, 18, 16, 18, 20, 22, 24, 26]
        };
        
        // Dados de categorias
        const categories = {
            labels: ['Trabalho', 'Pessoal', 'Estudo', 'Saúde', 'Outros'],
            data: [40, 25, 20, 10, 5],
            colors: [primaryColor, warningColor, dangerColor, '#6c757d', '#17a2b8']
        };
        
        // Configuração inicial dos gráficos
        let tasksChart, completionRateChart, categoriesChart, completionTimeChart;
        let currentPeriod = 'week';
        let currentData = weekData;
        let currentLabels = weekLabels;
        
        // Função para atualizar os dados dos cards
        function updateCardData(data) {
            const totalTasks = data.created.reduce((a, b) => a + b, 0);
            const completedTasks = data.completed.reduce((a, b) => a + b, 0);
            const completionRate = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
            const avgTime = data.time.reduce((a, b) => a + b, 0) / data.time.length;
            
            $('#total-tasks').text(totalTasks);
            $('#completed-tasks').text(completedTasks);
            $('#completion-rate').text(completionRate + '%');
            $('#avg-completion-time').text(Math.round(avgTime) + 'h');
        }
        
        // Função para inicializar os gráficos
        function initCharts() {
            // Gráfico de tarefas criadas vs concluídas
            const tasksCtx = document.getElementById('tasks-chart').getContext('2d');
            tasksChart = new Chart(tasksCtx, {
                type: 'bar',
                data: {
                    labels: currentLabels,
                    datasets: [
                        {
                            label: 'Tarefas Criadas',
                            data: currentData.created,
                            backgroundColor: primaryColorLight,
                            borderColor: primaryColor,
                            borderWidth: 1
                        },
                        {
                            label: 'Tarefas Concluídas',
                            data: currentData.completed,
                            backgroundColor: warningColorLight,
                            borderColor: warningColor,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tarefas Criadas vs Concluídas',
                            color: textColor
                        },
                        legend: {
                            labels: {
                                color: textColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de taxa de conclusão
            const rateCtx = document.getElementById('completion-rate-chart').getContext('2d');
            completionRateChart = new Chart(rateCtx, {
                type: 'line',
                data: {
                    labels: currentLabels,
                    datasets: [{
                        label: 'Taxa de Conclusão (%)',
                        data: currentData.rate,
                        backgroundColor: 'rgba(32, 172, 130, 0.2)',
                        borderColor: primaryColor,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Taxa de Conclusão de Tarefas (%)',
                            color: textColor
                        },
                        legend: {
                            labels: {
                                color: textColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                },
                                color: textColor
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de categorias
            const categoriesCtx = document.getElementById('categories-chart').getContext('2d');
            categoriesChart = new Chart(categoriesCtx, {
                type: 'pie',
                data: {
                    labels: categories.labels,
                    datasets: [{
                        data: categories.data,
                        backgroundColor: categories.colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribuição por Categoria',
                            color: textColor
                        },
                        legend: {
                            position: 'right',
                            labels: {
                                color: textColor
                            }
                        }
                    }
                }
            });
            
            // Gráfico de tempo médio de conclusão
            const timeCtx = document.getElementById('completion-time-chart').getContext('2d');
            completionTimeChart = new Chart(timeCtx, {
                type: 'line',
                data: {
                    labels: currentLabels,
                    datasets: [{
                        label: 'Tempo Médio (horas)',
                        data: currentData.time,
                        backgroundColor: dangerColorLight,
                        borderColor: dangerColor,
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tempo Médio de Conclusão (horas)',
                            color: textColor
                        },
                        legend: {
                            labels: {
                                color: textColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + 'h';
                                },
                                color: textColor
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });
        }
        
        // Função para atualizar os gráficos com novos dados
        function updateCharts(period) {
            // Atualizar período atual
            currentPeriod = period;
            
            // Definir dados e labels com base no período
            switch(period) {
                case 'week':
                    currentData = weekData;
                    currentLabels = weekLabels;
                    break;
                case 'month':
                    currentData = monthData;
                    currentLabels = monthLabels;
                    break;
                case 'quarter':
                    currentData = quarterData;
                    currentLabels = quarterLabels;
                    break;
                case 'year':
                    currentData = yearData;
                    currentLabels = yearLabels;
                    break;
            }
            
            // Atualizar dados dos cards
            updateCardData(currentData);
            
            // Atualizar dados dos gráficos
            tasksChart.data.labels = currentLabels;
            tasksChart.data.datasets[0].data = currentData.created;
            tasksChart.data.datasets[1].data = currentData.completed;
            tasksChart.update();
            
            completionRateChart.data.labels = currentLabels;
            completionRateChart.data.datasets[0].data = currentData.rate;
            completionRateChart.update();
            
            completionTimeChart.data.labels = currentLabels;
            completionTimeChart.data.datasets[0].data = currentData.time;
            completionTimeChart.update();
        }
        
        // Inicializar quando o documento estiver pronto
        $(document).ready(function() {
            // Inicializar gráficos
            initCharts();
            
            // Atualizar dados dos cards
            updateCardData(currentData);
            
            // Manipular cliques nos botões de filtro
            $('.filter-button').click(function() {
                $('.filter-button').removeClass('active');
                $(this).addClass('active');
                
                const period = $(this).data('period');
                updateCharts(period);
            });
            
            // Botão de exportar relatório
            $('#export-report-btn').click(function() {
                alert('Funcionalidade de exportação será implementada em breve!');
            });
        });
        
        // Garantir que os gráficos se ajustem quando o tema mudar
        document.getElementById('theme-toggle').addEventListener('click', function() {
            setTimeout(function() {
                const textColor = getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim();
                
                // Atualizar cores de texto nos gráficos
                const updateOptions = {
                    plugins: {
                        title: {
                            color: textColor
                        },
                        legend: {
                            labels: {
                                color: textColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: textColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            }
                        }
                    }
                };
                
                tasksChart.options.plugins.title.color = textColor;
                tasksChart.options.plugins.legend.labels.color = textColor;
                tasksChart.options.scales.y.ticks.color = textColor;
                tasksChart.options.scales.x.ticks.color = textColor;
                tasksChart.update();
                
                completionRateChart.options.plugins.title.color = textColor;
                completionRateChart.options.plugins.legend.labels.color = textColor;
                completionRateChart.options.scales.y.ticks.color = textColor;
                completionRateChart.options.scales.x.ticks.color = textColor;
                completionRateChart.update();
                
                categoriesChart.options.plugins.title.color = textColor;
                categoriesChart.options.plugins.legend.labels.color = textColor;
                categoriesChart.update();
                
                completionTimeChart.options.plugins.title.color = textColor;
                completionTimeChart.options.plugins.legend.labels.color = textColor;
                completionTimeChart.options.scales.y.ticks.color = textColor;
                completionTimeChart.options.scales.x.ticks.color = textColor;
                completionTimeChart.update();
            }, 300);
        });
    </script>
    
    <script src="{{ asset('js/script.js') }}"></script>
</body>

<button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark/light mode">
    <i class="fas fa-moon"></i>
</button>

</html>