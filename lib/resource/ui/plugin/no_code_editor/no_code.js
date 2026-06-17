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
    activePropsTab: "themes",
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
          text: "Write title here",
          align: "center",
          color: "#1a1a2e",
          size: "48px",
          width: 100,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Write paragraph here. Add predefined sections with zero coding. Customise text, colours, alignments and buttons — all inline.",
          align: "center",
          color: "#666666",
          size: "18px",
          width: 100,
        },
        {
          id: bid(),
          type: "button",
          text: "Button Text",
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
          text: "Write heading here",
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
          text: "Write paragraph here. This is a two-column layout. The heading and text sit on one side while the image floats on the other. Drag to reorder or resize blocks.",
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
          text: "Write heading here",
          align: "center",
          color: "#1a1a2e",
          size: "36px",
          width: 100,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h3",
          text: "⚡ Write title here",
          align: "center",
          color: "#1a1a2e",
          size: "20px",
          width: 33,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h3",
          text: "🎨 Write title here",
          align: "center",
          color: "#1a1a2e",
          size: "20px",
          width: 33,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h3",
          text: "📱 Write title here",
          align: "center",
          color: "#1a1a2e",
          size: "20px",
          width: 33,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Write paragraph here. Pages render extremely fast on all viewports.",
          align: "center",
          color: "#666",
          size: "15px",
          width: 33,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Write paragraph here. Gradients, micro-animations, and modern styling.",
          align: "center",
          color: "#666",
          size: "15px",
          width: 33,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Write paragraph here. Adapts seamlessly to tablet, phone and desktop.",
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
          text: "Write heading here",
          align: "center",
          color: "#1a1a2e",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "static_html",
          width: 100,
          content: `<div style="max-width:720px;margin:0 auto;" class="faq-container">
  <details class="faq-item" style="background:#fff;padding:18px 20px;border-radius:10px;margin-bottom:10px;border:1px solid #e1e1e6;cursor:pointer;">
    <summary class="faq-question" style="font-size:16px;font-weight:600;color:#1a1a2e;list-style:none;">Write question here</summary>
    <p class="faq-answer" style="margin:10px 0 0;font-size:14px;color:#666;line-height:1.7;">Write answer here. Customize all contents and styles inline — zero coding required.</p>
  </details>
  <details class="faq-item" style="background:#fff;padding:18px 20px;border-radius:10px;margin-bottom:10px;border:1px solid #e1e1e6;cursor:pointer;">
    <summary class="faq-question" style="font-size:16px;font-weight:600;color:#1a1a2e;list-style:none;">Write question here</summary>
    <p class="faq-answer" style="margin:10px 0 0;font-size:14px;color:#666;line-height:1.7;">Write answer here. Legacy HTML is loaded as a Custom HTML block so nothing is ever lost.</p>
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
          text: "Write heading here",
          align: "center",
          color: "#1a1a2e",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "static_html",
          width: 100,
          content: `<div style="max-width:820px;margin:20px auto 0;display:flex;gap:18px;flex-wrap:wrap;" class="testimonials-container">
  <div class="testimonial-card" style="flex:1;min-width:240px;background:#f8f9fa;padding:22px;border-radius:12px;border-left:4px solid ${state.themeColor};">
    <p class="testimonial-quote" style="font-style:italic;font-size:15px;color:#444;line-height:1.7;margin:0 0 12px;">"Write testimonial quote here. Premium results out of the box."</p>
    <strong class="testimonial-author" style="font-size:13px;color:#222;">— Write author here</strong>
  </div>
  <div class="testimonial-card" style="flex:1;min-width:240px;background:#f8f9fa;padding:22px;border-radius:12px;border-left:4px solid ${state.themeColor};">
    <p class="testimonial-quote" style="font-style:italic;font-size:15px;color:#444;line-height:1.7;margin:0 0 12px;">"Write testimonial quote here. My team loves how easy it is to update page layouts."</p>
    <strong class="testimonial-author" style="font-size:13px;color:#222;">— Write author here</strong>
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
          text: "Write heading here",
          align: "center",
          color: "#ffffff",
          size: "36px",
          width: 100,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Write paragraph here. Join thousands of businesses who trust us for exceptional results.",
          align: "center",
          color: "rgba(255,255,255,0.85)",
          size: "17px",
          width: 100,
        },
        {
          id: bid(),
          type: "button",
          text: "Button Text",
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
          text: "Write heading here",
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
  <h3 style="margin:0 0 8px;">Write heading here</h3>
  <p style="margin:0;font-size:14px;opacity:.9;">Write paragraph here.</p>
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
    why_choose_us: () => ({
      id: sid(),
      label: "Why Choose Us",
      type: "why_choose_us_section",
      settings: {
        background_color: "#f9fafb",
        background_image: "",
        padding_top: "60",
        padding_bottom: "60",
        margin_top: "0",
        margin_bottom: "0",
      },
      blocks: [
        {
          id: bid(),
          type: "image",
          src: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&auto=format&fit=crop&q=60",
          alt: "Team",
          align: "center",
          radius: "12",
          link: "",
          width: 50,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h2",
          text: "Write heading here",
          align: "left",
          color: "#111827",
          size: "32px",
          width: 50,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Write paragraph here. We offer the most advanced treatments with highly personalized care and state-of-the-art technology.",
          align: "left",
          color: "#4b5563",
          size: "16px",
          width: 50,
        },
        {
          id: bid(),
          type: "static_html",
          width: 50,
          content: `<div style="display:flex;flex-direction:column;gap:18px;margin-top:20px;" class="why-container">
  <div class="why-step" style="display:flex;align-items:center;gap:12px;">
    <div style="width:36px;height:36px;border-radius:50%;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:16px;">
      <i class="why-step-icon fi-rr-badge-check"></i>
    </div>
    <div style="flex:1;">
      <h5 class="why-step-title" style="margin:0;font-size:15px;font-weight:600;color:#111827;">Write benefit highlight here</h5>
    </div>
  </div>
  <div class="why-step" style="display:flex;align-items:center;gap:12px;">
    <div style="width:36px;height:36px;border-radius:50%;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:16px;">
      <i class="why-step-icon fi-rr-time-fast"></i>
    </div>
    <div style="flex:1;">
      <h5 class="why-step-title" style="margin:0;font-size:15px;font-weight:600;color:#111827;">Write benefit highlight here</h5>
    </div>
  </div>
  <div class="why-step" style="display:flex;align-items:center;gap:12px;">
    <div style="width:36px;height:36px;border-radius:50%;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:16px;">
      <i class="why-step-icon fi-rr-heart"></i>
    </div>
    <div style="flex:1;">
      <h5 class="why-step-title" style="margin:0;font-size:15px;font-weight:600;color:#111827;">Write benefit highlight here</h5>
    </div>
  </div>
</div>`,
        }
      ],
    }),
    about_doctor: () => ({
      id: sid(),
      label: "About Doctor",
      type: "about_doctor_section",
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
          text: "Write heading here",
          align: "center",
          color: "#111827",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "image",
          src: "https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=600&auto=format&fit=crop&q=60",
          alt: "Doctor Photo",
          align: "center",
          radius: "12",
          link: "",
          width: 40,
        },
        {
          id: bid(),
          type: "heading",
          tag: "h3",
          text: "Write subheading here",
          align: "left",
          color: "#111827",
          size: "22px",
          width: 60,
        },
        {
          id: bid(),
          type: "paragraph",
          text: "Write paragraph here. Write doctor profile or background bio detailing clinical achievements and cosmetic specialities.",
          align: "left",
          color: "#4b5563",
          size: "16px",
          width: 60,
        },
        {
          id: bid(),
          type: "static_html",
          width: 60,
          content: `<div style="margin-top:20px;" class="credentials-container">
  <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
    <li class="credential-item" style="font-size:14px;color:#4b5563;display:flex;align-items:center;gap:8px;"><i class="fi-rr-graduation-cap" style="color:var(--accent)"></i> Write credential here</li>
    <li class="credential-item" style="font-size:14px;color:#4b5563;display:flex;align-items:center;gap:8px;"><i class="fi-rr-award" style="color:var(--accent)"></i> Write credential here</li>
    <li class="credential-item" style="font-size:14px;color:#4b5563;display:flex;align-items:center;gap:8px;"><i class="fi-rr-briefcase" style="color:var(--accent)"></i> Write credential here</li>
  </ul>
</div>`,
        }
      ],
    }),
    services_list: () => ({
      id: sid(),
      label: "Services Grid",
      type: "services_section",
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
          text: "Write heading here",
          align: "center",
          color: "#111827",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "static_html",
          width: 100,
          content: `<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;margin-top:20px;" class="services-container">
  <div class="service-item" style="background:#f9fafb;padding:24px;border-radius:10px;border:1px solid #e5e7eb;text-align:center;transition:var(--trans);">
    <i class="fi-rr-sparkles" style="font-size:28px;color:var(--accent);display:inline-block;margin-bottom:12px;"></i>
    <h4 style="margin:0 0 8px;font-size:16px;font-weight:600;color:#111827;" class="service-title">Write service title here</h4>
    <p style="margin:0 0 14px;font-size:13px;color:#6b7280;line-height:1.6;" class="service-desc">Write service description here. Customize in properties.</p>
    <a href="#" style="font-size:12px;font-weight:600;color:var(--accent);text-decoration:none;" class="service-link">Learn More &rarr;</a>
  </div>
  <div class="service-item" style="background:#f9fafb;padding:24px;border-radius:10px;border:1px solid #e5e7eb;text-align:center;transition:var(--trans);">
    <i class="fi-rr-scissors" style="font-size:28px;color:var(--accent);display:inline-block;margin-bottom:12px;"></i>
    <h4 style="margin:0 0 8px;font-size:16px;font-weight:600;color:#111827;" class="service-title">Write service title here</h4>
    <p style="margin:0 0 14px;font-size:13px;color:#6b7280;line-height:1.6;" class="service-desc">Write service description here. Customize in properties.</p>
    <a href="#" style="font-size:12px;font-weight:600;color:var(--accent);text-decoration:none;" class="service-link">Learn More &rarr;</a>
  </div>
  <div class="service-item" style="background:#f9fafb;padding:24px;border-radius:10px;border:1px solid #e5e7eb;text-align:center;transition:var(--trans);">
    <i class="fi-rr-magic-wand" style="font-size:28px;color:var(--accent);display:inline-block;margin-bottom:12px;"></i>
    <h4 style="margin:0 0 8px;font-size:16px;font-weight:600;color:#111827;" class="service-title">Write service title here</h4>
    <p style="margin:0 0 14px;font-size:13px;color:#6b7280;line-height:1.6;" class="service-desc">Write service description here. Customize in properties.</p>
    <a href="#" style="font-size:12px;font-weight:600;color:var(--accent);text-decoration:none;" class="service-link">Learn More &rarr;</a>
  </div>
</div>`,
        }
      ],
    }),
    contact_info: () => ({
      id: sid(),
      label: "Contact &amp; Map",
      type: "contact_section",
      settings: {
        background_color: "#f3f4f6",
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
          text: "Write heading here",
          align: "center",
          color: "#111827",
          size: "32px",
          width: 100,
        },
        {
          id: bid(),
          type: "static_html",
          width: 50,
          content: `<div style="display:flex;flex-direction:column;gap:20px;" class="contacts-container">
  <div class="contact-card" style="background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb;display:flex;align-items:start;gap:12px;">
    <i class="fi-rr-marker" style="color:var(--accent);font-size:20px;margin-top:2px;"></i>
    <div>
      <h5 style="margin:0 0 4px;font-size:15px;font-weight:600;color:#111827;">Write location title here</h5>
      <p class="contact-value" style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">Write location details here</p>
    </div>
  </div>
  <div class="contact-card" style="background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb;display:flex;align-items:start;gap:12px;">
    <i class="fi-rr-phone-call" style="color:var(--accent);font-size:20px;margin-top:2px;"></i>
    <div>
      <h5 style="margin:0 0 4px;font-size:15px;font-weight:600;color:#111827;">Write contact title here</h5>
      <p class="contact-value" style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">+00 00000 00000</p>
    </div>
  </div>
  <div class="contact-card" style="background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb;display:flex;align-items:start;gap:12px;">
    <i class="fi-rr-envelope" style="color:var(--accent);font-size:20px;margin-top:2px;"></i>
    <div>
      <h5 style="margin:0 0 4px;font-size:15px;font-weight:600;color:#111827;">Write email title here</h5>
      <p class="contact-value" style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">support@example.com</p>
    </div>
  </div>
</div>`,
        },
        {
          id: bid(),
          type: "static_html",
          width: 50,
          content: `<div style="height:100%;min-height:300px;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;" class="map-container">
  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.1378893649516!2d77.2185517150824!3d28.625624782419736!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfd36a9b40001%3A0x2db4e0ff5b001d!2sNew%20Delhi!5e0!3m2!1sen!2sin!4v1655000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;min-height:300px;" allowfullscreen="" loading="lazy"></iframe>
</div>`,
        }
      ],
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

    // Drag and drop type tracker
    let draggedType = null;

    // Section picker
    document.querySelectorAll(".sec-card").forEach((card) => {
      card.setAttribute("draggable", "true");
      card.addEventListener("click", () => {
        const type = card.dataset.type;
        if (T[type]) {
          snap();
          addSection(T[type]());
        }
      });
      card.addEventListener("dragstart", (e) => {
        draggedType = card.dataset.type;
        e.dataTransfer.setData("text/plain", draggedType);
        document.getElementById("builder")?.classList.add("dragging");
        showDropZones();
      });
      card.addEventListener("dragend", () => {
        document.getElementById("builder")?.classList.remove("dragging");
        setTimeout(removeDropZones, 50);
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

    // Right Properties panel tabs switcher
    const propsTabs = document.querySelectorAll(".props-tab");
    propsTabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        propsTabs.forEach((t) => t.classList.remove("active"));
        tab.classList.add("active");
        state.activePropsTab = tab.dataset.tab;
        renderProps();
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

    // Help dialog
    const helpBtn = document.getElementById("btn-help");
    const helpModal = document.getElementById("help-modal");
    const closeHelpBtn = document.getElementById("close-help");
    if (helpBtn && helpModal) {
      helpBtn.addEventListener("click", () => {
        helpModal.classList.add("open");
      });
      if (closeHelpBtn) {
        closeHelpBtn.addEventListener("click", () => {
          helpModal.classList.remove("open");
        });
      }
      helpModal.addEventListener("click", (e) => {
        if (e.target === helpModal) {
          helpModal.classList.remove("open");
        }
      });
    }

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
        const hModal = document.getElementById("help-modal");
        if (hModal && hModal.classList.contains("open")) {
          hModal.classList.remove("open");
          return;
        }
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
    if (!sec.settings.layout_style) sec.settings.layout_style = "style_1";
    
    sec.blocks = (sec.blocks || []).map((b) => {
      let normalizedBlock = { width: 100, ...b };
      
      // Dynamic placeholder text cleanup as requested by user
      if (normalizedBlock.type === "heading") {
        normalizedBlock.text = normalizedBlock.tag === "h1" ? "Write title here" : "Write heading here";
      } else if (normalizedBlock.type === "paragraph" || normalizedBlock.type === "rich_text") {
        normalizedBlock.text = "Write paragraph here. Add predefined sections with zero coding. Customise text, colours, alignments and buttons — all inline.";
      } else if (normalizedBlock.type === "button") {
        normalizedBlock.text = "Button Text";
      } else if (normalizedBlock.type === "static_html" && normalizedBlock.content) {
        try {
          const tempParser = new DOMParser();
          const tempDoc = tempParser.parseFromString(normalizedBlock.content || "", "text/html");
          
          const isFaq = tempDoc.querySelector(".faq-container, .faq-item");
          const isTestimonial = tempDoc.querySelector(".testimonials-container, .testimonial-card");
          const isWhyChooseUs = tempDoc.querySelector(".why-container, .why-step");
          const isCredentials = tempDoc.querySelector(".credentials-container, .credential-item");
          const isServices = tempDoc.querySelector(".services-container, .service-item");
          
          if (isFaq) {
            tempDoc.querySelectorAll(".faq-question").forEach(q => q.textContent = "Write question here");
            tempDoc.querySelectorAll(".faq-answer").forEach(p => p.textContent = "Write answer here. Customize all contents and styles inline — zero coding required.");
            normalizedBlock.content = tempDoc.body.innerHTML;
          } else if (isTestimonial) {
            tempDoc.querySelectorAll(".testimonial-quote").forEach(q => q.textContent = `"Write testimonial quote here. Premium results out of the box."`);
            tempDoc.querySelectorAll(".testimonial-author").forEach(a => a.textContent = "— Write author here");
            normalizedBlock.content = tempDoc.body.innerHTML;
          } else if (isWhyChooseUs) {
            tempDoc.querySelectorAll(".why-step-title").forEach(t => t.textContent = "Write benefit highlight here");
            normalizedBlock.content = tempDoc.body.innerHTML;
          } else if (isCredentials) {
            tempDoc.querySelectorAll(".credential-item").forEach(item => {
              const icon = item.querySelector("i");
              item.innerHTML = "";
              if (icon) item.appendChild(icon);
              item.appendChild(document.createTextNode(" Write credential here"));
            });
            normalizedBlock.content = tempDoc.body.innerHTML;
          } else if (isServices) {
            tempDoc.querySelectorAll(".service-title").forEach(t => t.textContent = "Write service title here");
            tempDoc.querySelectorAll(".service-desc").forEach(p => p.textContent = "Write service description here. Customize treatments, options, and layouts dynamically.");
            normalizedBlock.content = tempDoc.body.innerHTML;
          }
        } catch (e) {
          console.error("Error normalizing static HTML:", e);
        }
      }
      return normalizedBlock;
    });
    
    return sec;
  }

  function showEmptyCanvas() {
    if (!canvas) return;
    canvas.innerHTML = `
        <div class="empty-canvas" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 40px;text-align:center;">
            <div class="icon-ring" style="width:72px;height:72px;border-radius:50%;background:#f1f5f9;border:2px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;margin-bottom:20px;color:#cbd5e1;font-size:28px;">
                <i class="fi-rr-layer-plus"></i>
            </div>
            <h3 style="font-size:17px;font-weight:600;color:#94a3b8;margin-bottom:8px;">Start Building Your Page</h3>
            <p style="font-size:13px;color:#94a3b8;max-width:280px;line-height:1.7;margin-bottom:20px;">Add elements visually using predefined layouts. Click any section card in the sidebar to start creating.</p>
            <button class="fc-btn" id="empty-state-explore-btn" style="padding:10px 20px;font-weight:600;"><i class="fi-rr-grid"></i> Explore Layout Templates</button>
        </div>`;

    const exploreBtn = document.getElementById("empty-state-explore-btn");
    if (exploreBtn) {
      exploreBtn.addEventListener("click", () => {
        // Find Layout category tab and click it
        const layoutTab = document.querySelector('.p-tab[data-cat="layout"]');
        if (layoutTab) layoutTab.click();
        
        // Highlight search input
        const searchInput = document.getElementById("sec-search");
        if (searchInput) {
          searchInput.focus();
          searchInput.style.outline = "2px solid var(--accent)";
          setTimeout(() => {
            searchInput.style.outline = "";
          }, 1500);
        }
      });
    }
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

  function renderSingleBlock(sec, blk, bi, parentColWidth = null) {
    const edit = state.mode === "edit";
    const w = parentColWidth || blk.width || 100;
    const col = el("div", "editor-col");
    col.style.cssText = `flex:0 0 ${w}%;max-width:${w}%;padding:12px;box-sizing:border-box;`;

    const bWrap = el(
      "div",
      "blk-wrap" + (state.selBlock === blk.id ? " selected" : ""),
      { "data-blk-id": blk.id },
    );

    if (edit) {
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
    if (edit) {
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
    return col;
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

      // Section style & layout class
      const s = sec.settings || {};
      const lStyle = s.layout_style || "style_1";
      const secEl = el("section", "builder-section " + sec.type + " " + lStyle);
      
      let style = `background-color:${s.background_color || "#fff"};`;
      if (s.background_image)
        style += `background-image:url('${s.background_image}');background-size:cover;background-position:center;`;
      style += `padding:${s.padding_top !== undefined ? s.padding_top : 40}px 0 ${s.padding_bottom !== undefined ? s.padding_bottom : 40}px;`;
      style += `margin:${s.margin_top !== undefined ? s.margin_top : 0}px 0 ${s.margin_bottom !== undefined ? s.margin_bottom : 0}px;`;
      
      if (s.border_width && s.border_style && s.border_style !== 'none') {
        style += `border-top:${s.border_width}px ${s.border_style} ${s.border_color || "var(--border)"};`;
        style += `border-bottom:${s.border_width}px ${s.border_style} ${s.border_color || "var(--border)"};`;
      }
      if (s.border_radius) {
        style += `border-radius:${s.border_radius}px;`;
      }
      if (s.box_shadow) {
        const shadows = {
          none: 'none',
          soft: '0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.05)',
          medium: '0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04)',
          hard: '0 20px 25px -5px rgba(0,0,0,0.15), 0 10px 10px -5px rgba(0,0,0,0.04)',
          accent: '0 10px 20px var(--accent-dim), 0 2px 6px var(--accent-mid)'
        };
        style += `box-shadow:${shadows[s.box_shadow] || 'none'};`;
      }
      if (s.opacity !== undefined) {
        style += `opacity:${s.opacity / 100};`;
      }
      
      secEl.style.cssText = style;

      const cnt = el("div", "builder-container");
      cnt.style.cssText =
        "max-width:1200px;margin:0 auto;padding:0 15px;box-sizing:border-box;";
      const row = el("div", "editor-row");

      // Check layout styles
      if (sec.type === "hero_section" && lStyle === "style_5") {
        // Slider layout!
        const sliderWrap = el("div", "builder-widget", { "data-widget": "hero_slider", style: "width:100%;" });
        sliderWrap.innerHTML = `<div style="text-align:center; padding:50px; color:#aaa; font-family:sans-serif;"><i class="fi-rr-spinner animate-spin" style="font-size:24px; display:block; margin-bottom:8px;"></i>Loading Slides...</div>`;
        row.appendChild(sliderWrap);
        
        // Fetch slider HTML
        fetch(`/api/builder/widget/?action=render&type=hero_slider`)
          .then(r => r.json())
          .then(data => {
            if (data.html) {
              sliderWrap.innerHTML = data.html;
              const scripts = sliderWrap.querySelectorAll("script");
              scripts.forEach(script => {
                const newScript = document.createElement("script");
                newScript.textContent = script.textContent;
                document.body.appendChild(newScript);
              });
            } else {
              sliderWrap.innerHTML = `<p style="color:red; text-align:center; padding:20px;">Failed to load slider content</p>`;
            }
          })
          .catch(err => {
            sliderWrap.innerHTML = `<p style="color:red; text-align:center; padding:20px;">Error loading slider</p>`;
          });
      } else if (
        (sec.type === "hero_section" && (lStyle === "style_2" || lStyle === "style_3")) ||
        (sec.type === "text_image_section" && (lStyle === "style_1" || lStyle === "style_2" || lStyle === "style_4")) ||
        (sec.type === "why_choose_us_section" && (lStyle === "style_1" || lStyle === "style_2")) ||
        (sec.type === "about_doctor_section" && (lStyle === "style_1" || lStyle === "style_2")) ||
        (sec.type === "contact_section" && (lStyle === "style_1" || lStyle === "style_2"))
      ) {
        // Split Column layouts!
        const isMediaBlock = (b) => b.type === "image" || (b.type === "static_html" && (b.content.includes("map-container") || b.content.includes("iframe")));
        
        // Identify blocks
        const cBlocks = sec.blocks.filter(b => !isMediaBlock(b));
        const mBlocks = sec.blocks.filter(b => isMediaBlock(b));
        
        // Col widths depending on section type
        let leftWidth = 50;
        let rightWidth = 50;
        if (sec.type === "about_doctor_section") {
          leftWidth = lStyle === "style_1" ? 40 : 45;
          rightWidth = lStyle === "style_1" ? 60 : 55;
        }
        
        // Create Left Column wrapper and Right Column wrapper
        const colLeft = el("div", "editor-col-split left-col");
        colLeft.style.cssText = `flex:0 0 ${leftWidth}%;max-width:${leftWidth}%;padding:12px;box-sizing:border-box;display:flex;flex-direction:column;gap:12px;`;
        
        const colRight = el("div", "editor-col-split right-col");
        colRight.style.cssText = `flex:0 0 ${rightWidth}%;max-width:${rightWidth}%;padding:12px;box-sizing:border-box;display:flex;flex-direction:column;gap:12px;`;
        
        // Card wrapper for Modern Card Split
        if (sec.type === "text_image_section" && lStyle === "style_4") {
          colLeft.classList.add("modern-text-card");
          colLeft.style.background = "var(--bg-1)";
          colLeft.style.border = "1px solid var(--border)";
          colLeft.style.boxShadow = "var(--shadow-lg)";
          colLeft.style.borderRadius = "var(--radius-lg)";
          colLeft.style.padding = "30px";
        }
        
        const isReverse = 
          (sec.type === "hero_section" && lStyle === "style_3") ||
          (sec.type === "text_image_section" && lStyle === "style_2") ||
          (sec.type === "why_choose_us_section" && lStyle === "style_2") ||
          (sec.type === "about_doctor_section" && (lStyle === "style_1" || lStyle === "style_2")) || // doctor photo is left
          (sec.type === "contact_section" && lStyle === "style_2");
          
        if (isReverse) {
          // Media left, Content right
          mBlocks.forEach(blk => {
            const idx = sec.blocks.indexOf(blk);
            colLeft.appendChild(renderSingleBlock(sec, blk, idx, 100));
          });
          cBlocks.forEach(blk => {
            const idx = sec.blocks.indexOf(blk);
            colRight.appendChild(renderSingleBlock(sec, blk, idx, 100));
          });
        } else {
          // Content left, Media right
          cBlocks.forEach(blk => {
            const idx = sec.blocks.indexOf(blk);
            colLeft.appendChild(renderSingleBlock(sec, blk, idx, 100));
          });
          mBlocks.forEach(blk => {
            const idx = sec.blocks.indexOf(blk);
            colRight.appendChild(renderSingleBlock(sec, blk, idx, 100));
          });
        }
        
        row.appendChild(colLeft);
        row.appendChild(colRight);
      } else {
        // Standard Flat Flow Layout (style_1, style_3, etc.)
        sec.blocks.forEach((blk, bi) => {
          let w = blk.width || 100;
          
          // Force layout widths if specific grid styles are chosen
          if (sec.type === "features_section" && lStyle === "style_2") {
            // Force 4-column
            w = blk.type === "heading" && blk.tag === "h2" ? 100 : 25;
          } else if (sec.type === "services_section" && lStyle === "style_2") {
            // Force 2-column list
            w = blk.type === "heading" ? 100 : 50;
          } else if (sec.type === "testimonials_section" && lStyle === "style_2") {
            // Force 3-column
            w = blk.type === "heading" ? 100 : 33;
          }
          
          row.appendChild(renderSingleBlock(sec, blk, bi, w));
        });
      }

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
    if (state.activePropsTab === "themes") {
      state.activePropsTab = "content";
      document.querySelectorAll(".props-tab").forEach(t => {
        t.classList.toggle("active", t.dataset.tab === "content");
      });
    }

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
  /* ── Properties panel ────────────────────────────── */
  function renderProps() {
    if (!propsBody) return;
    const title = document.getElementById("props-title");
    const tabs = document.getElementById("props-tabs");

    if (state.mode === "preview") {
      if (title) {
        title.style.display = "";
        title.textContent = "Preview";
      }
      if (tabs) tabs.style.display = "none";
      propsBody.innerHTML = `<div class="empty-panel"><i class="fi-rr-eye"></i><p>Preview mode is active. Return to Edit Mode to change properties.</p></div>`;
      return;
    }

    if (state.selSubElement && state.selBlock) {
      if (title) {
        title.style.display = "";
      }
      if (tabs) tabs.style.display = "none";
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

    if (state.selSection) {
      if (title) title.style.display = "none";
      if (tabs) tabs.style.display = "flex";

      const sec = state.sections.find((s) => s.id === state.selSection);
      if (!sec) {
        state.selSection = state.selBlock = null;
        if (title) {
          title.style.display = "";
          title.textContent = "Properties";
        }
        if (tabs) tabs.style.display = "none";
        propsBody.innerHTML = `<div class="empty-panel"><i class="fi-rr-cursor"></i><p>Click any <strong>section</strong> or <strong>block</strong> on the canvas to edit its properties here.</p></div>`;
        return;
      }

      if (state.activePropsTab === "themes" && state.selBlock) {
        state.selBlock = null;
        canvas.querySelectorAll(".blk-wrap.selected").forEach(b => b.classList.remove("selected"));
      }

      if (state.activePropsTab === "themes") {
        renderLayoutTab(sec);
      } else if (state.activePropsTab === "design") {
        if (state.selBlock) {
          const blk = sec.blocks.find(b => b.id === state.selBlock);
          if (blk) renderDesignTab(blk, "block");
          else renderDesignTab(sec, "section");
        } else {
          renderDesignTab(sec, "section");
        }
      } else if (state.activePropsTab === "content") {
        if (state.selBlock) {
          const blk = sec.blocks.find(b => b.id === state.selBlock);
          if (blk) renderContentTab(blk, "block", sec);
          else renderContentTab(sec, "section");
        } else {
          renderContentTab(sec, "section");
        }
      }
      return;
    }

    if (title) {
      title.style.display = "";
      title.textContent = "Properties";
    }
    if (tabs) tabs.style.display = "none";
    propsBody.innerHTML = `<div class="empty-panel"><i class="fi-rr-cursor"></i><p>Click any <strong>section</strong> or <strong>block</strong> on the canvas to edit its properties here.</p></div>`;
  }

  /* ── Drag and Drop dropzone controls ──────────────── */
  function showDropZones() {
    removeDropZones();
    const secWraps = Array.from(canvas.querySelectorAll(".sec-wrap"));
    
    const createZone = (idx) => {
      const zone = el("div", "builder-drop-zone", { "data-index": idx });
      zone.innerHTML = `<span class="drop-zone-text"><i class="fi-rr-plus"></i> Drop Here</span>`;
      
      zone.addEventListener("dragover", (e) => {
        e.preventDefault();
        zone.classList.add("hover");
      });
      zone.addEventListener("dragleave", () => {
        zone.classList.remove("hover");
      });
      zone.addEventListener("drop", (e) => {
        e.preventDefault();
        const index = parseInt(zone.dataset.index);
        const draggedType = e.dataTransfer.getData("text/plain");
        if (draggedType && T[draggedType]) {
          snap();
          const newSec = T[draggedType]();
          state.sections.splice(index, 0, newSec);
          state.selSection = newSec.id;
          state.selBlock = null;
          renderCanvas();
          renderProps();
          showSave("saving", "Unsaved");
        }
        removeDropZones();
      });
      return zone;
    };

    if (secWraps.length === 0) {
      const emptyCanvas = canvas.querySelector(".empty-canvas");
      if (emptyCanvas) {
        const zone = createZone(0);
        zone.style.height = "160px";
        emptyCanvas.style.display = "none";
        canvas.appendChild(zone);
      }
    } else {
      secWraps.forEach((wrap, idx) => {
        canvas.insertBefore(createZone(idx), wrap);
      });
      canvas.appendChild(createZone(secWraps.length));
    }
  }

  function removeDropZones() {
    canvas.querySelectorAll(".builder-drop-zone").forEach(z => z.remove());
    const emptyCanvas = canvas.querySelector(".empty-canvas");
    if (emptyCanvas && state.sections.length === 0) {
      emptyCanvas.style.display = "";
    }
  }

  /* ── Layout Styles (Themes) Tab Panel ────────────── */
  function renderLayoutTab(sec) {
    const st = sec.settings || {};
    const layoutOptions = getLayoutOptionsForType(sec.type);
    
    let h = `
      <div class="section-title-label" style="margin-top: 5px;">Choose Layout Template</div>
      <div class="theme-grid">
    `;

    layoutOptions.forEach(opt => {
      const isActive = st.layout_style === opt.value;
      h += `
        <div class="theme-card ${isActive ? "active" : ""}" data-style="${opt.value}">
          <div class="theme-preview ${opt.value}-prev">
            ${renderThemePreviewMockup(sec.type, opt.value)}
          </div>
          <span class="theme-label">${opt.label.split(":")[0]}</span>
        </div>
      `;
    });

    h += `</div>`;
    
    if (sec.type === 'hero_section' && st.layout_style === 'style_5') {
      h += `
        <div class="divider"></div>
        <div style="background:var(--accent-dim); border:1px solid var(--accent-mid); padding:12px; border-radius:var(--radius-md); font-size:11px; color:var(--text-1); line-height:1.6; margin-top:14px;">
          <i class="fi-rr-info" style="color:var(--accent); font-size:13px; vertical-align:middle; margin-right:4px;"></i>
          <strong>Slider Management:</strong> The dynamic slider pulls slides directly from your dashboard configuration.
          <div style="margin-top:8px;">
            <a href="/dashboard/appearance/?type=sliders" target="_top" class="fc-btn" style="padding:6px 12px; font-size:10px; text-decoration:none;"><i class="fi-rr-settings"></i> Edit Slider Slides</a>
          </div>
        </div>
      `;
    }

    propsBody.innerHTML = h;

    propsBody.querySelectorAll(".theme-card").forEach(card => {
      card.addEventListener("click", () => {
        const val = card.dataset.style;
        if (st.layout_style !== val) {
          snap();
          sec.settings.layout_style = val;
          renderCanvas();
          renderProps();
          showSave("saving", "Unsaved");
        }
      });
    });
  }

  function renderThemePreviewMockup(type, style) {
    if (style === "style_1") {
      return `
        <div class="mock-centered">
          <div class="mock-line title"></div>
          <div class="mock-line text"></div>
          <div class="mock-btn"></div>
        </div>
      `;
    } else if (style === "style_2") {
      return `
        <div class="mock-split">
          <div class="mock-col">
            <div class="mock-line title"></div>
            <div class="mock-line text"></div>
          </div>
          <div class="mock-col">
            <div class="mock-box"></div>
          </div>
        </div>
      `;
    } else if (style === "style_3") {
      return `
        <div class="mock-split">
          <div class="mock-col">
            <div class="mock-box"></div>
          </div>
          <div class="mock-col">
            <div class="mock-line title"></div>
            <div class="mock-line text"></div>
          </div>
        </div>
      `;
    } else if (style === "style_4") {
      return `
        <div class="mock-centered glass">
          <div class="mock-line title"></div>
          <div class="mock-line text"></div>
        </div>
      `;
    } else if (style === "style_5") {
      return `
        <div class="mock-slider">
          <i class="fi fi-rr-angle-left arrow" style="font-size: 7px; color: var(--text-3);"></i>
          <div class="mock-centered">
            <div class="mock-line title" style="width: 50%;"></div>
          </div>
          <i class="fi fi-rr-angle-right arrow" style="font-size: 7px; color: var(--text-3);"></i>
          <div class="mock-dots"><span class="dot active"></span><span class="dot"></span></div>
        </div>
      `;
    }
    return `<div class="mock-centered"><div class="mock-line title"></div></div>`;
  }

  /* ── Design Tab Panel ────────────────────────────── */
  function renderDesignTab(element, elementType) {
    const isSec = elementType === "section";
    const st = isSec ? element.settings : element;
    
    const renderGroup = (id, label, content) => `
      <div class="design-group open" id="dg-${id}">
        <div class="design-group-head">
          <span>${label}</span>
          <i class="fi-rr-angle-small-down"></i>
        </div>
        <div class="design-group-body">
          ${content}
        </div>
      </div>
    `;

    let html = `<div class="design-accordion">`;

    let spacingHtml = "";
    if (isSec) {
      spacingHtml = `
        <div class="fg-range-num">
          <label>Padding Top (px)</label>
          <div class="range-num-wrap">
            <input type="range" min="0" max="200" class="fc-range" id="p-spt-range" value="${st.padding_top || 40}">
            <input type="number" min="0" max="200" class="fc fc-num" id="p-spt" value="${st.padding_top || 40}">
          </div>
        </div>
        <div class="fg-range-num">
          <label>Padding Bottom (px)</label>
          <div class="range-num-wrap">
            <input type="range" min="0" max="200" class="fc-range" id="p-spb-range" value="${st.padding_bottom || 40}">
            <input type="number" min="0" max="200" class="fc fc-num" id="p-spb" value="${st.padding_bottom || 40}">
          </div>
        </div>
        <div class="fg-range-num">
          <label>Margin Top (px)</label>
          <div class="range-num-wrap">
            <input type="range" min="0" max="100" class="fc-range" id="p-smt-range" value="${st.margin_top || 0}">
            <input type="number" min="0" max="100" class="fc fc-num" id="p-smt" value="${st.margin_top || 0}">
          </div>
        </div>
        <div class="fg-range-num">
          <label>Margin Bottom (px)</label>
          <div class="range-num-wrap">
            <input type="range" min="0" max="100" class="fc-range" id="p-smb-range" value="${st.margin_bottom || 0}">
            <input type="number" min="0" max="100" class="fc fc-num" id="p-smb" value="${st.margin_bottom || 0}">
          </div>
        </div>
      `;
    } else {
      spacingHtml = `
        <div class="fg"><label>Block Width</label>
          <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${element.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
        </div>
      `;
      if (element.type === "button") {
        spacingHtml += `
          <div class="fg-row">
            <div><label>Padding X (px)</label><input type="number" class="fc" id="p-bpx" value="${element.padding_x || 24}"></div>
            <div><label>Padding Y (px)</label><input type="number" class="fc" id="p-bpy" value="${element.padding_y || 12}"></div>
          </div>
        `;
      } else if (element.type === "spacer") {
        spacingHtml += `
          <div class="fg"><label>Spacer Height (px)</label><input type="number" class="fc" id="p-sh" value="${element.height || 40}"></div>
        `;
      }
    }
    html += renderGroup("spacing", "Spacing", spacingHtml);

    if (isSec || element.type === "button") {
      let bgHtml = "";
      if (isSec) {
        bgHtml = `
          <div class="fg">
            <label>Background Color</label>
            <div class="color-picker-wrap" style="display:flex;gap:6px;">
              <input type="color" class="fc" id="p-sbg" value="${st.background_color || "#ffffff"}" style="width:40px;padding:2px;height:32px;">
              <input type="text" class="fc" id="p-sbg-text" value="${st.background_color || "#ffffff"}" style="flex:1;">
            </div>
          </div>
          <div class="fg">
            <label>Background Image URL</label>
            <input type="text" class="fc" id="p-sbgimg" value="${st.background_image || ""}" placeholder="https://…">
          </div>
        `;
      } else {
        bgHtml = `
          <div class="fg-row">
            <div>
              <label>BG Color</label>
              <input type="color" class="fc" id="p-bbg" value="${element.bg_color || state.themeColor}">
            </div>
            <div>
              <label>Text Color</label>
              <input type="color" class="fc" id="p-bclr" value="${element.color || "#fff"}">
            </div>
          </div>
        `;
      }
      html += renderGroup("background", "Background", bgHtml);
    }

    if (!isSec && ["heading", "paragraph", "rich_text"].includes(element.type)) {
      let typoHtml = `
        <div class="fg"><label>Text Align</label>
          <select class="fc" id="p-align">${["left", "center", "right", "justify"].map((v) => `<option value="${v}" ${element.align === v ? "selected" : ""}>${v[0].toUpperCase() + v.slice(1)}</option>`).join("")}</select>
        </div>
        <div class="fg-row">
          <div><label>Text Color</label><input type="color" class="fc" id="p-color" value="${element.color || "#333"}"></div>
          <div><label>Font Size</label><input type="text" class="fc" id="p-size" value="${element.size || ""}" placeholder="16px"></div>
        </div>
      `;
      if (element.type === "heading") {
        typoHtml += `
          <div class="fg"><label>Heading Tag</label>
            <select class="fc" id="p-tag">${["h1", "h2", "h3", "h4", "h5", "h6"].map((v) => `<option value="${v}" ${element.tag === v ? "selected" : ""}>${v.toUpperCase()}</option>`).join("")}</select>
          </div>
        `;
      }
      html += renderGroup("typography", "Typography", typoHtml);
    }

    if (isSec || ["button", "image"].includes(element.type)) {
      let borderHtml = "";
      if (isSec) {
        borderHtml = `
          <div class="fg-range-num">
            <label>Border Radius (px)</label>
            <div class="range-num-wrap">
              <input type="range" min="0" max="100" class="fc-range" id="p-srad-range" value="${st.border_radius || 0}">
              <input type="number" min="0" max="100" class="fc fc-num" id="p-srad" value="${st.border_radius || 0}">
            </div>
          </div>
          <div class="fg-row">
            <div>
              <label>Border Width (px)</label>
              <input type="number" class="fc" id="p-sbdw" value="${st.border_width || 0}">
            </div>
            <div>
              <label>Border Style</label>
              <select class="fc" id="p-sbds">
                ${["none", "solid", "dashed", "dotted", "double"].map(s => `<option value="${s}" ${st.border_style === s ? "selected" : ""}>${s.toUpperCase()}</option>`).join("")}
              </select>
            </div>
          </div>
          <div class="fg">
            <label>Border Color</label>
            <input type="color" class="fc" id="p-sbdc" value="${st.border_color || "#d1d5db"}">
          </div>
        `;
      } else {
        borderHtml = `
          <div class="fg"><label>Border Radius (px)</label>
            <input type="number" class="fc" id="${element.type === "button" ? "p-brad" : "p-irad"}" value="${element.radius || 0}">
          </div>
        `;
      }
      html += renderGroup("borders", "Borders & Corners", borderHtml);
    }

    if (isSec) {
      const effectsHtml = `
        <div class="fg">
          <label>Box Shadow</label>
          <select class="fc" id="p-s-shadow">
            ${[
              ["none", "None"],
              ["soft", "Soft Shadow"],
              ["medium", "Medium Shadow"],
              ["hard", "Hard Shadow"],
              ["accent", "Accent Glow"]
            ].map(([val, lbl]) => `<option value="${val}" ${st.box_shadow === val ? "selected" : ""}>${lbl}</option>`).join("")}
          </select>
        </div>
        <div class="fg-range-num">
          <label>Opacity (%)</label>
          <div class="range-num-wrap">
            <input type="range" min="0" max="100" class="fc-range" id="p-s-opacity-range" value="${st.opacity !== undefined ? st.opacity : 100}">
            <input type="number" min="0" max="100" class="fc fc-num" id="p-s-opacity" value="${st.opacity !== undefined ? st.opacity : 100}">
          </div>
        </div>
      `;
      html += renderGroup("effects", "Effects", effectsHtml);
    }

    html += `</div>`;
    propsBody.innerHTML = html;

    propsBody.querySelectorAll(".design-group-head").forEach(head => {
      head.addEventListener("click", () => {
        head.closest(".design-group").classList.toggle("open");
      });
    });

    const bindRangeNum = (rangeId, numId, callback) => {
      const range = document.getElementById(rangeId);
      const num = document.getElementById(numId);
      if (range && num) {
        range.addEventListener("input", () => {
          num.value = range.value;
          callback(parseInt(range.value));
        });
        num.addEventListener("input", () => {
          range.value = num.value;
          callback(parseInt(num.value) || 0);
        });
      }
    };

    if (isSec) {
      bindRangeNum("p-spt-range", "p-spt", (v) => {
        st.padding_top = v;
        renderCanvas();
      });
      bindRangeNum("p-spb-range", "p-spb", (v) => {
        st.padding_bottom = v;
        renderCanvas();
      });
      bindRangeNum("p-smt-range", "p-smt", (v) => {
        st.margin_top = v;
        renderCanvas();
      });
      bindRangeNum("p-smb-range", "p-smb", (v) => {
        st.margin_bottom = v;
        renderCanvas();
      });

      const cp = document.getElementById("p-sbg");
      const cTxt = document.getElementById("p-sbg-text");
      if (cp && cTxt) {
        cp.addEventListener("input", () => {
          cTxt.value = cp.value;
          st.background_color = cp.value;
          renderCanvas();
        });
        cTxt.addEventListener("input", () => {
          cp.value = cTxt.value;
          st.background_color = cTxt.value;
          renderCanvas();
        });
        cp.addEventListener("change", snap);
        cTxt.addEventListener("change", snap);
      }

      bind("p-sbgimg", "input", (v) => {
        st.background_image = v;
        renderCanvas();
      });
      bind("p-sbgimg", "change", snap);

      bindRangeNum("p-srad-range", "p-srad", (v) => {
        st.border_radius = v;
        renderCanvas();
      });
      bind("p-sbdw", "input", (v) => {
        st.border_width = parseInt(v) || 0;
        renderCanvas();
      });
      bind("p-sbdw", "change", snap);
      bind("p-sbds", "change", (v) => {
        st.border_style = v;
        renderCanvas();
      });
      bind("p-sbdc", "input", (v) => {
        st.border_color = v;
        renderCanvas();
      });
      bind("p-sbdc", "change", snap);

      bind("p-s-shadow", "change", (v) => {
        st.box_shadow = v;
        renderCanvas();
      });
      bindRangeNum("p-s-opacity-range", "p-s-opacity", (v) => {
        st.opacity = v;
        renderCanvas();
      });
    } else {
      bind("p-w", "change", (v) => {
        snap();
        element.width = parseInt(v);
        renderCanvas();
      });

      if (element.type === "button") {
        bind("p-bpx", "input", (v) => {
          element.padding_x = parseInt(v) || 0;
          renderCanvas();
        });
        bind("p-bpx", "change", snap);
        bind("p-bpy", "input", (v) => {
          element.padding_y = parseInt(v) || 0;
          renderCanvas();
        });
        bind("p-bpy", "change", snap);
        
        bind("p-bbg", "input", (v) => {
          element.bg_color = v;
          renderCanvas();
        });
        bind("p-bbg", "change", snap);
        bind("p-bclr", "input", (v) => {
          element.color = v;
          renderCanvas();
        });
        bind("p-bclr", "change", snap);
        bind("p-brad", "input", (v) => {
          element.radius = parseInt(v) || 0;
          renderCanvas();
        });
        bind("p-brad", "change", snap);
      } else if (element.type === "spacer") {
        bind("p-sh", "input", (v) => {
          element.height = parseInt(v) || 0;
          renderCanvas();
        });
        bind("p-sh", "change", snap);
      } else if (element.type === "image") {
        bind("p-irad", "input", (v) => {
          element.radius = parseInt(v) || 0;
          renderCanvas();
        });
        bind("p-irad", "change", snap);
      }

      if (["heading", "paragraph", "rich_text"].includes(element.type)) {
        bind("p-align", "change", (v) => {
          element.align = v;
          renderCanvas();
        });
        bind("p-color", "input", (v) => {
          element.color = v;
          renderCanvas();
        });
        bind("p-color", "change", snap);
        bind("p-size", "input", (v) => {
          element.size = v;
          renderCanvas();
        });
        bind("p-size", "change", snap);
        
        if (element.type === "heading") {
          bind("p-tag", "change", (v) => {
            snap();
            element.tag = v;
            renderCanvas();
          });
        }
      }
    }
  }

  /* ── Content Tab Panel ───────────────────────────── */
  function renderContentTab(element, elementType, sec = null) {
    if (elementType === "block") {
      const blk = element;
      let html = `
        <div class="fg" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
          <span style="font-size:11px;font-weight:600;color:var(--text-2);text-transform:uppercase;">Block Content</span>
          <button class="fc-btn secondary" id="btn-select-parent-sec" style="font-size:10px;padding:4px 8px;"><i class="fi-rr-arrow-up"></i> Parent Section</button>
        </div>
      `;

      if (blk.type === "heading") {
        html += `
          <div class="fg">
            <label>Heading Text</label>
            <textarea class="fc content-text-input" data-sync-text="${blk.id}" rows="3" style="font-family:inherit;font-size:13px;height:auto;">${blk.text || ""}</textarea>
          </div>
        `;
      } else if (["paragraph", "rich_text"].includes(blk.type)) {
        html += `
          <div class="fg">
            <label>Paragraph Text</label>
            <textarea class="fc content-text-input" data-sync-text="${blk.id}" rows="8" style="font-family:inherit;font-size:13px;height:160px;">${blk.text || ""}</textarea>
          </div>
        `;
      } else if (blk.type === "button") {
        html += `
          <div class="fg">
            <label>Button Text</label>
            <input type="text" class="fc content-text-input" data-sync-text="${blk.id}" value="${blk.text || ""}">
          </div>
          <div class="fg">
            <label>Link URL</label>
            <input type="text" class="fc" id="p-blink" value="${blk.link || "#"}">
          </div>
        `;
      } else if (blk.type === "image") {
        html += `
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
          <div class="fg">
            <label>Alt Text</label>
            <input type="text" class="fc" id="p-alt" value="${blk.alt || ""}">
          </div>
          <div class="fg">
            <label>Link URL (Optional)</label>
            <input type="text" class="fc" id="p-ilink" value="${blk.link || ""}" placeholder="https://…">
          </div>
          <div style="display:flex;gap:8px;margin-top:14px;">
            <button class="fc-btn" id="p-image-update" style="flex:1;"><i class="fi-rr-check"></i> Update</button>
            <button class="fc-btn secondary" id="p-image-reset" style="flex:1;"><i class="fi-rr-refresh"></i> Reset</button>
          </div>
        `;
      } else if (blk.type === "static_html") {
        const tempParser = new DOMParser();
        const tempDoc = tempParser.parseFromString(blk.content || "", "text/html");
        
        const isFaq = tempDoc.querySelector(".faq-container, .faq-item");
        const isTestimonial = tempDoc.querySelector(".testimonials-container, .testimonial-card");
        const isWhyChooseUs = tempDoc.querySelector(".why-container, .why-step");
        const isCredentials = tempDoc.querySelector(".credentials-container, .credential-item");
        const isContact = tempDoc.querySelector(".contacts-container, .contact-card");
        const isServices = tempDoc.querySelector(".services-container, .service-item");

        if (isFaq) {
          renderFaqSidebarForm(blk, tempDoc);
          return;
        } else if (isTestimonial) {
          renderTestimonialsSidebarForm(blk, tempDoc);
          return;
        } else if (isWhyChooseUs) {
          renderWhyChooseUsSidebarForm(blk, tempDoc);
          return;
        } else if (isCredentials) {
          renderCredentialsSidebarForm(blk, tempDoc);
          return;
        } else if (isContact) {
          renderContactSidebarForm(blk, tempDoc);
          return;
        } else if (isServices) {
          renderServicesSidebarForm(blk, tempDoc);
          return;
        }

        html += `
          <div class="fg">
            <label>HTML Code</label>
            <textarea class="fc" id="p-html" rows="16" style="height:280px;font-family:monospace;font-size:12px;line-height:1.5;">${blk.content || ""}</textarea>
          </div>
        `;
      } else if (blk.type === "widget" && blk.widget_type === "recent_articles") {
        html += `
          <div class="fg">
            <label>Articles Limit</label>
            <input type="number" class="fc" id="p-wlim" min="1" max="20" value="${blk.settings?.limit || 3}">
          </div>
        `;
      } else {
        html += `<div class="empty-panel"><p>No content settings for this block type.</p></div>`;
      }

      propsBody.innerHTML = html;

      const parentBtn = document.getElementById("btn-select-parent-sec");
      if (parentBtn) {
        parentBtn.addEventListener("click", () => {
          if (sec) selectSec(sec.id);
        });
      }

      if (blk.type === "heading" || ["paragraph", "rich_text"].includes(blk.type)) {
        const textInput = propsBody.querySelector(".content-text-input");
        if (textInput) {
          textInput.addEventListener("input", () => {
            blk.text = textInput.value;
            const bWrap = canvas.querySelector(`[data-blk-id="${blk.id}"]`);
            const editable = bWrap?.querySelector(".editable");
            if (editable) editable.innerHTML = blk.text;
            showSave("saving", "Unsaved");
          });
          textInput.addEventListener("blur", snap);
        }
      } else if (blk.type === "button") {
        const textInput = propsBody.querySelector(".content-text-input");
        if (textInput) {
          textInput.addEventListener("input", () => {
            blk.text = textInput.value;
            const bWrap = canvas.querySelector(`[data-blk-id="${blk.id}"]`);
            const editable = bWrap?.querySelector(".editable");
            if (editable) editable.innerHTML = blk.text;
            showSave("saving", "Unsaved");
          });
          textInput.addEventListener("blur", snap);
        }
        bind("p-blink", "input", (v) => {
          blk.link = v;
          showSave("saving", "Unsaved");
        });
        bind("p-blink", "change", snap);
      } else if (blk.type === "image") {
        setupImageBlockControls(blk);
      } else if (blk.type === "static_html") {
        bind("p-html", "input", (v) => {
          blk.content = v;
          renderCanvas();
        });
        bind("p-html", "change", snap);
      } else if (blk.type === "widget" && blk.widget_type === "recent_articles") {
        bind("p-wlim", "input", (v) => {
          blk.settings.limit = parseInt(v) || 3;
          renderCanvas();
        });
        bind("p-wlim", "change", snap);
      }
    } else {
      const sec = element;
      let html = `
        <div class="fg" style="display:flex; gap:10px; align-items:center; justify-content:space-between; margin-bottom:14px;">
          <div style="flex:1;">
            <label style="margin-bottom:4px;">Section Label</label>
            <input type="text" class="fc" id="p-slbl" value="${sec.label || "Section"}" style="padding:6px 10px; font-size:11px;">
          </div>
          <button class="fc-btn danger" id="sidebar-delete-sec" style="align-self:flex-end; padding:6px 10px; font-size:11px; font-weight:600; height:30px;"><i class="fi-rr-trash"></i> Delete Section</button>
        </div>
        <div class="divider"></div>
        <div class="section-title-label">Content Blocks</div>
        <div class="unified-blocks-list" style="display:flex;flex-direction:column;gap:14px;margin-bottom:16px;">
      `;

      if (sec.blocks.length === 0) {
        html += `<div class="empty-panel" style="padding:14px;"><p style="font-size:11px;">No blocks in this section. Add one below.</p></div>`;
      } else {
        sec.blocks.forEach((blk, idx) => {
          const typeLabel = blk.type.replace(/_/g, " ").toUpperCase();
          let summary = "";
          let itemInputHtml = "";

          if (blk.type === "heading" || ["paragraph", "rich_text"].includes(blk.type)) {
            summary = blk.text ? blk.text.replace(/<[^>]*>/g, '').substring(0, 40) + (blk.text.length > 40 ? "..." : "") : "Empty text";
            itemInputHtml = `
              <textarea class="fc content-text-input" data-sync-text="${blk.id}" data-idx="${idx}" rows="2" style="font-size:11px;padding:6px;font-family:inherit;margin-top:6px;height:auto;">${blk.text || ""}</textarea>
            `;
          } else if (blk.type === "button") {
            summary = `Text: "${blk.text || ""}"`;
            itemInputHtml = `
              <div style="display:flex;flex-direction:column;gap:6px;margin-top:6px;">
                <input type="text" class="fc content-text-input" data-sync-text="${blk.id}" data-idx="${idx}" value="${blk.text || ""}" style="font-size:11px;padding:6px;" placeholder="Button Text">
                <input type="text" class="fc unified-btn-link" data-idx="${idx}" value="${blk.link || ""}" style="font-size:11px;padding:6px;" placeholder="Link URL">
              </div>
            `;
          } else if (blk.type === "image") {
            summary = blk.src ? blk.src.split('/').pop().substring(0, 30) : "No image";
            itemInputHtml = `
              <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
                <img src="${blk.src || ""}" style="width:36px;height:36px;object-fit:cover;border-radius:4px;border:1px solid var(--border);">
                <input type="text" class="fc unified-img-src" data-idx="${idx}" value="${blk.src || ""}" style="font-size:11px;padding:6px;flex:1;" placeholder="Image URL">
              </div>
            `;
          } else if (blk.type === "spacer") {
            summary = `Height: ${blk.height || 30}px`;
          } else if (blk.type === "static_html") {
            summary = "Custom HTML Content";
            itemInputHtml = `
              <button class="fc-btn secondary edit-block-btn" data-blk-id="${blk.id}" style="width:100%;margin-top:6px;font-size:10px;padding:4px;"><i class="fi-rr-pencil"></i> Customize HTML/List</button>
            `;
          } else {
            summary = blk.type;
          }

          html += `
            <div class="block-item-card" data-blk-id="${blk.id}" style="background:var(--bg-2);padding:10px;border-radius:var(--radius-md);border:1px solid var(--border);position:relative;">
              <div class="block-item-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;cursor:pointer;">
                <strong style="font-size:10px;color:var(--accent);display:flex;align-items:center;gap:4px;">
                  ${getBlockIcon(blk.type)} ${typeLabel}
                </strong>
                <div class="block-item-actions" style="display:flex;gap:2px;">
                  <button class="ctrl-btn move-up" data-idx="${idx}" title="Move Up" style="width:18px;height:18px;font-size:10px;"><i class="fi-rr-angle-small-up"></i></button>
                  <button class="ctrl-btn move-down" data-idx="${idx}" title="Move Down" style="width:18px;height:18px;font-size:10px;"><i class="fi-rr-angle-small-down"></i></button>
                  <button class="ctrl-btn danger delete-blk" data-idx="${idx}" title="Delete" style="width:18px;height:18px;font-size:10px;"><i class="fi-rr-trash"></i></button>
                </div>
              </div>
              <div style="font-size:11px;color:var(--text-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                ${summary}
              </div>
              ${itemInputHtml}
            </div>
          `;
        });
      }

      html += `
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
        </div>
      `;

      propsBody.innerHTML = html;

      bind("p-slbl", "input", (v) => {
        sec.label = v;
        renderCanvas();
      });

      const sidebarDeleteSecBtn = document.getElementById("sidebar-delete-sec");
      if (sidebarDeleteSecBtn) {
        sidebarDeleteSecBtn.addEventListener("click", () => {
          const si = state.sections.findIndex(s => s.id === sec.id);
          if (si !== -1) {
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
                state.selSection = null;
                state.selBlock = null;
                renderCanvas();
                renderProps();
              }
            });
          }
        });
      }

      propsBody.querySelectorAll(".content-text-input").forEach(input => {
        input.addEventListener("input", () => {
          const idx = parseInt(input.dataset.idx || input.getAttribute("data-idx"));
          const blk = sec.blocks[idx];
          if (blk) {
            blk.text = input.value;
            const bWrap = canvas.querySelector(`[data-blk-id="${blk.id}"]`);
            const editable = bWrap?.querySelector(".editable");
            if (editable) editable.innerHTML = blk.text;
            showSave("saving", "Unsaved");
          }
        });
        input.addEventListener("blur", snap);
      });

      propsBody.querySelectorAll(".unified-btn-link").forEach(input => {
        input.addEventListener("input", () => {
          const idx = parseInt(input.dataset.idx || input.getAttribute("data-idx"));
          const blk = sec.blocks[idx];
          if (blk) {
            blk.link = input.value;
            showSave("saving", "Unsaved");
          }
        });
        input.addEventListener("blur", snap);
      });

      propsBody.querySelectorAll(".unified-img-src").forEach(input => {
        input.addEventListener("input", () => {
          const idx = parseInt(input.dataset.idx || input.getAttribute("data-idx"));
          const blk = sec.blocks[idx];
          if (blk) {
            blk.src = input.value;
            renderCanvas();
            showSave("saving", "Unsaved");
          }
        });
        input.addEventListener("blur", snap);
      });

      propsBody.querySelectorAll(".edit-block-btn").forEach(btn => {
        btn.addEventListener("click", () => {
          const bid = btn.dataset.blkId;
          selectBlk(sec.id, bid);
        });
      });

      propsBody.querySelectorAll(".move-up").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const idx = parseInt(btn.dataset.idx || btn.getAttribute("data-idx"));
          if (idx > 0) {
            snap();
            [sec.blocks[idx], sec.blocks[idx - 1]] = [sec.blocks[idx - 1], sec.blocks[idx]];
            renderCanvas();
            renderProps();
          }
        });
      });

      propsBody.querySelectorAll(".move-down").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const idx = parseInt(btn.dataset.idx || btn.getAttribute("data-idx"));
          if (idx < sec.blocks.length - 1) {
            snap();
            [sec.blocks[idx], sec.blocks[idx + 1]] = [sec.blocks[idx + 1], sec.blocks[idx]];
            renderCanvas();
            renderProps();
          }
        });
      });

      propsBody.querySelectorAll(".delete-blk").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const idx = parseInt(btn.dataset.idx || btn.getAttribute("data-idx"));
          swal({
            title: "Delete Block?",
            text: "This will remove this block from the section.",
            icon: "warning",
            buttons: ["Cancel", "Delete"],
            dangerMode: true,
          }).then((ok) => {
            if (ok) {
              snap();
              sec.blocks.splice(idx, 1);
              renderCanvas();
              renderProps();
            }
          });
        });
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
  }

  function getBlockIcon(type) {
    const icons = {
      heading: '<i class="fi-rr-text-size"></i>',
      paragraph: '<i class="fi-rr-align-left"></i>',
      rich_text: '<i class="fi-rr-align-left"></i>',
      button: '<i class="fi-rr-mouse-pointer"></i>',
      image: '<i class="fi-rr-picture"></i>',
      spacer: '<i class="fi-rr-resize"></i>',
      static_html: '<i class="fi-rr-code"></i>',
      widget: '<i class="fi-rr-rss"></i>'
    };
    return icons[type] || '<i class="fi-rr-square"></i>';
  }

  function setupImageBlockControls(blk) {
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
              blk.link = linkVal;
              state.tempImageFile = null;

              renderCanvas();
              renderProps();
              showSave("saved", "Uploaded");
              swal("Updated!", "Image updated successfully.", "success");
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
      document.getElementById("p-ilink").value = blk.link || "";
      revertPreview();
    });
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

    if (blk.type === "static_html") {
      renderStaticHtmlForm(blk);
      return;
    }

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

  function getLayoutOptionsForType(type) {
    switch (type) {
      case 'hero_section':
        return [
          { value: 'style_1', label: 'Style 1: Centered Bold' },
          { value: 'style_2', label: 'Style 2: Left Content, Right Image Split' },
          { value: 'style_3', label: 'Style 3: Right Content, Left Image Split' },
          { value: 'style_4', label: 'Style 4: Glassmorphic Overlay Centered' },
          { value: 'style_5', label: 'Style 5: Dynamic Slider (from Dashboard)' }
        ];
      case 'text_image_section':
        return [
          { value: 'style_1', label: 'Style 1: Left Text, Right Image' },
          { value: 'style_2', label: 'Style 2: Left Image, Right Text' },
          { value: 'style_3', label: 'Style 3: Stacked Centered' },
          { value: 'style_4', label: 'Style 4: Modern Cards Split' }
        ];
      case 'features_section':
        return [
          { value: 'style_1', label: 'Style 1: 3-Column Grid with Icons' },
          { value: 'style_2', label: 'Style 2: 4-Column Grid' },
          { value: 'style_3', label: 'Style 3: Alternating Rows' },
          { value: 'style_4', label: 'Style 4: Bordered Card Grid' }
        ];
      case 'why_choose_us_section':
        return [
          { value: 'style_1', label: 'Style 1: Left Image, Right Benefits' },
          { value: 'style_2', label: 'Style 2: Left Benefits, Right Image' },
          { value: 'style_3', label: 'Style 3: 3-Column Benefit Grid' },
          { value: 'style_4', label: 'Style 4: Minimalist Checklist' }
        ];
      case 'about_doctor_section':
        return [
          { value: 'style_1', label: 'Style 1: Centered Profile Card' },
          { value: 'style_2', label: 'Style 2: Full-width Split Section' },
          { value: 'style_3', label: 'Style 3: Bio Grid Layout' },
          { value: 'style_4', label: 'Style 4: Modern Minimalist Bordered Card' }
        ];
      case 'services_section':
        return [
          { value: 'style_1', label: 'Style 1: 3-Column Cards Grid' },
          { value: 'style_2', label: 'Style 2: 2-Column List with Icons' },
          { value: 'style_3', label: 'Style 3: Minimalist Borderless Grid' },
          { value: 'style_4', label: 'Style 4: Modern Dark Card Grid' }
        ];
      case 'spacer_section':
        return [
          { value: 'style_1', label: 'Style 1: Empty Vertical Space' },
          { value: 'style_2', label: 'Style 2: Solid Light Divider Line' },
          { value: 'style_3', label: 'Style 3: Dashed Divider Line' },
          { value: 'style_4', label: 'Style 4: Gradient Accent Line' }
        ];
      case 'faq_section':
        return [
          { value: 'style_1', label: 'Style 1: Clean Bordered Accordion' },
          { value: 'style_2', label: 'Style 2: Minimal Underlined FAQ' },
          { value: 'style_3', label: 'Style 3: 2-Column Grid FAQ' },
          { value: 'style_4', label: 'Style 4: Floating Shadow Cards' }
        ];
      case 'testimonials_section':
        return [
          { value: 'style_1', label: 'Style 1: 2-Column Cards' },
          { value: 'style_2', label: 'Style 2: 3-Column Grid Cards' },
          { value: 'style_3', label: 'Style 3: Large Centered Single Quote' },
          { value: 'style_4', label: 'Style 4: Modern Profile Card Testimonials' }
        ];
      case 'cta_section':
        return [
          { value: 'style_1', label: 'Style 1: Full-Width Colored Banner' },
          { value: 'style_2', label: 'Style 2: Rounded Container Card' },
          { value: 'style_3', label: 'Style 3: Inline Text & Button Split' },
          { value: 'style_4', label: 'Style 4: Outlined Accent Card' }
        ];
      case 'contact_section':
        return [
          { value: 'style_1', label: 'Style 1: Left Info Cards, Right Map' },
          { value: 'style_2', label: 'Style 2: Left Map, Right Info Cards' },
          { value: 'style_3', label: 'Style 3: Stacked Grid Cards & Map' },
          { value: 'style_4', label: 'Style 4: 3-Column Info Layout' }
        ];
      default:
        return [
          { value: 'style_1', label: 'Style 1: Default Layout' }
        ];
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
    const layoutOptions = getLayoutOptionsForType(sec.type);
    
    let extraSliderInfoHtml = "";
    if (sec.type === 'hero_section' && st.layout_style === 'style_5') {
      extraSliderInfoHtml = `
        <div style="background:var(--accent-dim); border:1px solid var(--accent-mid); padding:10px; border-radius:var(--radius-md); font-size:11px; color:var(--text-1); line-height:1.5; margin-bottom:12px;">
          <i class="fi-rr-info" style="color:var(--accent); font-size:13px; vertical-align:middle; margin-right:4px;"></i>
          <strong>Dynamic Slider Active:</strong> Slide content is loaded dynamically from your dashboard slides.
          <div style="margin-top:6px;">
            <a href="/dashboard/appearance/?type=sliders" target="_top" style="color:var(--accent); font-weight:600; text-decoration:none;">Go to Manage Sliders &rarr;</a>
          </div>
        </div>
      `;
    }

    propsBody.innerHTML = `
        <div class="fg"><label>Section Name</label><input type="text" class="fc" id="p-slbl" value="${sec.label || "Section"}"></div>
        <div class="fg">
          <label>Layout Style</label>
          <select class="fc" id="p-s-layout-style">
            ${layoutOptions.map(opt => `<option value="${opt.value}" ${st.layout_style === opt.value ? "selected" : ""}>${opt.label}</option>`).join("")}
          </select>
        </div>
        ${extraSliderInfoHtml}
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
    bind("p-s-layout-style", "change", (v) => {
      snap();
      sec.settings.layout_style = v;
      renderCanvas();
      renderProps();
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

  function renderStaticHtmlForm(blk) {
    const tempParser = new DOMParser();
    const tempDoc = tempParser.parseFromString(blk.content || "", "text/html");
    
    // Check type of container
    const isFaq = tempDoc.querySelector(".faq-container, .faq-item");
    const isTestimonial = tempDoc.querySelector(".testimonials-container, .testimonial-card");
    const isWhyChooseUs = tempDoc.querySelector(".why-container, .why-step");
    const isCredentials = tempDoc.querySelector(".credentials-container, .credential-item");
    const isContact = tempDoc.querySelector(".contacts-container, .contact-card");
    const isServices = tempDoc.querySelector(".services-container, .service-item");

    if (isFaq) {
      renderFaqSidebarForm(blk, tempDoc);
    } else if (isTestimonial) {
      renderTestimonialsSidebarForm(blk, tempDoc);
    } else if (isWhyChooseUs) {
      renderWhyChooseUsSidebarForm(blk, tempDoc);
    } else if (isCredentials) {
      renderCredentialsSidebarForm(blk, tempDoc);
    } else if (isContact) {
      renderContactSidebarForm(blk, tempDoc);
    } else if (isServices) {
      renderServicesSidebarForm(blk, tempDoc);
    } else {
      // Default: Raw HTML Editor
      propsBody.innerHTML = `
        <div class="fg"><label>Block Type</label>
            <input class="fc" value="CUSTOM HTML" readonly style="opacity:.5;cursor:default;"></div>
        <div class="fg"><label>Width</label>
            <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
        </div><div class="divider"></div>
        <div class="fg"><label>HTML Code</label>
            <textarea class="fc" id="p-html" rows="14" style="height:280px;font-family:monospace;font-size:12px;line-height:1.5;">${blk.content || ""}</textarea>
        </div>`;
      
      bind("p-w", "change", (v) => {
        snap();
        blk.width = parseInt(v);
        renderCanvas();
      });
      bind("p-html", "input", (v) => {
        blk.content = v;
        renderCanvas();
      });
    }
  }

  function renderFaqSidebarForm(blk, tempDoc) {
    const container = tempDoc.querySelector(".faq-container") || tempDoc.body;
    const items = Array.from(container.querySelectorAll(".faq-item"));
    
    let h = `
      <div class="fg"><label>Block Type</label>
        <input class="fc" value="FAQ ACCORDION" readonly style="opacity:.5;cursor:default;">
      </div>
      <div class="fg"><label>Width</label>
        <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
      </div>
      <div class="divider"></div>
      <div class="section-title-label">Questions &amp; Answers</div>
      <div class="list-editor-items" style="display:flex;flex-direction:column;gap:12px;">
    `;

    items.forEach((item, idx) => {
      const qEl = item.querySelector(".faq-question");
      const aEl = item.querySelector(".faq-answer");
      const qVal = qEl ? qEl.textContent : "";
      const aVal = aEl ? aEl.innerHTML : "";

      h += `
        <div class="list-item-card" style="background:var(--bg-2);padding:10px;border-radius:var(--radius-md);border:1px solid var(--border);position:relative;">
          <button class="ctrl-btn danger" style="position:absolute;top:5px;right:5px;width:20px;height:20px;font-size:10px;" data-del-idx="${idx}" title="Delete Item">&times;</button>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Question ${idx+1}</label>
            <input type="text" class="fc faq-q-input" data-idx="${idx}" value="${escapeHtml(qVal)}" style="font-size:12px;padding:6px 8px;">
          </div>
          <div class="fg" style="margin-bottom:0;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Answer</label>
            <textarea class="fc faq-a-input" data-idx="${idx}" style="font-size:12px;padding:6px 8px;height:60px;min-height:60px;" rows="2">${escapeHtml(aVal)}</textarea>
          </div>
        </div>
      `;
    });

    h += `
      </div>
      <button class="fc-btn" id="faq-add-item" style="margin-top:12px;width:100%;padding:8px;font-size:11px;font-weight:600;"><i class="fi-rr-plus"></i> Add Q&amp;A Item</button>
      <div class="divider"></div>
      <button class="fc-btn secondary" id="faq-raw-mode" style="width:100%;padding:6px;font-size:11px;"><i class="fi-rr-code"></i> Edit Raw HTML</button>
    `;

    propsBody.innerHTML = h;

    bind("p-w", "change", (v) => {
      snap();
      blk.width = parseInt(v);
      renderCanvas();
    });

    propsBody.querySelectorAll(".faq-q-input").forEach(input => {
      input.addEventListener("input", () => {
        const idx = parseInt(input.dataset.idx);
        const qEl = items[idx].querySelector(".faq-question");
        if (qEl) qEl.textContent = input.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      input.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".faq-a-input").forEach(textarea => {
      textarea.addEventListener("input", () => {
        const idx = parseInt(textarea.dataset.idx);
        const aEl = items[idx].querySelector(".faq-answer");
        if (aEl) aEl.innerHTML = textarea.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      textarea.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll("[data-del-idx]").forEach(btn => {
      btn.addEventListener("click", () => {
        snap();
        const idx = parseInt(btn.dataset.delIdx || btn.getAttribute("data-del-idx"));
        items[idx].remove();
        blk.content = container.outerHTML;
        renderCanvas();
        renderProps();
      });
    });

    document.getElementById("faq-add-item").addEventListener("click", () => {
      snap();
      const newItem = document.createElement("details");
      newItem.className = "faq-item";
      newItem.style.cssText = "background:#fff;padding:18px 20px;border-radius:10px;margin-bottom:10px;border:1px solid #e1e1e6;cursor:pointer;";
      newItem.innerHTML = `
        <summary class="faq-question" style="font-size:16px;font-weight:600;color:#1a1a2e;list-style:none;">New Question</summary>
        <p class="faq-answer" style="margin:10px 0 0;font-size:14px;color:#666;line-height:1.7;">New Answer text...</p>
      `;
      container.appendChild(newItem);
      blk.content = container.outerHTML;
      renderCanvas();
      renderProps();
    });

    document.getElementById("faq-raw-mode").addEventListener("click", () => {
      container.classList.remove("faq-container");
      renderProps();
    });
  }

  function renderTestimonialsSidebarForm(blk, tempDoc) {
    const container = tempDoc.querySelector(".testimonials-container") || tempDoc.body;
    const items = Array.from(container.querySelectorAll(".testimonial-card"));
    
    let h = `
      <div class="fg"><label>Block Type</label>
        <input class="fc" value="TESTIMONIALS" readonly style="opacity:.5;cursor:default;">
      </div>
      <div class="fg"><label>Width</label>
        <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
      </div>
      <div class="divider"></div>
      <div class="section-title-label">Testimonials</div>
      <div class="list-editor-items" style="display:flex;flex-direction:column;gap:12px;">
    `;

    items.forEach((item, idx) => {
      const qEl = item.querySelector(".testimonial-quote");
      const aEl = item.querySelector(".testimonial-author");
      const qVal = qEl ? qEl.textContent.replace(/^"|"$/g, '') : "";
      const aVal = aEl ? aEl.textContent.replace(/^—\s*/, '') : "";

      h += `
        <div class="list-item-card" style="background:var(--bg-2);padding:10px;border-radius:var(--radius-md);border:1px solid var(--border);position:relative;">
          <button class="ctrl-btn danger" style="position:absolute;top:5px;right:5px;width:20px;height:20px;font-size:10px;" data-del-idx="${idx}" title="Delete Item">&times;</button>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Author / Client</label>
            <input type="text" class="fc t-author-input" data-idx="${idx}" value="${escapeHtml(aVal)}" style="font-size:12px;padding:6px 8px;">
          </div>
          <div class="fg" style="margin-bottom:0;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Quote</label>
            <textarea class="fc t-quote-input" data-idx="${idx}" style="font-size:12px;padding:6px 8px;height:60px;min-height:60px;" rows="2">${escapeHtml(qVal)}</textarea>
          </div>
        </div>
      `;
    });

    h += `
      </div>
      <button class="fc-btn" id="t-add-item" style="margin-top:12px;width:100%;padding:8px;font-size:11px;font-weight:600;"><i class="fi-rr-plus"></i> Add Testimonial</button>
      <div class="divider"></div>
      <button class="fc-btn secondary" id="t-raw-mode" style="width:100%;padding:6px;font-size:11px;"><i class="fi-rr-code"></i> Edit Raw HTML</button>
    `;

    propsBody.innerHTML = h;

    bind("p-w", "change", (v) => {
      snap();
      blk.width = parseInt(v);
      renderCanvas();
    });

    propsBody.querySelectorAll(".t-author-input").forEach(input => {
      input.addEventListener("input", () => {
        const idx = parseInt(input.dataset.idx);
        const aEl = items[idx].querySelector(".testimonial-author");
        if (aEl) aEl.textContent = "— " + input.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      input.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".t-quote-input").forEach(textarea => {
      textarea.addEventListener("input", () => {
        const idx = parseInt(textarea.dataset.idx);
        const qEl = items[idx].querySelector(".testimonial-quote");
        if (qEl) qEl.textContent = `"${textarea.value}"`;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      textarea.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll("[data-del-idx]").forEach(btn => {
      btn.addEventListener("click", () => {
        snap();
        const idx = parseInt(btn.dataset.delIdx || btn.getAttribute("data-del-idx"));
        items[idx].remove();
        blk.content = container.outerHTML;
        renderCanvas();
        renderProps();
      });
    });

    document.getElementById("t-add-item").addEventListener("click", () => {
      snap();
      const newItem = document.createElement("div");
      newItem.className = "testimonial-card";
      newItem.style.cssText = `flex:1;min-width:240px;background:#f8f9fa;padding:22px;border-radius:12px;border-left:4px solid ${state.themeColor};`;
      newItem.innerHTML = `
        <p class="testimonial-quote" style="font-style:italic;font-size:15px;color:#444;line-height:1.7;margin:0 0 12px;">"New client quote..."</p>
        <strong class="testimonial-author" style="font-size:13px;color:#222;">— Client Name, Role</strong>
      `;
      container.appendChild(newItem);
      blk.content = container.outerHTML;
      renderCanvas();
      renderProps();
    });

    document.getElementById("t-raw-mode").addEventListener("click", () => {
      container.classList.remove("testimonials-container");
      renderProps();
    });
  }

  function renderWhyChooseUsSidebarForm(blk, tempDoc) {
    const container = tempDoc.querySelector(".why-container") || tempDoc.body;
    const items = Array.from(container.querySelectorAll(".why-step"));
    
    let h = `
      <div class="fg"><label>Block Type</label>
        <input class="fc" value="WHY CHOOSE US STEPS" readonly style="opacity:.5;cursor:default;">
      </div>
      <div class="fg"><label>Width</label>
        <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
      </div>
      <div class="divider"></div>
      <div class="section-title-label">Why Choose Us Items</div>
      <div class="list-editor-items" style="display:flex;flex-direction:column;gap:12px;">
    `;

    items.forEach((item, idx) => {
      const iconEl = item.querySelector(".why-step-icon");
      const titleEl = item.querySelector(".why-step-title");
      const titleVal = titleEl ? titleEl.textContent : "";
      
      let iconClass = "fi-rr-badge-check";
      if (iconEl) {
        const classes = Array.from(iconEl.classList);
        const match = classes.find(c => c.startsWith("fi-rr-"));
        if (match) iconClass = match;
      }

      h += `
        <div class="list-item-card" style="background:var(--bg-2);padding:10px;border-radius:var(--radius-md);border:1px solid var(--border);position:relative;">
          <button class="ctrl-btn danger" style="position:absolute;top:5px;right:5px;width:20px;height:20px;font-size:10px;" data-del-idx="${idx}" title="Delete Item">&times;</button>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Title</label>
            <input type="text" class="fc why-title-input" data-idx="${idx}" value="${escapeHtml(titleVal)}" style="font-size:12px;padding:6px 8px;">
          </div>
          <div class="fg" style="margin-bottom:0;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Icon Class (Uicons)</label>
            <select class="fc why-icon-select" data-idx="${idx}" style="font-size:12px;padding:6px 8px;">
              ${[
                ["fi-rr-badge-check", "Badge Check"],
                ["fi-rr-time-fast", "Fast Support"],
                ["fi-rr-heart", "Heart/Care"],
                ["fi-rr-graduation-cap", "Graduation Cap"],
                ["fi-rr-award", "Award Medal"],
                ["fi-rr-briefcase", "Briefcase"],
                ["fi-rr-sparkles", "Sparkles"],
                ["fi-rr-shield-check", "Shield Check"],
                ["fi-rr-star", "Star"]
              ].map(([cls, name]) => `<option value="${cls}" ${iconClass === cls ? "selected" : ""}>${name} (${cls})</option>`).join("")}
            </select>
          </div>
        </div>
      `;
    });

    h += `
      </div>
      <button class="fc-btn" id="why-add-item" style="margin-top:12px;width:100%;padding:8px;font-size:11px;font-weight:600;"><i class="fi-rr-plus"></i> Add Benefit Item</button>
      <div class="divider"></div>
      <button class="fc-btn secondary" id="why-raw-mode" style="width:100%;padding:6px;font-size:11px;"><i class="fi-rr-code"></i> Edit Raw HTML</button>
    `;

    propsBody.innerHTML = h;

    bind("p-w", "change", (v) => {
      snap();
      blk.width = parseInt(v);
      renderCanvas();
    });

    propsBody.querySelectorAll(".why-title-input").forEach(input => {
      input.addEventListener("input", () => {
        const idx = parseInt(input.dataset.idx);
        const titleEl = items[idx].querySelector(".why-step-title");
        if (titleEl) titleEl.textContent = input.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      input.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".why-icon-select").forEach(select => {
      select.addEventListener("change", () => {
        const idx = parseInt(select.dataset.idx);
        const iconEl = items[idx].querySelector(".why-step-icon");
        if (iconEl) {
          iconEl.className = "why-step-icon " + select.value;
        }
        blk.content = container.outerHTML;
        renderCanvas();
      });
      select.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll("[data-del-idx]").forEach(btn => {
      btn.addEventListener("click", () => {
        snap();
        const idx = parseInt(btn.dataset.delIdx || btn.getAttribute("data-del-idx"));
        items[idx].remove();
        blk.content = container.outerHTML;
        renderCanvas();
        renderProps();
      });
    });

    document.getElementById("why-add-item").addEventListener("click", () => {
      snap();
      const newItem = document.createElement("div");
      newItem.className = "why-step";
      newItem.style.cssText = "display:flex;align-items:center;gap:12px;";
      newItem.innerHTML = `
        <div style="width:36px;height:36px;border-radius:50%;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:16px;">
          <i class="why-step-icon fi-rr-badge-check"></i>
        </div>
        <div style="flex:1;">
          <h5 class="why-step-title" style="margin:0;font-size:15px;font-weight:600;color:#111827;">New Benefit Highlight</h5>
        </div>
      `;
      container.appendChild(newItem);
      blk.content = container.outerHTML;
      renderCanvas();
      renderProps();
    });

    document.getElementById("why-raw-mode").addEventListener("click", () => {
      container.classList.remove("why-container");
      renderProps();
    });
  }

  function renderCredentialsSidebarForm(blk, tempDoc) {
    const container = tempDoc.querySelector(".credentials-container") || tempDoc.body;
    const items = Array.from(container.querySelectorAll(".credential-item"));
    
    let h = `
      <div class="fg"><label>Block Type</label>
        <input class="fc" value="CREDENTIALS LIST" readonly style="opacity:.5;cursor:default;">
      </div>
      <div class="fg"><label>Width</label>
        <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
      </div>
      <div class="divider"></div>
      <div class="section-title-label">Doctor Credentials</div>
      <div class="list-editor-items" style="display:flex;flex-direction:column;gap:10px;">
    `;

    items.forEach((item, idx) => {
      const iconEl = item.querySelector("i");
      let iconClass = "fi-rr-graduation-cap";
      if (iconEl) {
        const classes = Array.from(iconEl.classList);
        const match = classes.find(c => c.startsWith("fi-rr-"));
        if (match) iconClass = match;
      }
      
      const textVal = item.textContent.trim();

      h += `
        <div class="list-item-card" style="background:var(--bg-2);padding:10px;border-radius:var(--radius-md);border:1px solid var(--border);position:relative;">
          <button class="ctrl-btn danger" style="position:absolute;top:5px;right:5px;width:20px;height:20px;font-size:10px;" data-del-idx="${idx}" title="Delete Item">&times;</button>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Credential ${idx+1}</label>
            <input type="text" class="fc cred-text-input" data-idx="${idx}" value="${escapeHtml(textVal)}" style="font-size:12px;padding:6px 8px;">
          </div>
          <div class="fg" style="margin-bottom:0;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Icon</label>
            <select class="fc cred-icon-select" data-idx="${idx}" style="font-size:12px;padding:6px 8px;">
              ${[
                ["fi-rr-graduation-cap", "Graduation"],
                ["fi-rr-award", "Award / Medal"],
                ["fi-rr-briefcase", "Work / Briefcase"],
                ["fi-rr-star", "Star"],
                ["fi-rr-heart", "Heart"]
              ].map(([cls, name]) => `<option value="${cls}" ${iconClass === cls ? "selected" : ""}>${name}</option>`).join("")}
            </select>
          </div>
        </div>
      `;
    });

    h += `
      </div>
      <button class="fc-btn" id="cred-add-item" style="margin-top:12px;width:100%;padding:8px;font-size:11px;font-weight:600;"><i class="fi-rr-plus"></i> Add Credential</button>
      <div class="divider"></div>
      <button class="fc-btn secondary" id="cred-raw-mode" style="width:100%;padding:6px;font-size:11px;"><i class="fi-rr-code"></i> Edit Raw HTML</button>
    `;

    propsBody.innerHTML = h;

    bind("p-w", "change", (v) => {
      snap();
      blk.width = parseInt(v);
      renderCanvas();
    });

    propsBody.querySelectorAll(".cred-text-input").forEach(input => {
      input.addEventListener("input", () => {
        const idx = parseInt(input.dataset.idx);
        const iconSelect = propsBody.querySelector(`.cred-icon-select[data-idx="${idx}"]`);
        const iconCls = iconSelect ? iconSelect.value : "fi-rr-graduation-cap";
        items[idx].innerHTML = `<i class="${iconCls}" style="color:var(--accent)"></i> ${input.value}`;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      input.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".cred-icon-select").forEach(select => {
      select.addEventListener("change", () => {
        const idx = parseInt(select.dataset.idx);
        const textInput = propsBody.querySelector(`.cred-text-input[data-idx="${idx}"]`);
        const textVal = textInput ? textInput.value : "";
        items[idx].innerHTML = `<i class="${select.value}" style="color:var(--accent)"></i> ${textVal}`;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      select.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll("[data-del-idx]").forEach(btn => {
      btn.addEventListener("click", () => {
        snap();
        const idx = parseInt(btn.dataset.delIdx || btn.getAttribute("data-del-idx"));
        items[idx].remove();
        blk.content = container.outerHTML;
        renderCanvas();
        renderProps();
      });
    });

    document.getElementById("cred-add-item").addEventListener("click", () => {
      snap();
      const newItem = document.createElement("li");
      newItem.className = "credential-item";
      newItem.style.cssText = "font-size:14px;color:#4b5563;display:flex;align-items:center;gap:8px;";
      newItem.innerHTML = `<i class="fi-rr-graduation-cap" style="color:var(--accent)"></i> New Credential Title`;
      container.querySelector("ul").appendChild(newItem);
      blk.content = container.outerHTML;
      renderCanvas();
      renderProps();
    });

    document.getElementById("cred-raw-mode").addEventListener("click", () => {
      container.classList.remove("credentials-container");
      renderProps();
    });
  }

  function renderContactSidebarForm(blk, tempDoc) {
    const container = tempDoc.querySelector(".contacts-container") || tempDoc.body;
    const items = Array.from(container.querySelectorAll(".contact-card"));
    
    let h = `
      <div class="fg"><label>Block Type</label>
        <input class="fc" value="CONTACT CARDS" readonly style="opacity:.5;cursor:default;">
      </div>
      <div class="fg"><label>Width</label>
        <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
      </div>
      <div class="divider"></div>
      <div class="section-title-label">Contact Cards</div>
      <div class="list-editor-items" style="display:flex;flex-direction:column;gap:12px;">
    `;

    items.forEach((item, idx) => {
      const headingEl = item.querySelector("h5");
      const valueEl = item.querySelector(".contact-value");
      const iconEl = item.querySelector("i");
      
      const headVal = headingEl ? headingEl.textContent : "";
      const valVal = valueEl ? valueEl.textContent : "";
      
      let iconClass = "fi-rr-marker";
      if (iconEl) {
        const classes = Array.from(iconEl.classList);
        const match = classes.find(c => c.startsWith("fi-rr-"));
        if (match) iconClass = match;
      }

      h += `
        <div class="list-item-card" style="background:var(--bg-2);padding:10px;border-radius:var(--radius-md);border:1px solid var(--border);position:relative;">
          <button class="ctrl-btn danger" style="position:absolute;top:5px;right:5px;width:20px;height:20px;font-size:10px;" data-del-idx="${idx}" title="Delete Item">&times;</button>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Title</label>
            <input type="text" class="fc c-head-input" data-idx="${idx}" value="${escapeHtml(headVal)}" style="font-size:12px;padding:6px 8px;">
          </div>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Value</label>
            <input type="text" class="fc c-val-input" data-idx="${idx}" value="${escapeHtml(valVal)}" style="font-size:12px;padding:6px 8px;">
          </div>
          <div class="fg" style="margin-bottom:0;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Icon</label>
            <select class="fc c-icon-select" data-idx="${idx}" style="font-size:12px;padding:6px 8px;">
              ${[
                ["fi-rr-marker", "Location Marker"],
                ["fi-rr-phone-call", "Phone Call"],
                ["fi-rr-envelope", "Envelope / Mail"],
                ["fi-rr-time-fast", "Hours / Clock"]
              ].map(([cls, name]) => `<option value="${cls}" ${iconClass === cls ? "selected" : ""}>${name}</option>`).join("")}
            </select>
          </div>
        </div>
      `;
    });

    h += `
      </div>
      <button class="fc-btn" id="c-add-item" style="margin-top:12px;width:100%;padding:8px;font-size:11px;font-weight:600;"><i class="fi-rr-plus"></i> Add Contact Card</button>
      <div class="divider"></div>
      <button class="fc-btn secondary" id="c-raw-mode" style="width:100%;padding:6px;font-size:11px;"><i class="fi-rr-code"></i> Edit Raw HTML</button>
    `;

    propsBody.innerHTML = h;

    bind("p-w", "change", (v) => {
      snap();
      blk.width = parseInt(v);
      renderCanvas();
    });

    propsBody.querySelectorAll(".c-head-input").forEach(input => {
      input.addEventListener("input", () => {
        const idx = parseInt(input.dataset.idx);
        const headingEl = items[idx].querySelector("h5");
        if (headingEl) headingEl.textContent = input.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      input.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".c-val-input").forEach(input => {
      input.addEventListener("input", () => {
        const idx = parseInt(input.dataset.idx);
        const valueEl = items[idx].querySelector(".contact-value");
        if (valueEl) valueEl.textContent = input.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      input.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".c-icon-select").forEach(select => {
      select.addEventListener("change", () => {
        const idx = parseInt(select.dataset.idx);
        const iconEl = items[idx].querySelector("i");
        if (iconEl) {
          iconEl.className = select.value;
        }
        blk.content = container.outerHTML;
        renderCanvas();
      });
      select.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll("[data-del-idx]").forEach(btn => {
      btn.addEventListener("click", () => {
        snap();
        const idx = parseInt(btn.dataset.delIdx || btn.getAttribute("data-del-idx"));
        items[idx].remove();
        blk.content = container.outerHTML;
        renderCanvas();
        renderProps();
      });
    });

    document.getElementById("c-add-item").addEventListener("click", () => {
      snap();
      const newItem = document.createElement("div");
      newItem.className = "contact-card";
      newItem.style.cssText = "background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb;display:flex;align-items:start;gap:12px;";
      newItem.innerHTML = `
        <i class="fi-rr-marker" style="color:var(--accent);font-size:20px;margin-top:2px;"></i>
        <div>
          <h5 style="margin:0 0 4px;font-size:15px;font-weight:600;color:#111827;">Contact Title</h5>
          <p class="contact-value" style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">Contact Value details...</p>
        </div>
      `;
      container.appendChild(newItem);
      blk.content = container.outerHTML;
      renderCanvas();
      renderProps();
    });

    document.getElementById("c-raw-mode").addEventListener("click", () => {
      container.classList.remove("contacts-container");
      renderProps();
    });
  }

  function renderServicesSidebarForm(blk, tempDoc) {
    const container = tempDoc.querySelector(".services-container") || tempDoc.body;
    const items = Array.from(container.querySelectorAll(".service-item"));
    
    let h = `
      <div class="fg"><label>Block Type</label>
        <input class="fc" value="SERVICES GRID" readonly style="opacity:.5;cursor:default;">
      </div>
      <div class="fg"><label>Width</label>
        <select class="fc" id="p-w">${[25, 33, 50, 66, 75, 100].map((v) => `<option value="${v}" ${blk.width == v ? "selected" : ""}>${v}%</option>`).join("")}</select>
      </div>
      <div class="divider"></div>
      <div class="section-title-label">Services / Treatments</div>
      <div class="list-editor-items" style="display:flex;flex-direction:column;gap:12px;">
    `;

    items.forEach((item, idx) => {
      const titleEl = item.querySelector(".service-title");
      const descEl = item.querySelector(".service-desc");
      const iconEl = item.querySelector("i");
      
      const titleVal = titleEl ? titleEl.textContent : "";
      const descVal = descEl ? descEl.textContent : "";
      
      let iconClass = "fi-rr-sparkles";
      if (iconEl) {
        const classes = Array.from(iconEl.classList);
        const match = classes.find(c => c.startsWith("fi-rr-"));
        if (match) iconClass = match;
      }

      h += `
        <div class="list-item-card" style="background:var(--bg-2);padding:10px;border-radius:var(--radius-md);border:1px solid var(--border);position:relative;">
          <button class="ctrl-btn danger" style="position:absolute;top:5px;right:5px;width:20px;height:20px;font-size:10px;" data-del-idx="${idx}" title="Delete Item">&times;</button>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Service Title</label>
            <input type="text" class="fc s-title-input" data-idx="${idx}" value="${escapeHtml(titleVal)}" style="font-size:12px;padding:6px 8px;">
          </div>
          <div class="fg" style="margin-bottom:8px;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Description</label>
            <textarea class="fc s-desc-input" data-idx="${idx}" style="font-size:12px;padding:6px 8px;height:50px;min-height:50px;" rows="2">${escapeHtml(descVal)}</textarea>
          </div>
          <div class="fg" style="margin-bottom:0;">
            <label style="font-size:10px;font-weight:600;margin-bottom:2px;display:block;">Icon</label>
            <select class="fc s-icon-select" data-idx="${idx}" style="font-size:12px;padding:6px 8px;">
              ${[
                ["fi-rr-sparkles", "Sparkles"],
                ["fi-rr-scissors", "Scissors / Cut"],
                ["fi-rr-magic-wand", "Wand / Cosmetic"],
                ["fi-rr-heart", "Heart / Health"],
                ["fi-rr-user-md", "Doctor"],
                ["fi-rr-shield-check", "Shield Check"],
                ["fi-rr-microscope", "Lab / Tech"],
                ["fi-rr-capsules", "Capsules / Medicine"]
              ].map(([cls, name]) => `<option value="${cls}" ${iconClass === cls ? "selected" : ""}>${name} (${cls})</option>`).join("")}
            </select>
          </div>
        </div>
      `;
    });

    h += `
      </div>
      <button class="fc-btn" id="s-add-item" style="margin-top:12px;width:100%;padding:8px;font-size:11px;font-weight:600;"><i class="fi-rr-plus"></i> Add Service Card</button>
      <div class="divider"></div>
      <button class="fc-btn secondary" id="s-raw-mode" style="width:100%;padding:6px;font-size:11px;"><i class="fi-rr-code"></i> Edit Raw HTML</button>
    `;

    propsBody.innerHTML = h;

    bind("p-w", "change", (v) => {
      snap();
      blk.width = parseInt(v);
      renderCanvas();
    });

    propsBody.querySelectorAll(".s-title-input").forEach(input => {
      input.addEventListener("input", () => {
        const idx = parseInt(input.dataset.idx);
        const titleEl = items[idx].querySelector(".service-title");
        if (titleEl) titleEl.textContent = input.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      input.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".s-desc-input").forEach(textarea => {
      textarea.addEventListener("input", () => {
        const idx = parseInt(textarea.dataset.idx);
        const descEl = items[idx].querySelector(".service-desc");
        if (descEl) descEl.textContent = textarea.value;
        blk.content = container.outerHTML;
        renderCanvas();
      });
      textarea.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll(".s-icon-select").forEach(select => {
      select.addEventListener("change", () => {
        const idx = parseInt(select.dataset.idx);
        const iconEl = items[idx].querySelector("i");
        if (iconEl) {
          iconEl.className = select.value;
        }
        blk.content = container.outerHTML;
        renderCanvas();
      });
      select.addEventListener("blur", snap);
    });

    propsBody.querySelectorAll("[data-del-idx]").forEach(btn => {
      btn.addEventListener("click", () => {
        snap();
        const idx = parseInt(btn.dataset.delIdx || btn.getAttribute("data-del-idx"));
        items[idx].remove();
        blk.content = container.outerHTML;
        renderCanvas();
        renderProps();
      });
    });

    document.getElementById("s-add-item").addEventListener("click", () => {
      snap();
      const newItem = document.createElement("div");
      newItem.className = "service-item";
      newItem.style.cssText = "background:#f9fafb;padding:24px;border-radius:10px;border:1px solid #e5e7eb;text-align:center;transition:var(--trans);";
      newItem.innerHTML = `
        <i class="fi-rr-sparkles" style="font-size:28px;color:var(--accent);display:inline-block;margin-bottom:12px;"></i>
        <h4 style="margin:0 0 8px;font-size:16px;font-weight:600;color:#111827;" class="service-title">New Service Title</h4>
        <p style="margin:0 0 14px;font-size:13px;color:#6b7280;line-height:1.6;" class="service-desc">Description of the new service treatment or package.</p>
        <a href="#" style="font-size:12px;font-weight:600;color:var(--accent);text-decoration:none;" class="service-link">Learn More &rarr;</a>
      `;
      container.appendChild(newItem);
      blk.content = container.outerHTML;
      renderCanvas();
      renderProps();
    });

    document.getElementById("s-raw-mode").addEventListener("click", () => {
      container.classList.remove("services-container");
      renderProps();
    });
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
