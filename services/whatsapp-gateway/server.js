/**
 * ISW CRM WhatsApp Web Gateway Daemon - Multi-Device Edition
 * Powered by @whiskeysockets/baileys
 * Path: services/whatsapp-gateway/server.js
 */

const {
  default: makeWASocket,
  useMultiFileAuthState,
  makeCacheableSignalKeyStore,
  DisconnectReason,
  Browsers,
  fetchLatestBaileysVersion,
  ALL_WA_PATCH_NAMES,
  jidNormalizedUser,
  jidDecode,
  isJidBroadcast,
  isJidGroup,
  isJidStatusBroadcast,
  isJidNewsletter,
} = require("@whiskeysockets/baileys");
const QRCode = require("qrcode");
const express = require("express");
const cors = require("cors");
const pino = require("pino");
const fs = require("fs");
const path = require("path");
const http = require("http");
const https = require("https");

// Auto-load .env file if present in gateway directory
const envFile = path.join(__dirname, ".env");
if (fs.existsSync(envFile)) {
  try {
    const lines = fs.readFileSync(envFile, "utf8").split(/\r?\n/);
    for (const line of lines) {
      const trimmed = line.trim();
      if (trimmed && !trimmed.startsWith("#")) {
        const idx = trimmed.indexOf("=");
        if (idx > 0) {
          const k = trimmed.substring(0, idx).trim();
          const v = trimmed.substring(idx + 1).trim().replace(/^["']|["']$/g, "");
          if (!process.env[k]) process.env[k] = v;
        }
      }
    }
  } catch (e) {}
}

const PORT = process.env.PORT || 3001;
const AUTH_DIR = path.join(__dirname, "auth_session");
const CRM_WEBHOOK_URL = process.env.CRM_WEBHOOK_URL || "http://localhost/api/v3/whatsapp/webhook/";

if (!fs.existsSync(AUTH_DIR)) {
  fs.mkdirSync(AUTH_DIR, { recursive: true });
}

const app = express();
app.use(cors());
app.use(express.json());

process.on("uncaughtException", (err) => {
  console.error("[WhatsApp Gateway] Uncaught Exception:", err);
});

process.on("unhandledRejection", (reason, promise) => {
  console.error("[WhatsApp Gateway] Unhandled Rejection:", reason);
});

const logger = pino({ level: "error" });

// Helper to decode message text across various WhatsApp formats
function getMessageText(message) {
  if (!message) return "";
  if (message.viewOnceMessage?.message) message = message.viewOnceMessage.message;
  if (message.viewOnceMessageV2?.message) message = message.viewOnceMessageV2.message;
  if (message.ephemeralMessage?.message) message = message.ephemeralMessage.message;
  if (message.documentWithCaptionMessage?.message) message = message.documentWithCaptionMessage.message;
  if (message.editedMessage?.message) message = message.editedMessage.message;

  if (message.conversation) return message.conversation;
  if (message.extendedTextMessage?.text) return message.extendedTextMessage.text;
  if (message.imageMessage?.caption) return message.imageMessage.caption || "[Image]";
  if (message.imageMessage) return "[Image]";
  if (message.videoMessage?.caption) return message.videoMessage.caption || "[Video]";
  if (message.videoMessage) return "[Video]";
  if (message.audioMessage) return "[Voice Note]";
  if (message.documentMessage) return message.documentMessage.title || message.documentMessage.fileName ? `[Doc: ${message.documentMessage.fileName || message.documentMessage.title}]` : "[Document]";
  if (message.contactMessage) return `[Contact: ${message.contactMessage.displayName || ""}]`;
  if (message.locationMessage) return `[Location: ${message.locationMessage.name || message.locationMessage.address || ""}]`;
  if (message.stickerMessage) return "[Sticker]";
  return "";
}

// -----------------------------------------------------------------------------
// WhatsApp Multi-Session Instance
// -----------------------------------------------------------------------------
class WhatsAppSession {
  constructor(sessionId) {
    this.sessionId = sessionId;
    this.authDir = sessionId === "default" ? AUTH_DIR : path.join(AUTH_DIR, sessionId);
    if (!fs.existsSync(this.authDir)) {
      fs.mkdirSync(this.authDir, { recursive: true });
    }
    this.storePath = path.join(this.authDir, "baileys_store.json");

    this.sock = null;
    this.currentQrSvg = null;
    this.currentQrString = null;
    this.qrGeneratedAt = 0;
    this.connectionStatus = "disconnected"; // disconnected, qr_ready, connected
    this.connectedUser = null;
    this.lastSyncTime = null;
    this.hasSyncedInitial = false;

    this.chatStore = new Map();
    this.contactStore = new Map();
    this.lidPhoneMap = new Map();
    this.messageStore = new Map();
    this.avatarCache = new Map();

    this.webhookUrl = null;
    this.siteId = null;

    this.loadStore();
    this.setupAutoSave();
  }

  async getMessage(key) {
    if (!key) return undefined;
    const msgId = key.id;

    // 1. Direct msgId match
    if (msgId && this.messageStore.has(msgId)) {
      return this.messageStore.get(msgId);
    }

    // 2. Exact combo lookup
    if (key.remoteJid && msgId) {
      const combo = `${key.remoteJid}:${msgId}`;
      if (this.messageStore.has(combo)) {
        return this.messageStore.get(combo);
      }
      const normJid = jidNormalizedUser(key.remoteJid);
      const normCombo = `${normJid}:${msgId}`;
      if (this.messageStore.has(normCombo)) {
        return this.messageStore.get(normCombo);
      }
    }

    // 3. Search across all stored keys for matching message ID
    if (msgId) {
      for (const [k, v] of this.messageStore.entries()) {
        if (k === msgId || k.endsWith(`:${msgId}`)) {
          return v;
        }
      }
    }

    // 4. Fallback: return last known text message from chatStore
    const cleanJid = key.remoteJid ? jidNormalizedUser(key.remoteJid) : null;
    if (cleanJid) {
      const chat = this.chatStore.get(cleanJid);
      if (chat && chat.lastMessageText) {
        return { conversation: chat.lastMessageText };
      }
    }
    return undefined;
  }

  notify(payload) {
    const enriched = {
      session_id: this.sessionId,
      site_id: this.siteId || payload.site_id || null,
      ...payload,
    };
    notifyCrm(enriched, this.webhookUrl);
  }

  loadStore() {
    try {
      if (fs.existsSync(this.storePath)) {
        const raw = JSON.parse(fs.readFileSync(this.storePath, "utf8"));
        if (raw.webhookUrl) this.webhookUrl = raw.webhookUrl;
        if (raw.siteId) this.siteId = raw.siteId;
        if (Array.isArray(raw.chats)) raw.chats.forEach((c) => { if (c && c.id) this.chatStore.set(c.id, c); });
        if (Array.isArray(raw.contacts)) raw.contacts.forEach((c) => { if (c && c.id) this.contactStore.set(c.id, c); });
        if (Array.isArray(raw.lidMap)) raw.lidMap.forEach(([k, v]) => this.lidPhoneMap.set(k, v));
        console.log(`[WhatsApp Gateway:${this.sessionId}] Restored ${this.chatStore.size} chats, ${this.contactStore.size} contacts, webhook: ${this.webhookUrl || 'default'}.`);
      }
    } catch (e) {
      console.error(`[WhatsApp Gateway:${this.sessionId}] Error reading store:`, e.message);
    }
  }

  setupAutoSave() {
    setInterval(() => {
      try {
        if (fs.existsSync(this.authDir)) {
          const data = {
            webhookUrl: this.webhookUrl,
            siteId: this.siteId,
            chats: Array.from(this.chatStore.values()),
            contacts: Array.from(this.contactStore.values()),
            lidMap: Array.from(this.lidPhoneMap.entries()),
          };
          fs.writeFileSync(this.storePath, JSON.stringify(data));
        }
      } catch (e) {}
    }, 15000);
  }

  getPhoneNumber(jid, altJid = null) {
    if (altJid && !isJidBroadcast(altJid) && !isJidGroup(altJid)) {
      const normAlt = jidNormalizedUser(altJid);
      const decAlt = jidDecode(normAlt);
      if (decAlt?.server === "s.whatsapp.net" && decAlt?.user) {
        const digits = decAlt.user.replace(/[^0-9]/g, "");
        if (digits.length >= 7) return "+" + digits;
      }
    }

    if (!jid) return null;
    if (isJidBroadcast(jid) || isJidGroup(jid) || isJidStatusBroadcast(jid) || isJidNewsletter(jid)) return null;

    const normalized = jidNormalizedUser(jid);
    const decoded = jidDecode(normalized);
    if (!decoded || !decoded.user) return null;

    // Handle WhatsApp Privacy LID (Linked Device identifier)
    if (decoded.server === "lid") {
      let mapped = this.lidPhoneMap.get(normalized) || this.lidPhoneMap.get(decoded.user + "@lid");
      if (!mapped) {
        for (const [k, c] of this.contactStore.entries()) {
          const cLidNorm = c.lid ? jidNormalizedUser(c.lid) : null;
          if (cLidNorm === normalized || c.lid === normalized || c.lid === (decoded.user + "@lid")) {
            mapped = k;
            this.lidPhoneMap.set(normalized, k);
            break;
          }
        }
      }
      if (mapped) {
        const mappedDec = jidDecode(mapped);
        if (mappedDec?.user) {
          const digits = mappedDec.user.replace(/[^0-9]/g, "");
          if (digits.length >= 7) return "+" + digits;
        }
      }
      // Check direct contact phone / phoneNumber
      const directCt = this.contactStore.get(normalized) || this.contactStore.get(decoded.user + "@lid");
      if (directCt) {
        const p = directCt.phoneNumber || directCt.phone;
        if (p) {
          const digits = String(p).replace(/[^0-9]/g, "");
          if (digits.length >= 7) return "+" + digits;
        }
      }
      if (this.connectedUser?.phone && this.sock?.user?.lid && normalized.startsWith(this.sock.user.lid.split(":")[0])) {
        return this.connectedUser.phone;
      }
      // Never return raw LID digits as a phone number
      return null;
    }

    const digits = decoded.user.replace(/[^0-9]/g, "");
    if (!digits || digits.length < 7) return null;
    return "+" + digits;
  }

  async resolvePhoneNumber(jid, altJid = null) {
    const syncPhone = this.getPhoneNumber(jid, altJid);
    if (syncPhone) return syncPhone;

    if (!jid) return null;
    const normalized = jidNormalizedUser(jid);
    const decoded = jidDecode(normalized);
    if (!decoded || !decoded.user) return null;

    if (decoded.server === "lid") {
      if (this.sock?.signalRepository?.lidMapping?.getPNForLID) {
        try {
          const pn = await this.sock.signalRepository.lidMapping.getPNForLID(normalized);
          if (pn) {
            const pnNorm = jidNormalizedUser(pn);
            const pnDec = jidDecode(pnNorm);
            if (pnDec?.user) {
              const digits = pnDec.user.replace(/[^0-9]/g, "");
              if (digits.length >= 7) {
                this.lidPhoneMap.set(normalized, pnNorm);
                this.lidPhoneMap.set(decoded.user + "@lid", pnNorm);
                return "+" + digits;
              }
            }
          }
        } catch (e) {}
      }
    }

    return null;
  }

  async getProfilePicUrl(jid) {
    if (!jid || !this.sock) return null;
    try {
      const norm = jidNormalizedUser(jid);
      const cached = this.avatarCache.get(norm);
      if (cached && (Date.now() - cached.ts) < 24 * 60 * 60 * 1000) {
        return cached.url;
      }
      const url = await this.sock.profilePictureUrl(norm, "preview").catch(() => null);
      this.avatarCache.set(norm, { url: url || null, ts: Date.now() });
      return url || null;
    } catch (e) {
      return null;
    }
  }

  getContactName(jid, fallbackPushName = "") {
    if (fallbackPushName && fallbackPushName.trim()) return fallbackPushName.trim();
    const normalized = jidNormalizedUser(jid);
    const contact = this.contactStore.get(normalized) || this.contactStore.get(jid);
    if (contact) {
      const name = contact.name || contact.notify || contact.verifiedName;
      if (name && name.trim()) return name.trim();
    }
    return null;
  }

  async syncBatchHistory(chats, messages, contacts) {
    if (Array.isArray(contacts)) {
      for (const c of contacts) {
        if (!c || !c.id) continue;
        const normalized = jidNormalizedUser(c.id);
        this.contactStore.set(normalized, { ...(this.contactStore.get(normalized) || {}), ...c });
        if (c.lid) {
          const normLid = jidNormalizedUser(c.lid);
          this.lidPhoneMap.set(normLid, normalized);
          this.lidPhoneMap.set(c.lid, normalized);
        }
        if (c.phoneNumber) {
          const phoneJid = jidNormalizedUser(c.phoneNumber.includes("@") ? c.phoneNumber : (c.phoneNumber + "@s.whatsapp.net"));
          this.lidPhoneMap.set(normalized, phoneJid);
        }
      }
    }

    if (Array.isArray(chats)) {
      for (const c of chats) {
        if (!c || !c.id) continue;
        const normalized = jidNormalizedUser(c.id);
        if (!normalized || isJidBroadcast(normalized) || isJidGroup(normalized) || isJidStatusBroadcast(normalized) || isJidNewsletter(normalized)) continue;
        const existing = this.chatStore.get(normalized) || {};
        this.chatStore.set(normalized, { ...existing, ...c, id: normalized });
      }
    }

    if (Array.isArray(messages)) {
      for (const m of messages) {
        if (!m || !m.key) continue;
        const rawJid = m.key.remoteJid;
        const altJid = m.key.remoteJidAlt;
        if (!rawJid) continue;

        if (altJid && rawJid.endsWith("@lid") && altJid.endsWith("@s.whatsapp.net")) {
          const normLid = jidNormalizedUser(rawJid);
          const normPhone = jidNormalizedUser(altJid);
          this.lidPhoneMap.set(normLid, normPhone);
        }

        const normalized = jidNormalizedUser(rawJid);
        if (!normalized || isJidBroadcast(normalized) || isJidGroup(normalized) || isJidStatusBroadcast(normalized) || isJidNewsletter(normalized)) continue;

        const body = getMessageText(m.message);
        const ts = Number(m.messageTimestamp) || Math.floor(Date.now() / 1000);
        const existing = this.chatStore.get(normalized) || { id: normalized };
        if (!existing.conversationTimestamp || ts >= (existing.conversationTimestamp || 0)) {
          existing.conversationTimestamp = ts;
          if (body) existing.lastMessageText = body;
        }
        if (m.pushName && !existing.pushName) {
          existing.pushName = m.pushName;
        }
        this.chatStore.set(normalized, existing);
      }
    }

    const batchPayload = [];
    const processedPhones = new Set();
    const ownDigits = this.connectedUser?.phone ? this.connectedUser.phone.replace(/[^0-9]/g, "") : "";

    for (const [jid, c] of this.chatStore.entries()) {
      const phone = this.getPhoneNumber(jid);
      if (!phone) continue;

      const pDigits = phone.replace(/[^0-9]/g, "");
      if (ownDigits && (pDigits === ownDigits || pDigits.endsWith(ownDigits) || ownDigits.endsWith(pDigits))) {
        continue;
      }
      processedPhones.add(phone);

      const contact = this.contactStore.get(jid) || {};
      const pushname = c.pushName || contact.notify || contact.verifiedName || "";
      const name = c.name || contact.name || "";
      const lastMsg = c.lastMessageText || "";
      let ts = c.conversationTimestamp;
      if (typeof ts === "object" && ts !== null) ts = ts.low || ts.seconds || 0;
      ts = Number(ts) || Math.floor(Date.now() / 1000);
      const avatar = c.avatar || (this.avatarCache.get(jid)?.url) || "";

      batchPayload.push({
        phone: phone,
        name: name,
        pushname: pushname,
        avatar: avatar,
        unread_count: Number(c.unreadCount) || 0,
        timestamp: ts,
        last_message: lastMsg,
      });
    }

    for (const [jid, ct] of this.contactStore.entries()) {
      const phone = this.getPhoneNumber(jid);
      if (!phone || processedPhones.has(phone)) continue;

      const pDigits = phone.replace(/[^0-9]/g, "");
      if (ownDigits && (pDigits === ownDigits || pDigits.endsWith(ownDigits) || ownDigits.endsWith(pDigits))) {
        continue;
      }
      processedPhones.add(phone);

      const pushname = ct.notify || ct.verifiedName || "";
      const name = ct.name || "";
      const avatar = (this.avatarCache.get(jid)?.url) || "";

      batchPayload.push({
        phone: phone,
        name: name,
        pushname: pushname,
        avatar: avatar,
        unread_count: 0,
        timestamp: Math.floor(Date.now() / 1000),
        last_message: "",
      });
    }

    if (batchPayload.length > 0) {
      console.log(`[WhatsApp Gateway:${this.sessionId}] Forwarding ${batchPayload.length} conversations to CRM Webhook in chunks...`);
      const CHUNK_SIZE = 150;
      for (let i = 0; i < batchPayload.length; i += CHUNK_SIZE) {
        const chunk = batchPayload.slice(i, i + CHUNK_SIZE);
        this.notify({
          event: "batch_sync",
          chats: chunk,
        });
      }
    }

    return batchPayload.length;
  }

  async startSocket() {
    try {
      const { version } = await fetchLatestBaileysVersion().catch(() => ({ version: [2, 3000, 1043857760] }));
      const { state, saveCreds } = await useMultiFileAuthState(this.authDir);

      this.sock = makeWASocket({
        version,
        auth: {
          creds: state.creds,
          keys: makeCacheableSignalKeyStore(state.keys, logger),
        },
        logger: logger,
        browser: ["Aleya.in", "Chrome", "1.0.0"],
        syncFullHistory: false,
        connectTimeoutMs: 30000,
        keepAliveIntervalMs: 30000,
        emitOwnEvents: true,
        defaultQueryTimeoutMs: 60000,
        markOnlineOnConnect: true,
        getMessage: async (key) => {
          return this.getMessage(key);
        },
      });

      this.sock.ev.on("creds.update", saveCreds);

      this.sock.ev.on("connection.update", async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
          this.currentQrString = qr;
          this.qrGeneratedAt = Date.now();
          try {
            this.currentQrSvg = await QRCode.toString(qr, {
              type: "svg",
              margin: 2,
              width: 280,
              color: { dark: "#000000", light: "#ffffff" },
            });
            const hasOtherConnected = Array.from(sessions.values()).some((sess) => sess.sessionId !== this.sessionId && sess.connectionStatus === "connected");
            if (!hasOtherConnected) {
              const termQr = await QRCode.toString(qr, { type: "terminal", small: true });
              console.log(`\n================== SCAN WHATSAPP QR [${this.sessionId}] ==================\n` + termQr + `\n========================================================================\n`);
            }
            this.connectionStatus = "qr_ready";
          } catch (err) {
            console.error(`[WhatsApp Gateway:${this.sessionId}] Failed to generate QR SVG:`, err);
          }
        }

        if (connection === "open") {
          this.connectionStatus = "connected";
          this.currentQrSvg = null;
          this.currentQrString = null;
          this.lastSyncTime = new Date().toISOString();

          const rawId = this.sock.user?.id || "";
          const phoneDigits = rawId.split(":")[0].replace(/[^0-9]/g, "");
          const formattedPhone = phoneDigits ? `+${phoneDigits}` : "Unknown";

          this.connectedUser = {
            phone: formattedPhone,
            name: this.sock.user?.name || "WhatsApp Business",
            device: "Aleya.in",
            connected_at: this.lastSyncTime,
          };

          console.log(`[WhatsApp Gateway:${this.sessionId}] Connected successfully as ${formattedPhone}`);

          this.notify({
            event: "device_connected",
            phone: formattedPhone,
            name: this.connectedUser.name,
            device: this.connectedUser.device,
          });

          if (!this.hasSyncedInitial && this.chatStore.size > 0) {
            this.hasSyncedInitial = true;
            setTimeout(() => {
              const cachedChats = Array.from(this.chatStore.values());
              this.syncBatchHistory(cachedChats, [], Array.from(this.contactStore.values()));
            }, 3000);
          }
        }

        if (connection === "close") {
          const statusCode = lastDisconnect?.error?.output?.statusCode;
          const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
          const err = lastDisconnect?.error;
          const errMsg = err?.message || err?.output?.payload?.message || "";

          this.currentQrSvg = null;
          this.currentQrString = null;

          console.log(`[WhatsApp Gateway:${this.sessionId}] Connection closed. StatusCode: ${statusCode}. Reason: ${errMsg}. Reconnect: ${shouldReconnect}`);

          if (statusCode === DisconnectReason.loggedOut) {
            this.connectionStatus = "disconnected";
            this.connectedUser = null;
            try {
              fs.rmSync(this.authDir, { recursive: true, force: true });
            } catch (e) {}
            setTimeout(() => this.startSocket(), 1500);
          } else if (shouldReconnect) {
            // Keep connectedUser and mark reconnecting so momentary Baileys cycling does not wipe device status
            this.connectionStatus = "reconnecting";
            const delay = statusCode === 408 ? 1000 : 2500;
            setTimeout(() => this.startSocket(), delay);
          } else {
            this.connectionStatus = "disconnected";
            this.connectedUser = null;
          }
        }
      });

      this.sock.ev.on("contacts.upsert", (contacts) => {
        for (const c of contacts) {
          if (!c || !c.id) continue;
          const norm = jidNormalizedUser(c.id);
          this.contactStore.set(norm, { ...(this.contactStore.get(norm) || {}), ...c });
          if (c.lid) this.lidPhoneMap.set(c.lid, norm);
        }
      });

      this.sock.ev.on("contacts.update", (contacts) => {
        for (const c of contacts) {
          if (!c || !c.id) continue;
          const norm = jidNormalizedUser(c.id);
          this.contactStore.set(norm, { ...(this.contactStore.get(norm) || {}), ...c });
          if (c.lid) this.lidPhoneMap.set(c.lid, norm);
        }
      });

      this.sock.ev.on("messaging-history.set", async ({ chats, contacts, messages }) => {
        console.log(`[WhatsApp Gateway:${this.sessionId}] History received: ${chats?.length || 0} chats, ${contacts?.length || 0} contacts`);
        await this.syncBatchHistory(chats || [], messages || [], contacts || []);
      });

      this.sock.ev.on("chats.upsert", async (chats) => {
        await this.syncBatchHistory(chats, [], []);
      });

      this.sock.ev.on("chats.update", async (chats) => {
        await this.syncBatchHistory(chats, [], []);
      });

      this.sock.ev.on("messages.upsert", async (m) => {
        if (!m.messages || !m.messages.length) return;

        for (const msg of m.messages) {
          const rawJid = msg.key?.remoteJid;
          const altJid = msg.key?.remoteJidAlt;
          if (!rawJid) continue;

          // Cache incoming message for encryption retry support
          if (msg.key?.id && msg.message) {
            this.messageStore.set(msg.key.id, msg.message);
            this.messageStore.set(`${rawJid}:${msg.key.id}`, msg.message);
            if (this.messageStore.size > 2500) {
              const firstK = this.messageStore.keys().next().value;
              this.messageStore.delete(firstK);
            }
          }

          if (altJid && rawJid.endsWith("@lid") && altJid.endsWith("@s.whatsapp.net")) {
            this.lidPhoneMap.set(rawJid, altJid);
          }

          const jid = jidNormalizedUser(rawJid);
          if (!jid || isJidBroadcast(jid) || isJidGroup(jid) || isJidStatusBroadcast(jid) || isJidNewsletter(jid)) continue;

          const contactPhone = await this.resolvePhoneNumber(jid, altJid);
          if (!contactPhone) continue;

          // Exclude messages to/from connected device's own number
          if (this.connectedUser?.phone) {
            const cleanUserPhone = this.connectedUser.phone.replace(/[^0-9]/g, "");
            const cleanContact = contactPhone.replace(/[^0-9]/g, "");
            if (cleanUserPhone && (cleanContact === cleanUserPhone || cleanContact.endsWith(cleanUserPhone) || cleanUserPhone.endsWith(cleanContact))) {
              continue;
            }
          }

          const body = getMessageText(msg.message);
          if (!body) continue;

          // Fetch profile picture avatar
          let avatarUrl = null;
          try {
            avatarUrl = await this.getProfilePicUrl(jid);
            if (!avatarUrl && contactPhone) {
              const digits = contactPhone.replace(/[^0-9]/g, "");
              avatarUrl = await this.getProfilePicUrl(`${digits}@s.whatsapp.net`);
            }
          } catch (e) {}

          const pushName = msg.pushName || this.getContactName(jid) || "";
          const isFromMe = !!msg.key.fromMe;
          const ts = Number(msg.messageTimestamp) || Math.floor(Date.now() / 1000);

          const existing = this.chatStore.get(jid) || { id: jid };
          existing.conversationTimestamp = ts;
          existing.lastMessageText = body;
          if (avatarUrl && !existing.avatar) existing.avatar = avatarUrl;
          if (pushName && !existing.pushName) existing.pushName = pushName;
          if (!isFromMe) {
            existing.unreadCount = (existing.unreadCount || 0) + 1;
          }
          this.chatStore.set(jid, existing);

          console.log(`[WhatsApp Gateway:${this.sessionId}] Message (${isFromMe ? "OUT" : "IN"}) ${contactPhone}: ${body.substring(0, 40)}`);

          this.notify({
            event: "message",
            direction: isFromMe ? "outgoing" : "incoming",
            phone: contactPhone,
            from: isFromMe ? (this.connectedUser?.phone || "") : contactPhone,
            to: isFromMe ? contactPhone : (this.connectedUser?.phone || ""),
            name: isFromMe ? (this.connectedUser?.name || "") : "",
            pushname: pushName,
            avatar: avatarUrl || "",
            text: body,
            msg_id: msg.key.id,
            timestamp: ts,
          });
        }
      });
    } catch (err) {
      console.error(`[WhatsApp Gateway:${this.sessionId}] Error initializing socket:`, err);
    }
  }

  async logout() {
    try {
      if (this.sock) {
        await this.sock.logout();
      }
    } catch (e) {}

    try {
      fs.rmSync(this.authDir, { recursive: true, force: true });
    } catch (e) {}

    this.chatStore.clear();
    this.contactStore.clear();
    this.lidPhoneMap.clear();

    this.connectionStatus = "disconnected";
    this.connectedUser = null;
    this.currentQrSvg = null;
    this.currentQrString = null;

    setTimeout(() => this.startSocket(), 1500);
  }
}

// -----------------------------------------------------------------------------
// Session Manager
// -----------------------------------------------------------------------------
const sessions = new Map();
let lastKnownWebhookUrl = null;

function enrichSession(req, session) {
  if (!session || !req) return;
  const webhookUrl = req.query?.webhook_url || req.body?.webhook_url;
  const siteId = req.query?.site_id || req.body?.site_id;
  if (webhookUrl && typeof webhookUrl === "string" && webhookUrl.startsWith("http")) {
    lastKnownWebhookUrl = webhookUrl;
    if (session.webhookUrl !== webhookUrl) {
      console.log(`[WhatsApp Gateway:${session.sessionId}] Dynamic webhook registered: ${webhookUrl}`);
      session.webhookUrl = webhookUrl;
    }
  }
  if (siteId) {
    session.siteId = siteId;
  }
}

function getSession(sessionId = "default", req = null) {
  const cleanId = String(sessionId || "default").trim().replace(/[^a-zA-Z0-9_\-]/g, "");
  if (!sessions.has(cleanId)) {
    const s = new WhatsAppSession(cleanId);
    sessions.set(cleanId, s);
    s.startSocket();
  }
  const s = sessions.get(cleanId);
  if (req) {
    enrichSession(req, s);
  }
  return s;
}

// Initialize any pre-existing connected sessions on startup
const defaultHasCreds = fs.existsSync(path.join(AUTH_DIR, "creds.json"));
if (defaultHasCreds) {
  getSession("default");
}

try {
  const dirs = fs.readdirSync(AUTH_DIR, { withFileTypes: true });
  for (const d of dirs) {
    if (d.isDirectory() && d.name !== "default") {
      const hasCreds = fs.existsSync(path.join(AUTH_DIR, d.name, "creds.json"));
      if (hasCreds) {
        console.log(`[WhatsApp Gateway] Auto-loading discovered session: ${d.name}`);
        getSession(d.name);
      }
    }
  }
} catch (e) {}

function notifyCrm(payload, targetOverrideUrl = null, redirectCount = 0) {
  if (redirectCount > 3) {
    console.error("[WhatsApp Gateway] CRM Webhook exceeded max redirects (3).");
    return;
  }
  try {
    let targetUrl = targetOverrideUrl || lastKnownWebhookUrl || process.env.CRM_WEBHOOK_URL || CRM_WEBHOOK_URL;
    if (!targetUrl) {
      console.warn("[WhatsApp Gateway] No CRM_WEBHOOK_URL configured for webhook notification.");
      return;
    }

    // Always canonicalize /webhook.php to /webhook/ to prevent Apache 301/302 redirects
    targetUrl = targetUrl.replace(/\/webhook\.php(\?|$)/, "/webhook/$1");

    const data = JSON.stringify(payload);
    const url = new URL(targetUrl);
    const client = url.protocol === "https:" ? https : http;
    const defaultPort = url.protocol === "https:" ? 443 : 80;

    const req = client.request(
      {
        hostname: url.hostname,
        port: url.port || defaultPort,
        path: url.pathname + url.search,
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Content-Length": Buffer.byteLength(data),
          "Host": url.host,
          "User-Agent": "WhatsApp-Gateway/1.0",
        },
        timeout: 10000,
        rejectUnauthorized: false,
      },
      (res) => {
        // Follow redirect if 301, 302, 307, 308
        if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
          const redirectUrl = new URL(res.headers.location, targetUrl).toString();
          console.log(`[WhatsApp Gateway] Webhook redirected (${res.statusCode}) to ${redirectUrl}`);
          return notifyCrm(payload, redirectUrl, redirectCount + 1);
        }

        let resBody = "";
        res.on("data", (chunk) => (resBody += chunk));
        res.on("end", () => {
          if (res.statusCode >= 400) {
            console.error(`[WhatsApp Gateway] CRM Webhook responded HTTP ${res.statusCode} (${targetUrl}):`, resBody);
          } else {
            console.log(`[WhatsApp Gateway] CRM Webhook delivered successfully (${res.statusCode}): ${payload.event || 'message'}`);
          }
        });
      }
    );

    req.on("error", (e) => {
      console.error(`[WhatsApp Gateway] Error connecting to CRM webhook (${targetUrl}):`, e.message);
    });
    req.write(data);
    req.end();
  } catch (e) {
    console.error("[WhatsApp Gateway] Error building CRM webhook request:", e.message);
  }
}

// -----------------------------------------------------------------------------
// REST API Endpoints for CRM Integration
// -----------------------------------------------------------------------------

// 0. List all active WhatsApp device sessions
app.get("/devices", (req, res) => {
  try {
    const list = [];
    for (const [id, s] of sessions.entries()) {
      enrichSession(req, s);
      const hasCreds = fs.existsSync(path.join(s.authDir, "creds.json"));
      const isConn = s.connectionStatus === "connected" || (hasCreds && s.connectionStatus === "reconnecting");
      list.push({
        session_id: id,
        connection_status: s.connectionStatus,
        is_connected: isConn,
        is_linked: hasCreds || isConn,
        has_qr: s.connectionStatus === "qr_ready" && !!s.currentQrSvg,
        user: s.connectedUser,
        last_sync: s.lastSyncTime,
        total_chats: s.chatStore.size,
        total_contacts: s.contactStore.size,
      });
    }
    res.json({
      status: "success",
      devices: list,
    });
  } catch (err) {
    console.error("[WhatsApp Gateway] /devices error:", err);
    res.status(500).json({ status: "error", message: err.message });
  }
});

// 1. Status & QR Info for specific session
app.get("/status", (req, res) => {
  try {
    const s = getSession(req.query.session_id, req);
    const hasCreds = fs.existsSync(path.join(s.authDir, "creds.json"));
    const isConn = s.connectionStatus === "connected" || (hasCreds && s.connectionStatus === "reconnecting");
    res.json({
      status: "success",
      session_id: s.sessionId,
      connection_status: s.connectionStatus,
      is_connected: isConn,
      is_linked: hasCreds || isConn,
      has_qr: s.connectionStatus === "qr_ready" && !!s.currentQrSvg,
      user: s.connectedUser,
      last_sync: s.lastSyncTime,
    });
  } catch (err) {
    console.error("[WhatsApp Gateway] /status error:", err);
    res.status(500).json({ status: "error", message: err.message });
  }
});

// 2. Fetch live QR Code for specific session
app.get("/qr", async (req, res) => {
  try {
    const s = getSession(req.query.session_id, req);

    if (s.connectionStatus === "connected") {
      return res.json({
        status: "connected",
        session_id: s.sessionId,
        message: "Device is already connected.",
        user: s.connectedUser,
      });
    }

    const force = req.query.force === "1" || req.query.force === "true";
    let isStale = (Date.now() - s.qrGeneratedAt) > 28000;

    if (force && s.connectionStatus !== "connected") {
      try {
        if (s.sock) s.sock.end();
      } catch (e) {}
      s.currentQrSvg = null;
      s.currentQrString = null;
      s.startSocket();
    }

    // If QR is missing or stale, wait up to 4.5s for the live socket to generate it
    if (!s.currentQrSvg || isStale) {
      let waited = 0;
      while (!s.currentQrSvg && s.connectionStatus !== "connected" && waited < 4500) {
        await new Promise((r) => setTimeout(r, 300));
        waited += 300;
      }
      isStale = (Date.now() - s.qrGeneratedAt) > 28000;
    }

    if (s.connectionStatus === "connected") {
      return res.json({
        status: "connected",
        session_id: s.sessionId,
        message: "Device is already connected.",
        user: s.connectedUser,
      });
    }

    if (s.currentQrSvg && !isStale) {
      const elapsed = Math.floor((Date.now() - s.qrGeneratedAt) / 1000);
      const remaining = Math.max(5, 30 - elapsed);
      return res.json({
        status: "qr_ready",
        session_id: s.sessionId,
        qr_svg: s.currentQrSvg,
        qr_string: s.currentQrString,
        expires_in: remaining,
      });
    }

    res.json({
      status: "initializing",
      session_id: s.sessionId,
      message: "Generating QR Code, please wait...",
    });
  } catch (err) {
    console.error("[WhatsApp Gateway] /qr error:", err);
    res.status(500).json({ status: "error", message: err.message });
  }
});

// 2b. Request Crockford 8-digit Pairing Code for specific session
app.post("/pairing-code", async (req, res) => {
  const s = getSession(req.body?.session_id || req.query?.session_id, req);

  if (s.connectionStatus === "connected") {
    return res.json({
      status: "connected",
      session_id: s.sessionId,
      message: "Device already connected.",
      user: s.connectedUser,
    });
  }

  const { phone } = req.body;
  if (!phone) {
    return res.status(400).json({ status: "error", message: "Phone number is required." });
  }

  let cleanPhone = phone.replace(/[^0-9]/g, "");
  if (cleanPhone.startsWith("0")) cleanPhone = cleanPhone.replace(/^0+/, "");
  if (cleanPhone.length === 10) cleanPhone = "91" + cleanPhone;
  if (cleanPhone.length < 8) {
    return res.status(400).json({ status: "error", message: "Invalid phone number." });
  }

  try {
    if (!s.sock || s.connectionStatus === "disconnected") {
      await s.startSocket();
    }

    // Wait until socket has established initial handshake and is ready for pairing
    let waited = 0;
    while (!s.currentQrString && s.connectionStatus !== "connected" && waited < 8000) {
      await new Promise((r) => setTimeout(r, 400));
      waited += 400;
    }

    if (!s.sock) {
      throw new Error("WhatsApp socket could not be initialized.");
    }

    const code = await s.sock.requestPairingCode(cleanPhone);
    console.log(`[WhatsApp Gateway:${s.sessionId}] Pairing code for ${cleanPhone}: ${code}`);
    res.json({
      status: "success",
      session_id: s.sessionId,
      pairing_code: code,
      phone: cleanPhone,
    });
  } catch (err) {
    console.error(`[WhatsApp Gateway:${s.sessionId}] Failed to generate pairing code:`, err);
    res.status(500).json({
      status: "error",
      message: err.message || "Failed to generate pairing code from WhatsApp.",
    });
  }
});

// 3. Send Outbound Message from CRM
app.post("/send-message", async (req, res) => {
  let reqSid = req.body?.session_id || req.query?.session_id;
  let s = reqSid ? getSession(reqSid, req) : null;
  if (!s || s.connectionStatus !== "connected") {
    for (const sess of sessions.values()) {
      if (sess.connectionStatus === "connected") {
        s = sess;
        break;
      }
    }
  }
  if (!s) {
    s = getSession("default", req);
  }

  if (s.connectionStatus !== "connected" || !s.sock) {
    return res.status(400).json({
      status: "error",
      message: `WhatsApp device (${s.sessionId}) is not connected. Please scan QR first.`,
    });
  }

  let { to, message } = req.body;
  if (!to || !message) {
    return res.status(400).json({ status: "error", message: "Missing recipient (to) or message" });
  }

  try {
    let cleanNumber = String(to).replace(/[^0-9]/g, "");
    if (cleanNumber.startsWith("0")) {
      cleanNumber = cleanNumber.replace(/^0+/, "");
    }
    if (cleanNumber.length === 10) {
      cleanNumber = "91" + cleanNumber;
    }
    if (cleanNumber.length < 8) {
      return res.status(400).json({ status: "error", message: "Invalid recipient phone number." });
    }

    let targetJid = `${cleanNumber}@s.whatsapp.net`;
    try {
      if (typeof s.sock.onWhatsApp === "function") {
        const results = await s.sock.onWhatsApp(cleanNumber);
        if (results && results.length > 0 && results[0].exists && results[0].jid) {
          if (!results[0].jid.endsWith("@lid")) {
            targetJid = results[0].jid;
          }
        }
      }
    } catch (e) {}

    // Proactively pre-populate LID mapping for this recipient so immediate replies link cleanly
    try {
      if (s.sock?.signalRepository?.lidMapping?.getLIDForPN) {
        const normTarget = jidNormalizedUser(targetJid);
        const mappedLid = await s.sock.signalRepository.lidMapping.getLIDForPN(normTarget);
        if (mappedLid) {
          const normLid = jidNormalizedUser(mappedLid);
          s.lidPhoneMap.set(normLid, normTarget);
          s.lidPhoneMap.set(mappedLid, normTarget);
        }
      }
    } catch (e) {}

    // Subscribe presence to establish connection and trigger key exchange before sending
    try {
      await s.sock.presenceSubscribe(targetJid);
      await s.sock.sendPresenceUpdate("composing", targetJid);
    } catch (e) {}

    const sent = await s.sock.sendMessage(targetJid, { text: message });

    // Store in messageStore under all key formats to resolve encryption retry requests from recipient
    if (sent?.key?.id && sent.message) {
      const msgId = sent.key.id;
      const normJid = jidNormalizedUser(targetJid);
      s.messageStore.set(msgId, sent.message);
      s.messageStore.set(`${targetJid}:${msgId}`, sent.message);
      s.messageStore.set(`${normJid}:${msgId}`, sent.message);
      if (s.messageStore.size > 3000) {
        const firstK = s.messageStore.keys().next().value;
        s.messageStore.delete(firstK);
      }
    }

    // Update local chatStore
    const norm = jidNormalizedUser(targetJid);
    const existing = s.chatStore.get(norm) || { id: norm };
    existing.conversationTimestamp = Math.floor(Date.now() / 1000);
    existing.lastMessageText = message;
    s.chatStore.set(norm, existing);

    res.json({
      status: "success",
      session_id: s.sessionId,
      msg_id: sent?.key?.id,
      to: "+" + cleanNumber,
    });
  } catch (err) {
    console.error(`[WhatsApp Gateway:${s.sessionId}] Failed to send message:`, err);
    res.status(500).json({ status: "error", message: err.message || "Failed to send message via WhatsApp." });
  }
});

// 3b. Manual Sync Chats from WhatsApp to CRM
app.all("/sync-chats", async (req, res) => {
  let reqSid = req.body?.session_id || req.query?.session_id;
  let s = reqSid ? getSession(reqSid, req) : null;
  if (!s || s.connectionStatus !== "connected") {
    // Pick the first connected session
    for (const sess of sessions.values()) {
      if (sess.connectionStatus === "connected") {
        s = sess;
        break;
      }
    }
  }
  if (!s) {
    s = getSession("default", req);
  }

  if (s.connectionStatus !== "connected" || !s.sock) {
    return res.status(400).json({ status: "error", message: "WhatsApp is not connected. Scan QR first." });
  }

  try {
    if (typeof s.sock.resyncAppState === "function" && Array.isArray(ALL_WA_PATCH_NAMES)) {
      console.log(`[WhatsApp Gateway:${s.sessionId}] Triggering resyncAppState...`);
      await s.sock.resyncAppState(ALL_WA_PATCH_NAMES, false).catch((e) => console.log("resyncAppState notice:", e.message));
    }
  } catch (e) {
    console.log(`[WhatsApp Gateway:${s.sessionId}] resyncAppState notice:`, e.message);
  }

  await new Promise((resolve) => setTimeout(resolve, 1500));

  const allChats = Array.from(s.chatStore.values());
  const allContacts = Array.from(s.contactStore.values());
  console.log(`[WhatsApp Gateway:${s.sessionId}] Syncing ${allChats.length} chats, ${allContacts.length} contacts.`);
  const synced = await s.syncBatchHistory(allChats, [], allContacts);

  res.json({
    status: "success",
    session_id: s.sessionId,
    synced_chats: synced,
    total_in_store: allChats.length,
    contacts_in_store: allContacts.length,
    user: s.connectedUser,
    message: `Synchronized ${synced} conversations to CRM.`
  });
});

// 4. Disconnect / Logout specific session
app.all("/logout", async (req, res) => {
  const s = getSession(req.body?.session_id || req.query?.session_id, req);
  await s.logout();

  res.json({
    status: "success",
    session_id: s.sessionId,
    message: "Logged out successfully. Generating new QR...",
  });
});

// Start Express and WhatsApp Gateway
app.listen(PORT, () => {
  console.log(`[WhatsApp Gateway] Multi-Device Gateway Server running on http://127.0.0.1:${PORT}`);
});
