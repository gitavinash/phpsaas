<?php
require_once 'header.php';

/**
 * Assumptions you can tweak:
 * - SITE_ID and USER_ID available (or inject as needed)
 * - discussion_status: 1 = published
 * - discussion_parent_id: 0 for top-level
 * - discussion_highlight stores JSON: { "tags": [...], "allow_comments": true/false }
 */

$isEdit = isset($_GET['id']) && ctype_digit($_GET['id']);
$editId = $isEdit ? (int) $_GET['id'] : 0;

$editRow = null;
$galleryImages = [];
if ($isEdit) {
    // Load discussion
    $stmt = $connection->prepare("
        SELECT
            d.discussion_id,
            d.discussion_title,
            d.discussion_description,
            d.discussion_highlight,
            d.discussion_category,
            d.discussion_gallery_id,
            d.discussion_before_image,
            d.discussion_after_image,
            d.discussion_anonymous,
            d.discussion_type,
            d.discussion_status
        FROM isw_discussion d
        WHERE d.discussion_id = ? AND d.discussion_status >= 0
        LIMIT 1
    ");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $res = $stmt->get_result();
    $editRow = $res->fetch_assoc() ?: null;
    $stmt->close();

    if ($editRow && !empty($editRow['discussion_gallery_id'])) {
        $gid = (int)$editRow['discussion_gallery_id'];
        $gs = $connection->prepare("
            SELECT gallery_image_list
            FROM isw_gallery
            WHERE gallery_id = ?
            LIMIT 1
        ");
        $gs->bind_param("i", $gid);
        $gs->execute();
        $gres = $gs->get_result();
        if ($g = $gres->fetch_assoc()) {
            $raw = trim((string)$g['gallery_image_list']);
            // Accept JSON array OR comma/newline separated strings
            $arr = json_decode($raw, true);
            if (!is_array($arr)) {
                $arr = preg_split('/[\r\n,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
            }
            $galleryImages = array_values(array_filter(array_map('trim', $arr)));
        }
        $gs->close();
    }
}

// Prefill values
$title       = $editRow['discussion_title']        ?? '';
$categoryId  = $editRow['discussion_category']     ?? '';
$postType    = $editRow['discussion_type']         ?? 'discussion';
$contentHtml = $editRow['discussion_description']  ?? '';
$beforeImg   = $editRow['discussion_before_image'] ?? '';
$afterImg    = $editRow['discussion_after_image']  ?? '';
$anonymous   = isset($editRow['discussion_anonymous']) ? (int)$editRow['discussion_anonymous'] === 1 : false;
$highlight   = $editRow['discussion_highlight']    ?? '';
$meta        = [];
if ($highlight) {
    $tmp = json_decode($highlight, true);
    if (is_array($tmp)) $meta = $tmp;
}
$allowComments = array_key_exists('allow_comments', $meta) ? (bool)$meta['allow_comments'] : true;
$tagsValue     = $meta['tags'] ?? []; // array or empty

// Categories
$categoryList = getAllCategoryListFilter($connection, 'discussion');
$catMap = [];
foreach ($categoryList as $cat) {
    $catMap[$cat['category_id']] = $cat['category_title'];
}

// Prepare payload for JS bootstrap
$jsPayload = [
    'isEdit'          => $isEdit,
    'id'              => $isEdit ? $editId : 0,
    'title'           => $title,
    'category_id'     => $categoryId,
    'category_text'   => ($categoryId && isset($catMap[$categoryId])) ? $catMap[$categoryId] : '',
    'post_type'       => $postType,
    'content_html'    => $contentHtml,
    'before_image'    => $beforeImg,
    'after_image'     => $afterImg,
    'anonymous'       => $anonymous,
    'allow_comments'  => $allowComments,
    'tags'            => $tagsValue,        // array OK
    'gallery'         => $galleryImages,    // array of URLs
];
?>
<script>
window.EDIT_DATA = <?php echo json_encode($jsPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8">
            <form id="discussionForm" enctype="multipart/form-data" method="post" action="">
                <?php if ($isEdit): ?>
                <input type="hidden" name="discussion_id" id="discussionId" value="<?php echo (int)$editId; ?>">
                <?php endif; ?>

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" id="title" name="discussion_title" placeholder="Enter a descriptive title..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                        maxlength="150" value="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>">
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500">Make it clear and specific to attract the right audience</p>
                        <span id="titleCounter" class="text-xs text-gray-400"><?php echo strlen($title); ?>/150</span>
                    </div>
                </div>

                <!-- Category + Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <div class="relative">
                            <button type="button" id="categoryButton"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-left bg-white flex items-center justify-between">
                                <span id="categoryText"
                                    class="<?php echo $categoryId ? 'text-gray-900' : 'text-gray-500'; ?>">
                                    <?php echo $categoryId && isset($catMap[$categoryId]) ? htmlspecialchars($catMap[$categoryId]) : 'Select a category...'; ?>
                                </span>
                                <i class="ri-arrow-down-s-line ri-lg text-gray-400"></i>
                            </button>
                            <input type="hidden" id="categoryValue" name="discussion_category"
                                value="<?php echo htmlspecialchars((string)$categoryId); ?>" />
                            <div id="categoryDropdown" class="category-dropdown">
                                <?php foreach ($categoryList as $category): ?>
                                <div class='category-option'
                                    data-value='<?php echo htmlspecialchars((string)$category["category_id"]); ?>'>
                                    <?php echo htmlspecialchars($category["category_title"]); ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <div class="space-y-3">
                            <?php
                            $types = [
                                'discussion'    => ['title' => 'Discussion', 'desc' => 'Share experiences and start conversations'],
                                'questions'     => ['title' => 'Question', 'desc' => 'Ask for advice or specific information'],
                                'success_story' => ['title' => 'Success Story', 'desc' => 'Share your positive results and journey'],
                            ];
                            foreach ($types as $val => $metaRow):
                                $checked = ($postType === $val) ? 'checked' : '';
                            ?>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="discussion_type" value="<?php echo $val; ?>"
                                    class="radio-custom mr-3" <?php echo $checked; ?>>
                                <div>
                                    <span
                                        class="font-medium text-gray-900 text-sm"><?php echo $metaRow['title']; ?></span>
                                    <p class="text-xs text-gray-500"><?php echo $metaRow['desc']; ?></p>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Editor -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your experiences</label>
                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                        <div class="editor-toolbar bg-gray-50">
                            <button type="button" class="editor-btn" data-command="bold" title="Bold"><i
                                    class="ri-bold ri-lg"></i></button>
                            <button type="button" class="editor-btn" data-command="italic" title="Italic"><i
                                    class="ri-italic ri-lg"></i></button>
                            <button type="button" class="editor-btn" data-command="underline" title="Underline"><i
                                    class="ri-underline ri-lg"></i></button>
                            <div class="w-px h-6 bg-gray-300 mx-1"></div>
                            <button type="button" class="editor-btn" data-command="insertUnorderedList"
                                title="Bullet List"><i class="ri-list-unordered ri-lg"></i></button>
                            <button type="button" class="editor-btn" data-command="insertOrderedList"
                                title="Numbered List"><i class="ri-list-ordered ri-lg"></i></button>
                            <div class="w-px h-6 bg-gray-300 mx-1"></div>
                            <button type="button" class="editor-btn" data-command="createLink" title="Insert Link"><i
                                    class="ri-link ri-lg"></i></button>
                        </div>
                        <div id="editor" class="editor-content" contenteditable="true"
                            placeholder="Share your thoughts, experiences, or questions in detail.">
                            <?php if ($contentHtml !== '') echo $contentHtml; ?>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Use the toolbar above to format your text.</p>
                </div>

                <!-- Gallery -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Images (Optional)</label>
                    <div id="uploadZone" class="upload-zone">
                        <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                            <i class="ri-image-add-line ri-xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600 font-medium mb-1">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each (max 5 images)</p>
                    </div>
                    <input type="file" id="imageInput" name="images[]" multiple accept=".jpg,.jpeg,.png" class="hidden">
                    <div id="imagePreview" class="image-preview mt-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="previewGrid"></div>
                    </div>
                    <input type="hidden" name="removed_gallery" id="removedGallery">
                </div>

                <!-- Before/After -->
                <div id="beforeAfterSection"
                    class="mb-6 <?php echo ($postType === 'success_story' ? '' : 'hidden'); ?>">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Before & After Comparison</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Before -->
                        <div>
                            <div class="mb-2 flex items-center">
                                <span class="text-sm font-medium text-gray-900 mr-2">Before</span>
                                <span class="text-xs text-gray-500">(Upload image showing initial condition)</span>
                            </div>
                            <div
                                class="before-upload-zone upload-zone cursor-pointer <?php echo ($beforeImg ? 'hidden' : ''); ?>">
                                <div
                                    class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="ri-image-add-line ri-xl text-gray-400"></i>
                                </div>
                                <p class="text-xs text-gray-500">Click to upload before image</p>
                            </div>
                            <input type="file" id="beforeImageInput" name="discussion_before_image"
                                accept=".jpg,.jpeg,.png" class="hidden">
                            <div id="beforePreview" class="<?php echo ($beforeImg ? '' : 'hidden'); ?> mt-3">
                                <div class="relative">
                                    <img src="<?php echo htmlspecialchars($beforeImg); ?>" alt="Before"
                                        class="w-full h-48 object-cover object-top rounded-lg">
                                    <button type="button"
                                        class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 js-remove-existing-before">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="remove_before" id="removeBefore" value="0">
                        </div>

                        <!-- After -->
                        <div>
                            <div class="mb-2 flex items-center">
                                <span class="text-sm font-medium text-gray-900 mr-2">After</span>
                                <span class="text-xs text-gray-500">(Upload image showing results)</span>
                            </div>
                            <div
                                class="after-upload-zone upload-zone cursor-pointer <?php echo ($afterImg ? 'hidden' : ''); ?>">
                                <div
                                    class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="ri-image-add-line ri-xl text-gray-400"></i>
                                </div>
                                <p class="text-xs text-gray-500">Click to upload after image</p>
                            </div>
                            <input type="file" id="afterImageInput" name="discussion_after_image"
                                accept=".jpg,.jpeg,.png" class="hidden">
                            <div id="afterPreview" class="<?php echo ($afterImg ? '' : 'hidden'); ?> mt-3">
                                <div class="relative">
                                    <img src="<?php echo htmlspecialchars($afterImg); ?>" alt="After"
                                        class="w-full h-48 object-cover object-top rounded-lg">
                                    <button type="button"
                                        class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 js-remove-existing-after">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="remove_after" id="removeAfter" value="0">
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <div id="tagInput" class="tag-input">
                        <input type="text" placeholder="Add tags to help others find your post..."
                            class="tag-input-field">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add tags.</p>
                </div>

                <!-- Privacy -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Privacy Settings</label>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">Allow comments</h4>
                                <p class="text-xs text-gray-500">Let community members comment on your post</p>
                            </div>
                            <div class="toggle-switch <?php echo $allowComments ? 'active' : ''; ?>"
                                data-setting="comments"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">Anonymous posting</h4>
                                <p class="text-xs text-gray-500">Hide your username and profile from this post</p>
                            </div>
                            <div class="toggle-switch <?php echo $anonymous ? 'active' : ''; ?>"
                                data-setting="anonymous"></div>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields for meta packing -->
                <input type="hidden" name="discussion_highlight" id="highlightJSON">
                <input type="hidden" name="discussion_description" id="contentHtml">
                <input type="hidden" name="discussion_anonymous" id="anonymousPost"
                    value="<?php echo $anonymous ? 1 : 0; ?>">

                <!-- Submit -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <button type="submit" id="submitDiscussion"
                        class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-medium rounded-md !rounded-button whitespace-nowrap">
                        <i
                            class="ri-send-plane-line mr-2"></i><?php echo $isEdit ? 'Update Discussion' : 'Publish Discussion'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>