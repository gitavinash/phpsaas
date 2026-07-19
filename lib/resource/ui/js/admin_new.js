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

      if (
        main &&
        (sidebarHeight !== lastSidebarHeight ||
          viewportHeight !== lastViewportHeight ||
          lastScrollY === -1)
      ) {
        if (sidebarHeight + headerHeight > viewportHeight) {
          main.style.minHeight = sidebarHeight + headerHeight + "px";
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
  // Before Unload (only when form state changed)
  // ==========================================

  const skipUnsavedPromptForPageType = ["add", "edit", "taxonomy"].includes(
    String(pageType || "").toLowerCase(),
  );

  if (
    (pageType === "edit" || pageType === "add") &&
    !skipUnsavedPromptForPageType
  ) {
    const forms = Array.prototype.slice.call(document.querySelectorAll("form"));
    let baselineSnapshot = "";
    let baselineReady = false;
    let hasUserInteraction = false;

    const buildFormSnapshot = function (formElement) {
      if (!formElement) {
        return "";
      }

      const parts = [];
      const fields = formElement.querySelectorAll("input, textarea, select");

      for (let i = 0; i < fields.length; i++) {
        const field = fields[i];

        if (!field || field.disabled) {
          continue;
        }

        const type = (field.type || "").toLowerCase();
        if (["submit", "button", "reset", "image"].includes(type)) {
          continue;
        }

        const key = field.name || field.id || field.tagName + "_" + i;
        let value = "";

        if (type === "checkbox" || type === "radio") {
          value = field.checked ? "1" : "0";
        } else if (type === "file") {
          const files = field.files || [];
          const fileNames = [];
          for (let f = 0; f < files.length; f++) {
            fileNames.push((files[f].name || "") + ":" + (files[f].size || 0));
          }
          value = fileNames.join("|");
        } else if (field.tagName === "SELECT" && field.multiple) {
          const selectedValues = [];
          const options = field.options || [];
          for (let o = 0; o < options.length; o++) {
            if (options[o].selected) {
              selectedValues.push(options[o].value);
            }
          }
          value = selectedValues.join("|");
        } else {
          value = field.value || "";
        }

        parts.push(key + "=" + value);
      }

      // Include visual editor iframe content, since textarea value syncs on submit.
      const editorFrames = formElement.querySelectorAll("iframe.editor");
      for (let i = 0; i < editorFrames.length; i++) {
        const frame = editorFrames[i];
        let html = "";

        try {
          html =
            (frame.contentWindow &&
              frame.contentWindow.document &&
              frame.contentWindow.document.body &&
              frame.contentWindow.document.body.innerHTML) ||
            "";
        } catch (err) {
          html = "";
        }

        parts.push("editorFrame_" + i + "=" + html);
      }

      return parts.join("\n");
    };

    const buildPageSnapshot = function () {
      const snapshots = [];
      for (let i = 0; i < forms.length; i++) {
        snapshots.push(buildFormSnapshot(forms[i]));
      }
      return snapshots.join("\n\n---FORM---\n\n");
    };

    const refreshBaselineSnapshot = function () {
      // Do not re-baseline after user starts editing.
      if (hasUserInteraction) {
        return;
      }

      baselineSnapshot = buildPageSnapshot();
      baselineReady = true;
    };

    const hasUnsavedChanges = function () {
      if (!baselineReady) {
        return false;
      }

      return baselineSnapshot !== buildPageSnapshot();
    };

    const markUserInteraction = function (event) {
      if (event && event.isTrusted === false) {
        return;
      }
      hasUserInteraction = true;
    };

    refreshBaselineSnapshot();
    window.addEventListener("load", refreshBaselineSnapshot);
    window.setTimeout(refreshBaselineSnapshot, 1200);

    for (let i = 0; i < forms.length; i++) {
      forms[i].addEventListener("input", markUserInteraction, true);
      forms[i].addEventListener("change", markUserInteraction, true);
      forms[i].addEventListener("keydown", markUserInteraction, true);
      forms[i].addEventListener("paste", markUserInteraction, true);
      forms[i].addEventListener("drop", markUserInteraction, true);

      forms[i].addEventListener("submit", function () {
        window.__bypassUnsavedChangesPrompt = true;
      });
    }

    window.addEventListener("beforeunload", function (e) {
      if (window.__bypassUnsavedChangesPrompt === true) {
        return;
      }

      if (!hasUserInteraction) {
        return;
      }

      if (!hasUnsavedChanges()) {
        return;
      }

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

  // Auto-initialize DataTable for #table if it exists and hasn't been initialized
  if (typeof $ !== "undefined" && $.fn && $.fn.DataTable) {
    const $table = $("#table");
    if ($table.length) {
      document.body.classList.add("premium-admin");
      if (!$.fn.DataTable.isDataTable("#table")) {
        const defaultOrderColumn = parseInt(
          $table.attr("data-default-order-column"),
          10,
        );
        const defaultOrderDir = $table.attr("data-default-order-dir") || "desc";
        const pageLength = parseInt($table.attr("data-page-length"), 10) || 10;
        $table.DataTable({
          bDestroy: true,
          bStateSave: false,
          pageLength: pageLength,
          order: [
            [
              !isNaN(defaultOrderColumn) ? defaultOrderColumn : 2,
              defaultOrderDir,
            ],
          ],
        });
      }
    }
  }
});

// Header

document.addEventListener("DOMContentLoaded", function () {
  var menuSearch = document.getElementById("side-nav-search");
  if (!menuSearch) {
    return;
  }

  var menuRoot = document.querySelector("nav.side-nav > ul");
  if (!menuRoot) {
    return;
  }

  var menuItems = menuRoot.children;

  function normalizeText(value) {
    return value.toLowerCase().trim();
  }

  document.addEventListener("keydown", function (event) {
    if (
      event.key === "/" &&
      !event.metaKey &&
      !event.ctrlKey &&
      !event.altKey
    ) {
      var target = event.target;
      var isTypingField =
        target &&
        (target.tagName === "INPUT" ||
          target.tagName === "TEXTAREA" ||
          target.isContentEditable);
      if (!isTypingField) {
        event.preventDefault();
        menuSearch.focus();
        menuSearch.select();
      }
    }

    if (event.key === "Escape" && document.activeElement === menuSearch) {
      menuSearch.value = "";
      menuSearch.dispatchEvent(new Event("input"));
      menuSearch.blur();
    }
  });

  menuSearch.addEventListener("input", function () {
    var searchTerm = normalizeText(menuSearch.value);

    for (var i = 0; i < menuItems.length; i++) {
      var item = menuItems[i];
      var topAnchor = item.firstElementChild;

      if (!topAnchor || topAnchor.tagName !== "A") {
        continue;
      }

      var searchableText = normalizeText(topAnchor.textContent);
      var subLinks = item.querySelectorAll(".side-menu a, .bottom-menu a");
      var hasSubMatch = false;
      var isTopMatch =
        searchTerm === "" || searchableText.indexOf(searchTerm) !== -1;

      for (var j = 0; j < subLinks.length; j++) {
        var link = subLinks[j];
        var subText = normalizeText(link.textContent);
        var isSubMatch =
          searchTerm === "" || subText.indexOf(searchTerm) !== -1;

        searchableText += " " + subText;

        link.classList.remove("nav-hit");

        if (searchTerm !== "" && isSubMatch) {
          hasSubMatch = true;
          link.classList.add("nav-hit");
        }

        if (searchTerm === "" || isTopMatch || isSubMatch) {
          link.parentElement.style.display = "";
        } else {
          link.parentElement.style.display = "none";
        }
      }

      item.style.display =
        searchTerm === "" ||
        isTopMatch ||
        hasSubMatch ||
        searchableText.indexOf(searchTerm) !== -1
          ? ""
          : "none";
    }
  });
});

// footer

(function () {
  var body = document.body;
  var toggleBtn = document.getElementById("nav-toggle");
  var overlay = document.getElementById("nav-overlay");
  var mediaQuery = window.matchMedia("(max-width: 1024px)");

  function closeMobileNav() {
    body.classList.remove("sidebar-open");
  }

  if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      if (!mediaQuery.matches) {
        return;
      }
      body.classList.toggle("sidebar-open");
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

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeMobileNav();
    }
  });

  document.addEventListener("click", function (event) {
    if (!mediaQuery.matches) {
      return;
    }
    var insideSidebar = event.target.closest("aside");
    var clickedToggle = event.target.closest("#nav-toggle");
    if (
      !insideSidebar &&
      !clickedToggle &&
      body.classList.contains("sidebar-open")
    ) {
      closeMobileNav();
    }
  });
})();

// Data Tables

document.addEventListener("DOMContentLoaded", function () {
  if (typeof $ !== "undefined") {
    // Get the "type" parameter from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get("type");

    // Types that should initialize DataTable
    const dataTableTypes = ["list", "category", "tags"];

    if (dataTableTypes.includes(type)) {
      const $table = $("#table");
      const defaultOrderColumn = parseInt(
        $table.attr("data-default-order-column"),
        10,
      );
      const defaultOrderDir = $table.attr("data-default-order-dir") || "asc";

      const tableOptions = {
        bDestroy: true,
        autoWidth: false,
        responsive: true,
        pageLength: 10,
        lengthMenu: [
          [10, 25, 50, 100],
          [10, 25, 50, 100],
        ],
        language: {
          lengthMenu: "Rows _MENU_",
          search: "",
          searchPlaceholder: "Search records...",
        },
      };

      if (!isNaN(defaultOrderColumn)) {
        tableOptions.order = [[defaultOrderColumn, defaultOrderDir]];
      }

      $table.DataTable(tableOptions);
    } else {
      function hideSelectedOption(data) {
        if (!data.id) {
          return data.text;
        }
        return data.selected ? null : data.text;
      }

      $(".selectSimilarProduct").select2({
        templateResult: hideSelectedOption,
      });
    }

    // Handle state change to load cities
    if ($("#state").length) {
      $("#state").on("change", function (e) {
        e.preventDefault();
        var stateId = $(this).val();
        $.ajax({
          type: "GET",
          url: "/api/city/?state_id=" + stateId,
          dataType: "json",
          success: function (response) {
            $("#cityBox").show();
            $("#city").empty();
            $("#city").append(new Option("Select city", "select"));
            if (response.cityData && Array.isArray(response.cityData)) {
              response.cityData.forEach(function (city) {
                $("#city").append(new Option(city.name, city.id));
              });
            }
          },
          error: function (response) {
            console.log(`Error : `, response);
          },
        });
      });
    }
  }
});

// Handle Form Actions

// Handle new company form submission
if ($("#addNewCompanyForm").length) {
  $("#addNewCompanyForm")
    .off("submit")
    .on("submit", function (e) {
      e.preventDefault();

      var name = $("#name").val().trim();
      var address = $("#address").val().trim();
      var state = $("#state").val();
      var city = $("#city").val();
      var pinCode = $("#pinCode").val().trim();
      var email = $("#email").val().trim();
      var mobile = $("#mobile").val().trim();
      var companyType = $("#companyType").val();

      function showMsg(message, status) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", message, status);
        } else if (
          typeof Swal !== "undefined" &&
          typeof Swal.fire === "function"
        ) {
          Swal.fire({
            position: "center",
            icon: status === "success" ? "success" : "warning",
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
        } else {
          alert(message);
        }
      }

      function displayWarning(msg, selector) {
        if (typeof showWarning === "function") {
          showWarning(msg, selector);
        } else {
          showMsg(msg, "warning");
          $(selector).focus();
        }
      }

      if (!name) return displayWarning("Kindly enter company name", "#name");
      if (!email) return displayWarning("Kindly enter company email", "#email");
      if (!mobile)
        return displayWarning("Kindly enter mobile number", "#mobile");

      var submitBtn = $(this).find('input[type="submit"]');
      var originalBtnVal = submitBtn.val();

      submitBtn.prop("disabled", true).val("Submitting...");

      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "/api/company/create/",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
          showMsg(response.msg, response.status);
          if (response.status === "success") {
            setTimeout(function () {
              window.location.href = `/dashboard/company/?type=list&status=all`;
            }, 1500);
          } else {
            submitBtn.prop("disabled", false).val(originalBtnVal);
          }
        },
        error: function (xhr) {
          console.error("❌ Error:", xhr);
          var err = xhr.responseJSON || {
            msg: "Something went wrong. Please try again.",
            status: "error",
          };
          showMsg(err.msg, err.status);
          submitBtn.prop("disabled", false).val(originalBtnVal);
        },
      });
    });
}

// Handle company update form submission
if ($("#updateCompanyForm").length) {
  $("#updateCompanyForm")
    .off("submit")
    .on("submit", function (e) {
      e.preventDefault();

      var name = $("#name").val().trim();
      var address = $("#address").val().trim();
      var state = $("#state").val();
      var city = $("#city").val();
      var pinCode = $("#pinCode").val().trim();
      var email = $("#email").val().trim();
      var mobile = $("#mobile").val().trim();
      var companyType = $("#companyType").val();

      function showMsg(message, status) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", message, status);
        } else if (
          typeof Swal !== "undefined" &&
          typeof Swal.fire === "function"
        ) {
          Swal.fire({
            position: "center",
            icon: status === "success" ? "success" : "warning",
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
        } else {
          alert(message);
        }
      }

      function displayWarning(msg, selector) {
        if (typeof showWarning === "function") {
          showWarning(msg, selector);
        } else {
          showMsg(msg, "warning");
          $(selector).focus();
        }
      }

      if (!name) return displayWarning("Kindly enter company name", "#name");
      if (!email) return displayWarning("Kindly enter company email", "#email");
      if (!mobile)
        return displayWarning("Kindly enter mobile number", "#mobile");

      var submitBtn = $(this).find('input[type="submit"]');
      var originalBtnVal = submitBtn.val();

      submitBtn.prop("disabled", true).val("Submitting...");

      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "/api/company/update/",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
          showMsg(response.msg, response.status);
          if (response.status === "success") {
            setTimeout(function () {
              window.location.href = `/dashboard/company/?type=list&status=all`;
            }, 1500);
          } else {
            submitBtn.prop("disabled", false).val(originalBtnVal);
          }
        },
        error: function (xhr) {
          console.error("❌ Error:", xhr);
          var err = xhr.responseJSON || {
            msg: "Something went wrong. Please try again.",
            status: "error",
          };
          showMsg(err.msg, err.status);
          submitBtn.prop("disabled", false).val(originalBtnVal);
        },
      });
    });
}

// Handle new vendor form submission
if ($("#addNewVendorForm").length) {
  $("#addNewVendorForm")
    .off("submit")
    .on("submit", function (e) {
      e.preventDefault();

      var name = $("#name").val().trim();
      var address = $("#address").val().trim();
      var state = $("#state").val();
      var city = $("#city").val();
      var pinCode = $("#pinCode").val().trim();
      var email = $("#email").val().trim();
      var mobile = $("#mobile").val().trim();
      var vendorType = $("#vendorType").val();

      function showMsg(message, status) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", message, status);
        } else if (
          typeof Swal !== "undefined" &&
          typeof Swal.fire === "function"
        ) {
          Swal.fire({
            position: "center",
            icon: status === "success" ? "success" : "warning",
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
        } else {
          alert(message);
        }
      }

      function displayWarning(msg, selector) {
        if (typeof showWarning === "function") {
          showWarning(msg, selector);
        } else {
          showMsg(msg, "warning");
          $(selector).focus();
        }
      }

      if (!name) return displayWarning("Kindly enter vendor name", "#name");
      if (!email) return displayWarning("Kindly enter vendor email", "#email");
      if (!mobile)
        return displayWarning("Kindly enter mobile number", "#mobile");

      var submitBtn = $(this).find('input[type="submit"]');
      var originalBtnVal = submitBtn.val();

      submitBtn.prop("disabled", true).val("Submitting...");

      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "/api/vendor/create/",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
          showMsg(response.msg, response.status);
          if (response.status === "success") {
            setTimeout(function () {
              window.location.href = `/dashboard/vendor/?type=list&status=all`;
            }, 1500);
          } else {
            submitBtn.prop("disabled", false).val(originalBtnVal);
          }
        },
        error: function (xhr) {
          console.error("❌ Error:", xhr);
          var err = xhr.responseJSON || {
            msg: "Something went wrong. Please try again.",
            status: "error",
          };
          showMsg(err.msg, err.status);
          submitBtn.prop("disabled", false).val(originalBtnVal);
        },
      });
    });
}

// Handle vendor update form submission
if ($("#updateVendorForm").length) {
  $("#updateVendorForm")
    .off("submit")
    .on("submit", function (e) {
      e.preventDefault();

      var name = $("#name").val().trim();
      var address = $("#address").val().trim();
      var state = $("#state").val();
      var city = $("#city").val();
      var pinCode = $("#pinCode").val().trim();
      var email = $("#email").val().trim();
      var mobile = $("#mobile").val().trim();
      var vendorType = $("#vendorType").val();

      function showMsg(message, status) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", message, status);
        } else if (
          typeof Swal !== "undefined" &&
          typeof Swal.fire === "function"
        ) {
          Swal.fire({
            position: "center",
            icon: status === "success" ? "success" : "warning",
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
        } else {
          alert(message);
        }
      }

      function displayWarning(msg, selector) {
        if (typeof showWarning === "function") {
          showWarning(msg, selector);
        } else {
          showMsg(msg, "warning");
          $(selector).focus();
        }
      }

      if (!name) return displayWarning("Kindly enter vendor name", "#name");
      if (!email) return displayWarning("Kindly enter vendor email", "#email");
      if (!mobile)
        return displayWarning("Kindly enter mobile number", "#mobile");

      var submitBtn = $(this).find('input[type="submit"]');
      var originalBtnVal = submitBtn.val();

      submitBtn.prop("disabled", true).val("Submitting...");

      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "/api/vendor/update/",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
          showMsg(response.msg, response.status);
          if (response.status === "success") {
            setTimeout(function () {
              window.location.href = `/dashboard/vendor/?type=list&status=all`;
            }, 1500);
          } else {
            submitBtn.prop("disabled", false).val(originalBtnVal);
          }
        },
        error: function (xhr) {
          console.error("❌ Error:", xhr);
          var err = xhr.responseJSON || {
            msg: "Something went wrong. Please try again.",
            status: "error",
          };
          showMsg(err.msg, err.status);
          submitBtn.prop("disabled", false).val(originalBtnVal);
        },
      });
    });
}
