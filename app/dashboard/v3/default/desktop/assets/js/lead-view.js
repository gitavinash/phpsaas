/**
 * V3 Lead View & Profile SPA Module
 * Full Screen Cover Itinerary Builder with Rich Accommodation, Transport & Activity Controls.
 */
(function (factory) {
  if (typeof window.jQuery !== "undefined") {
    factory(window.jQuery);
  } else {
    document.addEventListener("DOMContentLoaded", function () {
      if (typeof window.jQuery !== "undefined") {
        factory(window.jQuery);
      }
    });
  }
})(function ($) {
  "use strict";

  const WA_SVG = `<svg class="v3-wa-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>`;

  function escapeHtml(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  if (window.V3LeadView && window.V3LeadView.isModuleLoaded) {
    if (typeof window.V3LeadView.init === "function") {
      window.V3LeadView.init();
    }
    return;
  }

  window.V3LeadView = {
    isModuleLoaded: true,
    leadId: null,
    leadData: null,
    isLoading: false,
    eventsBound: false,
    viewElement: null,
    loadTimeoutTimer: null,
    activeComposerType: "note", // "note" | "followup"
    availablePackages: [],
    selectedPackage: null,

    activeXhr: null,

    notify: function (title, type, text) {
      type = type || "info";
      if (typeof popUpMessage === "function" && !text) {
        popUpMessage("center", title, type);
      } else if (
        typeof Swal !== "undefined" &&
        typeof Swal.fire === "function"
      ) {
        Swal.fire({
          position: "center",
          type: type,
          icon: type,
          title: title,
          text: text || "",
          showConfirmButton: !!text,
          timer: text ? undefined : 1800,
        });
      } else {
        alert(title + (text ? "\n" + text : ""));
      }
    },

    playNotificationSound: function () {
      console.log(
        "[Follow-Up Reminder] 🔊 Playing notification sound for assigned user...",
      );
      try {
        const AudioContextClass =
          window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) {
          const audioCtx = new AudioContextClass();
          const playAudioChime = function () {
            const playTone = (freq, startTime, duration) => {
              const osc = audioCtx.createOscillator();
              const gain = audioCtx.createGain();
              osc.type = "sine";
              osc.frequency.setValueAtTime(
                freq,
                audioCtx.currentTime + startTime,
              );
              gain.gain.setValueAtTime(0.35, audioCtx.currentTime + startTime);
              gain.gain.exponentialRampToValueAtTime(
                0.0001,
                audioCtx.currentTime + startTime + duration,
              );
              osc.connect(gain);
              gain.connect(audioCtx.destination);
              osc.start(audioCtx.currentTime + startTime);
              osc.stop(audioCtx.currentTime + startTime + duration);
            };
            // 3-tone bright chime
            playTone(880.0, 0, 0.18);
            playTone(1174.66, 0.12, 0.18);
            playTone(1479.98, 0.24, 0.35);
            console.log(
              "[Follow-Up Reminder] 🔔 Notification chime successfully played.",
            );
          };

          if (audioCtx.state === "suspended") {
            audioCtx.resume().then(playAudioChime).catch(playAudioChime);
          } else {
            playAudioChime();
          }
        }
      } catch (e) {
        console.warn("[Follow-Up Reminder] ⚠️ Audio playback error:", e);
      }
    },

    scheduleClientReminder: function (
      followupTimeStr,
      customerName,
      agentId,
      leadId,
    ) {
      // Global follow-up reminders are coordinated by app.js across the dashboard
      // to avoid duplicate notification banners, popups, and audio triggers.
    },

    getLeadId: function () {
      const urlParams = new URLSearchParams(window.location.search);
      let id = urlParams.get("id") || urlParams.get("leadId") || "";
      if (!id) {
        const $app = $("#leadViewApp");
        if ($app.length) {
          id = $app.data("lead-id") || $app.attr("data-lead-id") || "";
        }
      }
      if (!id) {
        const match = window.location.pathname.match(/\/leads\/view\/([^\/]+)/);
        if (match && match[1]) {
          id = match[1];
        }
      }
      return id ? String(id).trim() : "";
    },

    init: function () {
      const $app = $("#leadViewApp");
      if (!$app.length) return;

      const currentId = this.getLeadId();
      if (!currentId) {
        $("#leadViewMainContainer").html(`
          <div class="v3-leads-empty-state">
            <div class="v3-empty-icon-wrap"><i class="fi fi-rr-exclamation"></i></div>
            <h4 class="v3-empty-title">Lead ID Missing</h4>
            <p class="v3-empty-subtitle">No valid lead identifier was provided in the URL request.</p>
            <a href="/dashboard/leads/list/" class="btn-empty-reset-filters"><i class="fi fi-rr-arrow-left"></i> Back to Leads</a>
          </div>
        `);
        return;
      }

      const isNewView = this.viewElement !== $app[0];
      if (isNewView) {
        this.viewElement = $app[0];
        this.leadId = null;
        this.leadData = null;
      }

      // If we are ALREADY fetching or already loaded for this exact leadId, ignore redundant calls
      if (this.leadId === currentId && (this.isLoading || this.leadData)) {
        return;
      }

      this.leadId = currentId;
      this.leadData = null;

      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }

      this.loadLeadDetails();
    },

    loadLeadDetails: function () {
      const self = this;
      if (!self.leadId) return;

      if (self.activeXhr) {
        self.activeXhr.abort();
        self.activeXhr = null;
      }
      self.isLoading = true;

      const $container = $("#leadViewMainContainer");
      $container.html(`
        <div class="v3-leads-table-loader">
          <div class="v3-table-spinner">
            <div class="v3-spinner-ring"></div>
            <div class="v3-spinner-inner-dot"></div>
          </div>
          <h4 class="v3-loader-title">Loading Lead Details...</h4>
          <p class="v3-loader-subtitle">Fetching customer profile, timeline, and booking records.</p>
          <button type="button" class="btn-empty-reset-filters v3-lead-loader-retry" id="btnRetryLeadView" style="display: none;"><i class="fi fi-rr-refresh"></i> Reload Lead</button>
        </div>
      `);

      clearTimeout(self.loadTimeoutTimer);
      self.loadTimeoutTimer = setTimeout(function () {
        if (self.isLoading && self.leadId) {
          $("#btnRetryLeadView").stop(true, true).fadeIn(150);
        }
      }, 3000);

      self.activeXhr = $.ajax({
        url: "/api/v3/leads/view/",
        type: "GET",
        data: { id: self.leadId },
        dataType: "json",
        cache: false,
        success: function (res) {
          clearTimeout(self.loadTimeoutTimer);
          self.isLoading = false;
          self.activeXhr = null;
          if (res.status === "success" && res.data) {
            self.leadData = res.data;
            self.renderView(res.data);
          } else {
            $container.html(`
              <div class="v3-leads-empty-state">
                <div class="v3-empty-icon-wrap"><i class="fi fi-rr-cross-circle"></i></div>
                <h4 class="v3-empty-title">Lead Not Found</h4>
                <p class="v3-empty-subtitle">${res.message || "The requested lead could not be located or access is restricted."}</p>
                <a href="/dashboard/leads/list/" class="btn-empty-reset-filters"><i class="fi fi-rr-arrow-left"></i> Back to Leads</a>
              </div>
            `);
          }
        },
        error: function (xhr, status) {
          if (status === "abort") return;
          clearTimeout(self.loadTimeoutTimer);
          self.isLoading = false;
          self.activeXhr = null;
          $container.html(`
            <div class="v3-leads-empty-state">
              <div class="v3-empty-icon-wrap"><i class="fi fi-rr-exclamation"></i></div>
              <h4 class="v3-empty-title">Connection Error</h4>
              <p class="v3-empty-subtitle">Unable to fetch lead details from server. Please check your network and retry.</p>
              <button type="button" class="btn-empty-reset-filters" id="btnRetryLeadView"><i class="fi fi-rr-refresh"></i> Retry</button>
            </div>
          `);
        },
      });
    },

    isTrackingKey: function (key) {
      if (!key && key !== 0) return false;
      const k = String(key).trim().toLowerCase();
      const exactTracking = [
        "gclid",
        "gad_source",
        "wbraid",
        "gbraid",
        "dclid",
        "fbclid",
        "msclkid",
        "ttclid",
        "twclid",
        "li_fat_id",
        "mc_eid",
        "igshid",
        "yclid",
        "btnsubmit",
        "btn_submit",
        "submit",
        "submit_button",
        "action",
      ];
      if (exactTracking.indexOf(k) !== -1) return true;
      if (k.startsWith("utm_")) return true;
      if (k.indexOf("recaptcha") !== -1) return true;
      if (k.indexOf("grecaptcha") !== -1) return true;
      if (/^(_ga|_gid|_fbp|_fbc|_gat|_gac|_clck|_clsk)/i.test(k)) return true;
      return false;
    },

    filterTrackingKeys: function (obj) {
      if (!obj || typeof obj !== "object") return obj;
      const self = this;
      if (Array.isArray(obj)) {
        return obj
          .filter(function (item) {
            if (item && typeof item === "object" && item.name) {
              return !self.isTrackingKey(item.name);
            }
            return true;
          })
          .map(function (item) {
            return typeof item === "object"
              ? self.filterTrackingKeys(item)
              : item;
          });
      }
      const clean = {};
      Object.keys(obj).forEach(function (k) {
        if (!self.isTrackingKey(k)) {
          clean[k] =
            typeof obj[k] === "object" && obj[k] !== null
              ? self.filterTrackingKeys(obj[k])
              : obj[k];
        }
      });
      return clean;
    },

    isProfileOrDuplicateKey: function (key) {
      if (!key && key !== 0) return true;
      const kStr = String(key).trim();
      if (!kStr) return true;

      // 1. Technical / Meta / Tracking Keys
      const technicalKeys = [
        "id",
        "created_time",
        "inbox_url",
        "ad_name",
        "campaign_name",
        "form_id",
        "page_id",
        "leadgen_id",
        "campaign_id",
        "adset_id",
        "ad_id",
        "partner_category",
        "lead_id",
        "enquiry_id",
      ];
      if (technicalKeys.includes(kStr.toLowerCase())) return true;
      if (this.isTrackingKey(kStr)) return true;

      // 2. Normalize key
      const norm = kStr
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, " ")
        .trim();
      if (!norm) return true;

      // 3. Match against standard Lead Profile fields (Customer Information & Travel Logistics)
      const matchedField = this.matchJsonKeyToLeadField(kStr);
      if (matchedField && matchedField.field && matchedField.field !== "do_not_map") {
        return true;
      }

      // 4. Additional substring / alias checks for contact, name, and travel logistics
      if (
        norm.includes("email") ||
        norm === "e mail" ||
        norm === "mail" ||
        norm.includes("phone") ||
        norm.includes("mobile") ||
        norm.includes("whatsapp") ||
        norm.includes("contact number") ||
        norm.includes("contact no") ||
        norm.includes("phone no") ||
        norm.includes("travel date") ||
        norm.includes("date of travel") ||
        norm.includes("trip date") ||
        norm.includes("departure date") ||
        norm.includes("how many person") ||
        norm.includes("how many people") ||
        norm.includes("group size") ||
        norm === "name" ||
        norm === "full name" ||
        norm === "fullname" ||
        norm === "first name" ||
        norm === "firstname" ||
        norm === "last name" ||
        norm === "lastname" ||
        norm === "customer name" ||
        norm === "your name"
      ) {
        return true;
      }

      return false;
    },

    hasMeaningfulRequirements: function (parsed) {
      if (!parsed) return false;
      const self = this;

      if (parsed.field_data && Array.isArray(parsed.field_data)) {
        const remaining = parsed.field_data.filter(function (item) {
          if (!item) return false;
          const k = item.name || "";
          if (self.isProfileOrDuplicateKey(k)) return false;
          const val = Array.isArray(item.values)
            ? item.values.join(", ")
            : item.values;
          return (
            val !== null &&
            val !== undefined &&
            String(val).trim() !== "" &&
            String(val).trim() !== "#"
          );
        });
        if (remaining.length > 0) return true;

        const otherKeys = Object.keys(parsed).filter(function (k) {
          if (k === "field_data") return false;
          if (self.isProfileOrDuplicateKey(k)) return false;
          const val = parsed[k];
          return (
            val !== null &&
            val !== undefined &&
            String(val).trim() !== "" &&
            String(val).trim() !== "#"
          );
        });
        return otherKeys.length > 0;
      }

      if (Array.isArray(parsed)) {
        return parsed.some(function (item) {
          if (item && typeof item === "object") {
            return Object.keys(item).some(function (k) {
              return !self.isProfileOrDuplicateKey(k);
            });
          }
          return (
            item !== null && item !== undefined && String(item).trim() !== ""
          );
        });
      }

      if (typeof parsed === "object") {
        const keys = Object.keys(parsed).filter(function (k) {
          return !self.isProfileOrDuplicateKey(k);
        });
        if (keys.length === 0) return false;
        return keys.some(function (k) {
          const val = parsed[k];
          if (val === null || val === undefined) return false;
          if (typeof val === "object") return true;
          const s = String(val).trim();
          return s !== "" && s !== "#";
        });
      }

      return String(parsed).trim() !== "";
    },

    parseRequirementsJson: function (raw) {
      if (!raw && raw !== 0) return null;
      if (typeof raw === "object" && raw !== null) {
        return this.filterTrackingKeys(raw);
      }
      if (typeof raw !== "string") return null;
      const trimmed = raw.trim();
      if (!trimmed) return null;

      if (
        (!trimmed.startsWith("{") || !trimmed.endsWith("}")) &&
        (!trimmed.startsWith("[") || !trimmed.endsWith("]"))
      ) {
        return null;
      }

      try {
        const parsed = JSON.parse(trimmed);
        if (typeof parsed === "object" && parsed !== null) {
          return this.filterTrackingKeys(parsed);
        }
      } catch (e) {
        try {
          const doc = new DOMParser().parseFromString(trimmed, "text/html");
          const unescaped = doc.body.textContent || "";
          if (unescaped && unescaped !== trimmed) {
            const parsed2 = JSON.parse(unescaped);
            if (typeof parsed2 === "object" && parsed2 !== null) {
              return this.filterTrackingKeys(parsed2);
            }
          }
        } catch (e2) {
          return null;
        }
      }
      return null;
    },

    cleanLeadStringValue: function (val) {
      if (val === null || val === undefined) return "";
      let str = "";
      if (typeof val === "object") {
        if (Array.isArray(val)) {
          str = val
            .map((x) => (typeof x === "object" ? JSON.stringify(x) : String(x)))
            .join(", ");
        } else {
          str = JSON.stringify(val);
        }
      } else {
        str = String(val);
      }
      str = str.trim();
      // Fix unicode @ artifact e.g. u0040 or \u0040
      str = str.replace(/\\u0040/gi, "@").replace(/\bu0040/gi, "@");
      // Fix unicode dash artifacts
      str = str.replace(/\\u2013/gi, "–").replace(/\bu2013/gi, "–");
      str = str.replace(/\\u2014/gi, "—").replace(/\bu2014/gi, "—");
      // If it's a JSON quoted string like "\"Md Koushar Ali\""
      if (str.startsWith('"') && str.endsWith('"') && str.length >= 2) {
        try {
          str = JSON.parse(str);
        } catch (e) {
          str = str.slice(1, -1);
        }
      }
      return String(str).trim();
    },

    formatFieldLabel: function (key) {
      if (!key && key !== 0) return "";
      const s = String(key)
        .replace(/_/g, " ")
        .replace(/-/g, " ")
        .replace(/([a-z])([A-Z])/g, "$1 $2")
        .replace(/\?/g, "")
        .trim();
      return s.replace(/\b\w/g, function (c) {
        return c.toUpperCase();
      });
    },

    formatFieldValue: function (val) {
      const self = this;
      if (val === null || val === undefined || val === "") {
        return '<span class="v3-req-val-empty">-</span>';
      }
      if (typeof val === "boolean") {
        return val
          ? '<span class="v3-badge badge-confirmed">Yes</span>'
          : '<span class="v3-badge badge-cancelled">No</span>';
      }
      if (typeof val === "number") {
        return `<span class="v3-req-val-num">${val}</span>`;
      }
      if (typeof val === "object") {
        if (Array.isArray(val)) {
          if (val.length === 0) {
            return '<span class="v3-req-val-empty">-</span>';
          }
          const isAllPrimitive = val.every(function (x) {
            return typeof x !== "object";
          });
          if (isAllPrimitive) {
            return val
              .map(function (x) {
                return `<span class="v3-req-badge-pill">${escapeHtml(self.cleanLeadStringValue(x))}</span>`;
              })
              .join(" ");
          }
          return `<pre class="v3-req-val-json"><code>${escapeHtml(JSON.stringify(val, null, 2))}</code></pre>`;
        }
        return `<pre class="v3-req-val-json"><code>${escapeHtml(JSON.stringify(val, null, 2))}</code></pre>`;
      }
      const str = self.cleanLeadStringValue(val);
      if (str === "#" || str === "") {
        return '<span class="v3-req-val-empty">-</span>';
      }
      if (/^https?:\/\//i.test(str)) {
        const displayUrl = str.length > 55 ? str.substring(0, 52) + "..." : str;
        return `<a href="${escapeHtml(str)}" target="_blank" rel="noopener noreferrer" class="v3-req-link"><i class="fi fi-rr-link"></i> ${escapeHtml(displayUrl)}</a>`;
      }
      return `<span class="v3-req-val-text">${escapeHtml(str)}</span>`;
    },

    renderRequirementsTable: function (parsed) {
      const self = this;
      if (parsed && parsed.field_data && Array.isArray(parsed.field_data)) {
        const rows = parsed.field_data
          .filter(function (item) {
            return item && !self.isProfileOrDuplicateKey(item.name || "");
          })
          .map(function (item) {
            const label = self.formatFieldLabel(item.name || "Field");
            const val = Array.isArray(item.values)
              ? item.values.join(", ")
              : item.values || "-";
            return `
              <tr>
                <td class="req-key-col">
                  <span class="req-key-bullet"></span>
                  <span class="req-key-text">${escapeHtml(label)}</span>
                </td>
                <td class="req-val-col">${self.formatFieldValue(val)}</td>
              </tr>
            `;
          })
          .join("");

        let metaRows = "";
        Object.keys(parsed).forEach(function (k) {
          if (k === "field_data") return;
          if (self.isProfileOrDuplicateKey(k)) return;
          const label = self.formatFieldLabel(k);
          metaRows += `
            <tr>
              <td class="req-key-col">
                <span class="req-key-bullet"></span>
                <span class="req-key-text">${escapeHtml(label)}</span>
              </td>
              <td class="req-val-col">${self.formatFieldValue(parsed[k])}</td>
            </tr>
          `;
        });

        if (!rows && !metaRows) {
          return '<div class="v3-requirements-empty-note"><i class="fi fi-rr-info"></i><span>No additional special notes or requirements specified.</span></div>';
        }

        return `
          <div class="v3-requirements-table-wrapper">
            <table class="v3-requirements-table">
              <thead>
                <tr>
                  <th>Field / Lead Question</th>
                  <th>Customer Response / Details</th>
                </tr>
              </thead>
              <tbody>${rows}${metaRows}</tbody>
            </table>
          </div>
        `;
      }

      if (Array.isArray(parsed)) {
        if (parsed.length === 0) {
          return '<div class="v3-requirements-empty-note"><i class="fi fi-rr-info"></i><span>Empty list of requirements.</span></div>';
        }
        const allKeys = [];
        parsed.forEach(function (item) {
          if (typeof item === "object" && item !== null) {
            Object.keys(item).forEach(function (k) {
              if (!self.isProfileOrDuplicateKey(k) && !allKeys.includes(k)) {
                allKeys.push(k);
              }
            });
          }
        });

        if (allKeys.length > 0) {
          const thead = `<tr>${allKeys
            .map(function (k) {
              return `<th>${escapeHtml(self.formatFieldLabel(k))}</th>`;
            })
            .join("")}</tr>`;
          const tbody = parsed
            .map(function (item) {
              return `<tr>${allKeys
                .map(function (k) {
                  return `<td>${self.formatFieldValue(item[k])}</td>`;
                })
                .join("")}</tr>`;
            })
            .join("");
          return `
            <div class="v3-requirements-table-wrapper">
              <table class="v3-requirements-table">
                <thead>${thead}</thead>
                <tbody>${tbody}</tbody>
              </table>
            </div>
          `;
        } else {
          const nonDupItems = parsed.filter(function (val) {
            if (typeof val === "object" && val !== null) {
              return Object.keys(val).some(function (k) {
                return !self.isProfileOrDuplicateKey(k);
              });
            }
            return (
              val !== null &&
              val !== undefined &&
              String(val).trim() !== "" &&
              String(val).trim() !== "#"
            );
          });
          if (nonDupItems.length === 0) {
            return '<div class="v3-requirements-empty-note"><i class="fi fi-rr-info"></i><span>No additional special notes or requirements specified.</span></div>';
          }
          const tbody = nonDupItems
            .map(function (val, idx) {
              return `
                <tr>
                  <td class="req-key-col">#${idx + 1}</td>
                  <td class="req-val-col">${self.formatFieldValue(val)}</td>
                </tr>
              `;
            })
            .join("");
          return `
            <div class="v3-requirements-table-wrapper">
              <table class="v3-requirements-table">
                <thead>
                  <tr>
                    <th style="width: 80px;">#</th>
                    <th>Requirement Item</th>
                  </tr>
                </thead>
                <tbody>${tbody}</tbody>
              </table>
            </div>
          `;
        }
      }

      if (typeof parsed === "object" && parsed !== null) {
        const keys = Object.keys(parsed).filter(function (k) {
          return !self.isProfileOrDuplicateKey(k);
        });
        if (keys.length === 0) {
          return '<div class="v3-requirements-empty-note"><i class="fi fi-rr-info"></i><span>Empty structured data.</span></div>';
        }
        const tbody = keys
          .map(function (k) {
            const label = self.formatFieldLabel(k);
            const val = parsed[k];
            return `
              <tr>
                <td class="req-key-col">
                  <span class="req-key-bullet"></span>
                  <span class="req-key-text">${escapeHtml(label)}</span>
                </td>
                <td class="req-val-col">${self.formatFieldValue(val)}</td>
              </tr>
            `;
          })
          .join("");

        return `
          <div class="v3-requirements-table-wrapper">
            <table class="v3-requirements-table">
              <thead>
                <tr>
                  <th>Field / Specification</th>
                  <th>Value / Details</th>
                </tr>
              </thead>
              <tbody>${tbody}</tbody>
            </table>
          </div>
        `;
      }

      return `<div class="v3-requirements-box">${escapeHtml(String(parsed))}</div>`;
    },

    matchJsonKeyToLeadField: function (key) {
      if (!key) return null;
      const norm = String(key)
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, " ")
        .trim();
      if (!norm) return null;

      // 1. Full Customer Name
      const nameExact = [
        "full name",
        "fullname",
        "name",
        "customer name",
        "lead name",
        "client name",
        "contact name",
        "user name",
        "username",
        "traveller name",
        "traveler name",
        "person name",
        "your name",
      ];
      if (nameExact.includes(norm)) {
        return {
          field: "customer_name",
          label: "Customer Full Name (First + Last)",
        };
      }
      // 2. First Name
      const fNameExact = [
        "first name",
        "firstname",
        "first",
        "fname",
        "given name",
      ];
      if (fNameExact.includes(norm)) {
        return { field: "first_name", label: "First Name" };
      }
      // 3. Last Name
      const lNameExact = [
        "last name",
        "lastname",
        "last",
        "lname",
        "surname",
        "family name",
      ];
      if (lNameExact.includes(norm)) {
        return { field: "last_name", label: "Last Name" };
      }
      // 4. Primary Mobile / Phone
      const mobileExact = [
        "phone number",
        "phonenumber",
        "phone",
        "mobile number",
        "mobilenumber",
        "mobile",
        "contact number",
        "contact no",
        "phone no",
        "cell",
        "cell phone",
        "whatsapp number",
        "wa number",
        "primary phone",
        "primary mobile",
        "enter your phone number",
        "your phone number",
      ];
      if (
        mobileExact.includes(norm) ||
        (norm.includes("phone") && !norm.includes("alt"))
      ) {
        return { field: "mobile", label: "Primary Mobile" };
      }
      // 5. Alternate Mobile
      const altMobileExact = [
        "alternate mobile",
        "alt mobile",
        "alternate phone",
        "alt phone",
        "secondary phone",
        "secondary mobile",
        "emergency contact",
        "alternate contact",
      ];
      if (
        altMobileExact.includes(norm) ||
        norm.includes("alternate") ||
        norm.includes("alt mobile")
      ) {
        return { field: "alt_mobile", label: "Alternate Mobile" };
      }
      // 6. Email
      const emailExact = [
        "email",
        "email address",
        "e mail",
        "mail",
        "customer email",
        "enter your email",
        "your email",
      ];
      if (emailExact.includes(norm) || norm.includes("email")) {
        return { field: "email", label: "Email Address" };
      }
      // 7. City / Current Location
      const cityExact = [
        "current city",
        "city",
        "current location",
        "location",
        "residence",
        "living in",
        "from city",
        "departure city",
        "home town",
        "hometown",
        "state",
        "your city",
      ];
      if (
        cityExact.includes(norm) ||
        norm.includes("current city") ||
        norm.includes("current location")
      ) {
        return { field: "current_location", label: "Current City / Location" };
      }
      // 8. Destination / Tour Package
      const destExact = [
        "destination",
        "tour package",
        "package",
        "subject",
        "tour",
        "holiday destination",
        "travel destination",
        "which destination",
        "destination city",
        "where do you want to go",
        "where to go",
        "preferred destination",
      ];
      if (
        destExact.includes(norm) ||
        norm.includes("destination") ||
        (norm.includes("trip") &&
          !norm.includes("when") &&
          !norm.includes("date")) ||
        norm.includes("places to visit") ||
        norm.includes("places you want to visit")
      ) {
        return { field: "destination", label: "Tour Package / Destination" };
      }
      // 9. Date of Travel
      const dateExact = [
        "date of travel",
        "travel date",
        "departure date",
        "start date",
        "trip date",
        "travel month",
        "tentative date",
        "planning to travel",
        "travel time",
      ];
      if (
        dateExact.includes(norm) ||
        norm.includes("when are you planning") ||
        norm.includes("planning your") ||
        norm.includes("travel date") ||
        norm.includes("date of travel")
      ) {
        return { field: "date_of_travel", label: "Date of Travel" };
      }
      // 10. Duration
      const durExact = [
        "duration",
        "duration days",
        "number of days",
        "days",
        "how many days",
        "trip duration",
        "total days",
      ];
      if (
        durExact.includes(norm) ||
        norm.includes("duration") ||
        norm.includes("how many days")
      ) {
        return { field: "duration_days", label: "Duration (Days)" };
      }
      // 11. Adults / Pax
      const adultExact = [
        "adults",
        "number of adults",
        "no of adults",
        "pax",
        "number of persons",
        "number of person",
        "how many person",
        "how many persons",
        "how many people",
        "how many people are travelling",
        "travelers",
        "travellers",
        "guests",
        "passengers",
        "group size",
        "number of travelers",
      ];
      if (
        adultExact.includes(norm) ||
        norm.includes("how many people") ||
        norm.includes("how many person") ||
        norm.includes("adult")
      ) {
        return { field: "adults", label: "Adults / Pax" };
      }
      // 12. Children
      const childExact = [
        "children",
        "number of children",
        "number of childrens",
        "kids",
        "child",
        "no of children",
        "children count",
      ];
      if (
        childExact.includes(norm) ||
        norm.includes("child") ||
        norm.includes("kid")
      ) {
        return { field: "children", label: "Children" };
      }
      // 13. Infants
      const infantExact = [
        "infants",
        "number of infants",
        "baby",
        "infant",
        "infants count",
      ];
      if (
        infantExact.includes(norm) ||
        norm.includes("infant") ||
        norm.includes("baby")
      ) {
        return { field: "infants", label: "Infants" };
      }
      // 14. Pickup Location
      const pickupExact = [
        "pickup location",
        "pickup point",
        "pickup",
        "boarding point",
        "boarding city",
        "departure point",
      ];
      if (pickupExact.includes(norm) || norm.includes("pickup")) {
        return { field: "pickup_location", label: "Pickup Location" };
      }
      // 15. Drop Location
      const dropExact = [
        "drop location",
        "drop point",
        "drop",
        "dropping point",
        "destination drop",
      ];
      if (
        dropExact.includes(norm) ||
        norm.includes("drop point") ||
        norm.includes("drop location")
      ) {
        return { field: "drop_location", label: "Drop Location" };
      }

      return null;
    },

    autoFillFieldIfEmpty: function (field, val) {
      if (!val) return false;
      const strVal = String(val).trim();
      if (!strVal) return false;

      switch (field) {
        case "customer_name": {
          const $f = $("#editLeadFirstName");
          const $l = $("#editLeadLastName");
          if (!$f.val().trim() && !$l.val().trim()) {
            const parts = strVal.split(/\s+/);
            $f.val(parts[0] || "");
            $l.val(parts.slice(1).join(" ") || "");
            return true;
          }
          return false;
        }
        case "first_name": {
          const $f = $("#editLeadFirstName");
          if (!$f.val().trim()) {
            $f.val(strVal);
            return true;
          }
          return false;
        }
        case "last_name": {
          const $l = $("#editLeadLastName");
          if (!$l.val().trim()) {
            $l.val(strVal);
            return true;
          }
          return false;
        }
        case "mobile": {
          const $m = $("#editLeadMobile");
          if (!$m.val().trim()) {
            const clean = strVal.replace(/[^0-9+]/g, "");
            $m.val(clean || strVal);
            return true;
          }
          return false;
        }
        case "alt_mobile": {
          const $am = $("#editLeadAltMobile");
          if (!$am.val().trim()) {
            const clean = strVal.replace(/[^0-9+]/g, "");
            $am.val(clean || strVal);
            return true;
          }
          return false;
        }
        case "email": {
          const $e = $("#editLeadEmail");
          if (!$e.val().trim()) {
            const clean = strVal.replace(/[\s\u200B-\u200D\uFEFF]/g, "");
            $e.val(clean);
            return true;
          }
          return false;
        }
        case "current_location": {
          const $loc = $("#editLeadCurrentLocation");
          if (!$loc.val()) {
            $loc
              .append(new Option(strVal, strVal, true, true))
              .trigger("change");
            return true;
          }
          return false;
        }
        case "destination": {
          const $pkg = $("#editLeadPackage");
          if (!$pkg.val()) {
            let dest = strVal;
            const matchCity = strVal.match(
              /\b(himachal|kashmir|kerala|goa|dubai|bali|thailand|singapore|maldives|andaman|rajasthan|ladakh|manali|shimla|ooty|sikkim)\b/i,
            );
            if (matchCity) {
              dest =
                matchCity[0].charAt(0).toUpperCase() +
                matchCity[0].slice(1).toLowerCase();
            }
            $pkg.append(new Option(dest, dest, true, true)).trigger("change");
            if (!$("#editLeadSubject").val().trim()) {
              $("#editLeadSubject").val(dest);
            }
            return true;
          }
          return false;
        }
        case "date_of_travel": {
          const $d = $("#editLeadTravelDate");
          if (!$d.val()) {
            if (
              typeof moment !== "undefined" &&
              moment(
                strVal,
                ["YYYY-MM-DD", "DD-MM-YYYY", "DD/MM/YYYY", "MM/DD/YYYY"],
                true,
              ).isValid()
            ) {
              $d.val(moment(strVal).format("YYYY-MM-DD"));
              return true;
            }
          }
          return false;
        }
        case "duration_days": {
          const $dur = $("#editLeadDuration");
          if (parseInt($dur.val(), 10) <= 0) {
            const m = strVal.match(/\d+/);
            if (m) {
              $dur.val(parseInt(m[0], 10));
              return true;
            }
          }
          return false;
        }
        case "adults": {
          const $ad = $("#editLeadAdults");
          if (parseInt($ad.val(), 10) <= 1) {
            const m = strVal.match(/\d+/);
            if (m) {
              $ad.val(parseInt(m[0], 10));
              return true;
            }
          }
          return false;
        }
        case "children": {
          const $ch = $("#editLeadChildren");
          if (parseInt($ch.val(), 10) <= 0) {
            const m = strVal.match(/\d+/);
            if (m) {
              $ch.val(parseInt(m[0], 10));
              return true;
            }
          }
          return false;
        }
        case "infants": {
          const $inf = $("#editLeadInfants");
          if (parseInt($inf.val(), 10) <= 0) {
            const m = strVal.match(/\d+/);
            if (m) {
              $inf.val(parseInt(m[0], 10));
              return true;
            }
          }
          return false;
        }
        case "pickup_location": {
          const $p = $("#editLeadPickup");
          if (!$p.val()) {
            $p.append(new Option(strVal, strVal, true, true)).trigger("change");
            return true;
          }
          return false;
        }
        case "drop_location": {
          const $dr = $("#editLeadDrop");
          if (!$dr.val()) {
            $dr
              .append(new Option(strVal, strVal, true, true))
              .trigger("change");
            return true;
          }
          return false;
        }
        default:
          return false;
      }
    },

    applyMappedValueToField: function (field, val) {
      if (!val) return;
      const strVal = String(val).trim();
      if (!strVal) return;

      switch (field) {
        case "customer_name": {
          const parts = strVal.split(/\s+/);
          $("#editLeadFirstName").val(parts[0] || "");
          $("#editLeadLastName").val(parts.slice(1).join(" ") || "");
          break;
        }
        case "first_name": {
          $("#editLeadFirstName").val(strVal);
          break;
        }
        case "last_name": {
          $("#editLeadLastName").val(strVal);
          break;
        }
        case "mobile": {
          const clean = strVal.replace(/[^0-9+]/g, "");
          $("#editLeadMobile").val(clean || strVal);
          break;
        }
        case "alt_mobile": {
          const clean = strVal.replace(/[^0-9+]/g, "");
          $("#editLeadAltMobile").val(clean || strVal);
          break;
        }
        case "email": {
          const clean = strVal.replace(/[\s\u200B-\u200D\uFEFF]/g, "");
          $("#editLeadEmail").val(clean);
          break;
        }
        case "current_location": {
          $("#editLeadCurrentLocation")
            .empty()
            .append(new Option(strVal, strVal, true, true))
            .trigger("change");
          break;
        }
        case "destination": {
          let dest = strVal;
          const matchCity = strVal.match(
            /\b(himachal|kashmir|kerala|goa|dubai|bali|thailand|singapore|maldives|andaman|rajasthan|ladakh|manali|shimla|ooty|sikkim)\b/i,
          );
          if (matchCity) {
            dest =
              matchCity[0].charAt(0).toUpperCase() +
              matchCity[0].slice(1).toLowerCase();
          }
          $("#editLeadPackage")
            .empty()
            .append(new Option(dest, dest, true, true))
            .trigger("change");
          $("#editLeadSubject").val(dest);
          break;
        }
        case "date_of_travel": {
          if (
            typeof moment !== "undefined" &&
            moment(
              strVal,
              ["YYYY-MM-DD", "DD-MM-YYYY", "DD/MM/YYYY", "MM/DD/YYYY"],
              true,
            ).isValid()
          ) {
            $("#editLeadTravelDate").val(moment(strVal).format("YYYY-MM-DD"));
          } else {
            $("#editLeadTravelDate").val(strVal);
          }
          break;
        }
        case "duration_days": {
          const m = strVal.match(/\d+/);
          if (m) $("#editLeadDuration").val(parseInt(m[0], 10));
          break;
        }
        case "adults": {
          const m = strVal.match(/\d+/);
          if (m) $("#editLeadAdults").val(parseInt(m[0], 10));
          break;
        }
        case "children": {
          const m = strVal.match(/\d+/);
          if (m) $("#editLeadChildren").val(parseInt(m[0], 10));
          break;
        }
        case "infants": {
          const m = strVal.match(/\d+/);
          if (m) $("#editLeadInfants").val(parseInt(m[0], 10));
          break;
        }
        case "pickup_location": {
          $("#editLeadPickup")
            .empty()
            .append(new Option(strVal, strVal, true, true))
            .trigger("change");
          break;
        }
        case "drop_location": {
          $("#editLeadDrop")
            .empty()
            .append(new Option(strVal, strVal, true, true))
            .trigger("change");
          break;
        }
      }
    },

    buildReqMapRowHtml: function (key, val, selectedField, isFromFieldData) {
      const self = this;
      const strKey = String(key || "");
      const cleanVal = self.cleanLeadStringValue(
        typeof val === "object" && val !== null
          ? Array.isArray(val)
            ? val.join(", ")
            : JSON.stringify(val)
          : String(val || ""),
      );
      const field = selectedField || "do_not_map";
      const fromFd = isFromFieldData ? "1" : "0";

      const options = [
        { val: "do_not_map", text: "— Keep in Notes (Do not map) —" },
        { val: "customer_name", text: "Customer Full Name (First + Last)" },
        { val: "first_name", text: "First Name" },
        { val: "last_name", text: "Last Name" },
        { val: "mobile", text: "Primary Mobile" },
        { val: "alt_mobile", text: "Alternate Mobile" },
        { val: "email", text: "Email Address" },
        { val: "current_location", text: "Current City / Location" },
        { val: "destination", text: "Tour Package / Destination" },
        { val: "date_of_travel", text: "Date of Travel" },
        { val: "duration_days", text: "Duration (Days)" },
        { val: "adults", text: "Adults / Pax" },
        { val: "children", text: "Children" },
        { val: "infants", text: "Infants" },
        { val: "pickup_location", text: "Pickup Location" },
        { val: "drop_location", text: "Drop Location" },
      ];

      const optHtml = options
        .map(function (opt) {
          const isSel = opt.val === field ? "selected" : "";
          return `<option value="${opt.val}" ${isSel}>${opt.text}</option>`;
        })
        .join("");

      let statusBadge = "";
      if (field === "do_not_map") {
        statusBadge = `<span class="v3-mapping-status-pill unmapped"><i class="fi fi-rr-document"></i> Retained in JSON</span>`;
      } else {
        statusBadge = `<span class="v3-mapping-status-pill mapped"><i class="fi fi-rr-check"></i> Maps to Field</span>`;
      }

      return `
        <div class="v3-req-map-row" data-json-key="${escapeHtml(strKey)}" data-from-field-data="${fromFd}">
          <div class="v3-req-map-td col-key">
            <span class="v3-req-key-badge">${escapeHtml(self.formatFieldLabel(strKey))}</span>
            <input type="hidden" class="v3-req-key-input" value="${escapeHtml(strKey)}">
            <input type="hidden" class="v3-req-from-field-data" value="${fromFd}">
          </div>
          <div class="v3-req-map-td col-val">
            <input type="text" class="v3-req-val-input v3-modal-input" value="${escapeHtml(cleanVal)}" placeholder="Value...">
          </div>
          <div class="v3-req-map-td col-map">
            <select class="v3-req-map-select v3-modal-select">
              ${optHtml}
            </select>
            <div class="v3-req-map-status-wrap">
              ${statusBadge}
            </div>
          </div>
          <div class="v3-req-map-td col-act">
            <button type="button" class="btn-remove-req-row" title="Delete Note Row">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>
        </div>
      `;
    },

    buildCustomReqRowHtml: function () {
      const options = [
        { val: "do_not_map", text: "— Keep in Notes (Do not map) —" },
        { val: "customer_name", text: "Customer Full Name (First + Last)" },
        { val: "first_name", text: "First Name" },
        { val: "last_name", text: "Last Name" },
        { val: "mobile", text: "Primary Mobile" },
        { val: "alt_mobile", text: "Alternate Mobile" },
        { val: "email", text: "Email Address" },
        { val: "current_location", text: "Current City / Location" },
        { val: "destination", text: "Tour Package / Destination" },
        { val: "date_of_travel", text: "Date of Travel" },
        { val: "duration_days", text: "Duration (Days)" },
        { val: "adults", text: "Adults / Pax" },
        { val: "children", text: "Children" },
        { val: "infants", text: "Infants" },
        { val: "pickup_location", text: "Pickup Location" },
        { val: "drop_location", text: "Drop Location" },
      ];

      const optHtml = options
        .map(function (opt) {
          return `<option value="${opt.val}">${opt.text}</option>`;
        })
        .join("");

      return `
        <div class="v3-req-map-row is-custom-row" data-from-field-data="0">
          <div class="v3-req-map-td col-key">
            <input type="text" class="v3-req-key-input v3-modal-input" placeholder="Field / Note Name...">
            <input type="hidden" class="v3-req-from-field-data" value="0">
          </div>
          <div class="v3-req-map-td col-val">
            <input type="text" class="v3-req-val-input v3-modal-input" placeholder="Value / Requirement...">
          </div>
          <div class="v3-req-map-td col-map">
            <select class="v3-req-map-select v3-modal-select">
              ${optHtml}
            </select>
            <div class="v3-req-map-status-wrap">
              <span class="v3-mapping-status-pill unmapped"><i class="fi fi-rr-document"></i> Retained in JSON</span>
            </div>
          </div>
          <div class="v3-req-map-td col-act">
            <button type="button" class="btn-remove-req-row" title="Delete Note Row">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>
        </div>
      `;
    },

    renderModalRequirementsTable: function (parsedData, rawText) {
      const self = this;
      const $container = $("#editLeadRequirementsTabularWrap");
      if (!$container.length) return;

      let entries = [];
      if (parsedData && typeof parsedData === "object") {
        // 1. Unpack field_data array if present (e.g. Facebook Lead Ads)
        if (parsedData.field_data && Array.isArray(parsedData.field_data)) {
          parsedData.field_data.forEach(function (item) {
            if (!item) return;
            const k = item.name || "Field";
            let v = "";
            if (Array.isArray(item.values)) {
              v = item.values.join(", ");
            } else if (item.values !== null && item.values !== undefined) {
              v = String(item.values);
            } else if (item.value !== null && item.value !== undefined) {
              v = String(item.value);
            }
            entries.push({
              key: k,
              value: self.cleanLeadStringValue(v),
              fromFieldData: true,
            });
          });

          // Also unpack any top-level metadata keys (e.g. inbox_url, created_time, ad_name)
          Object.keys(parsedData).forEach(function (k) {
            if (k === "field_data") return;
            const val = parsedData[k];
            if (val !== null && val !== undefined && val !== "") {
              entries.push({
                key: k,
                value:
                  typeof val === "object"
                    ? JSON.stringify(val)
                    : self.cleanLeadStringValue(val),
                fromFieldData: false,
              });
            }
          });
        } else if (Array.isArray(parsedData)) {
          // Check if it's an array of { name, values/value }
          const hasNamedItems = parsedData.some(function (it) {
            return (
              it &&
              typeof it === "object" &&
              it.name !== undefined &&
              (it.values !== undefined || it.value !== undefined)
            );
          });
          if (hasNamedItems) {
            parsedData.forEach(function (item) {
              if (!item) return;
              if (item.name !== undefined) {
                let v = "";
                if (Array.isArray(item.values)) {
                  v = item.values.join(", ");
                } else if (item.values !== null && item.values !== undefined) {
                  v = String(item.values);
                } else if (item.value !== null && item.value !== undefined) {
                  v = String(item.value);
                }
                entries.push({
                  key: item.name,
                  value: self.cleanLeadStringValue(v),
                  fromFieldData: true,
                });
              } else if (typeof item === "object") {
                Object.keys(item).forEach(function (k) {
                  entries.push({
                    key: k,
                    value: self.cleanLeadStringValue(item[k]),
                    fromFieldData: false,
                  });
                });
              }
            });
          } else {
            parsedData.forEach(function (item, idx) {
              if (item && typeof item === "object") {
                Object.keys(item).forEach(function (k) {
                  entries.push({
                    key: k,
                    value: self.cleanLeadStringValue(item[k]),
                    fromFieldData: false,
                  });
                });
              } else if (item !== null && item !== undefined) {
                entries.push({
                  key: `Item #${idx + 1}`,
                  value: self.cleanLeadStringValue(item),
                  fromFieldData: false,
                });
              }
            });
          }
        } else {
          // Standard flat object
          Object.keys(parsedData).forEach(function (k) {
            const val = parsedData[k];
            entries.push({
              key: k,
              value:
                typeof val === "object"
                  ? JSON.stringify(val)
                  : self.cleanLeadStringValue(val),
              fromFieldData: false,
            });
          });
        }
      }

      if (entries.length === 0 && rawText && String(rawText).trim() !== "") {
        const testJson = self.parseRequirementsJson(rawText);
        if (testJson && typeof testJson === "object") {
          return self.renderModalRequirementsTable(testJson, "");
        }
        $container.html(`
          <div class="v3-plain-notes-editor-wrap">
            <textarea id="editLeadPlainNotes" class="v3-modal-textarea" rows="4" placeholder="Enter special requirements or notes...">${escapeHtml(String(rawText).trim())}</textarea>
            <div class="v3-plain-notes-hint">
              <i class="fi fi-rr-info"></i>
              <span>Plain text notes detected. Click <strong>+ Add Note</strong> above to add structured key-value pairs.</span>
            </div>
          </div>
        `);
        return;
      }

      if (entries.length === 0) {
        $container.html(`
          <div class="v3-req-mapping-empty">
            <i class="fi fi-rr-document"></i>
            <p>No special requirements or JSON keys currently attached.</p>
            <button type="button" class="btn-v3-action btn-outline btn-xs" id="btnAddFirstReqRow">
              <i class="fi fi-rr-plus"></i> Add Note
            </button>
          </div>
        `);
        return;
      }

      // First pass: auto-map entries to lead fields without overriding existing values
      entries.forEach(function (entry) {
        const match = self.matchJsonKeyToLeadField(entry.key);
        if (match) {
          entry.matchField = match.field;
          entry.matchLabel = match.label;
          self.autoFillFieldIfEmpty(match.field, entry.value);
        } else {
          entry.matchField = "do_not_map";
          entry.matchLabel = "Keep in Notes";
        }
      });

      const rowsHtml = entries
        .map(function (entry) {
          return self.buildReqMapRowHtml(
            entry.key,
            entry.value,
            entry.matchField,
            entry.fromFieldData,
          );
        })
        .join("");

      const tableHtml = `
        <div class="v3-req-mapping-table-card">
          <div class="v3-req-mapping-table-header">
            <div class="v3-req-map-th col-key">Form / JSON Key</div>
            <div class="v3-req-map-th col-val">Customer Response / Value</div>
            <div class="v3-req-map-th col-map">Map to Lead Field</div>
            <div class="v3-req-map-th col-act">Action</div>
          </div>
          <div class="v3-req-mapping-table-body" id="reqMappingTableBody">
            ${rowsHtml}
          </div>
        </div>
      `;

      $container.html(tableHtml);
    },

    renderRequirementsSection: function (req) {
      const self = this;
      const hasContent =
        req !== null && req !== undefined && String(req).trim() !== "";

      if (!hasContent) {
        return `
          <div class="v3-requirements-section-wrap" id="requirementsSectionWrap">
            <div class="v3-requirements-header-wrap">
              <div class="v3-requirements-header-left">
                <h4 class="v3-info-section-heading">Special Notes & Requirements</h4>
              </div>
              <div class="v3-requirements-header-actions">
                <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnEditRequirements" data-tooltip="Add or edit requirements">
                  <i class="fi fi-rr-edit"></i> Edit
                </button>
              </div>
            </div>
            <div class="v3-requirements-empty-note">
              <i class="fi fi-rr-info"></i>
              <span>No special notes or requirements specified. Click <strong>Edit</strong> to add details.</span>
            </div>
          </div>
        `;
      }

      const parsedJson = self.parseRequirementsJson(req);
      if (parsedJson) {
        if (!self.hasMeaningfulRequirements(parsedJson)) {
          return `
            <div class="v3-requirements-section-wrap" id="requirementsSectionWrap">
              <div class="v3-requirements-header-wrap">
                <div class="v3-requirements-header-left">
                  <h4 class="v3-info-section-heading">Special Notes & Requirements</h4>
                </div>
                <div class="v3-requirements-header-actions">
                  <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnEditRequirements" data-tooltip="Add or edit requirements">
                    <i class="fi fi-rr-edit"></i> Edit
                  </button>
                </div>
              </div>
              <div class="v3-requirements-empty-note">
                <i class="fi fi-rr-info"></i>
                <span>No special notes or requirements specified. Click <strong>Edit</strong> to add details.</span>
              </div>
            </div>
          `;
        }

        return `
          <div class="v3-requirements-section-wrap" id="requirementsSectionWrap">
            <div class="v3-requirements-header-wrap">
              <div class="v3-requirements-header-left">
                <h4 class="v3-info-section-heading">Special Notes & Requirements</h4>
                <span class="v3-req-type-pill"><i class="fi fi-rr-brackets-curly"></i> Structured Form Data</span>
              </div>
              <div class="v3-requirements-header-actions">
                <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnEditRequirements" data-tooltip="Edit & Map Requirements to Lead Fields">
                  <i class="fi fi-rr-edit"></i> Edit & Map Fields
                </button>
              </div>
            </div>
            <div class="v3-requirements-view-tabular" id="reqTabularView">
              ${self.renderRequirementsTable(parsedJson)}
            </div>
          </div>
        `;
      }

      return `
        <div class="v3-requirements-section-wrap" id="requirementsSectionWrap">
          <div class="v3-requirements-header-wrap">
            <div class="v3-requirements-header-left">
              <h4 class="v3-info-section-heading">Special Notes & Requirements</h4>
            </div>
            <div class="v3-requirements-header-actions">
              <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnEditRequirements" data-tooltip="Edit Special Notes & Requirements">
                <i class="fi fi-rr-edit"></i> Edit
              </button>
            </div>
          </div>
          <div class="v3-requirements-box">${escapeHtml(String(req))}</div>
        </div>
      `;
    },

    renderView: function (lead) {
      const self = this;
      const $container = $("#leadViewMainContainer");

      const $app = $("#leadViewApp");
      const pkgAttr = $app.data("package-id") || $app.attr("data-package-id");
      const isPkg6Attr = $app.data("is-package-six") || $app.attr("data-is-package-six");
      const apiPkgId = (lead && lead.site_package_id) || (self.leadData && self.leadData.site_package_id);
      const globalPkgId = typeof SITE_PACKAGE_ID !== "undefined" ? parseInt(SITE_PACKAGE_ID, 10) : 0;

      self.isPackageSix = (
        pkgAttr == 6 ||
        isPkg6Attr == 1 ||
        isPkg6Attr == "1" ||
        isPkg6Attr === true ||
        apiPkgId == 6 ||
        globalPkgId === 6 ||
        $app.hasClass("is-package-six")
      );

      document.title =
        (lead.name ? lead.name + " - Lead Details" : "Lead Details") +
        " | Dashboard";

      const travel = lead.travel_details || {};
      const quote = lead.quotation || {};

      // 1. Build Agent Select Options
      let agentOptions = `<option value="0">Unassigned</option>`;
      (lead.agents || []).forEach(function (ag) {
        const isSel = lead.agent_id === ag.id ? "selected" : "";
        agentOptions += `<option value="${ag.id}" ${isSel}>${ag.name} (${ag.role})</option>`;
      });

      // 2. Build Timeline Stream
      let timelineHtml = "";
      if (lead.timeline && lead.timeline.length > 0) {
        timelineHtml = '<div class="v3-timeline-stream">';
        lead.timeline.forEach((t) => {
          const type = (t.type || "note").toLowerCase();
          let extraContent = "";
          let cardExtraClass = `v3-timeline-card-${type}`;
          let categoryHtml = "";

          if (type === "followup") {
            const cat = t.category || "General Follow-up";
            categoryHtml = `<span class="v3-timeline-category-pill pill-followup"><i class="fi fi-rr-flag"></i> ${cat}</span>`;

            let timeChip = "";
            if (t.followup_time) {
              timeChip = `
                <div class="v3-timeline-followup-time-box">
                  <i class="fi fi-rr-calendar-clock"></i>
                  <span>Follow-Up Due: <strong>${t.followup_time}</strong></span>
                </div>
              `;
            }

            extraContent = `
              <div class="v3-timeline-followup-body">
                ${timeChip}
                ${t.description ? `<div class="v3-timeline-note-text">${t.description}</div>` : ""}
              </div>
            `;
          } else if (type === "payment") {
            const p = t.payment_data || {};
            const amt = p.amount ? parseFloat(p.amount) : 0;
            const method = p.method || "Standard";
            const ref = p.reference || "";
            const status = (p.status || "verified").toLowerCase();
            const statusLabel =
              status === "pending"
                ? "Pending Verification"
                : status === "rejected"
                  ? "Rejected"
                  : "Verified";
            const statusClass =
              status === "pending"
                ? "status-pending"
                : status === "rejected"
                  ? "status-rejected"
                  : "status-verified";

            categoryHtml = `<span class="v3-status-pill ${statusClass} v3-tl-status-badge">${statusLabel}</span>`;
            extraContent = `
              <div class="v3-timeline-payment-card">
                <div class="v3-tl-pay-top">
                  <div class="v3-tl-pay-amount">₹ ${amt > 0 ? amt.toLocaleString("en-IN", { minimumFractionDigits: 2 }) : t.description ? t.description.replace(/^Recorded payment of /, "") : "0.00"}</div>
                  <span class="v3-tl-pay-method"><i class="fi fi-rr-credit-card"></i> ${method}</span>
                </div>
                ${ref ? `<div class="v3-tl-pay-ref">Reference / Transaction ID: <strong>${ref}</strong></div>` : ""}
              </div>
            `;
          } else if (t.type === "itinerary") {
            const q = t.quotation || {};
            const encId =
              q.encoded_id ||
              (t.description && !isNaN(t.description)
                ? btoa(t.description)
                : "");
            const title =
              q.title ||
              (t.description
                ? isNaN(t.description)
                  ? t.description
                  : `Tour Itinerary #${t.description}`
                : "Tour Itinerary");
            const price = parseFloat(q.price || 0);
            const mrp = parseFloat(q.mrp || 0);
            const viewUrl =
              q.view_url || (encId ? `/view-itinerary/${encId}/` : "#");
            const downloadUrl =
              q.download_url || (encId ? `/download-itinerary/${encId}/` : "#");
            const hasMeta = !!(q.destination || q.duration || q.theme);

            categoryHtml = `<span class="v3-timeline-category-pill pill-itinerary"><i class="fi fi-rr-route"></i> Itinerary Proposal</span>`;
            cardExtraClass = "v3-timeline-card-itinerary";
            extraContent = `
              <div class="v3-timeline-itinerary-card">
                <div class="v3-tin-card-top">
                  <div class="v3-tin-card-info">
                    <div class="v3-tin-title-row">
                      <span class="v3-tin-title"><i class="fi fi-rr-route"></i> ${title}</span>
                      ${q.id || (t.description && !isNaN(t.description)) ? `<span class="v3-tin-chip">#${q.id || t.description}</span>` : ""}
                    </div>
                    ${
                      hasMeta
                        ? `
                      <div class="v3-tin-tags">
                        ${q.destination ? `<span class="v3-tin-tag"><i class="fi fi-rr-marker"></i> ${q.destination}</span>` : ""}
                        ${q.duration ? `<span class="v3-tin-tag"><i class="fi fi-rr-moon"></i> ${q.duration}</span>` : ""}
                        ${q.theme ? `<span class="v3-tin-tag theme"><i class="fi fi-rr-star"></i> ${q.theme}</span>` : ""}
                      </div>
                    `
                        : ""
                    }
                  </div>
                  ${
                    price > 0
                      ? `
                    <div class="v3-tin-price-box">
                      <span class="v3-tin-price-val">₹ ${price.toLocaleString("en-IN")}</span>
                      ${mrp > price ? `<span class="v3-tin-price-mrp">₹ ${mrp.toLocaleString("en-IN")}</span>` : ""}
                    </div>
                  `
                      : ""
                  }
                </div>
                ${
                  encId
                    ? `
                  <div class="v3-tin-actions-row">
                    <a href="${viewUrl}" target="_blank" class="v3-tin-action-btn primary" title="View interactive itinerary webpage">
                      <i class="fi fi-rr-eye"></i> View Itinerary
                    </a>
                    <a href="${downloadUrl}" target="_blank" class="v3-tin-action-btn secondary" title="Download official PDF proposal">
                      <i class="fi fi-rr-file-pdf"></i> Download PDF
                    </a>
                    <button type="button" class="v3-tin-action-btn outline btn-copy-itin-link" data-url="${window.location.origin + viewUrl}" title="Copy itinerary link to clipboard">
                      <i class="fi fi-rr-copy"></i> Copy Link
                    </button>
                  </div>
                `
                    : ""
                }
              </div>
            `.trim();
          } else if (type === "note") {
            categoryHtml = `<span class="v3-timeline-category-pill pill-note"><i class="fi fi-rr-comment"></i> Note</span>`;
            extraContent = `<div class="v3-timeline-note-text">${t.description || "Note logged"}</div>`;
          } else if (type === "update") {
            categoryHtml = `<span class="v3-timeline-category-pill pill-update"><i class="fi fi-rr-refresh"></i> Update</span>`;
            extraContent = `<div class="v3-timeline-update-text">${t.description || "Lead details updated."}</div>`;
          } else if (type === "assign") {
            categoryHtml = `<span class="v3-timeline-category-pill pill-assign"><i class="fi fi-rr-user-check"></i> Assignment</span>`;
            extraContent = `<div class="v3-timeline-assign-text">${t.description || "Lead assigned to agent."}</div>`;
          } else {
            extraContent = t.description
              ? `<div class="v3-timeline-note-text">${t.description}</div>`
              : "";
          }

          timelineHtml += `
            <div class="v3-timeline-row type-${type}">
              <div class="v3-timeline-bullet"><i class="${t.icon || "fi fi-rr-comment"}"></i></div>
              <div class="v3-timeline-row-meta">
                <span class="v3-timeline-tag">${t.title}</span>
                ${categoryHtml}
                <span>•</span>
                <span class="v3-timeline-author">${t.user_name}</span>
                <span>•</span>
                <span class="v3-timeline-time-ago">${t.time_relative || t.time}</span>
              </div>
              ${extraContent ? `<div class="v3-timeline-card-content ${cardExtraClass}">${extraContent}</div>` : ""}
            </div>
          `;
        });
        timelineHtml += "</div>";
      } else {
        timelineHtml = `
          <div class="v3-leads-empty-state v3-empty-compact">
            <div class="v3-empty-icon-wrap v3-empty-icon-compact"><i class="fi fi-rr-comment-alt"></i></div>
            <h4 class="v3-empty-title">No Activity Logged Yet</h4>
            <p class="v3-empty-subtitle">Use the composer above to write customer notes or set follow-up reminders.</p>
          </div>
        `;
      }

      // 3. Build Payment Ledger
      self.leadPayments = lead.payments || [];
      let totalPaid = 0;
      let totalPendingVerification = 0;

      self.leadPayments.forEach((p) => {
        const pStatus = String(p.status || "verified")
          .toLowerCase()
          .trim();
        const amt = parseFloat(p.amount || 0);
        if (pStatus === "verified") {
          totalPaid += amt;
        } else if (
          pStatus === "pending" ||
          pStatus === "pending approval" ||
          pStatus === "unverified"
        ) {
          totalPendingVerification += amt;
        }
      });

      // Calculate Total Package Value & Pending Amount
      let quotePrice = parseFloat(
        (lead.quotation && lead.quotation.price) ||
          (self.leadData &&
            self.leadData.quotation &&
            self.leadData.quotation.price) ||
          0,
      );
      if (quotePrice <= 0 && lead.timeline && lead.timeline.length > 0) {
        lead.timeline.forEach((t) => {
          if (
            t.quotation &&
            t.quotation.price &&
            parseFloat(t.quotation.price) > 0 &&
            quotePrice <= 0
          ) {
            quotePrice = parseFloat(t.quotation.price);
          }
        });
      }

      let pendingAmount = 0;
      let pendingHtml = "";

      if (quotePrice > 0) {
        pendingAmount = Math.max(0, quotePrice - totalPaid);
        if (pendingAmount > 0) {
          pendingHtml = `
            <div class="v3-pay-stat-pill stat-pending">
              <i class="fi fi-rr-clock"></i>
              <span class="v3-pay-stat-label">Pending</span>
              <span class="v3-pay-stat-val">₹ ${pendingAmount.toLocaleString("en-IN")}</span>
            </div>
          `;
        } else {
          pendingHtml = `
            <div class="v3-pay-stat-pill stat-settled">
              <i class="fi fi-rr-badge-check"></i>
              <span class="v3-pay-stat-label">Pending</span>
              <span class="v3-pay-stat-val">₹ 0 (Paid in Full)</span>
            </div>
          `;
        }
      } else if (totalPendingVerification > 0) {
        pendingHtml = `
          <div class="v3-pay-stat-pill stat-pending">
            <i class="fi fi-rr-clock"></i>
            <span class="v3-pay-stat-label">Pending</span>
            <span class="v3-pay-stat-val">₹ ${totalPendingVerification.toLocaleString("en-IN")}</span>
          </div>
        `;
      } else {
        pendingHtml = `
          <div class="v3-pay-stat-pill stat-pending">
            <i class="fi fi-rr-clock"></i>
            <span class="v3-pay-stat-label">Pending</span>
            <span class="v3-pay-stat-val">₹ 0</span>
          </div>
        `;
      }

      function getPaymentMethodBadge(method) {
        const m = (method || "").toLowerCase();
        let icon = "fi-rr-wallet";
        let cls = "badge-method-default";
        if (m.includes("upi") || m.includes("qr")) {
          icon = "fi-rr-qr-scan";
          cls = "badge-method-upi";
        } else if (
          m.includes("bank") ||
          m.includes("neft") ||
          m.includes("imps")
        ) {
          icon = "fi-rr-bank";
          cls = "badge-method-bank";
        } else if (m.includes("card")) {
          icon = "fi-rr-credit-card";
          cls = "badge-method-card";
        } else if (m.includes("cash")) {
          icon = "fi-rr-money-bill-wave";
          cls = "badge-method-cash";
        } else if (m.includes("cheque") || m.includes("dd")) {
          icon = "fi-rr-document";
          cls = "badge-method-cheque";
        }
        return `<span class="v3-payment-method-chip ${cls}"><i class="fi ${icon}"></i> ${method || "Standard"}</span>`;
      }

      let paymentsHtml = "";
      if (self.leadPayments.length > 0) {
        paymentsHtml = `
          <div class="v3-payments-container-card">
            <div class="v3-payments-header-bar">
              <div class="v3-payments-summary-group">
                ${
                  quotePrice > 0
                    ? `
                  <div class="v3-pay-stat-pill stat-package">
                    <i class="fi fi-rr-box-alt"></i>
                    <span class="v3-pay-stat-label">Package Total</span>
                    <span class="v3-pay-stat-val">₹ ${quotePrice.toLocaleString("en-IN")}</span>
                  </div>
                `
                    : ""
                }
                <div class="v3-pay-stat-pill stat-collected">
                  <i class="fi fi-rr-check-circle"></i>
                  <span class="v3-pay-stat-label">Collected</span>
                  <span class="v3-pay-stat-val">₹ ${totalPaid.toLocaleString("en-IN")}</span>
                </div>
                ${pendingHtml}
                <div class="v3-pay-stat-pill stat-entries">
                  <i class="fi fi-rr-receipt"></i>
                  <span class="v3-pay-stat-label">Entries</span>
                  <span class="v3-pay-stat-val">${self.leadPayments.length}</span>
                </div>
              </div>
              ${
                !self.isPackageSix
                  ? `
              <button type="button" class="btn-v3-action btn-green" id="btnOpenPaymentModalTab" data-tooltip="Record Payment">
                <i class="fi fi-rr-plus"></i> Record Payment
              </button>
              `
                  : ""
              }
            </div>
            <div class="v3-table-responsive-wrapper">
              <table class="v3-payments-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference / UTR</th>
                    <th>Receipt Proof</th>
                    <th>Recorded By</th>
                    <th>Status</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
        `;
        const userRole = (
          (self.leadData && self.leadData.access_type) ||
          $("#leadViewApp").data("user-role") ||
          (typeof siteAdminType !== "undefined" ? siteAdminType : "")
        )
          .toLowerCase()
          .trim();
        const canVerifyPayment =
          userRole === "administrator" ||
          userRole === "accountant" ||
          userRole === "superadmin";

        self.leadPayments.forEach((p, idx) => {
          const imgUrl =
            p.image_url ||
            (p.image && typeof p.image === "object"
              ? p.image.path
              : typeof p.image === "string" &&
                  (p.image.includes("/") || p.image.includes("."))
                ? p.image
                : "");
          const receiptHtml = imgUrl
            ? `<button type="button" class="v3-payment-receipt-btn btn-view-payment-receipt" data-payment-idx="${idx}" title="Click to view receipt image and payment details">
                <i class="fi fi-rr-picture"></i> View Receipt
               </button>`
            : `<span class="v3-receipt-none-chip"><i class="fi fi-rr-cross-small"></i> None</span>`;

          const refHtml = p.reference
            ? `<span class="v3-pay-ref-text monospace" title="${p.reference}">${p.reference}</span>`
            : `<span class="v3-text-muted">-</span>`;

          let statusBadge = `<span class="v3-status-pill status-verified"><i class="fi fi-rr-check"></i> Verified</span>`;
          if (p.status === "pending") {
            if (canVerifyPayment) {
              statusBadge = `
                <div style="display: inline-flex; align-items: center; gap: 6px;">
                  <span class="v3-status-pill status-pending"><i class="fi fi-rr-clock"></i> Pending</span>
                  <button type="button" class="btn-v3-action btn-green btn-xs btn-lead-approve-payment" data-note-id="${p.id}" title="Approve & Verify Payment">
                    <i class="fi fi-rr-check"></i> Verify
                  </button>
                </div>
              `;
            } else {
              statusBadge = `<span class="v3-status-pill status-pending"><i class="fi fi-rr-clock"></i> Pending</span>`;
            }
          } else if (p.status === "rejected") {
            statusBadge = `<span class="v3-status-pill status-rejected"><i class="fi fi-rr-cross-circle"></i> Rejected</span>`;
          }

          paymentsHtml += `
            <tr class="v3-payment-row">
              <td><span class="v3-pay-date-text"><i class="fi fi-rr-calendar"></i> ${p.date}</span></td>
              <td><strong class="v3-pay-amount-text">₹ ${parseFloat(p.amount).toLocaleString("en-IN")}</strong></td>
              <td>${getPaymentMethodBadge(p.method)}</td>
              <td>${refHtml}</td>
              <td>${receiptHtml}</td>
              <td><span class="v3-pay-recorded-by"><i class="fi fi-rr-user"></i> ${p.recorded_by}</span></td>
              <td>${statusBadge}</td>
              <td><span class="v3-pay-remarks-text">${p.notes || "-"}</span></td>
            </tr>
          `;
        });
        paymentsHtml += `</tbody></table></div></div>`;
      } else {
        paymentsHtml = `
          <div class="v3-leads-empty-state v3-empty-compact">
            <div class="v3-empty-icon-wrap v3-empty-icon-compact"><i class="fi fi-rr-indian-rupee-sign"></i></div>
            <h4 class="v3-empty-title">No Payments Recorded</h4>
            <p class="v3-empty-subtitle">No advance deposit or final settlement payment has been recorded yet.</p>
            ${
              !self.isPackageSix
                ? `
            <button type="button" class="btn-v3-action btn-green" id="btnOpenPaymentModal" data-tooltip="Record Payment">
              <i class="fi fi-rr-plus"></i> Record Payment
            </button>
            `
                : ""
            }
          </div>
        `;
      }

      const kpis = lead.kpis || {
        next_followup: {
          is_scheduled: false,
          time_formatted: "Not Scheduled",
          status_badge: "none",
          label: "Not Scheduled",
        },
        lead_age: {
          days: 0,
          text: "Active Today",
        },
        touchpoints: {
          total: lead.timeline ? lead.timeline.length : 0,
          summary: `${lead.timeline ? lead.timeline.length : 0} Touchpoints`,
        },
      };
      const followup = kpis.next_followup;
      const leadAge = kpis.lead_age;
      const touchpoints = kpis.touchpoints;

      const st = parseInt(lead.status || 0);
      const isAssigned = parseInt(lead.agent_id || 0) > 0;

      const defaultFollowupMoment = self.getDefaultFollowupDateTime();
      const defaultFollowupStr =
        typeof moment !== "undefined" &&
        defaultFollowupMoment &&
        defaultFollowupMoment.format
          ? defaultFollowupMoment.format("YYYY-MM-DD, hh:mm A")
          : "";

      // Role check: Only Administrator and Operational can see/manage Assigned Representative
      const userRole = (
        lead.access_type ||
        $("#leadViewApp").data("user-role") ||
        ""
      )
        .toLowerCase()
        .trim();
      const canManageAgent =
        userRole === "administrator" ||
        userRole === "operations" ||
        userRole === "superadmin" ||
        userRole === "operational";

      const agentSidebarHtml = canManageAgent
        ? `
          <!-- Assigned Representative Box (Administrator & Operational Only) -->
          <div class="v3-sidebar-card">
            <div class="v3-sidebar-card-title">
              <span>Assigned Representative</span>
              <i class="fi fi-rr-user-gear"></i>
            </div>
            <div class="v3-assigned-agent-wrapper">
              <select id="leadDetailAgentSelect" class="leads-filter-select v3-select-full">
                <option value="0">-- Unassigned --</option>
                ${(lead.agents || [])
                  .map((ag) => {
                    const agId = parseInt(ag.id || ag.user_id || 0);
                    const isSelected = parseInt(lead.agent_id || 0) === agId;
                    return `<option value="${agId}" ${isSelected ? "selected" : ""}>${ag.name} (${ag.role})</option>`;
                  })
                  .join("")}
              </select>
            </div>
          </div>
        `
        : "";

      // 4. Complete Unified CRM Assembly
      const html = `
        <div class="v3-lead-view-wrapper">
          <!-- 1. Minimal Hero Header Banner -->
          <div class="v3-lead-hero-banner">
            <div class="v3-lead-hero-left">
              <a href="/dashboard/leads/list/" class="v3-lead-back-circle" id="btnBackToLeads" data-tooltip="Back to Leads Pipeline">
                <i class="fi fi-rr-arrow-left"></i>
              </a>

              <div class="v3-lead-hero-title-wrap">
                <div class="v3-lead-hero-name-row">
                  <h2 class="v3-lead-hero-name">${lead.name}</h2>
                  <div class="v3-minimal-status-picker">
                    <button type="button" class="v3-minimal-status-trigger ${lead.status_class}" id="btnMinimalStatusTrigger" title="Click to change status">
                      <span class="lead-dot" style="background-color: ${lead.status_color}"></span>
                      <span>${lead.status_label}</span>
                      <i class="fi fi-rr-angle-small-down"></i>
                    </button>

                    <div class="v3-status-dropdown-menu" id="statusDropdownMenu">
                      <div class="v3-status-opt-header">Lifecycle Flow</div>
                      <div class="v3-status-opt ${st === 0 ? "selected" : ""}" data-status="0">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot initiate"></span>
                          <span>Initiate (0)</span>
                        </div>
                        ${st === 0 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 2 ? "selected" : ""}" data-status="2">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot pending"></span>
                          <span>Pending Assigned (2)</span>
                        </div>
                        ${st === 2 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 9 ? "selected" : ""}" data-status="9">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot itinerary-sent"></span>
                          <span>Itinerary Sent (9)</span>
                        </div>
                        ${st === 9 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 3 ? "selected" : ""}" data-status="3">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot followup"></span>
                          <span>Follow-up (3)</span>
                        </div>
                        ${st === 3 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 10 ? "selected" : ""}" data-status="10">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot quotation-approved"></span>
                          <span>Quotation Approved (10)</span>
                        </div>
                        ${st === 10 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 6 ? "selected" : ""}" data-status="6">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot payment-initiated"></span>
                          <span>Payment Initiated (6)</span>
                        </div>
                        ${st === 6 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 12 ? "selected" : ""}" data-status="12">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot payment-received"></span>
                          <span>Payment Received (12)</span>
                        </div>
                        ${st === 12 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 8 ? "selected" : ""}" data-status="8">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot upay-done"></span>
                          <span>Upay Done (8)</span>
                        </div>
                        ${st === 8 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 1 ? "selected" : ""}" data-status="1">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot success"></span>
                          <span>Success (1)</span>
                        </div>
                        ${st === 1 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 11 ? "selected" : ""}" data-status="11">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot voucher-issued"></span>
                          <span>Voucher Issued (11)</span>
                        </div>
                        ${st === 11 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 13 ? "selected" : ""}" data-status="13">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot trip-progress"></span>
                          <span>Trip in Progress (13)</span>
                        </div>
                        ${st === 13 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 14 ? "selected" : ""}" data-status="14">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot trip-completed"></span>
                          <span>Trip Completed (14)</span>
                        </div>
                        ${st === 14 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 15 ? "selected" : ""}" data-status="15">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot closed"></span>
                          <span>Closed (15)</span>
                        </div>
                        ${st === 15 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>

                      <div class="v3-status-opt-header">Exception & Drop-Off</div>
                      <div class="v3-status-opt ${st === 4 ? "selected" : ""}" data-status="4">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot failed"></span>
                          <span>Failed (4)</span>
                        </div>
                        ${st === 4 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 5 ? "selected" : ""}" data-status="5">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot duplicate"></span>
                          <span>Duplicate (5)</span>
                        </div>
                        ${st === 5 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 7 ? "selected" : ""}" data-status="7">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot sales-request"></span>
                          <span>Sent for Sales Request (7)</span>
                        </div>
                        ${st === 7 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 16 ? "selected" : ""}" data-status="16">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot cancelled"></span>
                          <span>Cancelled (16)</span>
                        </div>
                        ${st === 16 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 17 ? "selected" : ""}" data-status="17">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot lost"></span>
                          <span>Lost (17)</span>
                        </div>
                        ${st === 17 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 18 ? "selected" : ""}" data-status="18">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot refund"></span>
                          <span>Payment Refund (18)</span>
                        </div>
                        ${st === 18 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="v3-lead-hero-meta">
                  <span class="v3-hero-meta-item"><i class="fi fi-rr-calendar"></i> Added ${lead.created_at || "Recently"}</span>
                  <span class="v3-meta-sep">•</span>
                  <span class="v3-hero-meta-item"><i class="fi fi-rr-time-check"></i> Updated ${lead.updated_at || lead.created_at || "Recently"}</span>
                  <span class="v3-meta-sep">•</span>
                  <span class="v3-hero-meta-badge source"><i class="fi fi-rr-globe"></i> ${lead.source}</span>
                  ${lead.campaign ? `<span class="v3-meta-sep">•</span><span class="v3-hero-meta-badge campaign"><i class="fi fi-rr-bullhorn"></i> ${lead.campaign}</span>` : ""}
                </div>
              </div>
            </div>

            <div class="v3-lead-hero-actions">
              ${
                lead.wa_mobile
                  ? `
                <a href="https://wa.me/${lead.wa_mobile}?text=Hello%20${encodeURIComponent(lead.first_name || lead.name)}" target="_blank" class="v3-btn-contact-action wa" data-tooltip="Chat on WhatsApp">
                  ${WA_SVG} <span>WhatsApp</span>
                </a>
              `
                  : ""
              }
              ${
                lead.mobile
                  ? `
                <a href="tel:${lead.mobile}" class="v3-btn-contact-action call" data-tooltip="Call Primary Phone">
                  <i class="fi fi-rr-phone-call"></i> <span>${lead.mobile}</span>
                </a>
              `
                  : ""
              }
              <button type="button" class="btn-v3-action btn-outline" id="btnHeroEditLead" data-tooltip="Edit Lead & Requirements">
                <i class="fi fi-rr-edit"></i> <span>Edit Lead</span>
              </button>
              ${
                isAssigned
                  ? `
                ${
                  !self.isPackageSix
                    ? `
                <button type="button" class="btn-v3-action btn-outline" id="btnHeroSendItinerary" data-tooltip="Search, Customize & Send Itinerary">
                  <i class="fi fi-rr-paper-plane"></i> <span>Send Itinerary</span>
                </button>
                `
                    : ""
                }
                <button type="button" class="btn-v3-action btn-orange" id="btnHeroSetFollowUp" data-tooltip="Set Follow-Up Reminder">
                  <i class="fi fi-rr-alarm-clock"></i> <span>Follow-Up</span>
                </button>
                ${
                  st !== 1
                    ? `
                  <button type="button" class="btn-v3-action btn-green" id="btnHeroConvertToSale" data-tooltip="Convert Lead to Confirmed Sale">
                    <i class="fi fi-rr-check"></i> <span>Convert to Sale</span>
                  </button>
                `
                    : ""
                }
              `
                  : ""
              }
            </div>
          </div>

          <!-- 2. Top 5-Card Snapshot Grid -->
          <div class="v3-lead-snapshot-grid">
            <!-- Card 1: Package / Requirement -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Package / Requirement</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-plane"></i></div>
              </div>
              <div class="v3-snapshot-value" title="${lead.subject || "Custom Package"}">${lead.subject || "Custom Package"}</div>
              <div class="v3-snapshot-sub"><i class="fi fi-rr-calendar"></i> Travel: ${travel.date_of_travel || "Flexible"}</div>
            </div>

            <!-- Card 2: Duration & Guests -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Duration & Guests</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-users"></i></div>
              </div>
              <div class="v3-snapshot-value">${travel.duration_days > 0 ? `${travel.duration_days} Nights` : "Duration N/A"}</div>
              <div class="v3-snapshot-sub">${travel.adults || 1} Adults • ${travel.children || 0} Kids • ${travel.infants || 0} Infants</div>
            </div>

            <!-- Card 3: Next Follow-Up (KPI 1) -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Next Follow-Up</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-alarm-clock"></i></div>
              </div>
              <div class="v3-snapshot-value" title="${followup.time_formatted}">${followup.is_scheduled ? followup.time_formatted : "No Follow-Up Set"}</div>
              <div class="v3-snapshot-sub">
                <span class="v3-kpi-pill ${followup.status_badge}">${followup.label}</span>
              </div>
            </div>

            <!-- Card 4: Lead Age & Activity (KPI 2 & 3) -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Lead Age & Activity</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-time-past"></i></div>
              </div>
              <div class="v3-snapshot-value">${leadAge.text}</div>
              <div class="v3-snapshot-sub"><i class="fi fi-rr-comment-dots"></i> ${touchpoints.summary || `${touchpoints.total} Touchpoints`}</div>
            </div>

            <!-- Card 5: Quotation & Value -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Quotation Value</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-receipt"></i></div>
              </div>
              <div class="v3-snapshot-value">₹ ${parseFloat(quote.price || 0).toLocaleString("en-IN")}</div>
              <div class="v3-snapshot-sub">
                ${quote.min_payment > 0 ? `Min. Deposit: ₹ ${parseFloat(quote.min_payment).toLocaleString("en-IN")}` : "Direct Payment Available"}
              </div>
            </div>
          </div>

          <!-- 3. Main 2-Column Section -->
          <div class="v3-lead-main-layout">
            <!-- Left Area: Tabs for Details, Notes, and Payments -->
            <div class="v3-lead-tabs-card">
              <div class="v3-tabs-header-bar">
                <button type="button" class="v3-tab-link active" data-tab="tab-details">
                  <i class="fi fi-rr-info"></i> Trip & Lead Details
                </button>
                <button type="button" class="v3-tab-link" data-tab="tab-activity">
                  <i class="fi fi-rr-time-past"></i> Activity & Notes (${lead.timeline ? lead.timeline.length : 0})
                </button>
                <button type="button" class="v3-tab-link ${!isAssigned ? "v3-tab-disabled" : ""}" data-tab="tab-payments" ${!isAssigned ? 'disabled title="Lead must be assigned to an agent to access payments"' : ""}>
                  <i class="fi fi-rr-money-check"></i> Payments (${lead.payments ? lead.payments.length : 0}) ${!isAssigned ? '<i class="fi fi-rr-lock v3-tab-lock-icon"></i>' : ""}
                </button>
              </div>

              <div class="v3-tab-body">
                <!-- Tab 1: Trip & Lead Details -->
                <div class="v3-lead-tab-pane active" id="tab-details">
                  <div class="v3-flex-end-row">
                    <button type="button" class="btn-v3-action btn-outline" id="btnTabEditLead">
                      <i class="fi fi-rr-edit"></i> Edit Details
                    </button>
                  </div>

                  <h4 class="v3-info-section-heading">Customer Information</h4>
                  <div class="v3-data-grid">
                    <div class="v3-data-item">
                      <span class="v3-data-label">Customer Name</span>
                      <span class="v3-data-val">${lead.name}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Email Address</span>
                      <span class="v3-data-val">${lead.email ? `<a href="mailto:${lead.email}">${lead.email}</a>` : "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Primary Mobile</span>
                      <span class="v3-data-val">${lead.mobile ? `<a href="tel:${lead.mobile}">${lead.mobile}</a>` : "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Alternate Mobile</span>
                      <span class="v3-data-val">${lead.alt_mobile || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Current City / Location</span>
                      <span class="v3-data-val">${travel.current_location || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Inquiry Date</span>
                      <span class="v3-data-val">${lead.created_at || "-"}</span>
                    </div>
                  </div>

                  <h4 class="v3-info-section-heading">Travel Logistics</h4>
                  <div class="v3-data-grid">
                    <div class="v3-data-item">
                      <span class="v3-data-label">Package / Destination</span>
                      <span class="v3-data-val">${lead.subject || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Date of Travel</span>
                      <span class="v3-data-val">${travel.date_of_travel || "Flexible"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Trip Duration</span>
                      <span class="v3-data-val">${travel.duration_days > 0 ? `${travel.duration_days} Nights` : "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Total Travelers</span>
                      <span class="v3-data-val">${travel.adults || 1} Adults, ${travel.children || 0} Kids, ${travel.infants || 0} Infants</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Pickup Location</span>
                      <span class="v3-data-val">${travel.pickup_location || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Drop Location</span>
                      <span class="v3-data-val">${travel.drop_location || "-"}</span>
                    </div>
                  </div>

                  ${self.renderRequirementsSection(travel.requirements)}
                </div>

                <!-- Tab 2: Activity & Notes (HubSpot/Linear Style) -->
                <div class="v3-lead-tab-pane" id="tab-activity">
                  <!-- Quick Composer Box -->
                  ${
                    isAssigned
                      ? `
                  <div class="v3-note-composer-card">
                    <div class="v3-composer-type-nav">
                      <button type="button" class="v3-composer-type-btn active" data-type="note">
                        <i class="fi fi-rr-comment-alt"></i> Add Note
                      </button>
                      <button type="button" class="v3-composer-type-btn" data-type="followup">
                        <i class="fi fi-rr-alarm-clock"></i> Set Follow-Up
                      </button>
                    </div>

                    <textarea id="composerNoteInput" class="v3-composer-input" placeholder="Type a note, call log, or customer update..."></textarea>

                    <div class="v3-composer-followup-row" id="composerFollowupRow">
                      <div class="v3-followup-schedule-card">
                        <div class="v3-followup-presets-wrap">
                          <span class="v3-preset-label"><i class="fi fi-rr-bolt"></i> Quick Presets:</span>
                          <div class="v3-preset-chips" data-target="#composerFollowupTime">
                            <button type="button" class="v3-preset-chip" data-preset="in_2_hours"><i class="fi fi-rr-bolt"></i> In 2 Hours</button>
                            <button type="button" class="v3-preset-chip active" data-preset="tomorrow_morning"><i class="fi fi-rr-sun"></i> Tomorrow 10:30 AM</button>
                            <button type="button" class="v3-preset-chip" data-preset="tomorrow_afternoon"><i class="fi fi-rr-sun"></i> Tomorrow 3:00 PM</button>
                            <button type="button" class="v3-preset-chip" data-preset="tomorrow_evening"><i class="fi fi-rr-sunset"></i> Tomorrow 6:00 PM</button>
                            <button type="button" class="v3-preset-chip" data-preset="in_2_days"><i class="fi fi-rr-calendar"></i> In 2 Days 11:00 AM</button>
                            <button type="button" class="v3-preset-chip" data-preset="next_monday"><i class="fi fi-rr-calendar-clock"></i> Next Mon 10:30 AM</button>
                          </div>
                        </div>
                        <div class="v3-datetime-split-grid">
                          <div class="v3-datetime-col">
                            <label class="v3-sub-label" for="composerFollowupDate"><i class="fi fi-rr-calendar"></i> Date</label>
                            <div class="v3-native-input-wrap">
                              <input type="date" id="composerFollowupDate" class="v3-native-datetime-input">
                            </div>
                          </div>
                          <div class="v3-datetime-col">
                            <label class="v3-sub-label" for="composerFollowupTimeInput"><i class="fi fi-rr-clock"></i> Time</label>
                            <div class="v3-native-input-wrap">
                              <input type="time" id="composerFollowupTimeInput" class="v3-native-datetime-input">
                            </div>
                          </div>
                        </div>
                        <input type="hidden" id="composerFollowupTime" value="${defaultFollowupStr}">
                        <div class="v3-time-quick-pills" data-target="#composerFollowupTimeInput">
                          <span class="v3-time-pills-label">Popular slots:</span>
                          <button type="button" class="v3-time-pill" data-time="10:00">10:00 AM</button>
                          <button type="button" class="v3-time-pill active" data-time="10:30">10:30 AM</button>
                          <button type="button" class="v3-time-pill" data-time="14:30">02:30 PM</button>
                          <button type="button" class="v3-time-pill" data-time="16:00">04:00 PM</button>
                          <button type="button" class="v3-time-pill" data-time="18:00">06:00 PM</button>
                          <button type="button" class="v3-time-pill" data-time="19:30">07:30 PM</button>
                        </div>
                        <div class="v3-slot-status-box" id="composerSlotStatus"></div>
                      </div>
                      <div class="v3-modal-form-group" style="margin-top: 10px;">
                        <label class="v3-modal-label">Reason / Objective *</label>
                        <select id="composerFollowupReason" class="leads-filter-select v3-select-full">
                          <option value="Interested - Price Discussion">Price Discussion</option>
                          <option value="Itinerary Customization">Itinerary Customization</option>
                          <option value="Call Back Request">Call Back Request</option>
                          <option value="Payment Follow-up">Payment Follow-up</option>
                          <option value="General Follow-up" selected>General Follow-up</option>
                        </select>
                      </div>
                    </div>

                    <div class="v3-composer-footer">
                      <button type="button" class="btn-v3-action btn-green" id="btnSubmitComposer">
                        <i class="fi fi-rr-check"></i> Post Update
                      </button>
                    </div>
                  </div>
                  `
                      : `
                  <div class="v3-composer-disabled">
                    <div class="v3-unassigned-banner">
                      <div class="v3-unassigned-banner-icon"><i class="fi fi-rr-lock"></i></div>
                      <div class="v3-unassigned-banner-text">
                        <h5>Activity & Notes Form Disabled</h5>
                        <p>This lead is currently unassigned. Please assign an agent or representative to log call notes or schedule follow-up reminders.</p>
                      </div>
                    </div>
                  </div>
                  `
                  }

                  <!-- Timeline Stream -->
                  ${timelineHtml}
                </div>

                <!-- Tab 3: Payment History -->
                <div class="v3-lead-tab-pane" id="tab-payments">
                  ${
                    isAssigned
                      ? `
                    ${paymentsHtml}
                  `
                      : `
                    <div class="v3-leads-empty-state v3-empty-compact">
                      <div class="v3-empty-icon-wrap v3-empty-icon-compact"><i class="fi fi-rr-lock"></i></div>
                      <h4 class="v3-empty-title">Payments Disabled</h4>
                      <p class="v3-empty-subtitle">This lead is unassigned. Please assign an agent or representative before recording or managing payments.</p>
                    </div>
                  `
                  }
                </div>
              </div>
            </div>

            <!-- Right Area: Sidebar Panels -->
            <div class="v3-sidebar-panel">
              ${agentSidebarHtml}

              <!-- Fast Actions Sidebar for Telecallers -->
              <div class="v3-sidebar-card v3-fast-actions-card">
                <div class="v3-sidebar-card-title">
                  <span>Fast Actions</span>
                  <i class="fi fi-rr-bolt"></i>
                </div>
                ${
                  isAssigned
                    ? `
                  <div class="v3-fast-actions-grid">
                    ${
                      lead.wa_mobile
                        ? `
                      <a href="https://wa.me/${lead.wa_mobile}?text=Hello%20${encodeURIComponent(lead.first_name || lead.name)}" target="_blank" class="btn-fast-tile tile-wa" data-tooltip="Chat on WhatsApp">
                        <div class="fast-tile-icon">${WA_SVG}</div>
                        <div class="fast-tile-meta">
                          <span class="fast-tile-title">WhatsApp Chat</span>
                          <span class="fast-tile-sub">Send instant message</span>
                        </div>
                      </a>
                    `
                        : ""
                    }

                    ${
                      lead.mobile
                        ? `
                      <a href="tel:${lead.mobile}" class="btn-fast-tile tile-call" data-tooltip="Call customer mobile">
                        <div class="fast-tile-icon"><i class="fi fi-rr-phone-call"></i></div>
                        <div class="fast-tile-meta">
                          <span class="fast-tile-title">Call Customer</span>
                          <span class="fast-tile-sub">${lead.mobile}</span>
                        </div>
                      </a>
                    `
                        : ""
                    }

                    <button type="button" class="btn-fast-tile tile-followup" id="btnSideSetFollowUp" data-tooltip="Schedule next callback reminder">
                      <div class="fast-tile-icon"><i class="fi fi-rr-alarm-clock"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Schedule Follow-Up</span>
                        <span class="fast-tile-sub">Set reminder / call later</span>
                      </div>
                    </button>

                    ${
                      !self.isPackageSix
                        ? `
                    <button type="button" class="btn-fast-tile tile-itinerary" id="btnSideSendItinerary" data-tooltip="Create and send tour proposal">
                      <div class="fast-tile-icon"><i class="fi fi-rr-paper-plane"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Send Tour Itinerary</span>
                        <span class="fast-tile-sub">Build & share quotation</span>
                      </div>
                    </button>
                    `
                        : ""
                    }

                    <button type="button" class="btn-fast-tile tile-edit" id="btnSideEditLead" data-tooltip="Edit customer information and trip requirements">
                      <div class="fast-tile-icon"><i class="fi fi-rr-edit"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Edit Lead & Travel</span>
                        <span class="fast-tile-sub">Update customer needs</span>
                      </div>
                    </button>

                    ${
                      !self.isPackageSix
                        ? `
                    <button type="button" class="btn-fast-tile tile-pay-record" id="btnSideRecordPayment" data-tooltip="Record payment received from client">
                      <div class="fast-tile-icon"><i class="fi fi-rr-indian-rupee-sign"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Record Payment</span>
                        <span class="fast-tile-sub">Log received payment</span>
                      </div>
                    </button>

                    <button type="button" class="btn-fast-tile tile-pay-link" id="btnSideSendPaymentLink" data-tooltip="Generate and share payment link">
                      <div class="fast-tile-icon"><i class="fi fi-rr-link"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Send Payment Link</span>
                        <span class="fast-tile-sub">Request online deposit</span>
                      </div>
                    </button>
                    `
                        : ""
                    }

                    ${
                      st !== 1
                        ? `
                      <button type="button" class="btn-fast-tile tile-convert" id="btnSideConvertToSale" data-tooltip="Mark lead as confirmed booking">
                        <div class="fast-tile-icon"><i class="fi fi-rr-check-circle"></i></div>
                        <div class="fast-tile-meta">
                          <span class="fast-tile-title">Convert to Confirmed Sale</span>
                          <span class="fast-tile-sub">Confirm tour booking</span>
                        </div>
                      </button>
                    `
                        : ""
                    }

                    <button type="button" class="btn-fast-tile tile-close" id="btnSideCloseLeadModal" data-tooltip="Archive or close lead with reason">
                      <div class="fast-tile-icon"><i class="fi fi-rr-cross-circle"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Close / Lost Lead</span>
                        <span class="fast-tile-sub">Mark cancelled / not interested</span>
                      </div>
                    </button>
                  </div>
                `
                    : `
                  <div class="v3-unassigned-lead-banner">
                    <div class="v3-unassigned-icon"><i class="fi fi-rr-lock"></i></div>
                    <h5 class="v3-unassigned-title">Lead Unassigned</h5>
                    <p class="v3-unassigned-desc">Fast Actions will be visible and enabled once this lead is assigned to a representative.</p>
                  </div>
                `
                }
              </div>
            </div>
          </div>
        </div>
      `;

      $container.html(html);

      // Initialize Single Datepickers & Follow-up DateTime Pickers
      self.initSingleDatePickers();
      self.initDateTimePicker();

      if (typeof $.fn.select2 !== "undefined") {
        if (canManageAgent && $("#leadDetailAgentSelect").length) {
          $("#leadDetailAgentSelect").select2({
            dropdownAutoWidth: true,
            width: "100%",
          });
        }
        if ($("#composerFollowupReason").length) {
          $("#composerFollowupReason").select2({
            dropdownAutoWidth: true,
            width: "100%",
          });
        }
      }

      // Check & schedule upcoming follow-up reminder if assigned to current user
      if (
        lead.kpis &&
        lead.kpis.next_followup &&
        lead.kpis.next_followup.is_scheduled &&
        lead.kpis.next_followup.raw_time
      ) {
        self.scheduleClientReminder(
          lead.kpis.next_followup.raw_time,
          lead.name || "Customer",
          lead.agent_id,
          self.leadId,
        );
      }
    },

    // Load and cache active hotel properties, vehicles, and stay cities
    // Load and cache active hotel properties, vehicles, and stay cities from Cities API
    loadItineraryMeta: function (callback) {
      const self = this;
      let pending = 3;
      const done = () => {
        pending--;
        if (pending <= 0 && typeof callback === "function") {
          callback();
        }
      };

      // 1. Fetch all cities from Cities API
      if (self.availableStayCities && self.availableStayCities.length > 0) {
        pending--;
      } else {
        const countryParam =
          (self.selectedPackage &&
            (self.selectedPackage.tour_package_country ||
              self.selectedPackage.country)) ||
          (self.leadData &&
            self.leadData.travel_details &&
            self.leadData.travel_details.country) ||
          "";
        const citiesUrl = countryParam
          ? `/api/v3/cities/?limit=100&country=${encodeURIComponent(countryParam)}`
          : "/api/v3/cities/?limit=100";

        $.ajax({
          url: citiesUrl,
          type: "GET",
          dataType: "json",
          success: function (cRes) {
            const citySet = new Set(self.availableStayCities || []);
            if (cRes) {
              if (Array.isArray(cRes.cities)) {
                cRes.cities.forEach((c) => {
                  if (c && typeof c === "string" && c.trim()) {
                    citySet.add(c.trim());
                  }
                });
              } else if (Array.isArray(cRes.results)) {
                cRes.results.forEach((item) => {
                  const cName = item.city || item.name || item.id;
                  if (cName && typeof cName === "string" && cName.trim()) {
                    citySet.add(cName.trim());
                  }
                });
              }
            }
            self.availableStayCities = Array.from(citySet).sort();

            // If day cards already exist in DOM, update their stay city dropdowns
            $("#itinDaysStack .v3-itin-day-card").each(function () {
              const $select = $(this).find(".itin-day-city-select");
              if ($select.length) {
                const currentVal = $select.val();
                const dayIdx = $(this).data("day-index") || 1;
                $select.html(self.buildCitySelectHtml(currentVal, dayIdx));
                if (currentVal) $select.val(currentVal);
                if (
                  typeof $.fn.select2 !== "undefined" &&
                  $select.hasClass("select2-hidden-accessible")
                ) {
                  $select.trigger("change.select2");
                }
              }
            });

            done();
          },
          error: function () {
            self.availableStayCities = self.availableStayCities || [];
            done();
          },
        });
      }

      // 2. Fetch properties
      if (self.propertiesList && self.propertiesList.length > 0) {
        pending--;
      } else {
        $.ajax({
          url: "/api/v3/properties/?status=active&limit=1000",
          type: "GET",
          dataType: "json",
          success: function (res) {
            if (res && res.properties) {
              self.propertiesList = res.properties;
              const citySet = new Set(self.availableStayCities || []);
              res.properties.forEach((p) => {
                if (p.city && p.city.trim()) {
                  citySet.add(p.city.trim());
                }
              });
              if (res.locations && Array.isArray(res.locations)) {
                res.locations.forEach((loc) => {
                  if (loc && loc.trim()) citySet.add(loc.trim());
                });
              }
              self.availableStayCities = Array.from(citySet).sort();
            }
            done();
          },
          error: function () {
            self.propertiesList = self.propertiesList || [];
            self.availableStayCities = self.availableStayCities || [];
            done();
          },
        });
      }

      // 3. Fetch vehicles
      if (self.vehiclesList && self.vehiclesList.length > 0) {
        pending--;
      } else {
        $.ajax({
          url: "/api/v3/vehicles/list/",
          type: "GET",
          dataType: "json",
          success: function (vRes) {
            if (vRes && (vRes.vehicles || vRes.data)) {
              self.vehiclesList = vRes.vehicles || vRes.data || [];
            }
            done();
          },
          error: function () {
            self.vehiclesList = self.vehiclesList || [];
            done();
          },
        });
      }

      if (pending <= 0 && typeof callback === "function") {
        callback();
      }
    },

    buildCitySelectHtml: function (currentCity, dayIndex) {
      const self = this;
      const cities = self.availableStayCities || [];
      const curr = (currentCity || "").toLowerCase().trim();

      let destCity = "";
      if (
        self.leadData &&
        self.leadData.travel_details &&
        self.leadData.travel_details.destination
      ) {
        destCity = self.leadData.travel_details.destination.trim();
      } else if (
        self.selectedPackage &&
        self.selectedPackage.tour_package_city
      ) {
        destCity = self.selectedPackage.tour_package_city.trim();
      }

      const allOptions = new Set();
      if (destCity) allOptions.add(destCity);
      if (
        currentCity &&
        currentCity.trim() &&
        !currentCity.toLowerCase().includes("overnight") &&
        !currentCity.toLowerCase().includes("trip end") &&
        !currentCity.toLowerCase().includes("tour end") &&
        !currentCity.toLowerCase().includes("no stay") &&
        !currentCity.toLowerCase().includes("transit")
      ) {
        allOptions.add(currentCity.trim());
      }

      // Add top popular tour destinations / hubs
      const hubs = [
        "Manali", "Shimla", "Kasol", "Jibhi", "Kullu", "Dharamshala", "Dalhousie", "Amritsar",
        "Chandigarh", "Delhi", "Agra", "Jaipur", "Udaipur", "Goa", "Srinagar", "Gulmarg",
        "Pahalgam", "Leh", "Ladakh", "Munnar", "Alleppey", "Thekkady", "Wayanad", "Ooty",
        "Kodaikanal", "Rishikesh", "Haridwar", "Nainital", "Mussoorie", "Jim Corbett",
        "Varanasi", "Darjeeling", "Gangtok", "Port Blair", "Havelock"
      ];
      hubs.forEach((h) => allOptions.add(h));

      // Add cities from self.availableStayCities (up to 100)
      for (let i = 0; i < Math.min(cities.length, 100); i++) {
        if (cities[i]) allOptions.add(cities[i]);
      }

      const isOvernight =
        curr.includes("overnight") ||
        curr.includes("in transit") ||
        curr === "overnight_journey";
      const isTripEnd =
        curr.includes("trip end") ||
        curr.includes("tour end") ||
        curr.includes("departure (no stay)") ||
        curr === "trip_end";
      const isSelfNoStay =
        curr.includes("self / no") ||
        curr.includes("no hotel") ||
        curr === "no_stay";

      let html = `<option value="">Select Stay City</option>`;

      // Special Transit / Non-Stay Options
      html += `<optgroup label="Transit & Tour Status (No Hotel Stay)">`;
      html += `<option value="Overnight Journey (In Transit)" ${isOvernight ? "selected" : ""}>🚌 Overnight Journey (In Transit)</option>`;
      html += `<option value="Tour End / Departure (No Stay)" ${isTripEnd ? "selected" : ""}>🏁 Tour End / Departure (No Stay)</option>`;
      html += `<option value="Self / No Hotel Stay Needed" ${isSelfNoStay ? "selected" : ""}>❌ Self / No Hotel Stay Needed</option>`;
      html += `</optgroup>`;

      // Regular Destination Cities
      html += `<optgroup label="Stay Cities & Destinations">`;
      Array.from(allOptions)
        .sort()
        .forEach((c) => {
          const isSel = c.toLowerCase().trim() === curr ? "selected" : "";
          html += `<option value="${escapeHtml(c)}" ${isSel}>${escapeHtml(c)}</option>`;
        });
      html += `</optgroup>`;

      return html;
    },

    setupPickupDropSelects: function (pickupVal, dropVal) {
      const self = this;
      const $pickup = $("#itineraryCustomPickup");
      const $drop = $("#itineraryCustomDrop");

      if ($pickup.hasClass("select2-hidden-accessible")) {
        $pickup.select2("destroy");
      }
      if ($drop.hasClass("select2-hidden-accessible")) {
        $drop.select2("destroy");
      }

      $pickup.empty();
      if (pickupVal && pickupVal.trim()) {
        $pickup.append(new Option(pickupVal.trim(), pickupVal.trim(), true, true));
      }
      $drop.empty();
      if (dropVal && dropVal.trim()) {
        $drop.append(new Option(dropVal.trim(), dropVal.trim(), true, true));
      }

      self.initCitySelect2($pickup, $("#modalSendItinerary"), "Select or type pickup location...");
      self.initCitySelect2($drop, $("#modalSendItinerary"), "Select or type drop location...");
    },

    getPropertyCategoryOptionsHtml: function (selectedCategory) {
      const cats = [
        { id: "all", label: "All Categories" },
        { id: "3_star_hotel", label: "3 Star (Comfort)" },
        { id: "4_star_hotel", label: "4 Star (First Class)" },
        { id: "5_star_hotel", label: "5 Star (Luxury)" },
        { id: "resorts", label: "Resort & Spa" },
        { id: "heritage_hotel", label: "Heritage Hotel" },
        { id: "home_stay", label: "Homestay / Villa" },
        { id: "tent", label: "Camp / Tent" },
        { id: "2_star_hotel", label: "2 Star (Standard)" },
      ];
      const sel = (selectedCategory || "3_star_hotel").toLowerCase().trim();
      let html = "";
      cats.forEach((c) => {
        const isSel =
          c.id === sel ||
          (sel === "3" && c.id === "3_star_hotel") ||
          (sel === "4" && c.id === "4_star_hotel") ||
          (sel === "5" && c.id === "5_star_hotel") ||
          (sel === "luxury" && c.id === "5_star_hotel");
        html += `<option value="${c.id}" ${isSel ? "selected" : ""}>${c.label}</option>`;
      });
      return html;
    },

    buildHotelSelectHtml: function (
      currentCity,
      selectedCategory,
      selectedHotelName,
      selectedHotelId,
    ) {
      const self = this;
      const props = self.propertiesList || [];
      const currCityLower = (currentCity || "").toLowerCase().trim();
      const selHotelLower = (selectedHotelName || "").toLowerCase().trim();
      const selHotelIdStr = selectedHotelId
        ? String(selectedHotelId).trim()
        : parseInt(selectedHotelName) > 0
          ? String(selectedHotelName).trim()
          : "";
      const selCatLower = (selectedCategory || "all").toLowerCase().trim();

      // City-based no-stay detection (driven by Stay City selection)
      const isNoStayOption =
        currCityLower.includes("overnight") ||
        currCityLower.includes("transit") ||
        currCityLower.includes("tour end") ||
        currCityLower.includes("trip end") ||
        currCityLower.includes("no stay") ||
        currCityLower.includes("no hotel");

      if (isNoStayOption) {
        let label = "No Hotel Required";
        if (
          currCityLower.includes("overnight") ||
          currCityLower.includes("transit")
        ) {
          label = "Overnight / In Transit — No Hotel Stay";
        } else if (
          currCityLower.includes("end") ||
          currCityLower.includes("departure")
        ) {
          label = "Tour End / Departure — No Hotel Stay";
        } else if (currCityLower.includes("self")) {
          label = "Self Arranged — No Hotel Stay";
        }
        return `<option value="no_hotel" selected>${label}</option>`;
      }

      let matchedProps = [];

      props.forEach((p) => {
        const pCity = (p.city || "").toLowerCase().trim();
        const cityMatches =
          !currCityLower ||
          pCity === currCityLower ||
          pCity.includes(currCityLower) ||
          currCityLower.includes(pCity);

        const isTargetHotel =
          (selHotelIdStr && String(p.id) === selHotelIdStr) ||
          (selHotelLower &&
            (p.title.toLowerCase().trim() === selHotelLower ||
              selHotelLower.includes(p.title.toLowerCase().trim())));

        if (cityMatches || isTargetHotel) {

          if (selCatLower && selCatLower !== "all") {
            const pType = (p.type || "").toLowerCase().trim();
            if (
              isTargetHotel ||
              pType === selCatLower ||
              (selCatLower === "3_star_hotel" && pType.includes("3")) ||
              (selCatLower === "4_star_hotel" && pType.includes("4")) ||
              (selCatLower === "5_star_hotel" &&
                (pType.includes("5") || pType.includes("luxury"))) ||
              (selCatLower === "resorts" && pType.includes("resort")) ||
              (selCatLower === "heritage_hotel" &&
                pType.includes("heritage")) ||
              (selCatLower === "home_stay" &&
                (pType.includes("home") ||
                  pType.includes("stay") ||
                  pType.includes("villa"))) ||
              (selCatLower === "tent" &&
                (pType.includes("tent") || pType.includes("camp"))) ||
              (selCatLower === "2_star_hotel" && pType.includes("2"))
            ) {
              matchedProps.push(p);
            }
          } else {
            matchedProps.push(p);
          }
        }
      });

      // Deduplicate matchedProps by id
      const uniqueProps = [];
      const seenIds = new Set();
      matchedProps.forEach((p) => {
        if (!seenIds.has(p.id)) {
          seenIds.add(p.id);
          uniqueProps.push(p);
        }
      });
      matchedProps = uniqueProps;

      const cityDisplay = currentCity
        ? currentCity.charAt(0).toUpperCase() + currentCity.slice(1)
        : "Stay City";
      let html = `<option value="">-- Choose Hotel in ${escapeHtml(cityDisplay)} (${matchedProps.length} Available) --</option>`;

      let foundSelected = false;
      if (matchedProps.length > 0) {
        matchedProps.forEach((p) => {
          const isSel =
            !foundSelected &&
            ((selHotelIdStr && String(p.id) === selHotelIdStr) ||
              (selHotelLower &&
                (p.title.toLowerCase().trim() === selHotelLower ||
                  selHotelLower.includes(p.title.toLowerCase().trim()))));
          if (isSel) foundSelected = true;
          const stateLabel = p.state ? `, ${escapeHtml(p.state)}` : "";
          html += `<option value="${p.id}" data-type="${escapeHtml(p.type || "")}" data-city="${escapeHtml(p.city || "")}" data-state="${escapeHtml(p.state || "")}" ${isSel ? "selected" : ""}>${escapeHtml(p.title)} — ${escapeHtml(p.city || "")}${stateLabel}</option>`;
        });
      } else {
        html += `<option value="" disabled>No properties found in ${escapeHtml(currentCity || "selected city")}</option>`;
      }

      const isCustomSelected =
        selectedHotelName &&
        !foundSelected &&
        !matchedProps.some(
          (p) => p.title.toLowerCase().trim() === selHotelLower,
        );

      html += `<option value="custom" ${isCustomSelected ? "selected" : ""}>Enter Custom Hotel Name</option>`;

      return html;
    },

    getHotelSelect2Opts: function ($parentModal) {
      return {
        dropdownParent: $parentModal || $("#modalSendItinerary"),
        width: "100%",
        placeholder: "-- Select Property / Hotel --",
        templateResult: function (data) {
          if (!data.id || !data.element) return data.text;
          const $el = $(data.element);
          const city = $el.attr("data-city") || "";
          const state = $el.attr("data-state") || "";
          // Strip the appended " — City, State" from the raw option text to get just the name
          const rawText = data.text || "";
          const name = rawText.replace(/\s+—\s+.*$/, "").trim() || rawText;
          const locationLine = [city, state].filter(Boolean).join(", ");
          if (!locationLine) return $("<span>").text(name);
          return $(
            `<span class="v3-hotel-opt">
               <span class="v3-hotel-opt-name">${$("<span>").text(name).html()}</span>
               <span class="v3-hotel-opt-loc"><i class="fi fi-rr-marker"></i> ${$("<span>").text(locationLine).html()}</span>
             </span>`,
          );
        },
        templateSelection: function (data) {
          if (!data.id) return data.text;
          // Show only the hotel name (strip " — City, State" suffix)
          return (
            (data.text || "").replace(/\s+—\s+.*$/, "").trim() || data.text
          );
        },
      };
    },

    buildVehicleSelectHtml: function (selectedVehicle) {
      const self = this;
      const vehicles = self.vehiclesList || [];
      const selLower = (selectedVehicle || "").toLowerCase().trim();

      let html = `<option value="">-- Choose Transport / Fleet --</option>`;
      let foundSelected = false;

      if (vehicles.length > 0) {
        html += `<optgroup label="Fleet Vehicles">`;
        vehicles.forEach((v) => {
          const vTitle = v.title || v.name;
          const vType = v.type_label || v.type || "";
          const rawType = (v.type || "").toLowerCase().trim();
          const isSel =
            !foundSelected &&
            selLower &&
            (vTitle.toLowerCase() === selLower ||
              selLower.includes(vTitle.toLowerCase()) ||
              vTitle.toLowerCase().includes(selLower) ||
              (rawType &&
                (rawType === selLower || selLower.includes(rawType))));
          if (isSel) foundSelected = true;
          html += `<option value="${escapeHtml(vTitle)}" ${isSel ? "selected" : ""}>${escapeHtml(vTitle)}${vType ? ` (${escapeHtml(vType)})` : ""}</option>`;
        });
        html += `</optgroup>`;
      }

      const standardTransports = [
        "Private Sedan (Dzire / Etios)",
        "SUV (Innova / Ertiga)",
        "Tempo Traveller",
        "Luxury Coach",
        "Speedboat / Ferry",
        "Flight / Air Transfer",
      ];

      html += `<optgroup label="Standard Categories">`;
      standardTransports.forEach((st) => {
        const isSel =
          !foundSelected &&
          selLower &&
          (st.toLowerCase() === selLower ||
            selLower.includes(st.toLowerCase()) ||
            st.toLowerCase().includes(selLower));
        if (isSel) foundSelected = true;
        html += `<option value="${escapeHtml(st)}" ${isSel ? "selected" : ""}>${escapeHtml(st)}</option>`;
      });
      html += `</optgroup>`;

      const isCustomSelected = selectedVehicle && !foundSelected;

      html += `<option value="custom" ${isCustomSelected ? "selected" : ""}>Enter Custom Transport</option>`;

      return html;
    },

    loadRoomTypesForHotel: function (
      propertyId,
      $roomSelect,
      selectedRoomType,
    ) {
      const self = this;
      self.hotelRoomsCache = self.hotelRoomsCache || {};

      const rawSel = (selectedRoomType || "").toString().trim();
      const cleanSel = rawSel
        .replace(/^\d+:/, "")
        .toLowerCase()
        .replace(/room/g, "")
        .trim();

      const renderRoomsHtml = (roomsList) => {
        $roomSelect.prop("disabled", false);
        if (!roomsList || roomsList.length === 0) {
          const defaultRooms = [
            "Deluxe Room",
            "Standard Room",
            "Superior Room",
            "Executive Suite",
            "Family Room",
          ];
          let html = `<option value="">-- Select Room Type --</option>`;
          defaultRooms.forEach((r) => {
            const isSel =
              cleanSel &&
              (r.toLowerCase().includes(cleanSel) ||
                cleanSel.includes(r.toLowerCase().replace(/room/g, "").trim()));
            html += `<option value="${r}" ${isSel ? "selected" : ""}>${r}</option>`;
          });
          html += `<option value="custom">Enter Custom Room</option>`;
          $roomSelect.html(html);
          if (
            typeof $.fn.select2 !== "undefined" &&
            $roomSelect.hasClass("select2-hidden-accessible")
          ) {
            $roomSelect.trigger("change.select2");
          }
          return;
        }

        let html = `<option value="">-- Select Room Type (${roomsList.length} Available) --</option>`;
        let foundMatch = false;

        roomsList.forEach((rm) => {
          const rName = (
            rm.name ||
            rm.type_label ||
            rm.type ||
            "Deluxe Room"
          ).trim();
          const cleanName = rName.toLowerCase().replace(/room/g, "").trim();
          const isSel =
            !foundMatch &&
            cleanSel &&
            (cleanName === cleanSel ||
              cleanSel.includes(cleanName) ||
              cleanName.includes(cleanSel) ||
              String(rm.id) === rawSel ||
              (rm.val && rm.val.toLowerCase() === rawSel.toLowerCase()));
          if (isSel) foundMatch = true;
          html += `<option value="${escapeHtml(rName)}" data-id="${rm.id || 0}" ${isSel ? "selected" : ""}>${escapeHtml(rName)}</option>`;
        });

        // By default room type mentioned in itinerary: if not in list, add it as selected
        if (!foundMatch && rawSel && rawSel !== "custom" && rawSel !== "0") {
          const customRName = rawSel.replace(/^\d+:/, "").trim();
          html =
            `<option value="${escapeHtml(customRName)}" selected>${escapeHtml(customRName)}</option>` +
            html;
          foundMatch = true;
        }

        // Auto-select if only 1 room type available and none selected
        if (!foundMatch && roomsList.length === 1) {
          const onlyRName = (
            roomsList[0].name ||
            roomsList[0].type_label ||
            roomsList[0].type ||
            ""
          ).trim();
          if (onlyRName) {
            html = html.replace(
              `value="${escapeHtml(onlyRName)}"`,
              `value="${escapeHtml(onlyRName)}" selected`,
            );
          }
        }

        html += `<option value="custom">Enter Custom Room</option>`;
        $roomSelect.html(html);
        if (
          typeof $.fn.select2 !== "undefined" &&
          $roomSelect.hasClass("select2-hidden-accessible")
        ) {
          $roomSelect.trigger("change.select2");
        }
      };

      if (
        !propertyId ||
        propertyId === "" ||
        propertyId === "0" ||
        propertyId === "no_hotel"
      ) {
        const placeholderText =
          propertyId === "no_hotel"
            ? "Not Applicable (No Stay)"
            : "Select Hotel First";
        $roomSelect
          .prop("disabled", true)
          .html(`<option value="">${placeholderText}</option>`);
        if (
          typeof $.fn.select2 !== "undefined" &&
          $roomSelect.hasClass("select2-hidden-accessible")
        ) {
          $roomSelect.trigger("change.select2");
        }
        return;
      }

      if (propertyId === "custom" || parseInt(propertyId) <= 0) {
        renderRoomsHtml([]);
        return;
      }

      // Check cache first
      if (
        self.hotelRoomsCache[propertyId] &&
        self.hotelRoomsCache[propertyId].length > 0
      ) {
        renderRoomsHtml(self.hotelRoomsCache[propertyId]);
        return;
      }

      // Check if property in propertiesList already has rooms
      const propObj = (self.propertiesList || []).find(
        (p) => String(p.id) === String(propertyId),
      );
      if (propObj && Array.isArray(propObj.rooms) && propObj.rooms.length > 0) {
        self.hotelRoomsCache[propertyId] = propObj.rooms;
        renderRoomsHtml(propObj.rooms);
        return;
      }

      $roomSelect
        .prop("disabled", false)
        .html('<option value="">Loading room types...</option>');
      if (
        typeof $.fn.select2 !== "undefined" &&
        $roomSelect.hasClass("select2-hidden-accessible")
      ) {
        $roomSelect.trigger("change.select2");
      }

      $.ajax({
        url: `/api/v3/properties/rooms/?property_id=${propertyId}`,
        type: "GET",
        dataType: "json",
        success: function (res) {
          const loadedRooms = (res && res.rooms) || [];
          self.hotelRoomsCache[propertyId] = loadedRooms;
          renderRoomsHtml(loadedRooms);
        },
        error: function () {
          renderRoomsHtml([]);
        },
      });
    },

    showItinDaysLoader: function () {
      const $stack = $("#itinDaysStack");
      if (!$stack.length) return;
      $stack.html(`
        <div class="v3-itin-skeleton-card">
          <div class="v3-itin-skeleton-header">
            <div class="v3-skeleton-shimmer v3-sk-pill-sm"></div>
            <div class="v3-skeleton-shimmer v3-sk-pill-md"></div>
            <div class="v3-skeleton-shimmer v3-sk-input"></div>
          </div>
          <div class="v3-itin-skeleton-body">
            <div class="v3-skeleton-shimmer v3-sk-textarea"></div>
            <div class="v3-itin-skeleton-grid">
              <div class="v3-skeleton-shimmer v3-sk-box"></div>
              <div class="v3-skeleton-shimmer v3-sk-box"></div>
            </div>
          </div>
          <div class="v3-itin-skeleton-loading-text">
            <div class="v3-table-spinner"><div class="v3-spinner-ring"></div></div>
            <span>Loading day-wise itinerary, accommodations & transfers...</span>
          </div>
        </div>
        <div class="v3-itin-skeleton-card v3-sk-dimmed">
          <div class="v3-itin-skeleton-header">
            <div class="v3-skeleton-shimmer v3-sk-pill-sm"></div>
            <div class="v3-skeleton-shimmer v3-sk-pill-md"></div>
            <div class="v3-skeleton-shimmer v3-sk-input"></div>
          </div>
          <div class="v3-itin-skeleton-body">
            <div class="v3-skeleton-shimmer v3-sk-textarea-sm"></div>
          </div>
        </div>
      `);
    },

    // Render structured day cards inside the full screen itinerary customizer
    renderItineraryDays: function (days) {
      const self = this;
      const $stack = $("#itinDaysStack");
      self.showItinDaysLoader();

      if (!days || days.length === 0) {
        days = [
          {
            day: "Day 1 - Arrival & Sightseeing",
            overview:
              "Arrive at destination, transfer to hotel, and explore local sights.",
          },
          {
            day: "Day 2 - Full Day Tour & Activities",
            overview: "Enjoy full day guided tour and iconic attractions.",
          },
          {
            day: "Day 3 - Departure",
            overview:
              "Check out from hotel and transfer to airport/station for departure.",
          },
        ];
      }

      self.loadItineraryMeta(function () {
        $stack.empty();
        const totalDays = days.length;
        days.forEach((d, idx) => {
          d.is_last_day = idx === totalDays - 1;
          d.total_days = totalDays;
          self.appendDayCard(d, idx + 1);
        });
      });
    },

    appendDayCard: function (d, num) {
      const self = this;
      const title = d.day || `Day ${num} - Tour Plan`;
      const overview = d.overview || "";
      const city = (d.stay || d.city || d.stay_city || "").trim();
      const hotelId = parseInt(d.hotel_id || d.assigned_hotel_id || 0) || 0;
      const hotelTitle = d.hotel_title || d.hotel || "";
      const hotel = hotelTitle || (hotelId ? String(hotelId) : "");
      const hotelStars = d.hotel_stars || d.hotel_type || "all";
      const roomType =
        d.room_type || d.room_type_name || d.assigned_room_type || "";
      const mealPlan = d.meal_plan || "CP";

      // Pre-default vehicles day-wise: package outstation vehicle for day 1 and last day, local sightseeing vehicle for other days
      let vehicle = (d.vehicle || "").trim();
      if (!vehicle || vehicle === "Private Sedan") {
        const pkg = self.selectedPackage || {};
        const outstation = (
          pkg.outstation_vehicle ||
          pkg.tour_package_outstation_vehicle ||
          ""
        ).trim();
        const local = (
          pkg.localsightseen_vehicle ||
          pkg.tour_package_localsightseen_vehicle ||
          ""
        ).trim();

        if (
          num === 1 ||
          d.is_last_day ||
          (d.total_days && num === d.total_days)
        ) {
          vehicle = outstation || local || vehicle || "Private Sedan";
        } else {
          vehicle = local || outstation || vehicle || "Private Sedan";
        }
      }

      const transferNotes = d.transfer_notes || "";

      const cityLower = city.toLowerCase();
      const isNoStayCity =
        cityLower.includes("overnight") ||
        cityLower.includes("transit") ||
        cityLower.includes("tour end") ||
        cityLower.includes("trip end") ||
        cityLower.includes("no stay") ||
        cityLower.includes("no hotel") ||
        cityLower.includes("departure");

      const citySelectHtml = self.buildCitySelectHtml(city, num);
      const categorySelectHtml =
        self.getPropertyCategoryOptionsHtml(hotelStars);
      const hotelSelectHtml = self.buildHotelSelectHtml(
        city,
        "all",
        hotelTitle,
        hotelId,
      );
      const vehicleSelectHtml = self.buildVehicleSelectHtml(vehicle);
      let dayImg = d.feature_image || d.image || d.day_image || "";
      if (
        !dayImg &&
        self.selectedPackage &&
        self.selectedPackage.gallery_image_list
      ) {
        let gList = self.selectedPackage.gallery_image_list;
        if (typeof gList === "string") {
          try {
            gList = JSON.parse(gList);
          } catch (e) {
            gList = [];
          }
        }
        if (Array.isArray(gList)) {
          const match =
            gList.find((item) => parseInt(item.day) === num) || gList[num - 1];
          if (match && match.path) {
            dayImg = match.path;
          }
        }
      }

      const hotelLower = (hotel || "").toLowerCase();
      const isNoHotel =
        hotel === "no_hotel" ||
        hotelLower.includes("no hotel") ||
        hotelLower.includes("no stay") ||
        hotelLower.includes("transit") ||
        hotelLower.includes("tour end") ||
        hotelLower.includes("trip end") ||
        hotelLower.includes("departure") ||
        hotelLower.includes("self arranged");

      const isNoHotelStay = isNoStayCity || isNoHotel;

      const explicitShow =
        d.show_hotel === 1 || d.show_hotel === true || d.show_hotel === "1";
      const explicitHide =
        d.show_hotel === 0 ||
        d.show_hotel === false ||
        d.show_hotel === "0" ||
        d.is_hotel_hidden === 1 ||
        d.is_hotel_hidden === true ||
        d.hide_hotel === 1 ||
        d.hide_hotel === true;

      const showHotel = explicitShow
        ? true
        : explicitHide || isNoHotelStay
          ? false
          : true;

      const html = `
        <div class="v3-itin-day-card" data-day-index="${num}" data-day-image="${dayImg}">
          <div class="v3-itin-day-card-header">
            <span class="v3-itin-day-badge">Day ${num}</span>
            <div class="v3-itin-stay-city-chip" title="Select stay city or transit status for this day">
              <i class="fi fi-rr-marker"></i>
              <span class="stay-city-label">Night Stay / City:</span>
              <select class="itin-day-city-select" data-day-index="${num}">
                ${citySelectHtml}
              </select>
            </div>
            <input type="text" class="v3-itin-day-title-input itin-day-title" value="${title}" placeholder="e.g. Day ${num} - Arrival & City Tour">
            <button type="button" class="v3-itin-day-delete-btn btn-delete-itin-day" title="Delete Day ${num}">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>

          <div class="v3-modal-form-group">
            <label class="v3-modal-label">Day Schedule & Activities *</label>
            <textarea class="v3-modal-textarea itin-day-overview" rows="3" placeholder="Describe the day's schedule, sightseeing spots, and activities...">${overview}</textarea>
          </div>

          <div class="v3-itin-meta-boxes-grid">
            <!-- Hotel & Accommodation -->
            <div class="v3-itin-sub-box v3-itin-hotel-sub-box">
              <div class="v3-itin-sub-box-title">
                <div class="v3-itin-sub-box-header">
                  <span><i class="fi fi-rr-bed"></i> Hotel & Stay</span>
                  <label class="v3-hotel-visibility-toggle" title="Toggle hotel in customer itinerary proposal for Day ${num}">
                    <span class="v3-switch-label">Show in Itinerary</span>
                    <span class="v3-switch-slider-wrap">
                      <input type="checkbox" class="itin-day-show-hotel-toggle" data-day-index="${num}" ${showHotel ? 'checked' : ''}>
                      <span class="v3-switch-slider"></span>
                    </span>
                  </label>
                </div>
              </div>

              <div class="v3-itin-hotel-fields-wrap ${!showHotel ? 'is-hotel-hidden' : ''}">
                <div class="v3-form-grid-2 v3-mb-10">
                  <div>
                    <label class="v3-modal-label">Stay Type / Rating</label>
                    <select class="leads-filter-select v3-select-full itin-day-hotel-stars">
                      ${categorySelectHtml}
                    </select>
                  </div>
                  <div>
                    <label class="v3-modal-label">Property / Hotel</label>
                    <select class="leads-filter-select v3-select-full itin-day-hotel-select" data-day-index="${num}">
                      ${hotelSelectHtml}
                    </select>
                    <input type="text" class="v3-modal-input itin-day-hotel-custom ${hotel && !hotelSelectHtml.includes("selected") ? "" : "v3-hidden"}" value="${hotel}" placeholder="Enter custom hotel name...">
                  </div>
                </div>

                <div class="v3-form-grid-2">
                  <div>
                    <label class="v3-modal-label">Room Type</label>
                    <select class="leads-filter-select v3-select-full itin-day-room-type" ${!hotel && !showHotel ? "disabled" : ""}>
                      <!-- Populated dynamically based on property -->
                    </select>
                    <input type="text" class="v3-modal-input itin-day-room-type-custom v3-hidden" value="${roomType}" placeholder="Enter custom room">
                  </div>
                  <div>
                    <label class="v3-modal-label">Meal Plan</label>
                    <select class="leads-filter-select v3-select-full itin-day-meal-plan">
                      <option value="EP" ${mealPlan === "EP" ? "selected" : ""}>EP (Room Only)</option>
                      <option value="CP" ${mealPlan === "CP" ? "selected" : ""}>CP (Breakfast)</option>
                      <option value="MAP" ${mealPlan === "MAP" ? "selected" : ""}>MAP (Breakfast + Dinner)</option>
                      <option value="AP" ${mealPlan === "AP" ? "selected" : ""}>AP (All Meals)</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="v3-itin-hotel-hidden-notice ${!showHotel ? '' : 'v3-hidden'}">
                <i class="fi fi-rr-info"></i> Hotel details hidden from customer itinerary proposal for Day ${num}.
              </div>
            </div>

            <!-- Vehicle & Transport -->
            <div class="v3-itin-sub-box">
              <div class="v3-itin-sub-box-title">
                <i class="fi fi-rr-car"></i> Vehicle & Transfers
              </div>
              <div class="v3-modal-form-group">
                <label class="v3-modal-label">Transport Type</label>
                <select class="leads-filter-select v3-select-full itin-day-vehicle-type">
                  ${vehicleSelectHtml}
                </select>
                <input type="text" class="v3-modal-input itin-day-vehicle-custom ${vehicle && !vehicleSelectHtml.includes("selected") ? "" : "v3-hidden"}" value="${vehicle}" placeholder="Enter custom transport...">
              </div>
              <div class="v3-modal-form-group">
                <label class="v3-modal-label">Transfer Route / Notes</label>
                <input type="text" class="v3-modal-input itin-day-transfer-notes" value="${transferNotes}" placeholder="e.g. Airport to Hotel Transfer & Evening City Drive">
              </div>
            </div>
          </div>
        </div>
      `;
      const $card = $(html);
      $("#itinDaysStack").append($card);

      // Load initial room types for selected hotel
      const selectedPropId =
        $card.find(".itin-day-hotel-select").val() || hotelId;
      self.loadRoomTypesForHotel(
        selectedPropId,
        $card.find(".itin-day-room-type"),
        roomType,
      );

      // Initialize Select2 on all Day Card Dropdowns
      if (typeof $.fn.select2 !== "undefined") {
        $card.find(".itin-day-city-select").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          placeholder: "Stay City",
          tags: true,
          minimumInputLength: 0,
          createTag: function (params) {
            const term = $.trim(params.term);
            if (!term) return null;
            return {
              id: term,
              text: `${term} (Add New)`,
              city: term,
              isNewTag: true,
            };
          },
          insertTag: function (data, tag) {
            // Place search keyword with (Add New) at the LAST of dropdown options
            data.push(tag);
          },
          templateResult: function (opt) {
            if (!opt.id) return opt.text;
            if (opt.isNewTag || (opt.text && opt.text.endsWith("(Add New)"))) {
              return $(
                `<div class="v3-select2-add-new-opt"><i class="fi fi-rr-plus"></i> ${escapeHtml(opt.text)}</div>`,
              );
            }
            return opt.text;
          },
          templateSelection: function (item) {
            if (!item.id) return item.text;
            const clean = (item.city || item.id || item.text || "")
              .replace(/\s*\(Add New\)$/i, "")
              .trim();
            return clean || item.text;
          },
          ajax: {
            url: "/api/v3/cities/",
            dataType: "json",
            delay: 150,
            data: function (params) {
              return {
                q: params.term || "",
                package_id:
                  (self.selectedPackage &&
                    self.selectedPackage.tour_package_id) ||
                  0,
              };
            },
            processResults: function (data, params) {
              const rawResults = (data && data.results) || [];
              const term = (params.term || "").trim();
              if (!term) {
                return {
                  results: [
                    {
                      text: "Transit & Tour Status (No Hotel Stay)",
                      children: [
                        { id: "Overnight Journey (In Transit)", text: "🚌 Overnight Journey (In Transit)" },
                        { id: "Tour End / Departure (No Stay)", text: "🏁 Tour End / Departure (No Stay)" },
                        { id: "Self / No Hotel Stay Needed", text: "❌ Self / No Hotel Stay Needed" }
                      ]
                    },
                    {
                      text: "Cities & Destinations",
                      children: rawResults
                    }
                  ]
                };
              }
              return { results: rawResults };
            },
            cache: true,
          },
        });

        $card
          .find(".itin-day-hotel-select")
          .select2(self.getHotelSelect2Opts($("#modalSendItinerary")));

        $card.find(".itin-day-hotel-stars").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          minimumResultsForSearch: Infinity,
        });

        $card.find(".itin-day-room-type").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          placeholder: "Select Room Type",
        });

        $card.find(".itin-day-meal-plan").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          minimumResultsForSearch: Infinity,
        });

        $card.find(".itin-day-vehicle-type").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          placeholder: "-- Choose Transport / Fleet --",
        });
      }

      const $overviewTextarea = $card.find(".itin-day-overview");
      if (typeof $.fn.v3Editor === "function") {
        $overviewTextarea.v3Editor({
          minHeight: 60,
          placeholder:
            "Describe the day's schedule, sightseeing spots, and activities...",
          toolbar: [
            "history",
            "format",
            "styling",
            "colors",
            "alignment",
            "lists",
            "inserts",
          ],
        });
      }
    },

    reindexDays: function () {
      $("#itinDaysStack .v3-itin-day-card").each(function (idx) {
        const num = idx + 1;
        $(this).attr("data-day-index", num);
        $(this).find(".v3-itin-day-badge").text(`Day ${num}`);
        $(this).find(".btn-delete-itin-day").attr("title", `Delete Day ${num}`);
        $(this).find(".itin-day-show-hotel-toggle").attr("data-day-index", num);
        $(this).find(".v3-hotel-visibility-toggle").attr("title", `Toggle hotel in customer itinerary proposal for Day ${num}`);
        $(this).find(".v3-itin-hotel-hidden-notice").html(`<i class="fi fi-rr-info"></i> Hotel details hidden from customer itinerary proposal for Day ${num}.`);
      });
    },

    resolvePrimaryCity: function (structuredItin) {
      const self = this;
      if (
        self.selectedPackage &&
        (self.selectedPackage.tour_package_city || self.selectedPackage.city)
      ) {
        const pkgCity = (
          self.selectedPackage.tour_package_city || self.selectedPackage.city
        ).trim();
        if (pkgCity) return pkgCity;
      }
      if (Array.isArray(structuredItin) && structuredItin.length > 0) {
        for (const day of structuredItin) {
          const c = (day.city || day.stay_city || "").trim();
          const cLower = c.toLowerCase();
          if (
            c &&
            !cLower.includes("overnight") &&
            !cLower.includes("transit") &&
            !cLower.includes("tour end") &&
            !cLower.includes("trip end") &&
            !cLower.includes("no stay") &&
            !cLower.includes("no hotel")
          ) {
            return c;
          }
        }
      }
      if (
        self.leadData &&
        self.leadData.travel_details &&
        self.leadData.travel_details.destination
      ) {
        const leadDest = self.leadData.travel_details.destination.trim();
        if (leadDest) return leadDest;
      }
      const inputVal = ($("#itineraryDestinationInput").val() || "").trim();
      if (inputVal) return inputVal;

      return "Tour Destination";
    },

    resolvePrimaryHotel: function (structuredItin) {
      const self = this;
      if (Array.isArray(structuredItin) && structuredItin.length > 0) {
        const allHidden = structuredItin.every(
          (d) => d.show_hotel === 0 || d.is_hotel_hidden === 1,
        );
        if (allHidden) {
          return "";
        }
        const hotels = [];
        for (const day of structuredItin) {
          if (day.show_hotel === 0 || day.is_hotel_hidden === 1) continue;
          const h = (day.hotel || day.stay || "").trim();
          if (
            h &&
            h !== "no_hotel" &&
            !h.toLowerCase().includes("no hotel") &&
            !hotels.includes(h)
          ) {
            hotels.push(h);
          }
        }
        if (hotels.length > 0) {
          return hotels.slice(0, 3).join(" / ");
        }
      }
      if (self.selectedPackage && self.selectedPackage.tour_package_hotel) {
        return self.selectedPackage.tour_package_hotel;
      }
      return "";
    },

    resolvePrimaryHotelCategory: function (structuredItin) {
      const self = this;
      if (Array.isArray(structuredItin) && structuredItin.length > 0) {
        const allHidden = structuredItin.every(
          (d) => d.show_hotel === 0 || d.is_hotel_hidden === 1,
        );
        if (allHidden) {
          return "";
        }
        for (const day of structuredItin) {
          if (day.show_hotel === 0 || day.is_hotel_hidden === 1) continue;
          const s = (day.hotel_stars || "").trim();
          if (s && s !== "all" && s !== "no_hotel") {
            return s;
          }
        }
      }
      if (self.selectedPackage && self.selectedPackage.tour_package_hotel) {
        return self.selectedPackage.tour_package_hotel;
      }
      return "3_star_hotel";
    },

    resolvePrimaryVehicle: function (structuredItin) {
      const self = this;
      if (Array.isArray(structuredItin) && structuredItin.length > 0) {
        for (const day of structuredItin) {
          const v = (day.vehicle || "").trim();
          if (v && v !== "custom") return v;
        }
      }
      if (
        self.selectedPackage &&
        self.selectedPackage.tour_package_outstation_vehicle
      ) {
        return self.selectedPackage.tour_package_outstation_vehicle;
      }
      return "Private Sedan (Dzire / Etios)";
    },

    bindEvents: function () {
      const self = this;

      // 1. Tab Navigation
      $(document).on("click", ".btn-copy-itin-link", function (e) {
        e.preventDefault();
        const url = $(this).data("url") || $(this).attr("data-url");
        if (!url) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard
            .writeText(url)
            .then(function () {
              self.notify("Itinerary link copied to clipboard!", "success");
            })
            .catch(function () {
              self.notify("Failed to copy itinerary link.", "error");
            });
        } else {
          const $temp = $("<input>");
          $("body").append($temp);
          $temp.val(url).select();
          document.execCommand("copy");
          $temp.remove();
          self.notify("Itinerary link copied to clipboard!", "success");
        }
      });

      $(document).on("click", ".v3-tab-link", function () {
        if ($(this).hasClass("v3-tab-disabled") || $(this).prop("disabled")) {
          self.notify(
            "Please assign an agent to this lead before accessing payments.",
            "warning",
          );
          return;
        }
        const tabId = $(this).data("tab");
        $(".v3-tab-link").removeClass("active");
        $(this).addClass("active");
        $(".v3-lead-tab-pane").removeClass("active");
        $(`#${tabId}`).addClass("active");
      });

      // 2. Minimal Status Picker Toggle & Selection
      $(document).on("click", "#btnMinimalStatusTrigger", function (e) {
        e.stopPropagation();
        $("#statusDropdownMenu").toggleClass("is-open");
      });

      $(document).on("click", function (e) {
        if (!$(e.target).closest(".v3-minimal-status-picker").length) {
          $("#statusDropdownMenu").removeClass("is-open");
        }
      });

      $(document).on("click", ".v3-status-opt", function () {
        const targetStatus = parseInt($(this).data("status"));
        $("#statusDropdownMenu").removeClass("is-open");

        const currentStatus = self.leadData
          ? parseInt(self.leadData.status)
          : 0;
        if (targetStatus === currentStatus) return;

        if (targetStatus === 1) {
          self.confirmConvertToSale();
        } else if (
          targetStatus === 15 ||
          targetStatus === 16 ||
          targetStatus === 17 ||
          targetStatus === 4 ||
          targetStatus === 18
        ) {
          $("#modalCloseStatus").val(targetStatus);
          $("#modalCloseLead").addClass("is-active");
        } else {
          self.updateStatus(targetStatus);
        }
      });

      // 3. Composer Switch (Note vs Follow-Up)
      $(document).on("click", ".v3-composer-type-btn", function () {
        const type = $(this).data("type");
        self.activeComposerType = type;
        $(".v3-composer-type-btn").removeClass("active");
        $(this).addClass("active");

        if (type === "followup") {
          $("#composerFollowupRow").addClass("is-visible");
          self.initDateTimePicker();
          $("#composerNoteInput").attr(
            "placeholder",
            "Add instructions for this follow-up reminder...",
          );
          $("#btnSubmitComposer").html(
            '<i class="fi fi-rr-alarm-clock"></i> Set Reminder',
          );
        } else {
          $("#composerFollowupRow").removeClass("is-visible");
          $("#composerNoteInput").attr(
            "placeholder",
            "Type a note, call log, or customer update...",
          );
          $("#btnSubmitComposer").html(
            '<i class="fi fi-rr-check"></i> Post Note',
          );
        }
      });

      // 4. Submit Composer Update
      $(document).on("click", "#btnSubmitComposer", function () {
        if (!self.leadData || !(parseInt(self.leadData.agent_id || 0) > 0)) {
          self.notify(
            "Lead is unassigned. Please assign an agent first.",
            "warning",
          );
          return;
        }

        const msg = $("#composerNoteInput").val().trim();
        const type = self.activeComposerType;

        if (type === "note" && !msg) {
          self.notify("Please type a note before posting.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Posting...");

        const followupReason =
          $("#composerFollowupReason").val() || "General Follow-up";
        const postData = {
          leadId: self.leadId,
          title: type === "followup" ? followupReason : "Note",
          category: type === "followup" ? followupReason : "Note",
          msg: msg,
        };

        if (type === "followup") {
          postData.followupTime = $("#composerFollowupTime").val();
        }

        self.submitComposerNote(postData, $btn);
      });

      // 5. Agent Re-assign with SweetAlert Confirmation
      $(document).on("change", "#leadDetailAgentSelect", function () {
        const newAgentId = $(this).val();
        const newAgentName = $(this).find("option:selected").text();
        const currentAgentId = self.leadData ? self.leadData.agent_id : 0;

        if (parseInt(newAgentId) === parseInt(currentAgentId)) return;

        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Reassign Lead?",
            text: `Assign this lead to ${newAgentName}?`,
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0284c7",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Reassign",
            cancelButtonText: "Cancel",
          }).then((res) => {
            if (res.isConfirmed || res.value) {
              self.performAgentAssign(newAgentId);
            } else {
              $("#leadDetailAgentSelect")
                .val(currentAgentId)
                .trigger("change.select2");
            }
          });
        } else if (confirm(`Assign this lead to ${newAgentName}?`)) {
          self.performAgentAssign(newAgentId);
        } else {
          $("#leadDetailAgentSelect")
            .val(currentAgentId)
            .trigger("change.select2");
        }
      });

      // 6. Convert to Sale
      $(document).on(
        "click",
        "#btnHeroConvertToSale, #btnSideConvertToSale",
        function () {
          self.confirmConvertToSale();
        },
      );

      // 7. Edit Lead Modal Trigger & Submission
      $(document).on(
        "click",
        "#btnHeroEditLead, #btnTabEditLead, #btnSideEditLead, #btnEditRequirements",
        function () {
          if (!self.leadData) return;
          const d = self.leadData;
          const t = d.travel_details || {};
          const isReqTrigger = $(this).is("#btnEditRequirements");

          let fName = (d.first_name || d.name || "").trim();
          let lName = (d.last_name || "").trim();
          if (!lName && fName.indexOf(" ") !== -1) {
            const parts = fName.split(/\s+/);
            fName = parts[0];
            lName = parts.slice(1).join(" ");
          }

          $("#editLeadId").val(d.id || self.leadId || "");
          $("#editLeadFirstName").val(fName);
          $("#editLeadLastName").val(lName);
          $("#editLeadEmail").val(d.email || "");
          $("#editLeadMobile").val(d.mobile || "");
          $("#editLeadAltMobile").val(d.alt_mobile || "");

          $("#editLeadSubject").val(d.subject || "");
          $("#editLeadTravelDate").val(t.date_of_travel || "");
          $("#editLeadDuration").val(t.duration_days || 0);

          $("#editLeadAdults").val(t.adults || 1);
          $("#editLeadChildren").val(t.children || 0);
          $("#editLeadInfants").val(t.infants || 0);

          const rawReq = t.requirements || "";
          const parsedReq = self.parseRequirementsJson(rawReq);
          $("#editLeadRequirements").val(rawReq);
          self.renderModalRequirementsTable(parsedReq, rawReq);

          $("#modalEditLead").addClass("is-active");

          if (isReqTrigger) {
            setTimeout(function () {
              const $modalBody = $("#modalEditLead .v3-modal-body-scrollable");
              const $reqEl = $("#editLeadRequirementsTabularWrap");
              if ($modalBody.length && $reqEl.length) {
                const targetTop =
                  $reqEl.position().top + $modalBody.scrollTop() - 20;
                $modalBody.animate({ scrollTop: Math.max(0, targetTop) }, 200);
              }
            }, 180);
          } else {
            $("#modalEditLead .v3-modal-body-scrollable").scrollTop(0);
          }

          // Initialize Select2 for Tour Package / Destination via Packages API
          self.initPackageSelect2(
            $("#editLeadPackage"),
            $("#modalEditLead"),
            "Choose a tour package or type custom destination...",
          );

          // Initialize Select2 with Cities API for locations
          self.initCitySelect2(
            $("#editLeadCurrentLocation"),
            $("#modalEditLead"),
            "-- Search / Select Current Location --",
          );
          self.initCitySelect2(
            $("#editLeadPickup"),
            $("#modalEditLead"),
            "-- Search / Select Pickup Point / City --",
          );
          self.initCitySelect2(
            $("#editLeadDrop"),
            $("#modalEditLead"),
            "-- Search / Select Drop Point / City --",
          );

          // Helper to set Select2 option value
          function setSelect2Val($el, val, text) {
            if (!$el || !$el.length) return;
            $el.empty();
            if (val) {
              $el.append(new Option(text || val, val, true, true));
            }
            $el.val(val || "").trigger("change");
          }

          const pickupVal = t.pickup_location || "Delhi";
          const dropVal = t.drop_location || pickupVal;

          setSelect2Val(
            $("#editLeadCurrentLocation"),
            t.current_location || "",
          );
          setSelect2Val($("#editLeadPickup"), pickupVal, pickupVal);
          setSelect2Val($("#editLeadDrop"), dropVal, dropVal);
          $("#editLeadDrop").data(
            "userModified",
            t.drop_location && t.drop_location !== pickupVal,
          );

          const initialSubject = (d.subject || "").trim();
          const initialDest = (t.destination || "").trim();
          const initialPkgVal = initialSubject || initialDest;

          if (initialPkgVal) {
            setSelect2Val($("#editLeadPackage"), initialPkgVal, initialPkgVal);

            // Fetch package details from API to populate snapshot card if exact or matching package
            $.ajax({
              url:
                "/api/v3/packages/options/?q=" +
                encodeURIComponent(initialPkgVal),
              dataType: "json",
              success: function (res) {
                if (
                  res &&
                  res.data &&
                  res.data.all &&
                  res.data.all.length > 0
                ) {
                  const match =
                    res.data.all.find(
                      (p) =>
                        p.title.toLowerCase() === initialPkgVal.toLowerCase() ||
                        p.city.toLowerCase() === initialPkgVal.toLowerCase() ||
                        initialPkgVal
                          .toLowerCase()
                          .includes(p.title.toLowerCase()),
                    ) || res.data.all[0];

                  if (match) {
                    const isIntl =
                      (match.type || "").toLowerCase() === "international";
                    const dur = match.duration || 0;
                    const days = match.days || (dur > 0 ? dur + 1 : 1);
                    $("#editCardPkgTitle").text(match.title);
                    $("#editCardPkgTypeBadge")
                      .text(isIntl ? "International" : "Domestic")
                      .attr(
                        "class",
                        `v3-pkg-type-pill ${isIntl ? "intl" : "dom"}`,
                      );
                    $("#editCardPkgDuration").html(
                      `<i class="fi fi-rr-moon"></i> <strong>${dur}N / ${days}D</strong>`,
                    );
                    $("#editCardPkgLocation").html(
                      `<i class="fi fi-rr-marker"></i> ${match.city}`,
                    );
                    $("#editCardPkgTheme").html(
                      `<i class="fi fi-rr-star"></i> ${match.theme}`,
                    );
                    $("#editCardPkgIcon").html(
                      `<i class="fi ${isIntl ? "fi-rr-plane" : "fi-rr-route"}"></i>`,
                    );
                    $("#editLeadSelectedPkgCard").show();
                  }
                }
              },
            });
          } else {
            setSelect2Val($("#editLeadPackage"), "");
            $("#editLeadSelectedPkgCard").hide();
          }

          self.initSingleDatePickers();
          if (
            t.date_of_travel &&
            $("#editLeadTravelDate").data("daterangepicker")
          ) {
            if (
              typeof moment !== "undefined" &&
              moment(t.date_of_travel, "YYYY-MM-DD", true).isValid()
            ) {
              $("#editLeadTravelDate")
                .data("daterangepicker")
                .setStartDate(moment(t.date_of_travel, "YYYY-MM-DD"));
              $("#editLeadTravelDate")
                .data("daterangepicker")
                .setEndDate(moment(t.date_of_travel, "YYYY-MM-DD"));
            }
          }
        },
      );

      // Package select change in Edit Lead Modal: Update Snapshot Card, Duration, and Subject
      $(document).on("select2:select", "#editLeadPackage", function (e) {
        const data = e.params ? e.params.data : null;
        if (!data) return;

        const isIntl = (data.type || "").toLowerCase() === "international";
        const duration = parseInt(data.duration) || 0;
        const days = data.days || (duration > 0 ? duration + 1 : 1);
        const city = data.city || data.text || "";
        const theme = data.theme || "Standard";

        // Auto-fill Package / Subject
        $("#editLeadSubject").val(data.text || data.title || city);

        // Auto-fill duration if package has duration
        if (duration > 0) {
          $("#editLeadDuration").val(duration);
        }

        // Render selected package snapshot card
        if (data.pkg_id) {
          $("#editCardPkgTitle").text(data.text || data.title);
          $("#editCardPkgTypeBadge")
            .text(isIntl ? "International" : "Domestic")
            .attr("class", `v3-pkg-type-pill ${isIntl ? "intl" : "dom"}`);
          $("#editCardPkgDuration").html(
            `<i class="fi fi-rr-moon"></i> <strong>${duration}N / ${days}D</strong>`,
          );
          $("#editCardPkgLocation").html(
            `<i class="fi fi-rr-marker"></i> ${city}`,
          );
          $("#editCardPkgTheme").html(`<i class="fi fi-rr-star"></i> ${theme}`);
          $("#editCardPkgIcon").html(
            `<i class="fi ${isIntl ? "fi-rr-plane" : "fi-rr-route"}"></i>`,
          );
          $("#editLeadSelectedPkgCard").slideDown(150);
        } else {
          $("#editLeadSelectedPkgCard").slideUp(150);
        }
      });

      // Clear package snapshot card in modal
      $(document).on("click", "#editBtnClearPkgCard", function () {
        $("#editLeadPackage").val(null).trigger("change");
        $("#editLeadSelectedPkgCard").slideUp(150);
      });

      // Duration input change in modal: dynamically update snapshot badge (Nights / Days)
      $(document).on("input change keyup", "#editLeadDuration", function () {
        const nights = parseInt($(this).val(), 10);
        if (!isNaN(nights) && nights > 0) {
          const days = nights + 1;
          $("#editCardPkgDuration")
            .html(
              `<i class="fi fi-rr-moon"></i> <strong>${nights}N / ${days}D</strong>`,
            )
            .show();
        } else {
          $("#editCardPkgDuration").hide();
        }
      });

      $(document).on("change", "#editLeadPackage", function () {
        if (!$(this).val()) {
          $("#editLeadSelectedPkgCard").slideUp(150);
        }
      });

      // Auto-default drop point to chosen pickup point in modal
      $(document).on("change select2:select", "#editLeadPickup", function () {
        const pickupVal = $(this).val();
        if (pickupVal) {
          const currentDrop = $("#editLeadDrop").val();
          if (
            !currentDrop ||
            currentDrop === pickupVal ||
            !$("#editLeadDrop").data("userModified")
          ) {
            $("#editLeadDrop")
              .empty()
              .append(new Option(pickupVal, pickupVal, true, true))
              .trigger("change");
          }
        }
      });

      $(document).on("select2:select change", "#editLeadDrop", function () {
        const dropVal = $(this).val();
        const pickupVal = $("#editLeadPickup").val();
        if (dropVal && dropVal !== pickupVal) {
          $("#editLeadDrop").data("userModified", true);
        }
      });

      // Split name when space entered in First Name if Last Name is empty
      $(document).on("blur change", "#editLeadFirstName", function () {
        const val = $(this).val().trim();
        const currentLast = $("#editLeadLastName").val().trim();
        if (!currentLast && val.indexOf(" ") !== -1) {
          const parts = val.split(/\s+/);
          if (parts.length > 1) {
            $(this).val(parts[0]);
            $("#editLeadLastName").val(parts.slice(1).join(" "));
          }
        }
      });

      // Submit Edit Lead
      $(document).on("click", "#btnSubmitModalEditLead", function () {
        const currentLeadId =
          $("#editLeadId").val() ||
          self.leadId ||
          self.getLeadId() ||
          (self.leadData ? self.leadData.id || self.leadData.encoded_id : "");
        if (!currentLeadId) {
          self.notify("Lead ID is missing. Please reload the page.", "error");
          return;
        }

        // Process tabular requirements & key mapping
        let retainedFieldData = [];
        let retainedRootKeys = {};
        let removedKeys = [];
        let hasTabularRows = false;
        let hadFieldDataOrigin = false;

        $("#editLeadRequirementsTabularWrap .v3-req-map-row").each(function () {
          hasTabularRows = true;
          const $row = $(this);
          const rowKey = (
            $row.find(".v3-req-key-input").val() ||
            $row.data("json-key") ||
            ""
          ).trim();
          const rowVal = ($row.find(".v3-req-val-input").val() || "").trim();
          const mapping = $row.find(".v3-req-map-select").val();
          const isFromFd =
            $row.attr("data-from-field-data") === "1" ||
            $row.find(".v3-req-from-field-data").val() === "1";

          if (isFromFd) {
            hadFieldDataOrigin = true;
          }

          if (!rowKey) return;

          if (mapping && mapping !== "do_not_map") {
            // Apply mapped value to modal field
            self.applyMappedValueToField(mapping, rowVal);
            // Collect removed key so it is deleted from JSON
            removedKeys.push(rowKey);
          } else {
            // Keep in JSON requirements
            if (isFromFd) {
              retainedFieldData.push({ name: rowKey, values: [rowVal] });
            } else {
              retainedRootKeys[rowKey] = rowVal;
            }
          }
        });

        let finalReq = "";
        if (hasTabularRows) {
          if (hadFieldDataOrigin) {
            let payload = {};
            if (retainedFieldData.length > 0) {
              payload.field_data = retainedFieldData;
            }
            Object.keys(retainedRootKeys).forEach(function (rk) {
              payload[rk] = retainedRootKeys[rk];
            });
            finalReq =
              Object.keys(payload).length > 0 ? JSON.stringify(payload) : "";
          } else {
            finalReq =
              Object.keys(retainedRootKeys).length > 0
                ? JSON.stringify(retainedRootKeys)
                : "";
          }
        } else {
          finalReq = $("#editLeadPlainNotes").length
            ? $("#editLeadPlainNotes").val().trim()
            : $("#editLeadRequirements").val().trim();
        }

        let firstName = $("#editLeadFirstName").val().trim();
        let lastName = $("#editLeadLastName").val().trim();

        if (!lastName && firstName.indexOf(" ") !== -1) {
          const parts = firstName.split(/\s+/);
          firstName = parts[0];
          lastName = parts.slice(1).join(" ");
          $("#editLeadFirstName").val(firstName);
          $("#editLeadLastName").val(lastName);
        }
        const mobile = $("#editLeadMobile").val().trim();

        if (!firstName) {
          self.notify("First name is required.", "warning");
          return;
        }
        if (!mobile) {
          self.notify("Primary mobile is required.", "warning");
          return;
        }

        const travelDate = $("#editLeadTravelDate").val();
        if (travelDate && typeof moment !== "undefined") {
          const mDate = moment(travelDate, "YYYY-MM-DD", true);
          if (mDate.isValid() && mDate.isBefore(moment().startOf("day"))) {
            self.notify(
              "Date of travel must be today or a future date.",
              "warning",
            );
            return;
          }
        }

        const packageVal = $("#editLeadPackage").val() || "";
        let subjectVal = $("#editLeadSubject").val().trim();
        if (!subjectVal && packageVal) {
          subjectVal = packageVal;
          $("#editLeadSubject").val(subjectVal);
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Saving...");

        const postData = {
          leadId: currentLeadId,
          lead_id: currentLeadId,
          id: currentLeadId,
          enquiry_id: currentLeadId,
          first_name: firstName,
          last_name: $("#editLeadLastName").val().trim(),
          email: $("#editLeadEmail").val().trim(),
          mobile: mobile,
          alt_mobile: $("#editLeadAltMobile").val().trim(),
          current_location: $("#editLeadCurrentLocation").val() || "",
          destination: packageVal,
          destination_city: packageVal,
          city: packageVal,
          subject: subjectVal || packageVal,
          package: packageVal || subjectVal,
          date_of_travel: $("#editLeadTravelDate").val(),
          duration_days: $("#editLeadDuration").val(),
          pickup_location: $("#editLeadPickup").val() || "",
          drop_location: $("#editLeadDrop").val() || "",
          adults: $("#editLeadAdults").val(),
          children: $("#editLeadChildren").val(),
          infants: $("#editLeadInfants").val(),
          requirements: finalReq,
          removed_keys: JSON.stringify(removedKeys),
        };

        $.ajax({
          url: "/api/v3/leads/update/",
          type: "POST",
          data: postData,
          dataType: "json",
          success: function (res) {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-check"></i> Save Changes');
            if (res.status === "success") {
              $("#modalEditLead").removeClass("is-active");
              if (removedKeys.length > 0) {
                self.notify(
                  `Lead updated. Mapped ${removedKeys.length} key(s) to lead profile and removed from JSON.`,
                  "success",
                );
              } else {
                self.notify("Lead updated successfully", "success");
              }
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to update lead.", "error");
            }
          },
          error: function (xhr) {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-check"></i> Save Changes');
            let responseData = xhr.responseJSON;
            if (!responseData && xhr.responseText) {
              try {
                responseData = JSON.parse(xhr.responseText);
              } catch (e) {
                responseData = null;
              }
            }
            const err =
              (responseData && (responseData.message || responseData.msg)) ||
              (xhr.status
                ? `Unable to save lead (server error ${xhr.status}).`
                : "Network error while updating lead.");
            self.notify(err, "error");
          },
        });
      });

      // 7b. Tabular Requirements & Field Mapping Event Handlers
      $(document).on("click", "#btnAddReqRow, #btnAddFirstReqRow", function () {
        let $body = $("#reqMappingTableBody");
        if (!$body.length) {
          const wrapHtml = `
            <div class="v3-req-mapping-table-card">
              <div class="v3-req-mapping-table-header">
                <div class="v3-req-map-th col-key">Form / JSON Key</div>
                <div class="v3-req-map-th col-val">Customer Response / Value</div>
                <div class="v3-req-map-th col-map">Map to Lead Field</div>
                <div class="v3-req-map-th col-act">Action</div>
              </div>
              <div class="v3-req-mapping-table-body" id="reqMappingTableBody">
              </div>
            </div>
          `;
          $("#editLeadRequirementsTabularWrap").html(wrapHtml);
          $body = $("#reqMappingTableBody");
        }
        const newRow = $(self.buildCustomReqRowHtml());
        $body.append(newRow);
        newRow.find(".v3-req-key-input").focus();
      });

      $(document).on("change", ".v3-req-map-select", function () {
        const $select = $(this);
        const $row = $select.closest(".v3-req-map-row");
        const val = $select.val();
        const rowVal = $row.find(".v3-req-val-input").val();
        const $statusWrap = $row.find(".v3-req-map-status-wrap");

        if (val === "do_not_map") {
          $statusWrap.html(
            `<span class="v3-mapping-status-pill unmapped"><i class="fi fi-rr-document"></i> Retained in JSON</span>`,
          );
        } else {
          const wasFilled = self.autoFillFieldIfEmpty(val, rowVal);
          if (wasFilled) {
            $statusWrap.html(
              `<span class="v3-mapping-status-pill mapped"><i class="fi fi-rr-check"></i> Auto-mapped</span>`,
            );
          } else {
            $statusWrap.html(
              `<span class="v3-mapping-status-pill existing"><i class="fi fi-rr-check"></i> Maps to Field</span>`,
            );
          }
        }
      });

      $(document).on("click", ".btn-remove-req-row", function () {
        const $row = $(this).closest(".v3-req-map-row");
        $row.slideUp(150, function () {
          $row.remove();
          if ($("#reqMappingTableBody .v3-req-map-row").length === 0) {
            $("#editLeadRequirementsTabularWrap").html(`
              <div class="v3-req-mapping-empty">
                <i class="fi fi-rr-document"></i>
                <p>All JSON keys were removed or mapped.</p>
                <button type="button" class="btn-v3-action btn-outline btn-xs" id="btnAddFirstReqRow">
                  <i class="fi fi-rr-plus"></i> Add Note
                </button>
              </div>
            `);
          }
        });
      });

      // 8. Full Screen Itinerary Modal Trigger & Logic
      $(document).on(
        "click",
        "#btnHeroSendItinerary, #btnSideSendItinerary",
        function () {
          const d = self.leadData || {};
          const t = d.travel_details || {};

          // Directly assign Package / Destination value from Lead View
          const isGeneric = function (val) {
            if (!val || typeof val !== "string") return true;
            const s = val.trim().toLowerCase();
            return (
              !s ||
              s === "-" ||
              s === "tour package enquiry" ||
              s === "tour package inquiry" ||
              s === "tour inquiry" ||
              s === "tour enquiry" ||
              s === "general enquiry" ||
              s === "general inquiry" ||
              s === "website inquiry" ||
              s === "website enquiry" ||
              s === "whatsapp inquiry" ||
              s === "whatsapp web inquiry"
            );
          };

          let dest = "";
          if (d.subject && !isGeneric(d.subject)) {
            dest = d.subject.trim();
          } else if (t.destination && !isGeneric(t.destination)) {
            dest = t.destination.trim();
          } else if (d.subject && d.subject !== "-") {
            dest = d.subject.trim();
          } else if (t.destination) {
            dest = t.destination.trim();
          }

          $("#modalSendItinerary").addClass("is-active");

          // Preload stay cities from Cities API, vehicles, and properties in parallel
          self.loadItineraryMeta();

          // Reset state
          $("#itinerarySearchSection").removeClass("v3-hidden");
          $("#btnChangeItinerary").addClass("v3-hidden");
          $("#itinerarySearchLoader").addClass("v3-hidden");
          $("#itineraryNotFoundBox").addClass("v3-hidden");
          $("#itineraryResultContainer").addClass("v3-hidden");
          $("#itineraryCustomSection").addClass("v3-hidden");
          $("#btnPreviewItinerary").addClass("v3-hidden");
          $("#itineraryPackageSelect").html(
            '<option value="">-- Choose an Itinerary Package --</option>',
          );
          $("#itineraryCustomOverview").val("");
          self.selectedPackage = null;

          // Initialize V3 Cities API for destination
          self.initCitySelect2(
            $("#itineraryDestinationInput"),
            $("#modalSendItinerary"),
            "-- Search or Select Destination City / Country --",
          );

          if (self.leadData) {
            $("#itinModalLeadBadge").text(
              `Lead #${self.leadData.id}: ${self.leadData.name}`,
            );
            if (dest) {
              const $destInput = $("#itineraryDestinationInput");
              $destInput
                .empty()
                .append(new Option(dest, dest, true, true))
                .val(dest)
                .trigger("change");
            }
          }

          // Initialize Select2 for travel style
          if (typeof $.fn.select2 !== "undefined") {
            $("#itineraryTravelStyle").select2({
              dropdownParent: $("#modalSendItinerary"),
              width: "100%",
              minimumResultsForSearch: Infinity,
            });
          }
        },
      );

      // Find Itineraries in Modal
      $(document).on("click", "#btnFindItineraries", function () {
        const dest = $("#itineraryDestinationInput").val();
        const theme = $("#itineraryTravelStyle").val() || "";

        if (!dest) {
          self.notify("Please select or type a destination.", "warning");
          return;
        }

        const $btn = $(this);
        $btn
          .prop("disabled", true)
          .html('<i class="fi fi-rr-spinner fi-spin"></i> Searching...');

        $("#itinerarySearchLoaderText").text(
          `Searching matching tour packages for "${dest}"...`,
        );
        $("#itinerarySearchLoader").removeClass("v3-hidden");
        $("#itineraryNotFoundBox").addClass("v3-hidden");
        $("#itineraryResultContainer").addClass("v3-hidden");
        $("#itineraryCustomSection").addClass("v3-hidden");
        $("#btnPreviewItinerary").addClass("v3-hidden");

        $.ajax({
          url: "/api/v3/itinerary/list/",
          type: "GET",
          data: {
            destination: dest,
            theme: theme,
          },
          dataType: "json",
          success: function (res) {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-search"></i> Search Packages');
            $("#itinerarySearchLoader").addClass("v3-hidden");

            if (
              res &&
              res.status === "success" &&
              res.itineraryList &&
              res.itineraryList.length > 0
            ) {
              self.availablePackages = res.itineraryList;
              $("#itineraryNotFoundBox").addClass("v3-hidden");
              $("#itineraryPackageCountBadge").text(
                `${res.itineraryList.length} Packages Found`,
              );

              let options = `<option value="">-- Choose an Itinerary Package (${res.itineraryList.length} Available) --</option>`;
              res.itineraryList.forEach(function (pkg, idx) {
                const adultRate = parseFloat(
                  pkg.tour_package_price_adult || 0,
                ).toLocaleString("en-IN");
                let duration = "";
                const rawDur =
                  pkg.tour_package_duration !== undefined &&
                  pkg.tour_package_duration !== null
                    ? String(pkg.tour_package_duration).trim()
                    : "";

                let nights = 0;
                let days = 0;

                if (/^\d+$/.test(rawDur)) {
                  nights = parseInt(rawDur, 10);
                } else if (
                  Array.isArray(pkg.parsed_itinerary) &&
                  pkg.parsed_itinerary.length > 1
                ) {
                  nights = pkg.parsed_itinerary.length - 1;
                }

                if (
                  Array.isArray(pkg.parsed_itinerary) &&
                  pkg.parsed_itinerary.length > 0
                ) {
                  days = pkg.parsed_itinerary.length;
                } else {
                  days = nights > 0 ? nights + 1 : 1;
                }

                if (nights > 0) {
                  duration = `${nights} Night${nights > 1 ? "s" : ""} / ${days} Day${days > 1 ? "s" : ""}`;
                } else if (days > 0) {
                  duration = `${days} Day${days > 1 ? "s" : ""}`;
                } else {
                  duration = "Standard";
                }
                options += `<option value="${idx}" data-title="${pkg.tour_package_title}" data-duration="${duration}" data-price="${adultRate}">${pkg.tour_package_title} • ${duration} • ₹${adultRate}/person</option>`;
              });
              options += `<option value="custom" data-title="Create Custom Itinerary from Scratch" data-duration="Custom" data-price="0">Create Custom Itinerary from Scratch</option>`;

              const $pkgSelect = $("#itineraryPackageSelect");
              if ($pkgSelect.hasClass("select2-hidden-accessible")) {
                $pkgSelect.select2("destroy");
              }

              $pkgSelect.html(options).val("");
              $("#itineraryResultContainer").removeClass("v3-hidden");

              if (typeof $.fn.select2 !== "undefined") {
                $pkgSelect.select2({
                  dropdownParent: $("#modalSendItinerary"),
                  width: "100%",
                  placeholder: "Choose an Itinerary Package...",
                  templateResult: function (opt) {
                    if (!opt.id || opt.id === "") return opt.text;
                    const $el = $(opt.element);
                    const title = $el.data("title") || opt.text;
                    const duration = $el.data("duration") || "";
                    const price = $el.data("price") || "";

                    if (opt.id === "custom") {
                      return $(`
                        <div class="v3-select2-pkg-row">
                          <span class="v3-select2-pkg-title"><i class="fi fi-rr-plus"></i> ${title}</span>
                        </div>
                      `);
                    }

                    return $(`
                      <div class="v3-select2-pkg-row">
                        <span class="v3-select2-pkg-title">${title}</span>
                        <div class="v3-select2-pkg-meta">
                          ${duration ? `<span class="v3-select2-pkg-badge"><i class="fi fi-rr-calendar"></i> ${duration}</span>` : ""}
                          <span class="v3-select2-pkg-price">₹${price} / person</span>
                        </div>
                      </div>
                    `);
                  },
                  templateSelection: function (opt) {
                    if (!opt.id || opt.id === "") return opt.text;
                    const $el = $(opt.element);
                    const title = $el.data("title") || opt.text;
                    const duration = $el.data("duration") || "";
                    const price = $el.data("price") || "";
                    if (opt.id === "custom") return title;
                    return $(
                      `<span><strong>${title}</strong> ${duration ? `(${duration})` : ""} • ₹${price}/person</span>`,
                    );
                  },
                });
                $pkgSelect.off("select2:select").on("select2:select", function () {
                  $(this).trigger("change");
                });
              }
            } else {
              // Show Inline No Package Found Box
              self.availablePackages = [];
              $("#itineraryResultContainer").addClass("v3-hidden");
              $("#itineraryCustomSection").addClass("v3-hidden");
              $("#btnPreviewItinerary").addClass("v3-hidden");
              $("#itineraryNotFoundDesc").text(
                `No predefined tour packages found for "${dest}". You can build a customized itinerary from scratch.`,
              );
              $("#itineraryNotFoundBox").removeClass("v3-hidden");
            }
          },
          error: function () {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-search"></i> Search Packages');
            $("#itinerarySearchLoader").addClass("v3-hidden");
            self.availablePackages = [];
            $("#itineraryResultContainer").addClass("v3-hidden");
            $("#itineraryCustomSection").addClass("v3-hidden");
            $("#btnPreviewItinerary").addClass("v3-hidden");
            $("#itineraryNotFoundDesc").text(
              `No predefined tour packages found for "${dest}". You can build a customized itinerary from scratch.`,
            );
            $("#itineraryNotFoundBox").removeClass("v3-hidden");
          },
        });
      });

      // Quick CTA from No-Package-Found box to start custom plan
      $(document).on("click", "#btnStartCustomItinFromEmpty", function () {
        const dest =
          $("#itineraryDestinationInput").val() ||
          (self.leadData ? self.leadData.subject : "Custom Tour");
        self.selectedPackage = {
          tour_package_id: 0,
          tour_package_title: dest + " Custom Tour Package",
        };
        self.populateCustomizerFields(self.selectedPackage);
      });

      // Package Select Change - Reveal customization options only after an option is selected
      $(document).on("change select2:select", "#itineraryPackageSelect", function (e) {
        let val = $(this).val();
        if (
          (val === null || val === undefined || val === "") &&
          e &&
          e.params &&
          e.params.data &&
          e.params.data.id !== undefined
        ) {
          val = e.params.data.id;
          $(this).val(val);
        }

        if (val === null || val === undefined || val === "") {
          $("#itineraryCustomSection").addClass("v3-hidden");
          $("#btnPreviewItinerary").addClass("v3-hidden");
          self.selectedPackage = null;
          return;
        }

        if ($(this).hasClass("select2-hidden-accessible")) {
          $(this).select2("close");
        }

        if (val === "custom") {
          const dest =
            $("#itineraryDestinationInput").val() ||
            (self.leadData ? self.leadData.subject : "Custom Destination");
          self.selectedPackage = {
            tour_package_id: 0,
            tour_package_title: dest + " Tour Package",
          };
          self.populateCustomizerFields(self.selectedPackage);
        } else if (self.availablePackages && self.availablePackages[val]) {
          const pkg = self.availablePackages[val];
          self.selectedPackage = pkg;
          self.populateCustomizerFields(pkg);
        }
      });

      // Add Day (Top & Bottom CTA)
      $(document).on(
        "click",
        "#btnAddItinDayTop, #btnAddItinDayBottom",
        function () {
          const nextNum = $("#itinDaysStack .v3-itin-day-card").length + 1;
          self.appendDayCard(
            {
              day: `Day ${nextNum} - Sightseeing & Exploration`,
              overview: "",
              hotel: "",
              meal_plan: "CP",
              vehicle: "",
              transfer_notes: "",
            },
            nextNum,
          );
        },
      );

      // Hotel Show / Hide Switch Slider Toggle on Day Card
      $(document).on("change", ".itin-day-show-hotel-toggle", function () {
        const isShow = $(this).is(":checked");
        const $card = $(this).closest(".v3-itin-day-card");
        const $box = $(this).closest(".v3-itin-hotel-sub-box");
        const $fields = $box.find(".v3-itin-hotel-fields-wrap");
        const $notice = $box.find(".v3-itin-hotel-hidden-notice");

        if (isShow) {
          $fields.removeClass("is-hotel-hidden");
          $notice.addClass("v3-hidden");

          // Ensure form controls inside fields wrap are enabled so user can edit/pick hotels
          const $hotelSelect = $fields.find(".itin-day-hotel-select");
          const $roomSelect = $fields.find(".itin-day-room-type");
          $hotelSelect.prop("disabled", false);
          $roomSelect.prop("disabled", false);

          const curVal = ($hotelSelect.val() || "").trim();
          if (
            !curVal ||
            curVal === "no_hotel" ||
            $hotelSelect.find("option").length <= 1
          ) {
            const selectedCity = $card.find(".itin-day-city-select").val() || "";
            const selectedCategory =
              $fields.find(".itin-day-hotel-stars").val() || "all";
            const cityLower = (selectedCity || "").toLowerCase().trim();
            const isNoStay =
              cityLower.includes("overnight") ||
              cityLower.includes("transit") ||
              cityLower.includes("tour end") ||
              cityLower.includes("trip end") ||
              cityLower.includes("no stay") ||
              cityLower.includes("no hotel") ||
              cityLower.includes("departure");

            const targetCity = isNoStay
              ? self.resolvePrimaryCity() || ""
              : selectedCity;
            $hotelSelect.html(
              self.buildHotelSelectHtml(targetCity, selectedCategory, "", 0),
            );
            if (typeof $.fn.select2 !== "undefined") {
              if ($hotelSelect.hasClass("select2-hidden-accessible")) {
                $hotelSelect.select2("destroy");
              }
              $hotelSelect.select2(
                self.getHotelSelect2Opts($("#modalSendItinerary")),
              );
            }
            $hotelSelect.trigger("change");
          }
        } else {
          $fields.addClass("is-hotel-hidden");
          $notice.removeClass("v3-hidden");
        }
      });

      // Bulk Show / Hide Hotels across all days
      $(document).on("click", "#btnBulkShowHotels", function () {
        $("#itinDaysStack .v3-itin-day-card").each(function () {
          const $toggle = $(this).find(".itin-day-show-hotel-toggle");
          if (!$toggle.is(":checked")) {
            $toggle.prop("checked", true).trigger("change");
          }
        });
      });

      $(document).on("click", "#btnBulkHideHotels", function () {
        $("#itinDaysStack .v3-itin-day-card").each(function () {
          const $toggle = $(this).find(".itin-day-show-hotel-toggle");
          if ($toggle.is(":checked")) {
            $toggle.prop("checked", false).trigger("change");
          }
        });
      });

      // Delete Day
      $(document).on("click", ".btn-delete-itin-day", function () {
        if ($("#itinDaysStack .v3-itin-day-card").length <= 1) {
          self.notify(
            "At least 1 day is required in the itinerary.",
            "warning",
          );
          return;
        }
        $(this).closest(".v3-itin-day-card").remove();
        self.reindexDays();
      });

      // Recalculate total price on traveler count or per-person rate change
      $(document).on(
        "input change",
        "#itineraryCustomAdultCount, #itineraryCustomChildCount, #itineraryCustomInfantCount, #itineraryCustomAdultPrice, #itineraryCustomChildPrice, #itineraryCustomInfantPrice",
        function () {
          const adults = parseInt($("#itineraryCustomAdultCount").val()) || 0;
          const children = parseInt($("#itineraryCustomChildCount").val()) || 0;
          const infants = parseInt($("#itineraryCustomInfantCount").val()) || 0;

          const ap = parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
          const cp = parseFloat($("#itineraryCustomChildPrice").val()) || 0;
          const ip = parseFloat($("#itineraryCustomInfantPrice").val()) || 0;

          const total = ap * adults + cp * children + ip * infants;
          $("#itineraryCustomTotalPrice").val(total);

          const travelerParts = [];
          if (adults) travelerParts.push(`${adults} Adults`);
          if (children) travelerParts.push(`${children} Children`);
          if (infants) travelerParts.push(`${infants} Infants`);
          $("#itinTravelerCountBadge").text(
            travelerParts.join(", ") || "0 Travelers",
          );
        },
      );

      // Night Stay / City Dropdown Change on Itinerary Day
      $(document).on("change", ".itin-day-city-select", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const selectedCity = $(this).val();
        const selectedCategory =
          $card.find(".itin-day-hotel-stars").val() || "all";
        const $hotelSelect = $card.find(".itin-day-hotel-select");
        const $roomSelect = $card.find(".itin-day-room-type");
        const $customInput = $card.find(".itin-day-hotel-custom");
        const $toggle = $card.find(".itin-day-show-hotel-toggle");

        const cityLower = (selectedCity || "").toLowerCase().trim();
        const noStayCity =
          cityLower.includes("overnight") ||
          cityLower.includes("transit") ||
          cityLower.includes("tour end") ||
          cityLower.includes("trip end") ||
          cityLower.includes("no stay") ||
          cityLower.includes("no hotel") ||
          cityLower.includes("departure");

        if ($hotelSelect.hasClass("select2-hidden-accessible")) {
          $hotelSelect.select2("destroy");
        }

        if (noStayCity) {
          // Disable toggle switch by default (Hide in itinerary)
          // The toggle switch itself remains interactive so user can re-enable at any time
          if ($toggle.is(":checked")) {
            $toggle.prop("checked", false).trigger("change");
          }

          let noStayLabel = "No Hotel Stay";
          if (
            cityLower.includes("overnight") ||
            cityLower.includes("transit")
          ) {
            noStayLabel = "Overnight / In Transit — No Hotel Stay";
          } else if (
            cityLower.includes("end") ||
            cityLower.includes("departure")
          ) {
            noStayLabel = "Tour End / Departure — No Hotel Stay";
          } else if (cityLower.includes("self")) {
            noStayLabel = "Self Arranged — No Hotel Stay";
          }
          $hotelSelect
            .html(`<option value="no_hotel" selected>${noStayLabel}</option>`);
          $customInput.addClass("v3-hidden");
          $roomSelect
            .html(`<option value="">Not Applicable (No Stay)</option>`);
          if (
            typeof $.fn.select2 !== "undefined" &&
            $roomSelect.hasClass("select2-hidden-accessible")
          ) {
            $roomSelect.trigger("change.select2");
          }
          return;
        }

        // Normal city — re-enable toggle switch if it was disabled due to no-stay
        if (!$toggle.is(":checked")) {
          $toggle.prop("checked", true).trigger("change");
        }

        $hotelSelect.prop("disabled", false);
        $roomSelect.prop("disabled", false);

        const updateDropdown = () => {
          $hotelSelect.html(
            self.buildHotelSelectHtml(selectedCity, selectedCategory, "", 0),
          );
          if (typeof $.fn.select2 !== "undefined") {
            $hotelSelect.select2(
              self.getHotelSelect2Opts($("#modalSendItinerary")),
            );
          }
          $hotelSelect.trigger("change");
        };

        // If properties for this city are not in self.propertiesList, fetch them dynamically
        const hasCityProps = (self.propertiesList || []).some(
          (p) => (p.city || "").toLowerCase().trim() === cityLower,
        );

        if (!hasCityProps && selectedCity) {
          $.ajax({
            url: `/api/v3/packages/hotels/?city=${encodeURIComponent(selectedCity)}`,
            type: "GET",
            dataType: "json",
            success: function (cRes) {
              if (cRes && cRes.hotels && cRes.hotels.length > 0) {
                cRes.hotels.forEach((h) => {
                  if (!self.propertiesList.some((p) => p.id === h.id)) {
                    self.propertiesList.push(h);
                  }
                  if (h.rooms && Array.isArray(h.rooms)) {
                    self.hotelRoomsCache = self.hotelRoomsCache || {};
                    self.hotelRoomsCache[h.id] = h.rooms;
                  }
                });
              }
              updateDropdown();
            },
            error: function () {
              updateDropdown();
            },
          });
        } else {
          updateDropdown();
        }
      });

      // Stay Type / Rating Change -> Filter Hotels
      $(document).on("change", ".itin-day-hotel-stars", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const selectedCategory = $(this).val();
        const selectedCity = $card.find(".itin-day-city-select").val();
        const $hotelSelect = $card.find(".itin-day-hotel-select");
        const currentHotelVal = $hotelSelect.val();
        const currentHotelName = $hotelSelect
          .find("option:selected")
          .text()
          .replace(/\s+—\s+.*$/, "")
          .trim();

        if ($hotelSelect.hasClass("select2-hidden-accessible")) {
          $hotelSelect.select2("destroy");
        }

        $hotelSelect.html(
          self.buildHotelSelectHtml(
            selectedCity,
            selectedCategory,
            currentHotelName,
            currentHotelVal,
          ),
        );

        if (typeof $.fn.select2 !== "undefined") {
          $hotelSelect.select2(
            self.getHotelSelect2Opts($("#modalSendItinerary")),
          );
        }
        $hotelSelect.trigger("change");
      });

      // Hotel Select Change on Itinerary Day
      $(document).on("change", ".itin-day-hotel-select", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const propId = $(this).val();
        const $customInput = $card.find(".itin-day-hotel-custom");
        const $roomSelect = $card.find(".itin-day-room-type");
        const $starSelect = $card.find(".itin-day-hotel-stars");

        if (propId === "custom") {
          $customInput.removeClass("v3-hidden").focus();
        } else {
          $customInput.addClass("v3-hidden");
        }

        const selectedOption = $(this).find("option:selected");
        const pType = (selectedOption.data("type") || "").toLowerCase();
        const pCity = selectedOption.data("city");

        if (pCity && !$card.find(".itin-day-city-select").val()) {
          $card
            .find(".itin-day-city-select")
            .val(pCity)
            .trigger("change.select2");
        }

        // Sync star rating/category if property has a valid type
        if (pType && $starSelect.length) {
          $starSelect.val(pType).trigger("change.select2");
        }

        self.loadRoomTypesForHotel(propId, $roomSelect, "");
      });

      // Room Type Custom Toggle
      $(document).on("change", ".itin-day-room-type", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const val = $(this).val();
        const $custom = $card.find(".itin-day-room-type-custom");
        if (val === "custom") {
          $custom.removeClass("v3-hidden").focus();
        } else {
          $custom.addClass("v3-hidden");
        }
      });

      // Vehicle Type Custom Toggle
      $(document).on("change", ".itin-day-vehicle-type", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const val = $(this).val();
        const $custom = $card.find(".itin-day-vehicle-custom");
        if (val === "custom") {
          $custom.removeClass("v3-hidden").focus();
        } else {
          $custom.addClass("v3-hidden");
        }
      });

      // Helper: Publish/Download Itinerary
      self.publishItinerary = function (isDownload, $btn, onSuccess, onError) {
        const title =
          $("#itineraryCustomTitle").val().trim() || "Custom Tour Itinerary";
        const totalPrice =
          parseFloat($("#itineraryCustomTotalPrice").val()) || 0;
        const adultPrice =
          parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
        const childPrice =
          parseFloat($("#itineraryCustomChildPrice").val()) || 0;
        const infantPrice =
          parseFloat($("#itineraryCustomInfantPrice").val()) || 0;

        if (!title) {
          self.notify("Itinerary title is required.", "warning");
          if (typeof onError === "function") onError("title_required");
          return;
        }

        const structuredItin = [];
        $("#itinDaysStack .v3-itin-day-card").each(function () {
          const dayTitle = $(this).find(".itin-day-title").val().trim();
          const $overviewTextarea = $(this).find(".itin-day-overview");
          const editor = $overviewTextarea.data("v3Editor");
          const dayOverview = (
            editor ? editor.getContent() : $overviewTextarea.val()
          ).trim();
          const stayCity = $(this).find(".itin-day-city-select").val() || "";

          let hotelName = "";
          const $hotelSel = $(this).find(".itin-day-hotel-select");
          const hotelVal = $hotelSel.val();
          if (hotelVal === "custom") {
            hotelName = $(this).find(".itin-day-hotel-custom").val().trim();
          } else if (hotelVal && hotelVal !== "no_hotel") {
            hotelName = $hotelSel
              .find("option:selected")
              .text()
              .replace(/\s+—\s+.*$/, "")
              .trim();
          } else {
            hotelName =
              $(this).find(".itin-day-hotel-custom").val().trim() || "";
          }

          const hotelStars = $(this).find(".itin-day-hotel-stars").val();

          let roomType =
            $(this).find(".itin-day-room-type").val() || "Deluxe Room";
          if (roomType === "custom") {
            roomType =
              $(this).find(".itin-day-room-type-custom").val().trim() ||
              "Deluxe Room";
          }

          const mealPlan = $(this).find(".itin-day-meal-plan").val();
          let vehicleType = $(this).find(".itin-day-vehicle-type").val();
          if (vehicleType === "custom" || !vehicleType) {
            vehicleType =
              $(this).find(".itin-day-vehicle-custom").val().trim() ||
              "Private Sedan (Dzire / Etios)";
          }
          const transferNotes = $(this)
            .find(".itin-day-transfer-notes")
            .val()
            .trim();

          const dayImage = $(this).attr("data-day-image") || "";
          const hotelId = parseInt(hotelVal) > 0 ? parseInt(hotelVal) : 0;
          const roomTypeId =
            parseInt(
              $(this).find(".itin-day-room-type option:selected").data("id") ||
                0,
            ) || 0;

          const $toggle = $(this).find(".itin-day-show-hotel-toggle");
          const isHotelShown =
            $toggle.length > 0
              ? $toggle.is(":checked")
              : $(this).find(".itin-day-show-hotel-radio:checked").val() !== "0";

          structuredItin.push({
            day: dayTitle,
            city: stayCity,
            stay_city: stayCity,
            overview: dayOverview,
            hotel: isHotelShown ? hotelName : "",
            hotel_id: isHotelShown ? hotelId : 0,
            hotel_title: isHotelShown ? hotelName : "",
            stay: isHotelShown ? hotelName : "",
            hotel_stars: isHotelShown ? hotelStars : "",
            room_type: isHotelShown ? roomType : "",
            room_type_id: isHotelShown ? roomTypeId : 0,
            meal_plan: isHotelShown ? mealPlan : "",
            show_hotel: isHotelShown ? 1 : 0,
            is_hotel_hidden: isHotelShown ? 0 : 1,
            hide_hotel: isHotelShown ? 0 : 1,
            saved_hotel: hotelName,
            saved_hotel_id: hotelId,
            saved_hotel_stars: hotelStars,
            saved_room_type: roomType,
            saved_room_type_id: roomTypeId,
            saved_meal_plan: mealPlan,
            vehicle: vehicleType,
            transfer_notes: transferNotes,
            image: dayImage,
            feature_image: dayImage,
            day_image: dayImage,
          });
        });

        if (structuredItin.length === 0) {
          self.notify("Please add at least 1 day to the itinerary.", "warning");
          if (typeof onError === "function") onError("empty_itinerary");
          return;
        }

        const originalBtnHtml = $btn ? $btn.html() : "";
        if ($btn) {
          $btn
            .prop("disabled", true)
            .html(
              isDownload
                ? '<i class="fi fi-rr-spinner fi-spin"></i> Generating PDF...'
                : '<i class="fi fi-rr-spinner fi-spin"></i> Sending...',
            );
        }

        const pkgId =
          self.selectedPackage && self.selectedPackage.tour_package_id
            ? self.selectedPackage.tour_package_id
            : 1;

        const templateToUse =
          (self.selectedPackage &&
            self.selectedPackage.tour_package_template) ||
          self.selectedPackageTemplate ||
          "mit";

        const travelDate =
          $("#itineraryCustomTravelDate").val().trim() ||
          (self.leadData &&
          self.leadData.travel_details &&
          self.leadData.travel_details.date_of_travel
            ? self.leadData.travel_details.date_of_travel
            : "Flexible");

        const adults = parseInt($("#itineraryCustomAdultCount").val()) || 1;
        const children = parseInt($("#itineraryCustomChildCount").val()) || 0;
        const infants = parseInt($("#itineraryCustomInfantCount").val()) || 0;
        const pickupLocation = $("#itineraryCustomPickup").val() || "";
        const dropLocation = $("#itineraryCustomDrop").val() || "";
        const travelTheme = $("#itineraryCustomTheme").val();

        const rawRefId =
          self.leadData && (self.leadData.id || self.leadData.lead_id)
            ? self.leadData.id || self.leadData.lead_id
            : self.leadId || (self.getLeadId ? self.getLeadId() : "");

        const primaryCity = self.resolvePrimaryCity(structuredItin);
        const primaryHotel = self.resolvePrimaryHotel(structuredItin);
        const primaryVehicle = self.resolvePrimaryVehicle(structuredItin);
        const durationDays = structuredItin.length;
        const durationNights = durationDays > 1 ? durationDays - 1 : 1;

        const customerEmail =
          (self.leadData && self.leadData.email) ||
          $("#editLeadEmail").val() ||
          "";
        const customerMobile =
          (self.leadData &&
            (self.leadData.mobile || self.leadData.clean_mobile)) ||
          $("#editLeadPhone").val() ||
          "";

        const postData = {
          id: pkgId,
          refId: rawRefId,
          refType: "enquiry",
          email: customerEmail,
          mobile: customerMobile,
          mrp: adultPrice || 0,
          isDownload: !!isDownload,
          content: {
            title: title,
            city: primaryCity,
            destination: primaryCity,
            tour_package_city: primaryCity,
            travel_date: travelDate,
            pickup_location: pickupLocation,
            drop_location: dropLocation,
            travel_theme: travelTheme,
            theme: travelTheme,
            hotel: primaryHotel,
            hotel_category: self.resolvePrimaryHotelCategory(structuredItin),
            stay_category: self.resolvePrimaryHotelCategory(structuredItin),
            vehicle: primaryVehicle,
            outstation_vehicle: primaryVehicle,
            adults_count: adults,
            children_count: children,
            infants_count: infants,
            template: templateToUse,
            adult_per_person: adultPrice || 0,
            child_per_person: childPrice || 0,
            infant_per_person: infantPrice || 0,
            total_price: totalPrice || 0,
            duration:
              durationNights > 0
                ? `${durationNights} Nights / ${durationDays} Days`
                : `${durationDays} Days`,
            duration_days: durationDays,
            number_of_days: durationNights,
            overview: $("#itineraryCustomOverview").val().trim(),
            itinerary: structuredItin,
            inclusions: $("#itineraryCustomInclusions").val().trim(),
            exclusions: $("#itineraryCustomExclusions").val().trim(),
          },
        };

        $.ajax({
          url: "/api/v3/itinerary/publish/",
          type: "POST",
          contentType: "application/json",
          data: JSON.stringify(postData),
          dataType: "json",
          success: function (res) {
            if ($btn) {
              $btn.prop("disabled", false).html(originalBtnHtml);
            }
            if (res.status === "success" || res.itineraryId) {
              if (typeof onSuccess === "function") {
                onSuccess(res);
              }
            } else {
              if (typeof onError === "function") {
                onError(res);
              }
              self.notify(res.msg || "Failed to process itinerary.", "error");
            }
          },
          error: function (xhr) {
            if ($btn) {
              $btn.prop("disabled", false).html(originalBtnHtml);
            }
            if (typeof onError === "function") {
              onError(xhr);
            }
            let errorMsg = "Network error while processing itinerary.";
            if (
              xhr &&
              xhr.responseJSON &&
              (xhr.responseJSON.msg || xhr.responseJSON.message)
            ) {
              errorMsg = xhr.responseJSON.msg || xhr.responseJSON.message;
            }
            self.notify(errorMsg, "error");
          },
        });
      };

      // Change Itinerary CTA in Builder Modal
      $(document).on("click", "#btnChangeItinerary", function (e) {
        e.preventDefault();
        $("#itinerarySearchSection").removeClass("v3-hidden");
        $("#btnChangeItinerary").addClass("v3-hidden");
        $("#modalSendItinerary .v3-modal-fullscreen-body").animate(
          { scrollTop: 0 },
          250,
        );
      });

      // Preview Itinerary CTA in Builder Modal (Server-driven Iframe Preview using Selected Package Template)
      $(document).off("click", "#btnPreviewItinerary");
      $(document).off("click", "#btnPreviewItineraryBottom");
      $(document).off(
        "click",
        "#btnPreviewItinerary, #btnPreviewItineraryBottom",
      );
      $(document).off(".v3ItinPreview");
      $("#btnPreviewItinerary, #btnPreviewItineraryBottom").off("click");

      $(document).on(
        "click.v3ItinPreview",
        "#btnPreviewItinerary, #btnPreviewItineraryBottom",
        function (e) {
          e.preventDefault();
          e.stopPropagation();
          if (typeof e.stopImmediatePropagation === "function") {
            e.stopImmediatePropagation();
          }

          // 1. Strict global timestamp debounce (rejects any call within 2.5s)
          const now = Date.now();
          if (
            window.__v3LastShowApiCallTime &&
            now - window.__v3LastShowApiCallTime < 2500
          ) {
            return false;
          }
          window.__v3LastShowApiCallTime = now;

          // 2. Global mutex across all script instances and rapid clicks
          if (window._isV3ItineraryShowLoading || self.isPreviewLoading) {
            return false;
          }
          window._isV3ItineraryShowLoading = true;
          self.isPreviewLoading = true;

          const $btns = $("#btnPreviewItinerary, #btnPreviewItineraryBottom");
          $btns.prop("disabled", true);

          const title =
            $("#itineraryCustomTitle").val().trim() || "Custom Tour Itinerary";
          const travelDate =
            $("#itineraryCustomTravelDate").val().trim() ||
            (self.leadData &&
            self.leadData.travel_details &&
            self.leadData.travel_details.date_of_travel
              ? self.leadData.travel_details.date_of_travel
              : "Flexible");
          const totalPrice =
            parseFloat($("#itineraryCustomTotalPrice").val()) || 0;
          const adultPrice =
            parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
          const childPrice =
            parseFloat($("#itineraryCustomChildPrice").val()) || 0;
          const infantPrice =
            parseFloat($("#itineraryCustomInfantPrice").val()) || 0;
          const overview = $("#itineraryCustomOverview").val().trim();
          const inclusions = $("#itineraryCustomInclusions").val().trim();
          const exclusions = $("#itineraryCustomExclusions").val().trim();
          const clientName = self.leadData
            ? self.leadData.name
            : "Valued Customer";
          const adults =
            parseInt($("#itineraryCustomAdultCount").val()) ||
            (self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.adults || 1)
              : 1);
          const children =
            parseInt($("#itineraryCustomChildCount").val()) ||
            (self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.children || 0)
              : 0);
          const infants =
            parseInt($("#itineraryCustomInfantCount").val()) ||
            (self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.infants || 0)
              : 0);

          const structuredItin = [];
          $("#itinDaysStack .v3-itin-day-card").each(function () {
            const dayTitle = $(this).find(".itin-day-title").val().trim();
            const $overviewTextarea = $(this).find(".itin-day-overview");
            const editor = $overviewTextarea.data("v3Editor");
            const dayOverview = (
              editor ? editor.getContent() : $overviewTextarea.val()
            ).trim();
            const stayCity = $(this).find(".itin-day-city-select").val() || "";

            let hotelName = "";
            const $hotelSel = $(this).find(".itin-day-hotel-select");
            const hotelVal = $hotelSel.val();
            if (hotelVal === "custom") {
              hotelName = $(this).find(".itin-day-hotel-custom").val().trim();
            } else if (hotelVal && hotelVal !== "no_hotel") {
              hotelName = $hotelSel
                .find("option:selected")
                .text()
                .replace(/\s+—\s+.*$/, "")
                .trim();
            } else {
              hotelName =
                $(this).find(".itin-day-hotel-custom").val().trim() || "";
            }

            const hotelStars = $(this).find(".itin-day-hotel-stars").val();

            let roomType =
              $(this).find(".itin-day-room-type").val() || "Deluxe Room";
            if (roomType === "custom") {
              roomType =
                $(this).find(".itin-day-room-type-custom").val().trim() ||
                "Deluxe Room";
            }

            const mealPlan = $(this).find(".itin-day-meal-plan").val();
            let vehicleType = $(this).find(".itin-day-vehicle-type").val();
            if (vehicleType === "custom" || !vehicleType) {
              vehicleType =
                $(this).find(".itin-day-vehicle-custom").val().trim() ||
                "Private Sedan (Dzire / Etios)";
            }
            const transferNotes = $(this)
              .find(".itin-day-transfer-notes")
              .val()
              .trim();

            const dayImage = $(this).attr("data-day-image") || "";
            const hotelId = parseInt(hotelVal) > 0 ? parseInt(hotelVal) : 0;
            const roomTypeId =
              parseInt(
                $(this)
                  .find(".itin-day-room-type option:selected")
                  .data("id") || 0,
              ) || 0;

            const $toggle = $(this).find(".itin-day-show-hotel-toggle");
            const isHotelShown =
              $toggle.length > 0
                ? $toggle.is(":checked")
                : $(this).find(".itin-day-show-hotel-radio:checked").val() !== "0";

            structuredItin.push({
              day: dayTitle,
              city: stayCity,
              stay_city: stayCity,
              overview: dayOverview,
              hotel: isHotelShown ? hotelName : "",
              hotel_id: isHotelShown ? hotelId : 0,
              hotel_title: isHotelShown ? hotelName : "",
              stay: isHotelShown ? hotelName : "",
              hotel_stars: isHotelShown ? hotelStars : "",
              room_type: isHotelShown ? roomType : "",
              room_type_id: isHotelShown ? roomTypeId : 0,
              meal_plan: isHotelShown ? mealPlan : "",
              show_hotel: isHotelShown ? 1 : 0,
              is_hotel_hidden: isHotelShown ? 0 : 1,
              hide_hotel: isHotelShown ? 0 : 1,
              saved_hotel: hotelName,
              saved_hotel_id: hotelId,
              saved_hotel_stars: hotelStars,
              saved_room_type: roomType,
              saved_room_type_id: roomTypeId,
              saved_meal_plan: mealPlan,
              vehicle: vehicleType,
              transfer_notes: transferNotes,
              image: dayImage,
              feature_image: dayImage,
              day_image: dayImage,
            });
          });

          if (structuredItin.length === 0) {
            self.isPreviewLoading = false;
            window._isV3ItineraryShowLoading = false;
            $btns.prop("disabled", false);
            self.notify(
              "Please add at least 1 day to preview itinerary.",
              "warning",
            );
            return;
          }

          const templateToUse =
            (self.selectedPackage &&
              self.selectedPackage.tour_package_template) ||
            self.selectedPackageTemplate ||
            "mit";

          const primaryCity = self.resolvePrimaryCity(structuredItin);
          const primaryHotel = self.resolvePrimaryHotel(structuredItin);
          const primaryVehicle = self.resolvePrimaryVehicle(structuredItin);
          const pkgState = self.selectedPackage
            ? self.selectedPackage.tour_package_state ||
              self.selectedPackage.state ||
              ""
            : "";
          const pkgCountry = self.selectedPackage
            ? self.selectedPackage.tour_package_country ||
              self.selectedPackage.country ||
              ""
            : "";
          const durationDays = structuredItin.length;
          const durationNights = durationDays > 1 ? durationDays - 1 : 1;

          const pickupLocation = $("#itineraryCustomPickup").val() || "";
          const dropLocation = $("#itineraryCustomDrop").val() || "";
          const travelTheme = $("#itineraryCustomTheme").val() || "standard";

          const numericLeadId =
            self.leadData && self.leadData.id
              ? parseInt(self.leadData.id)
              : (function () {
                  try {
                    const dec = atob(self.leadId);
                    return /^\d+$/.test(dec) ? parseInt(dec) : self.leadId;
                  } catch (e) {
                    return self.leadId;
                  }
                })();

          const currentAgentId = parseInt(
            (self.leadData && self.leadData.agent_id) ||
              $("#heroAssignAgentSelect").val() ||
              0,
          );

          const pkgId = self.selectedPackage
            ? parseInt(
                self.selectedPackage.tour_package_id ||
                  self.selectedPackage.id ||
                  0,
              )
            : 0;

          const payload = {
            title: title,
            package_title: title,
            package_id: pkgId,
            tour_package_id: pkgId,
            gallery_image_list: self.selectedPackage
              ? self.selectedPackage.gallery_image_list || ""
              : "",
            refId: numericLeadId,
            leadId: numericLeadId,
            lead_id: numericLeadId,
            enquiry_id: numericLeadId,
            agent_id: currentAgentId,
            assigned_agent: currentAgentId,
            city: primaryCity,
            destination: primaryCity,
            tour_package_city: primaryCity,
            state: pkgState,
            country: pkgCountry,
            theme: travelTheme,
            travelTheme: travelTheme,
            travel_theme: travelTheme,
            pickupLocation: pickupLocation,
            dropLocation: dropLocation,
            pickup_location: pickupLocation,
            drop_location: dropLocation,
            adults: adults,
            children: children,
            infants: infants,
            adults_count: adults,
            children_count: children,
            infants_count: infants,
            template: templateToUse,
            hotel: primaryHotel,
            tour_package_hotel: primaryHotel,
            hotel_category: self.resolvePrimaryHotelCategory(structuredItin),
            stay_category: self.resolvePrimaryHotelCategory(structuredItin),
            outstationVehicle: primaryVehicle,
            outstation_vehicle: primaryVehicle,
            vehicle: primaryVehicle,
            totalPrice: totalPrice,
            total_price: totalPrice,
            price: totalPrice,
            adultPrice: adultPrice,
            adult_price: adultPrice,
            adult_per_person: adultPrice,
            childPrice: childPrice,
            child_price: childPrice,
            child_per_person: childPrice,
            infantPrice: infantPrice,
            infant_price: infantPrice,
            infant_per_person: infantPrice,
            clientName: clientName,
            travelDate: travelDate,
            travel_date: travelDate,
            duration:
              durationNights > 0
                ? `${durationNights} Nights / ${durationDays} Days`
                : `${durationDays} Days`,
            duration_days: durationDays,
            number_of_days: durationNights,
            overview: overview,
            inclusions: inclusions,
            exclusions: exclusions,
            days: structuredItin,
            itinerary: structuredItin,
            coverImage: self.selectedPackage
              ? self.selectedPackage.tour_package_image ||
                self.selectedPackage.gallery_image ||
                ""
              : "",
            format: "html",
            hideActions: true,
            preview: true,
          };

          $("#livePreviewHeaderTitle").text(title);
          $("#modalItineraryLivePreview").addClass("is-active");

          const iframe = document.getElementById("livePreviewIframe");
          if (iframe) {
            iframe.srcdoc = "";
          }
          $("#livePreviewLoader").removeClass("v3-hidden");

          if (
            self.activePreviewXhr &&
            typeof self.activePreviewXhr.abort === "function"
          ) {
            try {
              self.activePreviewXhr.abort();
            } catch (err) {}
          }

          self.activePreviewXhr = $.ajax({
            url: "/api/v3/itinerary/show/",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
            dataType: "html",
            success: function (renderedHtml) {
              if (iframe) {
                iframe.srcdoc = renderedHtml;
              }
            },
            error: function (xhr, status) {
              if (status !== "abort") {
                self.notify("Failed to render preview template.", "error");
              }
            },
            complete: function () {
              self.activePreviewXhr = null;
              self.isPreviewLoading = false;
              setTimeout(function () {
                window._isV3ItineraryShowLoading = false;
                $btns.prop("disabled", false);
              }, 400);
              $("#livePreviewLoader").addClass("v3-hidden");
            },
          });
        },
      );

      // Actions inside Live Proposal Preview Modal
      $(document)
        .off("click.v3LiveDlPdf", "#btnLivePreviewDownloadPdf")
        .on("click.v3LiveDlPdf", "#btnLivePreviewDownloadPdf", function () {
          if (self.isDownloadingLivePdf) return;
          self.isDownloadingLivePdf = true;

          const $btn = $(this);
          const origHtml = $btn.html();
          $btn
            .prop("disabled", true)
            .html(
              '<i class="fi fi-rr-spinner fi-spin"></i> <span>Generating PDF...</span>',
            );

          const resetBtn = function () {
            self.isDownloadingLivePdf = false;
            $btn.prop("disabled", false).html(origHtml);
          };

          // 1. Direct Export from rendered Iframe (Fast, no popup blocker, works every time)
          const iframe = document.getElementById("livePreviewIframe");
          let iframeWin = null;
          try {
            iframeWin = iframe
              ? iframe.contentWindow || iframe.contentDocument
              : null;
          } catch (e) {
            iframeWin = null;
          }

          const fallbackDownload = function () {
            self.publishItinerary(
              true,
              $btn,
              function (res) {
                resetBtn();
                const encId = res.encodedId || btoa(String(res.itineraryId));
                const downloadUrl = `/download-itinerary/${encId}/`;
                const $dlFrame = $("<iframe>", {
                  src: downloadUrl,
                  style: "display:none;width:0;height:0;border:0;",
                }).appendTo("body");
                setTimeout(function () {
                  $dlFrame.remove();
                }, 60000);
                self.notify(
                  "Itinerary PDF download initiated!",
                  "success",
                );
              },
              function () {
                resetBtn();
                self.notify("Failed to generate PDF. Please try again.", "error");
              },
            );
          };

          if (iframeWin && typeof iframeWin.downloadItineraryPDF === "function") {
            try {
              const resPromise = iframeWin.downloadItineraryPDF();
              if (resPromise && typeof resPromise.then === "function") {
                resPromise
                  .then(function () {
                    resetBtn();
                    self.notify("Itinerary PDF downloaded successfully!", "success");
                  })
                  .catch(function (err) {
                    console.warn(
                      "In-iframe PDF download failed, falling back to server:",
                      err,
                    );
                    fallbackDownload();
                  });
                return;
              } else {
                setTimeout(function () {
                  resetBtn();
                  self.notify("Itinerary PDF downloaded successfully!", "success");
                }, 1200);
                return;
              }
            } catch (err) {
              console.warn("Iframe export exception:", err);
              fallbackDownload();
              return;
            }
          }

          // Fallback if iframe export is not available
          fallbackDownload();
        });

      $(document)
        .off("click.v3LiveSendItin", "#btnLivePreviewSendItinerary")
        .on(
          "click.v3LiveSendItin",
          "#btnLivePreviewSendItinerary",
          function () {
            if (self.isPublishingItin) return;
            self.isPublishingItin = true;

            self.publishItinerary(
              false,
              $(this),
              function (res) {
                self.isPublishingItin = false;
                $("#modalItineraryLivePreview").removeClass("is-active");
                $("#modalSendItinerary").removeClass("is-active");
                self.notify(
                  res.msg || "Itinerary published and shared with customer!",
                  "success",
                );
                self.loadLeadDetails();
              },
              function () {
                self.isPublishingItin = false;
              },
            );
          },
        );

      $(document)
        .off("click.v3LiveCloseModal", ".v3-preview-close-trigger")
        .on("click.v3LiveCloseModal", ".v3-preview-close-trigger", function () {
          self.isDownloadingLivePdf = false;
          self.isPublishingItin = false;
          $("#modalItineraryLivePreview").removeClass("is-active");
        });

      // 9. Other Modals & Follow-up Presets
      $(document).on(
        "click",
        "#btnHeroSetFollowUp, #btnSideSetFollowUp",
        function () {
          $(".v3-tab-link[data-tab='tab-activity']").trigger("click");
          $(".v3-composer-type-btn[data-type='followup']").trigger("click");
          $("#composerNoteInput").focus();
          self.initDateTimePicker();
        },
      );

      // Follow-Up Quick Preset Chips Click
      $(document).on("click", ".v3-preset-chip", function (e) {
        e.preventDefault();
        const $chip = $(this);
        const $wrap = $chip.closest(".v3-followup-presets-wrap");
        const targetSelector = $chip.closest(".v3-preset-chips").data("target");
        const $targetInput = $(targetSelector);
        const preset = $chip.data("preset");

        $wrap.find(".v3-preset-chip").removeClass("active");
        $chip.addClass("active");

        const now = typeof moment !== "undefined" ? moment() : null;
        if (!now) return;

        let targetMoment;

        switch (preset) {
          case "in_2_hours": {
            targetMoment = now.clone().add(2, "hours");
            const remMin = targetMoment.minute() % 15;
            if (remMin > 0) targetMoment.add(15 - remMin, "minutes");
            targetMoment.second(0);
            if (targetMoment.hour() >= 21 || targetMoment.hour() < 9) {
              targetMoment = now
                .clone()
                .add(1, "days")
                .hour(10)
                .minute(0)
                .second(0);
            }
            break;
          }
          case "tomorrow_morning": {
            let nextDay = now.clone().add(1, "days");
            if (now.day() === 6) nextDay = now.clone().add(2, "days");
            targetMoment = nextDay.hour(10).minute(30).second(0);
            break;
          }
          case "tomorrow_afternoon": {
            let nextDay = now.clone().add(1, "days");
            if (now.day() === 6) nextDay = now.clone().add(2, "days");
            targetMoment = nextDay.hour(15).minute(0).second(0);
            break;
          }
          case "tomorrow_evening": {
            let nextDay = now.clone().add(1, "days");
            if (now.day() === 6) nextDay = now.clone().add(2, "days");
            targetMoment = nextDay.hour(18).minute(0).second(0);
            break;
          }
          case "in_2_days": {
            targetMoment = now
              .clone()
              .add(2, "days")
              .hour(11)
              .minute(0)
              .second(0);
            break;
          }
          case "next_monday": {
            const daysUntilMon = (1 + 7 - now.day()) % 7 || 7;
            targetMoment = now
              .clone()
              .add(daysUntilMon, "days")
              .hour(10)
              .minute(30)
              .second(0);
            break;
          }
          default: {
            targetMoment = self.getDefaultFollowupDateTime();
          }
        }

        const datePart = targetMoment.format("YYYY-MM-DD");
        const time24Part = targetMoment.format("HH:mm");
        const time12Part = targetMoment.format("hh:mm A");
        const formattedStr = datePart + ", " + time12Part;

        $targetInput.val(formattedStr);

        // Sync native date and time inputs
        if (targetSelector === "#modalFollowUpTime") {
          $("#modalFollowUpDate").val(datePart);
          $("#modalFollowUpTimeInput").val(time24Part);
          const $pills = $(
            '.v3-time-quick-pills[data-target="#modalFollowUpTimeInput"]',
          );
          $pills.find(".v3-time-pill").removeClass("active");
          $pills
            .find(`.v3-time-pill[data-time="${time24Part}"]`)
            .addClass("active");
          self.checkSlotConflict(formattedStr, $("#modalSlotStatus"));
        } else if (targetSelector === "#composerFollowupTime") {
          $("#composerFollowupDate").val(datePart);
          $("#composerFollowupTimeInput").val(time24Part);
          const $pills = $(
            '.v3-time-quick-pills[data-target="#composerFollowupTimeInput"]',
          );
          $pills.find(".v3-time-pill").removeClass("active");
          $pills
            .find(`.v3-time-pill[data-time="${time24Part}"]`)
            .addClass("active");
          self.checkSlotConflict(formattedStr, $("#composerSlotStatus"));
        }

        $targetInput.trigger("change");
      });

      // Quick Time Pill Click
      $(document).on("click", ".v3-time-pill", function (e) {
        e.preventDefault();
        const $pill = $(this);
        const time24 = $pill.data("time");
        const targetSelect = $pill
          .closest(".v3-time-quick-pills")
          .data("target");
        const $input = $(targetSelect);

        $pill.siblings(".v3-time-pill").removeClass("active");
        $pill.addClass("active");

        if ($input.length) {
          $input.val(time24).trigger("change");
        }
      });

      // Native Date/Time input changes: Sync master hidden input
      $(document).on(
        "change",
        "#modalFollowUpDate, #modalFollowUpTimeInput",
        function () {
          const dateVal =
            $("#modalFollowUpDate").val() ||
            (typeof moment !== "undefined"
              ? moment().add(1, "days").format("YYYY-MM-DD")
              : "");
          const time24Val = $("#modalFollowUpTimeInput").val() || "10:30";
          let time12 = "10:30 AM";
          if (typeof moment !== "undefined") {
            time12 = moment("2000-01-01 " + time24Val).format("hh:mm A");
          }
          const combined = dateVal + ", " + time12;
          $("#modalFollowUpTime").val(combined);

          const $pills = $(
            '.v3-time-quick-pills[data-target="#modalFollowUpTimeInput"]',
          );
          $pills.find(".v3-time-pill").removeClass("active");
          $pills
            .find(`.v3-time-pill[data-time="${time24Val}"]`)
            .addClass("active");

          self.checkSlotConflict(combined, $("#modalSlotStatus"));
        },
      );

      $(document).on(
        "change",
        "#composerFollowupDate, #composerFollowupTimeInput",
        function () {
          const dateVal =
            $("#composerFollowupDate").val() ||
            (typeof moment !== "undefined"
              ? moment().add(1, "days").format("YYYY-MM-DD")
              : "");
          const time24Val = $("#composerFollowupTimeInput").val() || "10:30";
          let time12 = "10:30 AM";
          if (typeof moment !== "undefined") {
            time12 = moment("2000-01-01 " + time24Val).format("hh:mm A");
          }
          const combined = dateVal + ", " + time12;
          $("#composerFollowupTime").val(combined);

          const $pills = $(
            '.v3-time-quick-pills[data-target="#composerFollowupTimeInput"]',
          );
          $pills.find(".v3-time-pill").removeClass("active");
          $pills
            .find(`.v3-time-pill[data-time="${time24Val}"]`)
            .addClass("active");

          self.checkSlotConflict(combined, $("#composerSlotStatus"));
        },
      );

      // Submit Schedule Follow-Up Modal (if opened directly)
      $(document).on("click", "#btnSubmitModalFollowUp", function () {
        if (!self.leadData || !(parseInt(self.leadData.agent_id || 0) > 0)) {
          self.notify(
            "Lead is unassigned. Please assign an agent first.",
            "warning",
          );
          return;
        }

        const reason = $("#modalFollowUpReason").val() || "General Follow-up";
        const fTime = $("#modalFollowUpTime").val();
        const comment = $("#modalFollowUpComment").val().trim();

        if (!fTime) {
          self.notify(
            "Please select a valid follow-up date and time.",
            "warning",
          );
          return;
        }

        const $btn = $(this);
        const postData = {
          leadId: self.leadId,
          title: reason,
          category: reason,
          msg: comment || "Scheduled callback for " + reason,
          followupTime: fTime,
        };

        self.submitModalFollowUp(postData, $btn);
      });

      $(document).on("click", "#btnSideSendPaymentLink", function () {
        if (self.leadData && self.leadData.quotation) {
          $("#modalPaymentAmountInput").val(
            self.leadData.quotation.price || "",
          );
        }
        $("#modalSendPaymentLink").addClass("is-active");
      });

      $(document).on(
        "click",
        "#btnOpenPaymentModal, #btnOpenPaymentModalTab, #btnSideRecordPayment",
        function () {
          const fileInput = document.getElementById("modalPayFile");
          if (fileInput) fileInput.value = "";
          $("#modalPayFileText").text(
            "Click to upload payment receipt or screenshot",
          );
          $("#btnClearPayFile").addClass("v3-hidden");
          $("#modalPayFilePreview").empty().addClass("v3-hidden");
          $("#modalUpdatePayment").addClass("is-active");
          self.initSingleDatePickers();
        },
      );

      // Payment Receipt File Change & Clear Handlers
      $(document).on("change", "#modalPayFile", function () {
        const file = this.files && this.files[0];
        const $preview = $("#modalPayFilePreview");
        const $text = $("#modalPayFileText");
        const $clearBtn = $("#btnClearPayFile");

        if (file) {
          const fileSizeKb = Math.round(file.size / 1024);
          const sizeStr =
            fileSizeKb > 1024
              ? (fileSizeKb / 1024).toFixed(1) + " MB"
              : fileSizeKb + " KB";
          $text.html(
            `<strong>${file.name}</strong> <span class="v3-file-size">(${sizeStr})</span>`,
          );
          $clearBtn.removeClass("v3-hidden");

          if (file.type && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function (e) {
              $preview
                .html(
                  `<img src="${e.target.result}" alt="Preview" class="v3-receipt-thumb-img">`,
                )
                .removeClass("v3-hidden");
            };
            reader.readAsDataURL(file);
          } else {
            $preview
              .html(
                `<div class="v3-receipt-doc-chip"><i class="fi fi-rr-document"></i> <span>${file.name}</span></div>`,
              )
              .removeClass("v3-hidden");
          }
        } else {
          $text.text("Click to upload payment receipt or screenshot");
          $clearBtn.addClass("v3-hidden");
          $preview.empty().addClass("v3-hidden");
        }
      });

      $(document).on("click", "#btnClearPayFile", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const fileInput = document.getElementById("modalPayFile");
        if (fileInput) fileInput.value = "";
        $("#modalPayFileText").text(
          "Click to upload payment receipt or screenshot",
        );
        $("#btnClearPayFile").addClass("v3-hidden");
        $("#modalPayFilePreview").empty().addClass("v3-hidden");
      });

      // Open Payment Receipt & Breakdown Modal on current page with Infinite Navigation
      self.currentReceiptIdx = 0;

      self.renderReceiptDetails = function (idx) {
        if (!self.leadPayments || self.leadPayments.length === 0) return;
        const total = self.leadPayments.length;
        const wrappedIdx = ((idx % total) + total) % total;
        self.currentReceiptIdx = wrappedIdx;
        const p = self.leadPayments[wrappedIdx];
        if (!p) return;

        const rawImg =
          p.image_url ||
          (p.image && typeof p.image === "object"
            ? p.image.path
            : typeof p.image === "string" &&
                (p.image.includes("/") || p.image.includes("."))
              ? p.image
              : "");
        const $imgContainer = $("#receiptImgContainer");
        const $downloadBtn = $("#receiptModalDownloadBtn");

        if (rawImg) {
          const isPdf = rawImg.toLowerCase().includes(".pdf");
          if (isPdf) {
            $imgContainer.html(`
              <div class="v3-receipt-pdf-placeholder">
                <i class="fi fi-rr-file-pdf"></i>
                <h5>PDF Document Attached</h5>
                <p>Click below to preview or download the receipt PDF.</p>
                <a href="${rawImg}" target="_blank" class="btn-v3-action btn-green"><i class="fi fi-rr-eye"></i> View PDF Document</a>
              </div>
            `);
          } else {
            $imgContainer.html(`
              <img src="${rawImg}" alt="Payment Receipt" class="v3-receipt-full-preview">
            `);
          }
          $downloadBtn.attr("href", rawImg).show();
        } else {
          $imgContainer.html(`
            <div class="v3-receipt-empty-preview">
              <i class="fi fi-rr-picture"></i>
              <p>No receipt image attached for this transaction.</p>
            </div>
          `);
          $downloadBtn.hide();
        }

        $("#receiptModalAmount").text(
          `₹ ${parseFloat(p.amount || 0).toLocaleString("en-IN")}`,
        );
        $("#receiptModalDate").text(p.date || "-");
        $("#receiptModalMethod").text(p.method || "Standard");
        $("#receiptModalRef").text(p.reference || "None");
        $("#receiptModalRecordedBy").text(p.recorded_by || "System");
        $("#receiptModalNotes").text(p.notes || "No remarks provided.");
        $("#receiptModalSubtitle").text(
          `Recorded on ${p.date || "recent"} by ${p.recorded_by || "Representative"}`,
        );
        $("#receiptNavCounter").text(`${wrappedIdx + 1} of ${total}`);

        if (total <= 1) {
          $("#receiptNavBar").hide();
        } else {
          $("#receiptNavBar").show();
        }
      };

      $(document).on("click", ".btn-view-payment-receipt", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const idx = parseInt($(this).data("payment-idx"), 10) || 0;
        self.renderReceiptDetails(idx);
        $("#modalViewPaymentReceipt").addClass("is-active");
      });

      // Infinite Left / Right Navigation Handlers
      $(document).on("click", "#btnReceiptNavPrev", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.renderReceiptDetails(self.currentReceiptIdx - 1);
      });

      $(document).on("click", "#btnReceiptNavNext", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.renderReceiptDetails(self.currentReceiptIdx + 1);
      });

      // Copy UTR / Reference from Receipt Modal
      $(document).on("click", "#btnCopyReceiptRef", function (e) {
        e.preventDefault();
        const ref = $("#receiptModalRef").text().trim();
        if (!ref || ref === "None" || ref === "-") return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(ref).then(function () {
            self.notify("Reference copied to clipboard!", "success");
          });
        }
      });

      $(document).on("click", "#btnSideCloseLeadModal", function () {
        $("#modalCloseStatus").val("15");
        $("#modalCloseLead").addClass("is-active");
      });

      $(document).on("click", ".v3-modal-close-trigger", function () {
        $(".v3-modal-overlay").removeClass("is-active");
      });

      // Close modal on Escape key or navigate with Left/Right Arrow keys
      $(document).on("keydown", function (e) {
        if ($("#modalViewPaymentReceipt").hasClass("is-active")) {
          if (e.key === "ArrowLeft" || e.keyCode === 37) {
            e.preventDefault();
            self.renderReceiptDetails(self.currentReceiptIdx - 1);
            return;
          } else if (e.key === "ArrowRight" || e.keyCode === 39) {
            e.preventDefault();
            self.renderReceiptDetails(self.currentReceiptIdx + 1);
            return;
          }
        }

        if (e.key === "Escape" || e.keyCode === 27) {
          if ($(".v3-modal-overlay.is-active").length) {
            $(".v3-modal-overlay.is-active").removeClass("is-active");
          }
          if ($(".popUp").is(":visible")) {
            $(".popUp").hide();
          }
        }
      });

      // 10. Submit Payment Record Modal
      $(document).on("click", "#btnSubmitModalPayment", function () {
        const amount = $("#modalPayAmount").val().trim();
        const method = $("#modalPayMethod").val();
        const date = $("#modalPayDate").val();
        const ref = $("#modalPayRef").val().trim();
        const notes = $("#modalPayNotes").val().trim();
        const fileInput = document.getElementById("modalPayFile");

        if (!amount || parseFloat(amount) <= 0) {
          self.notify("Please enter a valid payment amount.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Saving...");

        const formData = new FormData();
        formData.append("leadId", self.leadId);
        formData.append("amount", amount);
        formData.append("payment_method", method);
        formData.append("payment_date", date);
        formData.append("reference_number", ref);
        formData.append("payment_notes", notes);

        if (fileInput && fileInput.files && fileInput.files.length > 0) {
          formData.append("files", fileInput.files[0]);
        }

        $.ajax({
          url: "/api/v3/leads/payment-record/",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).text("Record Payment");
            $(".v3-modal-overlay").removeClass("is-active");
            if (res.status === "success") {
              self.notify("Payment recorded successfully", "success");
              // Reset modal inputs
              $("#modalPayAmount").val("");
              $("#modalPayRef").val("");
              $("#modalPayNotes").val("");
              if (fileInput) fileInput.value = "";
              $("#modalPayFileText").text(
                "Click to upload payment receipt or screenshot",
              );
              $("#btnClearPayFile").addClass("v3-hidden");
              $("#modalPayFilePreview").empty().addClass("v3-hidden");
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to record payment.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).text("Record Payment");
            self.notify("Network connection error.", "error");
          },
        });
      });

      // Quick verify payment from Lead Payments table with SweetAlert
      $(document).on("click", ".btn-lead-approve-payment", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const noteId = $(this).data("note-id");
        const $btn = $(this);
        if (!noteId) return;

        const doApproveLeadPayment = function () {
          $btn
            .prop("disabled", true)
            .html('<i class="fi fi-rr-refresh fa-spin"></i>');

          $.ajax({
            url: "/api/v3/accounts/verify-payment/",
            type: "POST",
            data: { note_id: noteId, action: "approve" },
            dataType: "json",
            success: function (res) {
              if (res.status === "success") {
                if (
                  typeof Swal !== "undefined" &&
                  typeof Swal.fire === "function"
                ) {
                  Swal.fire({
                    icon: "success",
                    type: "success",
                    title: "Payment Verified!",
                    text:
                      res.message ||
                      "Payment verified and lead marked as Confirmed.",
                    showConfirmButton: false,
                    timer: 1800,
                  });
                } else {
                  self.notify(
                    res.message || "Payment verified successfully!",
                    "success",
                  );
                }
                self.loadLeadDetails();
              } else {
                if (
                  typeof Swal !== "undefined" &&
                  typeof Swal.fire === "function"
                ) {
                  Swal.fire({
                    icon: "error",
                    type: "error",
                    title: "Verification Failed",
                    text: res.message || "Could not verify payment.",
                    confirmButtonColor: "#dc2626",
                  });
                } else {
                  self.notify(
                    res.message || "Could not verify payment.",
                    "error",
                  );
                }
                $btn
                  .prop("disabled", false)
                  .html('<i class="fi fi-rr-check"></i> Verify');
              }
            },
            error: function () {
              $btn
                .prop("disabled", false)
                .html('<i class="fi fi-rr-check"></i> Verify');
              self.notify("Error communicating with server.", "error");
            },
          });
        };

        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Verify & Approve Payment?",
            text: "Are you sure you want to verify and approve this payment record? This will mark the lead as Confirmed.",
            icon: "warning",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#059669",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Approve Payment",
            cancelButtonText: "Cancel",
          }).then(function (result) {
            if (result && (result.isConfirmed || result.value)) {
              doApproveLeadPayment();
            }
          });
        } else if (
          confirm(
            "Are you sure you want to verify and approve this payment record? This will mark the lead as Confirmed.",
          )
        ) {
          doApproveLeadPayment();
        }
      });

      // 11. Submit Close Lead Modal
      $(document).on("click", "#btnSubmitModalCloseLead", function () {
        const closeStatus = parseInt($("#modalCloseStatus").val()) || 15;
        const reason = $("#modalCloseReason").val();
        const msg = $("#modalCloseMsg").val().trim();

        if (!reason) {
          self.notify(
            "Please select a reason for closing this lead.",
            "warning",
          );
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Submitting...");

        $.ajax({
          url: "/api/v3/leads/add-note/",
          type: "POST",
          data: {
            leadId: self.leadId,
            title: reason,
            msg: msg,
            finalNotes: true,
            close_status: closeStatus,
          },
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).text("Confirm & Archive");
            $(".v3-modal-overlay").removeClass("is-active");
            if (res.status === "success") {
              self.notify("Lead status updated successfully", "success");
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to close lead.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).text("Confirm & Archive");
            self.notify("Network connection error.", "error");
          },
        });
      });

      // 12. Retry
      $(document).on("click", "#btnRetryLeadView", function () {
        self.loadLeadDetails();
      });
    },

    populateCustomizerFields: function (pkg) {
      const self = this;
      const adultPrice = parseFloat(pkg.tour_package_price_adult || 0);
      const childPrice = parseFloat(pkg.tour_package_price_child || 0);
      const adults =
        self.leadData && self.leadData.travel_details
          ? parseInt(self.leadData.travel_details.adults || 2)
          : 2;
      const children =
        self.leadData && self.leadData.travel_details
          ? parseInt(self.leadData.travel_details.children || 0)
          : 0;
      const infants =
        self.leadData && self.leadData.travel_details
          ? parseInt(self.leadData.travel_details.infants || 0)
          : 0;
      const totalPrice =
        adultPrice * (adults > 0 ? adults : 2) + childPrice * children;

      // Parse structured day-wise itinerary
      let daysList = [];
      try {
        if (pkg.tour_package_itinerary) {
          const rawItin =
            typeof pkg.tour_package_itinerary === "string"
              ? JSON.parse(pkg.tour_package_itinerary)
              : pkg.tour_package_itinerary;
          if (Array.isArray(rawItin)) {
            daysList = rawItin;
          }
        }
      } catch (e) {
        if (
          typeof pkg.tour_package_itinerary === "string" &&
          pkg.tour_package_itinerary.trim()
        ) {
          const rawBlocks = pkg.tour_package_itinerary.split("\n\n");
          rawBlocks.forEach((b, i) => {
            if (b.trim()) {
              daysList.push({ day: `Day ${i + 1}`, overview: b.trim() });
            }
          });
        }
      }

      const stripHtml = (s) => (s ? s.replace(/<[^>]*>?/gm, "").trim() : "");

      const formatOverviewText = (s) => {
        if (!s) return "";
        let str = String(s);
        // Normalize paragraph & line break tags to readable newlines
        str = str
          .replace(/<br\s*[\/]?>/gi, "\n")
          .replace(/<\/p>/gi, "\n\n")
          .replace(/<\/div>/gi, "\n")
          .replace(/<li[^>]*>/gi, "• ")
          .replace(/<\/li>/gi, "\n")
          .replace(/<[^>]*>?/gm, "");
        // Decode HTML entities
        if (typeof DOMParser !== "undefined") {
          try {
            const doc = new DOMParser().parseFromString(str, "text/html");
            str = doc.body.textContent || "";
          } catch (e) {}
        } else {
          try {
            const txt = document.createElement("textarea");
            txt.innerHTML = str;
            str = txt.value;
          } catch (e) {}
        }
        return str.replace(/\r\n/g, "\n").replace(/\n{3,}/g, "\n\n").trim();
      };

      const formatBulletList = (input, defaultFallback = "") => {
        if (!input) return defaultFallback;
        if (Array.isArray(input)) {
          return input
            .map((s) => formatBulletList(s))
            .filter(Boolean)
            .join("\n");
        }
        let str = String(input);
        // Replace HTML break and list tags with newlines
        str = str.replace(/<\/?(li|p|div|br|tr|td)[^>]*>/gi, "\n");
        // Remove remaining HTML tags
        str = str.replace(/<[^>]*>?/gm, "");
        // Remove intro prefix phrases
        str = str.replace(
          /^(our\s+)?(package\s+)?(inclusions?\s*(cover|include|includes|are|:)?|exclusions?\s*(cover|covers|include|includes|are|:)?)\s*/i,
          "",
        );
        str = str.replace(/^(our\s+)?package\s+includes\s+/i, "");
        str = str.replace(/^(inclusions?|exclusions?)\s*:\s*/i, "");
        // Split on comma / and if not already multiline
        if (!str.includes("\n") || str.split("\n").length <= 1) {
          str = str.replace(/,\s*and\s+/gi, "\n");
          str = str.replace(/;\s*/g, "\n");
          str = str.replace(/,\s*/g, "\n");
        }

        const lines = str
          .split("\n")
          .map((line) => {
            let l = line.trim();
            // Remove leading bullets/numbers/checks/dashes
            l = l.replace(/^[•\-*✓✗✔✕\d.):\s]+/u, "").trim();
            // Remove leading 'and '
            l = l.replace(/^and\s+/i, "").trim();
            // Remove trailing period
            l = l.replace(/\.$/, "").trim();
            // Capitalize first letter
            if (l.length > 0) {
              l = l.charAt(0).toUpperCase() + l.slice(1);
            }
            return l;
          })
          .filter((line) => line.length > 1);

        const unique = [...new Set(lines)];
        return unique.length > 0 ? unique.join("\n") : defaultFallback;
      };

      // Validate travel date — must be a real future date, else default to +7 days
      let resolvedTravelDate = "";
      const rawTravelDate =
        self.leadData &&
        self.leadData.travel_details &&
        self.leadData.travel_details.date_of_travel
          ? (self.leadData.travel_details.date_of_travel || "").trim()
          : "";
      if (
        rawTravelDate &&
        rawTravelDate.toLowerCase() !== "flexible" &&
        typeof moment !== "undefined" &&
        moment(rawTravelDate, "YYYY-MM-DD", true).isValid() &&
        moment(rawTravelDate, "YYYY-MM-DD").isSameOrAfter(
          moment().startOf("day"),
        )
      ) {
        resolvedTravelDate = rawTravelDate;
      } else if (typeof moment !== "undefined") {
        resolvedTravelDate = moment().add(7, "days").format("YYYY-MM-DD");
      }
      $("#itineraryCustomTravelDate").val(resolvedTravelDate);
      self.initSingleDatePickers();

      const defaultPickup =
        (pkg.tour_package_pickup || "").trim() ||
        (self.leadData &&
        self.leadData.travel_details &&
        (self.leadData.travel_details.departure_city ||
          self.leadData.travel_details.pickup_location)
          ? self.leadData.travel_details.departure_city ||
            self.leadData.travel_details.pickup_location
          : "");

      const defaultDrop =
        (pkg.tour_package_drop || "").trim() ||
        (self.leadData &&
        self.leadData.travel_details &&
        (self.leadData.travel_details.drop_location ||
          self.leadData.travel_details.destination)
          ? self.leadData.travel_details.drop_location ||
            self.leadData.travel_details.destination
          : "");

      self.loadItineraryMeta(function () {
        self.setupPickupDropSelects(defaultPickup, defaultDrop);
      });

      const defaultTheme = (
        pkg.tour_package_theme ||
        pkg.tour_package_travel_style ||
        $("#itineraryTravelStyle").val() ||
        "standard"
      )
        .toLowerCase()
        .trim();
      if (
        $("#itineraryCustomTheme option[value='" + defaultTheme + "']").length >
        0
      ) {
        $("#itineraryCustomTheme").val(defaultTheme);
      } else {
        $("#itineraryCustomTheme").val("standard");
      }

      if (typeof $.fn.select2 !== "undefined") {
        $("#itineraryCustomTheme").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          minimumResultsForSearch: Infinity,
        });
      }

      $("#itineraryCustomTitle").val(pkg.tour_package_title || "");
      $("#itineraryCustomAdultCount").val(adults > 0 ? adults : 2);
      $("#itineraryCustomChildCount").val(children);
      $("#itineraryCustomInfantCount").val(infants);

      $("#itineraryCustomAdultPrice").val(adultPrice);
      $("#itineraryCustomChildPrice").val(childPrice);
      $("#itineraryCustomInfantPrice").val(0);
      $("#itineraryCustomTotalPrice").val(
        totalPrice > 0 ? totalPrice : adultPrice * (adults > 0 ? adults : 2),
      );
      const defaultOverview = formatOverviewText(
        pkg.tour_package_overview || pkg.overview || "",
      );
      $("#itineraryCustomOverview").val(defaultOverview);
      $("#itineraryCustomInclusions").val(
        formatBulletList(
          pkg.tour_package_inclusions,
          "Hotel accommodation with breakfast\nAirport & sightseeing transfers\nAll toll taxes and parking charges",
        ),
      );
      $("#itineraryCustomExclusions").val(
        formatBulletList(
          pkg.tour_package_exclusions,
          "Airfare / Train tickets\nPersonal expenses & tips\nEntry fees and camera tickets",
        ),
      );

      const travelerParts = [];
      if (adults) travelerParts.push(`${adults} Adults`);
      if (children) travelerParts.push(`${children} Children`);
      if (infants) travelerParts.push(`${infants} Infants`);
      $("#itinTravelerCountBadge").text(
        travelerParts.join(", ") || "Auto-calculates per traveler",
      );

      const packageCity = (pkg.tour_package_city || "").trim();
      const leadDest = (
        self.leadData &&
        self.leadData.travel_details &&
        self.leadData.travel_details.destination
          ? self.leadData.travel_details.destination
          : ""
      ).trim();
      const defaultCity = packageCity || leadDest;

      // Smart city extractor from day title or overview text
      const detectCityFromText = (txt) => {
        if (!txt) return "";
        const lowerTxt = txt.toLowerCase();

        // 1. Prioritized destination candidates (default city, package city, lead dest, top tour hubs)
        const candidates = [];
        if (defaultCity) candidates.push(defaultCity);
        if (packageCity && packageCity !== defaultCity) candidates.push(packageCity);
        if (leadDest && leadDest !== defaultCity) candidates.push(leadDest);

        const popularHubs = [
          "Manali", "Shimla", "Kasol", "Jibhi", "Kullu", "Dharamshala", "Dalhousie", "Amritsar",
          "Chandigarh", "Delhi", "Agra", "Jaipur", "Udaipur", "Goa", "Srinagar", "Gulmarg",
          "Pahalgam", "Leh", "Ladakh", "Munnar", "Alleppey", "Thekkady", "Wayanad", "Ooty",
          "Kodaikanal", "Rishikesh", "Haridwar", "Nainital", "Mussoorie", "Jim Corbett",
          "Varanasi", "Darjeeling", "Gangtok", "Port Blair", "Havelock"
        ];
        popularHubs.forEach((h) => {
          if (!candidates.includes(h)) candidates.push(h);
        });

        if (Array.isArray(self.availableStayCities)) {
          for (let i = 0; i < Math.min(self.availableStayCities.length, 100); i++) {
            const c = self.availableStayCities[i];
            if (c && !candidates.includes(c)) candidates.push(c);
          }
        }

        // Check for direct "to [City]" or "in [City]" pattern
        for (const c of candidates) {
          const cLower = c.toLowerCase();
          const prefixes = ["to ", "in ", "at ", "stay in ", "reach ", "arrive in "];
          for (const p of prefixes) {
            if (lowerTxt.indexOf(p + cLower) !== -1) {
              return c;
            }
          }
        }

        // Check if candidate city name is mentioned directly
        for (const c of candidates) {
          const cLower = c.toLowerCase();
          if (cLower.length > 2 && lowerTxt.indexOf(cLower) !== -1) {
            return c;
          }
        }
        return "";
      };

      // Save vehicle preferences on self.selectedPackage
      self.selectedPackage = self.selectedPackage || {};
      self.selectedPackage.outstation_vehicle =
        pkg.tour_package_outstation_vehicle || pkg.outstation_vehicle || "";
      self.selectedPackage.localsightseen_vehicle =
        pkg.tour_package_localsightseen_vehicle ||
        pkg.localsightseen_vehicle ||
        "";

      const initialOutstation = (
        self.selectedPackage.outstation_vehicle || ""
      ).trim();
      const initialLocal = (
        self.selectedPackage.localsightseen_vehicle || ""
      ).trim();
      const initialTotalDays = daysList.length;

      daysList = daysList.map((d, i) => {
        if (!d.city && !d.stay_city) {
          const detected =
            detectCityFromText(d.day) || detectCityFromText(d.overview);
          d.city = detected || defaultCity || "";
          d.stay_city = d.city;
        }

        // Vehicles: outstation vehicle for day 1 and last day, local sightseeing for other days
        const isFirstOrLast = i === 0 || i === initialTotalDays - 1;
        const defaultVeh = isFirstOrLast
          ? initialOutstation || initialLocal || ""
          : initialLocal || initialOutstation || "";

        if (!d.vehicle || d.vehicle === "Private Sedan") {
          if (defaultVeh) {
            d.vehicle = defaultVeh;
          }
        }
        return d;
      });

      self.selectedPackageTemplate = pkg.tour_package_template || "mit";

      self.showItinDaysLoader();

      if (pkg.tour_package_id && parseInt(pkg.tour_package_id) > 0) {
        const pkgId = pkg.tour_package_id;

        $.when(
          $.ajax({
            url: `/api/v3/packages/get/?id=${encodeURIComponent(pkgId)}`,
            type: "GET",
            dataType: "json",
          }),
          $.ajax({
            url: `/api/v3/packages/hotels/?package_id=${encodeURIComponent(pkgId)}`,
            type: "GET",
            dataType: "json",
          }),
        )
          .done(function (getResArr, hotResArr) {
            const getRes =
              getResArr && getResArr[0] ? getResArr[0] : getResArr || {};
            const hotRes =
              hotResArr && hotResArr[0] ? hotResArr[0] : hotResArr || {};

            // Merge hotels into self.propertiesList and cache rooms
            if (hotRes && hotRes.hotels && hotRes.hotels.length > 0) {
              self.propertiesList = self.propertiesList || [];
              self.hotelRoomsCache = self.hotelRoomsCache || {};
              hotRes.hotels.forEach((h) => {
                if (!self.propertiesList.some((p) => p.id === h.id)) {
                  self.propertiesList.push(h);
                }
                if (h.rooms && Array.isArray(h.rooms) && h.rooms.length > 0) {
                  self.hotelRoomsCache[h.id] = h.rooms;
                }
              });
            }

            // Build assigned hotels map from getRes package_hotels and hotRes
            const assignedMap = {};
            const rawPkgHotels =
              (getRes && getRes.package && getRes.package.package_hotels) ||
              (getRes && getRes.package_hotels) ||
              null;
            if (rawPkgHotels) {
              const pHotelsArr = Array.isArray(rawPkgHotels)
                ? rawPkgHotels
                : Object.values(rawPkgHotels);
              pHotelsArr.forEach((ph) => {
                const night = parseInt(ph.night || ph.assigned_night || 0);
                if (night > 0) {
                  assignedMap[night] = {
                    id: ph.hotel_id || ph.id,
                    title: ph.hotel_title || ph.title,
                    city: ph.city || "",
                    assigned_room_type:
                      ph.room_type || ph.assigned_room_type || "",
                    type: ph.property_type || ph.type || "",
                  };
                }
              });
            }
            if (hotRes && hotRes.hotels && Array.isArray(hotRes.hotels)) {
              hotRes.hotels.forEach((h) => {
                const n = parseInt(h.assigned_night || 0);
                if (n > 0) {
                  assignedMap[n] = {
                    id: parseInt(h.id) || 0,
                    title: h.title || "",
                    city: h.city || "",
                    assigned_room_type: h.assigned_room_type || "",
                    assigned_room_id: parseInt(h.assigned_room_id) || 0,
                    type: h.type || "",
                  };
                }
              });
            }

            // If get.php returned an enriched itinerary, use it as baseline
            if (getRes && getRes.status === "success" && getRes.package) {
              if (
                Array.isArray(getRes.package.itinerary) &&
                getRes.package.itinerary.length > 0
              ) {
                daysList = getRes.package.itinerary;
              }
              if (
                getRes.package.outstation_vehicle ||
                getRes.package.tour_package_outstation_vehicle
              ) {
                self.selectedPackage.outstation_vehicle =
                  getRes.package.outstation_vehicle ||
                  getRes.package.tour_package_outstation_vehicle;
              }
              if (
                getRes.package.localsightseen_vehicle ||
                getRes.package.tour_package_localsightseen_vehicle
              ) {
                self.selectedPackage.localsightseen_vehicle =
                  getRes.package.localsightseen_vehicle ||
                  getRes.package.tour_package_localsightseen_vehicle;
              }
              const enrichedOverview =
                getRes.package.tour_package_overview ||
                getRes.package.overview ||
                "";
              if (enrichedOverview) {
                self.selectedPackage.tour_package_overview = enrichedOverview;
                self.selectedPackage.overview = enrichedOverview;
                const $currentOverview = $("#itineraryCustomOverview");
                const currentVal = ($currentOverview.val() || "").trim();
                const initialVal = formatOverviewText(
                  pkg.tour_package_overview || pkg.overview || "",
                );
                // If textarea is currently empty or still has initial un-enriched overview, update it
                if (!currentVal || currentVal === initialVal) {
                  $currentOverview.val(formatOverviewText(enrichedOverview));
                }
              }
            }

            const pkgOutstation = (
              self.selectedPackage.outstation_vehicle ||
              pkg.tour_package_outstation_vehicle ||
              pkg.outstation_vehicle ||
              ""
            ).trim();
            const pkgLocal = (
              self.selectedPackage.localsightseen_vehicle ||
              pkg.tour_package_localsightseen_vehicle ||
              pkg.localsightseen_vehicle ||
              ""
            ).trim();
            const totalDays = daysList.length;

            // Map daysList to ensure hotel_id, hotel, room_type, city, and vehicles are properly populated
            daysList = daysList.map((d, i) => {
              const nightNum = i + 1;
              const assignedH = assignedMap[nightNum];

              // 1. Stay City & Assigned Hotel (Package day-wise hotel assignment has top priority for default display)
              if (assignedH && assignedH.id) {
                d.hotel_id = parseInt(assignedH.id) || 0;
                d.hotel_title = assignedH.title || "";
                d.hotel = assignedH.title || "";
                if (assignedH.city) {
                  d.stay = assignedH.city;
                  d.stay_city = assignedH.city;
                  d.city = assignedH.city;
                }
                if (assignedH.assigned_room_type) {
                  d.room_type = assignedH.assigned_room_type;
                }
                if (assignedH.assigned_room_id) {
                  d.room_type_id = parseInt(assignedH.assigned_room_id) || 0;
                }
                if (assignedH.type && assignedH.type !== "home_stay") {
                  d.hotel_stars = assignedH.type;
                } else if (!d.hotel_stars) {
                  const defaultPkgHotel =
                    (
                      pkg.tour_package_hotel ||
                      pkg.hotel ||
                      "3_star_hotel"
                    ).trim() || "3_star_hotel";
                  d.hotel_stars = defaultPkgHotel;
                }
                d.show_hotel = 1;
                d.is_hotel_hidden = 0;
                d.hide_hotel = 0;
              } else {
                // Fallback for days without an assigned hotel in package
                if (!d.stay && !d.city && !d.stay_city) {
                  const detected =
                    detectCityFromText(d.day) || detectCityFromText(d.overview);
                  d.stay = detected || defaultCity || "";
                  d.city = d.stay;
                } else {
                  d.stay = d.stay || d.city || d.stay_city || defaultCity || "";
                  d.city = d.stay;
                }

                const sLower = (d.stay || "").toLowerCase().trim();
                const isNoStayFallback =
                  sLower.includes("overnight") ||
                  sLower.includes("transit") ||
                  sLower.includes("tour end") ||
                  sLower.includes("trip end") ||
                  sLower.includes("no stay") ||
                  sLower.includes("no hotel") ||
                  sLower.includes("departure");

                if (isNoStayFallback) {
                  d.show_hotel = 0;
                  d.is_hotel_hidden = 1;
                  d.hide_hotel = 1;
                }

                if (d.hotel_title || d.hotel) {
                  if (!d.hotel) d.hotel = d.hotel_title;
                  if (!d.hotel_title) d.hotel_title = d.hotel;
                  if (!d.hotel_id && self.propertiesList) {
                    const match = self.propertiesList.find(
                      (p) =>
                        p.title &&
                        p.title.toLowerCase().trim() ===
                          d.hotel.toLowerCase().trim(),
                    );
                    if (match) d.hotel_id = match.id;
                  }
                }
              }

              if (d.hotel_id && !d.hotel) {
                d.hotel = d.hotel_title || d.hotel_id;
              }

              // 2. Vehicles: outstation vehicle for Day 1 and last day, local sightseeing for other days
              const isFirstOrLast = i === 0 || i === totalDays - 1;
              const defaultVeh = isFirstOrLast
                ? pkgOutstation || pkgLocal || ""
                : pkgLocal || pkgOutstation || "";

              if (!d.vehicle || d.vehicle === "Private Sedan") {
                if (defaultVeh) {
                  d.vehicle = defaultVeh;
                }
              }

              return d;
            });

            self.renderItineraryDays(daysList);
          })
          .fail(function () {
            self.renderItineraryDays(daysList);
          });
      } else {
        self.renderItineraryDays(daysList);
      }

      $("#itinerarySearchSection").addClass("v3-hidden");
      $("#btnChangeItinerary").removeClass("v3-hidden");
      $("#itineraryCustomSection").removeClass("v3-hidden");
      $("#btnPreviewItinerary").removeClass("v3-hidden");
    },

    confirmConvertToSale: function () {
      const self = this;
      if (self.isConvertingToSale || self.isUpdatingStatus) return;

      const $heroBtn = $("#btnHeroConvertToSale");
      const $sideBtn = $("#btnSideConvertToSale");

      const setButtonsLoading = function () {
        self.isConvertingToSale = true;
        if ($heroBtn.length) {
          if (!$heroBtn.data("orig-html")) {
            $heroBtn.data("orig-html", $heroBtn.html());
          }
          $heroBtn
            .prop("disabled", true)
            .addClass("is-loading disabled")
            .html(
              '<i class="fi fi-rr-spinner fi-rr-spin"></i> <span>Converting...</span>',
            );
        }
        if ($sideBtn.length) {
          if (!$sideBtn.data("orig-title")) {
            $sideBtn.data(
              "orig-title",
              $sideBtn.find(".fast-tile-title").text(),
            );
            $sideBtn.data("orig-sub", $sideBtn.find(".fast-tile-sub").text());
          }
          $sideBtn.prop("disabled", true).addClass("is-loading disabled");
          $sideBtn
            .find(".fast-tile-icon")
            .html('<i class="fi fi-rr-spinner fi-rr-spin"></i>');
          $sideBtn.find(".fast-tile-title").text("Converting...");
          $sideBtn.find(".fast-tile-sub").text("Please wait...");
        }
      };

      const resetButtons = function () {
        self.isConvertingToSale = false;
        if ($heroBtn.length && $heroBtn.data("orig-html")) {
          $heroBtn
            .prop("disabled", false)
            .removeClass("is-loading disabled")
            .html($heroBtn.data("orig-html"));
        }
        if ($sideBtn.length && $sideBtn.data("orig-title")) {
          $sideBtn.prop("disabled", false).removeClass("is-loading disabled");
          $sideBtn
            .find(".fast-tile-icon")
            .html('<i class="fi fi-rr-check-circle"></i>');
          $sideBtn.find(".fast-tile-title").text($sideBtn.data("orig-title"));
          $sideBtn.find(".fast-tile-sub").text($sideBtn.data("orig-sub"));
        }
      };

      setButtonsLoading();

      if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
        Swal.fire({
          title: "Convert to Confirmed Sale?",
          text: "This will transition the lead to confirmed status in your revenue pipeline and notify administration.",
          type: "warning",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#10b981",
          cancelButtonColor: "#64748b",
          confirmButtonText: "Yes, Confirm Sale",
          cancelButtonText: "Cancel",
          allowOutsideClick: false,
        }).then((res) => {
          if (res.isConfirmed || res.value) {
            self.updateStatus(1, function () {
              resetButtons();
            });
          } else {
            resetButtons();
          }
        });
      } else if (confirm("Convert this lead to confirmed sale?")) {
        self.updateStatus(1, function () {
          resetButtons();
        });
      } else {
        resetButtons();
      }
    },

    performAgentAssign: function (agentId) {
      const self = this;
      $.ajax({
        url: "/api/v3/leads/assign/",
        type: "POST",
        data: {
          leadId: self.leadId,
          agentId: agentId,
        },
        dataType: "json",
        success: function (res) {
          if (res.status === 200 || res.status === "success") {
            self.notify("Agent reassigned successfully", "success");

            if ("Notification" in window && res.notification) {
              if (Notification.permission === "granted") {
                const notifTitle = res.notification.title || "Lead Assigned";
                const notifOptions = {
                  body: res.notification.body || "Lead assignment updated.",
                  icon:
                    res.notification.icon ||
                    "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png",
                  badge:
                    res.notification.icon ||
                    "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png",
                  data: {
                    url: res.notification.url || window.location.href,
                    click_action: res.notification.url || window.location.href,
                  },
                };

                if (
                  "serviceWorker" in navigator &&
                  navigator.serviceWorker.controller
                ) {
                  navigator.serviceWorker.ready
                    .then(function (reg) {
                      reg.showNotification(notifTitle, notifOptions);
                    })
                    .catch(function () {
                      try {
                        const notif = new Notification(
                          notifTitle,
                          notifOptions,
                        );
                        if (res.notification.url) {
                          notif.onclick = function () {
                            window.open(res.notification.url, "_blank");
                          };
                        }
                      } catch (err) {}
                    });
                } else {
                  try {
                    const notif = new Notification(notifTitle, notifOptions);
                    if (res.notification.url) {
                      notif.onclick = function () {
                        window.open(res.notification.url, "_blank");
                      };
                    }
                  } catch (err) {}
                }
              }
            }

            self.loadLeadDetails();
          } else {
            self.notify(
              res.msg || res.message || "Failed to reassign agent.",
              "error",
            );
            self.loadLeadDetails();
          }
        },
        error: function () {
          self.notify("Network error during assignment.", "error");
          self.loadLeadDetails();
        },
      });
    },

    updateStatus: function (status, callback) {
      const self = this;
      if (self.isUpdatingStatus) return;
      self.isUpdatingStatus = true;

      $.ajax({
        url: "/api/v3/leads/update-status/",
        type: "POST",
        data: {
          leadId: self.leadId,
          lead_id: self.leadId,
          id: self.leadId,
          status: status,
        },
        dataType: "json",
        success: function (res) {
          self.isUpdatingStatus = false;
          if (typeof callback === "function") callback(null, res);

          if (
            res.status === 200 ||
            res.status === "success" ||
            res.status_code === 200
          ) {
            const successMsg =
              res.msg || res.message || "Status updated successfully";
            self.notify(successMsg, "success");

            if (
              typeof Swal !== "undefined" &&
              typeof Swal.fire === "function" &&
              status === 1
            ) {
              Swal.fire({
                title: "Lead Confirmed!",
                text: "Lead has been marked as confirmed sale.",
                type: "success",
                icon: "success",
                timer: 2200,
                showConfirmButton: false,
              });
            }

            self.loadLeadDetails();
          } else {
            self.notify(
              res.msg || res.message || "Failed to update status.",
              "error",
            );
          }
        },
        error: function () {
          self.isUpdatingStatus = false;
          if (typeof callback === "function")
            callback(new Error("Network error"));
          self.notify("Network error during status update.", "error");
        },
      });
    },

    findBestCityMatch: function (originalText, results) {
      if (!results || !results.length) return null;
      const lower = (originalText || "").toLowerCase().trim();

      // Priority 1: Match city or id directly in originalText
      for (let i = 0; i < results.length; i++) {
        const item = results[i];
        const cityName = (item.city || item.id || "").toLowerCase().trim();
        if (
          cityName &&
          (lower.includes(cityName) || cityName.includes(lower))
        ) {
          return item;
        }
      }

      // Priority 2: Match state directly in originalText
      for (let i = 0; i < results.length; i++) {
        const item = results[i];
        const stateName = (item.state || "").toLowerCase().trim();
        if (
          stateName &&
          (lower.includes(stateName) || stateName.includes(lower))
        ) {
          return item;
        }
      }

      // Priority 3: First result from search
      return results[0] || null;
    },

    detectDestinationFromSubject: function (subjectText, callback) {
      const self = this;
      if (!subjectText || typeof subjectText !== "string") {
        if (callback) callback(null);
        return;
      }

      // Clean common tourism noise words
      const cleanText = subjectText
        .replace(
          /\b(\d+N\/?\d*D|\d+\s*(nights?|days?)|tour|package|holiday|trip|honeymoon|special|custom|itinerary|vacation|group|couple|family|deluxe|standard|premium|budget|super|luxury|stay|resort|hotel|enquiry|booking)\b/gi,
          " ",
        )
        .replace(/[^a-zA-Z\s]/g, " ")
        .replace(/\s+/g, " ")
        .trim();

      const candidateWords = cleanText
        ? cleanText.split(/\s+/).filter(function (w) {
            return w.length >= 3;
          })
        : [];
      const searchTerm =
        cleanText ||
        (candidateWords.length ? candidateWords[0] : subjectText.trim());

      if (!searchTerm) {
        if (callback) callback(null);
        return;
      }

      $.ajax({
        url: "/api/v3/cities/",
        type: "GET",
        data: { q: searchTerm },
        dataType: "json",
        success: function (res) {
          const results = (res && res.results) || [];
          if (!results.length && candidateWords.length > 1) {
            // Fallback: search with first individual keyword
            $.ajax({
              url: "/api/v3/cities/",
              type: "GET",
              data: { q: candidateWords[0] },
              dataType: "json",
              success: function (res2) {
                const results2 = (res2 && res2.results) || [];
                const match = self.findBestCityMatch(subjectText, results2);
                if (callback) callback(match);
              },
              error: function () {
                if (callback) callback(null);
              },
            });
            return;
          }

          const match = self.findBestCityMatch(subjectText, results);
          if (callback) callback(match);
        },
        error: function () {
          if (callback) callback(null);
        },
      });
    },

    initCitySelect2: function ($el, $dropdownParent, placeholder) {
      if (!$el || !$el.length || typeof $.fn.select2 === "undefined") return;

      if ($el.hasClass("select2-hidden-accessible") || $el.data("select2")) {
        try {
          $el.select2("destroy");
        } catch (e) {}
      }

      $el.select2({
        placeholder: placeholder || "-- Search Destination City / Country --",
        allowClear: true,
        width: "100%",
        tags: true,
        dropdownParent:
          $dropdownParent && $dropdownParent.length
            ? $dropdownParent
            : undefined,
        minimumInputLength: 0,
        createTag: function (params) {
          const term = $.trim(params.term);
          if (!term) return null;
          return {
            id: term,
            text: `${term} (Add New)`,
            city: term,
            isNewTag: true,
          };
        },
        insertTag: function (data, tag) {
          // Place search keyword with (Add New) at the LAST of dropdown options
          data.push(tag);
        },
        templateResult: function (opt) {
          if (!opt.id) return opt.text;
          if (opt.isNewTag || (opt.text && opt.text.endsWith("(Add New)"))) {
            return $(
              `<div class="v3-select2-add-new-opt"><i class="fi fi-rr-plus"></i> ${escapeHtml(opt.text)}</div>`,
            );
          }
          return opt.text;
        },
        templateSelection: function (item) {
          if (!item.id) return item.text;
          const clean = (item.city || item.id || item.text || "")
            .replace(/\s*\(Add New\)$/i, "")
            .trim();
          return clean || item.text;
        },
        ajax: {
          url: "/api/v3/cities/",
          dataType: "json",
          delay: 150,
          data: function (params) {
            return {
              q: params.term || "",
            };
          },
          processResults: function (data) {
            return {
              results: (data && data.results) || [],
            };
          },
          cache: true,
        },
      });
    },

    initPackageSelect2: function ($el, $dropdownParent, placeholder) {
      if (!$el || !$el.length || typeof $.fn.select2 === "undefined") return;

      $el.select2({
        placeholder:
          placeholder || "Choose a tour package or type custom destination...",
        allowClear: true,
        width: "100%",
        tags: true,
        dropdownParent:
          $dropdownParent && $dropdownParent.length
            ? $dropdownParent
            : undefined,
        ajax: {
          url: "/api/v3/packages/options/",
          dataType: "json",
          delay: 150,
          data: function (params) {
            return {
              q: params.term || "",
            };
          },
          processResults: function (data) {
            return {
              results: (data && data.results) || [],
            };
          },
          cache: true,
        },
        templateResult: function (data) {
          if (!data.id) return data.text;
          if (data.children) return data.text;

          const isCustom = !data.type && !data.pkg_id;
          if (isCustom) {
            return $(`
              <div class="v3-pkg-option-item custom">
                <div class="v3-pkg-option-icon custom"><i class="fi fi-rr-search-location"></i></div>
                <div class="v3-pkg-option-details">
                  <div class="v3-pkg-option-title">${data.text}</div>
                  <span class="v3-pkg-badge custom">Custom Destination</span>
                </div>
              </div>
            `);
          }

          const duration = data.duration;
          const days = data.days || parseInt(duration) + 1;
          const city = data.city || "";
          const type = data.type || "domestic";
          const theme = data.theme || "";
          const isIntl = type.toLowerCase() === "international";

          return $(`
            <div class="v3-pkg-option-item">
              <div class="v3-pkg-option-icon ${isIntl ? "intl" : "dom"}">
                <i class="fi ${isIntl ? "fi-rr-plane" : "fi-rr-route"}"></i>
              </div>
              <div class="v3-pkg-option-details">
                <div class="v3-pkg-option-title">${data.text}</div>
                <div class="v3-pkg-option-badges">
                  ${duration ? `<span class="v3-pkg-badge duration"><i class="fi fi-rr-moon"></i> ${duration}N / ${days}D</span>` : ""}
                  ${city ? `<span class="v3-pkg-badge destination"><i class="fi fi-rr-marker"></i> ${city}</span>` : ""}
                  ${theme ? `<span class="v3-pkg-badge theme"><i class="fi fi-rr-star"></i> ${theme}</span>` : ""}
                  <span class="v3-pkg-badge type ${isIntl ? "intl" : "dom"}">${isIntl ? "International" : "Domestic"}</span>
                </div>
              </div>
            </div>
          `);
        },
        templateSelection: function (data) {
          return data.text || data.id;
        },
      });
    },

    initSingleDatePickers: function () {
      if (typeof $.fn.daterangepicker === "undefined") return;

      $(".v3-single-datepicker-input").each(function () {
        const $el = $(this);
        if ($el.data("daterangepicker")) {
          $el.data("daterangepicker").remove();
        }

        const initialVal = $el.val();
        let startMoment = typeof moment !== "undefined" ? moment() : undefined;
        if (
          initialVal &&
          typeof moment !== "undefined" &&
          moment(initialVal, "YYYY-MM-DD", true).isValid()
        ) {
          startMoment = moment(initialVal, "YYYY-MM-DD");
        }

        const curYear = typeof moment !== "undefined" ? moment().year() : 2026;
        const isFutureOnly =
          $el.attr("id") === "editLeadTravelDate" ||
          $el.data("future-only") === true ||
          $el.attr("data-future-only") === "true";

        let minDateOpt = false;
        let minYearOpt = curYear - 10;
        if (isFutureOnly) {
          minDateOpt =
            typeof moment !== "undefined" ? moment().startOf("day") : false;
          minYearOpt = curYear;
          if (
            startMoment &&
            typeof moment !== "undefined" &&
            startMoment.isBefore(moment().startOf("day"))
          ) {
            startMoment = moment().startOf("day");
          }
        }

        $el.daterangepicker(
          {
            singleDatePicker: true,
            showDropdowns: true,
            minDate: minDateOpt,
            minYear: minYearOpt,
            maxYear: curYear + 15,
            autoApply: true,
            autoUpdateInput: false,
            startDate: startMoment,
            opens: "left",
            drops: "down",
            locale: {
              format: "YYYY-MM-DD",
            },
          },
          function (chosen) {
            $el.val(chosen.format("YYYY-MM-DD")).trigger("change");
          },
        );

        $el.on("apply.daterangepicker", function (ev, picker) {
          $(this).val(picker.startDate.format("YYYY-MM-DD")).trigger("change");
        });

        $el.on("show.daterangepicker", function (ev, picker) {
          if (picker && picker.container) {
            picker.container.addClass("single");
            picker.container.find(".drp-calendar.right").css("display", "none");
            picker.container.find(".ranges").css("display", "none");
            picker.container.find(".drp-buttons").css("display", "none");
          }
        });

        // Ensure container is strictly hidden upon instantiation
        const pickerObj = $el.data("daterangepicker");
        if (pickerObj && pickerObj.container) {
          pickerObj.container.hide().css("display", "none");
        }
      });
    },

    getDefaultFollowupDateTime: function () {
      if (typeof moment !== "undefined") {
        const now = moment();
        const currentHour = now.hour();
        const dayOfWeek = now.day(); // 0 = Sunday, 6 = Saturday

        // Calculate next working day
        let nextDay = now.clone().add(1, "days");
        if (dayOfWeek === 6) {
          // Saturday -> Monday
          nextDay = now.clone().add(2, "days");
        } else if (dayOfWeek === 0) {
          // Sunday -> Monday
          nextDay = now.clone().add(1, "days");
        }

        // Determine default hour within morning 9 AM to evening 9 PM (09:00 - 21:00)
        let defaultHour = 10;
        let defaultMinute = 30;

        if (currentHour >= 9 && currentHour < 18) {
          // Active working hours: same time next working day (rounded to neat 15/30 min slot)
          defaultHour = currentHour;
          defaultMinute = now.minute() >= 30 ? 30 : 0;
        } else if (currentHour >= 18 && currentHour <= 21) {
          // Evening hours (6 PM - 9 PM) -> Next working day 11:00 AM
          defaultHour = 11;
          defaultMinute = 0;
        } else {
          // Night or early morning (before 9 AM or after 9 PM) -> 10:30 AM
          defaultHour = 10;
          defaultMinute = 30;
        }

        nextDay.hour(defaultHour).minute(defaultMinute).second(0);
        return nextDay;
      }

      // Plain Date fallback
      const d = new Date();
      d.setDate(d.getDate() + 1);
      d.setHours(10, 30, 0, 0);
      return {
        format: function (fmt) {
          const y = d.getFullYear();
          const m = String(d.getMonth() + 1).padStart(2, "0");
          const day = String(d.getDate()).padStart(2, "0");
          return `${y}-${m}-${day} 10:30 AM`;
        },
      };
    },

    initDateTimePicker: function () {
      const self = this;
      const defaultMoment = self.getDefaultFollowupDateTime();
      const defaultDateStr =
        typeof moment !== "undefined" && defaultMoment && defaultMoment.format
          ? defaultMoment.format("YYYY-MM-DD")
          : "";
      const defaultTime24Str =
        typeof moment !== "undefined" && defaultMoment && defaultMoment.format
          ? defaultMoment.format("HH:mm")
          : "10:30";
      const defaultTime12Str =
        typeof moment !== "undefined" && defaultMoment && defaultMoment.format
          ? defaultMoment.format("hh:mm A")
          : "10:30 AM";
      const defaultFullStr =
        typeof moment !== "undefined" && defaultMoment && defaultMoment.format
          ? defaultMoment.format("YYYY-MM-DD, hh:mm A")
          : "";
      const todayStr =
        typeof moment !== "undefined" ? moment().format("YYYY-MM-DD") : "";

      // 1. Initialize Native Date Inputs
      if (todayStr) {
        $("#modalFollowUpDate, #composerFollowupDate").attr("min", todayStr);
      }

      if (!$("#modalFollowUpDate").val())
        $("#modalFollowUpDate").val(defaultDateStr);
      if (!$("#modalFollowUpTimeInput").val())
        $("#modalFollowUpTimeInput").val(defaultTime24Str);
      if (!$("#modalFollowUpTime").val())
        $("#modalFollowUpTime").val(defaultFullStr);

      if (!$("#composerFollowupDate").val())
        $("#composerFollowupDate").val(defaultDateStr);
      if (!$("#composerFollowupTimeInput").val())
        $("#composerFollowupTimeInput").val(defaultTime24Str);
      if (!$("#composerFollowupTime").val())
        $("#composerFollowupTime").val(defaultFullStr);

      // Initial conflict check
      if ($("#modalSlotStatus").length && $("#modalFollowUpTime").val()) {
        self.checkSlotConflict(
          $("#modalFollowUpTime").val(),
          $("#modalSlotStatus"),
        );
      }
      if ($("#composerSlotStatus").length && $("#composerFollowupTime").val()) {
        self.checkSlotConflict(
          $("#composerFollowupTime").val(),
          $("#composerSlotStatus"),
        );
      }
    },

    checkSlotConflict: function (datetimeStr, $statusBox) {
      const self = this;
      if (!datetimeStr || !$statusBox || !$statusBox.length) return;
      const agentId = self.leadData ? self.leadData.agent_id || 0 : 0;

      $.ajax({
        url: "/api/v3/leads/check-slot/",
        type: "GET",
        data: {
          lead_id: self.leadId,
          agent_id: agentId,
          datetime: datetimeStr,
        },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "conflict") {
            $statusBox.html(`
              <div class="v3-slot-conflict-alert">
                <i class="fi fi-rr-exclamation"></i>
                <div>
                  <strong>Slot Conflict:</strong> Already scheduled with <span>${res.conflict_customer}</span> (Lead #${res.conflict_lead_id}) at ${res.conflict_time}.
                </div>
              </div>
            `);
          } else if (res && res.status === "available") {
            $statusBox.html(`
              <div class="v3-slot-available-pill">
                <i class="fi fi-rr-check"></i> Time slot is available (${res.time_formatted})
              </div>
            `);
          } else {
            $statusBox.empty();
          }
        },
        error: function () {
          $statusBox.empty();
        },
      });
    },

    submitComposerNote: function (postData, $btn) {
      const self = this;
      $btn.prop("disabled", true).text("Posting...");

      $.ajax({
        url: "/api/v3/leads/add-note/",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function (res) {
          $btn
            .prop("disabled", false)
            .html('<i class="fi fi-rr-check"></i> Post Note');

          if (res && res.status === "conflict") {
            if (
              typeof Swal !== "undefined" &&
              typeof Swal.fire === "function"
            ) {
              Swal.fire({
                title: "⚠️ Time Slot Conflict",
                html: `<div style="text-align:left; font-size:13.5px; line-height:1.5;">
                        <p style="margin-bottom:8px;">You already have another follow-up scheduled with <strong>${res.conflict_customer}</strong> (Lead #${res.conflict_lead_id}) at <strong>${res.conflict_time}</strong>.</p>
                        <p style="color:#64748b; font-size:12.5px;">Would you like to select a different follow-up time or schedule anyway?</p>
                       </div>`,
                type: "warning",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#059669",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Schedule Anyway",
                cancelButtonText: "Change Time",
              }).then(function (result) {
                if (result.value || result.isConfirmed) {
                  postData.force_override = 1;
                  self.submitComposerNote(postData, $btn);
                } else {
                  $("#composerFollowupTimeSelect").focus();
                }
              });
            } else {
              if (
                confirm(res.message + "\n\nDo you want to schedule anyway?")
              ) {
                postData.force_override = 1;
                self.submitComposerNote(postData, $btn);
              } else {
                $("#composerFollowupTimeSelect").focus();
              }
            }
            return;
          }

          if (res && res.status === "success") {
            $("#composerNoteInput").val("");
            self.notify(res.message || "Update logged successfully", "success");

            if (res.followup_time) {
              self.scheduleClientReminder(
                res.followup_time,
                self.leadData ? self.leadData.name : "Customer",
                self.leadData ? self.leadData.agent_id : 0,
                self.leadId,
              );
            }

            self.loadLeadDetails();
          } else {
            self.notify(
              (res && res.message) || "Failed to post update.",
              "error",
            );
          }
        },
        error: function () {
          $btn
            .prop("disabled", false)
            .html('<i class="fi fi-rr-check"></i> Post Note');
          self.notify("Network connection error.", "error");
        },
      });
    },

    submitModalFollowUp: function (postData, $btn) {
      const self = this;
      $btn.prop("disabled", true).text("Scheduling...");

      $.ajax({
        url: "/api/v3/leads/add-note/",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function (res) {
          $btn.prop("disabled", false).text("Set Reminder");

          if (res && res.status === "conflict") {
            if (
              typeof Swal !== "undefined" &&
              typeof Swal.fire === "function"
            ) {
              Swal.fire({
                title: "⚠️ Time Slot Conflict",
                html: `<div style="text-align:left; font-size:13.5px; line-height:1.5;">
                        <p style="margin-bottom:8px;">You already have another follow-up scheduled with <strong>${res.conflict_customer}</strong> (Lead #${res.conflict_lead_id}) at <strong>${res.conflict_time}</strong>.</p>
                        <p style="color:#64748b; font-size:12.5px;">Would you like to select a different follow-up time or schedule anyway?</p>
                       </div>`,
                type: "warning",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#059669",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Schedule Anyway",
                cancelButtonText: "Change Time",
              }).then(function (result) {
                if (result.value || result.isConfirmed) {
                  postData.force_override = 1;
                  self.submitModalFollowUp(postData, $btn);
                } else {
                  $("#modalFollowUpTimeSelect").focus();
                }
              });
            } else {
              if (
                confirm(res.message + "\n\nDo you want to schedule anyway?")
              ) {
                postData.force_override = 1;
                self.submitModalFollowUp(postData, $btn);
              } else {
                $("#modalFollowUpTimeSelect").focus();
              }
            }
            return;
          }

          if (res && res.status === "success") {
            self.notify(
              res.message || "Follow-up reminder set successfully.",
              "success",
            );
            $("#modalSetFollowUp").removeClass("is-active");
            self.loadLeadDetails();
          } else {
            self.notify(
              (res && res.message) || "Failed to set follow-up reminder.",
              "error",
            );
          }
        },
        error: function () {
          $btn.prop("disabled", false).text("Set Reminder");
          self.notify(
            "Network error while setting follow-up reminder.",
            "error",
          );
        },
      });
    },
  };

  // Run on page ready and SPA transitions
  $(document).ready(function () {
    window.V3LeadView.init();
  });

  $(document)
    .off("spa:loaded.v3LeadView")
    .on("spa:loaded.v3LeadView", function () {
      window.V3LeadView.init();
    });
});
