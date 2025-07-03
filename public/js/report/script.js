document.addEventListener('DOMContentLoaded', function() {
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--link-color').trim();
    const primaryColorLight = 'rgba(32, 172, 130, 0.2)';
    const warningColor = getComputedStyle(document.documentElement).getPropertyValue('--edit-color').trim();
    const warningColorLight = 'rgba(205, 161, 65, 0.2)';
    const dangerColor = getComputedStyle(document.documentElement).getPropertyValue('--delete-color').trim();
    const dangerColorLight = 'rgba(181, 46, 41, 0.2)';
    const textColor = getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim();
    
    // Variáveis globais para os gráficos
    let tasksChart, completionRateChart, categoriesChart, completionTimeChart;
    let currentPeriod = document.querySelector('.filter-button.active').dataset.period;
    let currentLabels = [];
    
    // Dados atuais
    let currentData = {
        created: [],
        completed: [],
        rate: [],
        time: []
    };
    
    // Função para atualizar os dados dos cards
    function updateCardData(data) {
        const totalTasks = data.created.reduce((a, b) => a + b, 0);
        const completedTasks = data.completed.reduce((a, b) => a + b, 0);
        const completionRate = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
        const avgTime = data.time.length > 0 ? data.time.reduce((a, b) => a + b, 0) / data.time.length : 0;
        
        $('#total-tasks').text(totalTasks);
        $('#completed-tasks').text(completedTasks);
        $('#completion-rate').text(completionRate + '%');
        $('#avg-completion-time').text(Math.round(avgTime) + 'h');
    }
    
    // Função para inicializar os gráficos
    function initCharts() {
        // Obter dados iniciais do servidor
        currentLabels = window.periods || [];
        currentData = {
            created: window.createdByPeriod || [],
            completed: window.completedByPeriod || [],
            rate: window.completionRate || [],
            time: window.avgTimeData || []
        };
        
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
                labels: window.categoriesLabels || [],
                datasets: [{
                    data: window.categoriesData || [],
                    backgroundColor: window.categoriesColors && window.categoriesColors.length > 0 ? 
                        window.categoriesColors : [primaryColor, warningColor, dangerColor, '#6c757d', '#17a2b8'],
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
        // Mostrar indicador de carregamento
        $('#total-tasks, #completed-tasks, #completion-rate, #avg-completion-time').html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Atualizar período atual
        currentPeriod = period;
        
        // Fazer requisição AJAX para buscar novos dados
        $.ajax({
            url: window.reportRoute,
            type: 'GET',
            data: { period: period },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Atualizar dados com a resposta
                currentLabels = response.periods;
                currentData = {
                    created: response.created,
                    completed: response.completed,
                    rate: response.rates || [],
                    time: response.times || []
                };
                
                // Atualizar dados de categorias
                if (response.categories) {
                    window.categoriesLabels = response.categories.labels || [];
                    window.categoriesData = response.categories.data || [];
                    window.categoriesColors = response.categories.colors || [];
                }
                
                // Atualizar dados dos cards
                updateCardData(currentData);
                
                // Atualizar gráficos
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
                
                // Atualizar gráfico de categorias
                categoriesChart.data.labels = window.categoriesLabels;
                categoriesChart.data.datasets[0].data = window.categoriesData;
                if (window.categoriesColors && window.categoriesColors.length > 0) {
                    categoriesChart.data.datasets[0].backgroundColor = window.categoriesColors;
                }
                categoriesChart.update();
            },
            error: function(error) {
                console.error('Erro ao buscar dados:', error);
                // Mostrar mensagem de erro
                $('#total-tasks, #completed-tasks, #completion-rate, #avg-completion-time').text('Erro');
            }
        });
    }
    
    // Função para atualizar cores dos gráficos quando o tema mudar
    function updateChartColors() {
        const textColor = getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim();
        
        // Atualizar cores de texto nos gráficos
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
    }
    
    // Função para exportar o relatório como PDF
    function exportReport() {
        // Criar elemento canvas temporário para combinar os gráficos
        const tempCanvas = document.createElement('canvas');
        const ctx = tempCanvas.getContext('2d');
        
        // Definir tamanho do canvas baseado no conteúdo do relatório
        const reportContent = document.querySelector('.report-content');
        tempCanvas.width = reportContent.offsetWidth;
        tempCanvas.height = reportContent.offsetHeight;
        
        // Definir fundo branco para o PDF
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        
        // Função para converter HTML para canvas
        html2canvas(reportContent, {
            backgroundColor: '#ffffff',
            scale: 2,
            useCORS: true,
            logging: false
        }).then(canvas => {
            // Criar PDF
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            // Adicionar título
            pdf.setFontSize(18);
            pdf.text('Relatório de Desempenho', 105, 15, { align: 'center' });
            pdf.setFontSize(12);
            
            // Adicionar período
            const periodText = document.querySelector('.filter-button.active').textContent;
            pdf.text('Período: ' + periodText, 105, 25, { align: 'center' });
            
            // Adicionar data de geração
            const today = new Date();
            const dateStr = today.toLocaleDateString('pt-BR');
            pdf.text('Gerado em: ' + dateStr, 105, 30, { align: 'center' });
            
            // Adicionar linha separadora
            pdf.line(20, 35, 190, 35);
            
            // Adicionar imagem do relatório
            const imgData = canvas.toDataURL('image/png');
            
            // Calcular proporção para ajustar ao tamanho da página
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const imgWidth = pageWidth - 40; // margens de 20mm em cada lado
            const imgHeight = canvas.height * imgWidth / canvas.width;
            
            // Adicionar imagem ao PDF
            let heightLeft = imgHeight;
            let position = 40; // Posição inicial após o cabeçalho
            
            // Adicionar primeira página
            pdf.addImage(imgData, 'PNG', 20, position, imgWidth, imgHeight);
            heightLeft -= (pageHeight - 40);
            
            // Adicionar páginas adicionais se necessário
            while (heightLeft > 0) {
                position = 20; // Posição inicial na nova página
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 20, position - imgHeight + heightLeft, imgWidth, imgHeight);
                heightLeft -= (pageHeight - 40);
            }
            
            // Salvar PDF
            pdf.save('relatorio-desempenho-' + dateStr + '.pdf');
        });
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
            // Verificar se as bibliotecas necessárias estão carregadas
            if (typeof html2canvas === 'undefined' || typeof jsPDF === 'undefined') {
                // Carregar bibliotecas necessárias dinamicamente
                const html2canvasScript = document.createElement('script');
                html2canvasScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                document.head.appendChild(html2canvasScript);
                
                const jsPDFScript = document.createElement('script');
                jsPDFScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
                document.head.appendChild(jsPDFScript);
                
                // Mostrar mensagem de carregamento
                const exportBtn = $(this);
                exportBtn.html('<i class="fas fa-spinner fa-spin"></i> Carregando...');
                
                // Verificar quando ambas as bibliotecas estiverem carregadas
                const checkLibraries = setInterval(function() {
                    if (typeof html2canvas !== 'undefined' && typeof jsPDF !== 'undefined') {
                        clearInterval(checkLibraries);
                        exportBtn.html('<i class="fas fa-download"></i> Exportar Relatório');
                        exportReport();
                    }
                }, 100);
            } else {
                exportReport();
            }
        });
        
        // Garantir que os gráficos se ajustem quando o tema mudar
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                setTimeout(updateChartColors, 300);
            });
        }
    });
});