document.addEventListener("DOMContentLoaded", function () {
    if (window.innerWidth >= 992) {
        document.querySelectorAll(".nav-item.dropdown").forEach(function (dropdown) {
            dropdown.addEventListener("mouseenter", function () {
                let toggle = dropdown.querySelector(".dropdown-toggle")
                    || dropdown.querySelector('[data-bs-toggle="dropdown"]');
                let menu = dropdown.querySelector(".dropdown-menu");

                if (!toggle || !menu)
                    return;

                toggle.classList.add("show");
                menu.classList.add("show");
                toggle.setAttribute("aria-expanded", "true");
            });

            dropdown.addEventListener("mouseleave", function () {
                let toggle = dropdown.querySelector(".dropdown-toggle")
                    || dropdown.querySelector('[data-bs-toggle="dropdown"]');
                let menu = dropdown.querySelector(".dropdown-menu");

                if (!toggle || !menu)
                    return;

                toggle.classList.remove("show");
                menu.classList.remove("show");
                toggle.setAttribute("aria-expanded", "false");
            });
        });
    }
});