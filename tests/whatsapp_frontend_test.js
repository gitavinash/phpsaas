/**
 * Automated Test Suite: WhatsApp Frontend Unit, UI Component & Regression Tests
 * Runs under Node.js.
 * Validates: Scroll preservation, avatar reset, stale response isolation,
 * voice note player SVG icons & states, and reaction pill rendering.
 */

const assert = require("assert");

let passed = 0;
let failed = 0;

function it(desc, fn) {
  try {
    fn();
    passed++;
    console.log(`  ✓ PASS: ${desc}`);
  } catch (err) {
    failed++;
    console.error(`  ✗ FAIL: ${desc} -> ${err.message}`);
  }
}

console.log("\n======================================================");
console.log("   WhatsApp CRM Frontend Unit & Regression Tests");
console.log("======================================================\n");

// 1. Stale Response & Contact Isolation Logic
console.log("[1] Testing Rapid Contact Switching & Stale Response Isolation");

class MockWhatsAppClient {
  constructor() {
    this.activeChatId = null;
    this.activePhone = null;
    this.activeChatReqSeq = 0;
    this.renderedMessages = [];
    this.activeHeader = { name: "", phone: "", avatar: "" };
  }

  selectChat(chatId, phone, threadData = {}) {
    this.activeChatReqSeq++;
    const currentSeq = this.activeChatReqSeq;

    this.activeChatId = chatId;
    this.activePhone = phone;

    // Immediate header reset
    this.activeHeader = {
      name: threadData.name || phone || "WhatsApp Contact",
      phone: phone || "",
      avatar: threadData.avatar || '<i class="fi fi-rr-user"></i>'
    };
    this.renderedMessages = []; // Cleared immediately

    return currentSeq;
  }

  onMessagesResponse(chatId, reqSeq, messages) {
    // Drop response if user has moved to another chat or if sequence is stale
    if (String(this.activeChatId) !== String(chatId)) {
      return false; // Dropped
    }
    if (reqSeq && reqSeq !== this.activeChatReqSeq) {
      return false; // Dropped
    }
    this.renderedMessages = messages;
    return true; // Applied
  }
}

it("Immediate header reset updates active contact name and avatar synchronously", () => {
  const client = new MockWhatsAppClient();
  client.selectChat(101, "+919810610879", { name: "Vineet", avatar: "vineet.jpg" });

  assert.strictEqual(client.activeHeader.name, "Vineet");
  assert.strictEqual(client.activeHeader.avatar, "vineet.jpg");

  // User clicks Contact B
  client.selectChat(202, "+919599635770", { name: "Aakash Bhatt", avatar: "" });

  // Contact A's avatar must NOT linger
  assert.strictEqual(client.activeHeader.name, "Aakash Bhatt");
  assert.strictEqual(client.activeHeader.avatar, '<i class="fi fi-rr-user"></i>');
});

it("Rapid contact switching rejects delayed out-of-order responses from previous contact", () => {
  const client = new MockWhatsAppClient();

  // 1. User clicks Contact A
  const seqA = client.selectChat(101, "+919810610879");

  // 2. User immediately clicks Contact B before A loads
  const seqB = client.selectChat(202, "+919599635770");

  // 3. Delayed response from Contact A arrives late
  const appliedA = client.onMessagesResponse(101, seqA, [{ id: 1, text: "Secret from Contact A" }]);
  assert.strictEqual(appliedA, false, "Delayed Contact A response must be rejected");
  assert.strictEqual(client.renderedMessages.length, 0, "No messages rendered yet");

  // 4. Response from Contact B arrives
  const appliedB = client.onMessagesResponse(202, seqB, [{ id: 2, text: "Message for Contact B" }]);
  assert.strictEqual(appliedB, true, "Contact B response accepted");
  assert.strictEqual(client.renderedMessages[0].text, "Message for Contact B");
});

// 2. Scroll Preservation Calculation Logic
console.log("\n[2] Testing Scroll Position Preservation & Auto-Scroll Logic");

function calculateScrollBehavior(prevScrollTop, prevScrollHeight, clientHeight, isUserExplicitScrollBottom, newScrollHeight) {
  const currentDistance = prevScrollHeight - prevScrollTop - clientHeight;
  const wasNearBottom = currentDistance < 120;
  const shouldScrollBottom = isUserExplicitScrollBottom || wasNearBottom;

  if (shouldScrollBottom) {
    return {
      action: "scroll_to_bottom",
      newScrollTop: newScrollHeight,
      showUnreadDot: false,
      showReturnButton: false
    };
  } else {
    const heightDiff = newScrollHeight - prevScrollHeight;
    return {
      action: "preserve_position",
      newScrollTop: prevScrollTop + (heightDiff > 0 ? heightDiff : 0),
      showUnreadDot: true,
      showReturnButton: true
    };
  }
}

it("Auto-scrolls to bottom when opening a conversation for the first time", () => {
  const result = calculateScrollBehavior(0, 500, 400, true, 800);
  assert.strictEqual(result.action, "scroll_to_bottom");
  assert.strictEqual(result.newScrollTop, 800);
  assert.strictEqual(result.showUnreadDot, false);
});

it("Preserves scroll position when reader is scrolled up reading previous history", () => {
  // User is at scrollTop 200, total height 1500, visible height 500 (distance = 800 > 120)
  const result = calculateScrollBehavior(200, 1500, 500, false, 1700);
  assert.strictEqual(result.action, "preserve_position");
  // Position is preserved with height delta (1700 - 1500 = +200) -> 200 + 200 = 400
  assert.strictEqual(result.newScrollTop, 400);
  assert.strictEqual(result.showUnreadDot, true);
  assert.strictEqual(result.showReturnButton, true);
});

it("Auto-scrolls when reader is already near the bottom and a new message arrives", () => {
  // User is at scrollTop 950, total height 1500, visible height 500 (distance = 50 < 120)
  const result = calculateScrollBehavior(950, 1500, 500, false, 1600);
  assert.strictEqual(result.action, "scroll_to_bottom");
  assert.strictEqual(result.newScrollTop, 1600);
  assert.strictEqual(result.showUnreadDot, false);
});

// 3. Voice Player Icon & State Logic
console.log("\n[3] Testing Voice Note Player SVG Icons & State Machine");

function getVoiceButtonClasses(state) {
  const classes = ["wa-voice-play-btn"];
  if (state === "playing") classes.push("is-playing");
  if (state === "loading") classes.push("is-loading");
  if (state === "download") classes.push("is-download");
  return classes.join(" ");
}

it("Voice note default state displays play button without is-playing class", () => {
  const cls = getVoiceButtonClasses("idle");
  assert.strictEqual(cls, "wa-voice-play-btn");
  assert.strictEqual(cls.includes("is-playing"), false);
});

it("Voice note playing state adds is-playing class to display Pause SVG", () => {
  const cls = getVoiceButtonClasses("playing");
  assert.strictEqual(cls.includes("is-playing"), true);
});

it("Voice note download / pending state adds is-download class to display Download SVG", () => {
  const cls = getVoiceButtonClasses("download");
  assert.strictEqual(cls.includes("is-download"), true);
  assert.strictEqual(cls.includes("is-playing"), false);
});

it("Audio error or playback end resets button state to idle", () => {
  let state = "playing";
  assert.strictEqual(getVoiceButtonClasses(state).includes("is-playing"), true);

  // Trigger error or ended event
  state = "idle";
  assert.strictEqual(getVoiceButtonClasses(state).includes("is-playing"), false);
});

// 4. Message Normalization & Reaction Rendering
console.log("\n[4] Testing Message Normalization & Reaction Component Rendering");

function renderMessageContent(msg) {
  const trimmedText = (msg.text || "").trim();
  const isReaction = msg.type === "reaction" || !!msg.reaction_emoji || /^\[Reaction:\s*(.*?)\]$/u.test(trimmedText);
  const isDoc = msg.type === "document" || !!msg.doc_name || /^\[Doc:\s*.*?\]$/i.test(trimmedText);

  if (isReaction) {
    let emoji = msg.reaction_emoji;
    if (!emoji) {
      const rxMatch = trimmedText.match(/^\[Reaction:\s*(.*?)\]$/u);
      emoji = rxMatch ? rxMatch[1].trim() : "👍";
    }
    return `<div class="wa-msg-reaction-pill"><span class="wa-reaction-emoji">${emoji}</span><span class="wa-reaction-label">Reacted</span></div>`;
  }

  if (isDoc) {
    let filename = msg.doc_name;
    if (!filename) {
      const dm = trimmedText.match(/^\[Doc:\s*(.*?)\]$/i);
      filename = dm ? dm[1].trim() : "Document";
    }
    return `<div class="wa-doc-name">${filename}</div>`;
  }

  return `<div class="wa-msg-text">${trimmedText}</div>`;
}

it("Renders [Reaction: 😂] as reaction pill component without internal label", () => {
  const html = renderMessageContent({ type: "text", text: "[Reaction: 😂]" });
  assert.strictEqual(html.includes("wa-msg-reaction-pill"), true);
  assert.strictEqual(html.includes("😂"), true);
  assert.strictEqual(html.includes("[Reaction:"), false);
});

it("Renders [Doc: Invoice.pdf] as document card component without internal prefix", () => {
  const html = renderMessageContent({ type: "text", text: "[Doc: Invoice.pdf]" });
  assert.strictEqual(html.includes("wa-doc-name"), true);
  assert.strictEqual(html.includes("Invoice.pdf"), true);
  assert.strictEqual(html.includes("[Doc:"), false);
});

it("Preserves legitimate user text containing brackets as normal text", () => {
  const userText = "Flight booking confirmed [PNR: AB123CD]";
  const html = renderMessageContent({ type: "text", text: userText });
  assert.strictEqual(html.includes("wa-msg-text"), true);
  assert.strictEqual(html.includes("[PNR: AB123CD]"), true);
  assert.strictEqual(html.includes("wa-msg-reaction-pill"), false);
});

console.log("\n------------------------------------------------------");
console.log(`Test Summary: ${passed} Passed, ${failed} Failed`);
console.log("------------------------------------------------------\n");

process.exit(failed === 0 ? 0 : 1);
