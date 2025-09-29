// --- Tiny helpers ---
const $ = (s, c = document) => c.querySelector(s);
const $$ = (s, c = document) => [...c.querySelectorAll(s)];

function setParamAndReload(key, val) {
  const url = new URL(location.href);
  if (val == null || val === "") url.searchParams.delete(key);
  else url.searchParams.set(key, val);
  location.href = url.toString();
}

// If the value looks like a URL/route, navigate there; else set ?category=
function selectCategory(value, text) {
  const btn = $("#category-text");
  if (btn) btn.textContent = text || "All Categories";

  const looksLikeURL = /^https?:\/\//i.test(value) || value.startsWith("/");
  if (!value) {
    // All categories: drop category param
    setParamAndReload("category", "");
    return;
  }
  if (looksLikeURL) {
    location.href = value;
  } else {
    setParamAndReload("category", value);
  }
}

function selectSort(value, text) {
  const btn = $("#sort-text");
  if (btn) btn.textContent = text || "Latest";
  const v = (value || "").toLowerCase();
  if (!v) setParamAndReload("sort", "");
  else setParamAndReload("sort", v);
}

function selectDate(value, text) {
  const btn = $("#date-text");
  if (btn) btn.textContent = text || "All Time";
  const v = (value || "").toLowerCase();
  // Keep clean URL for "all"
  if (!v || v === "all") setParamAndReload("date", "");
  else setParamAndReload("date", v);
}

// --- Dropdown controls (remove/add hidden & keep ARIA in sync) ---
function openDropdown(el) {
  el.classList.remove("hidden");
  el.classList.add("show");
  el.setAttribute("aria-hidden", "false");
}

function closeDropdown(el) {
  el.classList.remove("show");
  el.classList.add("hidden");
  el.setAttribute("aria-hidden", "true");
}

function toggleDropdown(type) {
  const dd = $("#" + type + "-dropdown");
  if (!dd) return;

  // Close others
  $$(".dropdown-content").forEach((el) => {
    if (el !== dd) closeDropdown(el);
  });

  if (dd.classList.contains("hidden")) openDropdown(dd);
  else closeDropdown(dd);
}

document.addEventListener("click", (e) => {
  if (!e.target.closest(".filter-dropdown")) {
    $$(".dropdown-content").forEach(closeDropdown);
  }
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") $$(".dropdown-content").forEach(closeDropdown);
});

// --- Tags ---
function handleTagClick(el, paramName = "tag") {
  const slug = el.getAttribute("data-slug") || "";
  setParamAndReload(paramName, slug);
}
