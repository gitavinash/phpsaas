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

  // ===== Collapsible sections =====
  function attachToggle($h3, $box) {
    const $arrow = $h3.find(".arrow");
    $h3.on("click", function () {
      const open = $box.css("display") !== "none";
      $box.css("display", open ? "none" : "");
      $arrow.text(open ? "▶" : "▼");
    });
  }
  attachToggle($("#catToggle"), $("#categoryBox"));
  attachToggle($("#pageToggle"), $("#pageBox"));
  attachToggle($("#customToggle"), $("#customBox"));

  // ===== Left lists: click to add + Sortable clone to right =====
  $("#categoryBox, #pageBox").each(function () {
    const list = this;

    $(list).on("click", "li.dd-new-item", function () {
      const $li = $(this);
      const label = $li.data("label") || $.trim($li.text());
      const url = $li.data("url") || "";
      addItem(label, url);
    });

    new Sortable(list, {
      group: { name: "menu", pull: "clone", put: false },
      sort: false,
      animation: 150,
    });
  });

  // ===== Custom link =====
  $("#addCustom").on("click", function () {
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

  // ===== Build item =====
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
    $li.find(".item-edit").on("click", function () {
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
        if (evt.from.id === "categoryBox" || evt.from.id === "pageBox") {
          const src = evt.item; // <li> dragged clone
          const $src = $(src);
          const label = $src.data("label") || $.trim($src.text());
          const url = $src.data("url") || "";
          const $li = buildItem(label, url);
          $src.replaceWith($li);
        }
      },
      onEnd: function (evt) {
        const it = evt.item;
        if (containerDepth(it.parentElement) + subtreeMax(it) - 1 > MAX_DEPTH) {
          // revert back to original
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

  // ===== Position handling =====
  function loadPosition(name) {
    // DEMO (localStorage)
    const store = getStore();
    renderMenu(store[name] || []);

    // TODO: AJAX — fetch menu by position name from server
    // $.ajax({
    //   url: '/api/menus/get',
    //   method: 'GET',
    //   data: { position: name },
    //   dataType: 'json'
    // }).done(function(res){
    //   // res.items should be the nested array [{label,url,children:[...]}]
    //   renderMenu(res.items || []);
    // });
  }

  function savePosition(name) {
    // DEMO (localStorage)
    const store = getStore();
    store[name] = serialize($root);
    setStore(store);

    // TODO: AJAX — save menu for this position
    // $.ajax({
    //   url: '/api/menus/save',
    //   method: 'POST',
    //   contentType: 'application/json',
    //   data: JSON.stringify({ position: name, items: store[name] })
    // }).done(function(res){
    //   console.log('Saved', res);
    // });
  }

  function ensureOption(name) {
    if (!name) return;
    const exists = $positionSelect
      .find("option")
      .map(function () {
        return $(this).val() || $(this).text();
      })
      .get();
    if (exists.indexOf(name) === -1) {
      const $newOpt = $("<option/>", { value: name, text: name });
      $positionSelect.find('option[value="__NEW__"]').before($newOpt);
    }
    $positionSelect.val(name);
  }

  // When user changes menu position
  $positionSelect.on("change", function () {
    const val = $(this).val();
    if (val === "__NEW__") {
      const name = (prompt("Enter new menu name:") || "").trim();
      if (!name) {
        // revert to first non-new option
        const revert =
          $positionSelect
            .find("option")
            .not('[value="__NEW__"]')
            .first()
            .val() || "";
        $positionSelect.val(revert);
        return;
      }
      ensureOption(name);
      $root.empty();
      savePosition(name); // create empty slot on store/api
      return;
    }
    loadPosition(val);
  });

  // Initial load (first option)
  (function initLoad() {
    const first = $positionSelect.val();
    if (first && first !== "__NEW__") loadPosition(first);

    // OPTIONAL: Replace defaults by fetching positions from server
    // TODO: AJAX — fetch positions list to populate the <select>
    // $.ajax({
    //   url: '/api/menus/positions',
    //   method: 'GET',
    //   dataType: 'json'
    // }).done(function(res){
    //   // res.positions = ['Main Header','Footer 1',...]
    //   const pos = res.positions || [];
    //   const $new = $('<select/>');
    //   pos.forEach(function(p){ $new.append($('<option/>',{text:p,value:p})); });
    //   $new.append($('<option/>',{text:'➕ New menu…', value:'__NEW__'}));
    //   $positionSelect.empty().append($new.children());
    //   // auto-load first
    //   const firstSrv = $positionSelect.find('option').not('[value="__NEW__"]').first().val();
    //   if (firstSrv) loadPosition(firstSrv);
    // });
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
    console.log("Submit payload", payload);
    alert('Check console for JSON payload for "' + name + '".');

    // TODO: AJAX — submit to server
    // $.ajax({
    //   url: '/api/menus/submit',
    //   method: 'POST',
    //   contentType: 'application/json',
    //   data: JSON.stringify(payload)
    // }).done(function(res){
    //   console.log('Submitted', res);
    // });
  });
});
