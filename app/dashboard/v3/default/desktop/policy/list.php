<h2 class="heading">Policies list<span>All Policies list will be displayed here</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/policy/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Published on</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($supplierList = mysqli_query($connection, "SELECT page_id, page_title, link_url, link_status, page_type, page_status, link_published FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=page_id WHERE link_status=200 AND link_type='page' AND page_type='policy' AND link_site_id=" . SITE_ID)) {
                    echo "<tbody>";
                    if (mysqli_num_rows($supplierList) > 0) {
                        while ($supplierData = mysqli_fetch_assoc($supplierList)) {
                            extract($supplierData);
                            echo "<tr>
                                <td>{$page_title}</td>
                                <td>" . date('d-m-Y H:i:s', strtotime($link_published)) . "</td>
                                <td>{$page_status}</td>
                                <td>
                                <a href='/dashboard/policy/update/?id=" . base64_encode($page_id) . "' class='tableActionIconBtn' data-title='Edit policy' ><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='{$link_url}' class='tableActionIconBtn' data-title='Edit policy' target='_blank' ><i class='fi fi-rr-eye btn-blue'></i></a>
                                <a href='#' data-type='policy' data-id='{$page_id}' data-title='Delete policy' class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
                                </td>
                            </tr>";
                        }
                    }
                    echo "</tbody>";
                }

                ?>
            </table>
        </div>
    </section>
</div>