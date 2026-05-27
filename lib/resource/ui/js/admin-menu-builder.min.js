$(function () {
  const MAX_DEPTH = 3;
  const $root = $("#menuRoot");

  // Local storage key (demo only; replace with your API)
  const LS_KEY = "menuPositions_v1";

  // ===== Store helpers (remove if you wire real API) =====
  function getStore() {
    try {
      return JSON.parse(localStorage.getItem(LS_KEY)) || {};
    } catch (e) {
      return {};
    }
  }
  function setStore(obj) {
    localStorage.setItem(LS_KEY, JSON.stringify(obj || {}));
  }

  const $positionSelect = $("#menuPosition");

  // ===== LEFT SIDE HEADERS — ACCORDION (open one at a time) =====
  const sections = [
    { $h: $("#catToggle"), $b: $("#categoryBoxContainer") },
    { $h: $("#pageToggle"), $b: $("#pageBoxContainer") },
    { $h: $("#tagToggle"), $b: $("#tagBoxContainer") },
    { $h: $("#customToggle"), $b: $("#customBox") },
  ];
  sections.forEach(({ $h, $b }) => {
    $h.on("click", function () {
      const isOpen = $b.is(":visible");
      // close all
      sections.forEach(({ $h, $b }) => {
        $b.hide();
        $h.find(".arrow").text("▶");
      });
      // open only if it was closed; if already open, leave all closed
      if (!isOpen) {
        $b.show();
        $h.find(".arrow").text("▼");
      }
    });
  });

  // ===== Left lists: click to add + Sortable clone to right =====
  $("#categoryBox, #pageBox, #tagBox").each(function () {
    const list = this;

    $(list).on("click", "li.dd-new-item", function () {
      const $li = $(this);
      const label = $li.data("label") || $.trim($li.text());
      const url = $li.data("url") || "";
      addItem(label, url);
      
      // Clear search input for this box
      const $container = $li.closest('[id$="BoxContainer"]');
      const $input = $container.find('input[type="text"]');
      if ($input.length > 0) {
        $input.val("").trigger("input");
      }
    });

    new Sortable(list, {
      group: { name: "menu", pull: "clone", put: false },
      sort: false,
      animation: 150,
    });
  });

  // ===== Instant search for left side lists =====
  function setupInstantSearch($input, $list) {
    const $clearBtn = $input.siblings(".clear-search");

    $input.on("input", function () {
      const query = $(this).val().toLowerCase().trim();
      
      if (query.length > 0) {
        $clearBtn.show();
      } else {
        $clearBtn.hide();
      }

      $list.find("li.dd-new-item").each(function () {
        const $li = $(this);
        const text = ($li.find("span").first().text() || $li.text()).toLowerCase();
        if (text.indexOf(query) > -1) {
          $li.show();
        } else {
          $li.hide();
        }
      });
    });

    $clearBtn.on("click", function () {
      $input.val("").trigger("input");
    });
  }
  setupInstantSearch($("#categorySearchInput"), $("#categoryBox"));
  setupInstantSearch($("#pageSearchInput"), $("#pageBox"));
  setupInstantSearch($("#tagSearchInput"), $("#tagBox"));

  // ===== Custom link =====
  $("#addCustom").on("click", function () {
    console.log(`Menu is clicked`);

    const label = $.trim($("#customLabel").val() || "");
    const url = $.trim($("#customUrl").val() || "");
    if (!label) {
      alert("Please enter a label");
      return;
    }
    addItem(label, url);
    $("#customLabel").val("");
    $("#customUrl").val("");
  });

  // ===== Add blank =====
  $("#addBlank").on("click", function () {
    addItem("Untitled", "");
  });

  // ===== Build item (Edit panel = accordion) =====
  function buildItem(label, url) {
    label = label || "Untitled";
    url = url || "";

    const $tpl = $(".dd-item-blueprint").first();
    const $li = $("<li/>", { class: "dd-item" }).html($tpl.html());

    const $title = $li.find(".item-title");
    const $urlTxt = $li.find(".item-url-text");
    const $edit = $li.find(".edit-box");
    const $inName = $li.find(".field-label");
    const $inUrl = $li.find(".field-url");

    $title.text(label);
    $urlTxt.text(url);
    $inName.val(label);
    $inUrl.val(url);

    $li.find(".item-remove").on("click", function () {
      $li.remove();
    });

    // 👇 Only one Edit panel open at a time
    $li.find(".item-edit").on("click", function () {
      $("#menuRoot .edit-box").not($edit).hide();
      $edit.show();
    });

    $li.find(".cancel-edit").on("click", function () {
      $edit.hide();
    });

    $li.find(".save-edit").on("click", function () {
      const newLabel = $.trim($inName.val() || "Untitled");
      const newUrl = $.trim($inUrl.val() || "");
      $title.text(newLabel);
      $urlTxt.text(newUrl);
      $edit.hide();
    });

    // children container
    const $child = $("<ol/>", { class: "dd-list" });
    $li.append($child);
    enableSortable($child[0]);

    return $li;
  }

  function addItem(label, url) {
    $root.append(buildItem(label, url));
  }

  // ===== Depth helpers =====
  function containerDepth(ol) {
    let d = 1,
      p = ol;
    while (p && p.id !== "menuRoot") {
      if ($(p).hasClass("dd-list")) d++;
      const $closestLi = $(p).closest("li");
      p = $closestLi.length ? $closestLi.parent()[0] : null;
    }
    return d;
  }

  function subtreeMax(li) {
    let max = 1;
    const stack = [[li, 1]];
    while (stack.length) {
      const node = stack.pop();
      const n = node[0],
        depth = node[1];
      max = Math.max(max, depth);
      $(n)
        .children(".dd-list")
        .children("li")
        .each(function () {
          stack.push([this, depth + 1]);
        });
    }
    return max;
  }

  // ===== Sortable enable on any <ol> =====
  function enableSortable(ol) {
    new Sortable(ol, {
      group: { name: "menu", pull: true, put: true },
      handle: ".dd-handle",
      draggable: "> li",
      animation: 150,
      ghostClass: "dd-ghost",
      fallbackOnBody: true,
      swapThreshold: 0.65,
      emptyInsertThreshold: 12,

      onAdd: function (evt) {
        // Convert from LEFT panes to real menu items
        if (evt.from.id === "categoryBox" || evt.from.id === "pageBox" || evt.from.id === "tagBox") {
          const src = evt.item;
          const $src = $(src);
          const label = $src.data("label") || $.trim($src.text());
          const url = $src.data("url") || "";
          const $li = buildItem(label, url);
          $src.replaceWith($li);

          // Clear search input for this box on successful drop
          const $container = $(evt.from).closest('[id$="BoxContainer"]');
          const $input = $container.find('input[type="text"]');
          if ($input.length > 0) {
            $input.val("").trigger("input");
          }
        }
      },
      onEnd: function (evt) {
        const it = evt.item;
        if (containerDepth(it.parentElement) + subtreeMax(it) - 1 > MAX_DEPTH) {
          const from = evt.from;
          const oldIndex = evt.oldIndex;
          if (from) {
            const ref = from.children[oldIndex] || null;
            from.insertBefore(it, ref);
          }
          alert("Max depth of " + MAX_DEPTH + " levels reached.");
        }
      },
    });
  }
  enableSortable($root[0]);

  // ===== Serialize / Render =====
  function serialize($ol) {
    $ol = $ol || $root;
    return $ol
      .children("li")
      .map(function () {
        const $li = $(this);
        const label = $.trim($li.find(".item-title").first().text() || "");
        const url = $.trim($li.find(".item-url-text").first().text() || "");
        const $child = $li.children(".dd-list").first();
        return {
          label: label,
          url: url,
          children: serialize($child),
        };
      })
      .get();
  }

  function renderMenu(data) {
    $root.empty();
    (data || []).forEach(function (node) {
      $root.append(renderNode(node));
    });

    function renderNode(n) {
      const $li = buildItem(n.label || "Untitled", n.url || "");
      const $child = $li.children(".dd-list").first();
      (n.children || []).forEach(function (c) {
        $child.append(renderNode(c));
      });
      return $li;
    }
  }

  // ===== Helper: convert menu name → lowercase_slug
  function menuKeyFromName(name) {
    return (name || "").toLowerCase().trim().replace(/\s+/g, "_");
  }

  // ===== Position handling =====
  function loadPosition(name) {
    const key = menuKeyFromName(name);

    $.ajax({
      url: "/api/appearance/menu-info/",
      method: "GET",
      dataType: "json",
      data: { name: key },
      success: function (res) {
        const items = (res && (res.items || (res.data && res.data))) || [];
        renderMenu(items);
      },
      error: function (xhr) {
        console.warn("Failed to load menu info:", xhr);
        renderMenu([]);
      },
    });
  }

  // When user changes menu position
  const defaultMenus = ['Main Header', 'Footer 1', 'Footer 2', 'Footer 3', 'Footer 4'];
  const $btnRename = $("#renameMenuPosition");
  const $btnDelete = $("#deleteMenuPosition");

  function toggleActionButtons(menuName) {
    if (!menuName || menuName === "__NEW__" || defaultMenus.includes(menuName)) {
        $btnRename.hide();
        $btnDelete.hide();
    } else {
        $btnRename.show();
        $btnDelete.show();
    }
  }

  $positionSelect.on("change", function () {
    const val = $(this).val();
    if (val === "__NEW__") {
      const name = (prompt("Enter new menu name:") || "").trim();
      if (!name) {
        const revert =
          $positionSelect
            .find("option")
            .not('[value="__NEW__"]')
            .first()
            .val() || "";
        $positionSelect.val(revert);
        toggleActionButtons(revert);
        return;
      }
      $positionSelect.append(
        $("<option/>", { value: name, text: name }).insertBefore(
          '[value="__NEW__"]',
        ),
      );
      $positionSelect.val(name);
      $root.empty();
      toggleActionButtons(name);
      return;
    }
    toggleActionButtons(val);
    loadPosition(val);
  });

  $btnRename.on("click", function() {
    const currentName = $positionSelect.val();
    const newName = (prompt("Rename menu:", currentName) || "").trim();
    if (!newName || newName === currentName) return;

    $.ajax({
      url: "/api/appearance/menu/",
      method: "PUT",
      contentType: "application/json",
      data: JSON.stringify({ old_position: currentName, new_position: newName }),
      success: function (response) {
        popUpMessage("center", response.msg, response.status);
        setTimeout(function () { location.reload(); }, 1500);
      },
      error: function (error) {
        popUpMessage("center", error.responseJSON?.msg || "Rename failed", "error");
      }
    });
  });

  $btnDelete.on("click", function() {
    const currentName = $positionSelect.val();
    if (!confirm("Are you sure you want to delete '" + currentName + "'?")) return;

    $.ajax({
      url: "/api/appearance/menu/",
      method: "DELETE",
      contentType: "application/json",
      data: JSON.stringify({ position: currentName }),
      success: function (response) {
        popUpMessage("center", response.msg, response.status);
        setTimeout(function () { location.reload(); }, 1500);
      },
      error: function (error) {
        popUpMessage("center", error.responseJSON?.msg || "Delete failed", "error");
      }
    });
  });

  // Initial load (first option)
  (function initLoad() {
    const first = $positionSelect.val();
    if (first && first !== "__NEW__") {
      toggleActionButtons(first);
      loadPosition(first);
    }
  })();

  // ===== Toolbar =====
  $("#expandAll").on("click", function () {
    $("#menuRoot .edit-box").show();
  });
  $("#collapseAll").on("click", function () {
    $("#menuRoot .edit-box").hide();
  });
  $("#clearAll").on("click", function () {
    $root.empty();
  });

  // ===== Submit current menu =====
  $("#submitMenu").on("click", function () {
    const name = $positionSelect.val() || "";
    if (!name || name === "__NEW__") {
      alert("Please select a valid menu position");
      return;
    }

    const payload = { position: name, items: serialize($root) };

    $.ajax({
      url: "/api/appearance/menu/",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify(payload),
      success: function (response) {
        popUpMessage("center", response.msg, response.status);
        setTimeout(function () {
          location.reload();
        }, 1500);
      },
      error: function (error, response) {
        popUpMessage(
          "center",
          error.responseJSON.msg,
          error.responseJSON.status,
        );
      },
    });
  });
});
