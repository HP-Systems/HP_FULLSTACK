document.addEventListener("DOMContentLoaded", function(event) {
    const showNavbar = (toggleId, navId, bodyId, headerId) => {
        const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId),
            bodypd = document.getElementById(bodyId),
            headerpd = document.getElementById(headerId);

        if (toggle && nav && bodypd && headerpd) {
            toggle.addEventListener("click", () => {
                nav.classList.toggle("show");
                bodypd.classList.toggle("body-pd");
                headerpd.classList.toggle("body-pd");
            });
            
            nav.addEventListener('mouseenter', () => {
                nav.classList.add('show');
                bodypd.classList.add('body-pd');
                headerpd.classList.add('body-pd');
                nav.classList.add('sidebar-closed');
            });

            nav.addEventListener('mouseleave', () => {
                nav.classList.remove('show');
                bodypd.classList.remove('body-pd');
                headerpd.classList.remove('body-pd');
                nav.classList.remove('sidebar-closed');
            });
        }
    };

    showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

    const rightMenuToggle = document.getElementById("menu-toggle-right");
    const rightNavBar = document.getElementById("nav-bar-right");

    if (rightMenuToggle && rightNavBar) {
        rightMenuToggle.addEventListener("click", () => {
            rightNavBar.classList.toggle("mostrar");
        });
    }


    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link');

    function colorLink() {
        linkColor.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    }

    linkColor.forEach(l => l.addEventListener('click', colorLink));

    const linkColorRight = document.querySelectorAll('.nav_right');

    function colorLinkRight() {
        linkColorRight.forEach(l => l.classList.remove('activ_right'));
        this.classList.add('activ_right');
    }
    
    linkColorRight.forEach(l => l.addEventListener('click', colorLinkRight));

    const reportToggle = document.querySelector('.report-toggle');

    reportToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const submenu = document.querySelector('.report-submenu');
        const arrowIcon = this.querySelector('.arrow');

        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';

        if (submenu.style.display === 'block') {
            arrowIcon.classList.remove('fa-chevron-right');
            arrowIcon.classList.add('fa-chevron-down');
        } else {
            arrowIcon.classList.remove('fa-chevron-down');
            arrowIcon.classList.add('fa-chevron-right');
        }
    });

    const reportLinks = document.querySelectorAll('.report-submenu .nav_link.sub');

    reportLinks.forEach(link => {
        link.addEventListener('click', function() {
            reportLinks.forEach(l => l.classList.remove('active'));

            this.classList.add('active');
        });
    });

});
