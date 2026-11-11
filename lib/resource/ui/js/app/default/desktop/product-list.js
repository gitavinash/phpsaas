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

    var toggleBtn = document.getElementById("toggleAdvancedFilters");
    var advancedPanel = document.getElementById("advancedProductFilters");
    var feedback = document.getElementById("productFilterFeedback");
    var submitBtn = form.querySelector('button[type="submit"]');
    var minPriceInput = document.getElementById("filterMinPrice");
    var maxPriceInput = document.getElementById("filterMaxPrice");
    var dateFromInput = document.getElementById("filterDateFrom");
    var dateToInput = document.getElementById("filterDateTo");
    var searchInput = document.getElementById("filterSearch");
    var tagInput = document.getElementById("filterTag");

    function setAdvancedExpanded(expanded, shouldFocusFirst) {
      if (!toggleBtn || !advancedPanel) {
        return;
      }
      toggleBtn.setAttribute("aria-expanded", expanded ? "true" : "false");
      if (expanded) {
        advancedPanel.removeAttribute("hidden");
      } else {
        advancedPanel.setAttribute("hidden", "");
      }

      var labelSpan = toggleBtn.querySelector("[data-toggle-label]");
      if (labelSpan) {
        var closedText = labelSpan.getAttribute("data-label-closed") || "More Filters";
        var openText = labelSpan.getAttribute("data-label-open") || "Less Filters";
        labelSpan.textContent = expanded ? openText : closedText;
      }

      if (expanded && shouldFocusFirst) {
        var firstControl = advancedPanel.querySelector("input, select, button");
        if (firstControl && typeof firstControl.focus === "function") {
          firstControl.focus();
        }
      }
    }

    if (toggleBtn && advancedPanel) {
      toggleBtn.addEventListener("click", function () {
        var currentlyExpanded = toggleBtn.getAttribute("aria-expanded") === "true";
        setAdvancedExpanded(!currentlyExpanded, false);
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
        var msg = label + " must be a positive amount (for example, 250 or 250.50).";
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
            (minPriceInput && minPriceInput.getAttribute("aria-invalid") === "true") ||
            (maxPriceInput && maxPriceInput.getAttribute("aria-invalid") === "true");
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

    var imeGuardedInputs = [searchInput, tagInput, minPriceInput, maxPriceInput];
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
        if (advancedPanel && advancedPanel.hasAttribute("hidden")) {
          setAdvancedExpanded(true, false);
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
        if (minVal !== "" && maxVal !== "" && parseFloat(minVal) > parseFloat(maxVal)) {
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
    var defaultDirAttr = String($table.attr("data-default-order-dir") || "desc").toLowerCase();
    var defaultDir = defaultDirAttr === "asc" ? "asc" : "desc";
    var hasValidDefaultOrder = !isNaN(defaultCol) && defaultCol >= 0 && defaultCol < colCount;

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
          searchPlaceholder: "Quick filter table..."
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
    var topControls = document.getElementById("productTableControlsTop");
    var bottomControls = document.getElementById("productTableControlsBottom");

    if (wrapper && topControls) {
      var lengthEl = document.getElementById("table_length");
      var filterEl = document.getElementById("table_filter");
      if (lengthEl) {
        var lengthSelect = lengthEl.querySelector("select");
        if (lengthSelect && !lengthSelect.getAttribute("aria-label")) {
          lengthSelect.setAttribute("aria-label", "Rows per page");
        }
        topControls.appendChild(lengthEl);
      }
      if (filterEl) {
        var filterInput = filterEl.querySelector('input[type="search"], input[type="text"]');
        if (filterInput) {
          if (!filterInput.getAttribute("aria-label")) {
            filterInput.setAttribute("aria-label", "Quick filter displayed products");
          }
          if (!filterInput.getAttribute("placeholder")) {
            filterInput.setAttribute("placeholder", "Quick filter table...");
          }
        }
        topControls.appendChild(filterEl);
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
