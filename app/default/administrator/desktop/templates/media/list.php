<h2><?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> Library <span><a href="media/?type=add"
            class="add-btn">Add New</a></span></h2>
<style>
.media-grid {
    list-style: none;
    margin: 18px 0 0 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
}

.media-card {
    border: 1px solid #dce2e8;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}

.media-card.is-hidden {
    display: none;
}

.media-thumb {
    height: 140px;
    background: #f3f6f8;
    display: flex;
    align-items: center;
    justify-content: center;
}

.media-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.media-file-label {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.4px;
    color: #5b6978;
    text-transform: uppercase;
}

.media-meta {
    padding: 10px 12px 12px;
}

.media-name {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #1d2b38;
    line-height: 1.35;
    word-break: break-word;
}

.media-type {
    margin: 6px 0 0;
    font-size: 12px;
    color: #4d5a66;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.media-card-actions {
    margin-top: 10px;
    display: flex;
    gap: 8px;
}

.media-card-btn {
    border: 1px solid #cad4de;
    background: #fff;
    color: #324252;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
    padding: 6px 10px;
    cursor: pointer;
}

.media-card-btn:hover {
    border-color: #00609e;
    color: #00609e;
}

.media-delete-btn {
    border-color: #e6c9c9;
    color: #af2e2e;
}

.media-delete-btn:hover {
    border-color: #af2e2e;
    color: #af2e2e;
}

.media-empty {
    grid-column: 1 / -1;
    border: 1px dashed #c8d1db;
    border-radius: 8px;
    background: #f8fafc;
    padding: 18px;
    color: #556270;
}

.media-show-more-wrap {
    margin-top: 16px;
    text-align: center;
}

.media-show-more-btn {
    border: 1px solid #00609e;
    background: #00609e;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    padding: 9px 16px;
    cursor: pointer;
}

.media-show-more-btn:hover {
    background: #004d7d;
    border-color: #004d7d;
}

.media-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    padding: 16px;
    box-sizing: border-box;
    align-items: center;
    justify-content: center;
    background: rgba(11, 18, 26, 0.66);
}

.media-modal.is-open {
    display: flex;
}

.media-modal-inner {
    width: 92%;
    max-width: 1100px;
    margin: 0;
    position: relative;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    min-height: 560px;
    max-height: calc(100vh - 32px);
}

.media-modal-left {
    background: #F7FAFC;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 560px;
}

.media-modal-left img {
    max-width: 100%;
    max-height: calc(100vh - 110px);
    object-fit: contain;
}

.media-modal-file {
    color: #d7dde5;
    font-size: 16px;
    padding: 20px;
    text-align: center;
}

.media-modal-right {
    padding: 20px;
    overflow: auto;
}

.media-modal-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.media-modal-top h3 {
    margin: 0;
    font-size: 18px;
}

.media-modal-close {
    border: none;
    background: #eef3f7;
    color: #4b5b69;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-size: 18px;
    line-height: 30px;
    cursor: pointer;
}

.media-modal-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
}

.media-modal-nav.prev {
    left: 10px;
}

.media-modal-nav.next {
    right: 10px;
}

.media-modal-nav-btn {
    border: 1px solid rgba(255, 255, 255, 0.55);
    background: rgba(15, 23, 32, 0.75);
    color: #ffffff;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
}

.media-modal-nav-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.media-info {
    margin-bottom: 14px;
    padding: 10px 12px;
    background: #f7fafc;
    border: 1px solid #d8e2eb;
    border-radius: 6px;
}

.media-info p {
    margin: 6px 0;
    font-size: 13px;
    color: #2f3e4c;
}

.media-edit-form label {
    display: block;
    margin: 10px 0 6px;
    font-size: 13px;
    color: #2c3b49;
    font-weight: 600;
}

.media-edit-form input,
.media-edit-form textarea {
    width: 100%;
    border: 1px solid #ccd7e1;
    border-radius: 6px;
    padding: 8px 10px;
    font-size: 13px;
    box-sizing: border-box;
}

.media-edit-form textarea {
    resize: vertical;
    min-height: 90px;
}

.media-form-actions {
    margin-top: 14px;
    display: flex;
    gap: 10px;
}

.media-save-btn,
.media-delete-large-btn {
    border: none;
    border-radius: 6px;
    padding: 9px 12px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

.media-save-btn {
    background: #00609e;
    color: #fff;
}

.media-delete-large-btn {
    background: #efefef;
    color: #b92f2f;
}

@media (max-width: 900px) {
    .media-modal-inner {
        grid-template-columns: 1fr;
        max-height: calc(100vh - 32px);
        margin: 0;
    }

    .media-modal-nav.prev {
        left: 6px;
    }

    .media-modal-nav.next {
        right: 6px;
    }

    .media-modal-left {
        min-height: 280px;
    }
}
</style>

<ul class="media-grid">
    <?php

    $query = "SELECT media_id, media_title, media_type, media_url,
        (SELECT link_published FROM " . TABLE_PREFIX . "link WHERE link_relation_id=media_id AND link_type='media' AND link_site_id='" . SITE_ID . "' ORDER BY link_published DESC LIMIT 0, 1) AS uploaded_at,
        (SELECT meta_keyword FROM " . TABLE_PREFIX . "meta WHERE meta_relation_id=media_id AND meta_type='media' AND meta_site_id='" . SITE_ID . "' LIMIT 0, 1) AS media_alt,
        (SELECT meta_description FROM " . TABLE_PREFIX . "meta WHERE meta_relation_id=media_id AND meta_type='media' AND meta_site_id='" . SITE_ID . "' LIMIT 0, 1) AS media_description
        FROM " . TABLE_PREFIX . "media WHERE media_site_id='" . SITE_ID . "' ORDER BY media_id DESC";

    if ($mediaList = mysqli_query($connection, $query)) {
        if (mysqli_num_rows($mediaList) != 0) {
            $mediaIndex = 0;
            while ($media = mysqli_fetch_assoc($mediaList)) {
                $mediaId = (int)$media['media_id'];
                $mediaName = trim($media['media_title']) !== '' ? $media['media_title'] : 'Untitled';
                $mediaType = trim($media['media_type']) !== '' ? $media['media_type'] : 'unknown';
                $mediaUrl = trim($media['media_url']);
                $mediaAlt = isset($media['media_alt']) ? trim($media['media_alt']) : '';
                $mediaDescription = isset($media['media_description']) ? trim($media['media_description']) : '';
                $uploadedAt = 'Not available';
                if (isset($media['uploaded_at']) && trim($media['uploaded_at']) !== '' && trim($media['uploaded_at']) !== '0000-00-00 00:00:00') {
                    $uploadedAt = date('d/m/Y h:i A', strtotime($media['uploaded_at']));
                }
                $isImage = preg_match('/^image\//i', $mediaType) || preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i', $mediaUrl);
                $hiddenClass = ($mediaIndex >= 100) ? ' is-hidden' : '';
    ?>
    <li class="media-card<?php echo $hiddenClass; ?>" data-media-index="<?php echo $mediaIndex; ?>"
        data-media-id="<?php echo $mediaId; ?>"
        data-media-name="<?php echo htmlspecialchars($mediaName, ENT_QUOTES); ?>"
        data-media-type="<?php echo htmlspecialchars($mediaType, ENT_QUOTES); ?>"
        data-media-url="<?php echo htmlspecialchars($mediaUrl, ENT_QUOTES); ?>"
        data-media-alt="<?php echo htmlspecialchars($mediaAlt, ENT_QUOTES); ?>"
        data-media-description="<?php echo htmlspecialchars($mediaDescription, ENT_QUOTES); ?>"
        data-media-uploaded="<?php echo htmlspecialchars($uploadedAt, ENT_QUOTES); ?>"
        data-media-is-image="<?php echo $isImage ? '1' : '0'; ?>">
        <div class="media-thumb media-open-trigger" role="button" tabindex="0">
            <?php if ($isImage && $mediaUrl !== '') { ?>
            <img src="<?php echo htmlspecialchars($mediaUrl); ?>" alt="<?php echo htmlspecialchars($mediaName); ?>">
            <?php } else { ?>
            <span class="media-file-label"><?php echo htmlspecialchars($mediaType); ?></span>
            <?php } ?>
        </div>
        <div class="media-meta">
            <h3 class="media-name"><?php echo htmlspecialchars($mediaName); ?></h3>
            <p class="media-type"><?php echo htmlspecialchars($mediaType); ?></p>
            <div class="media-card-actions">
                <button type="button" class="media-card-btn media-open-btn">Open</button>
                <button type="button" class="media-card-btn media-delete-btn">Delete</button>
            </div>
        </div>
    </li>
    <?php
                $mediaIndex++;
            }
        } else {
            echo '<li class="media-empty">No media found.</li>';
        }
    } else {
        echo '<li class="media-empty">Unable to load media right now.</li>';
    }

    ?>
</ul>

<div class="media-show-more-wrap" id="mediaShowMoreWrap" style="display: none;">
    <button type="button" class="media-show-more-btn" id="mediaShowMoreBtn">Show More</button>
</div>

<div class="media-modal" id="mediaModal">
    <div class="media-modal-inner">
        <div class="media-modal-nav prev">
            <button type="button" class="media-modal-nav-btn" id="mediaPrevBtn"
                aria-label="Previous media">&#10094;</button>
        </div>

        <div class="media-modal-nav next">
            <button type="button" class="media-modal-nav-btn" id="mediaNextBtn"
                aria-label="Next media">&#10095;</button>
        </div>

        <div class="media-modal-left">
            <img id="mediaModalImage" src="" alt="" style="display: none;">
            <div class="media-modal-file" id="mediaModalFile">Preview unavailable for this file type.</div>
        </div>
        <div class="media-modal-right">
            <div class="media-modal-top">
                <h3>Media Details</h3>
                <button type="button" class="media-modal-close" id="mediaModalClose">x</button>
            </div>

            <div class="media-info">
                <p><strong>Name:</strong> <span id="mediaViewName">-</span></p>
                <p><strong>Type:</strong> <span id="mediaViewType">-</span></p>
                <p><strong>Uploaded:</strong> <span id="mediaViewUploaded">-</span></p>
            </div>

            <form class="media-edit-form" id="mediaEditForm">
                <input type="hidden" id="mediaEditId" value="">

                <label for="mediaEditTitle">Title</label>
                <input type="text" id="mediaEditTitle" name="title">

                <label for="mediaEditAlt">Alt Text</label>
                <input type="text" id="mediaEditAlt" name="alt">

                <label for="mediaEditDescription">Description</label>
                <textarea id="mediaEditDescription" name="description"></textarea>

                <div class="media-form-actions">
                    <button type="submit" class="media-save-btn" id="mediaSaveBtn">Save Changes</button>
                    <button type="button" class="media-delete-large-btn" id="mediaDeleteLargeBtn">Delete Media</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var batchSize = 100;
    var cards = document.querySelectorAll('.media-grid .media-card');
    var showMoreWrap = document.getElementById('mediaShowMoreWrap');
    var showMoreBtn = document.getElementById('mediaShowMoreBtn');
    var visibleCount = Math.min(batchSize, cards.length);
    var mediaModal = document.getElementById('mediaModal');
    var mediaModalClose = document.getElementById('mediaModalClose');
    var mediaModalImage = document.getElementById('mediaModalImage');
    var mediaModalFile = document.getElementById('mediaModalFile');
    var mediaViewName = document.getElementById('mediaViewName');
    var mediaViewType = document.getElementById('mediaViewType');
    var mediaViewUploaded = document.getElementById('mediaViewUploaded');
    var mediaEditForm = document.getElementById('mediaEditForm');
    var mediaEditId = document.getElementById('mediaEditId');
    var mediaEditTitle = document.getElementById('mediaEditTitle');
    var mediaEditAlt = document.getElementById('mediaEditAlt');
    var mediaEditDescription = document.getElementById('mediaEditDescription');
    var mediaSaveBtn = document.getElementById('mediaSaveBtn');
    var mediaDeleteLargeBtn = document.getElementById('mediaDeleteLargeBtn');
    var mediaPrevBtn = document.getElementById('mediaPrevBtn');
    var mediaNextBtn = document.getElementById('mediaNextBtn');

    var activeCard = null;
    var activeCardIndex = -1;

    if (!cards.length || !showMoreWrap || !showMoreBtn || !mediaModal) {
        return;
    }

    if (cards.length > batchSize) {
        showMoreWrap.style.display = 'block';
    }

    showMoreBtn.addEventListener('click', function() {
        var nextVisibleCount = Math.min(visibleCount + batchSize, cards.length);

        for (var i = visibleCount; i < nextVisibleCount; i++) {
            cards[i].classList.remove('is-hidden');
        }

        visibleCount = nextVisibleCount;

        if (visibleCount >= cards.length) {
            showMoreWrap.style.display = 'none';
        }
    });

    function openMediaModal(card) {
        if (!card) {
            return;
        }

        activeCard = card;
        activeCardIndex = getConnectedCards().indexOf(card);

        var mediaId = card.getAttribute('data-media-id') || '';
        var mediaName = card.getAttribute('data-media-name') || '';
        var mediaType = card.getAttribute('data-media-type') || '';
        var mediaUrl = card.getAttribute('data-media-url') || '';
        var mediaAlt = card.getAttribute('data-media-alt') || '';
        var mediaDescription = card.getAttribute('data-media-description') || '';
        var mediaUploaded = card.getAttribute('data-media-uploaded') || 'Not available';
        var isImage = card.getAttribute('data-media-is-image') === '1';

        mediaEditId.value = mediaId;
        mediaEditTitle.value = mediaName;
        mediaEditAlt.value = mediaAlt;
        mediaEditDescription.value = mediaDescription;

        mediaViewName.textContent = mediaName;
        mediaViewType.textContent = mediaType;
        mediaViewUploaded.textContent = mediaUploaded;

        if (isImage && mediaUrl !== '') {
            mediaModalImage.style.display = 'block';
            mediaModalFile.style.display = 'none';
            mediaModalImage.src = mediaUrl;
            mediaModalImage.alt = mediaName;
        } else {
            mediaModalImage.style.display = 'none';
            mediaModalFile.style.display = 'block';
            mediaModalFile.textContent = 'Preview unavailable for this file type.';
            mediaModalImage.src = '';
        }

        mediaModal.classList.add('is-open');
        updateModalNavButtons();
    }

    function closeMediaModal() {
        mediaModal.classList.remove('is-open');
    }

    function getConnectedCards() {
        return Array.prototype.filter.call(cards, function(item) {
            return item && item.isConnected;
        });
    }

    function updateModalNavButtons() {
        var connectedCards = getConnectedCards();
        var hasItems = connectedCards.length > 0;

        if (!hasItems || activeCardIndex <= 0) {
            mediaPrevBtn.disabled = true;
        } else {
            mediaPrevBtn.disabled = false;
        }

        if (!hasItems || activeCardIndex >= connectedCards.length - 1) {
            mediaNextBtn.disabled = true;
        } else {
            mediaNextBtn.disabled = false;
        }
    }

    function navigateModal(step) {
        var connectedCards = getConnectedCards();

        if (!connectedCards.length) {
            return;
        }

        var currentIndex = activeCardIndex;
        if (currentIndex < 0) {
            currentIndex = connectedCards.indexOf(activeCard);
        }

        var nextIndex = currentIndex + step;

        if (nextIndex < 0 || nextIndex >= connectedCards.length) {
            return;
        }

        openMediaModal(connectedCards[nextIndex]);
    }

    function showToast(message, status) {
        var messageType = status || 'info';
        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                toast: true,
                position: 'top-end',
                type: messageType,
                title: message,
                showConfirmButton: false,
                timer: 1700
            });
        } else {
            alert(message);
        }
    }

    function askDeleteConfirmation(callback) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                title: 'Delete this media?',
                text: "You won't be able to undo this action.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result && (result.value === true || result.isConfirmed === true)) {
                    callback();
                }
            });
        } else if (confirm('Are you sure you want to delete this media?')) {
            callback();
        }
    }

    function deleteMedia(card) {
        if (!card) {
            return;
        }

        var mediaId = card.getAttribute('data-media-id');

        if (!mediaId) {
            showToast('Invalid media item.', 'error');
            return;
        }

        askDeleteConfirmation(function() {
            var params = 'action=' + encodeURIComponent('delete') + '&id=' + encodeURIComponent(mediaId);

            fetch('/api/media.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: params
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                if (String(data.status) === '200') {
                    var connectedCards = getConnectedCards();
                    var currentIndex = connectedCards.indexOf(card);

                    card.remove();

                    if (activeCard === card) {
                        connectedCards = getConnectedCards();

                        if (connectedCards.length === 0) {
                            closeMediaModal();
                            activeCard = null;
                            activeCardIndex = -1;
                        } else {
                            var reopenIndex = currentIndex;
                            if (reopenIndex >= connectedCards.length) {
                                reopenIndex = connectedCards.length - 1;
                            }
                            openMediaModal(connectedCards[reopenIndex]);
                        }
                    }
                    showToast(data.msg || 'Media deleted successfully', 'success');
                } else {
                    showToast(data.msg || 'Unable to delete media.', 'error');
                }
            }).catch(function() {
                showToast('Unable to delete media right now.', 'error');
            });
        });
    }

    cards.forEach(function(card) {
        var thumb = card.querySelector('.media-open-trigger');
        var openBtn = card.querySelector('.media-open-btn');
        var deleteBtn = card.querySelector('.media-delete-btn');

        if (thumb) {
            thumb.addEventListener('click', function() {
                openMediaModal(card);
            });

            thumb.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openMediaModal(card);
                }
            });
        }

        if (openBtn) {
            openBtn.addEventListener('click', function() {
                openMediaModal(card);
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                deleteMedia(card);
            });
        }
    });

    mediaModalClose.addEventListener('click', function() {
        closeMediaModal();
    });

    mediaPrevBtn.addEventListener('click', function() {
        navigateModal(-1);
    });

    mediaNextBtn.addEventListener('click', function() {
        navigateModal(1);
    });

    mediaModal.addEventListener('click', function(event) {
        if (event.target === mediaModal) {
            closeMediaModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (!mediaModal.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            navigateModal(-1);
        }

        if (event.key === 'ArrowRight') {
            navigateModal(1);
        }

        if (event.key === 'Escape') {
            closeMediaModal();
        }
    });

    mediaDeleteLargeBtn.addEventListener('click', function() {
        deleteMedia(activeCard);
    });

    mediaEditForm.addEventListener('submit', function(event) {
        event.preventDefault();

        var mediaId = mediaEditId.value;
        var title = mediaEditTitle.value.trim();
        var alt = mediaEditAlt.value.trim();
        var description = mediaEditDescription.value.trim();

        if (!mediaId) {
            showToast('Invalid media item.', 'error');
            return;
        }

        if (title === '') {
            showToast('Title is required.', 'warning');
            return;
        }

        mediaSaveBtn.disabled = true;
        mediaSaveBtn.textContent = 'Saving...';

        var params = 'action=' + encodeURIComponent('update') +
            '&id=' + encodeURIComponent(mediaId) +
            '&title=' + encodeURIComponent(title) +
            '&alt=' + encodeURIComponent(alt) +
            '&description=' + encodeURIComponent(description);

        fetch('/api/media.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: params
        }).then(function(response) {
            return response.json();
        }).then(function(data) {
            mediaSaveBtn.disabled = false;
            mediaSaveBtn.textContent = 'Save Changes';

            if (String(data.status) === '200') {
                if (activeCard) {
                    activeCard.setAttribute('data-media-name', title);
                    activeCard.setAttribute('data-media-alt', alt);
                    activeCard.setAttribute('data-media-description', description);

                    var nameNode = activeCard.querySelector('.media-name');
                    var thumbImage = activeCard.querySelector('.media-thumb img');

                    if (nameNode) {
                        nameNode.textContent = title;
                    }

                    if (thumbImage) {
                        thumbImage.alt = title;
                    }
                }

                mediaViewName.textContent = title;
                showToast(data.msg || 'Media details updated successfully', 'success');
            } else {
                showToast(data.msg || 'Unable to update media details.', 'error');
            }
        }).catch(function() {
            mediaSaveBtn.disabled = false;
            mediaSaveBtn.textContent = 'Save Changes';
            showToast('Unable to update media right now.', 'error');
        });
    });
})();
</script>