<h2>Manage Menu</h2>

<div class="form-box main-box" style="display:flex; gap:16px; border: none;">
    <!-- LEFT PANE -->
    <div class="fix-box" style="min-width:250px; max-width:280px;">
        <!-- Categories (open) -->
        <h3 id="catToggle"
            style="background:#eef2f6;color:#00609e;border:1px solid #e2e2e2;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;">
            Categories <span class="arrow">▼</span>
        </h3>
        <ul id="categoryBox" class="menu-creator">
            <?php
            $sql = "SELECT c.category_id, c.category_title, l.link_url
              FROM " . TABLE_PREFIX . "category c
              JOIN " . TABLE_PREFIX . "link l
                ON c.category_id = l.link_relation_id
               AND l.link_type='category'
               AND l.link_status=200
             WHERE c.category_site_id='" . SITE_ID . "'
             ORDER BY c.category_id DESC";
            if ($res = mysqli_query($connection, $sql)) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $label = htmlspecialchars($row['category_title'], ENT_QUOTES);
                    $url   = htmlspecialchars($row['link_url'], ENT_QUOTES);
                    echo "<li class='dd-new-item' data-label='{$label}' data-url='{$url}'>{$label}</li>";
                }
            }
            ?>
        </ul>

        <!-- Pages (closed) -->
        <h3 id="pageToggle"
            style="background:#eef2f6;color:#00609e;border:1px solid #e2e2e2;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;">
            Pages <span class="arrow">▶</span>
        </h3>
        <ul id="pageBox" class="menu-creator" style="display:none">
            <?php
            $sql = "SELECT p.page_id, p.page_title, l.link_url
              FROM " . TABLE_PREFIX . "page p
              JOIN " . TABLE_PREFIX . "link l
                ON p.page_id = l.link_relation_id
               AND l.link_type='page'
               AND l.link_status=200
             WHERE p.page_site_id='" . SITE_ID . "'
             ORDER BY p.page_id DESC";
            if ($res = mysqli_query($connection, $sql)) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $label = htmlspecialchars($row['page_title'], ENT_QUOTES);
                    $url   = htmlspecialchars($row['link_url'], ENT_QUOTES);
                    echo "<li class='dd-new-item' data-label='{$label}' data-url='{$url}'>{$label}</li>";
                }
            }
            ?>
        </ul>

        <!-- Custom Link (open) -->
        <h3 id="customToggle"
            style="background:#eef2f6;color:#00609e;border:1px solid #e2e2e2;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;">
            Custom Link <span class="arrow">▼</span>
        </h3>
        <div id="customBox" class="menu-creator" style="display:block;height:auto;">
            <input id="customLabel" class="form-control inside-shadow" type="text" placeholder="Label e.g. Offers" />
            <input id="customUrl" class="form-control inside-shadow" type="url" placeholder="URL e.g. /offers" />
            <button id="addCustom" type="button" class="btn btn-blue btn-block">Add to Menu</button>
        </div>
    </div>

    <!-- RIGHT PANE -->
    <div class="menu-box" style="flex:1;">
        <div class="main-box" style="border:1px solid #e5e5e5;border-radius:6px;">

            <!-- Menu Position controls -->
            <div class="mb-positionbar"
                style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin:8px 0;">
                <label style="font-weight:600;">Menu Position:</label>
                <select id="menuPosition" class="form-control inside-shadow" style="max-width:260px;">
                    <?php
                    $defaults = ['Main Header', 'Footer 1', 'Footer 2', 'Footer 3', 'Footer 4'];
                    foreach ($defaults as $p) {
                        echo "<option>" . htmlspecialchars($p, ENT_QUOTES) . "</option>";
                    }
                    ?>
                    <option value="__NEW__">➕ New menu…</option>
                </select>
            </div>

            <div style="margin-bottom:8px;">
                <button id="addBlank" class="btn btn-blue btn-sm">+ Add Blank</button>
            </div>

            <!-- Blueprint for menu items (template) -->
            <li class="dd-item-blueprint" style="display:none">
                <!-- COMPACT ROW: Name (left) — URL + actions (right) -->
                <div class="mb-row">
                    <!-- Drag handle (grip dots) -->
                    <span class="dd-handle" title="Drag"></span>

                    <div class="row-main">
                        <strong class="item-title">[Menu Name]</strong>
                    </div>

                    <div class="row-side">
                        <span class="item-url-text"></span>
                        <button class="btn btn-sm btn-blue item-edit" type="button">Edit</button>
                        <button class="btn btn-sm btn-red item-remove" type="button" title="Remove">✕</button>
                    </div>
                </div>

                <!-- HIDDEN EDIT BOX -->
                <div class="edit-box" style="display:none;">
                    <div class="form-group" style="margin-bottom:8px;">
                        <label>Menu Name</label>
                        <input type="text" class="form-control inside-shadow field-label" />
                    </div>
                    <div class="form-group" style="margin-bottom:8px;">
                        <label>URL</label>
                        <input type="url" class="form-control inside-shadow field-url" />
                    </div>
                    <div class="edit-actions" style="display:flex;gap:8px;">
                        <button class="btn btn-green btn-sm save-edit" type="button">Save</button>
                        <button class="btn btn-sm cancel-edit" type="button">Cancel</button>
                    </div>
                </div>
            </li>

            <ol id="menuRoot" class="dd-list"></ol>

            <div style="margin-top:10px;">
                <button id="expandAll" class="btn btn-sm">Expand all</button>
                <button id="collapseAll" class="btn btn-sm">Collapse all</button>
                <button id="clearAll" class="btn btn-red btn-sm">Clear</button>
                <button id="submitMenu" style="float: left; width: 100%;" class="btn btn-green">Save menu</button>
            </div>
        </div>
    </div>
</div>