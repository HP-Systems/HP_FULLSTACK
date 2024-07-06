const rowsPerPage = 10;
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function () {
    dibujarTable();
    renderPagination();
});

function dibujarTable() {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedData = serviciosData.slice(start, end);

    const tableBody = document.getElementById('serviciosTableBody');
    tableBody.innerHTML = '';

    paginatedData.forEach(service => {
        const row = `
            <tr>
                <td>${service.nombre}</td>
                <td>${service.descripcion}</td>
                <td>$${service.precio}</td>
                <td>${service.tipo}</td>
                <td>
                    ${service.status == 1 ? '<button disabled type="button" class="btn btn-success">ACTIVO</button>' : '<button disabled type="button" class="btn btn-danger">INACTIVO</button>'}
                </td>
                <td>
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#servicioModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal"
                            data-id="${service.id}" data-status="${service.status}">
                        <i class="fas fa-sync"></i>
                    </button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

function renderPagination() {
    const pageCount = Math.ceil(serviciosData.length / rowsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    const maxPagesToShow = 3;
    const half = Math.floor(maxPagesToShow / 2);
    let startPage = Math.max(1, currentPage - half);
    let endPage = Math.min(pageCount, currentPage + half);

    if (currentPage <= half) {
        endPage = Math.min(pageCount, maxPagesToShow);
    }

    if (currentPage + half > pageCount) {
        startPage = Math.max(1, pageCount - maxPagesToShow + 1);
    }

    if (currentPage > 1) {
        const prevPage = document.createElement('li');
        prevPage.classList.add('page-item');
        prevPage.innerHTML = `
            <a class="page-link" href="#">&laquo;</a>
        `;
        prevPage.querySelector('a').addEventListener('click', function () {
            goToPage(currentPage - 1);
        });
        pagination.appendChild(prevPage);
    }

    for (let i = startPage; i <= endPage; i++) {
        const pageItem = document.createElement('li');
        pageItem.classList.add('page-item');
        pageItem.innerHTML = `
            <a class="page-link" href="#" style="${i === currentPage ? 'background-color: #65999C !important; color: white !important;' : ''}">${i}</a>
        `;
        pageItem.querySelector('a').addEventListener('click', function () {
            goToPage(i);
        });
        pagination.appendChild(pageItem);
    }

    if (currentPage < pageCount) {
        const nextPage = document.createElement('li');
        nextPage.classList.add('page-item');
        nextPage.innerHTML = `
            <a class="page-link" href="#">&raquo;</a>
        `;
        nextPage.querySelector('a').addEventListener('click', function () {
            goToPage(currentPage + 1);
        });
        pagination.appendChild(nextPage);
    }
}

function goToPage(page) {
    currentPage = page;
    dibujarTable();
    renderPagination();
}