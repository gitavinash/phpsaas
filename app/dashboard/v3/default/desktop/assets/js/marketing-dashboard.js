/**
 * V3 Digital Marketing Dashboard Controller
 * File: app/dashboard/v3/default/desktop/assets/js/marketing-dashboard.js
 * Workspace Rule Compliant: External JS, REST API integration, SPA asynchronous state, Chart.js lifecycle.
 */

(function ($) {
  "use strict";

  const MarketingDashboard = {
    timelineChart: null,
    sourceChart: null,
    currentDateRange: "",

    init: function () {
      const $root = $("#digitalMarketingDashboardApp");
      if (!$root.length) return;

      this.currentDateRange = $("#mktDaterangeInput").val() || "";
      this.bindEvents();
      this.fetchData(this.currentDateRange);
    },

    bindEvents: function () {
      const self = this;

      // Refresh Button
      $(document).off("click.mktRefresh").on("click.mktRefresh", "#mktRefreshBtn", function (e) {
        e.preventDefault();
        const $btn = $(this);
        $btn.addClass("is-loading");
        self.fetchData(self.currentDateRange, function () {
          $btn.removeClass("is-loading");
        });
      });

      // Copy Webhook Endpoints
      $(document).off("click.mktCopy").on("click.mktCopy", ".mkt-btn-copy-endpoint", function (e) {
        e.preventDefault();
        const textToCopy = $(this).attr("data-copy");
        if (!textToCopy) return;

        const $btn = $(this);
        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(textToCopy).then(function () {
            self.showCopySuccess($btn);
          });
        } else {
          // Fallback
          const $temp = $("<input>");
          $("body").append($temp);
          $temp.val(textToCopy).select();
          document.execCommand("copy");
          $temp.remove();
          self.showCopySuccess($btn);
        }
      });

      // Quick Date Range Filter Submission (Prevent full page reload, SPA behavior)
      $(document).off("submit.mktDateForm").on("submit.mktDateForm", "#mktDateRangeForm", function (e) {
        e.preventDefault();
        const selectedRange = $("#mktDaterangeInput").val();
        self.currentDateRange = selectedRange;
        self.fetchData(selectedRange);
      });

      // Date Range Picker Change Trigger
      $("#mktDaterangeInput").on("apply.daterangepicker", function (ev, picker) {
        const val = picker.startDate.format("DD-MMM-YYYY") + " - " + picker.endDate.format("DD-MMM-YYYY");
        $(this).val(val);
        self.currentDateRange = val;
        self.fetchData(val);
      });
    },

    showCopySuccess: function ($btn) {
      const originalHtml = $btn.html();
      $btn.html('<i class="fi fi-rr-check" style="color:#10b981;"></i>');
      setTimeout(function () {
        $btn.html(originalHtml);
      }, 1800);
    },

    fetchData: function (daterange, callback) {
      const self = this;
      const apiUrl = "/api/v3/dashboard/marketing.php" + (daterange ? "?daterange=" + encodeURIComponent(daterange) : "");

      $.ajax({
        url: apiUrl,
        type: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            self.renderKPIs(res.kpis);
            self.renderCharts(res.charts);
            self.renderRecentInquiries(res.recentInquiries);
            self.renderIntegrations(res.integrations);
          }
          if (typeof callback === "function") callback();
        },
        error: function (err) {
          console.error("Failed to load marketing dashboard data", err);
          if (typeof callback === "function") callback();
        }
      });
    },

    animateValue: function (id, start, end, duration) {
      const obj = document.getElementById(id);
      if (!obj) return;
      if (start === end) {
        obj.textContent = end.toLocaleString();
        return;
      }
      const range = end - start;
      const minTimer = 50;
      let stepTime = Math.abs(Math.floor(duration / range));
      stepTime = Math.max(stepTime, minTimer);
      const startTime = new Date().getTime();
      const endTime = startTime + duration;
      let timer;

      function run() {
        const now = new Date().getTime();
        const remaining = Math.max((endTime - now) / duration, 0);
        const value = Math.round(end - remaining * range);
        obj.textContent = value.toLocaleString();
        if (value === end) {
          clearInterval(timer);
        }
      }

      timer = setInterval(run, stepTime);
      run();
    },

    renderKPIs: function (kpis) {
      if (!kpis) return;

      const currentTotal = parseInt($("#mktValTotal").text().replace(/,/g, "")) || 0;
      const currentFb = parseInt($("#mktValFb").text().replace(/,/g, "")) || 0;
      const currentGoogle = parseInt($("#mktValGoogle").text().replace(/,/g, "")) || 0;
      const currentForms = parseInt($("#mktValForms").text().replace(/,/g, "")) || 0;

      this.animateValue("mktValTotal", currentTotal, kpis.totalLeads || 0, 600);
      this.animateValue("mktValFb", currentFb, kpis.facebookLeads || 0, 600);
      this.animateValue("mktValGoogle", currentGoogle, kpis.googleLeads || 0, 600);
      this.animateValue("mktValForms", currentForms, kpis.activeFbForms || 0, 600);

      if (kpis.dateRangeLabel) {
        $("#mktDateDurationTag").text(kpis.dateRangeLabel);
      }
    },

    renderCharts: function (charts) {
      if (!charts) return;

      // 1. Timeline Line Chart
      const timelineCanvas = document.getElementById("mktTimelineCanvas");
      if (timelineCanvas && window.Chart && charts.timeline) {
        const ctx = timelineCanvas.getContext("2d");

        if (this.timelineChart) {
          this.timelineChart.destroy();
        }

        const gradientFb = ctx.createLinearGradient(0, 0, 0, 240);
        gradientFb.addColorStop(0, "rgba(24, 119, 242, 0.2)");
        gradientFb.addColorStop(1, "rgba(24, 119, 242, 0.0)");

        const gradientGg = ctx.createLinearGradient(0, 0, 0, 240);
        gradientGg.addColorStop(0, "rgba(245, 158, 11, 0.2)");
        gradientGg.addColorStop(1, "rgba(245, 158, 11, 0.0)");

        this.timelineChart = new Chart(ctx, {
          type: "line",
          data: {
            labels: charts.timeline.labels || [],
            datasets: [
              {
                label: "Facebook Leads",
                data: charts.timeline.facebook || [],
                borderColor: "#1877f2",
                backgroundColor: gradientFb,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: "#1877f2",
                fill: true,
                tension: 0.35
              },
              {
                label: "Google Leads",
                data: charts.timeline.google || [],
                borderColor: "#f59e0b",
                backgroundColor: gradientGg,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: "#f59e0b",
                fill: true,
                tension: 0.35
              },
              {
                label: "Total Captured",
                data: charts.timeline.total || [],
                borderColor: "#6366f1",
                borderWidth: 2,
                borderDash: [4, 4],
                pointRadius: 0,
                fill: false,
                tension: 0.35
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
              mode: "index",
              intersect: false
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: "#0f172a",
                titleColor: "#ffffff",
                bodyColor: "#f1f5f9",
                padding: 10,
                cornerRadius: 8,
                boxPadding: 4
              }
            },
            scales: {
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  color: "#94a3b8",
                  font: { size: 11 }
                }
              },
              y: {
                beginAtZero: true,
                grid: {
                  color: "rgba(226, 232, 240, 0.6)"
                },
                ticks: {
                  color: "#94a3b8",
                  font: { size: 11 },
                  stepSize: 1
                }
              }
            }
          }
        });
      }

      // 2. Channel Distribution Doughnut Chart & Bars
      const distCanvas = document.getElementById("mktSourceCanvas");
      if (distCanvas && window.Chart && charts.channelDistribution) {
        const ctxDist = distCanvas.getContext("2d");

        if (this.sourceChart) {
          this.sourceChart.destroy();
        }

        const colors = ["#1877f2", "#f59e0b", "#10b981", "#8b5cf6", "#ec4899", "#64748b"];

        this.sourceChart = new Chart(ctxDist, {
          type: "doughnut",
          data: {
            labels: charts.channelDistribution.labels || [],
            datasets: [
              {
                data: charts.channelDistribution.data || [],
                backgroundColor: colors.slice(0, charts.channelDistribution.labels.length),
                borderWidth: 2,
                borderColor: "#ffffff",
                hoverOffset: 4
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: "bottom",
                labels: {
                  boxWidth: 10,
                  font: { size: 11.5, weight: 600 },
                  color: "#475569"
                }
              },
              tooltip: {
                backgroundColor: "#0f172a",
                titleColor: "#ffffff",
                bodyColor: "#f1f5f9",
                cornerRadius: 8,
                padding: 10
              }
            },
            cutout: "68%"
          }
        });

        // Render Bars list
        const $barsList = $("#mktSourceBarsList");
        $barsList.empty();

        if (charts.channelDistribution.bars && charts.channelDistribution.bars.length > 0) {
          charts.channelDistribution.bars.forEach(function (bar) {
            let fillClass = "other";
            const low = bar.source.toLowerCase();
            if (low.includes("facebook") || low.includes("fb")) fillClass = "fb";
            else if (low.includes("google")) fillClass = "google";
            else if (low.includes("whatsapp")) fillClass = "whatsapp";

            const rowHtml =
              '<div class="mkt-source-bar-row">' +
              '  <div class="mkt-source-bar-header">' +
              '    <span class="mkt-source-name">' + bar.source + '</span>' +
              '    <span class="mkt-source-val">' + bar.count + ' (' + bar.percentage + '%)</span>' +
              '  </div>' +
              '  <div class="mkt-bar-track">' +
              '    <div class="mkt-bar-fill ' + fillClass + '" style="width: ' + bar.percentage + '%;"></div>' +
              '  </div>' +
              '</div>';

            $barsList.append(rowHtml);
          });
        }
      }
    },

    renderRecentInquiries: function (inquiries) {
      const $tbody = $("#mktInquiriesTbody");
      $tbody.empty();

      if (!inquiries || inquiries.length === 0) {
        $tbody.append(
          '<tr><td colspan="5" class="mkt-empty-row-cell">' +
          '<i class="fi fi-rr-bullseye-arrow mkt-empty-row-icon"></i>' +
          'No campaign inquiries recorded in this period.' +
          '</td></tr>'
        );
        return;
      }

      inquiries.forEach(function (item) {
        let tagClass = "other";
        if (item.source.includes("Facebook") || item.source.includes("IG")) tagClass = "fb";
        else if (item.source.includes("Google")) tagClass = "google";

        const initial = (item.name ? item.name.charAt(0) : "P").toUpperCase();

        const row =
          '<tr>' +
          '  <td>' +
          '    <div class="mkt-lead-profile-cell">' +
          '      <div class="mkt-lead-avatar">' + initial + '</div>' +
          '      <div class="mkt-lead-text">' +
          '        <h4>' + item.name + '</h4>' +
          '        <span>' + item.lead_tag + '</span>' +
          '      </div>' +
          '    </div>' +
          '  </td>' +
          '  <td>' +
          '    <span class="mkt-source-tag ' + tagClass + '">' +
          '      <i class="fi ' + item.channel_icon + '"></i> ' + item.source +
          '    </span>' +
          '  </td>' +
          '  <td><span class="mkt-campaign-name">' + item.campaign + '</span></td>' +
          '  <td><span class="mkt-time-cell" title="' + item.time + '">' + item.time_ago + '</span></td>' +
          '  <td><span class="mkt-status-badge"><span class="mkt-live-dot mkt-status-dot"></span> ' + item.status + '</span></td>' +
          '</tr>';

        $tbody.append(row);
      });
    },

    renderIntegrations: function (integrations) {
      // Webhook cards removed as per user preference
    }
  };

  $(document).ready(function () {
    MarketingDashboard.init();
  });
})(jQuery);
