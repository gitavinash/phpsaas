// Native Theme-Matched Toast Notification System (replaces default browser alert)
window.showToast = function (message, type = "info") {
  if (!message) return;
  let container = document.getElementById("native-toast-container");
  if (!container) {
    container = document.createElement("div");
    container.id = "native-toast-container";
    container.className = "native-toast-container";
    document.body.appendChild(container);
  }

  const toast = document.createElement("div");
  toast.className = `native-toast ${type}`;

  let iconClass = "ph ph-info";
  if (type === "success") iconClass = "ph ph-check-circle";
  if (type === "error" || type === "warning") iconClass = "ph ph-warning-circle";

  toast.innerHTML = `
    <i class="${iconClass} native-toast-icon"></i>
    <span class="native-toast-msg">${message}</span>
  `;

  container.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.add("show");
  });

  const dismiss = () => {
    toast.classList.remove("show");
    setTimeout(() => {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 350);
  };

  toast.addEventListener("click", dismiss);
  setTimeout(dismiss, 3200);
};

window.alert = function (message) {
  if (!message) return;
  const msgLower = String(message).toLowerCase();
  let type = "info";
  if (
    msgLower.includes("error") ||
    msgLower.includes("wrong") ||
    msgLower.includes("invalid") ||
    msgLower.includes("failed") ||
    msgLower.includes("required") ||
    msgLower.includes("please enter") ||
    msgLower.includes("kindly enter")
  ) {
    type = "error";
  } else if (
    msgLower.includes("success") ||
    msgLower.includes("sent") ||
    msgLower.includes("saved") ||
    msgLower.includes("updated") ||
    msgLower.includes("resent")
  ) {
    type = "success";
  }
  window.showToast(message, type);
};

document.addEventListener("DOMContentLoaded", () => {
  // ---- THEME LOGIC ----
  const profileThemeToggle = document.getElementById("profile-theme-toggle");
  const body = document.body;

  const currentTheme = localStorage.getItem("theme");
  if (currentTheme === "dark") {
    body.classList.replace("light-theme", "dark-theme");
    if (profileThemeToggle) profileThemeToggle.checked = true;
  }

  const switchTheme = () => {
    if (body.classList.contains("light-theme")) {
      body.classList.replace("light-theme", "dark-theme");
      localStorage.setItem("theme", "dark");
      if (profileThemeToggle) profileThemeToggle.checked = true;
    } else {
      body.classList.replace("dark-theme", "light-theme");
      localStorage.setItem("theme", "light");
      if (profileThemeToggle) profileThemeToggle.checked = false;
    }
  };

  if (profileThemeToggle) {
    profileThemeToggle.addEventListener("change", switchTheme);
  }

  // ---- SEARCH & RECENT SEARCHES LOGIC ----
  const searchInput = document.getElementById("main-search-input");
  const searchRecent = document.getElementById("search-recent");
  const searchResults = document.getElementById("search-results");
  const queryDisplay = document.getElementById("search-query-display");

  const RECENT_SEARCHES_KEY = "recent_searches";
  const MAX_RECENT_SEARCHES = 10;
  const SUGGESTED_TOPICS_POOL = [
    "Paytm Payments Bank",
    "Fintech Regulations",
    "Peak XV",
    "Artificial Intelligence",
    "Startups & VC",
    "Indian Economy",
    "RBI Policy",
    "E-commerce",
    "Banking Sector",
    "Crypto & Blockchain",
    "SaaS Growth",
    "Deepdive Analysis",
    "Stock Market",
    "Tech News",
    "EV & Clean Tech",
  ];

  const AUTOCOMPLETE_KEYWORDS = [
    "Paytm",
    "Paytm Payments Bank",
    "Fintech",
    "Fintech Regulations",
    "Peak XV",
    "Artificial Intelligence",
    "Startups",
    "Startups & VC",
    "Indian Economy",
    "RBI",
    "RBI Policy",
    "E-commerce",
    "Banking",
    "Banking Sector",
    "Crypto",
    "Crypto & Blockchain",
    "SaaS",
    "SaaS Growth",
    "Deepdive",
    "Deepdive Analysis",
    "Stock Market",
    "Tech News",
    "EV & Clean Tech",
    "Zomato",
    "Swiggy",
    "Ola",
    "Zerodha",
    "CRED",
    "Razorpay",
    "PhonePe",
    "SEBI",
    "Inflation",
    "TCS",
    "Infosys",
    "Reliance",
  ];

  function escapeHtml(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function highlightMatch(text, query) {
    if (!query) return escapeHtml(text);
    const safeText = escapeHtml(text);
    const safeQuery = escapeHtml(query.trim());
    if (!safeQuery) return safeText;

    const regex = new RegExp(
      `(${safeQuery.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`,
      "gi",
    );
    return safeText.replace(regex, "<strong>$1</strong>");
  }

  function getRecentSearches() {
    try {
      const raw = localStorage.getItem(RECENT_SEARCHES_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  function saveRecentSearch(term) {
    if (!term || typeof term !== "string") return;
    const cleanTerm = term.trim();
    if (!cleanTerm) return;

    let searches = getRecentSearches();
    searches = searches.filter(
      (item) => item.toLowerCase() !== cleanTerm.toLowerCase(),
    );
    searches.unshift(cleanTerm);
    if (searches.length > MAX_RECENT_SEARCHES) {
      searches = searches.slice(0, MAX_RECENT_SEARCHES);
    }
    try {
      localStorage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(searches));
    } catch (e) {
      console.error("Failed to save search history:", e);
    }
  }

  function removeRecentSearch(term) {
    let searches = getRecentSearches();
    searches = searches.filter(
      (item) => item.toLowerCase() !== term.toLowerCase(),
    );
    try {
      localStorage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(searches));
    } catch (e) {}
    renderRecentOrSuggested();
  }

  function clearAllRecentSearches() {
    try {
      localStorage.removeItem(RECENT_SEARCHES_KEY);
    } catch (e) {}
    renderRecentOrSuggested();
  }

  function getRandomTopics(pool, count) {
    const shuffled = [...pool].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, count);
  }

  let autocompleteDebounceTimer = null;

  async function fetchTagSuggestions(query) {
    try {
      const response = await fetch(
        "/api/autosuggestion/?q=" + encodeURIComponent(query),
      );
      if (response.ok) {
        const data = await response.json();
        if (
          data &&
          (Array.isArray(data.suggestions) || Array.isArray(data.msg))
        ) {
          return data.suggestions || data.msg || [];
        }
      }
    } catch (e) {
      console.warn("API autosuggestion error, falling back to local pool", e);
    }
    return [];
  }

  function getAutocompleteSuggestions(query, apiTags = []) {
    if (!query) return [];
    const cleanQuery = query.toLowerCase().trim();
    if (!cleanQuery) return [];

    const recent = getRecentSearches();
    const combinedPool = Array.from(
      new Set([...apiTags, ...recent, ...AUTOCOMPLETE_KEYWORDS]),
    );

    const matches = combinedPool.filter((item) =>
      item.toLowerCase().includes(cleanQuery),
    );

    const suggestions = matches.slice(0, 6);

    const hasExact = suggestions.some(
      (item) => item.toLowerCase() === cleanQuery,
    );
    if (!hasExact) {
      suggestions.push(query.trim());
    }

    return suggestions;
  }

  async function renderAutocompleteSuggestions(query) {
    const suggestionsContainer = document.getElementById("search-suggestions");
    const suggestionsList = document.getElementById("suggestions-list");

    if (!suggestionsContainer || !suggestionsList) return;

    if (!query || !query.trim()) {
      suggestionsContainer.style.display = "none";
      if (searchRecent) searchRecent.style.display = "block";
      return;
    }

    const cleanQuery = query.trim();
    const apiTags = await fetchTagSuggestions(cleanQuery);
    const suggestions = getAutocompleteSuggestions(cleanQuery, apiTags);
    if (suggestions.length === 0) {
      suggestionsContainer.style.display = "none";
      if (searchRecent) searchRecent.style.display = "block";
      return;
    }

    let html = "";
    suggestions.forEach((item) => {
      const url = "/search/?q=" + encodeURIComponent(item);
      const highlighted = highlightMatch(item, cleanQuery);
      html += `
        <a href="${url}" class="suggestion-item native-list-item" data-search-term="${escapeHtml(item)}">
          <i class="ph ph-magnifying-glass"></i>
          <span>${highlighted}</span>
        </a>
      `;
    });

    suggestionsList.innerHTML = html;
    suggestionsContainer.style.display = "block";
    if (searchRecent) searchRecent.style.display = "none";
  }

  const clearInputBtn = document.getElementById("clear-search-input");

  function updateClearIconState() {
    if (clearInputBtn && searchInput) {
      clearInputBtn.style.display =
        searchInput.value.trim().length > 0 ? "block" : "none";
    }
  }

  function resetSearchViews() {
    const suggestionsContainer = document.getElementById("search-suggestions");
    if (suggestionsContainer) suggestionsContainer.style.display = "none";
    if (searchRecent) searchRecent.style.display = "block";
    if (searchResults) searchResults.style.display = "none";
  }

  if (searchInput) {
    updateClearIconState();

    // When user taps/focuses on input to search a new term, clear the existing input value
    searchInput.addEventListener("focus", () => {
      if (searchInput.value.length > 0) {
        searchInput.value = "";
        updateClearIconState();
        resetSearchViews();
      }
    });

    searchInput.addEventListener("input", (e) => {
      const val = e.target.value.trim();
      updateClearIconState();
      if (val.length > 0) {
        clearTimeout(autocompleteDebounceTimer);
        autocompleteDebounceTimer = setTimeout(() => {
          renderAutocompleteSuggestions(val);
        }, 150);
        if (searchResults) searchResults.style.display = "none";
        if (queryDisplay) queryDisplay.textContent = val;
      } else {
        clearTimeout(autocompleteDebounceTimer);
        resetSearchViews();
      }
    });
  }

  if (clearInputBtn && searchInput) {
    clearInputBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      searchInput.value = "";
      updateClearIconState();
      resetSearchViews();
      searchInput.focus();
    });
  }

  function renderRecentOrSuggested() {
    const recentList = document.getElementById("recent-list");
    const recentTitle = document.getElementById("recent-title");
    const clearBtn = document.getElementById("clear-recent-btn");

    if (!recentList || !recentTitle) return;

    const searches = getRecentSearches();

    if (searches.length > 0) {
      recentTitle.textContent = "Recent Searches";
      if (clearBtn) clearBtn.style.display = "inline-block";

      let html = "";
      searches.slice(0, MAX_RECENT_SEARCHES).forEach((term) => {
        const url = "/search/?q=" + encodeURIComponent(term);
        html += `
          <div class="recent-item-wrapper">
            <a href="${url}" class="recent-item native-list-item" data-search-term="${escapeHtml(term)}">
              <i class="ph ph-clock"></i>
              <span class="recent-text">${escapeHtml(term)}</span>
            </a>
            <button type="button" class="remove-recent-btn" data-remove-term="${escapeHtml(term)}" aria-label="Remove search item">
              <i class="ph ph-x"></i>
            </button>
          </div>
        `;
      });
      recentList.innerHTML = html;
    } else {
      recentTitle.textContent = "Suggested Topics";
      if (clearBtn) clearBtn.style.display = "none";

      const randomTopics = getRandomTopics(SUGGESTED_TOPICS_POOL, 5);
      let html = "";
      randomTopics.forEach((topic) => {
        const url = "/search/?q=" + encodeURIComponent(topic);
        html += `
          <a href="${url}" class="recent-item native-list-item suggested-topic-item" data-search-term="${escapeHtml(topic)}">
            <i class="ph ph-sparkle"></i>
            <span>${escapeHtml(topic)}</span>
          </a>
        `;
      });
      recentList.innerHTML = html;
    }
  }

  // Save query from URL if q is present
  const urlParams = new URLSearchParams(window.location.search);
  const qParam = urlParams.get("q");
  if (qParam && qParam.trim()) {
    saveRecentSearch(qParam.trim());
  }

  // Handle Search Form Submit
  const searchForms = document.querySelectorAll(".search-form");
  searchForms.forEach((form) => {
    form.addEventListener("submit", () => {
      const input = form.querySelector("input[name='q']");
      if (input && input.value.trim()) {
        saveRecentSearch(input.value.trim());
      }
    });
  });

  // Delegated click handler for search items (recent, suggested, or autocomplete)
  document.addEventListener("click", (e) => {
    const removeBtn = e.target.closest(".remove-recent-btn");
    if (removeBtn) {
      e.preventDefault();
      e.stopPropagation();
      const term = removeBtn.getAttribute("data-remove-term");
      if (term) removeRecentSearch(term);
      return;
    }

    const searchLink = e.target.closest("[data-search-term]");
    if (searchLink) {
      const term = searchLink.getAttribute("data-search-term");
      if (term) saveRecentSearch(term);
    }
  });

  // Clear All Button
  const clearBtn = document.getElementById("clear-recent-btn");
  if (clearBtn) {
    clearBtn.addEventListener("click", () => {
      clearAllRecentSearches();
    });
  }

  // Initial render on load
  renderRecentOrSuggested();

  // ---- NATIVE AUTH MODAL LOGIC ----
  function showAuthModal(
    redirectUrl = "/login/",
    message = "Please log in to save articles to your account.",
  ) {
    let modalOverlay = document.getElementById("native-auth-modal");
    if (!modalOverlay) {
      modalOverlay = document.createElement("div");
      modalOverlay.id = "native-auth-modal";
      modalOverlay.className = "native-modal-overlay";
      modalOverlay.innerHTML = `
        <div class="native-modal-sheet">
          <div class="native-modal-drag-handle"></div>
          <div class="native-modal-icon">
            <i class="ph ph-lock-key"></i>
          </div>
          <h3 class="native-modal-title">Login Required</h3>
          <p class="native-modal-desc" id="native-modal-desc">${escapeHtml(message)}</p>
          <div class="native-modal-actions">
            <a href="#" id="native-modal-login-btn" class="native-modal-btn primary-btn">
              Log In / Sign Up
            </a>
            <button type="button" id="native-modal-cancel-btn" class="native-modal-btn cancel-btn">
              Cancel
            </button>
          </div>
        </div>
      `;
      document.body.appendChild(modalOverlay);

      const cancelBtn = modalOverlay.querySelector("#native-modal-cancel-btn");
      if (cancelBtn) {
        cancelBtn.addEventListener("click", closeAuthModal);
      }

      modalOverlay.addEventListener("click", (e) => {
        if (e.target === modalOverlay) {
          closeAuthModal();
        }
      });
    }

    const descEl = modalOverlay.querySelector("#native-modal-desc");
    if (descEl) descEl.textContent = message;

    const loginBtn = modalOverlay.querySelector("#native-modal-login-btn");
    if (loginBtn) {
      loginBtn.href = "/login/?redirect_url=" + encodeURIComponent(redirectUrl);
    }

    requestAnimationFrame(() => {
      modalOverlay.classList.add("active");
    });
  }

  function closeAuthModal() {
    const modalOverlay = document.getElementById("native-auth-modal");
    if (modalOverlay) {
      modalOverlay.classList.remove("active");
    }
  }

  // ---- BOOKMARK NEWS LOGIC ----
  const bookmarkBtn = document.getElementById("bookmarkNews");
  if (bookmarkBtn) {
    const refId =
      bookmarkBtn.getAttribute("data-id") ||
      bookmarkBtn.getAttribute("data-ref-id");
    const refType = bookmarkBtn.getAttribute("data-ref-type") || "post";
    const icon = bookmarkBtn.querySelector("i");

    if (refId) {
      fetch(
        `/api/bookmark/?action=check&ref_id=${encodeURIComponent(refId)}&ref_type=${encodeURIComponent(refType)}`,
      )
        .then((res) => res.json())
        .then((data) => {
          if (data && data.bookmarked) {
            bookmarkBtn.classList.add("active");
            if (icon) {
              icon.className = "ph-fill ph-bookmark-simple";
            }
          }
        })
        .catch(() => {});
    }

    bookmarkBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();

      if (!refId) return;

      fetch("/api/bookmark/?action=toggle", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `ref_id=${encodeURIComponent(refId)}&ref_type=${encodeURIComponent(refType)}`,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === 401) {
            const currentUrl =
              window.location.pathname + window.location.search;
            showAuthModal(
              currentUrl,
              data.msg || "Please log in to save articles to your account.",
            );
            return;
          }

          if (data.status === 200) {
            if (data.bookmarked) {
              bookmarkBtn.classList.add("active");
              if (icon) {
                icon.className = "ph-fill ph-bookmark-simple";
              }
            } else {
              bookmarkBtn.classList.remove("active");
              if (icon) {
                icon.className = "ph ph-bookmark-simple";
              }
            }
          }
        })
        .catch((err) => {
          console.error("Bookmark request error:", err);
        });
    });
  }

  // ---- REMOVE SAVED ARTICLE LOGIC ----
  const removeBookmarkBtns = document.querySelectorAll(".remove-bookmark-btn");
  removeBookmarkBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const refId =
        btn.getAttribute("data-news-id") || btn.getAttribute("data-ref-id");
      const refType = btn.getAttribute("data-ref-type") || "post";
      const card = btn.closest(".saved-card");
      if (refId) {
        fetch(
          `/api/bookmark/?action=remove&ref_id=${encodeURIComponent(refId)}&ref_type=${encodeURIComponent(refType)}`,
        )
          .then((res) => res.json())
          .then((data) => {
            if (data.status === 200 && card) {
              card.style.opacity = "0";
              card.style.transform = "scale(0.95)";
              card.style.transition = "all 0.3s ease";
              setTimeout(() => {
                card.remove();
                const remaining = document.querySelectorAll(".saved-card");
                if (remaining.length === 0) {
                  location.reload();
                }
              }, 300);
            }
          });
      }
    });
  });
  // ---- EMAIL LOGIN & OTP VERIFICATION LOGIC ----
  function startResendCountdown(btnId) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    let countdown = 30;
    btn.textContent = `Resend OTP in ${countdown}s`;
    btn.classList.add("disabled");
    btn.style.pointerEvents = "none";

    const timer = setInterval(() => {
      countdown--;
      btn.textContent = `Resend OTP in ${countdown}s`;
      if (countdown <= 0) {
        clearInterval(timer);
        btn.textContent = "Resend OTP";
        btn.classList.remove("disabled");
        btn.style.pointerEvents = "auto";
      }
    }, 1000);
  }

  function getURLParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    let value = urlParams.get(name);
    if (value) {
      try {
        value = decodeURIComponent(decodeURIComponent(value));
      } catch (e) {}
      if (value.startsWith("/")) return value;
    }
    return null;
  }

  // ---- INFINITE SCROLL ENGINE ----
  const safeParseJson = async (res) => {
    const text = await res.text();
    try {
      return JSON.parse(text);
    } catch (e) {
      const jsonStart = text.indexOf("{");
      const jsonEnd = text.lastIndexOf("}");
      if (jsonStart !== -1 && jsonEnd !== -1 && jsonEnd > jsonStart) {
        const cleanJson = text.substring(jsonStart, jsonEnd + 1);
        try {
          return JSON.parse(cleanJson);
        } catch (e2) {}
      }
      throw new Error("Invalid JSON response");
    }
  };

  function bindRemoveBookmarkEvent(btn) {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const refId =
        btn.getAttribute("data-news-id") || btn.getAttribute("data-ref-id");
      const refType = btn.getAttribute("data-ref-type") || "post";
      const card = btn.closest(".saved-card");
      if (refId) {
        fetch(
          `/api/bookmark/?action=remove&ref_id=${encodeURIComponent(refId)}&ref_type=${encodeURIComponent(refType)}`,
        )
          .then((res) => safeParseJson(res))
          .then((data) => {
            if (data.status === 200 && card) {
              card.style.opacity = "0";
              card.style.transform = "scale(0.95)";
              card.style.transition = "all 0.3s ease";
              setTimeout(() => {
                card.remove();
                const remaining = document.querySelectorAll(".saved-card");
                if (remaining.length === 0) {
                  location.reload();
                }
              }, 300);
            }
          });
      }
    });
  }

  const sentinel = document.getElementById("infinite-scroll-sentinel");
  if (sentinel) {
    let isLoading = false;
    let currentPage = parseInt(sentinel.getAttribute("data-page") || "1", 10);
    const totalPages = parseInt(
      sentinel.getAttribute("data-total-pages") || "1",
      10,
    );
    const feedType = sentinel.getAttribute("data-feed-type") || "";
    const container = document.querySelector(".vertical-feed");

    // Track already rendered URLs / news_ids to prevent duplicate cards
    const renderedItemKeys = new Set();
    if (container) {
      container.querySelectorAll("a.list-card, .saved-card").forEach((el) => {
        const href =
          el.getAttribute("href") ||
          el.querySelector("a")?.getAttribute("href");
        const newsId = el.getAttribute("data-news-id");
        if (href) renderedItemKeys.add(href);
        if (newsId) renderedItemKeys.add("id_" + newsId);
      });
    }

    if (currentPage >= totalPages) {
      sentinel.style.display = "none";
    }

    const loadMoreItems = async () => {
      if (isLoading) return;
      if (currentPage >= totalPages) {
        sentinel.style.display = "none";
        return;
      }

      isLoading = true;
      const nextPage = currentPage + 1;

      let fetchUrl = "";
      if (feedType === "category") {
        const path = window.location.pathname.replace(/\/$/, "");
        fetchUrl = `${path}/?page=${nextPage}&ajax=1`;
      } else if (feedType === "search") {
        const query = sentinel.getAttribute("data-query") || "";
        fetchUrl = `/search/?q=${encodeURIComponent(query)}&page=${nextPage}&ajax=1`;
      } else if (feedType === "saved") {
        fetchUrl = `/saved/?page=${nextPage}&ajax=1`;
      }

      if (!fetchUrl) {
        isLoading = false;
        return;
      }

      sentinel.innerHTML =
        '<div style="padding: 16px; text-align: center;"><i class="ph ph-circle-notch ph-spin" style="font-size: 1.5rem; color: var(--accent-primary);"></i></div>';
      sentinel.style.display = "block";

      try {
        const res = await fetch(fetchUrl);
        const data = await safeParseJson(res);

        if (
          data.status === 200 &&
          Array.isArray(data.items) &&
          data.items.length > 0
        ) {
          data.items.forEach((item) => {
            const itemKey =
              item.link_url || (item.news_id ? "id_" + item.news_id : "");
            if (itemKey && renderedItemKeys.has(itemKey)) {
              return; // Skip duplicate content
            }
            if (itemKey) renderedItemKeys.add(itemKey);

            const paidBadge =
              item.news_paid == 1
                ? " &bull; <span class='premium-badge'><i class='ph-fill ph-crown'></i> Premium</span>"
                : "";

            if (feedType === "saved") {
              const publishedMeta = item.published_date
                ? ` &bull; ${item.published_date}`
                : "";
              const cardHtml = `
                <div class="saved-card" data-news-id="${item.news_id}">
                    <a href="${item.link_url}" class="list-card native-list-item">
                        <div class="list-content">
                            <div class="list-meta">
                                <span class="tag-text">${item.category_title || "News"}</span>
                                ${publishedMeta}
                                ${paidBadge}
                            </div>
                            <h4 class="list-title">${item.news_title}</h4>
                        </div>
                        ${item.news_image ? `<img src="${item.news_image}" alt="${item.news_title}" class="list-img" />` : ""}
                    </a>
                    <button type="button" class="remove-btn remove-bookmark-btn" data-news-id="${item.news_id}" data-ref-type="${item.ref_type || "post"}" aria-label="Remove saved article">
                        <i class="ph ph-trash"></i>
                    </button>
                </div>`;
              const tempDiv = document.createElement("div");
              tempDiv.innerHTML = cardHtml.trim();
              const newCard = tempDiv.firstElementChild;

              const removeBtn = newCard.querySelector(".remove-bookmark-btn");
              if (removeBtn) {
                bindRemoveBookmarkEvent(removeBtn);
              }
              if (container) container.appendChild(newCard);
            } else {
              const timeMeta = item.news_published || item.published_at || "";
              const cardHtml = `
                <a href="${item.link_url}" class="list-card native-list-item">
                    <div class="list-content">
                        <div class="list-meta">
                            <span class="tag-text">${item.category_title || "News"}</span> &bull; ${timeMeta}${paidBadge}
                        </div>
                        <h4 class="list-title">${item.news_title}</h4>
                    </div>
                    ${item.news_image ? `<img src="${item.news_image}" alt="${item.news_title}" class="list-img" />` : ""}
                </a>`;
              const tempDiv = document.createElement("div");
              tempDiv.innerHTML = cardHtml.trim();
              if (container) container.appendChild(tempDiv.firstElementChild);
            }
          });

          currentPage = nextPage;
          sentinel.setAttribute("data-page", currentPage.toString());

          if (currentPage >= (data.totalPages || totalPages)) {
            sentinel.style.display = "none";
          } else {
            sentinel.innerHTML = "";
          }
        } else {
          sentinel.style.display = "none";
        }
      } catch (err) {
        console.error("Infinite scroll error:", err);
        sentinel.style.display = "none";
      } finally {
        isLoading = false;
      }
    };

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting) {
          loadMoreItems();
        }
      },
      { rootMargin: "250px" },
    );

    observer.observe(sentinel);
  }
});
