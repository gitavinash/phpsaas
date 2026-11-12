(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    initNavigationDrawer();
    initSidebarNavigation();
    initProfileMenu();
    initDashboardCharts();
    initDashboardImageFallbacks();
    initSubpageEnhancements();
  });

  function initNavigationDrawer() {
    var drawer = document.getElementById("admin-nav-drawer");
    var openBtn = document.getElementById("nav-toggle");
    var closeBtn = document.getElementById("drawer-close");
    if (!drawer || !openBtn) {
      return;
    }

    var scroller = drawer.querySelector(".Drawer-scroller");
    var sheet = drawer.querySelector(".Drawer-sheet");
    if (!scroller || !sheet) {
      return;
    }

    var drawerMq = window.matchMedia("(max-width: 1024px)");
    var landmarks = [
      document.querySelector("header"),
      document.getElementById("main-content") || document.querySelector("main"),
      document.querySelector("footer")
    ].filter(Boolean);

    var supportsPopover = typeof drawer.showPopover === "function" && typeof drawer.hidePopover === "function";
    var supportsScrollInitial = typeof CSS !== "undefined" && CSS.supports && CSS.supports("scroll-initial-target", "nearest");
    var supportsScrollAnim = typeof CSS !== "undefined" && CSS.supports && CSS.supports("animation-timeline", "scroll()");
    var isOpen = false;
    var observer = null;

    function setPageInert(state) {
      for (var i = 0; i < landmarks.length; i++) {
        landmarks[i].inert = state;
      }
    }

    function syncDrawerMode() {
      if (drawerMq.matches) {
        if (supportsPopover && !drawer.hasAttribute("popover")) {
          drawer.setAttribute("popover", "manual");
        }
        drawer.setAttribute("role", "dialog");
        if (!isOpen) {
          drawer.removeAttribute("aria-modal");
          setPageInert(false);
          openBtn.setAttribute("aria-expanded", "false");
        }
      } else {
        forceClose(false);
        drawer.removeAttribute("popover");
        drawer.removeAttribute("role");
        drawer.removeAttribute("aria-modal");
        drawer.style.removeProperty("--drawer-backdrop");
        document.body.classList.remove("sidebar-open");
      }
    }

    function onDrawerOpened() {
      isOpen = true;
      openBtn.setAttribute("aria-expanded", "true");
      if (supportsScrollAnim) {
        return;
      }
      drawer.style.setProperty("--drawer-backdrop", "1");
    }

    function forceClose(restoreFocus) {
      if (!isOpen && !drawer.hasAttribute("aria-modal")) {
        setPageInert(false);
        openBtn.setAttribute("aria-expanded", "false");
        document.body.classList.remove("sidebar-open");
        return;
      }
      isOpen = false;
      if (supportsPopover && drawer.matches && drawer.matches(":popover-open")) {
        try {
          drawer.hidePopover();
        } catch (err) {
          /* ignore if already closed */
        }
      }
      document.body.classList.remove("sidebar-open");
      setPageInert(false);
      drawer.removeAttribute("aria-modal");
      openBtn.setAttribute("aria-expanded", "false");
      if (restoreFocus && drawerMq.matches) {
        openBtn.focus({ preventScroll: true });
      }
    }

    function openDrawer() {
      if (!drawerMq.matches || isOpen) {
        return;
      }
      isOpen = true;
      setPageInert(true);
      drawer.setAttribute("aria-modal", "true");
      openBtn.setAttribute("aria-expanded", "true");

      if (supportsPopover) {
        try {
          drawer.showPopover();
        } catch (err) {
          document.body.classList.add("sidebar-open");
        }
      } else {
        document.body.classList.add("sidebar-open");
      }

      if (!supportsScrollInitial) {
        scroller.scrollTo({ left: scroller.offsetWidth, behavior: "instant" });
        requestAnimationFrame(function () {
          requestAnimationFrame(function () {
            scroller.scrollTo({ left: 0, behavior: "auto" });
          });
        });
      } else {
        scroller.scrollTo({ left: 0, behavior: "auto" });
      }

      if (closeBtn && !supportsPopover) {
        closeBtn.focus({ preventScroll: true });
      }
    }

    function closeDrawer() {
      if (!drawerMq.matches || !isOpen) {
        return;
      }
      if (scroller.offsetWidth > 0) {
        scroller.scrollTo({ left: scroller.offsetWidth, behavior: "auto" });
        setTimeout(function () {
          if (isOpen && (!scroller.scrollLeft || scroller.scrollLeft >= sheet.offsetWidth - 4)) {
            forceClose(true);
          }
        }, 380);
      } else {
        forceClose(true);
      }
    }

    if ("IntersectionObserver" in window) {
      var visibleThreshold = 1 / Math.max(window.innerWidth || 375, 320);
      observer = new IntersectionObserver(
        function (entries) {
          if (!drawerMq.matches || !entries.length) {
            return;
          }
          var entry = entries[entries.length - 1];
          if (entry.intersectionRatio < visibleThreshold) {
            if (isOpen) {
              forceClose(true);
            }
          } else if (entry.intersectionRatio >= 0.99 && isOpen) {
            onDrawerOpened();
          }
        },
        { root: drawer, threshold: [visibleThreshold, 1] }
      );
      observer.observe(sheet);
    }

    if (!supportsScrollAnim) {
      scroller.addEventListener("scroll", function () {
        if (!drawerMq.matches || !sheet.offsetWidth) {
          return;
        }
        var ratio = Math.max(0, Math.min(1, 1 - scroller.scrollLeft / sheet.offsetWidth));
        drawer.style.setProperty("--drawer-backdrop", String(ratio));
      }, { passive: true });
    }

    openBtn.addEventListener("click", openDrawer);
    if (closeBtn) {
      closeBtn.addEventListener("click", closeDrawer);
    }

    drawer.addEventListener("click", function (event) {
      if (!drawerMq.matches) {
        return;
      }
      if (!sheet.contains(event.target)) {
        closeDrawer();
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && drawerMq.matches && isOpen) {
        event.preventDefault();
        closeDrawer();
      }
    });

    if (typeof drawerMq.addEventListener === "function") {
      drawerMq.addEventListener("change", syncDrawerMode);
    } else if (typeof drawerMq.addListener === "function") {
      drawerMq.addListener(syncDrawerMode);
    }

    syncDrawerMode();
  }

  function initSidebarNavigation() {
    var navRoot = document.querySelector("nav.side-nav > ul");
    if (!navRoot) {
      return;
    }

    var topItems = navRoot.children;

    // Highlight exact submenu item match for current URL
    var currentPath = window.location.pathname + window.location.search;
    var subAnchors = navRoot.querySelectorAll(".eg-submenu a");
    for (var k = 0; k < subAnchors.length; k++) {
      var anchor = subAnchors[k];
      try {
        var resolved = new URL(anchor.getAttribute("href"), document.baseURI);
        if (resolved.pathname + resolved.search === currentPath) {
          anchor.setAttribute("aria-current", "page");
        }
      } catch (err) {
        /* ignore malformed URL */
      }
    }

    // Submenu accordion toggle buttons
    navRoot.addEventListener("click", function (event) {
      var btn = event.target.closest(".eg-nav-sub-btn");
      if (!btn) {
        return;
      }
      var panelId = btn.getAttribute("aria-controls");
      var panel = panelId ? document.getElementById(panelId) : null;
      if (!panel) {
        return;
      }
      var expanded = btn.getAttribute("aria-expanded") === "true";
      btn.setAttribute("aria-expanded", expanded ? "false" : "true");
      panel.hidden = expanded;
    });

    // Quick Navigation Search Filter
    var searchInput = document.getElementById("side-nav-search");
    var clearBtn = document.getElementById("side-nav-search-clear");
    var emptyNotice = document.getElementById("nav-search-empty");
    var liveStatus = document.getElementById("nav-search-status");

    if (!searchInput) {
      return;
    }

    var searchTimer = null;

    function filterMenu() {
      var term = (searchInput.value || "").toLowerCase().trim();
      var visibleCount = 0;

      if (clearBtn) {
        clearBtn.hidden = term === "";
      }

      for (var i = 0; i < topItems.length; i++) {
        var item = topItems[i];
        var primaryLink = item.querySelector("a.eg-nav-link");
        if (!primaryLink) {
          continue;
        }

        var parentText = primaryLink.textContent.toLowerCase();
        var parentMatches = term === "" || parentText.indexOf(term) !== -1;
        var subLinks = item.querySelectorAll(".eg-submenu a");
        var subList = item.querySelector(".eg-submenu");
        var subBtn = item.querySelector(".eg-nav-sub-btn");
        var anySubMatch = false;

        for (var j = 0; j < subLinks.length; j++) {
          var subA = subLinks[j];
          var subLi = subA.parentElement;
          var subText = subA.textContent.toLowerCase();
          var subMatch = term !== "" && subText.indexOf(term) !== -1;

          subA.classList.toggle("nav-hit", subMatch);
          if (subMatch) {
            anySubMatch = true;
          }
          if (subLi) {
            subLi.style.display = (term === "" || parentMatches || subMatch) ? "" : "none";
          }
        }

        var showItem = term === "" || parentMatches || anySubMatch;
        item.style.display = showItem ? "" : "none";

        if (showItem) {
          visibleCount++;
        }

        if (subList && subBtn) {
          if (term !== "" && anySubMatch) {
            subList.hidden = false;
            subBtn.setAttribute("aria-expanded", "true");
          } else if (term === "") {
            var isActiveGroup = item.classList.contains("active");
            subList.hidden = !isActiveGroup;
            subBtn.setAttribute("aria-expanded", isActiveGroup ? "true" : "false");
          }
        }
      }

      if (emptyNotice) {
        emptyNotice.hidden = visibleCount > 0;
      }
      if (liveStatus) {
        liveStatus.textContent = term === ""
          ? ""
          : (visibleCount === 0
              ? "No matching menu items found"
              : visibleCount + " navigation section" + (visibleCount === 1 ? "" : "s") + " found");
      }
    }

    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(filterMenu, 80);
    });

    if (clearBtn) {
      clearBtn.addEventListener("click", function () {
        searchInput.value = "";
        filterMenu();
        searchInput.focus();
      });
    }

    // Keyboard shortcut "/" focuses search cleanly
    document.addEventListener("keydown", function (event) {
      if (event.key === "/" && !event.metaKey && !event.ctrlKey && !event.altKey) {
        var target = event.target;
        var isEditable = target && (
          target.tagName === "INPUT" ||
          target.tagName === "TEXTAREA" ||
          target.tagName === "SELECT" ||
          target.isContentEditable
        );
        if (!isEditable && searchInput.offsetParent !== null) {
          event.preventDefault();
          searchInput.focus();
          searchInput.select();
        }
      } else if (event.key === "Escape" && document.activeElement === searchInput && searchInput.value !== "") {
        searchInput.value = "";
        filterMenu();
      }
    });
  }

  function initProfileMenu() {
    var wrapper = document.getElementById("eg-profile-menu");
    var trigger = document.getElementById("profile-menu-trigger");
    var list = document.getElementById("profile-menu-list");
    var backdrop = document.getElementById("eg-profile-backdrop");
    if (!wrapper || !trigger || !list) {
      return;
    }

    var sheetMq = window.matchMedia("(max-width: 680px)");

    function setMenuOpen(open, restoreFocus) {
      trigger.setAttribute("aria-expanded", open ? "true" : "false");
      list.hidden = !open;
      if (backdrop) {
        backdrop.hidden = !(open && sheetMq.matches);
      }
      if (!open && restoreFocus) {
        trigger.focus({ preventScroll: true });
      }
    }

    trigger.addEventListener("click", function (event) {
      event.stopPropagation();
      var expanded = trigger.getAttribute("aria-expanded") === "true";
      setMenuOpen(!expanded, false);
    });

    if (backdrop) {
      backdrop.addEventListener("click", function () {
        setMenuOpen(false, true);
      });
    }

    document.addEventListener("click", function (event) {
      if (!wrapper.contains(event.target) && !list.hidden) {
        setMenuOpen(false, false);
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && !list.hidden) {
        setMenuOpen(false, true);
      }
    });

    if (typeof sheetMq.addEventListener === "function") {
      sheetMq.addEventListener("change", function () {
        if (backdrop) {
          backdrop.hidden = !(!list.hidden && sheetMq.matches);
        }
      });
    }
  }

  function initDashboardCharts() {
    var payloadEl = document.getElementById("eg-dash-chart-data");
    if (!payloadEl) {
      return;
    }

    var payload = null;
    try {
      payload = JSON.parse(payloadEl.textContent || "{}");
    } catch (err) {
      payload = null;
    }

    var revCanvas = document.getElementById("egRevChart");
    var statusCanvas = document.getElementById("egStatusChart");

    if (!payload || typeof window.Chart === "undefined") {
      showChartFallback(revCanvas, "Chart visualization unavailable.");
      showChartFallback(statusCanvas, "Chart visualization unavailable.");
      return;
    }

    var cur = payload.currency || "";
    var revLabels = Array.isArray(payload.revenueLabels) ? payload.revenueLabels : [];
    var revData = Array.isArray(payload.revenueData) ? payload.revenueData : [];
    var statRaw = Array.isArray(payload.statusCounts) ? payload.statusCounts : [];

    if (revCanvas) {
      new window.Chart(revCanvas, {
        type: "bar",
        data: {
          labels: revLabels,
          datasets: [{
            label: "Revenue",
            data: revData,
            backgroundColor: "rgba(37, 99, 235, 0.12)",
            borderColor: "rgba(37, 99, 235, 0.88)",
            borderWidth: 2,
            borderRadius: 5,
            borderSkipped: false,
            hoverBackgroundColor: "rgba(37, 99, 235, 0.24)"
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: "#0f172a",
              titleColor: "#94a3b8",
              bodyColor: "#f1f5f9",
              padding: 10,
              cornerRadius: 8,
              callbacks: {
                label: function (ctx) {
                  return "  " + cur + parseFloat(ctx.raw).toLocaleString("en", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  });
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              border: { display: false },
              grid: { color: "#f1f5f9" },
              ticks: {
                font: { size: 11 },
                color: "#64748b",
                callback: function (v) {
                  return cur + Number(v).toLocaleString();
                }
              }
            },
            x: {
              border: { display: false },
              grid: { display: false },
              ticks: {
                font: { size: 11 },
                color: "#64748b",
                maxRotation: 0,
                autoSkip: true
              }
            }
          }
        }
      });
    }

    if (statusCanvas) {
      var sLabels = ["Pending", "Confirmed", "Rejected", "Cancelled", "Waiting", "RTO", "Delivered"];
      var sColors = ["#f59e0b", "#22c55e", "#ef4444", "#f97316", "#3b82f6", "#8b5cf6", "#10b981"];
      var fL = [];
      var fD = [];
      var fC = [];

      for (var i = 0; i < statRaw.length; i++) {
        if (statRaw[i] > 0) {
          fL.push(sLabels[i]);
          fD.push(statRaw[i]);
          fC.push(sColors[i]);
        }
      }

      if (!fD.length) {
        fL = ["No orders in period"];
        fD = [1];
        fC = ["#e2e8f0"];
      }

      new window.Chart(statusCanvas, {
        type: "doughnut",
        data: {
          labels: fL,
          datasets: [{
            data: fD,
            backgroundColor: fC,
            borderWidth: 0,
            hoverOffset: 5
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: "68%",
          plugins: {
            legend: {
              position: "bottom",
              labels: {
                font: { size: 11 },
                color: "#334155",
                boxWidth: 10,
                padding: 12,
                usePointStyle: true,
                pointStyleWidth: 10
              }
            },
            tooltip: {
              backgroundColor: "#0f172a",
              titleColor: "#94a3b8",
              bodyColor: "#f1f5f9",
              padding: 10,
              cornerRadius: 8
            }
          }
        }
      });
    }
  }

  function showChartFallback(canvas, text) {
    if (!canvas || !canvas.parentElement) {
      return;
    }
    var msg = document.createElement("div");
    msg.className = "eg-chart-fallback";
    msg.textContent = text;
    canvas.hidden = true;
    canvas.parentElement.appendChild(msg);
  }

  function initDashboardImageFallbacks() {
    var dash = document.getElementById("egDash");
    if (!dash) {
      return;
    }

    function markBroken(img) {
      if (!img || img.tagName !== "IMG") {
        return;
      }
      img.classList.add("is-broken");
      var next = img.nextElementSibling;
      if (next && next.classList.contains("prod-thumb-fallback")) {
        next.classList.add("is-visible");
      }
    }

    dash.addEventListener("error", function (event) {
      var target = event && event.target;
      if (target && target.tagName === "IMG") {
        markBroken(target);
      }
    }, true);

    var imgs = dash.querySelectorAll("img.prod-img, img.top-img");
    for (var i = 0; i < imgs.length; i++) {
      if (imgs[i].complete && imgs[i].naturalWidth === 0) {
        markBroken(imgs[i]);
      }
    }
  }

  function initSubpageEnhancements() {
    if (typeof window.jQuery === "undefined") {
      wrapScrollableTables();
      return;
    }

    var $ = window.jQuery;
    var urlParams = new URLSearchParams(window.location.search || "");
    var pageType = urlParams.get("type");

    if (pageType === "list" || pageType === "category" || pageType === "tags") {
      var $table = $("#table");
      if (
        $table.length &&
        $.fn &&
        typeof $.fn.DataTable === "function" &&
        !$.fn.DataTable.isDataTable($table[0]) &&
        !$table.closest(".modern-product-list").length &&
        !$table.find("tbody td[colspan]").length
      ) {
        var defaultOrderColumn = parseInt($table.attr("data-default-order-column"), 10);
        var defaultOrderDir = $table.attr("data-default-order-dir") || "asc";
        var tableOptions = {
          bDestroy: true,
          autoWidth: false,
          pageLength: 10,
          lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
          ],
          language: {
            lengthMenu: "Rows _MENU_",
            search: "",
            searchPlaceholder: "Search records..."
          }
        };

        if (!isNaN(defaultOrderColumn)) {
          tableOptions.order = [[defaultOrderColumn, defaultOrderDir]];
        }

        $table.DataTable(tableOptions);
      }
    } else if (pageType) {
      var $select = $(".selectSimilarProduct");
      if ($select.length && $.fn && typeof $.fn.select2 === "function") {
        $select.select2({
          width: "100%",
          templateResult: function (data) {
            if (!data.id) {
              return data.text;
            }
            return data.selected ? null : data.text;
          }
        });
      }
    }

    wrapScrollableTables();
  }

  function wrapScrollableTables() {
    var tables = document.querySelectorAll("main .container table");
    for (var i = 0; i < tables.length; i++) {
      var tbl = tables[i];
      if (
        tbl.closest("#egDash") ||
        tbl.closest(".eg-table-scroll") ||
        tbl.closest(".table-wrap") ||
        tbl.closest(".table-wrapper")
      ) {
        continue;
      }

      var wrapper = document.createElement("div");
      wrapper.className = "eg-table-scroll";
      wrapper.setAttribute("role", "region");
      wrapper.setAttribute("tabindex", "0");
      wrapper.setAttribute("aria-label", "Scrollable table");

      tbl.parentNode.insertBefore(wrapper, tbl);
      wrapper.appendChild(tbl);
    }
  }
})();
