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
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
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