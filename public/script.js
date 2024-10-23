$(document).ready(function() {
    // Constantes y variables
    let currentPage = 1;
    let perPage = 5;

    // Funciones
    function showToast(message, isError = false) {
        const toastEl = $('#toastMessage');
        toastEl.find('.toast-body').text(message);
        toastEl.removeClass('bg-primary bg-danger').addClass(isError ? 'bg-danger' : 'bg-primary');
        const toast = new bootstrap.Toast(toastEl[0]);
        toast.show();
    }

    function showLoading(show) {
        if(show) {
            $('#productsTable tbody').html(`
                <tr class="spinner-row">
                    <td colspan="5" class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </td>
                </tr>
            `);
        }
        else {
            $('.spinner-row').remove();
        }
    }

    // Autenticación
    function getAuthHeader() {
        return 'Bearer ' + localStorage.getItem('authToken');
    }

    function isAuthenticated() {
        return !!localStorage.getItem('authToken');
    }

    $('#btn-logout').click(function() {
        localStorage.removeItem('authToken');
        window.location.href = '/login.html';
    });    

    // Token CSRF
    let csrfToken = '';

    $.ajax({
        url: '/csrf-token',
        method: 'GET',
        success: function(data) {
            csrfToken = data.csrf_token;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            });
        },
        error: function() {
            showToast('Error al obtener el token CSRF', true);
        }
    });

    /* CRUD productos */
    // Obtener productos
    function loadProducts(query = '', page = 1) {
        showLoading(true);

        $.ajax({
            url: `/products?page=${page}&per_page=${perPage}${query ? `&search=${query}` : ''}`,
            method: 'GET',
            headers: {
                'Authorization': getAuthHeader(),
            },
            success: function(response) {
                const products = response.data;
                let rows = '';
                products.forEach(function(product) {
                    rows += `<tr>
                                <td>${product.id}</td>
                                <td>${product.title}</td>
                                <td>${product.price}</td>
                                <td>${product.created_at}</td>
                                <td>
                                    <button class="btn btn-secondary btn-edit" data-id="${product.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-delete" data-id="${product.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                             </tr>`;
                });
                $('#productsTable tbody').html(rows);
                renderPagination(response);
            },
            error: function(response) {
                showToast('Error al cargar los productos', true);
                renderPagination(response);
            },
            complete: function() {
                showLoading(false);
            }
        });
    }

    // Agregar o editar producto
    $('#saveProduct').click(function() {
        let id = $('#productId').val();
        let title = $('#title').val();
        let price = $('#price').val();

        let url = id ? `/products/${id}` : '/products';
        let method = id ? 'PUT' : 'POST';

        showLoading(true);
        $.ajax({
            url: url,
            method: method,
            headers: {
                'Authorization': getAuthHeader(),
            },
            data: { title: title, price: price },
            success: function() {
                $('#productModal').modal('hide');
                showToast(id ? 'Producto actualizado' : 'Producto agregado');
            },
            error: function(data) {
                showToast(`Error al guardar el producto${data ? `: ${data.responseJSON.error || data.responseJSON.message}` : ''}`, true);
            },
            complete: function() {
                loadProducts();
                showLoading(false);
            }
        });
    });

    // Agregar producto
    $('#btn-add').click(function() {
        $('#productModalLabel').html('Agregar Producto');
        $('#productId').val('');
        $('#productForm')[0].reset();
        $('#productModal').modal('show');
    });

    // Editar producto
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $('#productModal').modal('show');
        $.ajax({
            url: `/products/${id}`,
            method: 'GET',
            headers: {
                'Authorization': getAuthHeader(),
            },
            success: function(product) {
                $('#productModalLabel').html(`Editar producto #${id}`);
                $('#productId').val(product.id);
                $('#title').val(product.title);
                $('#price').val(product.price);
            },
            error: function() {
                showToast('Error al cargar los detalles del producto', true);
            }
        });
    });

    // Eliminar producto
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        showLoading(true);
        $.ajax({
            url: `/products/${id}`,
            type: 'DELETE',
            headers: {
                'Authorization': getAuthHeader(),
            },
            success: function(response) {
                showToast('Producto eliminado');
            },
            error: function(data) {
                showToast(`Error al eliminar el producto${data ? `: ${data.responseJSON.error || data.responseJSON.message}` : ''}`, true);
            },
            complete: function() {
                loadProducts();
                showLoading(false);
            }
        });
    });

    // Buscar productos
    $('#search').on('input', function () {
        const query = $(this).val();
        loadProducts(query);
    });

    /* Paginación */
    // Renderizar botones de paginación
    function renderPagination(response = {}) {
        console.log(response)
        const paginationEl = $('#pagination');
        const totalEl = $('#totalProducts');
        paginationEl.empty();

        totalEl.html(response.total || '0');
        if(response.total > 0) {
            for(let page = 1; page <= response.last_page; page++) {
                paginationEl.append(`
                    <li class="page-item ${page === response.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${page}">${page}</a>
                    </li>
                `);
            }
        }
    }

    // Cambiar de página
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        currentPage = $(this).data('page');
        const query = $('#search').val();
        loadProducts(query, currentPage);
    });    

    /* Init */
    // Controlar autenticación
    if(!isAuthenticated()) {
        window.location.href = '/login.html';
    }

    // Cargar productos
    loadProducts();
});