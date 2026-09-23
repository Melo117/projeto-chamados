<!doctype html>
<html lang="pt-BR" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Central de Chamados — Administração</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="./css/adminlte.css">
  <link rel="stylesheet" href="./css/chamados.css">
  <script src="./js/script.js"></script>
  <script></script>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <header class="app-header navbar navbar-expand bg-body">
      <div class="container-fluid">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#"><i class="bi bi-list"></i></a>
          </li>
          <li class="nav-item d-none d-md-block"><span class="nav-link text-secondary">Painel administrativo</span></li>
        </ul>
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item"><span id="api-status" class="nav-link small text-secondary"><i
                class="bi bi-circle-fill text-secondary me-1"></i> verificando API</span></li>
          <li class="nav-item dropdown"><a class="nav-link" href="#" data-bs-toggle="dropdown"
              aria-label="Notificações"><i class="bi bi-bell"></i><span class="navbar-badge badge text-bg-danger"
                id="notification-count">0</span></a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end"><span
                class="dropdown-item dropdown-header">Notificações</span>
              <div id="notification-preview">
                <div class="dropdown-item text-secondary small">Integração de notificações pendente na API</div>
              </div>
              <div class="dropdown-divider"></div><a href="#notifications" class="dropdown-item dropdown-footer"
                data-section="notifications">Ver central de notificações</a>
            </div>
          </li>
          <li class="nav-item dropdown user-menu"><a href="#" class="nav-link dropdown-toggle"
              data-bs-toggle="dropdown"><span class="user-avatar" id="user-avatar">A</span><span
                class="d-none d-md-inline ms-2" id="user-name">Administrador</span></a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <li class="user-header text-bg-primary"><span class="user-avatar user-avatar-lg"
                  id="user-avatar-large">A</span>
                <p id="user-header-name">Administrador<small id="user-header-role">Modo administrativo</small></p>
              </li>
              <li class="user-footer"><button class="btn btn-outline-primary btn-sm" id="profile-btn"><i
                    class="bi bi-person me-1"></i>Perfil</button><button class="btn btn-outline-danger btn-sm float-end"
                  id="logout-btn"><i class="bi bi-box-arrow-right me-1"></i>Sair</button></li>
            </ul>
          </li>
          <li class="nav-item dropdown"><a class="nav-link" href="#" data-bs-toggle="dropdown"><i
                class="bi bi-circle-half"></i></a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><button class="dropdown-item" data-bs-theme-value="light">Tema claro</button></li>
              <li><button class="dropdown-item" data-bs-theme-value="dark">Tema escuro</button></li>
            </ul>
          </li>
        </ul>
      </div>
    </header>
    <aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
      <div class="sidebar-brand"><a href="#overview" class="brand-link"><span class="brand-mark"><i
              class="bi bi-headset"></i></span><span class="brand-text fw-semibold">Chamados</span></a></div>
      <div class="sidebar-search px-3 pb-2">
        <div class="input-group input-group-sm"><span
            class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-search"></i></span><input
            id="sidebar-search" class="form-control bg-dark border-secondary text-white" placeholder="Filtrar menu">
        </div>
      </div>
      <div class="sidebar-wrapper">
        <nav>
          <ul class="nav sidebar-menu flex-column" role="menu">
            <li class="nav-header">OPERAÇÃO</li>
            <li class="nav-item"><a href="#overview" class="nav-link active" data-section="overview"><i
                  class="nav-icon bi bi-grid-1x2-fill"></i>
                <p>Visão geral</p>
              </a></li>
            <li class="nav-item"><a href="#tickets" class="nav-link" data-section="tickets"><i
                  class="nav-icon bi bi-ticket-detailed"></i>
                <p>Todos os chamados <span class="nav-badge" id="sidebar-ticket-count">0</span></p>
              </a></li>
            <li class="nav-item"><a href="#my-tickets" class="nav-link" data-section="my-tickets"><i
                  class="nav-icon bi bi-person-check"></i>
                <p>Meus chamados</p>
              </a></li>
            <li class="nav-item"><a href="#new-ticket" class="nav-link" data-section="new-ticket"><i
                  class="nav-icon bi bi-plus-circle"></i>
                <p>Abrir chamado</p>
              </a></li>
            <li class="nav-header">ADMINISTRAÇÃO</li>
            <li class="nav-item admin-only"><a href="#users" class="nav-link" data-section="users"><i
                  class="nav-icon bi bi-people"></i>
                <p>Usuários</p>
              </a></li>
            <li class="nav-item admin-only"><a href="#agents" class="nav-link" data-section="agents"><i
                  class="nav-icon bi bi-person-workspace"></i>
                <p>Funcionários</p>
              </a></li>
            <li class="nav-item admin-only"><a href="#categories" class="nav-link" data-section="categories"><i
                  class="nav-icon bi bi-tags"></i>
                <p>Categorias</p>
              </a></li>
            <li class="nav-header">ACOMPANHAMENTO</li>
            <li class="nav-item"><a href="#notifications" class="nav-link" data-section="notifications"><i
                  class="nav-icon bi bi-bell"></i>
                <p>Notificações <span class="nav-badge" id="sidebar-notification-count">0</span></p>
              </a></li>
            <li class="nav-item"><a href="#reports" class="nav-link" data-section="reports"><i
                  class="nav-icon bi bi-bar-chart"></i>
                <p>Relatórios</p>
              </a></li>
            <li class="nav-item"><a href="#api-info" class="nav-link" data-section="api-info"><i
                  class="nav-icon bi bi-diagram-3"></i>
                <p>Integração</p>
              </a></li>
          </ul>
        </nav>
      </div>
    </aside>
    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <h1 class="mb-1" id="page-title">Visão geral</h1>
              <p class="text-secondary mb-0" id="page-subtitle">Administre o ciclo completo de atendimento.</p>
            </div><button class="btn btn-primary" id="header-new-ticket" data-section="new-ticket"><i
                class="bi bi-plus-lg me-2"></i>Novo chamado</button>
          </div>
        </div>
      </div>
      <div class="app-content">
        <div class="container-fluid">
          <div id="alert-area"></div>
          <section class="page-section active" data-page="overview">
            <div class="admin-context mb-3"><i class="bi bi-shield-check"></i>
              <div><strong>Visão administrativa</strong><span>Você está visualizando a operação completa. As restrições
                  por perfil já estão separadas no menu para facilitar a próxima etapa de autorização.</span></div>
              <select id="role-preview" class="form-select form-select-sm">
                <option value="ADMINISTRADOR">Administrador</option>
                <option value="ATENDENTE">Atendente</option>
                <option value="SOLICITANTE">Solicitante</option>
              </select>
            </div>
            <div class="row g-3 mb-4">
              <div class="col-sm-6 col-xl-3">
                <div class="metric-card metric-primary">
                  <div><span>Total de chamados</span><strong id="metric-total">0</strong><small>visão
                      administrativa</small></div><i class="bi bi-ticket-detailed"></i>
                </div>
              </div>
              <div class="col-sm-6 col-xl-3">
                <div class="metric-card metric-warning">
                  <div><span>Abertos</span><strong id="metric-open">0</strong><small>aguardando triagem</small></div><i
                    class="bi bi-hourglass-split"></i>
                </div>
              </div>
              <div class="col-sm-6 col-xl-3">
                <div class="metric-card metric-info">
                  <div><span>Em atendimento</span><strong id="metric-progress">0</strong><small>em tratamento</small>
                  </div><i class="bi bi-person-workspace"></i>
                </div>
              </div>
              <div class="col-sm-6 col-xl-3">
                <div class="metric-card metric-success">
                  <div><span>Resolvidos</span><strong id="metric-done">0</strong><small>resolvidos ou fechados</small>
                  </div><i class="bi bi-check2-circle"></i>
                </div>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-xl-8">
                <div class="card h-100">
                  <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-clock-history me-2 text-primary"></i>Chamados recentes</h3><a
                      href="#tickets" data-section="tickets" class="btn btn-sm btn-outline-primary">Ver todos</a>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle mb-0">
                        <thead>
                          <tr>
                            <th>Chamado</th>
                            <th>Solicitante</th>
                            <th>Status</th>
                            <th>Atualizado</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody id="recent-tickets">
                          <tr>
                            <td colspan="5" class="empty-state">Carregando...</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4">
                <div class="card h-100">
                  <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-bar-chart me-2 text-primary"></i>Distribuição por status</h3>
                  </div>
                  <div class="card-body" id="status-summary">
                    <div class="empty-state">Carregando...</div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="tickets">
            <div class="card">
              <div class="card-header d-flex flex-wrap justify-content-between gap-2">
                <h3 class="card-title"><i class="bi bi-ticket-detailed me-2 text-primary"></i>Todos os chamados</h3>
                <div class="d-flex gap-2"><select id="status-filter" class="form-select form-select-sm">
                    <option value="">Todos os status</option>
                    <option>ABERTO</option>
                    <option>EM_ANALISE</option>
                    <option>EM_ATENDIMENTO</option>
                    <option>RESOLVIDO</option>
                    <option>FECHADO</option>
                    <option>CANCELADO</option>
                    <option>RECUSADO</option>
                  </select><input id="ticket-search" class="form-control form-control-sm"
                    placeholder="Buscar chamado..."></div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead>
                      <tr>
                        <th>Número</th>
                        <th>Assunto</th>
                        <th>Categoria</th>
                        <th>Solicitante</th>
                        <th>Atendente</th>
                        <th>Status</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody id="tickets-table">
                      <tr>
                        <td colspan="7" class="empty-state">Carregando...</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="my-tickets">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="bi bi-person-check me-2 text-primary"></i>Meus chamados</h3>
              </div>
              <div class="card-body">
                <div class="empty-state">A filtragem final por usuário será aplicada quando o endpoint de sessão
                  retornar o usuário autenticado. A mesma tabela de chamados será reutilizada aqui.</div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="new-ticket">
            <div class="row justify-content-center">
              <div class="col-xl-9">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Abrir novo chamado</h3>
                  </div>
                  <form id="new-ticket-form">
                    <div class="card-body">
                      <div class="form-intro"><i class="bi bi-info-circle"></i><span>O chamado começa como
                          <strong>ABERTO</strong> e terá uma conversa própria na tela de detalhes.</span></div>
                      <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Número *</label><input class="form-control"
                            id="ticket-number" required maxlength="30" placeholder="CH-2026-001"></div>
                        <div class="col-md-8"><label class="form-label">Assunto *</label><input class="form-control"
                            id="ticket-name" required maxlength="200" placeholder="Resumo do problema"></div>
                        <div class="col-md-6"><label class="form-label">Categoria *</label><select class="form-select"
                            id="ticket-category" required>
                            <option value="">Selecione</option>
                          </select></div>
                        <div class="col-md-6"><label class="form-label">Atendente *</label><select class="form-select"
                            id="ticket-assignee" required>
                            <option value="">Selecione</option>
                          </select></div>
                        <div class="col-12"><label class="form-label">Descrição *</label><textarea class="form-control"
                            id="ticket-description" rows="6" required
                            placeholder="Informe o problema, contexto e impacto..."></textarea></div>
                      </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2"><button type="reset"
                        class="btn btn-light">Limpar</button><button class="btn btn-primary" type="submit"><i
                          class="bi bi-send me-2"></i>Criar chamado</button></div>
                  </form>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="users">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h3 class="card-title"><i class="bi bi-people me-2 text-primary"></i>Gestão de usuários</h3><button
                  class="btn btn-sm btn-primary" data-admin-action="create-user"><i class="bi bi-plus-lg me-1"></i>Novo
                  usuário</button>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table align-middle mb-0">
                    <thead>
                      <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody id="users-table">
                      <tr>
                        <td colspan="5" class="empty-state">Carregando...</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="agents">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h3 class="card-title"><i class="bi bi-person-workspace me-2 text-primary"></i>Funcionários e atendentes
                </h3><button class="btn btn-sm btn-primary" data-admin-action="create-agent"><i
                    class="bi bi-plus-lg me-1"></i>Novo funcionário</button>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table align-middle mb-0">
                    <thead>
                      <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Matrícula</th>
                        <th>Situação</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody id="agents-table">
                      <tr>
                        <td colspan="5" class="empty-state">Carregando...</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="categories">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h3 class="card-title"><i class="bi bi-tags me-2 text-primary"></i>Gestão de categorias</h3><button
                  class="btn btn-sm btn-primary" data-admin-action="create-category"><i
                    class="bi bi-plus-lg me-1"></i>Nova categoria</button>
              </div>
              <div class="card-body">
                <div class="row g-3" id="categories-grid">
                  <div class="empty-state">Carregando...</div>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="notifications">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="bi bi-bell me-2 text-primary"></i>Central de notificações</h3>
              </div>
              <div class="card-body">
                <div class="feature-placeholder"><i class="bi bi-bell-slash"></i>
                  <div><strong>Estrutura pronta para notificações</strong>
                    <p>A tabela <code>notificacoes</code> existe no banco, mas sua API ainda não possui métodos de
                      listagem, leitura e marcação como lida. Quando esses endpoints forem adicionados, esta área poderá
                      ser conectada sem mudar o layout.</p>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="reports">
            <div class="row g-3">
              <div class="col-lg-8">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-bar-chart me-2 text-primary"></i>Relatórios operacionais</h3>
                  </div>
                  <div class="card-body" id="report-bars">
                    <div class="empty-state">Os indicadores atuais são calculados a partir dos chamados carregados.
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Histórico do chamado</h3>
                  </div>
                  <div class="card-body">
                    <div class="feature-placeholder compact"><i class="bi bi-clock-history"></i>
                      <p>A tabela <code>historico_chamados</code> existe no banco, mas ainda falta um endpoint PHP para
                        exibir as alterações de status e ações.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <section class="page-section" data-page="api-info">
            <div class="row g-3">
              <div class="col-lg-7">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-diagram-3 me-2 text-primary"></i>APIs conectadas</h3>
                  </div>
                  <div class="card-body">
                    <div class="api-module"><i class="bi bi-ticket-detailed"></i>
                      <div><strong>Chamados</strong><span>listar · buscar · criar · atualizar · excluir</span></div>
                    </div>
                    <div class="api-module"><i class="bi bi-chat-left-text"></i>
                      <div><strong>Conversas</strong><span>Cada chamado possui sua própria lista de mensagens, carregada
                          pelo chamado_id.</span></div>
                    </div>
                    <div class="api-module"><i class="bi bi-paperclip"></i>
                      <div><strong>Anexos</strong><span>Estrutura preparada para anexos por chamado.</span></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-5">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Endpoint</h3>
                  </div>
                  <div class="card-body"><code id="api-endpoint"></code>
                    <hr>
                    <p class="small text-secondary mb-0">Interface baseada nas rotas atuais de
                      <strong>api/index.php</strong>.</p>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
    <footer class="app-footer"><strong>Central de Chamados</strong><span class="text-secondary ms-2">Painel
        administrativo</span><span class="float-end text-secondary">v1.1</span></footer>
  </div>
  <div class="modal fade" id="ticket-modal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title" id="modal-ticket-title">Detalhes do chamado</h5><span class="text-secondary small"
              id="modal-ticket-meta"></span>
          </div><button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-lg-7">
              <div class="detail-panel">
                <div class="d-flex justify-content-between gap-3">
                  <div><span class="text-secondary small">ASSUNTO</span>
                    <h4 id="modal-ticket-name"></h4>
                  </div><span id="modal-ticket-status"></span>
                </div>
                <p id="modal-ticket-description" class="detail-description"></p>
                <div class="detail-grid">
                  <div><span>Categoria</span><strong id="modal-ticket-category"></strong></div>
                  <div><span>Solicitante</span><strong id="modal-ticket-requester"></strong></div>
                  <div><span>Atendente</span><strong id="modal-ticket-assignee"></strong></div>
                  <div><span>Abertura</span><strong id="modal-ticket-opened"></strong></div>
                </div>
                <div class="ticket-actions mt-4"><label class="form-label">Alterar status</label>
                  <div class="input-group"><select id="modal-status-select" class="form-select"></select><button
                      id="save-ticket-status" class="btn btn-primary">Salvar status</button></div>
                </div>
                <div class="mt-3">
                  <h6><i class="bi bi-paperclip me-2 text-primary"></i>Anexos deste chamado</h6>
                  <div id="modal-attachments" class="attachment-list">
                    <div class="empty-state p-2">Carregando anexos...</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5">
              <h6 class="mb-3"><i class="bi bi-chat-left-text me-2 text-primary"></i>Conversa do chamado</h6>
              <div class="conversation-caption">Esta conversa pertence exclusivamente a este chamado.</div>
              <div id="modal-messages" class="messages-list">
                <div class="empty-state p-3">Carregando conversa...</div>
              </div>
              <form id="message-form" class="mt-3">
                <div class="input-group"><input id="message-input" class="form-control"
                    placeholder="Escreva uma mensagem..." required><button class="btn btn-primary"><i
                      class="bi bi-send"></i></button></div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="login-modal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <form id="login-form">
          <div class="modal-body p-4">
            <div class="text-center mb-3"><span class="brand-mark"><i class="bi bi-headset"></i></span>
              <h4 class="mt-3">Entrar no painel</h4>
              <p class="text-secondary small">A sessão usa a autenticação da API.</p>
            </div><label class="form-label">E-mail</label><input id="login-email" class="form-control mb-3" type="email"
              required placeholder="seu@email.com"><label class="form-label">Senha</label><input id="login-password"
              class="form-control mb-3" type="password" required>
            <div id="login-error" class="text-danger small mb-3"></div><button
              class="btn btn-primary w-100">Entrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/adminlte.min.js"></script>
  <script src="./js/chamados-dashboard.js"></script>
</body>

</html>