<h2>Manage Menu</h2>

<div class="form-box main-box" style="display:flex; gap:16px; border: none;">
    <!-- LEFT PANE -->
    <div class="fix-box" style="min-width:250px; max-width:280px;">
        <!-- Categories (open) -->
        <h3 id="catToggle"
            style="background:#eef2f6;color:#00609e;border:1px solid #e2e2e2;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;">
            Categories <span class="arrow">▼</span>
        </h3>
        <div id="categoryBoxContainer"
            style="display:block; border:1px solid #e2e2e2; border-radius:4px; padding:8px; margin-bottom:10px;">
            <div style="position:relative; width:100%; margin-bottom:8px;">
                <input type="text" id="categorySearchInput" class="form-control inside-shadow"
                    placeholder="Search categories..." style="padding-right:24px; margin-bottom:0;" />
                <span class="clear-search" data-target="#categorySearchInput"
                    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#94a3b8; display:none; font-weight:bold; font-size:14px; user-select:none; transition:color 0.2s;">✕</span>
            </div>
            <ul id="categoryBox" class="menu-creator" style="height:200px!important; overflow:auto;">
                <?php
                $sql = "SELECT c.category_id, c.category_title, l.link_url
                  FROM " . TABLE_PREFIX . "category c
                  JOIN " . TABLE_PREFIX . "link l
                    ON c.category_id = l.link_relation_id
                   AND l.link_type='category'
                   AND l.link_status=200
                 WHERE c.category_site_id='" . SITE_ID . "'
                 GROUP BY l.link_relation_id
                 ORDER BY c.category_id DESC ";
                if ($res = mysqli_query($connection, $sql)) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        $label = htmlspecialchars($row['category_title'], ENT_QUOTES);
                        $url   = htmlspecialchars($row['link_url'], ENT_QUOTES);
                        echo "<li class='dd-new-item' data-label='{$label}' data-url='{$url}'>{$label}</li>";
                    }
                }
                ?>
            </ul>
        </div>

        <!-- Pages (closed) -->
        <h3 id="pageToggle"
            style="background:#eef2f6;color:#00609e;border:1px solid #e2e2e2;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;">
            Pages <span class="arrow">▶</span>
        </h3>
        <div id="pageBoxContainer"
            style="display:none; border:1px solid #e2e2e2; border-radius:4px; padding:8px; margin-bottom:10px;">
            <div style="position:relative; width:100%; margin-bottom:8px;">
                <input type="text" id="pageSearchInput" class="form-control inside-shadow" placeholder="Search pages..."
                    style="padding-right:24px; margin-bottom:0;" />
                <span class="clear-search" data-target="#pageSearchInput"
                    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#94a3b8; display:none; font-weight:bold; font-size:14px; user-select:none; transition:color 0.2s;">✕</span>
            </div>
            <ul id="pageBox" class="menu-creator" style="height:200px!important; overflow:auto;">
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
        </div>

        <!-- Tags (closed) -->
        <h3 id="tagToggle"
            style="background:#eef2f6;color:#00609e;border:1px solid #e2e2e2;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;">
            Tags <span class="arrow">▶</span>
        </h3>
        <div id="tagBoxContainer"
            style="display:none; border:1px solid #e2e2e2; border-radius:4px; padding:8px; margin-bottom:10px;">
            <div style="position:relative; width:100%; margin-bottom:8px;">
                <input type="text" id="tagSearchInput" class="form-control inside-shadow" placeholder="Search tags..."
                    style="padding-right:24px; margin-bottom:0;" />
                <span class="clear-search" data-target="#tagSearchInput"
                    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#94a3b8; display:none; font-weight:bold; font-size:14px; user-select:none; transition:color 0.2s;">✕</span>
            </div>
            <ul id="tagBox" class="menu-creator" style="height:200px!important; overflow:auto;">
                <?php
                $sql = "SELECT tag_title, COUNT(*) as tag_count
                        FROM " . TABLE_PREFIX . "tag
                        WHERE tag_site_id='" . SITE_ID . "'
                        GROUP BY tag_title
                        ORDER BY tag_count DESC";
                if ($res = mysqli_query($connection, $sql)) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        $label = ucwords(htmlspecialchars($row['tag_title'], ENT_QUOTES));
                        $slug  = str_replace(' ', '-', $row['tag_title']);
                        $url   = '/tag/' . htmlspecialchars($slug, ENT_QUOTES) . '/';
                        $count = (int)$row['tag_count'];
                        echo "<li class='dd-new-item' data-label='{$label}' data-url='{$url}' style='cursor:pointer; display:flex; justify-content:space-between; align-items:center;'><span>{$label}</span> <span class='badge' style='background:#00609e; color:#fff; border-radius:10px; padding:2px 8px; font-size:11px;'>{$count}</span></li>";
                    }
                }
                ?>
            </ul>
        </div>

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
                    $menuList = [];
                    foreach ($defaults as $p) {
                        $key = strtolower(str_replace(' ', '_', $p));
                        $menuList[$key] = $p;
                    }

                    $sqlMenus = "SELECT site_configuration_key 
                                 FROM " . TABLE_PREFIX . "site_configuration 
                                 WHERE site_configuration_key LIKE 'site_nav_%' 
                                   AND site_configuration_site_id = '" . SITE_ID . "'";
                    if ($resMenus = mysqli_query($connection, $sqlMenus)) {
                        while ($rowMenu = mysqli_fetch_assoc($resMenus)) {
                            $key = str_replace('site_nav_', '', $rowMenu['site_configuration_key']);
                            if (!isset($menuList[$key])) {
                                $menuList[$key] = ucwords(str_replace('_', ' ', $key));
                            }
                        }
                    }

                    foreach ($menuList as $p) {
                        echo "<option value=\"" . htmlspecialchars($p, ENT_QUOTES) . "\">" . htmlspecialchars($p, ENT_QUOTES) . "</option>";
                    }
                    ?>
                    <option value="__NEW__">➕ New menu…</option>
                </select>
                <button id="renameMenuPosition" class="btn btn-sm btn-blue" style="display:none;" type="button"
                    title="Rename Menu">✎ Rename</button>
                <button id="deleteMenuPosition" class="btn btn-sm btn-red" style="display:none;" type="button"
                    title="Delete Menu">✕ Delete</button>
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