<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME', 'enfantIA') }} - Gerenciamento de Tarefas Inteligente</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/welcome/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="overlay"></div>
    <header>
        <nav>
            <div class="logo">
                <img src="{{ asset('favicon.svg') }}" alt="{{ env('APP_NAME', 'enfantIA') }} Logo">
                <a href="{{ route('dashboard') }}">{{ env('APP_NAME', 'enfantIA') }}</a>
            </div>
            <div class="menu-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="navbar">
                <li><a href="#recursos">Recursos</a></li>
                <li><a href="#ia">IA</a></li>
                <li><a href="#grupos">Grupos</a></li>
                <li><a href="#perfil">Perfil</a></li>
                <li><a href="{{ route('login') }}" class="login-btn">Login</a></li>
                <li><a href="{{ route('register') }}" class="signup-btn">Cadastre-se</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Organize suas tarefas com <span class="highlight">Inteligência Artificial</span></h1>
                <div class="typing-container">
                    <p class="typing-animation"></p>
                </div>
                <p class="hero-description">Gerencie seu tempo, aumente sua produtividade e alcance seus objetivos com o poder da IA.</p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn primary-btn">Comece Agora</a>
                    <a href="#saiba-mais" class="btn secondary-btn">Saiba Mais</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="floating-elements">
                    <div class="floating-task task-1">
                        <i class="fas fa-check-circle"></i>
                        <span>Reunião de Equipe</span>
                    </div>
                    <div class="floating-task task-2">
                        <i class="fas fa-clock"></i>
                        <span>Projeto Final</span>
                    </div>
                    <div class="floating-task task-3">
                        <i class="fas fa-star"></i>
                        <span>Exercícios</span>
                    </div>
                    <div class="floating-icon icon-1">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="floating-icon icon-2">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="floating-icon icon-3">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="hero-mockup">
                    <div class="mockup-header">
                        <div class="mockup-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="mockup-title">{{ env('APP_NAME', 'enfantIA') }} Dashboard</div>
                    </div>
                    <div class="mockup-content">
                        <div class="mockup-sidebar">
                            <div class="sidebar-item active"><i class="fas fa-home"></i></div>
                            <div class="sidebar-item"><i class="fas fa-tasks"></i></div>
                            <div class="sidebar-item"><i class="fas fa-users"></i></div>
                            <div class="sidebar-item"><i class="fas fa-chart-pie"></i></div>
                            <div class="sidebar-item"><i class="fas fa-cog"></i></div>
                        </div>
                        <div class="mockup-main">
                            <div class="mockup-welcome">Bem-vindo de volta!</div>
                            <div class="mockup-stats">
                                <div class="stat-item">
                                    <div class="stat-value">8</div>
                                    <div class="stat-label">Tarefas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">3</div>
                                    <div class="stat-label">Concluídas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">2</div>
                                    <div class="stat-label">Grupos</div>
                                </div>
                            </div>
                            <div class="mockup-tasks">
                                <div class="task-item">
                                    <div class="task-check"></div>
                                    <div class="task-content">Finalizar relatório</div>
                                    <div class="task-badge urgent">Urgente</div>
                                </div>
                                <div class="task-item">
                                    <div class="task-check checked"></div>
                                    <div class="task-content completed">Reunião com cliente</div>
                                    <div class="task-badge">Concluída</div>
                                </div>
                                <div class="task-item">
                                    <div class="task-check"></div>
                                    <div class="task-content">Preparar apresentação</div>
                                    <div class="task-badge medium">Média</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="recursos" class="features">
            <h2>Recursos <span class="highlight">Exclusivos</span></h2>
            <div class="features-container">
                <div class="feature-card" data-aos="fade-up">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Gerenciamento Intuitivo</h3>
                    <p>Crie, organize e gerencie suas tarefas com uma interface simples e poderosa. Categorize, defina prioridades e acompanhe seu progresso.</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>IA Integrada</h3>
                    <p>Nossa inteligência artificial analisa seus padrões de produtividade e oferece sugestões personalizadas para otimizar seu fluxo de trabalho.</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Colaboração em Grupo</h3>
                    <p>Trabalhe em equipe, compartilhe tarefas e acompanhe o progresso coletivo. Perfeito para projetos colaborativos e equipes de trabalho.</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Análises Detalhadas</h3>
                    <p>Visualize relatórios de produtividade, identifique padrões e receba insights sobre como melhorar sua eficiência e equilíbrio.</p>
                </div>
            </div>
        </section>

        <section id="ia" class="ai-section">
            <div class="section-content">
                <div class="ai-text">
                    <h2>Potencializado por <span class="highlight">Inteligência Artificial</span></h2>
                    <p>Nossa IA avançada trabalha para você, analisando seus padrões de produtividade e oferecendo recomendações personalizadas.</p>
                    <ul class="ai-features">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Sugestões inteligentes de categorização</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Recomendações de prioridade baseadas em seu comportamento</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Análise de equilíbrio trabalho-vida</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Identificação de padrões de produtividade</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Otimização de horários para melhor desempenho</span>
                        </li>
                    </ul>
                </div>
                <div class="ai-visual">
                    <div class="ai-brain">
                        <div class="brain-container">
                            <div class="brain-connections">
                                <div class="connection c1"></div>
                                <div class="connection c2"></div>
                                <div class="connection c3"></div>
                                <div class="connection c4"></div>
                                <div class="connection c5"></div>
                            </div>
                            <div class="brain-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div class="brain-pulse"></div>
                        </div>
                        <div class="ai-data">
                            <div class="data-point dp1">
                                <div class="data-icon"><i class="fas fa-clock"></i></div>
                                <div class="data-text">Horário Ideal: 9-11h</div>
                            </div>
                            <div class="data-point dp2">
                                <div class="data-icon"><i class="fas fa-calendar"></i></div>
                                <div class="data-text">Produtividade: Terças</div>
                            </div>
                            <div class="data-point dp3">
                                <div class="data-icon"><i class="fas fa-chart-bar"></i></div>
                                <div class="data-text">Eficiência: +27%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="grupos" class="groups-section">
            <h2>Colaboração em <span class="highlight">Grupos</span></h2>
            <div class="groups-container">
                <div class="group-visual">
                    <div class="group-mockup">
                        <div class="group-header">
                            <h4>Projeto Website</h4>
                            <div class="group-members">
                                <div class="member-avatar">JD</div>
                                <div class="member-avatar">MS</div>
                                <div class="member-avatar">RL</div>
                                <div class="member-avatar">+2</div>
                            </div>
                        </div>
                        <div class="group-progress">
                            <div class="progress-label">
                                <span>Progresso</span>
                                <span>68%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 68%"></div>
                            </div>
                        </div>
                        <div class="group-tasks">
                            <div class="group-task">
                                <div class="task-check checked"></div>
                                <div class="task-info">
                                    <div class="task-title">Design da Homepage</div>
                                    <div class="task-meta">
                                        <span class="task-assignee">Maria S.</span>
                                        <span class="task-date">Concluída</span>
                                    </div>
                                </div>
                            </div>
                            <div class="group-task">
                                <div class="task-check checked"></div>
                                <div class="task-info">
                                    <div class="task-title">Estrutura de Navegação</div>
                                    <div class="task-meta">
                                        <span class="task-assignee">João D.</span>
                                        <span class="task-date">Concluída</span>
                                    </div>
                                </div>
                            </div>
                            <div class="group-task active">
                                <div class="task-check"></div>
                                <div class="task-info">
                                    <div class="task-title">Implementação Frontend</div>
                                    <div class="task-meta">
                                        <span class="task-assignee">Rafael L.</span>
                                        <span class="task-date">Em andamento</span>
                                    </div>
                                </div>
                            </div>
                            <div class="group-task">
                                <div class="task-check"></div>
                                <div class="task-info">
                                    <div class="task-title">Testes e Otimização</div>
                                    <div class="task-meta">
                                        <span class="task-assignee">Não atribuída</span>
                                        <span class="task-date">Pendente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="group-features">
                    <div class="group-feature">
                        <div class="feature-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Convide Membros</h4>
                            <p>Adicione colegas, amigos ou familiares para colaborar em projetos e tarefas compartilhadas.</p>
                        </div>
                    </div>
                    <div class="group-feature">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Atribua Tarefas</h4>
                            <p>Distribua responsabilidades e acompanhe quem está trabalhando em cada parte do projeto.</p>
                        </div>
                    </div>
                    <div class="group-feature">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Comunique-se</h4>
                            <p>Discuta detalhes, compartilhe atualizações e mantenha todos na mesma página.</p>
                        </div>
                    </div>
                    <div class="group-feature">
                        <div class="feature-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Acompanhe o Progresso</h4>
                            <p>Visualize o andamento do projeto e a contribuição de cada membro em tempo real.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="perfil" class="profile-section">
            <h2>Seu <span class="highlight">Perfil</span>, Seu Estilo</h2>
            <div class="profile-container">
                <div class="profile-mockup">
                    <div class="profile-header">
                        <div class="profile-cover"></div>
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="profile-info">
                            <h3>Maria Silva</h3>
                            <p>Profissional Organizada</p>
                        </div>
                    </div>
                    <div class="profile-stats">
                        <div class="stat-box">
                            <div class="stat-value">87%</div>
                            <div class="stat-label">Conclusão</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">42</div>
                            <div class="stat-label">Tarefas</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">3</div>
                            <div class="stat-label">Grupos</div>
                        </div>
                    </div>
                    <div class="profile-productivity">
                        <h4>Produtividade Semanal</h4>
                        <div class="chart-container">
                            <div class="chart-bar" style="height: 30%"><span>S</span></div>
                            <div class="chart-bar" style="height: 45%"><span>T</span></div>
                            <div class="chart-bar" style="height: 80%"><span>Q</span></div>
                            <div class="chart-bar" style="height: 65%"><span>Q</span></div>
                            <div class="chart-bar" style="height: 50%"><span>S</span></div>
                            <div class="chart-bar" style="height: 20%"><span>S</span></div>
                            <div class="chart-bar" style="height: 15%"><span>D</span></div>
                        </div>
                    </div>
                </div>
                <div class="profile-features">
                    <div class="profile-feature">
                        <div class="feature-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h4>Personalize sua Experiência</h4>
                        <p>Escolha entre temas claro e escuro, organize categorias com cores e adapte a interface ao seu estilo.</p>
                    </div>
                    <div class="profile-feature">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Acompanhe seu Progresso</h4>
                        <p>Visualize estatísticas detalhadas sobre sua produtividade e evolução ao longo do tempo.</p>
                    </div>
                    <div class="profile-feature">
                        <div class="feature-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h4>Conquiste Objetivos</h4>
                        <p>Defina metas pessoais, acompanhe seu desempenho e celebre suas conquistas.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content">
                <h2>Pronto para <span class="highlight">Transformar</span> sua Produtividade?</h2>
                <p>Junte-se a milhares de usuários que já estão otimizando seu tempo e alcançando mais com o {{ env('APP_NAME', 'enfantIA') }}.</p>
                <div class="cta-buttons">
                    <a href="{{ route('register') }}" class="btn primary-btn">Comece Gratuitamente</a>
                </div>
            </div>
            <div class="cta-particles">
                <div class="particle p1"></div>
                <div class="particle p2"></div>
                <div class="particle p3"></div>
                <div class="particle p4"></div>
                <div class="particle p5"></div>
                <div class="particle p6"></div>
            </div>
        </section>
    </main>

    @include('layouts.footer')

    <button id="theme-toggle" class="theme-toggle" aria-label="Alternar modo claro/escuro">
        <i class="fas fa-moon"></i>
    </button>

    <script src="{{ asset('js/welcome/script.js') }}"></script>
</body>
</html>
