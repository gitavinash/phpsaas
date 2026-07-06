<?php

require_once 'header.php';

$postUrl = SITE_URL . $link_url;

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner" style="background-image: linear-gradient(rgba(28, 24, 20, 0.75), rgba(28, 24, 20, 0.85)), url('assets/images/before_skin.png');">
  <div class="container inner-banner-content">
    <h1 id="postTitle"><?php echo ucfirst($post_title) ?></h1>
    <div class="post-meta-strip">
      <span id="postAuthor"><i data-lucide="user" style="width: 14px"></i> <?php echo $profile_display_name ?></span>
      <span id="postDate"><i data-lucide="calendar" style="width: 14px"></i> <?php echo date('M d, Y', strtotime($link_published)) ?></span>
      <span id="postReadTime"><i data-lucide="clock" style="width: 14px"></i> <?php echo timeToRead($post_description) ?></span>
    </div>
  </div>
</section>

<!-- 3. MAIN DUAL-COLUMN LAYOUT -->
<section class="journal-single-section">
  <div class="container journal-single-grid">
    <!-- Left Column: Article + Bio + Share + Comments -->
    <div>
      <article
        class="post-body-content"
        id="postBody"
        style="margin-bottom: 4rem">
        <?php echo ucfirst($post_description); ?>
      </article>

      <!-- Social Share Widget -->
      <div class="share-article-wrap">
        <span class="share-title">Share this journal</span>
        <div class="share-icons">
          <a
            href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($postUrl) ?>"
            class="share-circle-btn"
            aria-label="Share on Facebook"
            target="_blank"
            rel="noopener noreferrer">
            <i data-lucide="facebook" style="width: 18px"></i>
          </a>
          <a
            href="http://twitter.com/share?text=<?php echo urlencode($post_title) ?>&url=<?php echo urlencode($postUrl) ?>"
            class="share-circle-btn"
            aria-label="Share on Twitter">
            <i data-lucide="twitter" style="width: 18px"></i>
          </a>
          <a
            href="https://wa.me/?text=<?php echo urlencode($post_title . ' ' . $postUrl) ?>"
            class="share-circle-btn"
            aria-label="Share on WhatsApp">
            <i data-lucide="message-square" style="width: 18px"></i>
          </a>
          <a
            href="javascript:void(0);"
            class="share-circle-btn"
            aria-label="Copy Link"
            onclick="copyLink(event)"
            id="copyLinkBtn">
            <i data-lucide="copy" style="width: 18px"></i>
          </a>
        </div>
      </div>

      <!-- Author Bio Card Container -->
      <div id="authorBioContainer">
        <div class="author-bio-card reveal">
          <div class="author-avatar">
            <img src="assets/images/dr_founder.png" alt="Dr. Elizabeth Reed, MD">
          </div>
          <div class="author-bio-info">
            <h4>Dr. Elizabeth Reed, MD</h4>
            <div class="author-title">Founder & Lead Dermatologist</div>
            <p>Dr. Elizabeth Reed is a board-certified clinical dermatologist with over 15 years of experience in laser resurfacing, facial contouring, and advanced acne scar treatment protocols. She trained in London and serves as our medical director.</p>
          </div>
        </div>
      </div>

      <!-- Interactive Comments & Discussion -->
      <div class="comments-container-wrap">
        <h3 class="comments-section-title">
          Clinical Q&A (<span id="commentCount">2</span>)
        </h3>

        <!-- Comments List -->
        <div class="comments-list" id="commentsList">
          <!-- Static Comment 1 -->
          <div class="comment-item reveal">
            <div class="comment-avatar-circle">PM</div>
            <div class="comment-content-box">
              <div class="comment-header-row">
                <span class="comment-author-name">Priya Malik</span>
                <span class="comment-date-stamp">May 22, 2026</span>
              </div>
              <p class="comment-text-body">Dr. Reed, this is so informative. I have deep rolling scars on my cheeks. Is subcision painful? How many sittings would I typically need?</p>
            </div>
          </div>

          <!-- Static Comment 2 (Clinic Expert Reply) -->
          <div class="comment-item reveal" style="border-left: 3px solid var(--color-primary-gold); background-color: var(--color-bg-cream);">
            <div class="comment-avatar-circle" style="background-color: var(--color-bg-dark); color: var(--color-primary-gold); border-color: var(--color-bg-dark);">DR</div>
            <div class="comment-content-box">
              <div class="comment-header-row">
                <span class="comment-author-name">Dr. Elizabeth Reed, MD <span class="gold-accent" style="font-size: 1.1rem; text-transform: uppercase; font-weight: 700; margin-left: 1rem;">Clinic Expert</span></span>
                <span class="comment-date-stamp">May 23, 2026</span>
              </div>
              <p class="comment-text-body">Hello Priya, subcision is performed under local anaesthetic, so the procedure itself is virtually painless. For rolling scars, 3 to 4 sessions combined with microneedling yield excellent collagen elevation.</p>
            </div>
          </div>
        </div>

        <!-- Leave a Reply Form -->
        <div class="comment-form-container">
          <h4 class="comment-form-title">Leave a Clinical Query</h4>
          <p class="comment-form-subtitle">
            Your email address will not be published. Required fields are
            marked *
          </p>
          <form
            id="commentForm"
            action="#"
            method="POST">
            <div class="comment-form-grid">
              <div class="comment-form-field">
                <label class="comment-form-label" for="commentAuthor">Name *</label>
                <input
                  type="text"
                  class="comment-form-input"
                  name="author"
                  id="commentAuthor"
                  required />
              </div>
              <div class="comment-form-field">
                <label class="comment-form-label" for="commentEmail">Email *</label>
                <input
                  type="email"
                  class="comment-form-input"
                  name="email"
                  id="commentEmail"
                  required />
              </div>
              <div class="comment-form-field full-width">
                <label class="comment-form-label" for="commentText">Clinical Query or Reply *</label>
                <textarea
                  class="comment-form-input comment-form-textarea"
                  name="message"
                  id="commentText"
                  required></textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">
              Submit Inquiry
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Right: Sidebar Widgets -->
    <?php

    require_once 'blog_sidebar.php';

    ?>
  </div>
</section>

<script>
  function copyLink(event) {
    if (event) {
      event.preventDefault();
    }

    var linkToCopy = <?php echo json_encode($postUrl); ?>;
    var copyBtn = document.getElementById('copyLinkBtn');

    function showCopiedState() {
      if (!copyBtn) {
        return;
      }

      var previousLabel = copyBtn.getAttribute('aria-label');
      copyBtn.setAttribute('aria-label', 'Link copied');
      copyBtn.classList.add('is-copied');

      setTimeout(function() {
        copyBtn.setAttribute('aria-label', previousLabel || 'Copy Link');
        copyBtn.classList.remove('is-copied');
      }, 1200);
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(linkToCopy).then(showCopiedState);
      return;
    }

    var textArea = document.createElement('textarea');
    textArea.value = linkToCopy;
    textArea.setAttribute('readonly', '');
    textArea.style.position = 'absolute';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    showCopiedState();
  }
</script>

<?php

require_once 'footer.php';

?>