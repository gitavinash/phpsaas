document.addEventListener("DOMContentLoaded", function () {
  "use strict";

  const pageConfig = window.pageConfig || {};
  const pageType = pageConfig.pageType || "";
  const responseMsg = pageConfig.responseMsg || "";

  const body = document.body;
  const aside = document.querySelector("aside");
  const main = document.querySelector("main");
  const toggleBtn = document.getElementById("nav-toggle");
  const overlay = document.getElementById("nav-overlay");

  // ==========================================
  // Sidebar Search
  // ==========================================

  const menuSearch = document.getElementById("side-nav-search");
  const menuRoot = document.querySelector("nav.side-nav > ul");

  if (menuSearch && menuRoot) {
    const menuItems = menuRoot.children;
    let searchTimeout;

    menuSearch.addEventListener("input", function () {
      clearTimeout(searchTimeout);

      searchTimeout = setTimeout(function () {
        const searchTerm = menuSearch.value.toLowerCase().trim();

        for (let i = 0; i < menuItems.length; i++) {
          const item = menuItems[i];
          const topAnchor = item.firstElementChild;

          if (!topAnchor || topAnchor.tagName !== "A") {
            continue;
          }

          let searchableText = topAnchor.textContent.toLowerCase();

          const subLinks = item.querySelectorAll(
            ".side-menu a, .bottom-menu a",
          );

          for (let j = 0; j < subLinks.length; j++) {
            searchableText += " " + subLinks[j].textContent.toLowerCase();
          }

          item.style.display =
            searchTerm === "" || searchableText.indexOf(searchTerm) !== -1
              ? ""
              : "none";
        }

        if (typeof updateSidebar === "function") {
          requestAnimationFrame(updateSidebar);
        }
      }, 150);
    });
  }

  // ==========================================
  // Sticky Sidebar
  // ==========================================

  let updateSidebar = null;

  if (aside) {
    const headerHeight = 62;
    let lastScrollY = -1;
    let lastSidebarHeight = -1;
    let lastViewportHeight = -1;

    aside.style.setProperty("height", "auto", "important");
    aside.style.minHeight = "calc(100vh - 62px)";

    updateSidebar = function () {
      // Disable sticky logic on mobile
      if (window.innerWidth <= 1024) {
        aside.style.position = "";
        aside.style.top = "";
        aside.style.bottom = "";
        if (main) {
          main.style.minHeight = "";
        }
        return;
      }

      const viewportHeight = window.innerHeight;
      const sidebarHeight = aside.scrollHeight;
      const currentScrollY = window.scrollY;

      if (main && (sidebarHeight !== lastSidebarHeight || viewportHeight !== lastViewportHeight || lastScrollY === -1)) {
        if (sidebarHeight + headerHeight > viewportHeight) {
          main.style.minHeight = (sidebarHeight + headerHeight) + "px";
        } else {
          main.style.minHeight = "";
        }
      }

      if (
        currentScrollY === lastScrollY &&
        sidebarHeight === lastSidebarHeight &&
        viewportHeight === lastViewportHeight
      ) {
        return;
      }

      if (sidebarHeight <= viewportHeight - headerHeight) {
        aside.style.position = "fixed";
        aside.style.top = headerHeight + "px";
        aside.style.bottom = "auto";

        lastScrollY = currentScrollY;
        lastSidebarHeight = sidebarHeight;
        lastViewportHeight = viewportHeight;
        return;
      }

      if (lastScrollY === -1) {
        if (currentScrollY + viewportHeight >= headerHeight + sidebarHeight) {
          aside.style.position = "fixed";
          aside.style.top = "auto";
          aside.style.bottom = "0";
        } else {
          aside.style.position = "absolute";
          aside.style.top = headerHeight + "px";
          aside.style.bottom = "auto";
        }

        lastScrollY = currentScrollY;
        lastSidebarHeight = sidebarHeight;
        lastViewportHeight = viewportHeight;
        return;
      }

      const scrollingDown = currentScrollY > lastScrollY;
      const position = aside.style.position;

      if (scrollingDown) {
        if (
          position === "fixed" &&
          aside.style.bottom !== "0px" &&
          aside.style.bottom !== "0"
        ) {
          aside.style.position = "absolute";
          aside.style.top = currentScrollY + headerHeight + "px";
          aside.style.bottom = "auto";
        }

        if (aside.style.position === "absolute") {
          const currentTop = parseFloat(aside.style.top) || 0;

          if (currentScrollY + viewportHeight >= currentTop + sidebarHeight) {
            aside.style.position = "fixed";
            aside.style.top = "auto";
            aside.style.bottom = "0";
          }
        }
      } else {
        if (
          position === "fixed" &&
          (aside.style.bottom === "0px" || aside.style.bottom === "0")
        ) {
          const currentTop = currentScrollY + viewportHeight - sidebarHeight;

          aside.style.position = "absolute";
          aside.style.top = currentTop + "px";
          aside.style.bottom = "auto";
        }

        if (aside.style.position === "absolute") {
          const currentTop = parseFloat(aside.style.top) || 0;

          if (currentScrollY + headerHeight <= currentTop) {
            aside.style.position = "fixed";
            aside.style.top = headerHeight + "px";
            aside.style.bottom = "auto";
          }
        }
      }

      lastScrollY = currentScrollY;
      lastSidebarHeight = sidebarHeight;
      lastViewportHeight = viewportHeight;
    };

    window.addEventListener("scroll", updateSidebar, {
      passive: true,
    });

    window.addEventListener("resize", updateSidebar, {
      passive: true,
    });

    aside.addEventListener("click", function () {
      requestAnimationFrame(updateSidebar);
    });

    aside.addEventListener("transitionend", updateSidebar);

    updateSidebar();
  }

  // ==========================================
  // Mobile Navigation
  // ==========================================

  const mediaQuery = window.matchMedia("(max-width: 1024px)");

  function closeMobileNav() {
    if (!body || !aside || !overlay) {
      return;
    }

    body.classList.remove("mobile-nav-open");
    aside.classList.remove("open");
    overlay.classList.remove("show");
  }

  if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      if (!mediaQuery.matches) {
        return;
      }

      if (aside && aside.classList.contains("open")) {
        closeMobileNav();
      } else {
        body.classList.add("mobile-nav-open");

        if (aside) {
          aside.classList.add("open");
        }

        if (overlay) {
          overlay.classList.add("show");
        }
      }
    });
  }

  if (overlay) {
    overlay.addEventListener("click", closeMobileNav);
  }

  window.addEventListener("resize", function () {
    if (!mediaQuery.matches) {
      closeMobileNav();
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeMobileNav();
    }
  });

  document.addEventListener("click", function (e) {
    if (!mediaQuery.matches || !aside) {
      return;
    }

    const insideSidebar = e.target.closest("aside");
    const clickedToggle = e.target.closest("#nav-toggle");

    if (!insideSidebar && !clickedToggle && aside.classList.contains("open")) {
      closeMobileNav();
    }
  });

  // ==========================================
  // Alert Message
  // ==========================================

  if (responseMsg) {
    alert(responseMsg);
  }

  // ==========================================
  // Before Unload
  // ==========================================

  if (pageType === "edit" || pageType === "add") {
    window.addEventListener("beforeunload", function (e) {
      e.preventDefault();
      e.returnValue = "";
    });
  }

  // ==========================================
  // DataTable
  // ==========================================

  if (["list", "sessions", "subscriber", "all-subscriber"].includes(pageType)) {
    if (typeof $ !== "undefined" && $.fn && $.fn.DataTable) {
      const $table = $("#table");

      if ($table.length) {
        const defaultOrderColumn = parseInt(
          $table.attr("data-default-order-column"),
          10,
        );

        const defaultOrderDir = $table.attr("data-default-order-dir") || "desc";

        if ($.fn.DataTable.isDataTable("#table")) {
          $table.DataTable().destroy();
        }

        const tableOptions = {
          bDestroy: true,

          createdRow: function (row, data) {
            const col2 = data[2] || "";
            const col6 = data[6] || "";

            if (col6 === "Confirm") {
              $(row).css({
                "background-color": "#FFFFFF",
                color: "#333",
              });
            } else if (col6 === "Cancel") {
              $(row).css({
                "background-color": "#C70039",
                color: "#fff",
              });
            } else if (col2 === "No") {
              $(row).css({
                "background-color": "#C70039",
                color: "#fff",
              });
            } else if (col6 === "Delivered") {
              $(row).css({
                "background-color": "#FFFFFF",
                color: "#333",
              });
            } else {
              $(row).css({
                "background-color": "#E6E6FA",
                color: "#333",
              });
            }
          },
        };

        if (!isNaN(defaultOrderColumn)) {
          tableOptions.order = [[defaultOrderColumn, defaultOrderDir]];
        } else {
          tableOptions.order = [[3, "desc"]];
        }

        $table.DataTable(tableOptions);
      }
    }
  }
});
