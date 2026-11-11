(function () {
  "use strict";

  var DECIMAL_PATTERN = /^\d+(?:\.\d{1,2})?$/;

  function padTwo(num) {
    return num < 10 ? "0" + num : String(num);
  }

  function parseFilterDate(raw) {
    var trimmed = String(raw || "").trim();
    if (!trimmed) {
      return null;
    }

    var year;
    var month;
    var day;
    var ymdMatch = /^(\d{4})-(\d{2})-(\d{2})$/.exec(trimmed);
    var dmyMatch = /^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/.exec(trimmed);

    if (ymdMatch) {
      year = parseInt(ymdMatch[1], 10);
      month = parseInt(ymdMatch[2], 10);
      day = parseInt(ymdMatch[3], 10);
    } else if (dmyMatch) {
      day = parseInt(dmyMatch[1], 10);
      month = parseInt(dmyMatch[2], 10);
      year = parseInt(dmyMatch[3], 10);
    } else {
      return null;
    }

    if (year < 1970 || year > 2100 || month < 1 || month > 12 || day < 1 || day > 31) {
      return null;
    }

    var dt = new Date(year, month - 1, day);
    if (
      dt.getFullYear() !== year ||
      dt.getMonth() !== month - 1 ||
      dt.getDate() !== day
    ) {
      return null;
    }

    return {
      ymd: year + "-" + padTwo(month) + "-" + padTwo(day),
      display: padTwo(day) + "/" + padTwo(month) + "/" + year
    };
  }

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

    function hideOpenDatePickers() {
      var closedAny = false;
      if (typeof window.jQuery === "undefined") {
        return closedAny;
      }
      var $ = window.jQuery;
      [dateFromInput, dateToInput].forEach(function (input) {
        if (!input) {
          return;
        }
        var picker = $(input).data("daterangepicker");
        if (picker && picker.isShowing) {
          picker.hide();
          closedAny = true;
        }
      });
      return closedAny;
    }

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
        initDatePickers();
        if (focusTarget === "first") {
          var firstControl =
            searchInput ||
            form.querySelector('input:not([type="hidden"]), select, button');
          if (firstControl && typeof firstControl.focus === "function") {
            firstControl.focus();
          }
        }
      } else {
        hideOpenDatePickers();
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
          if (hideOpenDatePickers()) {
            return;
          }
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

    function validateDateField(input, label) {
      if (!input) {
        return true;
      }
      var raw = String(input.value || "").trim();
      if (raw === "") {
        clearFieldError(input);
        return true;
      }
      var parsed = parseFilterDate(raw);
      if (!parsed) {
        var msg = label + " must be a valid date in DD/MM/YYYY format.";
        if (typeof input.setCustomValidity === "function") {
          input.setCustomValidity(msg);
        }
        input.setAttribute("aria-invalid", "true");
        return msg;
      }
      clearFieldError(input);
      return true;
    }

    function hasAnyInvalidRangeField() {
      var fields = [minPriceInput, maxPriceInput, dateFromInput, dateToInput];
      for (var k = 0; k < fields.length; k++) {
        if (fields[k] && fields[k].getAttribute("aria-invalid") === "true") {
          return true;
        }
      }
      return false;
    }

    function refreshAllRangeValidity(announceError) {
      var checks = [
        {
          el: minPriceInput,
          res: validateDecimalField(minPriceInput, "Minimum price")
        },
        {
          el: maxPriceInput,
          res: validateDecimalField(maxPriceInput, "Maximum price")
        },
        {
          el: dateFromInput,
          res: validateDateField(dateFromInput, "From date")
        },
        {
          el: dateToInput,
          res: validateDateField(dateToInput, "To date")
        }
      ];

      var firstErrEl = null;
      var firstErrMsg = "";
      for (var c = 0; c < checks.length; c++) {
        if (checks[c].res !== true) {
          firstErrEl = checks[c].el;
          firstErrMsg = checks[c].res;
          break;
        }
      }

      if (!firstErrEl) {
        setFeedbackMessage("");
        return null;
      }

      if (announceError) {
        setFeedbackMessage(firstErrMsg);
      }

      return firstErrEl;
    }

    var validatedFields = [
      { el: minPriceInput, label: "Minimum price", validator: validateDecimalField },
      { el: maxPriceInput, label: "Maximum price", validator: validateDecimalField },
      { el: dateFromInput, label: "From date", validator: validateDateField },
      { el: dateToInput, label: "To date", validator: validateDateField }
    ];

    for (var i = 0; i < validatedFields.length; i++) {
      (function (item) {
        if (!item.el) {
          return;
        }
        var touched = false;
        item.el.addEventListener("input", function () {
          touched = true;
          clearFieldError(item.el);
          if (!hasAnyInvalidRangeField()) {
            setFeedbackMessage("");
          }
        });

        item.el.addEventListener("blur", function () {
          if (!touched && String(item.el.value || "").trim() === "") {
            return;
          }
          var check = item.validator(item.el, item.label);
          if (check !== true) {
            setFeedbackMessage(check);
          } else {
            refreshAllRangeValidity(true);
          }
        });
      })(validatedFields[i]);
    }

    function updatePickerDrops(input, picker) {
      if (!input || !picker || !picker.container) {
        return;
      }
      var rect = input.getBoundingClientRect();
      var pickerHeight = picker.container.outerHeight() || 332;
      var gap = 12;
      var spaceBelow = window.innerHeight - rect.bottom;
      var spaceAbove = rect.top;
      picker.drops =
        spaceBelow < pickerHeight + gap && spaceAbove > spaceBelow
          ? "up"
          : "down";
    }

    function initDatePickers() {
      if (
        typeof window.jQuery === "undefined" ||
        !window.jQuery.fn ||
        typeof window.jQuery.fn.daterangepicker !== "function" ||
        typeof window.moment === "undefined"
      ) {
        return;
      }

      var $ = window.jQuery;
      var currentYear = new Date().getFullYear();
      var dateFields = [dateFromInput, dateToInput];

      dateFields.forEach(function (input, idx) {
        if (!input) {
          return;
        }
        var $input = $(input);
        if ($input.data("daterangepicker")) {
          return;
        }

        var parsedInitial = parseFilterDate(input.value);
        var startDateOption = parsedInitial
          ? window.moment(parsedInitial.ymd, "YYYY-MM-DD")
          : window.moment();

        var onDrpScroll = function () {
          var drp = $input.data("daterangepicker");
          if (drp && drp.isShowing) {
            drp.move();
          }
        };

        $input
          .daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            maxYear: currentYear + 5,
            autoUpdateInput: false,
            startDate: startDateOption,
            opens: idx === 1 ? "left" : "right",
            drops: "down",
            cancelButtonClasses: "btn-drp-clear",
            applyButtonClasses: "btn-drp-apply",
            locale: {
              format: "DD/MM/YYYY",
              applyLabel: "Apply",
              cancelLabel: "Clear"
            }
          })
          .on("show.daterangepicker", function (ev, picker) {
            dateFields.forEach(function (other) {
              if (other && other !== input) {
                var otherPicker = $(other).data("daterangepicker");
                if (otherPicker && otherPicker.isShowing) {
                  otherPicker.hide();
                }
              }
            });
            var parsedNow = parseFilterDate(input.value);
            if (parsedNow) {
              var m = window.moment(parsedNow.ymd, "YYYY-MM-DD");
              picker.setStartDate(m);
              picker.setEndDate(m);
              picker.updateView();
            }
            if (picker && picker.container) {
              picker.container.addClass("pl-daterangepicker");
              picker.move();
            }
            document.removeEventListener("scroll", onDrpScroll, true);
            document.addEventListener("scroll", onDrpScroll, true);
          })
          .on("hide.daterangepicker", function () {
            document.removeEventListener("scroll", onDrpScroll, true);
          })
          .on("apply.daterangepicker", function (ev, picker) {
            input.value = picker.startDate.format("DD/MM/YYYY");
            clearFieldError(input);
            refreshAllRangeValidity(true);
          })
          .on("cancel.daterangepicker", function () {
            input.value = "";
            clearFieldError(input);
            refreshAllRangeValidity(true);
          });

        var instance = $input.data("daterangepicker");
        if (instance) {
          var protoMove = Object.getPrototypeOf(instance).move;
          if (typeof protoMove === "function") {
            instance.move = function () {
              if (instance.container) {
                instance.container.addClass("pl-daterangepicker");
              }
              updatePickerDrops(input, instance);
              return protoMove.apply(this, arguments);
            };
          }
          if (instance.container) {
            instance.container.addClass("pl-daterangepicker");
            instance.container.on("keydown.plDrp", function (ev) {
              if (ev.key === "Escape") {
                ev.preventDefault();
                ev.stopPropagation();
                instance.hide();
              }
            });
            instance.container.on(
              "click.plDrpReposition change.plDrpReposition",
              ".prev, .next, select.monthselect, select.yearselect",
              function () {
                window.requestAnimationFrame(function () {
                  if (instance.isShowing) {
                    instance.move();
                  }
                });
              }
            );
          }
        }
      });
    }

    initDatePickers();

    var imeGuardedInputs = [
      searchInput,
      tagInput,
      minPriceInput,
      maxPriceInput,
      dateFromInput,
      dateToInput
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
      if (dateFromInput) {
        dateFromInput.value = String(dateFromInput.value || "").trim();
      }
      if (dateToInput) {
        dateToInput.value = String(dateToInput.value || "").trim();
      }

      var invalidRangeInput = refreshAllRangeValidity(true);
      if (invalidRangeInput) {
        event.preventDefault();
        if (filterPanel && filterPanel.hasAttribute("hidden")) {
          setFilterPanelOpen(true, { focusTarget: null });
        }
        if (submitBtn) {
          submitBtn.disabled = false;
        }
        invalidRangeInput.focus();
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
        var fromParsed = parseFilterDate(dateFromInput.value);
        var toParsed = parseFilterDate(dateToInput.value);
        if (fromParsed && toParsed) {
          if (fromParsed.ymd > toParsed.ymd) {
            dateFromInput.value = toParsed.display;
            dateToInput.value = fromParsed.display;
          } else {
            dateFromInput.value = fromParsed.display;
            dateToInput.value = toParsed.display;
          }
        } else if (fromParsed) {
          dateFromInput.value = fromParsed.display;
        } else if (toParsed) {
          dateToInput.value = toParsed.display;
        }
      }

      hideOpenDatePickers();

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
