<?php

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
    if ($subscriberList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "subscription INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=subscription_user_id WHERE subscription_refer_by='{$cp_admin_user_id}' AND subscription_site_id=" . SITE_ID)) {
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

<style>
  .desk-page {
    background: #f8fafc;
    min-height: 85vh;
    padding: 40px 16px 80px;
  }

  .desk-container {
    max-width: 1200px;
    margin: 0 auto;
  }

  .desk-hero {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
  }

  .desk-hero h1 {
    font-size: 1.8rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
  }

  .desk-hero p {
    font-size: 0.95rem;
    color: #64748b;
    margin: 0;
  }

  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 24px;
  }

  .metric-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
  }

  .metric-lbl {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
    margin-bottom: 6px;
  }

  .metric-val {
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
  }

  .grid-two-col {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 24px;
  }

  .desk-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }

  .desk-card-head {
    padding: 18px 24px;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .desk-card-head h3 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
  }

  .pill-badge {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    background: #f1f5f9;
    color: #475569;
  }

  .desk-card-body {
    padding: 24px;
  }

  .form-group {
    margin-bottom: 16px;
  }

  .form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
  }

  .form-control {
    width: 100%;
    height: 44px;
    box-sizing: border-box;
    padding: 0 14px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 0.92rem;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease;
  }

  .form-control:focus {
    border-color: var(--accent-color, #d93025);
    box-shadow: 0 0 0 3px rgba(217, 48, 37, 0.15);
  }

  .btn-invite {
    width: 100%;
    height: 46px;
    background: var(--accent-color, #d93025);
    color: #ffffff;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(217, 48, 37, 0.25);
    transition: all 0.15s ease;
  }

  .btn-invite:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(217, 48, 37, 0.35);
  }

  .filter-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
  }

  .filter-bar input,
  .filter-bar select {
    height: 40px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0 12px;
    font-size: 0.88rem;
    color: #0f172a;
    outline: none;
  }

  .filter-bar input {
    flex: 1;
  }

  .table-responsive {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
  }

  .desk-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
  }

  .desk-table th {
    background: #f8fafc;
    padding: 12px 16px;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
  }

  .desk-table td {
    padding: 12px 16px;
    font-size: 0.9rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
  }

  .user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fee2e2;
    color: #dc2626;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
  }

  .status-tag {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
  }

  .status-tag.active {
    background: #dcfce7;
    color: #15803d;
  }

  .status-tag.inactive {
    background: #fee2e2;
    color: #dc2626;
  }

  .pagination-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    font-size: 0.85rem;
    color: #64748b;
  }

  .pager-btn {
    padding: 6px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #ffffff;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
  }

  .pager-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }

  /* FAQ Accordion Styling */
  .desk-faq-section {
    margin-top: 32px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
  }

  .desk-faq-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 20px;
  }

  .faq__box,
  .faq-accordion-item {
    border-bottom: 1px solid #f1f5f9;
    padding: 14px 0;
  }

  .faq__box:last-child,
  .faq-accordion-item:last-child {
    border-bottom: none;
  }

  .faq__box .question,
  .faq-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    user-select: none;
    transition: color 0.15s ease;
  }

  .faq__box .question:hover,
  .faq-header:hover {
    color: var(--accent-color, #d93025);
  }

  .faq-chevron,
  .faq-icon-chevron,
  .faq__box .question i {
    font-size: 1.2rem;
    color: #94a3b8;
    transition: transform 0.25s ease;
    display: inline-block;
  }

  .faq__box.active .faq-chevron,
  .faq__box.active .question i,
  .faq__box.is-open .faq-chevron,
  .faq-accordion-item.is-open .faq-icon-chevron {
    transform: rotate(180deg);
    color: var(--accent-color, #d93025);
  }

  .faq__box .answer,
  .faq-body {
    display: none;
    padding-top: 10px;
    font-size: 0.92rem;
    line-height: 1.6;
    color: #475569;
  }

  .faq__box.active .answer,
  .faq__box.is-open .answer,
  .faq-accordion-item.is-open .faq-body {
    display: block !important;
  }

  @media (max-width: 992px) {
    .grid-two-col { grid-template-columns: 1fr; }
    .metrics-grid { grid-template-columns: repeat(2, 1fr); }
  }

  @media (max-width: 576px) {
    .metrics-grid { grid-template-columns: 1fr; }
    .filter-bar { flex-direction: column; }
  }
</style>

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
          <div class="metric-val" style="color: #16a34a"><?php echo $cp_used; ?></div>
        </div>
        <div class="metric-card">
          <div class="metric-lbl">Available Seats</div>
          <div class="metric-val" style="color: #d93025"><?php echo max(0, $cp_limit - $cp_used); ?></div>
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
                      <td colspan="3" style="text-align: center; color: #94a3b8; padding: 24px;">No corporate subscribers allocated yet.</td>
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
            Subscription seats may be transferred to a new team member after 6 months from the original activation date. Write to <a href="mailto:info@theheadandtale.com" style="color: var(--accent-color, #d93025); font-weight: 700;">info@theheadandtale.com</a> with your order ID for seat transfers.
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

          if (prevBtn) prevBtn.onclick = () => { if (curPage > 1) { curPage--; render(); } };
          if (nextBtn) nextBtn.onclick = () => { const totalPages = Math.ceil(filtered.length / perPage); if (curPage < totalPages) { curPage++; render(); } };

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
      <div class="desk-card" style="text-align: center; padding: 60px 24px; max-width: 600px; margin: 40px auto;">
        <h2 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">No Active Corporate Plan</h2>
        <p style="color: #64748b; line-height: 1.6; margin-bottom: 24px;">You do not currently have an active corporate membership. Redirecting to corporate plans in <strong id="redirectNoSub">3</strong> seconds...</p>
        <a href="/corporate-subscription/" class="btn-invite" style="display: inline-flex; align-items: center; justify-content: center; width: auto; padding: 0 24px; text-decoration: none;">Explore Corporate Plans →</a>

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