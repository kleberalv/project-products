<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gerenciamento de Produtos')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,.05);
            border: none;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,.02);
        }
        /* Corrigir botões de paginação */
        .pagination {
            margin: 0;
            flex-wrap: wrap;
        }
        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
        .pagination .page-item {
            margin: 0 2px;
        }
        /* Responsividade de botões */
        @media (max-width: 768px) {
            .btn-group-sm {
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            .btn-group-sm .btn {
                flex: 1;
                min-width: 40px;
            }
            .pagination .page-link {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }
        }
        /* Spinner Global */
        #loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        #loading-overlay.active {
            display: flex;
        }
        .spinner-container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .spinner-border-custom {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('produtos.index') }}">
                <i class="bi bi-box-seam"></i> Gerenciamento de Produtos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('produtos.index') ? 'active' : '' }}" 
                               href="{{ route('produtos.index') }}">
                                <i class="bi bi-box-seam"></i> Peças
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('produtos.create') ? 'active' : '' }}" 
                               href="{{ route('produtos.create') }}">
                                <i class="bi bi-plus-circle"></i> Nova Peça
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" 
                               href="{{ route('usuarios.index') }}">
                                <i class="bi bi-people"></i> Usuários
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><span class="dropdown-item-text">{{ Auth::user()->email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alertas -->
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="bi bi-exclamation-triangle"></i> Erros de validação:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Conteúdo -->
    <main class="container-fluid">
        @yield('content')
    </main>
    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner-container">
            <div class="spinner-border text-primary spinner-border-custom" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-3 mb-0 text-muted" id="loading-message">Carregando...</p>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-exclamation-circle"></i> Confirmação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage">Tem certeza que deseja realizar esta ação?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmButton">
                        <i class="bi bi-trash"></i> Deletar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="mt-5 py-4 bg-light border-top">
        <div class="container text-center text-muted">
            <small>&copy; {{ date('Y') }} Gerenciamento de Produtos - Laravel {{ app()->version() }}</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let pendingFormId = null;
        let confirmModal;

        // Spinner Global para CRUD
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('loading-overlay');
            const loadingMessage = document.getElementById('loading-message');
            confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            const currencyFormatter = new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // Máscara de moeda com limite máximo
            document.querySelectorAll('.currency-input').forEach(function(input) {
                const hiddenField = document.getElementById(input.dataset.target);
                const max = parseFloat(input.dataset.max || '99999999.99');

                const applyCurrencyMask = function(rawValue) {
                    const digits = (rawValue || '').replace(/\D/g, '');

                    if (!digits.length) {
                        input.value = '';
                        if (hiddenField) hiddenField.value = '';
                        return;
                    }

                    let numberValue = parseInt(digits, 10) / 100;

                    if (numberValue > max) {
                        numberValue = max;
                    }

                    input.value = currencyFormatter.format(numberValue);

                    if (hiddenField) {
                        hiddenField.value = numberValue.toFixed(2);
                    }
                };

                const initialValue = (hiddenField && hiddenField.value) || input.value;
                if (initialValue) {
                    applyCurrencyMask(initialValue);
                }

                input.addEventListener('input', function(event) {
                    applyCurrencyMask(event.target.value);
                });

                input.addEventListener('blur', function(event) {
                    applyCurrencyMask(event.target.value);
                });
            });
            
            // Função para mostrar o spinner com uma mensagem customizada
            function showSpinner(message = 'Carregando...') {
                loadingMessage.textContent = message;
                overlay.classList.add('active');
            }
            
            // Função para exibir modal de confirmação
            function showConfirmModal(message, formId) {
                pendingFormId = formId;
                document.getElementById('confirmMessage').textContent = message;
                confirmModal.show();
            }
            
            // Botão de confirmação na modal
            document.getElementById('confirmButton').addEventListener('click', function() {
                confirmModal.hide();
                if (pendingFormId) {
                    showSpinner('Deletando...');
                    document.getElementById(pendingFormId).submit();
                }
            });
            
            // Capturar submissão de formulários
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Mostrar spinner se não for GET, ou se for GET mas tiver data-show-spinner
                    const isGet = form.method.toLowerCase() === 'get';
                    const showSpinnerForGet = form.getAttribute('data-show-spinner') === 'true';
                    
                    if (!isGet || showSpinnerForGet) {
                        showSpinner('Aguarde...');
                    }
                });
            });
            
            // Capturar cliques em botões/links com data-action
            document.querySelectorAll('[data-action]').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    const action = element.getAttribute('data-action');
                    showSpinner(action);
                });
            });
            
            // Capturar cliques em links de ação (editar, excluir, voltar, etc)
            document.querySelectorAll('a.btn-warning, a.btn-info, a.btn-secondary, a.btn').forEach(function(link) {
                // Não mostrar spinner em links de navegação padrão (navbar, etc)
                if (link.classList.contains('nav-link') || link.classList.contains('dropdown-item')) {
                    return;
                }
                
                link.addEventListener('click', function(e) {
                    const linkText = link.textContent.trim();
                    let message = 'Carregando...';
                    
                    if (linkText.includes('Voltar') || linkText.includes('Cancelar')) {
                        message = 'Voltando...';
                    } else if (linkText.includes('Editar') || linkText.includes('Pencil')) {
                        message = 'Carregando formulário...';
                    } else if (linkText.includes('Deletar') || linkText.includes('Trash')) {
                        message = 'Deletando...';
                    } else if (linkText.includes('Visualizar') || linkText.includes('Eye')) {
                        message = 'Carregando detalhes...';
                    } else if (linkText.includes('Novo')) {
                        message = 'Abrindo formulário...';
                    }
                    
                    showSpinner(message);
                });
            });
            
            // Disponibilizar função global para confirmação
            window.confirmarExclusao = function(id) {
                showConfirmModal('Tem certeza que deseja deletar este produto?', 'delete-form-' + id);
            };

            document.querySelectorAll('.pagination a').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    showSpinner('Carregando página...');
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
