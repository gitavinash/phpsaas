(function () {
  "use strict";

  if (!window.__adminNewAvatarFallbackBound) {
    window.__adminNewAvatarFallbackBound = true;

    const applyAvatarFallback = function (img) {
      if (!img || img.tagName !== "IMG") {
        return;
      }
      const avatar = img.closest(".user-avatar[data-initials]");
      if (!avatar) {
        return;
      }
      const initials =
        (avatar.getAttribute("data-initials") || "US").trim() || "US";
      avatar.textContent = initials;
    };

    document.addEventListener(
      "error",
      function (event) {
        if (event && event.target && event.target.tagName === "IMG") {
          applyAvatarFallback(event.target);
        }
      },
      true,
    );

    document.addEventListener("DOMContentLoaded", function () {
      const avatarImages = document.querySelectorAll(
        ".user-avatar[data-initials] img",
      );
      for (let i = 0; i < avatarImages.length; i++) {
        const img = avatarImages[i];
        if (img.complete && img.naturalWidth === 0) {
          applyAvatarFallback(img);
        }
      }
    });
  }
})();

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
  // Sticky Sidebar
  // ==========================================

  let updateSidebar = null;

  if (aside && !aside.dataset.adminNewStickyBound) {
    aside.dataset.adminNewStickyBound = "1";
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
  // Sidebar Search
  // ==========================================

  const menuSearch = document.getElementById("side-nav-search");
  const menuRoot = document.querySelector("nav.side-nav > ul");

  if (menuSearch && menuRoot && !menuSearch.dataset.adminNewSearchBound) {
    menuSearch.dataset.adminNewSearchBound = "1";
    const menuItems = menuRoot.children;
    let searchTimeout;

    const normalizeText = function (value) {
      return String(value || "").toLowerCase().trim();
    };

    document.addEventListener("keydown", function (event) {
      if (
        event.key === "/" &&
        !event.metaKey &&
        !event.ctrlKey &&
        !event.altKey
      ) {
        const target = event.target;
        const isTypingField =
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
      clearTimeout(searchTimeout);

      searchTimeout = setTimeout(function () {
        const searchTerm = normalizeText(menuSearch.value);

        for (let i = 0; i < menuItems.length; i++) {
          const item = menuItems[i];
          const topAnchor = item.firstElementChild;

          if (!topAnchor || topAnchor.tagName !== "A") {
            continue;
          }

          let searchableText = normalizeText(topAnchor.textContent);
          const subLinks = item.querySelectorAll(
            ".side-menu a, .bottom-menu a",
          );
          let hasSubMatch = false;
          const isTopMatch =
            searchTerm === "" || searchableText.indexOf(searchTerm) !== -1;

          for (let j = 0; j < subLinks.length; j++) {
            const link = subLinks[j];
            const subText = normalizeText(link.textContent);
            const isSubMatch =
              searchTerm === "" || subText.indexOf(searchTerm) !== -1;

            searchableText += " " + subText;

            link.classList.remove("nav-hit");

            if (searchTerm !== "" && isSubMatch) {
              hasSubMatch = true;
              link.classList.add("nav-hit");
            }

            if (link.parentElement) {
              link.parentElement.style.display =
                searchTerm === "" || isTopMatch || isSubMatch ? "" : "none";
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

        if (typeof updateSidebar === "function") {
          requestAnimationFrame(updateSidebar);
        }
      }, 150);
    });
  }

  // ==========================================
  // Mobile Navigation
  // ==========================================

  if (toggleBtn && !toggleBtn.dataset.navToggleBound) {
    toggleBtn.dataset.navToggleBound = "1";
    const mediaQuery = window.matchMedia("(max-width: 1024px)");

    const closeMobileNav = function () {
      if (body) {
        body.classList.remove("sidebar-open");
      }
    };

    toggleBtn.addEventListener("click", function () {
      if (!mediaQuery.matches || !body) {
        return;
      }
      body.classList.toggle("sidebar-open");
    });

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
      if (!mediaQuery.matches || !body) {
        return;
      }
      const insideSidebar = event.target.closest("aside");
      const clickedToggle = event.target.closest("#nav-toggle");
      if (
        !insideSidebar &&
        !clickedToggle &&
        body.classList.contains("sidebar-open")
      ) {
        closeMobileNav();
      }
    });
  }

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
  // DataTable & jQuery Modules
  // ==========================================

  if (typeof $ !== "undefined") {
    const urlParams = new URLSearchParams(window.location.search);
    const queryType = (urlParams.get("type") || "").toLowerCase();
    const effectiveType = String(pageType || queryType || "").toLowerCase();
    const dataTableTypes = [
      "list",
      "category",
      "tags",
      "sessions",
      "subscriber",
      "all-subscriber",
    ];

    const $table = $("#table");
    if ($table.length && body) {
      body.classList.add("premium-admin");
    }

    if (
      $.fn &&
      $.fn.DataTable &&
      $table.length &&
      (effectiveType === "" || dataTableTypes.includes(effectiveType)) &&
      !$.fn.DataTable.isDataTable("#table")
    ) {
      // Remove any single colspan placeholder row before init so DataTables 1.10 does not throw _DT_CellIndex
      const $tbody = $table.find("tbody");
      const $bodyRows = $tbody.children("tr");
      let emptyMsg = $.trim($table.attr("data-empty-message") || "");
      if ($bodyRows.length === 1) {
        const $firstCells = $bodyRows.eq(0).children("td, th");
        if ($firstCells.length === 1 && $firstCells.eq(0).is("[colspan]")) {
          if (!emptyMsg) {
            emptyMsg = $.trim($firstCells.eq(0).text());
          }
          $bodyRows.eq(0).remove();
        }
      }

      const headerCount = $table.find("thead th").length;
      const defaultOrderColumn = parseInt(
        $table.attr("data-default-order-column"),
        10,
      );
      const attrOrderDir = $table.attr("data-default-order-dir");
      const defaultOrderDir =
        attrOrderDir && attrOrderDir.toLowerCase() === "desc" ? "desc" : "asc";
      const parsedPageLength = parseInt($table.attr("data-page-length"), 10);
      const pageLength =
        !isNaN(parsedPageLength) && parsedPageLength > 0
          ? parsedPageLength
          : 10;

      const tableOptions = {
        bStateSave: false,
        autoWidth: false,
        pageLength: pageLength,
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

      if (emptyMsg) {
        tableOptions.language.emptyTable = emptyMsg;
      }

      if (
        !isNaN(defaultOrderColumn) &&
        defaultOrderColumn >= 0 &&
        defaultOrderColumn < headerCount
      ) {
        tableOptions.order = [[defaultOrderColumn, defaultOrderDir]];
      }

      const isModernList =
        $table.closest(
          ".modern-user-list, .modern-customer-list, .modern-company-list, .modern-vendor-list, .modern-user-sessions",
        ).length > 0;

      if (
        pageType &&
        ["list", "sessions", "subscriber", "all-subscriber"].includes(
          pageType,
        ) &&
        !isModernList &&
        headerCount >= 7
      ) {
        tableOptions.createdRow = function (row, data) {
          const col2 = data[2] || "";
          const col6 = data[6] || "";

          if (col6 === "Confirm" || col6 === "Delivered") {
            $(row).css({
              "background-color": "#FFFFFF",
              color: "#333",
            });
          } else if (col6 === "Cancel" || col2 === "No") {
            $(row).css({
              "background-color": "#C70039",
              color: "#fff",
            });
          } else {
            $(row).css({
              "background-color": "#E6E6FA",
              color: "#333",
            });
          }
        };
      }

      $table.DataTable(tableOptions);
    } else if (
      $.fn &&
      $.fn.select2 &&
      !dataTableTypes.includes(effectiveType) &&
      $(".selectSimilarProduct").length
    ) {
      const hideSelectedOption = function (data) {
        if (!data.id) {
          return data.text;
        }
        return data.selected ? null : data.text;
      };

      $(".selectSimilarProduct").select2({
        templateResult: hideSelectedOption,
      });
    }

    // Handle state change to load cities
    if ($("#state").length && !$("#state").data("cityBound")) {
      $("#state").data("cityBound", true);
      $("#state").on("change", function (e) {
        e.preventDefault();
        const stateId = $(this).val();
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
            console.log("Error : ", response);
          },
        });
      });
    }

    // ==========================================
    // Handle Form Actions
    // ==========================================

    const showFormMsg = function (message, status) {
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
    };

    const displayFormWarning = function (msg, selector) {
      if (typeof showWarning === "function") {
        showWarning(msg, selector);
      } else {
        showFormMsg(msg, "warning");
        $(selector).focus();
      }
    };

    if ($("#addNewCompanyForm").length) {
      $("#addNewCompanyForm")
        .off("submit")
        .on("submit", function (e) {
          e.preventDefault();

          const name = $("#name").val().trim();
          const email = $("#email").val().trim();
          const mobile = $("#mobile").val().trim();

          if (!name) return displayFormWarning("Kindly enter company name", "#name");
          if (!email)
            return displayFormWarning("Kindly enter company email", "#email");
          if (!mobile)
            return displayFormWarning("Kindly enter mobile number", "#mobile");

          const submitBtn = $(this).find('input[type="submit"]');
          const originalBtnVal = submitBtn.val();

          submitBtn.prop("disabled", true).val("Submitting...");

          const formData = new FormData(this);

          $.ajax({
            type: "POST",
            url: "/api/company/create/",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
              showFormMsg(response.msg, response.status);
              if (response.status === "success") {
                setTimeout(function () {
                  window.location.href = "/dashboard/company/?type=list&status=all";
                }, 1500);
              } else {
                submitBtn.prop("disabled", false).val(originalBtnVal);
              }
            },
            error: function (xhr) {
              console.error("❌ Error:", xhr);
              const err = xhr.responseJSON || {
                msg: "Something went wrong. Please try again.",
                status: "error",
              };
              showFormMsg(err.msg, err.status);
              submitBtn.prop("disabled", false).val(originalBtnVal);
            },
          });
        });
    }

    if ($("#updateCompanyForm").length) {
      $("#updateCompanyForm")
        .off("submit")
        .on("submit", function (e) {
          e.preventDefault();

          const name = $("#name").val().trim();
          const email = $("#email").val().trim();
          const mobile = $("#mobile").val().trim();

          if (!name) return displayFormWarning("Kindly enter company name", "#name");
          if (!email)
            return displayFormWarning("Kindly enter company email", "#email");
          if (!mobile)
            return displayFormWarning("Kindly enter mobile number", "#mobile");

          const submitBtn = $(this).find('input[type="submit"]');
          const originalBtnVal = submitBtn.val();

          submitBtn.prop("disabled", true).val("Submitting...");

          const formData = new FormData(this);

          $.ajax({
            type: "POST",
            url: "/api/company/update/",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
              showFormMsg(response.msg, response.status);
              if (response.status === "success") {
                setTimeout(function () {
                  window.location.href = "/dashboard/company/?type=list&status=all";
                }, 1500);
              } else {
                submitBtn.prop("disabled", false).val(originalBtnVal);
              }
            },
            error: function (xhr) {
              console.error("❌ Error:", xhr);
              const err = xhr.responseJSON || {
                msg: "Something went wrong. Please try again.",
                status: "error",
              };
              showFormMsg(err.msg, err.status);
              submitBtn.prop("disabled", false).val(originalBtnVal);
            },
          });
        });
    }

    if ($("#addNewVendorForm").length) {
      $("#addNewVendorForm")
        .off("submit")
        .on("submit", function (e) {
          e.preventDefault();

          const name = $("#name").val().trim();
          const email = $("#email").val().trim();
          const mobile = $("#mobile").val().trim();

          if (!name) return displayFormWarning("Kindly enter vendor name", "#name");
          if (!email)
            return displayFormWarning("Kindly enter vendor email", "#email");
          if (!mobile)
            return displayFormWarning("Kindly enter mobile number", "#mobile");

          const submitBtn = $(this).find('input[type="submit"]');
          const originalBtnVal = submitBtn.val();

          submitBtn.prop("disabled", true).val("Submitting...");

          const formData = new FormData(this);

          $.ajax({
            type: "POST",
            url: "/api/vendor/create/",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
              showFormMsg(response.msg, response.status);
              if (response.status === "success") {
                setTimeout(function () {
                  window.location.href = "/dashboard/vendor/?type=list&status=all";
                }, 1500);
              } else {
                submitBtn.prop("disabled", false).val(originalBtnVal);
              }
            },
            error: function (xhr) {
              console.error("❌ Error:", xhr);
              const err = xhr.responseJSON || {
                msg: "Something went wrong. Please try again.",
                status: "error",
              };
              showFormMsg(err.msg, err.status);
              submitBtn.prop("disabled", false).val(originalBtnVal);
            },
          });
        });
    }

    if ($("#updateVendorForm").length) {
      $("#updateVendorForm")
        .off("submit")
        .on("submit", function (e) {
          e.preventDefault();

          const name = $("#name").val().trim();
          const email = $("#email").val().trim();
          const mobile = $("#mobile").val().trim();

          if (!name) return displayFormWarning("Kindly enter vendor name", "#name");
          if (!email)
            return displayFormWarning("Kindly enter vendor email", "#email");
          if (!mobile)
            return displayFormWarning("Kindly enter mobile number", "#mobile");

          const submitBtn = $(this).find('input[type="submit"]');
          const originalBtnVal = submitBtn.val();

          submitBtn.prop("disabled", true).val("Submitting...");

          const formData = new FormData(this);

          $.ajax({
            type: "POST",
            url: "/api/vendor/update/",
            dataType: "json",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
              showFormMsg(response.msg, response.status);
              if (response.status === "success") {
                setTimeout(function () {
                  window.location.href = "/dashboard/vendor/?type=list&status=all";
                }, 1500);
              } else {
                submitBtn.prop("disabled", false).val(originalBtnVal);
              }
            },
            error: function (xhr) {
              console.error("❌ Error:", xhr);
              const err = xhr.responseJSON || {
                msg: "Something went wrong. Please try again.",
                status: "error",
              };
              showFormMsg(err.msg, err.status);
              submitBtn.prop("disabled", false).val(originalBtnVal);
            },
          });
        });
    }
  }
});
