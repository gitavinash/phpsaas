/**
 * V3 Employee Performance Report Module
 * Path: app/dashboard/v3/default/desktop/assets/js/report-employees.js
 * Handles fetching, filtering, date range updating, and rendering employee performance KPIs
 */
(function (factory) {
  if (typeof window.jQuery !== 'undefined') {
    factory(window.jQuery);
  } else {
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof window.jQuery !== 'undefined') {
        factory(window.jQuery);
      }
    });
  }
})(function ($) {
  'use strict';

  window.V3ReportEmployees = {
    employeesData: [],
    metricsData: null,
    dateRangeData: null,
    activePreset: 'last_30_days',
    customDateRange: '',
    currentTab: 'all',
    isLoading: false,
    isLoaded: false,
    activeXhr: null,
    searchDebounceTimer: null,
    eventsBound: false,

    init: function () {
      const $app = $('#employeesReportApp');
      if (!$app.length) {
        this.isLoaded = false;
        this.isLoading = false;
        return;
      }

      if (this.isLoading) return;

      this.initDatePickers();
      this.bindEvents();
      this.loadReportData();
    },

    initDatePickers: function () {
      const self = this;
      const $picker = $('#reportDateRangePicker');

      if ($picker.length && typeof $.fn.daterangepicker === 'function') {
        $picker.daterangepicker({
          autoUpdateInput: false,
          locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
          },
          opens: 'left'
        });

        $picker.on('apply.daterangepicker', function (ev, picker) {
          const val = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');
          $(this).val(val);
          self.customDateRange = val;
          self.activePreset = 'custom';
          $('.btn-date-preset').removeClass('active');
          self.loadReportData();
        });

        $picker.on('cancel.daterangepicker', function () {
          $(this).val('');
          self.customDateRange = '';
          self.activePreset = 'last_30_days';
          $('.btn-date-preset[data-preset="last_30_days"]').addClass('active');
          self.loadReportData();
        });
      }
    },

    bindEvents: function () {
      const self = this;
      if (this.eventsBound) return;
      this.eventsBound = true;

      // Date Range Preset Buttons
      $(document).off('click', '.btn-date-preset').on('click', '.btn-date-preset', function (e) {
        e.preventDefault();
        const preset = $(this).data('preset');
        if (preset === self.activePreset && preset !== 'custom') return;

        $('.btn-date-preset').removeClass('active');
        $(this).addClass('active');

        self.activePreset = preset;
        self.customDateRange = '';
        $('#reportDateRangePicker').val('');
        self.loadReportData();
      });

      // Filter Tabs (All, Top Performers, Active Pipeline, Administrators)
      $(document).off('click', '.report-tab-btn').on('click', '.report-tab-btn', function (e) {
        e.preventDefault();
        $('.report-tab-btn').removeClass('active');
        $(this).addClass('active');
        self.currentTab = $(this).data('tab') || 'all';
        self.renderTableRows();
      });

      // Search input with debounce
      $(document).off('input', '#employeeReportSearch').on('input', '#employeeReportSearch', function () {
        const val = $(this).val();
        if (val && val.trim().length > 0) {
          $('#clearEmployeeSearchBtn').show();
        } else {
          $('#clearEmployeeSearchBtn').hide();
        }

        clearTimeout(self.searchDebounceTimer);
        self.searchDebounceTimer = setTimeout(function () {
          self.renderTableRows();
        }, 200);
      });

      // Clear search button
      $(document).off('click', '#clearEmployeeSearchBtn').on('click', '#clearEmployeeSearchBtn', function () {
        $('#employeeReportSearch').val('');
        $(this).hide();
        self.renderTableRows();
      });

      // Role filter dropdown
      $(document).off('change', '#filterEmployeeRole').on('change', '#filterEmployeeRole', function () {
        self.renderTableRows();
      });

      // Sort dropdown
      $(document).off('change', '#sortEmployeeReport').on('change', '#sortEmployeeReport', function () {
        self.renderTableRows();
      });

      // Refresh button
      $(document).off('click', '#btnRefreshEmployeeReport').on('click', '#btnRefreshEmployeeReport', function (e) {
        e.preventDefault();
        const $icon = $(this).find('i');
        $icon.addClass('fa-spin fi-spin');
        self.loadReportData(function () {
          $icon.removeClass('fa-spin fi-spin');
        });
      });

      // Retry button click
      $(document).off('click', '.retryReportBtn').on('click', '.retryReportBtn', function (e) {
        e.preventDefault();
        self.loadReportData();
      });
    },

    loadReportData: function (callback) {
      const self = this;

      if (this.activeXhr && typeof this.activeXhr.abort === 'function') {
        this.activeXhr.abort();
        this.activeXhr = null;
      }

      this.isLoading = true;

      const $tbody = $('#employeesReportTableBody');
      $tbody.html(`
        <tr>
          <td colspan="7" class="table-loading-state" style="padding: 56px 20px; text-align: center; color: #64748b;">
            <div class="table-loading-spinner" style="margin: 0 auto 12px;"></div>
            <span style="font-size: 14px; font-weight: 500; display: block;">Aggregating team performance KPIs...</span>
          </td>
        </tr>
      `);

      const queryParams = {};
      if (this.activePreset && this.activePreset !== 'custom') {
        queryParams.preset = this.activePreset;
      }
      if (this.customDateRange) {
        queryParams.daterange = this.customDateRange;
      }

      this.activeXhr = $.ajax({
        url: '/api/v3/reports/employees/',
        type: 'GET',
        data: queryParams,
        dataType: 'json',
        success: function (res) {
          self.isLoading = false;
          self.isLoaded = true;
          self.activeXhr = null;

          if (res && res.status === 'success') {
            self.employeesData = res.employees || [];
            self.metricsData = res.metrics || {};
            self.dateRangeData = res.date_range || {};

            self.renderDateRangeHeader();
            self.renderGlobalKpis();
            self.renderTabBadges();
            self.renderTableRows();
          } else {
            const msg = (res && res.msg) ? res.msg : 'Failed to fetch employee performance report.';
            self.renderErrorState(msg);
          }

          if (typeof callback === 'function') callback();
        },
        error: function (xhr, statusText) {
          if (statusText === 'abort') return;
          self.isLoading = false;
          self.activeXhr = null;
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Server error fetching performance report.';
          self.renderErrorState(msg);
          if (typeof callback === 'function') callback();
        }
      });
    },

    renderDateRangeHeader: function () {
      const d = this.dateRangeData || {};
      const label = d.display || 'Last 30 days';
      $('#reportActivePeriodLabel').text(label);
    },

    renderGlobalKpis: function () {
      const m = this.metricsData || {};

      $('#statTotalEmployees').text(m.total_employees !== undefined ? m.total_employees : '-');
      $('#statTotalAssignedLeads').text(m.total_leads !== undefined ? m.total_leads.toLocaleString() : '-');
      $('#statTotalConfirmedLeads').text(m.total_confirmed !== undefined ? m.total_confirmed.toLocaleString() : '-');
      $('#statTotalPipelineLeads').text(m.total_pipeline !== undefined ? m.total_pipeline.toLocaleString() : '-');

      const rate = m.overall_conversion_rate !== undefined ? m.overall_conversion_rate + '%' : '0.0%';
      $('#statConversionRate').text(rate);

      if (m.top_performer && m.top_performer.name) {
        $('#statTopPerformerName').text(m.top_performer.name);
        $('#statTopPerformerSub').text(`${m.top_performer.confirmed_leads} Won (${m.top_performer.conversion_rate}%)`);
        $('#statTopPerformerCard').show();
      } else {
        $('#statTopPerformerCard').hide();
      }
    },

    renderTabBadges: function () {
      const all = this.employeesData || [];
      const totalCount = all.length;
      const topCount = all.filter(e => e.confirmed_leads > 0).length;
      const pipelineCount = all.filter(e => e.pipeline_leads > 0).length;
      const adminCount = all.filter(e => e.role === 'administrator').length;

      $('#badgeTabAll').text(totalCount);
      $('#badgeTabTop').text(topCount);
      $('#badgeTabPipeline').text(pipelineCount);
      $('#badgeTabAdmin').text(adminCount);
    },

    renderTableRows: function () {
      const $tbody = $('#employeesReportTableBody');
      let list = (this.employeesData || []).slice();

      const search = ($('#employeeReportSearch').val() || '').toLowerCase().trim();
      const roleFilter = ($('#filterEmployeeRole').val() || '').toLowerCase().trim();
      const sortBy = $('#sortEmployeeReport').val() || 'confirmed';

      // 1. Filter by Tab
      if (this.currentTab === 'top') {
        list = list.filter(e => e.confirmed_leads > 0);
      } else if (this.currentTab === 'pipeline') {
        list = list.filter(e => e.pipeline_leads > 0);
      } else if (this.currentTab === 'admin') {
        list = list.filter(e => e.role === 'administrator');
      }

      // 2. Filter by Role dropdown
      if (roleFilter) {
        list = list.filter(e => e.role === roleFilter);
      }

      // 3. Filter by Search input
      if (search) {
        list = list.filter(function (e) {
          const haystack = (e.name + ' ' + (e.email || '') + ' ' + (e.mobile || '') + ' ' + e.role_label).toLowerCase();
          return haystack.indexOf(search) !== -1;
        });
      }

      // 4. Sort
      list.sort(function (a, b) {
        if (sortBy === 'leads') {
          return b.total_leads - a.total_leads;
        } else if (sortBy === 'conversion') {
          return b.conversion_rate - a.conversion_rate;
        } else if (sortBy === 'pipeline') {
          return b.pipeline_leads - a.pipeline_leads;
        } else if (sortBy === 'name') {
          return a.name.localeCompare(b.name);
        } else {
          // Default: confirmed leads desc
          if (b.confirmed_leads === a.confirmed_leads) {
            return b.total_leads - a.total_leads;
          }
          return b.confirmed_leads - a.confirmed_leads;
        }
      });

      if (list.length === 0) {
        $tbody.html(`
          <tr>
            <td colspan="7" style="padding: 0; border: none;">
              <div class="transporters-empty-state" style="padding: 48px 20px;">
                <div class="transporters-empty-icon-wrap" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); color: #6366f1;">
                  <i class="fi fi-rr-users"></i>
                </div>
                <h4 class="transporters-empty-title">No Team Performance Data Found</h4>
                <p class="transporters-empty-desc">No employees matched your selected date period or filter criteria.</p>
              </div>
            </td>
          </tr>
        `);
        return;
      }

      let html = '';
      list.forEach(function (emp, index) {
        const initials = emp.name.split(' ').map(n => n[0]).filter(Boolean).slice(0, 2).join('').toUpperCase() || 'EM';

        // Role badge styling
        let roleBadgeClass = 'role-agent';
        if (emp.role === 'administrator') {
          roleBadgeClass = 'role-admin';
        } else if (emp.role === 'operations') {
          roleBadgeClass = 'role-ops';
        }

        // Conversion progress bar color
        let progressColor = '#6366f1';
        let conversionBadgeClass = 'rate-neutral';
        if (emp.conversion_rate >= 10 || emp.confirmed_leads >= 5) {
          progressColor = '#10b981';
          conversionBadgeClass = 'rate-high';
        } else if (emp.conversion_rate >= 3 || emp.confirmed_leads >= 1) {
          progressColor = '#3b82f6';
          conversionBadgeClass = 'rate-medium';
        } else if (emp.pipeline_leads > 0) {
          progressColor = '#f59e0b';
          conversionBadgeClass = 'rate-pipeline';
        }

        const barWidth = Math.min(Math.max(emp.conversion_rate, emp.total_leads > 0 ? 4 : 0), 100);

        html += `
          <tr class="employee-perf-row" data-id="${emp.id}">
            <!-- Rank & Employee Profile -->
            <td>
              <div style="display: flex; align-items: center; gap: 10px;">
                <div class="emp-avatar-badge ${emp.confirmed_leads > 0 ? 'top-converter' : ''}">
                  ${initials}
                </div>
                <div style="min-width: 0; flex: 1;">
                  <div class="emp-name-row">
                    <span class="emp-name" title="${emp.name}">${emp.name}</span>
                    <span class="emp-role-pill ${roleBadgeClass}">${emp.role_label}</span>
                  </div>
                  <div class="emp-contact-sub">
                    ${emp.email ? `<span class="emp-contact-email" title="${emp.email}"><i class="fi fi-rr-envelope"></i> ${emp.email}</span>` : ''}
                    ${emp.mobile ? `<span class="emp-contact-phone" title="${emp.mobile}"><i class="fi fi-rr-phone-call"></i> ${emp.mobile}</span>` : ''}
                  </div>
                </div>
              </div>
            </td>

            <!-- Total Assigned Leads -->
            <td style="text-align: center;">
              <a href="${emp.links.all}" class="emp-stat-pill total" title="View all assigned leads for ${emp.name}">
                <span>${emp.total_leads.toLocaleString()}</span>
                <i class="fi fi-rr-arrow-up-right"></i>
              </a>
            </td>

            <!-- Confirmed (Won) Leads -->
            <td style="text-align: center;">
              ${emp.confirmed_leads > 0 ? `
                <a href="${emp.links.confirmed}" class="emp-stat-pill confirmed" title="View confirmed leads">
                  <i class="fi fi-rr-check-circle"></i>
                  <span>${emp.confirmed_leads.toLocaleString()}</span>
                </a>
              ` : '<span class="emp-stat-zero">-</span>'}
            </td>

            <!-- Active Pipeline (Assigned, Follow-ups, Itineraries, Payment Requests) -->
            <td style="text-align: center;">
              ${(emp.pipeline_leads > 0) ? `
                <div class="emp-pipeline-wrap">
                  ${(emp.assigned_leads > 0) ? `
                    <a href="${emp.links.assigned || emp.links.pending}" class="emp-pipeline-chip assigned" title="${emp.assigned_leads} Assigned Leads">
                      <span class="dot"></span> ${emp.assigned_leads} Assigned
                    </a>
                  ` : (emp.pending_leads > 0 && !emp.assigned_leads ? `
                    <a href="${emp.links.pending}" class="emp-pipeline-chip assigned" title="${emp.pending_leads} Assigned Leads">
                      <span class="dot"></span> ${emp.pending_leads} Assigned
                    </a>
                  ` : '')}
                  ${emp.followup_leads > 0 ? `
                    <a href="${emp.links.followup}" class="emp-pipeline-chip followup" title="${emp.followup_leads} Follow-ups">
                      <span class="dot"></span> ${emp.followup_leads} Follow-up
                    </a>
                  ` : ''}
                  ${emp.itinerary_leads > 0 ? `
                    <a href="${emp.links.itinerary}" class="emp-pipeline-chip itinerary" title="${emp.itinerary_leads} Itinerary Sent">
                      <span class="dot"></span> ${emp.itinerary_leads} Itinerary
                    </a>
                  ` : ''}
                  ${emp.payment_requested_leads > 0 ? `
                    <a href="${emp.links.payment_requested}" class="emp-pipeline-chip payment" title="${emp.payment_requested_leads} Payment Requested">
                      <span class="dot"></span> ${emp.payment_requested_leads} Payment Req
                    </a>
                  ` : ''}
                </div>
              ` : '<span class="emp-stat-zero">-</span>'}
            </td>

            <!-- Lost / Cancelled Leads -->
            <td style="text-align: center;">
              ${emp.lost_leads > 0 ? `
                <a href="${emp.links.lost}" class="emp-stat-pill lost" title="View cancelled / lost leads">
                  <span>${emp.lost_leads.toLocaleString()}</span>
                </a>
              ` : '<span class="emp-stat-zero">-</span>'}
            </td>

            <!-- Conversion Rate & Progress -->
            <td>
              ${emp.total_leads > 0 ? `
                <div class="emp-conversion-cell">
                  <div class="emp-conversion-header">
                    <span class="conversion-pct ${conversionBadgeClass}">${emp.conversion_rate}%</span>
                    <span class="conversion-tier-text">${emp.performance_tier}</span>
                  </div>
                  <div class="emp-progress-track">
                    <div class="emp-progress-fill" style="width: ${barWidth}%; background-color: ${progressColor};"></div>
                  </div>
                </div>
              ` : '<span class="emp-stat-zero">-</span>'}
            </td>

            <!-- Quick Action CTA -->
            <td style="text-align: right;">
              <a href="${emp.links.all}" class="btn-v3-action emp-btn-view" title="Explore leads handled by ${emp.name}">
                <i class="fi fi-rr-eye"></i> View
              </a>
            </td>
          </tr>
        `;
      });

      $tbody.html(html);
    },

    renderErrorState: function (msg) {
      $('#employeesReportTableBody').html(`
        <tr>
          <td colspan="7" style="padding: 0; border: none;">
            <div class="transporters-empty-state" style="padding: 48px 20px;">
              <div class="transporters-empty-icon-wrap" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-color: #fca5a5; color: #ef4444;">
                <i class="fi fi-rr-cross-circle"></i>
              </div>
              <h4 class="transporters-empty-title">Failed to load performance report</h4>
              <p class="transporters-empty-desc">${msg}</p>
              <button type="button" class="transporters-empty-action retryReportBtn" style="cursor: pointer;">
                <i class="fi fi-rr-refresh"></i> Retry Loading
              </button>
            </div>
          </td>
        </tr>
      `);
    }
  };
});
