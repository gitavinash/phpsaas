<?php

require_once 'header.php';

?>
<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Form Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8">
            <form id="discussionForm">
                <!-- Title Field -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter a descriptive title..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                        maxlength="150">
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500">Make it clear and specific to attract the right audience
                        </p>
                        <span id="titleCounter" class="text-xs text-gray-400">0/150</span>
                    </div>
                </div>
                <!-- Category and Post Type Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Category Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <div class="relative">
                            <button type="button" id="categoryButton"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-left bg-white flex items-center justify-between">
                                <span id="categoryText" class="text-gray-500">Select a category...</span>
                                <i class="ri-arrow-down-s-line ri-lg text-gray-400"></i>
                            </button>
                            <input type="hidden" id="categoryValue" name="categoryValue" value="" />
                            <div id="categoryDropdown" class="category-dropdown">

                                <?php

                                $categoryList = getAllCategoryList($connection, 'discussion');
                                foreach ($categoryList as $category) {
                                    echo "<div class='category-option' data-value='" . str_replace(' ', '_', $category['category_id']) . "'>{$category['category_title']}</div>";
                                }

                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Post Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Post Type</label>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="postType" value="discussion" class="radio-custom mr-3"
                                    <?php echo (($postType == 'discussion') ? 'checked' : '') ?>>
                                <div>
                                    <span class="font-medium text-gray-900 text-sm">Discussion</span>
                                    <p class="text-xs text-gray-500">Share experiences and start conversations</p>
                                </div>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="postType" value="questions" class="radio-custom mr-3"
                                    <?php echo (($postType == 'questions') ? 'checked' : '') ?>>
                                <div>
                                    <span class="font-medium text-gray-900 text-sm">Question</span>
                                    <p class="text-xs text-gray-500">Ask for advice or specific information</p>
                                </div>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="postType" value="success_story" class="radio-custom mr-3"
                                    <?php echo (($postType == 'success_story') ? 'checked' : '') ?>>
                                <div>
                                    <span class="font-medium text-gray-900 text-sm">Success Story</span>
                                    <p class="text-xs text-gray-500">Share your positive results and journey</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Rich Text Editor -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your experiences</label>
                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                        <div class="editor-toolbar bg-gray-50">
                            <button type="button" class="editor-btn" data-command="bold" title="Bold">
                                <i class="ri-bold ri-lg"></i>
                            </button>
                            <button type="button" class="editor-btn" data-command="italic" title="Italic">
                                <i class="ri-italic ri-lg"></i>
                            </button>
                            <button type="button" class="editor-btn" data-command="underline" title="Underline">
                                <i class="ri-underline ri-lg"></i>
                            </button>
                            <div class="w-px h-6 bg-gray-300 mx-1"></div>
                            <button type="button" class="editor-btn" data-command="insertUnorderedList"
                                title="Bullet List">
                                <i class="ri-list-unordered ri-lg"></i>
                            </button>
                            <button type="button" class="editor-btn" data-command="insertOrderedList"
                                title="Numbered List">
                                <i class="ri-list-ordered ri-lg"></i>
                            </button>
                            <div class="w-px h-6 bg-gray-300 mx-1"></div>
                            <button type="button" class="editor-btn" data-command="createLink" title="Insert Link">
                                <i class="ri-link ri-lg"></i>
                            </button>
                        </div>
                        <div id="editor" class="editor-content" contenteditable="true"
                            placeholder="Share your thoughts, experiences, or questions in detail. The more information you provide, the better the community can help you.">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Use the toolbar above to format your text. Be detailed and
                        specific to get the best responses.</p>
                </div>
                <!-- Image Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Images (Optional)</label>
                    <div id="uploadZone" class="upload-zone">
                        <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                            <i class="ri-image-add-line ri-xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600 font-medium mb-1">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each (max 5 images)</p>
                    </div>
                    <input type="file" id="imageInput" multiple accept=".jpg,.jpeg,.png" class="hidden">
                    <div id="imagePreview" class="image-preview mt-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="previewGrid"></div>
                    </div>
                </div>

                <!-- Before/After Comparison (Success Story Only) -->
                <div id="beforeAfterSection"
                    class="mb-6 <?php echo ($postType === 'success_story' ? '' : 'hidden'); ?>">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Before & After Comparison</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Before Image -->
                        <div>
                            <div class="mb-2 flex items-center">
                                <span class="text-sm font-medium text-gray-900 mr-2">Before</span>
                                <span class="text-xs text-gray-500">(Upload image showing initial condition)</span>
                            </div>
                            <div class="before-upload-zone upload-zone cursor-pointer">
                                <div
                                    class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="ri-image-add-line ri-xl text-gray-400"></i>
                                </div>
                                <p class="text-xs text-gray-500">Click to upload before image</p>
                            </div>
                            <input type="file" id="beforeImageInput" accept=".jpg,.jpeg,.png" class="hidden">
                            <div id="beforePreview" class="hidden mt-3">
                                <div class="relative">
                                    <img src="" alt="Before" class="w-full h-48 object-cover object-top rounded-lg">
                                    <button type="button"
                                        class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- After Image -->
                        <div>
                            <div class="mb-2 flex items-center">
                                <span class="text-sm font-medium text-gray-900 mr-2">After</span>
                                <span class="text-xs text-gray-500">(Upload image showing results)</span>
                            </div>
                            <div class="after-upload-zone upload-zone cursor-pointer">
                                <div
                                    class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="ri-image-add-line ri-xl text-gray-400"></i>
                                </div>
                                <p class="text-xs text-gray-500">Click to upload after image</p>
                            </div>
                            <input type="file" id="afterImageInput" accept=".jpg,.jpeg,.png" class="hidden">
                            <div id="afterPreview" class="hidden mt-3">
                                <div class="relative">
                                    <img src="" alt="After" class="w-full h-48 object-cover object-top rounded-lg">
                                    <button type="button"
                                        class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Add before & after images to better showcase your progress
                        and results</p>
                </div>
                <!-- Tags Input -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <div id="tagInput" class="tag-input">
                        <input type="text" placeholder="Add tags to help others find your post..."
                            class="tag-input-field">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add tags. Suggested: finasteride,
                        minoxidil, hair transplant, dermatologist</p>
                </div>
                <!-- Privacy Settings -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Privacy Settings</label>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">Allow comments</h4>
                                <p class="text-xs text-gray-500">Let community members comment on your post</p>
                            </div>
                            <div class="toggle-switch active" data-setting="comments"></div>
                        </div>
                        <!-- <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">Email notifications</h4>
                                    <p class="text-xs text-gray-500">Get notified when someone responds to your post</p>
                                </div>
                                <div class="toggle-switch active" data-setting="notifications"></div>
                            </div> -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">Anonymous posting</h4>
                                <p class="text-xs text-gray-500">Hide your username and profile from this post</p>
                            </div>
                            <div class="toggle-switch" data-setting="anonymous"></div>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <!-- <button type="button" id="saveDraftButton"
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-md !rounded-button whitespace-nowrap">
                            <i class="ri-save-line mr-2"></i>Save as Draft
                        </button> -->
                    <button type="submit" id="submitADiscussion"
                        class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-medium rounded-md !rounded-button whitespace-nowrap">
                        <i class="ri-send-plane-line mr-2"></i>Publish Discussion
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php

require_once 'footer.php';

?>