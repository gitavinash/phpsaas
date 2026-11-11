(function () {
  "use strict";

  var DECIMAL_PATTERN = /^\d+(?:\.\d{1,2})?$/;

  function markThumbFallback(img) {
    if (!img || img.tagName !== "IMG") {
      return;
    }
    var thumb = img.closest(".product-thumb");
    if (thumb) {
      thumb.classList.add("is-fallback");
    }
  }

  function initImageFallbacks() {
    var root = document.querySelector(".modern-product-list");
    if (!root) {
      return;
    }

    document.addEventListener(
      "error",
      function (event) {
        var target = event && event.target;
        if (
          target &&
          target.tagName === "IMG" &&
          target.closest(".modern-product-list .product-thumb")
        ) {
          markThumbFallback(target);
        }
      },
      true
    );

    var images = root.querySelectorAll(".product-thumb img");
    for (var i = 0; i < images.length; i++) {
      var img = images[i];
      if (img.complete && img.naturalWidth === 0) {
        markThumbFallback(img);
      }
    }
  }

  function initFilterForm() {
    var form = document.getElementById("productFilterForm");
    if (!form) {
      return;
    }

    var toggleBtn = document.getElementById("toggleProductFilters");
    var filterPanel = document.getElementById("productFilterPanel");
    var closeBtn = document.getElementById("closeProductFilters");
    var cancelBtn = document.getElementById("cancelProductFilters");
    var openTriggers = document.querySelectorAll('[data-open-filters="true"]');
    var feedback = document.getElementById("productFilterFeedback");
    var submitBtn = form.querySelector('button[type="submit"]');
    var minPriceInput = document.getElementById("filterMinPrice");
    var maxPriceInput = document.getElementById("filterMaxPrice");
    var dateFromInput = document.getElementById("filterDateFrom");
    var dateToInput = document.getElementById("filterDateTo");
    var searchInput = document.getElementById("filterSearch");
    var tagInput = document.getElementById("filterTag");

    function setFilterPanelOpen(open, options) {
      if (!filterPanel) {
        return;
      }
      var opts = options || {};
      var focusTarget = opts.focusTarget || null;

      if (toggleBtn) {
        toggleBtn.setAttribute("aria-expanded", open ? "true" : "false");
      }

      if (open) {
        filterPanel.removeAttribute("hidden");
        if (focusTarget === "first") {
          var firstControl =
            searchInput ||
            form.querySelector('input:not([type="hidden"]), select, button');
          if (firstControl && typeof firstControl.focus === "function") {
            firstControl.focus();
          }
        }
      } else {
        filterPanel.setAttribute("hidden", "");
        if (
          focusTarget === "trigger" &&
          toggleBtn &&
          typeof toggleBtn.focus === "function"
        ) {
          toggleBtn.focus();
        }
      }
    }

    if (toggleBtn && filterPanel) {
      toggleBtn.addEventListener("click", function () {
        var currentlyExpanded = !filterPanel.hasAttribute("hidden");
        setFilterPanelOpen(!currentlyExpanded, { focusTarget: null });
      });
    }

    if (closeBtn && filterPanel) {
      closeBtn.addEventListener("click", function () {
        setFilterPanelOpen(false, { focusTarget: "trigger" });
      });
    }

    if (cancelBtn && filterPanel) {
      cancelBtn.addEventListener("click", function () {
        setFilterPanelOpen(false, { focusTarget: "trigger" });
      });
    }

    for (var t = 0; t < openTriggers.length; t++) {
      openTriggers[t].addEventListener("click", function () {
        setFilterPanelOpen(true, { focusTarget: "first" });
      });
    }

    if (filterPanel) {
      filterPanel.addEventListener("keydown", function (event) {
        if (
          event.key === "Escape" &&
          !event.defaultPrevented &&
          !event.isComposing
        ) {
          event.preventDefault();
          setFilterPanelOpen(false, { focusTarget: "trigger" });
        }
      });
    }

    function setFeedbackMessage(message) {
      if (!feedback) {
        return;
      }
      feedback.textContent = message || "";
    }

    function clearFieldError(input) {
      if (!input) {
        return;
      }
      if (typeof input.setCustomValidity === "function") {
        input.setCustomValidity("");
      }
      input.removeAttribute("aria-invalid");
    }

    function validateDecimalField(input, label) {
      if (!input) {
        return true;
      }
      var raw = String(input.value || "").trim();
      if (raw === "") {
        clearFieldError(input);
        return true;
      }
      if (!DECIMAL_PATTERN.test(raw)) {
        var msg =
          label + " must be a positive amount (for example, 250 or 250.50).";
        if (typeof input.setCustomValidity === "function") {
          input.setCustomValidity(msg);
        }
        input.setAttribute("aria-invalid", "true");
        return msg;
      }
      clearFieldError(input);
      return true;
    }

    function refreshAllPriceValidity(announceError) {
      var minCheck = validateDecimalField(minPriceInput, "Minimum price");
      var maxCheck = validateDecimalField(maxPriceInput, "Maximum price");

      if (minCheck === true && maxCheck === true) {
        setFeedbackMessage("");
        return null;
      }

      if (announceError) {
        var firstErr = minCheck !== true ? minCheck : maxCheck;
        setFeedbackMessage(firstErr);
      }

      return minCheck !== true ? minPriceInput : maxPriceInput;
    }

    var priceInputs = [
      { el: minPriceInput, label: "Minimum price" },
      { el: maxPriceInput, label: "Maximum price" }
    ];

    for (var i = 0; i < priceInputs.length; i++) {
      (function (item) {
        if (!item.el) {
          return;
        }
        var touched = false;
        item.el.addEventListener("input", function () {
          touched = true;
          clearFieldError(item.el);
          var hasRemainingInvalid =
            (minPriceInput &&
              minPriceInput.getAttribute("aria-invalid") === "true") ||
            (maxPriceInput &&
              maxPriceInput.getAttribute("aria-invalid") === "true");
          if (!hasRemainingInvalid) {
            setFeedbackMessage("");
          }
        });

        item.el.addEventListener("blur", function () {
          if (!touched && String(item.el.value || "").trim() === "") {
            return;
          }
          var check = validateDecimalField(item.el, item.label);
          if (check !== true) {
            setFeedbackMessage(check);
          } else {
            refreshAllPriceValidity(true);
          }
        });
      })(priceInputs[i]);
    }

    var imeGuardedInputs = [
      searchInput,
      tagInput,
      minPriceInput,
      maxPriceInput
    ];
    for (var j = 0; j < imeGuardedInputs.length; j++) {
      if (!imeGuardedInputs[j]) {
        continue;
      }
      imeGuardedInputs[j].addEventListener("keydown", function (event) {
        if (
          event.key === "Enter" &&
          (event.isComposing || event.keyCode === 229)
        ) {
          event.preventDefault();
        }
      });
    }

    form.addEventListener("submit", function (event) {
      if (searchInput) {
        searchInput.value = String(searchInput.value || "").trim();
      }
      if (tagInput) {
        tagInput.value = String(tagInput.value || "").trim();
      }
      if (minPriceInput) {
        minPriceInput.value = String(minPriceInput.value || "").trim();
      }
      if (maxPriceInput) {
        maxPriceInput.value = String(maxPriceInput.value || "").trim();
      }

      var invalidPriceInput = refreshAllPriceValidity(true);
      if (invalidPriceInput) {
        event.preventDefault();
        if (filterPanel && filterPanel.hasAttribute("hidden")) {
          setFilterPanelOpen(true, { focusTarget: null });
        }
        if (submitBtn) {
          submitBtn.disabled = false;
        }
        invalidPriceInput.focus();
        return;
      }

      if (minPriceInput && maxPriceInput) {
        var minVal = minPriceInput.value;
        var maxVal = maxPriceInput.value;
        if (
          minVal !== "" &&
          maxVal !== "" &&
          parseFloat(minVal) > parseFloat(maxVal)
        ) {
          minPriceInput.value = maxVal;
          maxPriceInput.value = minVal;
        }
      }

      if (dateFromInput && dateToInput) {
        var fromVal = dateFromInput.value;
        var toVal = dateToInput.value;
        if (fromVal !== "" && toVal !== "" && fromVal > toVal) {
          dateFromInput.value = toVal;
          dateToInput.value = fromVal;
        }
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        window.setTimeout(function () {
          submitBtn.disabled = false;
        }, 2500);
      }
    });

    window.addEventListener("pageshow", function () {
      if (submitBtn) {
        submitBtn.disabled = false;
      }
    });
  }

  function enhanceProductTable() {
    var tableEl = document.getElementById("table");
    if (!tableEl || !tableEl.closest(".modern-product-list")) {
      return;
    }

    var emptyMsg = String(tableEl.getAttribute("data-empty-message") || "").trim();

    if (typeof window.jQuery === "undefined") {
      return;
    }

    var $ = window.jQuery;
    if (!$.fn || !$.fn.DataTable) {
      return;
    }

    var $table = $(tableEl);
    var colCount = $table.find("thead th").length;
    var defaultCol = parseInt($table.attr("data-default-order-column"), 10);
    var defaultDirAttr = String(
      $table.attr("data-default-order-dir") || "desc"
    ).toLowerCase();
    var defaultDir = defaultDirAttr === "asc" ? "asc" : "desc";
    var hasValidDefaultOrder =
      !isNaN(defaultCol) && defaultCol >= 0 && defaultCol < colCount;

    var dt;
    if ($.fn.DataTable.isDataTable(tableEl)) {
      dt = $table.DataTable();
      if (hasValidDefaultOrder) {
        var currentOrder = dt.order();
        var needsReorder =
          !Array.isArray(currentOrder) ||
          currentOrder.length === 0 ||
          Number(currentOrder[0][0]) !== defaultCol ||
          String(currentOrder[0][1]).toLowerCase() !== defaultDir;

        if (needsReorder) {
          dt.order([[defaultCol, defaultDir]]).draw(false);
        }
      }
    } else {
      var parsedLen = parseInt($table.attr("data-page-length"), 10);
      var pageLen = !isNaN(parsedLen) && parsedLen > 0 ? parsedLen : 10;
      var options = {
        autoWidth: false,
        pageLength: pageLen,
        lengthMenu: [
          [10, 25, 50, 100],
          [10, 25, 50, 100]
        ],
        language: {
          lengthMenu: "Rows _MENU_",
          search: "",
          searchPlaceholder: "Quick filter..."
        }
      };

      if (emptyMsg) {
        options.language.emptyTable = emptyMsg;
      }

      if (hasValidDefaultOrder) {
        options.order = [[defaultCol, defaultDir]];
      }

      dt = $table.DataTable(options);
    }

    if (emptyMsg && dt && dt.data && dt.data().count() === 0) {
      var emptyCell = tableEl.querySelector("td.dataTables_empty");
      if (emptyCell) {
        emptyCell.textContent = emptyMsg;
      }
    }

    var wrapper = document.getElementById("table_wrapper");
    var lengthSlot =
      document.getElementById("productTableLengthSlot") ||
      document.getElementById("productTableControlsTop");
    var searchSlot =
      document.getElementById("productTableSearchSlot") ||
      document.getElementById("productTableControlsTop");
    var bottomControls = document.getElementById("productTableControlsBottom");

    if (wrapper) {
      var lengthEl = document.getElementById("table_length");
      var filterEl = document.getElementById("table_filter");
      if (lengthEl && lengthSlot) {
        var lengthSelect = lengthEl.querySelector("select");
        if (lengthSelect && !lengthSelect.getAttribute("aria-label")) {
          lengthSelect.setAttribute("aria-label", "Rows per page");
        }
        lengthSlot.appendChild(lengthEl);
      }
      if (filterEl && searchSlot) {
        var filterInput = filterEl.querySelector(
          'input[type="search"], input[type="text"]'
        );
        if (filterInput) {
          if (!filterInput.getAttribute("aria-label")) {
            filterInput.setAttribute(
              "aria-label",
              "Quick filter displayed products"
            );
          }
          var existingPlaceholder = String(
            filterInput.getAttribute("placeholder") || ""
          ).trim();
          if (
            existingPlaceholder === "" ||
            existingPlaceholder === "Search records..."
          ) {
            filterInput.setAttribute("placeholder", "Quick filter...");
          }
        }
        var filterLabel = filterEl.querySelector("label");
        if (filterLabel) {
          for (var c = filterLabel.childNodes.length - 1; c >= 0; c--) {
            var node = filterLabel.childNodes[c];
            if (node.nodeType === 3) {
              var txt = String(node.nodeValue || "").trim();
              if (/^search:?$/i.test(txt) || /^filter:?$/i.test(txt)) {
                filterLabel.removeChild(node);
              }
            }
          }
        }
        searchSlot.appendChild(filterEl);
      }
    }

    if (wrapper && bottomControls) {
      var infoEl = document.getElementById("table_info");
      var paginateEl = document.getElementById("table_paginate");
      if (infoEl) {
        bottomControls.appendChild(infoEl);
      }
      if (paginateEl) {
        bottomControls.appendChild(paginateEl);
      }
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    initImageFallbacks();
    initFilterForm();
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      if (typeof window.jQuery !== "undefined") {
        window.jQuery(enhanceProductTable);
      }
    });
  } else if (typeof window.jQuery !== "undefined") {
    window.jQuery(enhanceProductTable);
  }
})();
