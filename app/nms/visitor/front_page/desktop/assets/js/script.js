document.addEventListener("DOMContentLoaded", () => {
  // 1. Navbar Scroll Effect & Smooth Anchors
  const header = document.querySelector(".site-header");
  if (header) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 50) {
        header.style.boxShadow = "0 2px 10px rgba(0,0,0,0.1)";
      } else {
        header.style.boxShadow = "none";
      }
    });
  }

  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");
      if (!targetId || targetId === "#") return;
      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        e.preventDefault();
        targetElement.scrollIntoView({
          behavior: "smooth",
        });
      }
    });
  });

  // 2. Header Search Modal, Autocomplete & Drawer Navigation
  const openBtn = document.getElementById("openSearchBtn");
  const closeBtn = document.getElementById("closeSearchBtn");
  const modal = document.getElementById("searchModal");
  const input = document.getElementById("searchInput");
  const searchForm = document.getElementById("searchForm");
  const recentBlock = document.getElementById("recentSearchesBlock");
  const recentPills = document.getElementById("recentSearchPills");
  const clearRecentBtn = document.getElementById("clearRecentBtn");
  const autoBlock = document.getElementById("autocompleteBlock");
  const autoResults = document.getElementById("autocompleteResults");

  const STORAGE_KEY = "isw_recent_searches";

  const getRecentSearches = function () {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    } catch (e) {
      return [];
    }
  };

  const saveRecentSearch = function (query) {
    if (!query || !query.trim()) return;
    let searches = getRecentSearches();
    searches = searches.filter(
      (q) => q.toLowerCase() !== query.toLowerCase().trim(),
    );
    searches.unshift(query.trim());
    if (searches.length > 6) searches = searches.slice(0, 6);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(searches));
    renderRecentSearches();
  };

  const removeRecentSearch = function (query) {
    let searches = getRecentSearches();
    searches = searches.filter(
      (q) => q.toLowerCase() !== query.toLowerCase().trim(),
    );
    localStorage.setItem(STORAGE_KEY, JSON.stringify(searches));
    renderRecentSearches();
  };

  const renderRecentSearches = function () {
    if (!recentBlock || !recentPills) return;
    const searches = getRecentSearches();
    if (searches.length === 0) {
      recentBlock.style.display = "none";
      return;
    }
    recentBlock.style.display = "block";
    recentPills.innerHTML = searches
      .map(
        (q) => `
      <div class="recent-pill" data-query="${q.replace(/"/g, "&quot;")}">
        <span>${q}</span>
        <span class="recent-pill-remove" data-remove="${q.replace(
          /"/g,
          "&quot;",
        )}">&times;</span>
      </div>
    `,
      )
      .join("");
  };

  if (clearRecentBtn) {
    clearRecentBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      localStorage.removeItem(STORAGE_KEY);
      renderRecentSearches();
    });
  }

  if (recentPills) {
    recentPills.addEventListener("click", function (e) {
      const removeTarget = e.target.closest("[data-remove]");
      if (removeTarget) {
        e.stopPropagation();
        removeRecentSearch(removeTarget.getAttribute("data-remove"));
        return;
      }
      const pill = e.target.closest(".recent-pill");
      if (pill) {
        const query = pill.getAttribute("data-query");
        if (input) {
          input.value = query;
          if (searchForm) searchForm.submit();
        }
      }
    });
  }

  if (searchForm) {
    searchForm.addEventListener("submit", function () {
      if (input && input.value) {
        saveRecentSearch(input.value);
      }
    });
  }

  let debounceTimer = null;
  let autoController = null;
  let autoRequestSeq = 0;
  const autoCache = new Map();

  const renderAutocompleteItems = function (items) {
    if (!autoBlock || !autoResults) return;
    if (!items || items.length === 0) {
      autoBlock.style.display = "none";
      return;
    }
    autoBlock.style.display = "block";
    if (recentBlock) recentBlock.style.display = "none";

    autoResults.innerHTML = items
      .map(
        (item) => `
              <a href="${item.url}" class="autocomplete-item ${item.type}">
                <div class="autocomplete-left">
                  ${
                    item.type === "story" && item.image
                      ? `<img src="${item.image}" alt="${item.title.replace(/"/g, "&quot;")}" class="autocomplete-img" onerror="this.outerHTML='<div class=\\'autocomplete-img-placeholder\\'><svg width=\\'16\\' height=\\'16\\' viewBox=\\'0 0 24 24\\' fill=\\'none\\' stroke=\\'currentColor\\' stroke-width=\\'2\\'><rect x=\\'3\\' y=\\'3\\' width=\\'18\\' height=\\'18\\' rx=\\'2\\'/><circle cx=\\'8.5\\' cy=\\'8.5\\' r=\\'1.5\\'/><polyline points=\\'21 15 16 10 5 21\\'/></svg></div>';" />`
                      : item.type === "story"
                        ? `<div class="autocomplete-img-placeholder"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>`
                        : `<div class="autocomplete-tag-icon">#</div>`
                  }
                  <span class="autocomplete-title">${item.title}</span>
                </div>
                <span class="autocomplete-badge ${item.type}">${
                  item.type === "tag" ? "Topic Tag" : "Story"
                }</span>
              </a>
            `,
      )
      .join("");
  };

  if (input) {
    input.addEventListener("input", function () {
      const q = input.value.trim();
      clearTimeout(debounceTimer);

      if (q.length < 2) {
        if (autoController) {
          autoController.abort();
          autoController = null;
        }
        if (autoBlock) autoBlock.style.display = "none";
        renderRecentSearches();
        return;
      }

      debounceTimer = setTimeout(() => {
        const cacheKey = q.toLowerCase();
        if (autoCache.has(cacheKey)) {
          renderAutocompleteItems(autoCache.get(cacheKey));
          return;
        }

        if (autoController) {
          autoController.abort();
        }
        autoController = new AbortController();
        autoRequestSeq += 1;
        const reqSeq = autoRequestSeq;

        fetch("/?action=autocomplete&q=" + encodeURIComponent(q), {
          signal: autoController.signal,
        })
          .then((res) => {
            if (!res.ok) throw new Error("Autocomplete request failed");
            return res.json();
          })
          .then((items) => {
            if (reqSeq !== autoRequestSeq) return;
            const safeItems = Array.isArray(items) ? items : [];
            autoCache.set(cacheKey, safeItems);
            if (autoCache.size > 25) {
              const firstKey = autoCache.keys().next().value;
              autoCache.delete(firstKey);
            }
            renderAutocompleteItems(safeItems);
          })
          .catch((err) => {
            if (err && err.name === "AbortError") return;
            console.error("Autocomplete fetch error:", err);
          });
      }, 300);
    });
  }

  document.querySelectorAll(".popular-tag-item").forEach((tag) => {
    tag.addEventListener("click", function () {
      const val = this.getAttribute("data-tag");
      if (input && val) {
        input.value = val;
        if (searchForm) searchForm.submit();
      }
    });
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && modal && modal.classList.contains("active")) {
      modal.classList.remove("active");
      modal.setAttribute("aria-hidden", "true");
      if (autoBlock) autoBlock.style.display = "none";
    }
  });

  const acctDropdown = document.getElementById("userAccountDropdown");
  const acctBtn = document.getElementById("accountMenuBtn");
  if (acctDropdown && acctBtn) {
    acctBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      acctDropdown.classList.toggle("active");
    });
    document.addEventListener("click", function (e) {
      if (!acctDropdown.contains(e.target)) {
        acctDropdown.classList.remove("active");
      }
    });
  }

  const overlay = document.getElementById("mobileDrawerOverlay");
  const openLeftBtn = document.getElementById("openLeftDrawerBtn");
  const closeLeftBtn = document.getElementById("closeLeftDrawerBtn");
  const leftDrawer = document.getElementById("leftMobileDrawer");

  const openRightBtn = document.getElementById("openRightDrawerBtn");
  const closeRightBtn = document.getElementById("closeRightDrawerBtn");
  const rightDrawer = document.getElementById("rightMobileDrawer");

  const closeAllDrawers = function () {
    if (leftDrawer) leftDrawer.classList.remove("active");
    if (rightDrawer) rightDrawer.classList.remove("active");
    if (overlay) overlay.classList.remove("active");
  };

  if (openLeftBtn && leftDrawer) {
    openLeftBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      closeAllDrawers();
      leftDrawer.classList.add("active");
      if (overlay) overlay.classList.add("active");
    });
  }

  if (closeLeftBtn) closeLeftBtn.addEventListener("click", closeAllDrawers);

  if (openRightBtn && rightDrawer) {
    openRightBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      closeAllDrawers();
      rightDrawer.classList.add("active");
      if (overlay) overlay.classList.add("active");
    });
  }

  if (closeRightBtn) closeRightBtn.addEventListener("click", closeAllDrawers);
  if (overlay) overlay.addEventListener("click", closeAllDrawers);

  const appBanner = document.getElementById("mobileAppBanner");
  const dismissBannerBtn = document.getElementById("dismissAppBannerBtn");
  if (appBanner) {
    if (!localStorage.getItem("ht_app_banner_dismissed")) {
      setTimeout(function () {
        appBanner.classList.add("visible");
      }, 1500);
    }
    if (dismissBannerBtn) {
      dismissBannerBtn.addEventListener("click", function () {
        appBanner.classList.remove("visible");
        localStorage.setItem("ht_app_banner_dismissed", "true");
      });
    }
  }

  const bookmarkModal = document.getElementById("bookmarkAppModal");
  const closeBookmarkModalBtn = document.getElementById(
    "closeBookmarkModalBtn",
  );

  document.addEventListener("click", function (e) {
    if (e.target.closest(".trigger-bookmark-app")) {
      e.preventDefault();
      if (bookmarkModal) {
        bookmarkModal.classList.add("active");
        bookmarkModal.setAttribute("aria-hidden", "false");
      }
    }
  });

  if (closeBookmarkModalBtn && bookmarkModal) {
    closeBookmarkModalBtn.addEventListener("click", function () {
      bookmarkModal.classList.remove("active");
      bookmarkModal.setAttribute("aria-hidden", "true");
    });
    bookmarkModal.addEventListener("click", function (e) {
      if (e.target === bookmarkModal) {
        bookmarkModal.classList.remove("active");
        bookmarkModal.setAttribute("aria-hidden", "true");
      }
    });
  }

  document.addEventListener("click", function (e) {
    if (
      e.target.closest(
        '#openSearchBtn, .open-search-trigger, [href="#openSearch"]',
      )
    ) {
      e.preventDefault();
      if (modal) {
        modal.classList.add("active");
        modal.setAttribute("aria-hidden", "false");
        renderRecentSearches();
        if (input) input.focus();
      }
    }
  });

  if (closeBtn && modal) {
    closeBtn.addEventListener("click", function () {
      modal.classList.remove("active");
      modal.setAttribute("aria-hidden", "true");
      if (autoBlock) autoBlock.style.display = "none";
    });

    modal.addEventListener("click", function (e) {
      if (e.target === modal) {
        modal.classList.remove("active");
        modal.setAttribute("aria-hidden", "true");
        if (autoBlock) autoBlock.style.display = "none";
      }
    });
  }

  // 3. Home Page Horizontal Sliders (Trending & IPO Tales)
  const track = document.getElementById("trendingTrack");
  const prevBtn = document.getElementById("trendingPrev");
  const nextBtn = document.getElementById("trendingNext");

  if (track && prevBtn && nextBtn) {
    const getScrollAmount = () => {
      const card = track.querySelector(".trending-card-slide");
      return card ? card.offsetWidth + 20 : 300;
    };

    nextBtn.addEventListener("click", () => {
      track.scrollBy({
        left: getScrollAmount(),
        behavior: "smooth",
      });
    });

    prevBtn.addEventListener("click", () => {
      track.scrollBy({
        left: -getScrollAmount(),
        behavior: "smooth",
      });
    });
  }

  const ipoTrack = document.getElementById("ipoTrack");
  const ipoPrev = document.getElementById("ipoPrev");
  const ipoNext = document.getElementById("ipoNext");

  if (ipoTrack && ipoPrev && ipoNext) {
    const getIpoScrollAmount = () => {
      const card = ipoTrack.querySelector(".ipo-card");
      return card ? card.offsetWidth + 20 : 320;
    };

    ipoNext.addEventListener("click", () => {
      ipoTrack.scrollBy({
        left: getIpoScrollAmount(),
        behavior: "smooth",
      });
    });

    ipoPrev.addEventListener("click", () => {
      ipoTrack.scrollBy({
        left: -getIpoScrollAmount(),
        behavior: "smooth",
      });
    });
  }

  const videoTrack = document.getElementById("videoTrack");
  const videoPrev = document.getElementById("videoPrev");
  const videoNext = document.getElementById("videoNext");

  if (videoTrack && videoPrev && videoNext) {
    const getVideoScrollAmount = () => {
      const card = videoTrack.querySelector(".video-card-slide");
      return card ? card.offsetWidth + 20 : 320;
    };

    videoNext.addEventListener("click", () => {
      videoTrack.scrollBy({
        left: getVideoScrollAmount(),
        behavior: "smooth",
      });
    });

    videoPrev.addEventListener("click", () => {
      videoTrack.scrollBy({
        left: -getVideoScrollAmount(),
        behavior: "smooth",
      });
    });
  }

  const initCardSlider = (
    trackEl,
    prevBtnEl,
    nextBtnEl,
    cardSelector,
    stepCount = 4,
  ) => {
    if (!trackEl || !prevBtnEl || !nextBtnEl) return;

    const getScrollAmount = () => {
      const cards = Array.from(trackEl.querySelectorAll(cardSelector));
      if (!cards.length) return 320;

      const gap = parseFloat(
        getComputedStyle(trackEl).columnGap ||
          getComputedStyle(trackEl).gap ||
          0,
      );
      let total = 0;
      cards.slice(0, stepCount).forEach((card, index) => {
        total += card.getBoundingClientRect().width + (index > 0 ? gap : 0);
      });

      return Math.max(total, 320);
    };

    nextBtnEl.addEventListener("click", () => {
      trackEl.scrollBy({
        left: getScrollAmount(),
        behavior: "smooth",
      });
    });

    prevBtnEl.addEventListener("click", () => {
      trackEl.scrollBy({
        left: -getScrollAmount(),
        behavior: "smooth",
      });
    });
  };

  initCardSlider(
    document.getElementById("pevcTrack"),
    document.getElementById("pevcPrev"),
    document.getElementById("pevcNext"),
    ".ipo-card",
  );
  initCardSlider(
    document.getElementById("moneyCryptoTrack"),
    document.getElementById("moneyCryptoPrev"),
    document.getElementById("moneyCryptoNext"),
    ".ipo-card",
  );

  // 4. Single News Detail Page Infinite Scroll & Gallery Initialization
  const newsTrigger = document.getElementById("infiniteScrollTrigger");
  const newsFeed = document.getElementById("storyFeedContainer");

  const initStoryComponents = function (container) {
    if (window.jQuery) {
      const $gallery = window.jQuery(container).find(".news-gallery");
      if (
        $gallery.length &&
        typeof window.jQuery.fn.slick === "function" &&
        !$gallery.hasClass("slick-initialized")
      ) {
        $gallery.slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: true,
          arrows: true,
          adaptiveHeight: true,
        });
      }
    }
  };

  initStoryComponents(document);

  if (newsTrigger && newsFeed && "IntersectionObserver" in window) {
    let isFetchingNextStory = false;

    const fetchNextStory = function (url) {
      isFetchingNextStory = true;
      newsTrigger.style.display = "block";

      fetch(url)
        .then((response) => response.text())
        .then((htmlText) => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(htmlText, "text/html");
          const newArticle = doc.querySelector(".article-detail-page");

          if (newArticle) {
            newsFeed.appendChild(newArticle);
            initStoryComponents(newArticle);

            const newTrigger = doc.querySelector("#infiniteScrollTrigger");
            const newNextUrl = newTrigger
              ? newTrigger.getAttribute("data-next-url")
              : "";

            if (newNextUrl && newNextUrl !== url) {
              newsTrigger.setAttribute("data-next-url", newNextUrl);
            } else {
              newsTrigger.style.display = "none";
              newsObserver.unobserve(newsTrigger);
            }
          } else {
            newsTrigger.style.display = "none";
          }
          isFetchingNextStory = false;
        })
        .catch((err) => {
          console.error("Failed to load next story:", err);
          newsTrigger.style.display = "none";
          isFetchingNextStory = false;
        });
    };

    const newsObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !isFetchingNextStory) {
            const nextUrl = newsTrigger.getAttribute("data-next-url");
            if (nextUrl) {
              fetchNextStory(nextUrl);
            }
          }
        });
      },
      {
        rootMargin: "400px",
      },
    );

    newsObserver.observe(newsTrigger);

    window.addEventListener("scroll", function () {
      const articles = document.querySelectorAll(".article-detail-page");
      articles.forEach((art) => {
        const rect = art.getBoundingClientRect();
        if (rect.top <= 200 && rect.bottom >= 200) {
          const artUrl = art.getAttribute("data-article-url");
          const artTitle = art.getAttribute("data-article-title");
          if (artUrl && window.location.href !== artUrl) {
            window.history.replaceState(null, artTitle, artUrl);
            document.title = artTitle;
          }
        }
      });
    });
  }

  // 5. Category Page Infinite Scroll
  const categoryTrigger = document.getElementById("categoryInfiniteTrigger");
  const categoryGrid = document.getElementById("categoryNewsGrid");

  if (categoryTrigger && categoryGrid) {
    let isFetchingCategory = false;

    const fetchNextCategoryPage = function (nextUrl) {
      if (isFetchingCategory || !nextUrl) return;
      isFetchingCategory = true;

      categoryTrigger.style.display = "block";

      fetch(nextUrl)
        .then((response) => response.text())
        .then((htmlText) => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(htmlText, "text/html");
          const newCards = doc.querySelectorAll(
            "#categoryNewsGrid .category-news-card",
          );

          if (newCards && newCards.length > 0) {
            newCards.forEach((card, index) => {
              card.style.opacity = "0";
              card.style.transform = "translateY(15px)";
              card.style.transition = `opacity 0.4s ease ${
                index * 0.05
              }s, transform 0.4s ease ${index * 0.05}s`;
              categoryGrid.appendChild(card);

              requestAnimationFrame(() => {
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
              });
            });

            const nextTrigger = doc.querySelector("#categoryInfiniteTrigger");
            const nextUrlAttribute = nextTrigger
              ? nextTrigger.getAttribute("data-next-page")
              : "";

            if (nextUrlAttribute && nextUrlAttribute !== nextUrl) {
              categoryTrigger.setAttribute("data-next-page", nextUrlAttribute);
            } else {
              categoryTrigger.style.display = "none";
              categoryObserver.unobserve(categoryTrigger);
            }
          } else {
            categoryTrigger.style.display = "none";
          }
          isFetchingCategory = false;
        })
        .catch((err) => {
          console.error("Failed to load category page:", err);
          categoryTrigger.style.display = "none";
          isFetchingCategory = false;
        });
    };

    const categoryObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !isFetchingCategory) {
            const nextUrl = categoryTrigger.getAttribute("data-next-page");
            if (nextUrl) {
              fetchNextCategoryPage(nextUrl);
            }
          }
        });
      },
      {
        rootMargin: "300px",
      },
    );

    categoryObserver.observe(categoryTrigger);
  }

  // 6. Tag Page Infinite Scroll
  const tagTrigger = document.getElementById("tagInfiniteTrigger");
  const tagGrid = document.getElementById("tagNewsGrid");

  if (tagTrigger && tagGrid) {
    let isFetchingTag = false;

    const fetchNextTagPage = function () {
      const nextPageUrl = tagTrigger.getAttribute("data-next-page");
      if (!nextPageUrl || isFetchingTag) return;

      isFetchingTag = true;

      fetch(nextPageUrl)
        .then((res) => res.text())
        .then((htmlText) => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(htmlText, "text/html");
          const newCards = doc.querySelectorAll(
            "#tagNewsGrid .category-news-card",
          );

          if (newCards && newCards.length > 0) {
            newCards.forEach((card, index) => {
              card.style.opacity = "0";
              card.style.transform = "translateY(15px)";
              card.style.transition = `opacity 0.4s ease ${
                index * 0.05
              }s, transform 0.4s ease ${index * 0.05}s`;
              tagGrid.appendChild(card);

              requestAnimationFrame(() => {
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
              });
            });

            const nextTrigger = doc.querySelector("#tagInfiniteTrigger");
            const nextUrlAttr = nextTrigger
              ? nextTrigger.getAttribute("data-next-page")
              : "";

            if (nextUrlAttr && nextUrlAttr !== nextPageUrl) {
              tagTrigger.setAttribute("data-next-page", nextUrlAttr);
            } else {
              tagTrigger.style.display = "none";
              tagObserver.unobserve(tagTrigger);
            }
          } else {
            tagTrigger.style.display = "none";
          }
          isFetchingTag = false;
        })
        .catch((err) => {
          console.error("Failed to load tag page:", err);
          tagTrigger.style.display = "none";
          isFetchingTag = false;
        });
    };

    const tagObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !isFetchingTag) {
            fetchNextTagPage();
          }
        });
      },
      {
        rootMargin: "300px",
      },
    );

    tagObserver.observe(tagTrigger);
  }

  // 7. Author Page Infinite Scroll
  const authorTrigger = document.getElementById("authorInfiniteTrigger");
  const authorGrid = document.getElementById("authorNewsGrid");

  if (authorTrigger && authorGrid) {
    let isFetchingAuthor = false;

    const fetchNextAuthorPage = function () {
      const nextPageUrl = authorTrigger.getAttribute("data-next-page");
      if (!nextPageUrl || isFetchingAuthor) return;

      isFetchingAuthor = true;

      fetch(nextPageUrl)
        .then((res) => res.text())
        .then((htmlText) => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(htmlText, "text/html");
          const newCards = doc.querySelectorAll(
            "#authorNewsGrid .category-news-card",
          );

          if (newCards && newCards.length > 0) {
            newCards.forEach((card, index) => {
              card.style.opacity = "0";
              card.style.transform = "translateY(15px)";
              card.style.transition = `opacity 0.4s ease ${
                index * 0.05
              }s, transform 0.4s ease ${index * 0.05}s`;
              authorGrid.appendChild(card);

              requestAnimationFrame(() => {
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
              });
            });

            const nextTrigger = doc.querySelector("#authorInfiniteTrigger");
            const nextUrlAttr = nextTrigger
              ? nextTrigger.getAttribute("data-next-page")
              : "";

            if (nextUrlAttr && nextUrlAttr !== nextPageUrl) {
              authorTrigger.setAttribute("data-next-page", nextUrlAttr);
            } else {
              authorTrigger.style.display = "none";
              authorObserver.unobserve(authorTrigger);
            }
          } else {
            authorTrigger.style.display = "none";
          }
          isFetchingAuthor = false;
        })
        .catch((err) => {
          console.error("Failed to load author page:", err);
          authorTrigger.style.display = "none";
          isFetchingAuthor = false;
        });
    };

    const authorObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !isFetchingAuthor) {
            fetchNextAuthorPage();
          }
        });
      },
      {
        rootMargin: "300px",
      },
    );

    authorObserver.observe(authorTrigger);
  }

  // 8. Search Results Infinite Scroll
  const searchTrigger = document.getElementById("searchInfiniteTrigger");
  const searchGrid = document.getElementById("searchResultsGrid");

  if (searchTrigger && searchGrid) {
    let remainingPosts = [];
    try {
      remainingPosts = JSON.parse(
        searchTrigger.getAttribute("data-remaining") || "[]",
      );
    } catch (e) {
      console.error("Error parsing remaining search posts:", e);
    }

    let isFetchingSearch = false;
    let searchObserver = null;
    let searchScrollFrame = 0;
    const searchLoadThreshold = 700;

    const isSearchNearBottom = function () {
      const triggerRect = searchTrigger.getBoundingClientRect();
      return triggerRect.top <= window.innerHeight + searchLoadThreshold;
    };

    const scheduleSearchCheck = function () {
      if (searchScrollFrame) return;
      searchScrollFrame = window.requestAnimationFrame(() => {
        searchScrollFrame = 0;
        if (
          !isFetchingSearch &&
          searchTrigger.offsetParent !== null &&
          isSearchNearBottom()
        ) {
          appendSearchBatch();
        }
      });
    };

    const appendSearchBatch = function () {
      if (isFetchingSearch) return;
      isFetchingSearch = true;

      if (remainingPosts.length > 0) {
        const batchSize = 10;
        const nextBatch = remainingPosts.splice(0, batchSize);

        nextBatch.forEach((row, index) => {
          const card = document.createElement("article");
          card.className = "search-result-card";
          card.style.opacity = "0";
          card.style.transform = "translateY(15px)";
          card.style.transition = `opacity 0.4s ease ${
            index * 0.05
          }s, transform 0.4s ease ${index * 0.05}s`;

          const linkUrl = row.url
            ? row.url.startsWith("/")
              ? row.url
              : "/" + row.url
            : "#";
          const newsTitle = row.title || "";
          const newsImg = row.image || "";
          const newsPaid = row.news_paid == 1;
          const pubDate = row.published_at
            ? new Date(row.published_at).toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
              })
            : "";
          let shortDesc = (
            row.meta_description ||
            row.description ||
            ""
          ).trim();
          if (shortDesc.length > 160)
            shortDesc = shortDesc.substring(0, 157) + "...";

          const paidBadge = newsPaid
            ? `
            <span class="paid-badge overlay">
              <svg class="paid-icon" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z"/></svg>
              Premium
            </span>`
            : "";

          const categoryTitle = row.category_title || "";
          const categoryUrl = row.category_url
            ? row.category_url.startsWith("/")
              ? row.category_url
              : "/" + row.category_url
            : "";
          const tagHtml = categoryTitle
            ? `<span class="tag">${
                categoryUrl
                  ? `<a href="${categoryUrl}">${categoryTitle}</a>`
                  : categoryTitle
              }</span>`
            : "";

          const authorName = row.author_name || row.profile_display_name || "";
          const authorId = row.author_id || row.news_owner_id || "";
          let authorHtml = "";
          if (authorName) {
            if (authorId) {
              const authorSlug = encodeURIComponent(
                authorName.trim().toLowerCase().replace(/\s+/g, "-"),
              );
              const authorUrl = `/author/${authorId}-${authorSlug}-articles/`;
              authorHtml = `<span class="meta-sep">•</span><span class="author">By <a href="${authorUrl}">${authorName}</a></span>`;
            } else {
              authorHtml = `<span class="meta-sep">•</span><span class="author">By ${authorName}</span>`;
            }
          }

          card.innerHTML = `
            <div class="search-card-img">
              ${paidBadge}
              <a href="${linkUrl}">
                <img src="${newsImg}" alt="${newsTitle}" loading="lazy" />
              </a>
            </div>
            <div class="search-card-content">
              ${tagHtml}
              <h2><a href="${linkUrl}">${newsTitle}</a></h2>
              <p>${shortDesc}</p>
              <div class="category-card-meta">
                <div class="meta-left">
                  <span class="date">${pubDate}</span>
                  ${authorHtml}
                </div>
                <span class="read-more">Read Story <span class="arrow">&rarr;</span></span>
              </div>
            </div>
          `;

          searchGrid.appendChild(card);
          requestAnimationFrame(() => {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
          });
        });

        if (remainingPosts.length === 0) {
          searchTrigger.style.display = "none";
          if (searchObserver) {
            searchObserver.unobserve(searchTrigger);
          }
        }
        isFetchingSearch = false;
        scheduleSearchCheck();
      } else {
        const nextPageUrl = searchTrigger.getAttribute("data-next-page");
        if (nextPageUrl) {
          fetch(nextPageUrl)
            .then((res) => res.text())
            .then((htmlText) => {
              const parser = new DOMParser();
              const doc = parser.parseFromString(htmlText, "text/html");
              const newCards = doc.querySelectorAll(
                "#searchResultsGrid .search-result-card",
              );

              if (newCards && newCards.length > 0) {
                newCards.forEach((card, index) => {
                  card.style.opacity = "0";
                  card.style.transform = "translateY(15px)";
                  card.style.transition = `opacity 0.4s ease ${
                    index * 0.05
                  }s, transform 0.4s ease ${index * 0.05}s`;
                  searchGrid.appendChild(card);

                  requestAnimationFrame(() => {
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                  });
                });

                const nextTrigger = doc.querySelector("#searchInfiniteTrigger");
                const nextUrlAttr = nextTrigger
                  ? nextTrigger.getAttribute("data-next-page")
                  : "";

                if (nextUrlAttr && nextUrlAttr !== nextPageUrl) {
                  searchTrigger.setAttribute("data-next-page", nextUrlAttr);
                } else {
                  searchTrigger.style.display = "none";
                  if (searchObserver) {
                    searchObserver.unobserve(searchTrigger);
                  }
                }
              } else {
                searchTrigger.style.display = "none";
              }
              isFetchingSearch = false;
              scheduleSearchCheck();
            })
            .catch((err) => {
              console.error("Failed to load search page:", err);
              searchTrigger.style.display = "none";
              isFetchingSearch = false;
            });
        } else {
          searchTrigger.style.display = "none";
          isFetchingSearch = false;
        }
      }
    };

    searchObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !isFetchingSearch) {
            appendSearchBatch();
          }
        });
      },
      {
        rootMargin: "700px 0px",
      },
    );

    searchObserver.observe(searchTrigger);
    window.addEventListener("scroll", scheduleSearchCheck, { passive: true });
    window.addEventListener("resize", scheduleSearchCheck);
    scheduleSearchCheck();
  }

  // 9. Subscribe Page FAQ Accordion
  const faqItems = document.querySelectorAll(".subscribe-faq-item");
  if (faqItems.length > 0) {
    faqItems.forEach((item) => {
      const q = item.querySelector(".subscribe-faq-question");
      if (q) {
        q.addEventListener("click", () => {
          const isActive = item.classList.contains("active");
          faqItems.forEach((el) => el.classList.remove("active"));
          if (!isActive) {
            item.classList.add("active");
          }
        });
      }
    });
  }

  // 10. Auto Redirect Notice Handler
  const redirectNotice = document.querySelector(
    ".redirect-notice[data-redirect-url]",
  );
  if (redirectNotice) {
    const redirectUrl = redirectNotice.getAttribute("data-redirect-url");
    if (redirectUrl) {
      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 5000);
    }
  }
});
