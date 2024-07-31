$(document).ready(function() {
    try{
    var rowsPerPage = 6;
    var rows = $('#card-container .card');
    var rowsCount = rows.length;
    var pageCount = Math.ceil(rowsCount / rowsPerPage);
    var pagination = $('.pagination');
    let currentPage = 0;

    for (var i = 0; i < pageCount; i++) {
        pagination.append(`
            <li class="page-item">
                <a class="page-link" href="javascript:void(0);">${i + 1}</a>
            </li>
        `);
    }

    pagination.find('li:first-child').addClass('active-page');

    function showPage(page) {
        rows.hide();
        rows.slice((page - 1) * rowsPerPage, page * rowsPerPage).show();
    }

    showPage(1);

    pagination.on('click', 'li', function(e) {
        e.preventDefault();
        var page = $(this).index() + 1;
        currentPage = page;
        pagination.find('li').removeClass('active-page');
        $(this).addClass('active-page');
        showPage(page);
    });
}catch(e){
    console.log('Error')
}
});

