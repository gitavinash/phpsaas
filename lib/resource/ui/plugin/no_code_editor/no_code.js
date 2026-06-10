(function () {
  "use strict";

  /* ── State ─────────────────────────────────────────── */
  const CFG = window.BUILDER_CONFIG || {};
  const state = {
    title: "",
    sections: [],
    selSection: null,
    selBlock: null,
    selSubElement: null, // Track sub-element selection (image/link inside a block)
    mode: "edit", // 'edit' | 'preview'
    themeColor: CFG.theme_color || "#6366f1",
    history: [],
    future: [],
    _noHistory: false,
    tempImageFile: null,
    tempSubImageFile: null,
  };

  /* ── History helpers ──────────────────────────────── */
  function snap() {
    if (state._noHistory) return;
    state.history.push(JSON.stringify(state.sections));
    state.future = [];
    if (state.history.length > 60) state.history.shift();
    updateUndoUI();
  }

  function undo() {
    if (!state.history.length) return;
    state.future.push(JSON.stringify(state.sections));
    state._noHistory = true;
    state.sections = JSON.parse(state.history.pop());
    state.selSection = state.selBlock = null;
    state._noHistory = false;
    renderCanvas();
    renderProps();
    updateUndoUI();
    showSave("saving", "Unsaved");
  }

  function redo() {
    if (!state.future.length) return;
    state.history.push(JSON.stringify(state.sections));
    state._noHistory = true;
    state.sections = JSON.parse(state.future.pop());
    state.selSection = state.selBlock = null;
    state._noHistory = false;
    renderCanvas();
    renderProps();
    updateUndoUI();
    showSave("saving", "Unsaved");
  }

  function updateUndoUI() {
    const u = document.getElementById("btn-undo");
    const r = document.getElementById("btn-redo");
    if (u) u.disabled = !state.history.length;
    if (r) r.disabled = !state.future.length;
  }

  /* ── Autosave indicator ──────────────────────────── */
  function showSave(status, label) {
    const el = document.getElementById("save-ind");
    const txt = document.getElementById("save-txt");
    if (!el) return;
    el.className = "save-ind " + status;
    if (txt)
      txt.textContent =
        label ||
        (status === "saved"
          ? "Saved"
          : status === "saving"
            ? "Saving…"
            : "Error");
  }

  /* ── ID helpers ──────────────────────────────────── */
  const uid = () => Math.random().toString(36).substr(2, 9);
  const sid = () => "sec_" + uid();
  const bid = () => "blk_" + uid();

  /* ── Section Templates ───────────────────────────── */
  const T = {
    hero: () => ({
      id: sid(),
      label: "Hero Banner",
      type: "hero_section",
      settings: {
        background_color: "#f8f9fa",
        background_image: "",
        padding_top: "80",
        padding_bottom: "80",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "heading",
          tag: "h1",
          text: "Dream Big. Build Beautiful Pages.",
          align: "center",
          color: "#1a1a2e",
          size: "48px",
          width: 100,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Add predefined sections with zero coding. Customise text, colours, alignments and buttons — all inline.",
          align: "center",
          color: "#666666",
          size: "18px",
          width: 100,
        },
        {
          id: bid(),
          type: "button",
          text: "Get Started Today",
          link: "#",
          align: "center",
          bg_color: state.themeColor,
          color: "#ffffff",
          radius: "8",
          padding_y: "14",
          padding_x: "32",
          width: 100,
        },
      ],
    }),
    text_image: () => ({
      id: sid(),
      label: "Text & Image",
      type: "text_image_section",
      settings: {
        background_color: "#ffffff",
        background_image: "",
        padding_top: "60",
        padding_bottom: "60",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "Perfect 2-Column Layouts",
          align: "left",
          color: "#1a1a2e",
          size: "32px",
          width: 50,
        },
        {
          id: bid(),
          type: "image",
          src: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&auto=format&fit=crop&q=60",
          alt: "Graphic",
          align: "center",
          radius: "12",
          link: "",
          width: 50,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "This is a two-column layout. The heading and text sit on the left while the image floats on the right. Drag to reorder or resize blocks.",
          align: "left",
          color: "#555555",
          size: "16px",
          width: 50,
        },
      ],
    }),
    features: () => ({
      id: sid(),
      label: "Feature Grid",
      type: "features_section",
      settings: {
        background_color: "#ffffff",
        background_image: "",
        padding_top: "60",
        padding_bottom: "60",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "Our Core Features",
          align: "center",
          color: "#1a1a2e",
          size: "36px",
          width: 100,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h3",
          text: "⚡ Modern Speed",
          align: "center",
          color: "#1a1a2e",
          size: "20px",
          width: 33,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h3",
          text: "🎨 Rich Aesthetics",
          align: "center",
          color: "#1a1a2e",
          size: "20px",
          width: 33,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h3",
          text: "📱 Responsive",
          align: "center",
          color: "#1a1a2e",
          size: "20px",
          width: 33,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Pages render extremely fast on all viewports.",
          align: "center",
          color: "#666",
          size: "15px",
          width: 33,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Gradients, micro-animations, and modern styling.",
          align: "center",
          color: "#666",
          size: "15px",
          width: 33,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Adapts seamlessly to tablet, phone and desktop.",
          align: "center",
          color: "#666",
          size: "15px",
          width: 33,
        },
      ],
    }),
    faq: () => ({
      id: sid(),
      label: "FAQ",
      type: "faq_section",
      settings: {
        background_color: "#f8f9fa",
        background_image: "",
        padding_top: "50",
        padding_bottom: "50",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "Frequently Asked Questions",
          align: "center",
          color: "#1a1a2e",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "static_html",
          width: 100,
          content: `<div style="max-width:720px;margin:0 auto;">
  <details style="background:#fff;padding:18px 20px;border-radius:10px;margin-bottom:10px;border:1px solid #e1e1e6;cursor:pointer;">
    <summary style="font-size:16px;font-weight:600;color:#1a1a2e;list-style:none;">How does the page builder work?</summary>
    <p style="margin:10px 0 0;font-size:14px;color:#666;line-height:1.7;">Add predefined layouts visually and customise all contents and styles inline — zero coding required.</p>
  </details>
  <details style="background:#fff;padding:18px 20px;border-radius:10px;margin-bottom:10px;border:1px solid #e1e1e6;cursor:pointer;">
    <summary style="font-size:16px;font-weight:600;color:#1a1a2e;list-style:none;">Are changes backward-compatible?</summary>
    <p style="margin:10px 0 0;font-size:14px;color:#666;line-height:1.7;">Yes. Legacy HTML is loaded as a Custom HTML block so nothing is ever lost.</p>
  </details>
</div>`,
        },
      ],
    }),
    testimonials: () => ({
      id: sid(),
      label: "Testimonials",
      type: "testimonials_section",
      settings: {
        background_color: "#ffffff",
        background_image: "",
        padding_top: "50",
        padding_bottom: "50",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "What Our Clients Say",
          align: "center",
          color: "#1a1a2e",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "static_html",
          width: 100,
          content: `<div style="max-width:820px;margin:20px auto 0;display:flex;gap:18px;flex-wrap:wrap;">
  <div style="flex:1;min-width:240px;background:#f8f9fa;padding:22px;border-radius:12px;border-left:4px solid ${state.themeColor};">
    <p style="font-style:italic;font-size:15px;color:#444;line-height:1.7;margin:0 0 12px;">"The best experience building pages. Premium results out of the box."</p>
    <strong style="font-size:13px;color:#222;">— Avinash Sharma, Founder</strong>
  </div>
  <div style="flex:1;min-width:240px;background:#f8f9fa;padding:22px;border-radius:12px;border-left:4px solid ${state.themeColor};">
    <p style="font-style:italic;font-size:15px;color:#444;line-height:1.7;margin:0 0 12px;">"My team loves how easy it is to update page layouts without a developer."</p>
    <strong style="font-size:13px;color:#222;">— Priya Mehta, Marketing</strong>
  </div>
</div>`,
        },
      ],
    }),
    cta: () => ({
      id: sid(),
      label: "Call to Action",
      type: "cta_section",
      settings: {
        background_color: state.themeColor,
        background_image: "",
        padding_top: "60",
        padding_bottom: "60",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "Ready to Get Started?",
          align: "center",
          color: "#ffffff",
          size: "36px",
          width: 100,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Join thousands of businesses who trust us for exceptional results.",
          align: "center",
          color: "rgba(255,255,255,0.85)",
          size: "17px",
          width: 100,
        },
        {
          id: bid(),
          type: "button",
          text: "Contact Us Today",
          link: "#",
          align: "center",
          bg_color: "#ffffff",
          color: state.themeColor,
          radius: "8",
          padding_y: "13",
          padding_x: "30",
          width: 100,
        },
      ],
    }),
    recent_articles: () => ({
      id: sid(),
      label: "Recent Articles",
      type: "widget_section",
      settings: {
        background_color: "#ffffff",
        background_image: "",
        padding_top: "60",
        padding_bottom: "60",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "Our Recent Articles",
          align: "center",
          color: "#1a1a2e",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "widget",
          widget_type: "recent_articles",
          settings: { limit: 3 },
          width: 100,
        },
      ],
    }),
    custom_html: () => ({
      id: sid(),
      label: "Custom HTML",
      type: "custom_html_section",
      settings: {
        background_color: "#ffffff",
        background_image: "",
        padding_top: "30",
        padding_bottom: "30",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "static_html",
          width: 100,
          content: `<div style="padding:24px;background:linear-gradient(135deg,${state.themeColor},#1a1a2e);color:#fff;border-radius:10px;text-align:center;">
  <h3 style="margin:0 0 8px;">Custom HTML Block</h3>
  <p style="margin:0;font-size:14px;opacity:.9;">Edit this code in the Properties panel on the right.</p>
</div>`,
        },
      ],
    }),
    spacer: () => ({
      id: sid(),
      label: "Spacer",
      type: "spacer_section",
      settings: {
        background_color: "#ffffff",
        background_image: "",
        padding_top: "0",
        padding_bottom: "0",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [{ id: bid(), type: "spacer", height: "40", width: 100 }],
    }),
  };

  /* ── DOM references ──────────────────────────────── */
  let canvas, propsBody, canvasArea;

  /* ── Init ────────────────────────────────────────── */
  // Script is loaded at bottom of <body>, so DOM is already ready.
  // Using DOMContentLoaded here would silently miss the event.
  function init() {
    canvas = document.getElementById("canvas-sections") || document.getElementById("canvas");
    propsBody = document.getElementById("props-body");
    canvasArea = document.getElementById("canvas-area");

    if (!canvas) return; // blank state — no builder UI

    // Section picker
    document.querySelectorAll(".sec-card").forEach((card) => {
      card.addEventListener("click", () => {
        const type = card.dataset.type;
        if (T[type]) {
          snap();
          addSection(T[type]());
        }
      });
    });

    // Category tabs
    document.querySelectorAll(".p-tab").forEach((tab) => {
      tab.addEventListener("click", () => {
        document
          .querySelectorAll(".p-tab")
          .forEach((t) => t.classList.remove("active"));
        tab.classList.add("active");
        filterCat(tab.dataset.cat);
      });
    });

    // Search
    const searchEl = document.getElementById("sec-search");
    if (searchEl)
      searchEl.addEventListener("input", () =>
        filterSearch(searchEl.value.toLowerCase()),
      );

    // Device toggles
    document.querySelectorAll(".dev-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        document
          .querySelectorAll(".dev-btn")
          .forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        const dev = btn.dataset.device;
        canvasArea.className = "canvas-area device-" + dev;
        const labels = {
          desktop: '<i class="fi-rr-computer"></i> <span>Desktop — 1200px</span>',
          tablet: '<i class="fi-rr-tablet"></i> <span>Tablet — 768px</span>',
          mobile: '<i class="fi-rr-smartphone"></i> <span>Mobile — 390px</span>',
        };
        const lbl = document.getElementById("canvas-label");
        if (lbl) lbl.innerHTML = labels[dev] || "";
      });
    });

    // Undo / Redo
    document.getElementById("btn-undo").addEventListener("click", undo);
    document.getElementById("btn-redo").addEventListener("click", redo);

    // Save
    document.getElementById("btn-save").addEventListener("click", saveLayout);

    // Preview
    document
      .getElementById("btn-preview")
      .addEventListener("click", togglePreview);

    // Click outside canvas → deselect
    canvasArea.addEventListener("click", (e) => {
      if (e.target === canvasArea || e.target.id === "canvas" || e.target.id === "canvas-sections") {
        state.selSection = state.selBlock = null;
        renderCanvas();
        renderProps();
      }
    });

    // Disable hyperlink navigation inside the canvas container & handle inline editing for images/links
    const canvasFrame = document.getElementById("canvas");
    if (canvasFrame) {
      canvasFrame.addEventListener("click", (e) => {
        const anchor = e.target.closest("a");
        const img = e.target.closest("img");
        
        if (state.mode === "edit") {
          if (img) {
            e.preventDefault();
            e.stopPropagation();
            const bWrap = img.closest(".blk-wrap");
            if (bWrap) {
              const bid = bWrap.dataset.blkId;
              const sid = img.closest(".sec-wrap")?.dataset.secId;
              if (sid && bid) {
                selectSubElement(sid, bid, 'image', img);
              }
            }
            return;
          } else if (anchor) {
            e.preventDefault();
            e.stopPropagation();
            const bWrap = anchor.closest(".blk-wrap");
            if (bWrap) {
              const bid = bWrap.dataset.blkId;
              const sid = anchor.closest(".sec-wrap")?.dataset.secId;
              if (sid && bid) {
                selectSubElement(sid, bid, 'link', anchor);
              }
            }
            return;
          }
        } else {
          if (anchor) {
            e.preventDefault();
          }
        }
        
        const button = e.target.closest("button, input[type='submit']");
        if (button && !button.classList.contains("ctrl-btn") && !button.classList.contains("tb-btn")) {
          e.preventDefault();
        }
      }, true);
    }

    // Keyboard shortcuts
    document.addEventListener("keydown", (e) => {
      const tag = e.target.tagName;
      if (
        tag === "TEXTAREA" ||
        tag === "INPUT" ||
        e.target.contentEditable === "true"
      )
        return;
      if ((e.ctrlKey || e.metaKey) && e.key === "z" && !e.shiftKey) {
        e.preventDefault();
        undo();
      }
      if (
        (e.ctrlKey || e.metaKey) &&
        (e.key === "y" || (e.shiftKey && e.key === "Z"))
      ) {
        e.preventDefault();
        redo();
      }
      if ((e.ctrlKey || e.metaKey) && e.key === "s") {
        e.preventDefault();
        saveLayout();
      }
      if (e.key === "Escape") {
        state.selSection = state.selBlock = null;
        renderCanvas();
        renderProps();
      }
      if (!e.ctrlKey && !e.metaKey) {
        if (e.key === "d")
          document.querySelector('[data-device="desktop"]')?.click();
        if (e.key === "t")
          document.querySelector('[data-device="tablet"]')?.click();
        if (e.key === "m")
          document.querySelector('[data-device="mobile"]')?.click();
      }
    });

    // Only call the API if both id and type are present
    if (CFG.id && CFG.id.trim() !== "" && CFG.type && CFG.type.trim() !== "") {
      loadContent();
    } else {
      // No valid id/type — remove skeleton and show empty canvas
      const skel = document.getElementById("skel");
      if (skel) skel.remove();
      showEmptyCanvas();
      showSave("error", "No content");
      const pgTitle = document.getElementById("pg-title");
      if (pgTitle) pgTitle.textContent = "No Content Selected";
    }
  } // end init()

  // Run immediately — DOM is ready since script is at bottom of <body>
  init();

  /* ── Sidebar filters ─────────────────────────────── */
  function filterCat(cat) {
    document.querySelectorAll(".sec-card").forEach((c) => {
      c.classList.toggle("hidden", cat !== "all" && c.dataset.cat !== cat);
    });
    document.querySelectorAll("[data-lbl]").forEach((l) => {
      l.style.display = cat === "all" || l.dataset.lbl === cat ? "" : "none";
    });
    checkNoResults();
  }

  function filterSearch(q) {
    document.querySelectorAll(".sec-card").forEach((c) => {
      const kw =
        (c.dataset.kw || "") +
        " " +
        (c.querySelector("span")?.textContent || "");
      c.classList.toggle("hidden", q !== "" && !kw.toLowerCase().includes(q));
    });
    document.querySelectorAll("[data-lbl]").forEach((l) => {
      l.style.display = q ? "none" : "";
    });
    checkNoResults();
  }

  function checkNoResults() {
    const msg = document.getElementById("no-results");
    if (msg)
      msg.style.display =
        document.querySelectorAll(".sec-card:not(.hidden)").length === 0
          ? "block"
          : "none";
  }

  /* ── Legacy HTML Parser ──────────────────────────── */
  function parseLegacyHtml(html) {
    const sections = [];
    if (!html || !html.trim()) return sections;

    const parser = new DOMParser();
    const doc = parser.parseFromString(html, "text/html");
    const body = doc.body;

    let currentSection = null;

    function startNewSection(bgColor = "#ffffff") {
      currentSection = {
        id: sid(),
        label: "Section",
        type: "full_width_section",
        settings: {
          background_color: bgColor,
          background_image: "",
          padding_top: "40",
          padding_bottom: "40",
          margin_top: "0",
          margin_bottom: "0"
        },
        blocks: []
      };
      sections.push(currentSection);
    }

    function rgbToHex(rgb) {
      if (!rgb) return "";
      if (rgb.startsWith("#")) return rgb;
      const match = rgb.match(/^rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)$/i);
      if (match) {
        return "#" + ("0" + parseInt(match[1], 10).toString(16)).slice(-2) +
                     ("0" + parseInt(match[2], 10).toString(16)).slice(-2) +
                     ("0" + parseInt(match[3], 10).toString(16)).slice(-2);
      }
      return rgb;
    }

    function elementToBlock(el) {
      const tag = el.tagName.toLowerCase();
      const text = el.innerText.trim();
      const innerHtml = el.innerHTML.trim();

      const align = el.style.textAlign || el.getAttribute("align") || "left";
      const color = rgbToHex(el.style.color) || "";
      const size = el.style.fontSize || "";

      // H1-H6
      if (/^h[1-6]$/.test(tag)) {
        if (tag === "h1" || tag === "h2") {
          startNewSection();
        }
        return {
          id: bid(),
          type: "heading",
          tag: tag,
          text: innerHtml,
          align: align,
          color: color || "#1a1a2e",
          size: size || (tag === "h1" ? "36px" : tag === "h2" ? "28px" : "20px"),
          width: 100
        };
      }

      // IMG
      if (tag === "img") {
        return {
          id: bid(),
          type: "image",
          src: el.getAttribute("src") || "",
          alt: el.getAttribute("alt") || "",
          align: align || "center",
          radius: parseInt(el.style.borderRadius) || 0,
          link: "",
          width: 100
        };
      }

      // Anchor enclosing image
      if (tag === "a" && el.children.length === 1 && el.children[0].tagName.toLowerCase() === "img") {
        const img = el.children[0];
        return {
          id: bid(),
          type: "image",
          src: img.getAttribute("src") || "",
          alt: img.getAttribute("alt") || "",
          align: align || "center",
          radius: parseInt(img.style.borderRadius) || 0,
          link: el.getAttribute("href") || "",
          width: 100
        };
      }

      // Button link
      const isBtnClass = el.classList.contains("btn") || el.classList.contains("button");
      const isBtnStyle = el.style.display === "inline-block" && (el.style.backgroundColor || el.style.border);
      if ((tag === "a" || tag === "button") && (isBtnClass || isBtnStyle)) {
        return {
          id: bid(),
          type: "button",
          text: text || "Click Here",
          link: el.getAttribute("href") || "#",
          align: align || "center",
          bg_color: rgbToHex(el.style.backgroundColor) || state.themeColor,
          color: rgbToHex(el.style.color) || "#ffffff",
          radius: parseInt(el.style.borderRadius) || 6,
          padding_x: parseInt(el.style.paddingLeft) || 24,
          padding_y: parseInt(el.style.paddingTop) || 12,
          width: 100
        };
      }

      // Spacer
      if (tag === "hr") {
        return {
          id: bid(),
          type: "spacer",
          height: "40",
          width: 100
        };
      }

      // Paragraph
      if (tag === "p") {
        return {
          id: bid(),
          type: "paragraph",
          text: innerHtml,
          align: align,
          color: color || "#555555",
          size: size || "16px",
          width: 100
        };
      }

      // Static HTML if complex
      if (["iframe", "form", "table", "details", "svg", "canvas"].includes(tag) || 
          el.querySelector("iframe, form, table, details, svg, canvas")) {
        return {
          id: bid(),
          type: "static_html",
          content: el.outerHTML,
          width: 100
        };
      }

      // Div or other block element
      if (tag === "div" && el.children.length === 0) {
        if (!text) return null;
        return {
          id: bid(),
          type: "paragraph",
          text: innerHtml,
          align: align,
          color: color || "#555555",
          size: size || "16px",
          width: 100
        };
      }

      // Container div with nested children (not a row)
      if (tag === "div" && el.children.length > 0) {
        // Check if all children are inline or simple text
        const hasBlockChildren = Array.from(el.children).some(child => 
          !["span", "a", "strong", "em", "i", "b", "u"].includes(child.tagName.toLowerCase())
        );
        if (!hasBlockChildren) {
          return {
            id: bid(),
            type: "paragraph",
            text: innerHtml,
            align: align,
            color: color || "#555555",
            size: size || "16px",
            width: 100
          };
        }
      }

      return null;
    }

    function parseNode(node) {
      if (node.nodeType !== Node.ELEMENT_NODE) return;
      const tag = node.tagName.toLowerCase();

      // Skip script tags
      if (tag === "script" || tag === "style") return;

      // Check for row class or flex layout
      const isRow = node.classList.contains("row") || node.classList.contains("editor-row") || node.style.display === "flex";
      if (isRow) {
        const cols = Array.from(node.children).filter(c => c.nodeType === Node.ELEMENT_NODE);
        if (cols.length > 0) {
          const colWidth = Math.floor(100 / cols.length);
          if (!currentSection) startNewSection();
          
          cols.forEach(col => {
            // Traverse column children
            Array.from(col.children).forEach(child => {
              const blk = elementToBlock(child);
              if (blk) {
                blk.width = colWidth;
                currentSection.blocks.push(blk);
              } else if (child.nodeType === Node.ELEMENT_NODE && child.children.length > 0) {
                // Nested container inside column
                Array.from(child.children).forEach(gchild => {
                  const gblk = elementToBlock(gchild);
                  if (gblk) {
                    gblk.width = colWidth;
                    currentSection.blocks.push(gblk);
                  }
                });
              }
            });
          });
          return;
        }
      }

      // Check if it is directly convertible to a block
      const block = elementToBlock(node);
      if (block) {
        if (!currentSection) startNewSection();
        currentSection.blocks.push(block);
      } else {
        // Recursive parsing for unhandled structural tags like main, section, body wrapper divs
        Array.from(node.children).forEach(child => {
          parseNode(child);
        });
      }
    }

    // Start parsing
    Array.from(body.children).forEach(child => {
      parseNode(child);
    });

    // Clean up empty sections
    return sections.filter(s => s.blocks.length > 0);
  }

  /* ── Load content ────────────────────────────────── */
  function loadContent() {
    const { id, type } = CFG;
    fetch(
      `/api/builder/load/?action=load&id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}`,
    )
      .then((r) => r.json())
      .then((data) => {
        // Hide skeleton
        const skel = document.getElementById("skel");
        if (skel) skel.remove();

        if (data.status === 200) {
          state.title = data.title || "";
          
          let parsedSections = data.sections || [];
          if (parsedSections.length === 1 && parsedSections[0].blocks && parsedSections[0].blocks.length === 1) {
            const firstBlock = parsedSections[0].blocks[0];
            if (firstBlock.type === "static_html" && firstBlock.content) {
              const parsed = parseLegacyHtml(firstBlock.content);
              if (parsed && parsed.length > 0) {
                parsedSections = parsed;
              }
            }
          }
          state.sections = parsedSections.map(normalizeSection);

          const el = document.getElementById("pg-title");
          if (el) el.textContent = state.title || "Untitled";
          renderCanvas();
          showSave("saved", "Loaded");
        } else {
          showEmptyCanvas();
          showSave("error", "Load error");
          swal("Load Error", data.msg || "Failed to load content.", "error");
        }
      })
      .catch((err) => {
        console.error(err);
        const skel = document.getElementById("skel");
        if (skel) skel.remove();
        showEmptyCanvas();
        showSave("error", "Server error");
        swal("Server Error", "Could not connect to the builder API.", "error");
      });
  }

  function normalizeSection(sec) {
    if (!sec.label) sec.label = "Section";
    if (!sec.settings) sec.settings = {};
    sec.blocks = (sec.blocks || []).map((b) => ({ width: 100, ...b }));
    return sec;
  }

  function showEmptyCanvas() {
    if (!canvas) return;
    canvas.innerHTML = `
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 40px;text-align:center;">
            <div style="width:72px;height:72px;border-radius:50%;background:#f1f5f9;border:2px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;margin-bottom:20px;color:#cbd5e1;font-size:28px;">
                <i class="fi-rr-layer-plus"></i>
            </div>
            <h3 style="font-size:17px;font-weight:600;color:#94a3b8;margin-bottom:8px;">Start Building Your Page</h3>
            <p style="font-size:13px;color:#cbd5e1;max-width:260px;line-height:1.7;">Choose a section from the left panel and click it to add to this canvas.</p>
        </div>`;
  }

  /* ── Save ────────────────────────────────────────── */
  function saveLayout() {
    const { id, type } = CFG;
    showSave("saving");
    const body = new URLSearchParams({
      id,
      type,
      sections: JSON.stringify(state.sections),
      action: "save",
    });
    fetch("/api/builder/save/", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.status === 200) {
          showSave("saved", "Saved");
          swal("Saved!", "Your layout was saved successfully.", "success");
        } else {
          showSave("error", "Failed");
          swal(
            "Save Failed",
            data.msg || "Could not write to database.",
            "error",
          );
        }
      })
      .catch(() => {
        showSave("error", "Error");
        swal("Error", "API connection failed.", "error");
      });
  }

  /* ── Image Upload Helper ─────────────────────────── */
  function uploadBlockImage(file, blk) {
    showSave("saving", "Uploading...");
    const formData = new FormData();
    formData.append("file", file);

    fetch("/api/builder/upload/", {
      method: "POST",
      body: formData
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.status === 200) {
          snap();
          blk.src = data.url;
          renderCanvas();
          renderProps();
          showSave("saved", "Uploaded");
          swal("Uploaded!", "Image uploaded successfully.", "success");
        } else {
          showSave("error", "Upload failed");
          swal("Upload Error", data.msg || "Failed to upload image.", "error");
        }
      })
      .catch((err) => {
        console.error(err);
        showSave("error", "Upload error");
        swal("Upload Error", "Connection to upload API failed.", "error");
      });
  }

  /* ── Custom HTML Block Image Upload Helper ───────── */
  function uploadStaticBlockImage(file, blk) {
    showSave("saving", "Uploading...");
    const formData = new FormData();
    formData.append("file", file);

    fetch("/api/builder/upload/", {
      method: "POST",
      body: formData
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.status === 200) {
          snap();
          
          const tempParser = new DOMParser();
          const tempDoc = tempParser.parseFromString(blk.content || "", "text/html");
          const imgEl = tempDoc.querySelector("img");
          if (imgEl) {
            imgEl.setAttribute("src", data.url);
            blk.content = tempDoc.body.innerHTML;
          }
          
          renderCanvas();
          renderProps();
          showSave("saved", "Uploaded");
          swal("Uploaded!", "Image uploaded successfully.", "success");
        } else {
          showSave("error", "Upload failed");
          swal("Upload Error", data.msg || "Failed to upload image.", "error");
        }
      })
      .catch((err) => {
        console.error(err);
        showSave("error", "Upload error");
        swal("Upload Error", "Connection to upload API failed.", "error");
      });
  }

  /* ── Render Canvas ───────────────────────────────── */
  function renderCanvas() {
    if (!canvas) return;
    canvas.querySelectorAll(".sec-wrap").forEach((n) => n.remove());

    if (state.sections.length === 0) {
      showEmptyCanvas();
      return;
    }

    // Clear empty state if present
    const inner = canvas.querySelector("div:not(.sec-wrap)");
    if (inner && inner.tagName !== "DIV".constructor) inner.remove();
    // Remove fallback empty state divs
    canvas.querySelectorAll("div").forEach((d) => {
      if (!d.classList.contains("sec-wrap") && !d.id) d.remove();
    });

    state.sections.forEach((sec, si) => {
      const wrap = el(
        "div",
        "sec-wrap" + (state.selSection === sec.id ? " selected" : ""),
        { "data-sec-id": sec.id },
      );

      // Badge
      const badge = el("div", "sec-badge");
      badge.textContent = sec.label || "Section";
      wrap.appendChild(badge);

      // Section click → select
      wrap.addEventListener("click", (e) => {
        if (state.mode === "preview") return;
        if (e.target.closest(".blk-wrap") || e.target.closest(".sec-actions"))
          return;
        selectSec(sec.id);
        e.stopPropagation();
      });

      // Section style
      const s = sec.settings || {};
      const secEl = el("section", "builder-section");
      let style = `background-color:${s.background_color || "#fff"};`;
      if (s.background_image)
        style += `background-image:url('${s.background_image}');background-size:cover;background-position:center;`;
      style += `padding:${s.padding_top || 40}px 0 ${s.padding_bottom || 40}px;`;
      style += `margin:${s.margin_top || 0}px 0 ${s.margin_bottom || 0}px;`;
      secEl.style.cssText = style;

      const cnt = el("div");
      cnt.style.cssText =
        "max-width:1200px;margin:0 auto;padding:0 15px;box-sizing:border-box;";
      const row = el("div", "editor-row");

      // Render blocks
      sec.blocks.forEach((blk, bi) => {
        const w = blk.width || 100;
        const col = el("div", "editor-col");
        col.style.cssText = `flex:0 0 ${w}%;max-width:${w}%;padding:12px;box-sizing:border-box;`;

        const bWrap = el(
          "div",
          "blk-wrap" + (state.selBlock === blk.id ? " selected" : ""),
          { "data-blk-id": blk.id },
        );

        if (state.mode === "edit") {
          bWrap.addEventListener("click", (e) => {
            selectBlk(sec.id, blk.id);
            e.stopPropagation();
          });
        }

        bWrap.innerHTML = renderBlock(blk);

        // Bind editable text
        bWrap.querySelectorAll(".editable").forEach((ed) => {
          ed.addEventListener("input", () => {
            blk.text = ed.innerHTML;
            showSave("saving", "Unsaved");
          });
          ed.addEventListener("blur", snap);
          if (["heading", "button"].includes(blk.type)) {
            ed.addEventListener("keydown", (e) => {
              if (e.key === "Enter") {
                e.preventDefault();
                ed.blur();
              }
            });
          }
        });

        // Bind editable text nodes inside static_html
        bWrap.querySelectorAll(".editable-static-node").forEach((ed) => {
          ed.addEventListener("input", () => {
            const clone = bWrap.cloneNode(true);
            clone.querySelectorAll(".editable-static-node").forEach(node => {
              node.removeAttribute("contenteditable");
              node.classList.remove("editable-static-node");
            });
            clone.querySelectorAll(".blk-actions").forEach(n => n.remove());
            
            const innerDiv = clone.querySelector("div");
            blk.content = innerDiv ? innerDiv.innerHTML : clone.innerHTML;
            showSave("saving", "Unsaved");
            
            const rawTextarea = document.getElementById("p-html");
            if (rawTextarea) rawTextarea.value = blk.content;
          });
          ed.addEventListener("blur", snap);
        });

        // Block controls
        if (state.mode === "edit") {
          const bc = el("div", "blk-actions");
          bc.innerHTML = `
                        <button class="ctrl-btn" title="25%">¼</button>
                        <button class="ctrl-btn" title="33%">⅓</button>
                        <button class="ctrl-btn" title="50%">½</button>
                        <button class="ctrl-btn" title="100%"><i class="fi-rr-square"></i></button>
                        <button class="ctrl-btn danger" title="Delete"><i class="fi-rr-trash"></i></button>`;
          const widths = [25, 33, 50, 100];
          bc.querySelectorAll(".ctrl-btn").forEach((btn, i) => {
            if (i < 4)
              btn.addEventListener("click", (e) => {
                e.stopPropagation();
                snap();
                blk.width = widths[i];
                renderCanvas();
              });
          });
          bc.querySelector(".danger").addEventListener("click", (e) => {
            e.stopPropagation();
            snap();
            sec.blocks.splice(bi, 1);
            state.selBlock = null;
            renderCanvas();
            renderProps();
          });
          bWrap.appendChild(bc);
        }

        col.appendChild(bWrap);
        row.appendChild(col);
      });

      cnt.appendChild(row);
      secEl.appendChild(cnt);
      wrap.appendChild(secEl);

      // Section action controls
      if (state.mode === "edit") {
        const ac = el("div", "sec-actions");
        ac.innerHTML = `
                    <button class="ctrl-btn" title="Move Up"><i class="fi-rr-angle-small-up"></i></button>
                    <button class="ctrl-btn" title="Move Down"><i class="fi-rr-angle-small-down"></i></button>
                    <button class="ctrl-btn" title="Duplicate"><i class="fi-rr-copy"></i></button>
                    <button class="ctrl-btn accent" title="Settings"><i class="fi-rr-settings"></i></button>
                    <button class="ctrl-btn danger" title="Delete"><i class="fi-rr-trash"></i></button>`;

        ac.querySelectorAll(".ctrl-btn")[0].addEventListener("click", (e) => {
          e.stopPropagation();
          if (si > 0) {
            snap();
            [state.sections[si], state.sections[si - 1]] = [
              state.sections[si - 1],
              state.sections[si],
            ];
            renderCanvas();
          }
        });
        ac.querySelectorAll(".ctrl-btn")[1].addEventListener("click", (e) => {
          e.stopPropagation();
          if (si < state.sections.length - 1) {
            snap();
            [state.sections[si], state.sections[si + 1]] = [
              state.sections[si + 1],
              state.sections[si],
            ];
            renderCanvas();
          }
        });
        ac.querySelectorAll(".ctrl-btn")[2].addEventListener("click", (e) => {
          e.stopPropagation();
          snap();
          const cl = JSON.parse(JSON.stringify(sec));
          cl.id = sid();
          cl.blocks.forEach((b) => (b.id = bid()));
          state.sections.splice(si + 1, 0, cl);
          renderCanvas();
        });
        ac.querySelectorAll(".ctrl-btn")[3].addEventListener("click", (e) => {
          e.stopPropagation();
          selectSec(sec.id);
        });
        ac.querySelectorAll(".ctrl-btn")[4].addEventListener("click", (e) => {
          e.stopPropagation();
          swal({
            title: "Delete Section?",
            text: "This will remove this section from the canvas.",
            icon: "warning",
            buttons: ["Cancel", "Delete"],
            dangerMode: true,
          }).then((ok) => {
            if (ok) {
              snap();
              state.sections.splice(si, 1);
              if (state.selSection === sec.id) state.selSection = null;
              renderCanvas();
              renderProps();
            }
          });
        });
        wrap.appendChild(ac);
      }

      canvas.appendChild(wrap);
    });

    if (window.lucide) {
      try {
        window.lucide.createIcons();
      } catch (e) {
        console.error("Lucide icons error:", e);
      }
    }

    // Re-initialize visitor theme micro-interactions for canvas blocks if loaded
    const themeInitFuncs = [
      'initCounters',
      'initBeforeAfterSlider',
      'initSocialCarousel',
      'initTestimonialCarousel',
      'initJourneyProgress',
      'initFAQAccordion',
      'initExperienceLightbox'
    ];
    themeInitFuncs.forEach(funcName => {
      if (typeof window[funcName] === "function") {
        try {
          window[funcName]();
        } catch (e) {
          console.error("Error running visitor theme init function " + funcName + ":", e);
        }
      }
    });
  }

  /* ── Block HTML renderer ─────────────────────────── */
  function renderBlock(b) {
    const edit = state.mode === "edit";
    switch (b.type) {
      case "static_html": {
        if (!b.content) {
          return `<div><p style="color:#aaa;font-size:13px;text-align:center;">Empty HTML block — edit in the Properties panel.</p></div>`;
        }
        if (edit) {
          const tempParser = new DOMParser();
          const tempDoc = tempParser.parseFromString(b.content, "text/html");
          tempDoc.querySelectorAll("h1, h2, h3, h4, h5, h6, p, span, li, td, th, dt, dd").forEach(el => {
            const hasBlockChildren = Array.from(el.children).some(child => 
              !["span", "a", "strong", "em", "i", "b", "u"].includes(child.tagName.toLowerCase())
            );
            if (!hasBlockChildren) {
              el.setAttribute("contenteditable", "true");
              el.classList.add("editable-static-node");
            }
          });
          return `<div>${tempDoc.body.innerHTML}</div>`;
        }
        return `<div>${b.content}</div>`;
      }

      case "heading": {
        const tag = b.tag || "h2";
        const s = `text-align:${b.align || "left"};color:${b.color || "#333"};margin:0;font-family:'Manrope',sans-serif;${b.size ? "font-size:" + b.size + ";" : ""}`;
        return `<${tag} class="editable" contenteditable="${edit}" style="${s}">${b.text || "Heading Text"}</${tag}>`;
      }

      case "rich_text":
      case "paragraph": {
        const s = `text-align:${b.align || "left"};color:${b.color || "#666"};font-size:${b.size || "16px"};line-height:1.7;margin:0;`;
        return `<div class="editable" contenteditable="${edit}" style="${s}">${b.text || "Paragraph text…"}</div>`;
      }

      case "image": {
        const src =
          b.src ||
          "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop&q=60";
        let img = `<img src="${src}" alt="${b.alt || ""}" style="max-width:100%;height:auto;display:inline-block;border-radius:${b.radius || 0}px;">`;
        if (b.link) img = `<a href="${b.link}">${img}</a>`;
        return `<div style="text-align:${b.align || "center"};">${img}</div>`;
      }

      case "button": {
        const s = `display:inline-block;padding:${b.padding_y || 12}px ${b.padding_x || 24}px;background:${b.bg_color || state.themeColor};color:${b.color || "#fff"};border-radius:${b.radius || 6}px;text-decoration:none;font-weight:600;border:none;cursor:pointer;font-family:'Manrope',sans-serif;`;
        return `<div style="text-align:${b.align || "center"};"><span class="editable" contenteditable="${edit}" style="${s}">${b.text || "Click Here"}</span></div>`;
      }

      case "spacer": {
        const h = b.height || 30;
        return edit
          ? `<div style="height:${h}px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.025);border-top:1px dashed #e2e8f0;border-bottom:1px dashed #e2e8f0;font-size:11px;color:#94a3b8;font-family:monospace;">Spacer — ${h}px</div>`
          : `<div style="height:${h}px;"></div>`;
      }

      case "widget":
        return `<div class="widget-preview">
                    <i class="fi-rr-rss" style="font-size:24px;color:${state.themeColor};display:block;margin-bottom:8px;"></i>
                    <strong>Recent Articles</strong>
                    <p style="margin:4px 0 0;font-size:12px;color:#aaa;">Displays ${b.settings?.limit || 3} latest posts on the published page</p>
                </div>`;

      default:
        return `<div style="padding:12px;border:1px dashed #ddd;text-align:center;color:#aaa;font-size:12px;">Unknown block: ${b.type}</div>`;
    }
  }

  /* ── Selection helpers ───────────────────────────── */
  function selectSec(id) {
    if (state.selSection === id && state.selBlock === null && state.selSubElement === null) {
      return;
    }
    
    // Deselect old block
    const oldSelBlock = canvas.querySelector(".blk-wrap.selected");
    if (oldSelBlock) {
      oldSelBlock.classList.remove("selected");
    }
    
    // Deselect old section
    const oldSelSec = canvas.querySelector(".sec-wrap.selected");
    if (oldSelSec) {
      oldSelSec.classList.remove("selected");
    }
 
    state.selSection = id;
    state.selBlock = null;
    state.selSubElement = null;
 
    // Select new section
    const newSelSec = canvas.querySelector(`[data-sec-id="${id}"]`);
    if (newSelSec) {
      newSelSec.classList.add("selected");
    }
 
    renderProps();
  }
 
  function selectBlk(sid, bid) {
    if (state.selSection === sid && state.selBlock === bid && state.selSubElement === null) {
      return; // Already selected
    }
    
    // Deselect old block
    const oldSelBlock = canvas.querySelector(".blk-wrap.selected");
    if (oldSelBlock) {
      oldSelBlock.classList.remove("selected");
    }
    
    // Deselect old section
    const oldSelSec = canvas.querySelector(".sec-wrap.selected");
    if (oldSelSec) {
      oldSelSec.classList.remove("selected");
    }
 
    state.selSection = sid;
    state.selBlock = bid;
    state.selSubElement = null;
 
    // Select new section
    const newSelSec = canvas.querySelector(`[data-sec-id="${sid}"]`);
    if (newSelSec) {
      newSelSec.classList.add("selected");
    }
 
    // Select new block
    const newSelBlock = canvas.querySelector(`[data-blk-id="${bid}"]`);
    if (newSelBlock) {
      newSelBlock.classList.add("selected");
    }
 
    renderProps();
  }
 
  function addSection(sec) {
    state.sections.push(sec);
    state.selSection = sec.id;
    state.selBlock = null;
    renderCanvas();
    renderProps();
    showSave("saving", "Unsaved");
    setTimeout(() => {
      const el = canvas.querySelector(`[data-sec-id="${sec.id}"]`);
      if (el) el.scrollIntoView({ behavior: "smooth", block: "end" });
    }, 80);
  }
 
  /* ── Direct Inline Canvas Editing Helpers ─────────── */
  function selectSubElement(sid, bid, type, node) {
    const bWrap = canvas.querySelector(`[data-blk-id="${bid}"]`);
    if (!bWrap) return;
    
    const selector = type === 'image' ? 'img' : 'a';
    const elements = Array.from(bWrap.querySelectorAll(selector));
    const idx = elements.indexOf(node);
    if (idx === -1) return;
    
    // Check if already selected to prevent resets
    if (state.selSection === sid && state.selBlock === bid && 
        state.selSubElement && state.selSubElement.type === type && state.selSubElement.index === idx) {
      return;
    }
    
    // Deselect old block
    const oldSelBlock = canvas.querySelector(".blk-wrap.selected");
    if (oldSelBlock) {
      oldSelBlock.classList.remove("selected");
    }
    
    // Deselect old section
    const oldSelSec = canvas.querySelector(".sec-wrap.selected");
    if (oldSelSec) {
      oldSelSec.classList.remove("selected");
    }
 
    state.selSection = sid;
    state.selBlock = bid;
    state.selSubElement = {
      type: type,
      index: idx,
      originalSrc: type === 'image' ? node.getAttribute('src') || '' : '',
      originalAlt: type === 'image' ? node.getAttribute('alt') || '' : '',
      originalHref: type === 'link' ? node.getAttribute('href') || '' : '',
      originalText: type === 'link' ? node.innerHTML : ''
    };

    const newSelSec = canvas.querySelector(`[data-sec-id="${sid}"]`);
    if (newSelSec) newSelSec.classList.add("selected");
    
    const newSelBlock = canvas.querySelector(`[data-blk-id="${bid}"]`);
    if (newSelBlock) newSelBlock.classList.add("selected");

    renderProps();
  }

  function renderSubElementLinkProps(sub, blk) {
    const title = document.getElementById("props-title");
    if (title) title.textContent = "Edit Link";

    const bWrap = canvas.querySelector(`[data-blk-id="${blk.id}"]`);
    const anchor = bWrap ? Array.from(bWrap.querySelectorAll("a"))[sub.index] : null;
    const currentHref = anchor ? anchor.getAttribute("href") : sub.originalHref;
    const currentText = anchor ? anchor.innerHTML : sub.originalText;

    propsBody.innerHTML = `
      <div class="fg">
        <label>Link Text</label>
        <input type="text" class="fc" id="sub-ltext" value="${escapeHtml(currentText)}">
      </div>
      <div class="fg">
        <label>Link URL</label>
        <input type="text" class="fc" id="sub-lhref" value="${escapeHtml(currentHref)}" placeholder="https://…">
      </div>
      <div class="divider"></div>
      <div style="display:flex;gap:8px;">
        <button class="fc-btn" id="sub-link-update" style="flex:1;"><i class="fi-rr-check"></i> Update</button>
        <button class="fc-btn secondary" id="sub-link-reset" style="flex:1;"><i class="fi-rr-refresh"></i> Reset</button>
      </div>
    `;

    document.getElementById("sub-link-update").addEventListener("click", () => {
      const textVal = document.getElementById("sub-ltext").value;
      const hrefVal = document.getElementById("sub-lhref").value;
      
      snap();
      
      if (blk.type === "static_html") {
        const tempParser = new DOMParser();
        const tempDoc = tempParser.parseFromString(blk.content || "", "text/html");
        const docAnchors = Array.from(tempDoc.querySelectorAll("a"));
        if (docAnchors[sub.index]) {
          docAnchors[sub.index].setAttribute("href", hrefVal);
          docAnchors[sub.index].innerHTML = textVal;
          blk.content = tempDoc.body.innerHTML;
        }
      } else {
        if (blk.type === "button") {
          blk.text = textVal;
        }
        blk.link = hrefVal;
      }
      
      renderCanvas();
      selectSubElement(state.selSection, blk.id, 'link', Array.from(canvas.querySelector(`[data-blk-id="${blk.id}"]`).querySelectorAll("a"))[sub.index]);
      showSave("saving", "Unsaved");
      swal("Updated!", "Link updated successfully.", "success");
    });

    document.getElementById("sub-link-reset").addEventListener("click", () => {
      document.getElementById("sub-ltext").value = sub.originalText;
      document.getElementById("sub-lhref").value = sub.originalHref;
    });
  }

  function renderSubElementImageProps(sub, blk) {
    const title = document.getElementById("props-title");
    if (title) title.textContent = "Edit Image";

    const bWrap = canvas.querySelector(`[data-blk-id="${blk.id}"]`);
    const imgNode = bWrap ? Array.from(bWrap.querySelectorAll("img"))[sub.index] : null;
    const currentSrc = imgNode ? imgNode.getAttribute("src") : sub.originalSrc;
    const currentAlt = imgNode ? imgNode.getAttribute("alt") : sub.originalAlt;

    propsBody.innerHTML = `
      <div class="fg">
        <label>Image Select / Upload</label>
        <div class="image-editor-section">
          <div class="image-preview-wrapper" id="sub-preview-wrapper" style="${currentSrc ? "" : "display:none;"}">
            <img id="sub-preview-img" src="${currentSrc || ""}" alt="Preview">
            <div class="image-preview-overlay" id="sub-change-overlay">
              <i class="fi-rr-edit"></i> Change Image
            </div>
          </div>
          <div class="image-dropzone" id="sub-image-dropzone" style="${currentSrc ? "display:none;" : ""}">
            <i class="fi-rr-cloud-upload"></i>
            <p>Drag & drop image here or <span>browse</span></p>
            <input type="file" id="sub-file-input" style="display:none;" accept="image/*">
          </div>
        </div>
      </div>
      <div class="fg">
        <label>Image Source URL</label>
        <input type="text" class="fc" id="sub-imgsrc" value="${escapeHtml(currentSrc)}" style="width: 100%;">
      </div>
      <div class="fg">
        <label>Alt Text</label>
        <input type="text" class="fc" id="sub-imgalt" value="${escapeHtml(currentAlt)}">
      </div>
      <div class="divider"></div>
      <div style="display:flex;gap:8px;">
        <button class="fc-btn" id="sub-image-update" style="flex:1;"><i class="fi-rr-check"></i> Update</button>
        <button class="fc-btn secondary" id="sub-image-reset" style="flex:1;"><i class="fi-rr-refresh"></i> Reset</button>
      </div>
    `;

    const dropzone = document.getElementById("sub-image-dropzone");
    const fileInput = document.getElementById("sub-file-input");
    const previewWrapper = document.getElementById("sub-preview-wrapper");
    const changeOverlay = document.getElementById("sub-change-overlay");

    if (changeOverlay && dropzone && previewWrapper) {
      changeOverlay.addEventListener("click", (e) => {
        e.stopPropagation();
        previewWrapper.style.display = "none";
        dropzone.style.display = "";
      });
    }

    if (dropzone && fileInput) {
      dropzone.addEventListener("click", (e) => {
        e.stopPropagation();
        fileInput.click();
      });

      dropzone.addEventListener("dragover", (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add("dragover");
      });

      dropzone.addEventListener("dragleave", (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove("dragover");
      });

      dropzone.addEventListener("drop", (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove("dragover");
        if (e.dataTransfer.files.length > 0) {
          handleSubImageSelect(e.dataTransfer.files[0]);
        }
      });

      fileInput.addEventListener("change", (e) => {
        e.stopPropagation();
        if (fileInput.files.length > 0) {
          handleSubImageSelect(fileInput.files[0]);
        }
      });
    }

    function handleSubImageSelect(file) {
      if (!file.type.startsWith("image/")) {
        swal("Invalid File", "Please select an image file.", "error");
        return;
      }

      state.tempSubImageFile = file;

      const localUrl = URL.createObjectURL(file);
      if (previewWrapper) {
        const previewImg = document.getElementById("sub-preview-img");
        if (previewImg) previewImg.src = localUrl;
        previewWrapper.style.display = "";
        if (dropzone) dropzone.style.display = "none";
      }
    }

    function revertPreview() {
      const loader = previewWrapper?.querySelector(".image-upload-loader");
      if (loader) loader.remove();

      const srcVal = document.getElementById("sub-imgsrc").value;
      if (srcVal) {
        const previewImg = document.getElementById("sub-preview-img");
        if (previewImg) previewImg.src = srcVal;
        if (previewWrapper) previewWrapper.style.display = "";
        if (dropzone) dropzone.style.display = "none";
      } else {
        if (previewWrapper) previewWrapper.style.display = "none";
        if (dropzone) dropzone.style.display = "";
      }
    }

    document.getElementById("sub-image-update").addEventListener("click", () => {
      const altVal = document.getElementById("sub-imgalt").value;

      if (state.tempSubImageFile) {
        if (previewWrapper) {
          let loader = previewWrapper.querySelector(".image-upload-loader");
          if (!loader) {
            loader = document.createElement("div");
            loader.className = "image-upload-loader";
            loader.innerHTML = '<i class="fi-rr-spinner"></i><span>Uploading...</span>';
            previewWrapper.appendChild(loader);
          }
        }

        showSave("saving", "Uploading...");
        const formData = new FormData();
        formData.append("file", state.tempSubImageFile);

        fetch("/api/builder/upload/", {
          method: "POST",
          body: formData
        })
          .then((r) => r.json())
          .then((data) => {
            if (data.status === 200) {
              const srcVal = data.url;
              document.getElementById("sub-imgsrc").value = srcVal;
              state.tempSubImageFile = null;
              const loader = previewWrapper?.querySelector(".image-upload-loader");
              if (loader) loader.remove();
              
              snap();
              
              if (blk.type === "static_html") {
                const tempParser = new DOMParser();
                const tempDoc = tempParser.parseFromString(blk.content || "", "text/html");
                const docImages = Array.from(tempDoc.querySelectorAll("img"));
                if (docImages[sub.index]) {
                  docImages[sub.index].setAttribute("src", srcVal);
                  docImages[sub.index].setAttribute("alt", altVal);
                  blk.content = tempDoc.body.innerHTML;
                }
              } else {
                blk.src = srcVal;
                blk.alt = altVal;
              }

              renderCanvas();
              selectSubElement(state.selSection, blk.id, 'image', Array.from(canvas.querySelector(`[data-blk-id="${blk.id}"]`).querySelectorAll("img"))[sub.index]);
              showSave("saved", "Uploaded");
              swal("Updated!", "Image uploaded and updated successfully.", "success");
            } else {
              showSave("error", "Upload failed");
              swal("Upload Error", data.msg || "Failed to upload image.", "error");
              revertPreview();
            }
          })
          .catch((err) => {
            console.error(err);
            showSave("error", "Upload error");
            swal("Upload Error", "Connection to upload API failed.", "error");
            revertPreview();
          });
      } else {
        const srcVal = document.getElementById("sub-imgsrc").value;
        snap();
        
        if (blk.type === "static_html") {
          const tempParser = new DOMParser();
          const tempDoc = tempParser.parseFromString(blk.content || "", "text/html");
          const docImages = Array.from(tempDoc.querySelectorAll("img"));
          if (docImages[sub.index]) {
            docImages[sub.index].setAttribute("src", srcVal);
            docImages[sub.index].setAttribute("alt", altVal);
            blk.content = tempDoc.body.innerHTML;
          }
        } else {
          blk.src = srcVal;
          blk.alt = altVal;
        }

        renderCanvas();
        selectSubElement(state.selSection, blk.id, 'image', Array.from(canvas.querySelector(`[data-blk-id="${blk.id}"]`).querySelectorAll("img"))[sub.index]);
        showSave("saving", "Unsaved");
        swal("Updated!", "Image updated successfully.", "success");
      }
    });

    document.getElementById("sub-image-reset").addEventListener("click", () => {
      state.tempSubImageFile = null;
      document.getElementById("sub-imgsrc").value = sub.originalSrc;
      document.getElementById("sub-imgalt").value = sub.originalAlt;
      
      // Reset live preview inside properties panel too
      const previewImg = document.getElementById("sub-preview-img");
      if (sub.originalSrc) {
        if (previewImg) previewImg.src = sub.originalSrc;
        if (previewWrapper) previewWrapper.style.display = "";
        if (dropzone) dropzone.style.display = "none";
      } else {
        if (previewWrapper) previewWrapper.style.display = "none";
        if (dropzone) dropzone.style.display = "";
      }
    });
  }

  function escapeHtml(str) {
    if (!str) return "";
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  /* ── Preview toggle ──────────────────────────────── */
  function togglePreview() {
    state.mode = state.mode === "edit" ? "preview" : "edit";
    state.selSection = state.selBlock = null;
    const btn = document.getElementById("btn-preview");
    const pl = document.getElementById("panel-l");
    const pr = document.getElementById("panel-r");
    const builder = document.getElementById("builder");
    if (state.mode === "preview") {
      btn.innerHTML = '<i class="fi-rr-pencil"></i> Edit Mode';
      btn.classList.add("on");
      if (pl) pl.style.display = "none";
      if (pr) pr.style.display = "none";
      if (builder) builder.classList.add("preview-mode");
    } else {
      btn.innerHTML = '<i class="fi-rr-eye"></i> Preview';
      btn.classList.remove("on");
      if (pl) pl.style.display = "";
      if (pr) pr.style.display = "";
      if (builder) builder.classList.remove("preview-mode");
    }
    renderCanvas();
    renderProps();
  }

  /* ── Properties panel ────────────────────────────── */
  function renderProps() {
    if (!propsBody) return;
    const title = document.getElementById("props-title");

    if (state.mode === "preview") {
      if (title) title.textContent = "Preview";
      propsBody.innerHTML = `<div class="empty-panel"><i class="fi-rr-eye"></i><p>Preview mode is active. Return to Edit Mode to change properties.</p></div>`;
      return;
    }

    if (state.selSubElement && state.selBlock) {
      const sec = state.sections.find((s) => s.id === state.selSection);
      const blk = sec?.blocks.find((b) => b.id === state.selBlock);
      if (blk) {
        if (state.selSubElement.type === "link") {
          renderSubElementLinkProps(state.selSubElement, blk);
          return;
        } else if (state.selSubElement.type === "image") {
          renderSubElementImageProps(state.selSubElement, blk);
          return;
        }
      }
    }

    if (state.selBlock) {
      renderBlockProps();
      return;
    }
    if (state.selSection) {
      renderSecProps();
      return;
    }

    if (title) title.textContent = "Properties";
    propsBody.innerHTML = `<div class="empty-panel"><i class="fi-rr-cursor"></i><p>Click any <strong>section</strong> or <strong>block</strong> on the canvas to edit its properties here.</p></div>`;
  }

  /* ── Block props ─────────────────────────────────── */
  function renderBlockProps() {
    const sec = state.sections.find((s) => s.id === state.selSection);
    const blk = sec?.blocks.find((b) => b.id === state.selBlock);
    if (!blk) {
      state.selBlock = null;
      renderProps();
      return;
    }

    const title = document.getElementById("props-title");
    if (title) title.textContent = "Block";

    let h = `
        <div class="fg"><label>Block Type</label>
            <input class="fc" value="${blk.type.replace(/_/g, " ").toUpperCase()}" readonly style="opacity:.5;cursor:default;"></div>
        <div class="fg"><label>Width</label>
            <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
        </div><div class="divider"></div>`;

    if (["heading", "paragraph", "rich_text"].includes(blk.type)) {
      h += `
            <div class="fg"><label>Text Align</label>
                <select class="fc" id="p-align">${["left", "center", "right", "justify"].map((v) => `<option value="${v}" ${blk.align === v ? "selected" : ""}>${v[0].toUpperCase() + v.slice(1)}</option>`).join("")}</select>
            </div>
            <div class="fg-row">
                <div><label>Text Color</label><input type="color" class="fc" id="p-color" value="${blk.color || "#333"}"></div>
                <div><label>Font Size</label><input type="text" class="fc" id="p-size" value="${blk.size || ""}" placeholder="18px"></div>
            </div>`;
      if (blk.type === "heading") {
        h += `<div class="fg"><label>Heading Tag</label><select class="fc" id="p-tag">${["h1", "h2", "h3", "h4", "h5", "h6"].map((v) => `<option value="${v}" ${blk.tag === v ? "selected" : ""}>${v.toUpperCase()}</option>`).join("")}</select></div>`;
      }
    }

    if (blk.type === "image") {
      h += `
            <div class="fg">
              <label>Image Select / Upload</label>
              <div class="image-editor-section">
                <div class="image-preview-wrapper" id="p-preview-wrapper" style="${blk.src ? "" : "display:none;"}">
                  <img id="p-preview-img" src="${blk.src || ""}" alt="Preview">
                  <div class="image-preview-overlay" id="p-change-overlay">
                    <i class="fi-rr-edit"></i> Change Image
                  </div>
                </div>
                <div class="image-dropzone" id="p-image-dropzone" style="${blk.src ? "display:none;" : ""}">
                  <i class="fi-rr-cloud-upload"></i>
                  <p>Drag & drop image here or <span>browse</span></p>
                  <input type="file" id="p-file-input" style="display:none;" accept="image/*">
                </div>
              </div>
            </div>
            <div class="fg">
              <label>Image Source URL</label>
              <input type="text" class="fc" id="p-src" value="${blk.src || ""}" placeholder="https://…">
            </div>
            <div class="fg"><label>Alt Text</label><input type="text" class="fc" id="p-alt" value="${blk.alt || ""}"></div>
            <div class="fg-row">
                <div><label>Align</label><select class="fc" id="p-ialign">${["left", "center", "right"].map((v) => `<option value="${v}" ${blk.align === v ? "selected" : ""}>${v[0].toUpperCase() + v.slice(1)}</option>`).join("")}</select></div>
                <div><label>Radius (px)</label><input type="number" class="fc" id="p-irad" value="${blk.radius || 0}"></div>
            </div>
            <div class="fg"><label>Link URL</label><input type="text" class="fc" id="p-ilink" value="${blk.link || ""}" placeholder="https://…"></div>
            <div class="divider"></div>
            <div style="display:flex;gap:8px;">
              <button class="fc-btn" id="p-image-update" style="flex:1;"><i class="fi-rr-check"></i> Update</button>
              <button class="fc-btn secondary" id="p-image-reset" style="flex:1;"><i class="fi-rr-refresh"></i> Reset</button>
            </div>`;
    }

    if (blk.type === "button") {
      h += `
            <div class="fg"><label>Button Text</label><input type="text" class="fc" id="p-btxt" value="${blk.text || ""}"></div>
            <div class="fg"><label>Link URL</label><input type="text" class="fc" id="p-blink" value="${blk.link || "#"}"></div>
            <div class="fg"><label>Align</label><select class="fc" id="p-balign">${["left", "center", "right"].map((v) => `<option value="${v}" ${blk.align === v ? "selected" : ""}>${v[0].toUpperCase() + v.slice(1)}</option>`).join("")}</select></div>
            <div class="fg-row">
                <div><label>BG Color</label><input type="color" class="fc" id="p-bbg" value="${blk.bg_color || state.themeColor}"></div>
                <div><label>Text Color</label><input type="color" class="fc" id="p-bclr" value="${blk.color || "#fff"}"></div>
            </div>
            <div class="fg-row">
                <div><label>Radius (px)</label><input type="number" class="fc" id="p-brad" value="${blk.radius || 6}"></div>
                <div><label>Pad X (px)</label><input type="number" class="fc" id="p-bpx" value="${blk.padding_x || 24}"></div>
            </div>
            <div class="fg"><label>Pad Y (px)</label><input type="number" class="fc" id="p-bpy" value="${blk.padding_y || 12}"></div>`;
    }

    if (blk.type === "spacer") {
      h += `<div class="fg"><label>Height (px)</label><input type="number" class="fc" id="p-sh" value="${blk.height || 40}"></div>`;
    }

    if (blk.type === "static_html") {
      h += `<div class="fg"><label>HTML Code</label><textarea class="fc" id="p-html" rows="14" style="height:280px;">${blk.content || ""}</textarea></div>`;
    }

    if (blk.type === "widget" && blk.widget_type === "recent_articles") {
      h += `<div class="fg"><label>Articles Limit</label><input type="number" class="fc" id="p-wlim" min="1" max="20" value="${blk.settings?.limit || 3}"></div>`;
    }

    propsBody.innerHTML = h;

    // Bind
    bind("p-w", "change", (v) => {
      snap();
      blk.width = parseInt(v);
      renderCanvas();
    });
    if (["heading", "paragraph", "rich_text"].includes(blk.type)) {
      bind("p-align", "change", (v) => {
        blk.align = v;
        renderCanvas();
      });
      bind("p-color", "input", (v) => {
        blk.color = v;
        renderCanvas();
      });
      bind("p-size", "input", (v) => {
        blk.size = v;
        renderCanvas();
      });
      if (blk.type === "heading")
        bind("p-tag", "change", (v) => {
          snap();
          blk.tag = v;
          renderCanvas();
        });
    }
    if (blk.type === "image") {
      const dropzone = document.getElementById("p-image-dropzone");
      const fileInput = document.getElementById("p-file-input");
      const previewWrapper = document.getElementById("p-preview-wrapper");
      const changeOverlay = document.getElementById("p-change-overlay");

      if (changeOverlay && dropzone && previewWrapper) {
        changeOverlay.addEventListener("click", (e) => {
          e.stopPropagation();
          previewWrapper.style.display = "none";
          dropzone.style.display = "";
        });
      }

      if (dropzone && fileInput) {
        dropzone.addEventListener("click", (e) => {
          e.stopPropagation();
          fileInput.click();
        });

        dropzone.addEventListener("dragover", (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.add("dragover");
        });

        dropzone.addEventListener("dragleave", (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.remove("dragover");
        });

        dropzone.addEventListener("drop", (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.remove("dragover");
          if (e.dataTransfer.files.length > 0) {
            handleBlockImageSelect(e.dataTransfer.files[0]);
          }
        });

        fileInput.addEventListener("change", (e) => {
          e.stopPropagation();
          if (fileInput.files.length > 0) {
            handleBlockImageSelect(fileInput.files[0]);
          }
        });
      }

      function handleBlockImageSelect(file) {
        if (!file.type.startsWith("image/")) {
          swal("Invalid File", "Please select an image file.", "error");
          return;
        }

        state.tempImageFile = file;

        const localUrl = URL.createObjectURL(file);
        if (previewWrapper) {
          const previewImg = document.getElementById("p-preview-img");
          if (previewImg) previewImg.src = localUrl;
          previewWrapper.style.display = "";
          if (dropzone) dropzone.style.display = "none";
        }
      }

      function revertPreview() {
        const loader = previewWrapper?.querySelector(".image-upload-loader");
        if (loader) loader.remove();

        if (blk.src) {
          const previewImg = document.getElementById("p-preview-img");
          if (previewImg) previewImg.src = blk.src;
          if (previewWrapper) previewWrapper.style.display = "";
          if (dropzone) dropzone.style.display = "none";
        } else {
          if (previewWrapper) previewWrapper.style.display = "none";
          if (dropzone) dropzone.style.display = "";
        }
      }

      document.getElementById("p-image-update").addEventListener("click", () => {
        const altVal = document.getElementById("p-alt").value;
        const alignVal = document.getElementById("p-ialign").value;
        const radVal = parseInt(document.getElementById("p-irad").value) || 0;
        const linkVal = document.getElementById("p-ilink").value;

        if (state.tempImageFile) {
          if (previewWrapper) {
            let loader = previewWrapper.querySelector(".image-upload-loader");
            if (!loader) {
              loader = document.createElement("div");
              loader.className = "image-upload-loader";
              loader.innerHTML = '<i class="fi-rr-spinner"></i><span>Uploading...</span>';
              previewWrapper.appendChild(loader);
            }
          }

          showSave("saving", "Uploading...");
          const formData = new FormData();
          formData.append("file", state.tempImageFile);

          fetch("/api/builder/upload/", {
            method: "POST",
            body: formData
          })
            .then((r) => r.json())
            .then((data) => {
              if (data.status === 200) {
                snap();
                blk.src = data.url;
                blk.alt = altVal;
                blk.align = alignVal;
                blk.radius = radVal;
                blk.link = linkVal;
                state.tempImageFile = null;

                renderCanvas();
                renderProps();
                showSave("saved", "Uploaded");
                swal("Updated!", "Image and properties updated successfully.", "success");
              } else {
                showSave("error", "Upload failed");
                swal("Upload Error", data.msg || "Failed to upload image.", "error");
                revertPreview();
              }
            })
            .catch((err) => {
              console.error(err);
              showSave("error", "Upload error");
              swal("Upload Error", "Connection to upload API failed.", "error");
              revertPreview();
            });
        } else {
          snap();
          blk.src = document.getElementById("p-src").value;
          blk.alt = altVal;
          blk.align = alignVal;
          blk.radius = radVal;
          blk.link = linkVal;

          renderCanvas();
          renderProps();
          showSave("saving", "Unsaved");
          swal("Updated!", "Image properties updated successfully.", "success");
        }
      });

      document.getElementById("p-image-reset").addEventListener("click", () => {
        state.tempImageFile = null;
        document.getElementById("p-src").value = blk.src || "";
        document.getElementById("p-alt").value = blk.alt || "";
        document.getElementById("p-ialign").value = blk.align || "left";
        document.getElementById("p-irad").value = blk.radius || 0;
        document.getElementById("p-ilink").value = blk.link || "";
        revertPreview();
      });
    }
    if (blk.type === "button") {
      bind("p-btxt", "input", (v) => {
        blk.text = v;
        renderCanvas();
      });
      bind("p-blink", "input", (v) => {
        blk.link = v;
      });
      bind("p-balign", "change", (v) => {
        blk.align = v;
        renderCanvas();
      });
      bind("p-bbg", "input", (v) => {
        blk.bg_color = v;
        renderCanvas();
      });
      bind("p-bclr", "input", (v) => {
        blk.color = v;
        renderCanvas();
      });
      bind("p-brad", "input", (v) => {
        blk.radius = parseInt(v) || 0;
        renderCanvas();
      });
      bind("p-bpx", "input", (v) => {
        blk.padding_x = parseInt(v) || 0;
        renderCanvas();
      });
      bind("p-bpy", "input", (v) => {
        blk.padding_y = parseInt(v) || 0;
        renderCanvas();
      });
    }
    if (blk.type === "spacer")
      bind("p-sh", "input", (v) => {
        blk.height = parseInt(v) || 0;
        renderCanvas();
      });
    if (blk.type === "static_html") {
      bind("p-html", "input", (v) => {
        blk.content = v;
        renderCanvas();
      });
    }
    if (blk.type === "widget" && blk.widget_type === "recent_articles") {
      bind("p-wlim", "input", (v) => {
        blk.settings.limit = parseInt(v) || 3;
        renderCanvas();
      });
    }
  }

  /* ── Section props ───────────────────────────────── */
  function renderSecProps() {
    const sec = state.sections.find((s) => s.id === state.selSection);
    if (!sec) {
      state.selSection = null;
      renderProps();
      return;
    }

    const title = document.getElementById("props-title");
    if (title) title.textContent = "Section";

    const st = sec.settings || {};
    propsBody.innerHTML = `
        <div class="fg"><label>Section Name</label><input type="text" class="fc" id="p-slbl" value="${sec.label || "Section"}"></div>
        <div class="divider"></div>

        <div class="section-title-label">Background</div>
        <div class="fg-row">
            <div><label>Color</label><input type="color" class="fc" id="p-sbg" value="${st.background_color || "#ffffff"}"></div>
            <div><label>Image URL</label><input type="text" class="fc" id="p-sbgimg" value="${st.background_image || ""}" placeholder="https://…"></div>
        </div>

        <div class="section-title-label">Padding</div>
        <div class="fg-row">
            <div><label>Top (px)</label><input type="number" class="fc" id="p-spt" value="${st.padding_top || 40}"></div>
            <div><label>Bottom (px)</label><input type="number" class="fc" id="p-spb" value="${st.padding_bottom || 40}"></div>
        </div>

        <div class="section-title-label">Margin</div>
        <div class="fg-row">
            <div><label>Top (px)</label><input type="number" class="fc" id="p-smt" value="${st.margin_top || 0}"></div>
            <div><label>Bottom (px)</label><input type="number" class="fc" id="p-smb" value="${st.margin_bottom || 0}"></div>
        </div>

        <div class="divider"></div>
        <div class="section-title-label">Add Block to Section</div>
        <button class="add-blk-btn" id="p-add-blk"><i class="fi-rr-plus"></i> Add Block</button>
        <div class="blk-picker" id="blk-picker">
            <div class="blk-opt" data-btype="heading"><i class="fi-rr-text-size"></i> Heading</div>
            <div class="blk-opt" data-btype="paragraph"><i class="fi-rr-align-left"></i> Paragraph</div>
            <div class="blk-opt" data-btype="button"><i class="fi-rr-mouse-pointer"></i> Button</div>
            <div class="blk-opt" data-btype="image"><i class="fi-rr-picture"></i> Image</div>
            <div class="blk-opt" data-btype="spacer"><i class="fi-rr-resize"></i> Spacer</div>
            <div class="blk-opt" data-btype="static_html"><i class="fi-rr-code"></i> Custom HTML</div>
        </div>`;

    bind("p-slbl", "input", (v) => {
      sec.label = v;
      renderCanvas();
    });
    bind("p-sbg", "input", (v) => {
      sec.settings.background_color = v;
      renderCanvas();
    });
    bind("p-sbgimg", "input", (v) => {
      sec.settings.background_image = v;
      renderCanvas();
    });
    bind("p-spt", "input", (v) => {
      sec.settings.padding_top = parseInt(v) || 0;
      renderCanvas();
    });
    bind("p-spb", "input", (v) => {
      sec.settings.padding_bottom = parseInt(v) || 0;
      renderCanvas();
    });
    bind("p-smt", "input", (v) => {
      sec.settings.margin_top = parseInt(v) || 0;
      renderCanvas();
    });
    bind("p-smb", "input", (v) => {
      sec.settings.margin_bottom = parseInt(v) || 0;
      renderCanvas();
    });

    const addBtn = document.getElementById("p-add-blk");
    const picker = document.getElementById("blk-picker");
    addBtn.addEventListener("click", () => {
      picker.classList.toggle("open");
      addBtn.querySelector("i").className = picker.classList.contains("open")
        ? "fi-rr-minus"
        : "fi-rr-plus";
    });
    picker.querySelectorAll(".blk-opt").forEach((opt) => {
      opt.addEventListener("click", () => {
        snap();
        const nb = newBlock(opt.dataset.btype);
        sec.blocks.push(nb);
        picker.classList.remove("open");
        selectBlk(sec.id, nb.id);
      });
    });
  }

  function newBlock(type) {
    switch (type) {
      case "heading":
        return {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "New Heading",
          align: "left",
          color: "#1a1a2e",
          size: "28px",
          width: 100,
        };
      case "paragraph":
        return {
          id: bid(),
          type: "paragraph",
          text: "New paragraph. Edit your content here.",
          align: "left",
          color: "#555",
          size: "16px",
          width: 100,
        };
      case "button":
        return {
          id: bid(),
          type: "button",
          text: "Learn More",
          link: "#",
          align: "left",
          bg_color: state.themeColor,
          color: "#fff",
          radius: "6",
          padding_y: "11",
          padding_x: "22",
          width: 100,
        };
      case "image":
        return {
          id: bid(),
          type: "image",
          src: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop&q=60",
          alt: "",
          align: "center",
          radius: "6",
          link: "",
          width: 100,
        };
      case "spacer":
        return { id: bid(), type: "spacer", height: "30", width: 100 };
      case "static_html":
        return {
          id: bid(),
          type: "static_html",
          content: "<p>Write your custom HTML here…</p>",
          width: 100,
        };
      default:
        return { id: bid(), type: "static_html", content: "", width: 100 };
    }
  }

  /* ── Utility: bind & el ──────────────────────────── */
  function bind(id, ev, cb) {
    const e = document.getElementById(id);
    if (e) e.addEventListener(ev, () => cb(e.value));
  }

  function el(tag, cls, attrs) {
    const e = document.createElement(tag);
    if (cls) e.className = cls;
    if (attrs) Object.entries(attrs).forEach(([k, v]) => e.setAttribute(k, v));
    return e;
  }
})();
