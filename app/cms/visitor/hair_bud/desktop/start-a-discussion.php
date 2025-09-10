<?php

require_once LOGIN_CHECK;
require_once HOMEPAGE_FUNCTIONS;

$postType = ((isset($postType)) ? $postType : 'discussion');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Create New <?php echo ucwords(str_replace('_', ' ', $postType)) ?> - <?php echo SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981'
                    },
                    borderRadius: {
                        'none': '0px',
                        'sm': '4px',
                        DEFAULT: '8px',
                        'md': '12px',
                        'lg': '16px',
                        'xl': '20px',
                        '2xl': '24px',
                        '3xl': '32px',
                        'full': '9999px',
                        'button': '8px'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }

        input[type="search"]::-webkit-search-decoration,
        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-results-button,
        input[type="search"]::-webkit-search-results-decoration {
            display: none;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            min-width: 200px;
            z-index: 50;
            margin-top: 4px;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            color: #374151;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .dropdown-divider {
            border-top: 1px solid #e5e7eb;
            margin: 4px 0;
        }

        .category-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            min-width: 100%;
            z-index: 50;
            margin-top: 4px;
            max-height: 200px;
            overflow-y: auto;
        }

        .category-dropdown.show {
            display: block;
        }

        .category-option {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            color: #374151;
            font-size: 14px;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .category-option:hover {
            background-color: #f3f4f6;
        }

        .radio-custom {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            position: relative;
            cursor: pointer;
        }

        .radio-custom:checked {
            border-color: #4F46E5;
            background-color: #4F46E5;
        }

        .radio-custom:checked::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .toggle-switch {
            width: 44px;
            height: 24px;
            background-color: #d1d5db;
            border-radius: 12px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .toggle-switch.active {
            background-color: #4F46E5;
        }

        .toggle-switch::after {
            content: '';
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
            transition: transform 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-switch.active::after {
            transform: translateX(20px);
        }

        .tag-input {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            min-height: 44px;
            cursor: text;
        }

        .tag-item {
            display: flex;
            align-items: center;
            background-color: #e5e7eb;
            padding: 4px 8px;
            border-radius: 16px;
            font-size: 14px;
        }

        .tag-remove {
            margin-left: 4px;
            cursor: pointer;
            color: #6b7280;
        }

        .tag-remove:hover {
            color: #374151;
        }

        .tag-input input {
            border: none;
            outline: none;
            flex: 1;
            min-width: 120px;
            font-size: 14px;
        }

        .preview-panel {
            display: none;
        }

        .preview-panel.show {
            display: block;
        }

        .editor-toolbar {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px;
            display: flex;
            gap: 4px;
        }

        .editor-btn {
            padding: 6px 8px;
            border: none;
            background: transparent;
            border-radius: 4px;
            cursor: pointer;
            color: #6b7280;
        }

        .editor-btn:hover {
            background-color: #f3f4f6;
            color: #374151;
        }

        .editor-btn.active {
            background-color: #e5e7eb;
            color: #374151;
        }

        .editor-content {
            min-height: 200px;
            padding: 16px;
            border: none;
            outline: none;
            resize: none;
            font-family: inherit;
        }

        .upload-zone {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            transition: border-color 0.3s;
            cursor: pointer;
        }

        .upload-zone:hover {
            border-color: #4F46E5;
            background-color: #f8fafc;
        }

        .upload-zone.dragover {
            border-color: #4F46E5;
            background-color: #eff6ff;
        }

        .image-preview {
            display: none;
        }

        .image-preview.show {
            display: block;
        }
    </style>
</head>

<body>

    <?php

    require_once 'head.php';

    ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Form Container -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8">
                <form id="discussionForm">
                    <!-- Title Field -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Discussion Title</label>
                        <input type="text" id="title" name="title"
                            placeholder="Enter a descriptive title for your discussion..."
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
                                <div id="categoryDropdown" class="category-dropdown">

                                    <?php

                                    $categoryList = getAllCategoryList($connection, 'discussion');
                                    foreach ($categoryList as $category) {
                                        echo "<div class='category-option' data-value='" . str_replace(' ', '_', $category['category_title']) . "'>{$category['category_title']}</div>";
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
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
                            <div
                                class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                <i class="ri-image-add-line ri-xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each (max 5 images)</p>
                        </div>
                        <input type="file" id="imageInput" multiple accept="image/*" class="hidden">
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
                                <input type="file" id="beforeImageInput" accept="image/*" class="hidden">
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
                                <input type="file" id="afterImageInput" accept="image/*" class="hidden">
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
                        <button type="submit" id="publishButton"
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
    <script id="profileDropdownScript">
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');
            profileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });
            document.addEventListener('click', function() {
                profileDropdown.classList.remove('show');
            });
            profileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
    <script id="categoryDropdownScript">
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButton = document.getElementById('categoryButton');
            const categoryDropdown = document.getElementById('categoryDropdown');
            const categoryText = document.getElementById('categoryText');
            const categoryOptions = document.querySelectorAll('.category-option');
            categoryButton.addEventListener('click', function(e) {
                e.stopPropagation();
                categoryDropdown.classList.toggle('show');
            });
            document.addEventListener('click', function() {
                categoryDropdown.classList.remove('show');
            });
            categoryOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;
                    categoryText.textContent = text;
                    categoryText.classList.remove('text-gray-500');
                    categoryText.classList.add('text-gray-900');
                    categoryDropdown.classList.remove('show');
                });
            });
        });
    </script>
    <script id="titleCounterScript">
        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.getElementById('title');
            const titleCounter = document.getElementById('titleCounter');
            titleInput.addEventListener('input', function() {
                const length = this.value.length;
                titleCounter.textContent = `${length}/150`;
                if (length > 120) {
                    titleCounter.classList.add('text-yellow-600');
                    titleCounter.classList.remove('text-gray-400');
                }
                if (length > 140) {
                    titleCounter.classList.add('text-red-600');
                    titleCounter.classList.remove('text-yellow-600');
                }
                if (length <= 120) {
                    titleCounter.classList.add('text-gray-400');
                    titleCounter.classList.remove('text-yellow-600', 'text-red-600');
                }
            });
        });
    </script>
    <script id="richTextEditorScript">
        document.addEventListener('DOMContentLoaded', function() {
            const editorButtons = document.querySelectorAll('.editor-btn');
            const editor = document.getElementById('editor');
            editorButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const command = this.getAttribute('data-command');
                    if (command === 'createLink') {
                        const url = prompt('Enter URL:');
                        if (url) {
                            document.execCommand(command, false, url);
                        }
                    } else {
                        document.execCommand(command, false, null);
                    }
                    editor.focus();
                });
            });
            editor.addEventListener('focus', function() {
                if (this.textContent.trim() === '' && this.innerHTML === '') {
                    this.innerHTML = '';
                }
            });
            editor.addEventListener('blur', function() {
                if (this.textContent.trim() === '') {
                    this.innerHTML = '';
                }
            });
        });
    </script>
    <script id="imageUploadScript">
        document.addEventListener('DOMContentLoaded', function() {
            const uploadZone = document.getElementById('uploadZone');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const previewGrid = document.getElementById('previewGrid');
            const beforeAfterSection = document.getElementById('beforeAfterSection');
            let uploadedImages = [];

            // Show/hide before/after section based on post type
            const postTypeRadios = document.querySelectorAll('input[name="postType"]');
            postTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'success_story') {
                        beforeAfterSection.classList.remove('hidden');
                    } else {
                        beforeAfterSection.classList.add('hidden');
                    }
                });
            });

            // Before Image Upload
            const beforeUploadZone = document.querySelector('.before-upload-zone');
            const beforeImageInput = document.getElementById('beforeImageInput');
            const beforePreview = document.getElementById('beforePreview');

            beforeUploadZone.addEventListener('click', () => beforeImageInput.click());

            beforeImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        beforePreview.querySelector('img').src = e.target.result;
                        beforePreview.classList.remove('hidden');
                        beforeUploadZone.classList.add('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            beforePreview.querySelector('button').addEventListener('click', function() {
                beforePreview.classList.add('hidden');
                beforeUploadZone.classList.remove('hidden');
                beforeImageInput.value = '';
                beforePreview.querySelector('img').src = '';
            });

            // After Image Upload
            const afterUploadZone = document.querySelector('.after-upload-zone');
            const afterImageInput = document.getElementById('afterImageInput');
            const afterPreview = document.getElementById('afterPreview');

            afterUploadZone.addEventListener('click', () => afterImageInput.click());

            afterImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        afterPreview.querySelector('img').src = e.target.result;
                        afterPreview.classList.remove('hidden');
                        afterUploadZone.classList.add('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            afterPreview.querySelector('button').addEventListener('click', function() {
                afterPreview.classList.add('hidden');
                afterUploadZone.classList.remove('hidden');
                afterImageInput.value = '';
                afterPreview.querySelector('img').src = '';
            });
            uploadZone.addEventListener('click', function() {
                imageInput.click();
            });
            uploadZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            uploadZone.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });
            uploadZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files);
                handleFiles(files);
            });
            imageInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                handleFiles(files);
            });

            function handleFiles(files) {
                files.forEach(file => {
                    if (file.type.startsWith('image/') && uploadedImages.length < 5) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            uploadedImages.push({
                                file: file,
                                url: e.target.result
                            });
                            displayImages();
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            function displayImages() {
                previewGrid.innerHTML = '';
                uploadedImages.forEach((image, index) => {
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'relative';
                    imageContainer.innerHTML = `
<img src="${image.url}" alt="Preview" class="w-full h-24 object-cover object-top rounded-lg">
<button type="button" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600" onclick="removeImage(${index})">
<i class="ri-close-line"></i>
</button>
`;
                    previewGrid.appendChild(imageContainer);
                });
                if (uploadedImages.length > 0) {
                    imagePreview.classList.add('show');
                } else {
                    imagePreview.classList.remove('show');
                }
            }
            window.removeImage = function(index) {
                uploadedImages.splice(index, 1);
                displayImages();
            };
        });
    </script>
    <script id="tagInputScript">
        document.addEventListener('DOMContentLoaded', function() {
            const tagInput = document.getElementById('tagInput');
            const tagInputField = tagInput.querySelector('.tag-input-field');
            let tags = [];
            tagInputField.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addTag(this.value.trim());
                    this.value = '';
                }
            });
            tagInputField.addEventListener('blur', function() {
                if (this.value.trim()) {
                    addTag(this.value.trim());
                    this.value = '';
                }
            });

            function addTag(tagText) {
                if (tagText && !tags.includes(tagText) && tags.length < 10) {
                    tags.push(tagText);
                    renderTags();
                }
            }

            function removeTag(tagText) {
                tags = tags.filter(tag => tag !== tagText);
                renderTags();
            }

            function renderTags() {
                const existingTags = tagInput.querySelectorAll('.tag-item');
                existingTags.forEach(tag => tag.remove());
                tags.forEach(tag => {
                    const tagElement = document.createElement('div');
                    tagElement.className = 'tag-item';
                    tagElement.innerHTML = `
<span>${tag}</span>
<span class="tag-remove" onclick="removeTag('${tag}')">×</span>
`;
                    tagInput.insertBefore(tagElement, tagInputField);
                });
            }
            window.removeTag = removeTag;
        });
    </script>
    <script id="toggleSwitchScript">
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSwitches = document.querySelectorAll('.toggle-switch');
            toggleSwitches.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    this.classList.toggle('active');
                });
            });
        });
    </script>
</body>

</html>