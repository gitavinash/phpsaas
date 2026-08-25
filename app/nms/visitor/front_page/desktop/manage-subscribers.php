<?php
echo "Updating..."
exit();
require_once LOGIN_CHECK;

$subscriberListCount = 0;
$subscribers = [];
$currentPage = 1;
$totalPages = 1;

if ($checkSubscription = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "corporate_subscription WHERE cp_admin_user_id=" . USER_ID . " AND cp_site_id=" . SITE_ID)) {
  if (mysqli_num_rows($checkSubscription) != 0) {
    $subscriptionStatus = 'active';
    extract(mysqli_fetch_assoc($checkSubscription));
    $subscriberListFound = false;
    if ($subscriberList = mysqli_query($connection, "SELECT profile_display_name, profile_email, subscription_status FROM " . TABLE_PREFIX . "subscription INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=subscription_user_id WHERE subscription_refer_by='" . USER_ID . "' AND subscription_site_id=" . SITE_ID)) {
      if (mysqli_num_rows($subscriberList) != 0) {
        $subscriberListFound = true;
        $subscriberListCount = mysqli_num_rows($subscriberList);
        while ($subRow = mysqli_fetch_assoc($subscriberList)) {
          $subscribers[] = $subRow;
        }

        $totalPages = ceil($subscriberListCount / 10);
      }
    }
  } else {
    $subscriptionStatus = 'inactive';
  }
}

require_once 'header.php';
?>

<div class="desk-page">
  <div class="desk-container">
    <?php if ($subscriptionStatus == 'active'): ?>
      <div class="desk-hero">
        <div>
          <h1>Corporate Subscriber Desk</h1>
          <p>Allocate team seats, manage sub-accounts, and view license utilization.</p>
        </div>
      </div>

      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-lbl">Expiry Date</div>
          <div class="metric-val"><?php echo date('M d, Y', strtotime($cp_expiry)); ?></div>
        </div>
        <div class="metric-card">
          <div class="metric-lbl">Total Licenses</div>
          <div class="metric-val"><?php echo $cp_limit; ?></div>
        </div>
        <div class="metric-card">
          <div class="metric-lbl">Active Seats</div>
          <div class="metric-val text-success"><?php echo $cp_used; ?></div>
        </div>
        <div class="metric-card">
          <div class="metric-lbl">Available Seats</div>
          <div class="metric-val text-danger"><?php echo max(0, $cp_limit - $cp_used); ?></div>
        </div>
        <div class="metric-card">
          <div class="metric-lbl">Utilization</div>
          <div class="metric-val">
            <?php
            $util = $cp_limit > 0 ? round(($cp_used / $cp_limit) * 100) : 0;
            echo $util . '%';
            ?>
          </div>
        </div>
      </div>

      <div class="grid-two-col">
        <!-- Add Subscriber Card -->
        <div class="desk-card">
          <div class="desk-card-head">
            <h3>Add Teammate</h3>
            <span class="pill-badge"><?php echo max(0, $cp_limit - $cp_used); ?> Seats Left</span>
          </div>
          <div class="desk-card-body">
            <form id="subscriberForm" method="POST" action="/add-new-corporate-subscriber/">
              <div class="form-group">
                <label for="subscriber_name">Full Name</label>
                <input type="text" id="subscriber_name" name="name" class="form-control" placeholder="Jane Doe" required>
              </div>
              <div class="form-group">
                <label for="subscriber_email">Work Email</label>
                <input type="email" id="subscriber_email" name="email" class="form-control" placeholder="jane@company.com" required>
              </div>
              <button type="submit" class="btn-invite" id="inviteBtn">Send License Invite →</button>
            </form>
          </div>
        </div>

        <!-- Directory Card -->
        <div class="desk-card">
          <div class="desk-card-head">
            <h3>Subscribers Directory</h3>
            <span class="pill-badge">Total <?php echo $subscriberListCount; ?></span>
          </div>
          <div class="desk-card-body">
            <div class="filter-bar">
              <input type="search" id="subscriberSearch" placeholder="Search name or email...">
              <select id="statusFilter">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>

            <div class="table-responsive">
              <table class="desk-table">
                <thead>
                  <tr>
                    <th>Subscriber Name</th>
                    <th>Work Email</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="subsTbody">
                  <?php if (!empty($subscribers)): ?>
                    <?php foreach ($subscribers as $sub):
                      $st = ($sub['subscription_status'] == '1') ? 'active' : 'inactive';
                    ?>
                      <tr data-name="<?php echo strtolower(htmlspecialchars($sub['profile_display_name'])); ?>" data-email="<?php echo strtolower(htmlspecialchars($sub['profile_email'])); ?>" data-status="<?php echo $st; ?>">
                        <td>
                          <span class="user-avatar"><?php echo strtoupper(substr($sub['profile_display_name'], 0, 1)); ?></span>
                          <strong><?php echo htmlspecialchars($sub['profile_display_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($sub['profile_email']); ?></td>
                        <td><span class="status-tag <?php echo $st; ?>"><?php echo ucfirst($st); ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="3" class="empty-table-cell">No corporate subscribers allocated yet.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <div class="pagination-bar">
              <span id="pageInfo">Showing subscribers</span>
              <div>
                <button class="pager-btn" id="prevBtn" disabled>← Prev</button>
                <button class="pager-btn" id="nextBtn" disabled>Next →</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Corporate Terms & FAQ Accordion -->
      <div class="desk-faq-section">
        <h3 class="desk-faq-title">Corporate Desk FAQ & Policy Guidelines</h3>

        <div class="faq__box">
          <div class="question">
            <span>How do I allocate seat licenses to my team?</span>
            <span class="faq-chevron">▾</span>
          </div>
          <div class="answer">
            Enter your teammate's full name and corporate work email address into the "Add Teammate" form above. Upon submission, an invitation link with instant access will be sent directly to their email.
          </div>
        </div>

        <div class="faq__box">
          <div class="question">
            <span>What are the device login rules for corporate seats?</span>
            <span class="faq-chevron">▾</span>
          </div>
          <div class="answer">
            Each corporate seat allows concurrent login access on up to 2 active personal or office devices. Logging in from a 3rd device triggers a temporary 48-hour security pause to protect corporate credentials.
          </div>
        </div>

        <div class="faq__box">
          <div class="question">
            <span>Can I reassign or transfer a seat to a new employee?</span>
            <span class="faq-chevron">▾</span>
          </div>
          <div class="answer">
            Subscription seats may be transferred to a new team member after 6 months from the original activation date. Write to <a href="mailto:info@theheadandtale.com" class="accent-link">info@theheadandtale.com</a> with your order ID for seat transfers.
          </div>
        </div>

        <div class="faq__box">
          <div class="question">
            <span>How do I obtain a GST Tax Invoice?</span>
            <span class="faq-chevron">▾</span>
          </div>
          <div class="answer">
            GST tax invoices are automatically generated and emailed to your registered billing email within 48 hours of payment completion. You can also request duplicate copies by emailing our billing desk.
          </div>
        </div>
      </div>

      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Table filtering & pagination
          const searchInput = document.getElementById('subscriberSearch');
          const statusFilter = document.getElementById('statusFilter');
          const tbody = document.getElementById('subsTbody');
          const prevBtn = document.getElementById('prevBtn');
          const nextBtn = document.getElementById('nextBtn');
          const pageInfo = document.getElementById('pageInfo');

          if (tbody) {
            const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
            let filtered = [...rows];
            let curPage = 1;
            const perPage = 5;

            function applyFilter() {
              const q = searchInput ? searchInput.value.toLowerCase().trim() : '';
              const st = statusFilter ? statusFilter.value : '';

              filtered = rows.filter(r => {
                const name = r.dataset.name || '';
                const email = r.dataset.email || '';
                const status = r.dataset.status || '';

                const mQ = !q || name.includes(q) || email.includes(q);
                const mS = !st || status === st;
                return mQ && mS;
              });

              curPage = 1;
              render();
            }

            function render() {
              const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
              rows.forEach(r => r.style.display = 'none');

              const start = (curPage - 1) * perPage;
              const end = start + perPage;
              filtered.slice(start, end).forEach(r => r.style.display = '');

              if (pageInfo) pageInfo.textContent = `Page ${curPage} of ${totalPages} (${filtered.length} items)`;
              if (prevBtn) prevBtn.disabled = curPage <= 1;
              if (nextBtn) nextBtn.disabled = curPage >= totalPages;
            }

            if (prevBtn) prevBtn.onclick = () => {
              if (curPage > 1) {
                curPage--;
                render();
              }
            };
            if (nextBtn) nextBtn.onclick = () => {
              const totalPages = Math.ceil(filtered.length / perPage);
              if (curPage < totalPages) {
                curPage++;
                render();
              }
            };

            if (searchInput) searchInput.addEventListener('input', applyFilter);
            if (statusFilter) statusFilter.addEventListener('change', applyFilter);

            applyFilter();
          }

          // FAQ Accordion click handler
          const faqBoxes = document.querySelectorAll('.faq__box, .faq-accordion-item, .subscribe-faq-item');
          faqBoxes.forEach(function(box) {
            const trigger = box.querySelector('.question, .faq-header, .subscribe-faq-question');
            if (trigger) {
              trigger.style.cursor = 'pointer';
              trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const isOpen = box.classList.contains('active') || box.classList.contains('is-open');

                // Toggle current box
                if (isOpen) {
                  box.classList.remove('active', 'is-open');
                } else {
                  box.classList.add('active', 'is-open');
                }

                const answer = box.querySelector('.answer, .faq-body, .subscribe-faq-answer');
                if (answer) {
                  answer.style.display = isOpen ? 'none' : 'block';
                }
              });
            }
          });
        });
      </script>

    <?php else: ?>
      <div class="desk-card corp-no-sub-card">
        <h2 class="corp-no-sub-title">No Active Corporate Plan</h2>
        <p class="corp-no-sub-desc">You do not currently have an active corporate membership. Redirecting to corporate plans in <strong id="redirectNoSub">3</strong> seconds...</p>
        <a href="/corporate-subscription/" class="btn-invite corp-no-sub-btn">Explore Corporate Plans →</a>

        <script>
          let s = 3;
          setInterval(function() {
            s--;
            const el = document.getElementById('redirectNoSub');
            if (el) el.textContent = s;
            if (s <= 0) window.location.href = '/corporate-subscription/';
          }, 1000);
        </script>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once 'footer.php'; ?>