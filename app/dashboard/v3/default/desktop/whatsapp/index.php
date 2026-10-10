<div class="package-page-wrapper whatsapp-inbox-wrapper" id="whatsappCrmApp">
    <!-- Top Bar -->
    <div class="package-header-bar wa-chat-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-whatsapp wa-inbox-icon">
                <span class="v3-wa-icon-svg wa-icon-emerald">
                    <svg viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                    </svg>
                </span>
            </div>
            <div class="package-header-titles">
                <h2>WhatsApp Live CRM Chat &amp; Messaging</h2>
                <p>Real-time customer conversations synced directly with linked WhatsApp number and CRM leads.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <!-- Multi-Device Switcher Dropdown (Admin & Multi-device accounts) -->
            <div class="wa-device-switcher-box" id="waDeviceSwitcherBox">
                <span class="wa-device-switcher-label"><i class="fi fi-rr-smartphone"></i> Inbox Line:</span>
                <div class="wa-device-select-wrap">
                    <select id="waDeviceSelect" class="wa-device-select" aria-label="Switch Device Inbox">
                        <option value="all">📱 All Devices</option>
                    </select>
                </div>
            </div>

            <span class="wa-device-status-badge" id="waHeaderStatusBadge">
                <span class="wa-pulse-dot"></span>
                <span id="waHeaderStatusText">Checking connection...</span>
            </span>
            <button type="button" class="btn-v3-action" id="btnSyncChatsInbox" data-tooltip="Sync All Chats from WhatsApp">
                <i class="fi fi-rr-refresh"></i> <span>Sync WhatsApp</span>
            </button>
            <button type="button" class="btn-v3-action" id="btnOpenNewChatModal" data-tooltip="Start New WhatsApp Chat">
                <i class="fi fi-rr-add"></i> <span>New Chat</span>
            </button>
            <a href="/dashboard/integration/whatsapp_web/" class="btn-v3-action" id="btnWaDeviceSettings" data-tooltip="Configure Linked Device">
                <i class="fi fi-rr-qr-scan"></i> <span>Device Settings</span>
            </a>
        </div>
    </div>

    <!-- Unlinked State Card (Shown when no WhatsApp device is linked) -->
    <div class="wa-unlinked-state-card d-none" id="waUnlinkedStateCard">
        <div class="wa-unlinked-card-body">
            <div class="wa-unlinked-icon-wrap">
                <span class="v3-wa-icon-svg wa-icon-emerald">
                    <svg viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                    </svg>
                </span>
                <span class="wa-unlinked-alert-badge">
                    <i class="fi fi-rr-cross-small"></i>
                </span>
            </div>
            <h3>No WhatsApp Device Linked</h3>
            <p id="waUnlinkedMessage">You must link a WhatsApp device before you can access the CRM Live Inbox, read incoming customer messages, or reply to leads.</p>
            <div class="wa-unlinked-actions">
                <a href="/dashboard/integration/whatsapp_web/" class="btn-integration-primary">
                    <i class="fi fi-rr-qr-scan"></i> Link WhatsApp Device Now
                </a>
                <button type="button" class="btn-wa-secondary" id="btnRecheckConnection">
                    <i class="fi fi-rr-refresh"></i> Check Connection Again
                </button>
            </div>
        </div>
    </div>

    <!-- Main Chat Workspace -->
    <div class="wa-chat-workspace" id="waChatWorkspace">
        <!-- Left Sidebar: Conversations Thread List -->
        <aside class="wa-threads-sidebar">
            <!-- Search & Filters -->
            <div class="wa-threads-header">
                <div class="wa-threads-header-top">
                    <button type="button" class="btn-wa-new-chat-sidebar" id="btnSidebarNewChat" title="Send WhatsApp Message to New Number">
                        <i class="fi fi-rr-comment-alt-add"></i> <span>+ New Conversation / Number</span>
                    </button>
                </div>
                <div class="wa-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" id="waThreadSearch" placeholder="Search chats by name or phone..." class="wa-search-input">
                    <button type="button" class="btn-wa-clear-search" id="btnClearSearch">
                        <i class="fi fi-rr-cross-small"></i>
                    </button>
                </div>
                <div class="wa-filter-tabs">
                    <button type="button" class="wa-filter-pill active" data-filter="all">All</button>
                    <button type="button" class="wa-filter-pill" data-filter="unread">Unread</button>
                    <button type="button" class="wa-filter-pill" data-filter="leads">CRM Leads</button>
                </div>

                <!-- Quick User Shortcut Bar (Admin only / Team filter) -->
                <div class="wa-users-shortcut-section d-none" id="waUsersShortcutSection">
                    <div class="wa-users-shortcut-header">
                        <span class="wa-users-shortcut-title"><i class="fi fi-rr-users-alt"></i> Team Chats:</span>
                        <button type="button" class="wa-users-shortcut-reset d-none" id="btnResetUserFilter" title="Show all team chats">Clear Filter</button>
                    </div>
                    <div class="wa-users-shortcut-scroll" id="waUsersShortcutBar">
                        <!-- Populated dynamically via JS: All Team, Avinash, Pooja, etc. -->
                    </div>
                </div>

                <!-- Active Filter Notice Banner -->
                <div class="wa-active-filter-banner d-none" id="waActiveFilterBanner">
                    <span id="waActiveFilterText">Showing chats</span>
                    <button type="button" class="btn-wa-dismiss-filter" id="btnDismissFilterBanner" title="Clear filter"><i class="fi fi-rr-cross-small"></i></button>
                </div>
            </div>

            <!-- Thread Items Container -->
            <div class="wa-threads-scroll" id="waThreadsContainer">
                <div class="wa-loading-threads">
                    <i class="fi fi-rr-spinner wa-spin"></i>
                    <span>Loading conversations...</span>
                </div>
            </div>
        </aside>

        <!-- Right Main: Active Message View -->
        <section class="wa-chat-main-panel" id="waChatMainPanel">
            <!-- Empty State (when no chat selected) -->
            <div class="wa-empty-chat-state" id="waEmptyState">
                <div class="wa-empty-art">
                    <i class="fi fi-rr-comments"></i>
                </div>
                <h3>WhatsApp CRM Workspace</h3>
                <p>Select a conversation from the left to read messages and reply to leads, or start a new chat.</p>
                <div class="wa-empty-actions">
                    <button type="button" class="btn-integration-primary" id="btnEmptyStartChat">
                        <i class="fi fi-rr-paper-plane"></i> Start New Chat
                    </button>
                    <a href="/dashboard/integration/whatsapp_web/" class="btn-wa-secondary">
                        <i class="fi fi-rr-qr-scan"></i> Check Device Status
                    </a>
                </div>
            </div>

            <!-- Active Chat Conversation View (hidden initially) -->
            <div class="wa-active-chat-wrapper" id="waActiveChatWrapper">
                <!-- Chat Top Header -->
                <div class="wa-active-chat-header">
                    <div class="wa-active-user-meta">
                        <button type="button" class="btn-wa-back-to-list" id="btnBackToThreads" title="Back to conversations">
                            <i class="fi fi-rr-arrow-small-left"></i>
                        </button>
                        <div class="wa-user-avatar" id="waActiveAvatar">WA</div>
                        <div class="wa-user-details">
                            <div class="wa-user-name-row">
                                <h4 id="waActiveName">Contact Name</h4>
                                <span class="wa-lead-tag" id="waActiveLeadBadge">
                                    <i class="fi fi-rr-bullseye-arrow"></i> <span id="waActiveLeadText">Lead #0</span>
                                </span>
                            </div>
                            <div class="wa-user-sub-row">
                                <span class="wa-user-phone" id="waActivePhone">+91 ...</span>
                                <button type="button" class="btn-wa-copy-phone" id="btnCopyActivePhone" title="Copy Phone Number">
                                    <i class="fi fi-rr-copy"></i>
                                </button>
                                <span class="wa-dot-sep">•</span>
                                <span class="wa-user-online-status" id="waActiveStatus">WhatsApp Connected</span>
                                <span class="wa-header-device-badge d-none" id="waActiveDeviceBadge"></span>
                            </div>
                        </div>
                    </div>

                    <div class="wa-active-chat-actions">
                        <a href="#" target="_blank" class="btn-wa-header-action" id="waViewLeadLink" title="View CRM Lead">
                            <i class="fi fi-rr-user"></i> <span>View Lead</span>
                        </a>
                        <button type="button" class="btn-wa-header-action" id="btnRefreshActiveChat" title="Refresh messages">
                            <i class="fi fi-rr-refresh"></i>
                        </button>
                    </div>
                </div>

                <!-- Chat Message Stream Wrap -->
                <div class="wa-messages-stream-wrap">
                    <div class="wa-messages-stream" id="waMessagesStream">
                        <div class="wa-stream-loading">
                            <i class="fi fi-rr-spinner wa-spin"></i>
                            <span>Loading messages...</span>
                        </div>
                    </div>
                    <button type="button" class="btn-wa-scroll-bottom d-none" id="btnScrollToBottom" title="Scroll to latest messages">
                        <i class="fi fi-rr-angle-small-down"></i>
                        <span class="wa-scroll-unread-dot d-none" id="waScrollUnreadDot"></span>
                    </button>
                </div>

                <!-- Chat Input Composer -->
                <div class="wa-composer-area">
                    <!-- Quick Response Templates bar -->
                    <div class="wa-quick-replies-bar">
                        <span class="quick-label"><i class="fi fi-rr-magic-wand"></i> Quick:</span>
                        <button type="button" class="wa-quick-chip" data-text="Hello! Thank you for contacting us. How can we help you today with your travel plans?">Greeting</button>
                        <button type="button" class="wa-quick-chip" data-text="We have prepared a personalized itinerary quotation for you. Please check and let us know your feedback!">Send Itinerary</button>
                        <button type="button" class="wa-quick-chip" data-text="A payment link has been generated for your booking reservation. Please find the details above.">Payment Link</button>
                        <button type="button" class="wa-quick-chip" data-text="Thank you for confirming your trip with us! Your booking confirmation voucher will follow shortly.">Confirmation</button>
                    </div>

                    <!-- Message Form -->
                    <form id="waMessageSendForm" class="wa-composer-form">
                        <div class="wa-input-row">
                            <textarea id="waComposerText" class="wa-composer-textarea" placeholder="Type a message (Press Enter to send, Shift+Enter for new line)..." rows="1"></textarea>
                            <button type="submit" class="btn-wa-send" id="btnSendMessage" title="Send WhatsApp Message">
                                <i class="fi fi-rr-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal: Start New Chat -->
    <div class="wa-modal-overlay" id="waNewChatModal" aria-hidden="true">
        <div class="wa-modal-dialog">
            <div class="wa-modal-header">
                <h3><i class="fi fi-rr-comment-alt-middle"></i> Start New WhatsApp Conversation</h3>
                <button type="button" class="btn-wa-modal-close" id="btnCloseNewChatModal">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <form id="waNewChatForm" class="wa-modal-body">
                <div class="v3-form-group d-none" id="waNewChatDeviceGroup">
                    <label for="newChatDeviceSelect" class="v3-form-label">Send From WhatsApp Line</label>
                    <div class="v3-input-icon-wrap">
                        <i class="fi fi-rr-smartphone input-icon"></i>
                        <select id="newChatDeviceSelect" class="v3-form-input"></select>
                    </div>
                </div>
                <div class="v3-form-group">
                    <label for="newChatPhone" class="v3-form-label">Customer Mobile Number <span class="required">*</span></label>
                    <div class="v3-input-icon-wrap">
                        <i class="fi fi-rr-phone-call input-icon"></i>
                        <input type="tel" id="newChatPhone" class="v3-form-input" placeholder="e.g. +91 9876543210" required>
                    </div>
                    <span class="v3-field-hint">Include country code for international destinations.</span>
                </div>
                <div class="v3-form-group">
                    <label for="newChatName" class="v3-form-label">Contact / Customer Name <span class="optional">(Optional)</span></label>
                    <div class="v3-input-icon-wrap">
                        <i class="fi fi-rr-user input-icon"></i>
                        <input type="text" id="newChatName" class="v3-form-input" placeholder="e.g. John Doe">
                    </div>
                </div>
                <div class="v3-form-group">
                    <label for="newChatMessage" class="v3-form-label">Initial Message <span class="required">*</span></label>
                    <textarea id="newChatMessage" class="v3-form-input wa-modal-textarea" rows="3" placeholder="Hello, I am contacting you regarding your inquiry..." required></textarea>
                </div>
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa-secondary" id="btnCancelNewChat">Cancel</button>
                    <button type="submit" class="btn-integration-primary" id="btnSubmitNewChat">
                        <i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
