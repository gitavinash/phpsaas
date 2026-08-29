/**
 * V3 Policy Create & Update Form Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/policy-form.js
 */

(function (window, $) {
  "use strict";

  const V3PolicyForm = {
    createApi: "/api/v3/policy/create/",
    updateApi: "/api/v3/policy/update/",
    detailsApi: "/api/v3/policy/details/",
    isEditMode: false,
    policyId: "",
    selectedBannerFile: null,
    existingBannerUrl: "",
    slugManuallyEdited: false,
    seoManuallyEdited: false,

    init: function () {
      const $addRoot = $("#policyAddApp");
      const $updateRoot = $("#policyUpdateApp");

      if (!$addRoot.length && !$updateRoot.length) return;

      this.isEditMode = !!$updateRoot.length;
      this.policyId = this.isEditMode ? ($updateRoot.data("policy-id") || $("#editPolicyId").val()) : "";
      this.selectedBannerFile = null;
      this.existingBannerUrl = "";
      this.slugManuallyEdited = false;
      this.seoManuallyEdited = false;

      this.initSelect2();
      this.initEditor();
      this.bindEvents();

      if (this.isEditMode && this.policyId) {
        this.loadPolicyDetails();
      } else {
        this.updateLivePreview();
        this.updateSeoCountersAndSerp();
      }
    },

    initSelect2: function () {
      if ($.fn.select2) {
        $("#policyStatus").select2({
          minimumResultsForSearch: Infinity,
          width: "100%"
        });
      }
    },

    initEditor: function () {
      const $desc = $("#policyDescription");
      if (!$desc.length) return;

      if (typeof $.fn.v3Editor === "function") {
        $desc.v3Editor({
          placeholder: "Enter complete policy clauses, cancellation timelines, refund rules, and terms of service..."
        });
      }
    },

    getEditorContent: function () {
      const editor = $("#policyDescription").data("v3Editor");
      if (editor && typeof editor.getContent === "function") {
        return editor.getContent();
      }
      return $("#policyDescription").val() || "";
    },

    setEditorContent: function (content) {
      const self = this;
      const val = content || "";
      $("#policyDescription").val(val);

      const editor = $("#policyDescription").data("v3Editor");
      if (editor && typeof editor.setContent === "function") {
        editor.setContent(val);
      }

      self.updateCounts();
      self.updateLivePreview();
      self.updateSeoCountersAndSerp();
    },

    loadPolicyDetails: function () {
      const self = this;

      $.ajax({
        url: self.detailsApi,
        type: "GET",
        data: { id: self.policyId },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.policy) {
            self.populateForm(res.policy);
          } else {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", (res && res.msg) || "Failed to load policy details.", "error");
            }
          }
        },
        error: function () {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Network error loading policy details.", "error");
          }
        }
      });
    },

    populateForm: function (p) {
      const self = this;

      $("#policyTitle").val(p.title || "");
      $("#policySlug").val(p.slug || "");
      $("#slugPreviewText").text(p.full_slug || `/${p.slug || ""}/`);
      self.setEditorContent(p.description || "");
      $("#policyMetaTitle").val(p.meta_title || "");
      $("#policyMetaDescription").val(p.meta_description || "");
      $("#policyMetaKeywords").val(p.meta_keywords || "");

      if (p.status !== undefined) {
        $("#policyStatus").val(String(p.status));
        if ($.fn.select2) $("#policyStatus").trigger("change.select2");
      }

      if (p.image_url) {
        self.existingBannerUrl = p.image_url;
        $("#policyPreviewImg").attr("src", p.image_url);
        $("#policyUploadPrompt").hide();
        $("#policyPreviewWrap").show();
      }

      self.slugManuallyEdited = true;
      self.seoManuallyEdited = !!(p.meta_title || p.meta_description);
      self.updateLivePreview();
      self.updateSeoCountersAndSerp();
    },

    bindEvents: function () {
      const self = this;
      $(document).off(".policyForm");

      // Auto slug generator from title
      $(document).on("input.policyForm", "#policyTitle", function () {
        const val = $(this).val();
        self.clearFieldError("policyTitle");

        if (!self.slugManuallyEdited || !self.isEditMode) {
          const autoSlug = self.generateSlug(val);
          $("#policySlug").val(autoSlug);
          $("#slugPreviewText").text(`/${autoSlug}/`);
        }

        // Auto-generate SEO if user hasn't manually altered SEO fields on create
        if (!self.seoManuallyEdited && !self.isEditMode) {
          self.autoSuggestSeo(false);
        }

        self.updateLivePreview();
        self.updateSeoCountersAndSerp();
      });

      // Manual slug editing
      $(document).on("input.policyForm", "#policySlug", function () {
        self.slugManuallyEdited = true;
        self.clearFieldError("policySlug");
        const clean = self.generateSlug($(this).val());
        $("#slugPreviewText").text(`/${clean}/`);
        self.updateLivePreview();
        self.updateSeoCountersAndSerp();
      });

      // Real-time description & word counter listener
      $(document).on("input.policyForm change.policyForm", "#policyDescription", function () {
        self.clearFieldError("policyDescription");
        self.updateCounts();
        self.updateLivePreview();

        if (!self.seoManuallyEdited && !self.isEditMode) {
          self.autoSuggestSeo(false);
        }
        self.updateSeoCountersAndSerp();
      });

      // Manual SEO fields editing
      $(document).on("input.policyForm", "#policyMetaTitle, #policyMetaDescription, #policyMetaKeywords", function () {
        self.seoManuallyEdited = true;
        self.updateSeoCountersAndSerp();
      });

      // Auto-Generate SEO button click
      $(document).on("click.policyForm", "#btnAutoSuggestSeo", function (e) {
        e.preventDefault();
        self.autoSuggestSeo(true);
      });

      // Status change
      $(document).on("change.policyForm", "#policyStatus", function () {
        self.updateLivePreview();
      });

      // Banner file dropzone click
      $(document).on("click.policyForm", "#policyDropZone, #btnBrowsePolicyBanner", function (e) {
        if (e.target.id !== "policyBannerInput") {
          $("#policyBannerInput").trigger("click");
        }
      });

      $(document).on("change.policyForm", "#policyBannerInput", function () {
        const file = this.files && this.files[0];
        if (file) {
          self.handleBannerSelected(file);
        }
      });

      // Drag & drop
      const $dropZone = $("#policyDropZone");
      if ($dropZone.length) {
        $dropZone.off(".policyFormDrag").on("dragover.policyFormDrag dragenter.policyFormDrag", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).addClass("drag-active");
        });

        $dropZone.on("dragleave.policyFormDrag drop.policyFormDrag", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).removeClass("drag-active");
        });

        $dropZone.on("drop.policyFormDrag", function (e) {
          const dt = e.originalEvent.dataTransfer;
          if (dt && dt.files && dt.files.length) {
            self.handleBannerSelected(dt.files[0]);
          }
        });
      }

      // Remove Banner
      $(document).on("click.policyForm", "#btnRemovePolicyBanner", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.selectedBannerFile = null;
        self.existingBannerUrl = "";
        $("#policyBannerInput").val("");
        $("#policyUploadPrompt").show();
        $("#policyPreviewWrap").hide();
      });

      // Submit Form
      $(document).on("submit.policyForm", "#policyForm", function (e) {
        e.preventDefault();
        self.submitForm();
      });
    },

    autoSuggestSeo: function (notifyUser) {
      const self = this;
      const title = $("#policyTitle").val().trim();
      const rawContent = self.getEditorContent();
      const cleanText = rawContent.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();

      if (!title && !cleanText) {
        if (notifyUser) {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Please enter a Policy Title or Content first.", "warning");
          } else {
            alert("Please enter a Policy Title or Content first.");
          }
        }
        return;
      }

      // 1. Meta Title (Max ~60 chars)
      let metaTitle = title ? `${title} | Legal Policy & Terms` : "Policy & Terms";
      if (metaTitle.length > 60 && title) {
        metaTitle = title.length <= 60 ? title : title.substring(0, 57) + "...";
      }

      // 2. Meta Description (Max ~155-160 chars)
      let metaDesc = "";
      if (cleanText) {
        // Extract first 1 or 2 sentences
        const sentences = cleanText.match(/[^.!?]+[.!?]+/g) || [cleanText];
        let snippet = sentences[0] || cleanText;
        if (snippet.length < 90 && sentences.length > 1) {
          snippet += " " + sentences[1];
        }
        snippet = snippet.trim();
        if (snippet.length > 155) {
          snippet = snippet.substring(0, 152).replace(/\s+\S*$/, "") + "...";
        }
        metaDesc = snippet;
      } else if (title) {
        metaDesc = `Review the official ${title} for our services, booking terms, user conditions, and legal guidelines.`;
      }

      // 3. Meta Keywords
      const keywordsSet = new Set();
      if (title) {
        title.toLowerCase().split(/\s+/).forEach((w) => {
          const cleanW = w.replace(/[^a-z0-9]/g, "");
          if (cleanW.length > 3 && !["with", "from", "that", "this", "your", "have"].includes(cleanW)) {
            keywordsSet.add(cleanW);
          }
        });
      }

      // Add common policy keyword buckets
      const lowerAll = (title + " " + cleanText).toLowerCase();
      if (lowerAll.includes("privacy") || lowerAll.includes("data") || lowerAll.includes("cookies")) {
        keywordsSet.add("privacy policy");
        keywordsSet.add("data protection");
        keywordsSet.add("user privacy");
      }
      if (lowerAll.includes("refund") || lowerAll.includes("cancel") || lowerAll.includes("return")) {
        keywordsSet.add("cancellation policy");
        keywordsSet.add("refund rules");
        keywordsSet.add("booking cancellation");
      }
      if (lowerAll.includes("terms") || lowerAll.includes("condition") || lowerAll.includes("service")) {
        keywordsSet.add("terms of service");
        keywordsSet.add("terms and conditions");
        keywordsSet.add("user agreement");
      }
      keywordsSet.add("legal policy");

      const metaKeywords = Array.from(keywordsSet).slice(0, 8).join(", ");

      // Update fields
      $("#policyMetaTitle").val(metaTitle);
      $("#policyMetaDescription").val(metaDesc);
      $("#policyMetaKeywords").val(metaKeywords);

      self.updateSeoCountersAndSerp();

      if (notifyUser) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", "Meta tags suggested from policy content!", "success");
        }
      }
    },

    updateSeoCountersAndSerp: function () {
      const metaTitle = $("#policyMetaTitle").val().trim() || $("#policyTitle").val().trim() || "Policy Title | Legal Terms";
      const metaDesc = $("#policyMetaDescription").val().trim() || "Policy summary and clauses will appear here in Google search engine result snippets...";
      const slug = $("#policySlug").val().trim() || this.generateSlug($("#policyTitle").val().trim()) || "policy";

      // Character counters
      const titleLen = $("#policyMetaTitle").val().length;
      const descLen = $("#policyMetaDescription").val().length;

      const $tCount = $("#metaTitleCharCount");
      $tCount.text(`${titleLen} / 60 chars`);
      $tCount.css("color", titleLen > 60 ? "#ef4444" : (titleLen >= 40 ? "#16a34a" : "#64748b"));

      const $dCount = $("#metaDescCharCount");
      $dCount.text(`${descLen} / 160 chars`);
      $dCount.css("color", descLen > 160 ? "#ef4444" : (descLen >= 110 ? "#16a34a" : "#64748b"));

      // SERP Preview
      $("#serpTitlePreview").text(metaTitle);
      $("#serpSnippetPreview").text(metaDesc);
      $("#serpSlugPreview").text(slug);
    },

    handleBannerSelected: function (file) {
      const self = this;
      if (!file.type.match("image.*")) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", "Please select an image file (PNG, JPG, WEBP, GIF).", "warning");
        } else {
          alert("Please select an image file (PNG, JPG, WEBP, GIF).");
        }
        return;
      }

      self.selectedBannerFile = file;
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#policyPreviewImg").attr("src", e.target.result);
        $("#policyUploadPrompt").hide();
        $("#policyPreviewWrap").show();
      };
      reader.readAsDataURL(file);
    },

    updateCounts: function () {
      const content = this.getEditorContent();
      // Strip HTML tags for accurate word & character counting
      const textOnly = content.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
      const charCount = textOnly.length;
      const words = textOnly ? textOnly.split(/\s+/).length : 0;

      $("#policyCharCount").text(`${charCount} characters`);
      $("#policyWordCount").text(`${words} words`);
    },

    updateLivePreview: function () {
      const title = $("#policyTitle").val().trim() || "Policy Title";
      const slug = $("#policySlug").val().trim() || this.generateSlug(title);
      const content = this.getEditorContent();
      const textOnly = content.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
      const statusVal = $("#policyStatus").val();

      $("#previewPolicyTitle").text(title);
      $("#previewPolicyUrl").text(`/${slug}/`);

      // Status pill
      if (statusVal === "1") {
        $("#previewPolicyStatusBadge").text("Active & Published").attr("class", "company-type-pill company-badge-hq");
      } else {
        $("#previewPolicyStatusBadge").text("Draft / Inactive").attr("class", "company-type-pill company-badge-branch");
      }

      // Read time
      const words = textOnly ? textOnly.split(/\s+/).length : 0;
      const readMin = Math.max(1, Math.ceil(words / 200));
      $("#previewReadTime").text(`${readMin} min read (${words} words)`);

      // Excerpt
      const excerpt = textOnly ? textOnly.substring(0, 140) + (textOnly.length > 140 ? "..." : "") : "Policy clauses and content summary will appear here...";
      $("#previewExcerpt").text(excerpt);
    },

    generateSlug: function (text) {
      if (!text) return "";
      return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/[\s\W-]+/g, "-")
        .replace(/^-+|-+$/g, "");
    },

    validate: function () {
      let isValid = true;
      const title = $("#policyTitle").val().trim();
      const desc = this.getEditorContent().trim();
      const textOnly = desc.replace(/<[^>]*>/g, "").trim();

      if (!title) {
        this.showFieldError("policyTitle", "Policy title is required.");
        isValid = false;
      }

      if (!desc || (!textOnly && !desc.includes("<img") && !desc.includes("<table"))) {
        this.showFieldError("policyDescription", "Policy content body is required.");
        isValid = false;
      }

      return isValid;
    },

    submitForm: function () {
      const self = this;
      if (!self.validate()) return;

      const $btn = $("#btnSubmitPolicy");
      const originalText = $btn.html();

      $btn.prop("disabled", true).html('<span class="v3-btn-spinner"></span> Saving Policy...');

      const formElement = document.getElementById("policyForm");
      const formData = new FormData(formElement);

      if (self.isEditMode) {
        formData.set("id", self.policyId);
      }

      const editorHtml = self.getEditorContent();

      formData.set("title", $("#policyTitle").val().trim());
      formData.set("slug", $("#policySlug").val().trim());
      formData.set("description", editorHtml);
      formData.set("status", $("#policyStatus").val());
      formData.set("meta_title", $("#policyMetaTitle").val().trim());
      formData.set("meta_description", $("#policyMetaDescription").val().trim());
      formData.set("meta_keywords", $("#policyMetaKeywords").val().trim());

      if (self.selectedBannerFile) {
        formData.set("featureImage", self.selectedBannerFile);
      }

      const targetUrl = self.isEditMode ? self.updateApi : self.createApi;

      $.ajax({
        url: targetUrl,
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (res) {
          $btn.prop("disabled", false).html(originalText);

          if (res && res.status === "success") {
            const successMsg = res.msg || (self.isEditMode ? "Policy updated successfully!" : "Policy created successfully!");

            if (typeof popUpMessage === "function") {
              popUpMessage("center", successMsg, "success");
              setTimeout(function () {
                window.location.href = "/dashboard/policy/list/";
              }, 1500);
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Success!",
                text: successMsg,
                icon: "success",
                type: "success",
                timer: 1600,
                showConfirmButton: false
              }).then(function () {
                window.location.href = "/dashboard/policy/list/";
              });
            } else {
              alert(successMsg);
              window.location.href = "/dashboard/policy/list/";
            }
          } else {
            const msg = (res && res.msg) || "Could not save policy.";
            if (res && res.field) {
              self.showFieldError("policy" + res.field.charAt(0).toUpperCase() + res.field.slice(1), msg);
            }
            if (typeof popUpMessage === "function") {
              popUpMessage("center", msg, "error");
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Error",
                text: msg,
                icon: "error",
                type: "error"
              });
            } else {
              alert(msg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false).html(originalText);

          let msg = "Network or server error while saving policy.";
          try {
            const errRes = JSON.parse(xhr.responseText);
            if (errRes && errRes.msg) msg = errRes.msg;
          } catch (e) {}

          if (typeof popUpMessage === "function") {
            popUpMessage("center", msg, "error");
          } else if (window.Swal && typeof window.Swal.fire === "function") {
            window.Swal.fire({
              title: "Error",
              text: msg,
              icon: "error",
              type: "error"
            });
          } else {
            alert(msg);
          }
        }
      });
    },

    showFieldError: function (fieldId, msg) {
      const $field = $("#" + fieldId);
      $field.addClass("is-invalid");
      const $err = $("#err_" + fieldId);
      if ($err.length) {
        $err.text(msg).show();
      }
    },

    clearFieldError: function (fieldId) {
      $("#" + fieldId).removeClass("is-invalid");
      $("#err_" + fieldId).text("").hide();
    }
  };

  $(document).ready(function () {
    V3PolicyForm.init();
  });

  $(document).on("spa:loaded", function () {
    V3PolicyForm.init();
  });

  window.V3PolicyForm = V3PolicyForm;
})(window, jQuery);
