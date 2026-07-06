<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No-Code Builder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f6fb;
            --surface: #ffffff;
            --surface-soft: #f8fafd;
            --panel: #fcfdff;
            --stroke: #dbe5f3;
            --stroke-strong: #c5d4ea;
            --text: #0f172a;
            --text-soft: #5e6a7d;
            --accent: #2d6cf6;
            --accent-soft: #eaf1ff;
            --danger: #e5484d;
            --shadow-1: 0 8px 30px rgba(13, 33, 74, 0.08);
            --shadow-2: 0 18px 40px rgba(13, 33, 74, 0.14);
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 10px;
            --topbar-h: 68px;
            --leftbar-w: 86px;
            --rightbar-w: 320px;
            --canvas-max: 1200px;
            --transition: 220ms cubic-bezier(0.2, 0.7, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: radial-gradient(1200px 700px at 90% -10%, #dbe9ff 0%, rgba(219, 233, 255, 0) 60%),
                radial-gradient(1000px 600px at -10% 100%, #e8f0ff 0%, rgba(232, 240, 255, 0) 65%),
                var(--bg);
            color: var(--text);
            font-family: "Plus Jakarta Sans", "Segoe UI", sans-serif;
        }

        button,
        input,
        select {
            font: inherit;
            color: inherit;
        }

        .builder {
            height: 100%;
            display: grid;
            grid-template-rows: var(--topbar-h) 1fr;
            grid-template-columns: 100%;
        }

        .topbar {
            border-bottom: 1px solid rgba(190, 206, 232, 0.7);
            backdrop-filter: blur(12px);
            background: rgba(252, 254, 255, 0.84);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: relative;
            z-index: 40;
        }

        .topbar-left,
        .topbar-center,
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #1160ff, #5a95ff);
            display: grid;
            place-items: center;
            color: #fff;
            font-family: Manrope, sans-serif;
            font-weight: 800;
            letter-spacing: 0.02em;
            box-shadow: 0 10px 24px rgba(17, 96, 255, 0.35);
        }

        .brand {
            font-family: Manrope, sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            margin-right: 8px;
        }

        .control,
        .icon-btn,
        .chip,
        .action-btn {
            border: 1px solid var(--stroke);
            background: var(--surface);
            border-radius: 10px;
            min-height: 38px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .control:hover,
        .icon-btn:hover,
        .chip:hover,
        .action-btn:hover {
            transform: translateY(-1px);
            border-color: var(--stroke-strong);
            box-shadow: 0 10px 18px rgba(31, 51, 84, 0.08);
        }

        .icon-btn {
            width: 38px;
            justify-content: center;
            padding: 0;
            font-size: 14px;
            color: #334155;
        }

        .chip {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            background: var(--surface-soft);
            min-height: 34px;
        }

        .chip.active {
            color: var(--accent);
            border-color: #b8cdfb;
            background: var(--accent-soft);
        }

        .action-btn {
            min-height: 40px;
            font-weight: 600;
            font-size: 13px;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #2d6cf6, #4f86ff);
            border: none;
            color: white;
            box-shadow: 0 8px 20px rgba(45, 108, 246, 0.34);
        }

        .action-btn::after,
        .icon-btn::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.55);
            transform: scale(0);
            opacity: 0;
            transition: transform 0.4s ease, opacity 0.3s ease;
            pointer-events: none;
        }

        .action-btn:active::after,
        .icon-btn:active::after {
            transform: scale(8);
            opacity: 1;
            transition: 0s;
        }

        .workspace {
            display: grid;
            grid-template-columns: var(--leftbar-w) 1fr var(--rightbar-w);
            height: calc(100vh - var(--topbar-h));
            overflow: hidden;
        }

        .leftbar {
            border-right: 1px solid rgba(200, 214, 238, 0.75);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(248, 251, 255, 0.86));
            backdrop-filter: blur(8px);
            padding: 16px 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: stretch;
        }

        .left-item {
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 12px;
            padding: 10px 8px;
            display: grid;
            place-items: center;
            gap: 4px;
            cursor: pointer;
            color: #5b6578;
            transition: var(--transition);
            font-size: 11px;
            font-weight: 600;
            text-align: center;
        }

        .left-context-panel {
            position: fixed;
            top: calc(var(--topbar-h) + 12px);
            left: calc(var(--leftbar-w) + 12px);
            bottom: 12px;
            width: min(310px, calc(100vw - 90px));
            border: 1px solid #d5e1f4;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            box-shadow: 0 20px 40px rgba(15, 31, 62, 0.14);
            z-index: 52;
            padding: 14px;
            overflow: auto;
            opacity: 0;
            transform: translateX(-8px);
            pointer-events: none;
            transition: var(--transition);
        }

        .left-context-panel.open {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        .left-context-title {
            font-family: Manrope, sans-serif;
            font-size: 18px;
            margin-bottom: 2px;
        }

        .left-context-sub {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .left-list {
            display: grid;
            gap: 8px;
            margin-bottom: 12px;
        }

        .left-list-item {
            border: 1px solid #d7e3f5;
            border-radius: 10px;
            background: #fff;
            min-height: 38px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 13px;
            color: #334155;
        }

        .left-list-item button {
            border: 1px solid #c8d9f4;
            background: #f4f8ff;
            border-radius: 8px;
            min-height: 28px;
            padding: 0 9px;
            font-size: 12px;
            cursor: pointer;
            color: #245dca;
        }

        .left-panel-input {
            width: 100%;
            border: 1px solid #cfdbef;
            border-radius: 10px;
            min-height: 36px;
            padding: 0 10px;
            margin-bottom: 10px;
        }

        .left-panel-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .left-panel-btn {
            border: 1px solid #cad9f1;
            background: #ffffff;
            border-radius: 10px;
            min-height: 34px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 600;
            color: #245dca;
            cursor: pointer;
        }

        .left-item:hover,
        .left-item.active {
            background: #fff;
            border-color: #d2dff2;
            color: #1f2a3d;
            box-shadow: 0 10px 18px rgba(21, 42, 79, 0.08);
        }

        .left-item .icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: #edf3ff;
            color: #3369e7;
            display: grid;
            place-items: center;
            font-size: 13px;
        }

        .canvas-wrap {
            position: relative;
            overflow: auto;
            padding: 24px;
        }

        .canvas-surface {
            width: min(100%, var(--canvas-max));
            min-height: calc(100% - 12px);
            margin: 0 auto;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(203, 219, 243, 0.8);
            box-shadow: var(--shadow-1);
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .canvas-grid {
            position: absolute;
            inset: 0;
            opacity: 0.35;
            background-image: linear-gradient(rgba(207, 220, 241, 0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(207, 220, 241, 0.3) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }

        .page {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            gap: 18px;
            min-height: 78vh;
        }

        .empty-state {
            margin: auto;
            width: min(560px, 100%);
            border: 1px dashed #b7caea;
            border-radius: 20px;
            background: linear-gradient(180deg, #fff, #f8fbff);
            text-align: center;
            padding: 38px 20px;
            box-shadow: inset 0 1px 0 #fff;
        }

        .empty-illustration {
            width: 190px;
            height: 120px;
            margin: 0 auto 18px;
            border-radius: 16px;
            background: linear-gradient(145deg, #dce8ff, #edf3ff 60%, #fff);
            position: relative;
            overflow: hidden;
        }

        .empty-illustration::before,
        .empty-illustration::after {
            content: "";
            position: absolute;
            border-radius: 10px;
            background: white;
            border: 1px solid #d7e3f8;
            box-shadow: 0 8px 14px rgba(31, 52, 93, 0.09);
        }

        .empty-illustration::before {
            width: 132px;
            height: 70px;
            left: 28px;
            top: 22px;
        }

        .empty-illustration::after {
            width: 88px;
            height: 42px;
            right: 16px;
            bottom: 14px;
        }

        .empty-state h2 {
            font-family: Manrope, sans-serif;
            font-size: 30px;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .empty-state p {
            color: var(--text-soft);
            margin-bottom: 22px;
        }

        .section {
            position: relative;
            border: 1px solid #dce7f8;
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff, #fbfdff);
            padding: 16px;
            transition: var(--transition);
            animation: popIn 260ms ease;
        }

        .section:hover {
            border-color: #bdd0ef;
            box-shadow: 0 12px 24px rgba(22, 44, 79, 0.08);
        }

        .columns {
            display: flex;
            gap: 14px;
            min-height: 80px;
        }

        .column {
            position: relative;
            border: 1px dashed #c7d8f3;
            border-radius: 12px;
            background: #fbfdff;
            min-height: 92px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: var(--transition);
        }

        .column:hover {
            border-color: #a8c1eb;
            background: #f6faff;
        }

        .add-widget-btn {
            margin: auto;
            border: 1px dashed #99b6e7;
            background: #eff5ff;
            color: #295ec9;
            border-radius: 11px;
            padding: 9px 14px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .add-widget-btn:hover {
            background: #e4eeff;
            transform: scale(1.02);
        }

        .widget {
            border: 1px solid #d9e4f7;
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            position: relative;
            animation: widgetIn 220ms ease;
        }

        .widget .editable {
            outline: none;
            min-height: 20px;
        }

        .widget h1.editable {
            font-family: Manrope, sans-serif;
            font-size: 30px;
            line-height: 1.18;
            letter-spacing: -0.02em;
        }

        .widget p.editable {
            color: #475569;
            line-height: 1.65;
        }

        .widget button.editable {
            border: none;
            background: #2d6cf6;
            color: #fff;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            cursor: text;
        }

        .image-widget {
            border: 1px dashed #c8d9f3;
            border-radius: 12px;
            background: #f5f9ff;
            min-height: 180px;
            overflow: hidden;
            position: relative;
        }

        .image-widget img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            aspect-ratio: 16 / 9;
        }

        .image-widget .image-placeholder {
            min-height: 180px;
            display: grid;
            place-items: center;
            color: #64748b;
            font-weight: 600;
        }

        .image-widget-controls {
            position: absolute;
            left: 10px;
            bottom: 10px;
            display: flex;
            gap: 8px;
        }

        .image-upload-chip {
            border: 1px solid #bdd0ef;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 10px;
            min-height: 30px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 600;
            color: #295ec9;
            cursor: pointer;
        }

        .node-toolbar {
            position: absolute;
            top: -15px;
            right: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px;
            border-radius: 10px;
            border: 1px solid #cfddf4;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 10px 16px rgba(18, 40, 78, 0.12);
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
            z-index: 9;
        }

        .section:hover>.node-toolbar,
        .column:hover>.node-toolbar,
        .widget:hover>.node-toolbar,
        .selected>.node-toolbar {
            opacity: 1;
            pointer-events: auto;
        }

        .node-toolbar button {
            border: none;
            background: transparent;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            color: #4a5b78;
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .node-toolbar button:hover {
            background: #eaf2ff;
            color: #194fb9;
        }

        .selected {
            box-shadow: 0 0 0 2px rgba(45, 108, 246, 0.66), 0 8px 18px rgba(45, 108, 246, 0.15) !important;
        }

        .selected::after {
            content: "";
            position: absolute;
            inset: -1px;
            border: 2px solid rgba(45, 108, 246, 0.5);
            border-radius: inherit;
            animation: pulse 1.7s infinite;
            pointer-events: none;
        }

        .sidebar-right {
            border-left: 1px solid rgba(200, 214, 238, 0.75);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(248, 251, 255, 0.86));
            backdrop-filter: blur(8px);
            overflow: auto;
            padding: 20px 16px;
        }

        .panel-title {
            font-family: Manrope, sans-serif;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .panel-subtitle {
            color: var(--text-soft);
            font-size: 13px;
            margin-bottom: 16px;
        }

        .prop-group {
            border: 1px solid #dce7f8;
            border-radius: 12px;
            padding: 10px;
            background: #fff;
            margin-bottom: 10px;
            animation: fadeSlide 200ms ease;
        }

        .prop-group h4 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #64748b;
            margin-bottom: 8px;
        }

        .field {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            margin-bottom: 9px;
        }

        .field label {
            font-size: 12px;
            color: #475569;
            font-weight: 600;
        }

        .field input,
        .field select {
            border: 1px solid #cfdbef;
            border-radius: 10px;
            min-height: 34px;
            padding: 0 10px;
            background: #fdfefe;
        }

        .floating-panel {
            position: fixed;
            z-index: 70;
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid #cfddf4;
            box-shadow: var(--shadow-2);
            border-radius: 14px;
            padding: 12px;
            width: min(680px, calc(100vw - 24px));
            opacity: 0;
            pointer-events: none;
            transform: translateY(8px);
            transition: var(--transition);
            max-height: min(62vh, 540px);
            overflow: auto;
        }

        .floating-panel.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .floating-panel h3 {
            font-family: Manrope, sans-serif;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
        }

        .layout-card {
            border: 1px solid #d7e3f7;
            border-radius: 11px;
            background: #fff;
            padding: 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .layout-card:hover {
            transform: translateY(-2px);
            border-color: #99b8ec;
            box-shadow: 0 10px 20px rgba(24, 44, 80, 0.1);
        }

        .layout-visual {
            height: 50px;
            border-radius: 8px;
            background: #f2f7ff;
            border: 1px dashed #b9ceec;
            padding: 6px;
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
        }

        .layout-col {
            background: #d7e7ff;
            border-radius: 4px;
            border: 1px solid #b5cdf3;
        }

        .layout-label {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
        }

        .widget-overlay {
            position: fixed;
            inset: 0;
            z-index: 95;
            background: rgba(10, 18, 32, 0.22);
            backdrop-filter: blur(7px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 180ms ease;
        }

        .widget-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .widget-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            z-index: 100;
            width: min(960px, calc(100vw - 40px));
            max-height: 75vh;
            overflow: hidden;
            border-radius: 20px;
            border: 1px solid #dce4f2;
            background: #ffffff;
            box-shadow: 0 26px 70px rgba(15, 27, 49, 0.18);
            transform: translate(-50%, -50%) scale(0.96);
            opacity: 0;
            pointer-events: none;
            transition: opacity 200ms ease, transform 200ms cubic-bezier(0.2, 0.7, 0.2, 1);
            font-family: "Inter", "Plus Jakarta Sans", sans-serif;
            display: flex;
            flex-direction: column;
        }

        .widget-modal.open {
            opacity: 1;
            pointer-events: auto;
            transform: translate(-50%, -50%) scale(1);
        }

        .widget-modal-header {
            padding: 20px 22px 14px;
        }

        .widget-modal-title {
            font-size: 28px;
            line-height: 1.2;
            font-weight: 650;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .widget-modal-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .widget-search-wrap {
            position: relative;
        }

        .widget-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #7c8ba1;
            pointer-events: none;
        }

        .widget-search {
            width: 100%;
            min-height: 50px;
            border: 1px solid #d7e2f1;
            border-radius: 14px;
            padding: 0 58px 0 36px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease;
            background: #fbfdff;
        }

        .widget-search:focus {
            border-color: #9cbaf6;
            box-shadow: 0 0 0 3px rgba(45, 108, 246, 0.1);
        }

        .widget-shortcut {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: 1px solid #d7e2f1;
            border-radius: 8px;
            padding: 3px 7px;
            background: #fff;
            color: #66758d;
            font-size: 12px;
            font-weight: 600;
        }

        .widget-modal-body {
            padding: 0 22px 18px;
            overflow: auto;
        }

        .widget-quick {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .widget-quick-btn,
        .widget-tab {
            border: 1px solid #dbe5f3;
            background: #fff;
            color: #475569;
            min-height: 34px;
            border-radius: 999px;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 550;
            cursor: pointer;
            transition: all 180ms ease;
        }

        .widget-quick-btn.active,
        .widget-tab.active {
            border-color: #b4cbfa;
            color: #255fd4;
            background: #edf4ff;
        }

        .widget-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .widget-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 14px;
            padding-bottom: 4px;
        }

        .widget-card {
            border: 1px solid #dbe5f3;
            background: #fff;
            border-radius: 16px;
            padding: 12px;
            min-height: 188px;
            text-align: left;
            cursor: pointer;
            transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 10px;
            animation: widgetCardIn 220ms ease both;
        }

        .widget-card:hover,
        .widget-card.active {
            transform: translateY(-2px);
            border-color: #97b6f3;
            background: #fafdff;
            box-shadow: 0 10px 20px rgba(17, 38, 77, 0.09);
        }

        .widget-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .widget-card-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            border: 1px solid #d5e1f3;
            background: #f4f8ff;
            display: grid;
            place-items: center;
            font-size: 15px;
        }

        .widget-fav-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid transparent;
            background: transparent;
            color: #8a9ab2;
            cursor: pointer;
            transition: all 150ms ease;
        }

        .widget-fav-btn:hover {
            border-color: #dbe5f3;
            background: #f5f9ff;
        }

        .widget-fav-btn.active {
            color: #2563eb;
            background: #edf4ff;
            border-color: #c7d8f7;
        }

        .widget-preview {
            border: 1px solid #e3ebf8;
            border-radius: 12px;
            background: linear-gradient(180deg, #fbfdff, #f7faff);
            min-height: 72px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            justify-content: center;
        }

        .sk-line {
            height: 8px;
            border-radius: 999px;
            background: #d5e1f3;
        }

        .sk-box {
            width: 100%;
            height: 38px;
            border-radius: 8px;
            border: 1px dashed #c4d6f0;
            color: #8fa3c3;
            display: grid;
            place-items: center;
            font-size: 11px;
            letter-spacing: 0.06em;
            font-weight: 600;
        }

        .sk-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }

        .sk-grid span {
            height: 16px;
            border-radius: 6px;
            background: #d8e3f6;
        }

        .widget-card-name {
            font-size: 14px;
            font-weight: 640;
            color: #0f172a;
        }

        .widget-card-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
            min-height: 32px;
        }

        .widget-insert {
            margin-top: auto;
            border: 1px solid #cddcf5;
            background: #fff;
            color: #295fcc;
            border-radius: 10px;
            min-height: 32px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 620;
            opacity: 0;
            transform: translateY(3px);
            transition: all 180ms ease;
            pointer-events: none;
            width: fit-content;
        }

        .widget-card:hover .widget-insert,
        .widget-card.active .widget-insert {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .widget-empty {
            border: 1px dashed #d4e0f3;
            border-radius: 16px;
            background: #fbfdff;
            min-height: 240px;
            display: grid;
            place-items: center;
            text-align: center;
            color: #64748b;
            padding: 20px;
        }

        .widget-empty-illus {
            width: 100px;
            height: 74px;
            border-radius: 14px;
            margin: 0 auto 10px;
            border: 1px solid #d7e3f6;
            background: linear-gradient(180deg, #f0f5ff, #ffffff);
            position: relative;
        }

        .widget-empty-illus::before {
            content: "";
            position: absolute;
            width: 62px;
            height: 8px;
            border-radius: 999px;
            background: #cfdbef;
            left: 50%;
            transform: translateX(-50%);
            top: 18px;
        }

        .widget-empty-illus::after {
            content: "";
            position: absolute;
            width: 42px;
            height: 8px;
            border-radius: 999px;
            background: #dce6f6;
            left: 50%;
            transform: translateX(-50%);
            top: 34px;
        }

        .drop-indicator {
            position: absolute;
            height: 3px;
            background: linear-gradient(90deg, #2d6cf6, #7ea8ff);
            border-radius: 999px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 120ms ease;
            z-index: 55;
        }

        .drop-indicator.show {
            opacity: 1;
        }

        .toast {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 120;
            background: #0f172a;
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            opacity: 0;
            transform: translateY(8px);
            transition: var(--transition);
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .skeleton {
            border-radius: 10px;
            background: linear-gradient(90deg, #eef3fb 25%, #f8fbff 37%, #eef3fb 63%);
            background-size: 400% 100%;
            animation: shimmer 1.4s linear infinite;
            height: 12px;
            margin-bottom: 8px;
        }

        .tablet .canvas-surface {
            max-width: 880px;
        }

        .mobile .canvas-surface {
            max-width: 430px;
        }

        .mobile .columns {
            flex-direction: column;
        }

        @media (max-width: 1160px) {
            :root {
                --rightbar-w: 286px;
            }
        }

        @media (max-width: 980px) {
            :root {
                --leftbar-w: 66px;
                --rightbar-w: 260px;
            }

            .left-item span:last-child {
                display: none;
            }

            .brand {
                display: none;
            }

            .topbar-center {
                display: none;
            }
        }

        @media (max-width: 860px) {
            .workspace {
                grid-template-columns: 56px 1fr;
            }

            .sidebar-right {
                position: fixed;
                right: 12px;
                top: calc(var(--topbar-h) + 12px);
                bottom: 12px;
                width: min(320px, calc(100vw - 24px));
                border-radius: 16px;
                border: 1px solid #d3e1f6;
                box-shadow: var(--shadow-2);
                z-index: 90;
            }
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.99);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes widgetIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                opacity: 0.9;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                opacity: 0.9;
            }
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: 100% 0;
            }

            100% {
                background-position: -100% 0;
            }
        }

        @keyframes widgetCardIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="builder desktop" id="builderRoot">
        <header class="topbar">
            <div class="topbar-left">
                <div class="logo">N</div>
                <div class="brand">Nova Builder</div>
                <select class="control" id="pageSelector">
                    <option>Home Page</option>
                    <option>About Page</option>
                    <option>Contact Page</option>
                </select>
            </div>
            <div class="topbar-center">
                <button class="icon-btn" id="undoBtn" title="Undo (Cmd/Ctrl+Z)">↺</button>
                <button class="icon-btn" id="redoBtn" title="Redo (Cmd/Ctrl+Shift+Z)">↻</button>
                <button class="action-btn" id="previewBtn">Preview</button>
                <button class="chip active" data-device="desktop">Desktop</button>
                <button class="chip" data-device="tablet">Tablet</button>
                <button class="chip" data-device="mobile">Mobile</button>
            </div>
            <div class="topbar-right">
                <button class="action-btn" id="saveBtn">Save</button>
                <button class="action-btn primary" id="publishBtn">Publish</button>
            </div>
        </header>

        <main class="workspace">
            <aside class="leftbar" id="leftTabs">
                <button class="left-item active" data-tab="Navigator"><span class="icon">☰</span><span>Navigator</span></button>
                <button class="left-item" data-tab="Pages"><span class="icon">P</span><span>Pages</span></button>
                <button class="left-item" data-tab="Layers"><span class="icon">L</span><span>Layers</span></button>
                <button class="left-item" data-tab="Styles"><span class="icon">G</span><span>Styles</span></button>
                <button class="left-item" data-tab="Media"><span class="icon">M</span><span>Media</span></button>
                <button class="left-item" data-tab="Templates"><span class="icon">T</span><span>Templates</span></button>
                <button class="left-item" data-tab="Components"><span class="icon">C</span><span>Components</span></button>
                <button class="left-item" data-tab="History"><span class="icon">H</span><span>History</span></button>
            </aside>

            <section class="canvas-wrap" id="canvasWrap">
                <div class="canvas-surface">
                    <div class="canvas-grid"></div>
                    <div class="page" id="pageCanvas">
                        <div class="empty-state" id="emptyState">
                            <div class="empty-illustration"></div>
                            <h2>Start Building Your Page</h2>
                            <p>Create sections visually and compose your page in minutes.</p>
                            <button class="action-btn primary" id="addSectionBtn">+ Add Section</button>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="sidebar-right" id="propertyPanel">
                <h3 class="panel-title">Properties</h3>
                <p class="panel-subtitle">Select a section, column, or widget to edit settings.</p>
                <div class="prop-group">
                    <h4>Quick Start</h4>
                    <div class="skeleton" style="width:92%"></div>
                    <div class="skeleton" style="width:74%"></div>
                    <div class="skeleton" style="width:84%"></div>
                </div>
            </aside>
        </main>
    </div>

    <div class="floating-panel" id="sectionPicker"></div>
    <div class="left-context-panel" id="leftContextPanel"></div>
    <div class="widget-overlay" id="widgetOverlay"></div>
    <div class="widget-modal" id="widgetLibrary" role="dialog" aria-modal="true" aria-label="Choose Widget"></div>
    <div class="drop-indicator" id="dropIndicator"></div>
    <div class="toast" id="toast"></div>

    <script>
        (function() {
            const state = {
                selected: null,
                history: [],
                historyIndex: -1,
                clipboard: null,
                currentInsertColumn: null,
                draggingEl: null,
                dropTarget: null,
                autoSaveKey: "nova_builder_autosave_html",
                sectionPickerAnchor: null,
                activeTab: "Navigator",
                pages: [{
                    id: "home_page",
                    name: "Home Page"
                }],
                currentPageId: "home_page",
                pageStore: {},
                components: [],
                widgetPicker: {
                    category: "All",
                    quick: "all",
                    search: "",
                    activeIndex: 0,
                    results: []
                }
            };

            const layoutCatalog = [{
                    id: "1-col",
                    label: "1 Column",
                    cols: [1]
                },
                {
                    id: "2-col",
                    label: "2 Equal Columns",
                    cols: [1, 1]
                },
                {
                    id: "3-col",
                    label: "3 Equal Columns",
                    cols: [1, 1, 1]
                },
                {
                    id: "4-col",
                    label: "4 Equal Columns",
                    cols: [1, 1, 1, 1]
                },
                {
                    id: "2-3-1-3",
                    label: "2/3 + 1/3",
                    cols: [2, 1]
                },
                {
                    id: "1-3-2-3",
                    label: "1/3 + 2/3",
                    cols: [1, 2]
                },
                {
                    id: "1-4-3-4",
                    label: "1/4 + 3/4",
                    cols: [1, 3]
                },
                {
                    id: "3-4-1-4",
                    label: "3/4 + 1/4",
                    cols: [3, 1]
                },
                {
                    id: "custom",
                    label: "Custom Grid",
                    cols: [1, 1, 1]
                }
            ];

            const widgetCategories = ["All", "Basic", "Media", "Business", "Marketing", "Forms", "Social", "Commerce", "Advanced"];
            const widgetRegistry = [{
                    name: "Heading",
                    icon: "📝",
                    category: "Basic",
                    description: "Large title with optional subtitle.",
                    aliases: ["heading", "title", "headline", "hero title"],
                    preview: "heading"
                },
                {
                    name: "Text",
                    icon: "✍️",
                    category: "Basic",
                    description: "Rich text paragraph block.",
                    aliases: ["text", "paragraph", "copy", "content"],
                    preview: "text"
                },
                {
                    name: "Button",
                    icon: "🔘",
                    category: "Basic",
                    description: "Clickable call to action button.",
                    aliases: ["button", "cta", "link", "action"],
                    preview: "button"
                },
                {
                    name: "Divider",
                    icon: "➖",
                    category: "Basic",
                    description: "Visual separator line.",
                    aliases: ["divider", "line", "separator"],
                    preview: "divider"
                },
                {
                    name: "Spacer",
                    icon: "↕️",
                    category: "Basic",
                    description: "Adds vertical breathing space.",
                    aliases: ["spacer", "gap", "space"],
                    preview: "spacer"
                },
                {
                    name: "Image",
                    icon: "🖼️",
                    category: "Media",
                    description: "Single responsive image.",
                    aliases: ["image", "photo", "picture", "hero image"],
                    preview: "image"
                },
                {
                    name: "Gallery",
                    icon: "📷",
                    category: "Media",
                    description: "Responsive image gallery.",
                    aliases: ["gallery", "photos", "portfolio", "grid"],
                    preview: "gallery"
                },
                {
                    name: "Video",
                    icon: "🎬",
                    category: "Media",
                    description: "Embedded or hosted video.",
                    aliases: ["video", "youtube", "vimeo", "media"],
                    preview: "video"
                },
                {
                    name: "Slider",
                    icon: "🎞️",
                    category: "Media",
                    description: "Interactive image/content slider.",
                    aliases: ["slider", "carousel", "banner", "hero slider"],
                    preview: "slider"
                },
                {
                    name: "Icon",
                    icon: "⭐",
                    category: "Basic",
                    description: "Decorative or functional icon.",
                    aliases: ["icon", "symbol", "badge"],
                    preview: "icon"
                },
                {
                    name: "Features",
                    icon: "✨",
                    category: "Business",
                    description: "Feature list with highlights.",
                    aliases: ["features", "benefits", "service cards"],
                    preview: "features"
                },
                {
                    name: "Services",
                    icon: "🛠️",
                    category: "Business",
                    description: "Service showcase section.",
                    aliases: ["services", "offerings", "solutions"],
                    preview: "services"
                },
                {
                    name: "Pricing",
                    icon: "💳",
                    category: "Commerce",
                    description: "Plans, prices, and benefits.",
                    aliases: ["pricing", "plans", "subscription", "cost", "price"],
                    preview: "pricing"
                },
                {
                    name: "FAQ",
                    icon: "❓",
                    category: "Marketing",
                    description: "Common questions accordion.",
                    aliases: ["faq", "questions", "accordion", "help"],
                    preview: "faq"
                },
                {
                    name: "Team",
                    icon: "👥",
                    category: "Business",
                    description: "Team member profile grid.",
                    aliases: ["team", "members", "about us", "staff"],
                    preview: "team"
                },
                {
                    name: "Testimonials",
                    icon: "💬",
                    category: "Marketing",
                    description: "Customer review section.",
                    aliases: ["testimonials", "reviews", "social proof", "rating"],
                    preview: "testimonials"
                },
                {
                    name: "Statistics",
                    icon: "📊",
                    category: "Business",
                    description: "Key metrics and counters.",
                    aliases: ["statistics", "numbers", "counter", "facts"],
                    preview: "statistics"
                },
                {
                    name: "Progress Bar",
                    icon: "📈",
                    category: "Business",
                    description: "Visual progress indicator.",
                    aliases: ["progress", "bar", "meter"],
                    preview: "progress"
                },
                {
                    name: "Contact Form",
                    icon: "📨",
                    category: "Forms",
                    description: "Lead capture contact form.",
                    aliases: ["contact", "form", "lead", "enquiry"],
                    preview: "contact"
                },
                {
                    name: "Newsletter",
                    icon: "✉️",
                    category: "Forms",
                    description: "Email signup form.",
                    aliases: ["newsletter", "email", "subscribe", "signup"],
                    preview: "newsletter"
                },
                {
                    name: "Call To Action",
                    icon: "🚀",
                    category: "Marketing",
                    description: "High-conversion CTA block.",
                    aliases: ["cta", "call to action", "hero", "banner"],
                    preview: "hero"
                },
                {
                    name: "Instagram",
                    icon: "📸",
                    category: "Social",
                    description: "Instagram feed showcase.",
                    aliases: ["instagram", "social", "feed"],
                    preview: "gallery"
                },
                {
                    name: "YouTube",
                    icon: "▶️",
                    category: "Social",
                    description: "YouTube video gallery.",
                    aliases: ["youtube", "video", "channel"],
                    preview: "video"
                },
                {
                    name: "Map",
                    icon: "🗺️",
                    category: "Advanced",
                    description: "Interactive location map.",
                    aliases: ["map", "location", "address", "google map"],
                    preview: "map"
                },
                {
                    name: "Custom HTML",
                    icon: "</>",
                    category: "Advanced",
                    description: "Insert custom HTML block.",
                    aliases: ["html", "code", "custom", "embed code"],
                    preview: "code"
                },
                {
                    name: "Embed",
                    icon: "🔗",
                    category: "Advanced",
                    description: "Embed external content.",
                    aliases: ["embed", "iframe", "external", "script"],
                    preview: "embed"
                }
            ];

            const builderRoot = document.getElementById("builderRoot");
            const pageCanvas = document.getElementById("pageCanvas");
            const propertyPanel = document.getElementById("propertyPanel");
            const sectionPicker = document.getElementById("sectionPicker");
            const leftContextPanel = document.getElementById("leftContextPanel");
            const widgetLibrary = document.getElementById("widgetLibrary");
            const widgetOverlay = document.getElementById("widgetOverlay");
            const dropIndicator = document.getElementById("dropIndicator");
            const canvasWrap = document.getElementById("canvasWrap");
            const toast = document.getElementById("toast");

            init();

            function init() {
                const addSectionBtn = document.getElementById("addSectionBtn");
                if (addSectionBtn) {
                    addSectionBtn.addEventListener("click", (e) => openSectionPicker(e.currentTarget));
                }
                document.getElementById("undoBtn").addEventListener("click", undo);
                document.getElementById("redoBtn").addEventListener("click", redo);
                document.getElementById("saveBtn").addEventListener("click", saveNow);
                document.getElementById("publishBtn").addEventListener("click", () => showToast("Site published successfully"));
                document.getElementById("previewBtn").addEventListener("click", () => showToast("Preview opened in a new tab"));
                document.getElementById("pageSelector").addEventListener("change", (e) => {
                    switchToPage(e.target.value);
                });

                bindDeviceToggles();
                bindLeftTabs();
                bindKeyboardShortcuts();
                bindGlobalInteractions();
                restoreAutosave();
                syncEmptyState();
                syncPageSelector();
                state.pageStore[state.currentPageId] = pageCanvas.innerHTML;
                pushHistory();
                setPanelIntro();
                updateCustomCssStyles();

                setInterval(() => {
                    localStorage.setItem(state.autoSaveKey, pageCanvas.innerHTML);
                }, 4000);
            }

            function bindDeviceToggles() {
                document.querySelectorAll("[data-device]").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        document.querySelectorAll("[data-device]").forEach((el) => el.classList.remove("active"));
                        btn.classList.add("active");
                        const mode = btn.dataset.device;
                        builderRoot.classList.remove("desktop", "tablet", "mobile");
                        builderRoot.classList.add(mode);
                        showToast(mode.charAt(0).toUpperCase() + mode.slice(1) + " view");
                    });
                });
            }

            function bindKeyboardShortcuts() {
                document.addEventListener("keydown", (e) => {
                    if (handleWidgetPickerKeys(e)) {
                        return;
                    }

                    if (isTypingTarget(e.target)) {
                        return;
                    }

                    if (e.key === "/") {
                        e.preventDefault();
                        if (state.currentInsertColumn) {
                            openWidgetLibrary(state.currentInsertColumn);
                        } else {
                            const fallback = pageCanvas.querySelector(".column");
                            if (fallback) {
                                openWidgetLibrary(fallback);
                            }
                        }
                        return;
                    }

                    const isMeta = e.metaKey || e.ctrlKey;
                    if (isMeta && e.key.toLowerCase() === "z" && !e.shiftKey) {
                        e.preventDefault();
                        undo();
                    }
                    if (isMeta && (e.key.toLowerCase() === "y" || (e.key.toLowerCase() === "z" && e.shiftKey))) {
                        e.preventDefault();
                        redo();
                    }
                    if (isMeta && e.key.toLowerCase() === "s") {
                        e.preventDefault();
                        saveNow();
                    }
                    if (isMeta && e.key.toLowerCase() === "k") {
                        e.preventDefault();
                        if (state.currentInsertColumn) {
                            openWidgetLibrary(state.currentInsertColumn);
                        } else {
                            const fallback = pageCanvas.querySelector(".column");
                            if (fallback) {
                                openWidgetLibrary(fallback);
                            }
                        }
                    }
                });
            }

            function bindGlobalInteractions() {
                document.addEventListener("click", (e) => {
                    if (!sectionPicker.contains(e.target) && e.target.id !== "addSectionBtn") {
                        sectionPicker.classList.remove("open");
                        state.sectionPickerAnchor = null;
                    }

                    if (!leftContextPanel.contains(e.target) && !e.target.closest(".left-item")) {
                        if (state.activeTab !== "Navigator") {
                            leftContextPanel.classList.remove("open");
                            state.activeTab = "Navigator";
                            highlightActiveTab("Navigator");
                        }
                    }
                });

                widgetOverlay.addEventListener("click", closeWidgetLibrary);

                pageCanvas.addEventListener("click", (e) => {
                    const node = e.target.closest(".section, .column, .widget");
                    if (node) {
                        selectNode(node);
                    }
                });

                pageCanvas.addEventListener("input", (e) => {
                    if (e.target.classList.contains("editable")) {
                        pushHistoryDebounced();
                    }
                });

                pageCanvas.addEventListener("click", (e) => {
                    const addWidgetBtn = e.target.closest(".add-widget-btn");
                    if (!addWidgetBtn) return;
                    const col = addWidgetBtn.closest(".column");
                    if (col) {
                        openWidgetLibrary(col);
                    }
                });

                document.addEventListener("click", (e) => {
                    const addSection = e.target.closest("#addSectionBtn");
                    if (!addSection) return;
                    e.preventDefault();
                    e.stopPropagation();
                    openSectionPicker(addSection);
                });

                pageCanvas.addEventListener("click", (e) => {
                    const trigger = e.target.closest(".image-upload-trigger");
                    if (!trigger) return;
                    const widget = trigger.closest(".widget");
                    const input = widget ? widget.querySelector(".image-upload-input") : null;
                    if (input) input.click();
                });

                pageCanvas.addEventListener("change", (e) => {
                    if (!e.target.classList.contains("image-upload-input")) return;
                    const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                    const widget = e.target.closest(".widget");
                    if (file && widget) {
                        setWidgetImageFromFile(widget, file);
                    }
                });

                pageCanvas.addEventListener("click", (e) => {
                    const actionBtn = e.target.closest("[data-action]");
                    if (!actionBtn) return;
                    const action = actionBtn.dataset.action;
                    const owner = actionBtn.closest(".section, .column, .widget");
                    if (!owner) return;
                    runAction(action, owner);
                });

                pageCanvas.addEventListener("dragstart", onDragStart);
                pageCanvas.addEventListener("dragover", onDragOver);
                pageCanvas.addEventListener("drop", onDrop);
                pageCanvas.addEventListener("dragend", onDragEnd);

                propertyPanel.addEventListener("click", (e) => {
                    const panelTrigger = e.target.closest("[data-action='panel-image-upload']");
                    if (!panelTrigger) return;
                    const fileInput = propertyPanel.querySelector("#panelImageUpload");
                    if (fileInput) fileInput.click();
                });

                propertyPanel.addEventListener("change", (e) => {
                    if (e.target.id !== "panelImageUpload") return;
                    const selected = state.selected;
                    const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                    if (selected && selected.dataset.widgetType === "Image" && file) {
                        setWidgetImageFromFile(selected, file);
                        renderPropertyPanel(selected);
                    }
                });

                leftContextPanel.addEventListener("click", (e) => {
                    const action = e.target.closest("[data-left-action]");
                    if (!action) return;
                    runLeftPanelAction(action.dataset.leftAction, action.dataset.value || "");
                });

                leftContextPanel.addEventListener("input", (e) => {
                    if (e.target.id === "globalAccent") {
                        document.documentElement.style.setProperty("--accent", e.target.value);
                        showToast("Accent updated");
                    }
                    if (e.target.id === "globalBg") {
                        document.documentElement.style.setProperty("--bg", e.target.value);
                    }
                });

                window.addEventListener("resize", () => {
                    repositionSectionPicker();
                });

                canvasWrap.addEventListener("scroll", () => {
                    repositionSectionPicker();
                });
            }

            function bindLeftTabs() {
                document.querySelectorAll("#leftTabs .left-item").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const tab = btn.dataset.tab || "Navigator";
                        state.activeTab = tab;
                        highlightActiveTab(tab);
                        if (tab === "Navigator") {
                            leftContextPanel.classList.remove("open");
                            return;
                        }
                        renderLeftPanel(tab);
                    });
                });
            }

            function highlightActiveTab(tab) {
                document.querySelectorAll("#leftTabs .left-item").forEach((el) => {
                    el.classList.toggle("active", (el.dataset.tab || "Navigator") === tab);
                });
            }

            function renderLeftPanel(tab) {
                let body = "";
                if (tab === "Pages") {
                    const pageItems = state.pages.map((p) => {
                        return '<div class="left-list-item"><span>' + escapeHtml(p.name) + '</span><button data-left-action="switch-page" data-value="' + escapeHtml(p.id) + '">Open</button></div>';
                    }).join("");
                    body = '<input id="newPageName" class="left-panel-input" placeholder="New page name">' +
                        '<div class="left-panel-actions"><button class="left-panel-btn" data-left-action="add-page">+ Add Page</button></div>' +
                        '<div class="left-list">' + pageItems + '</div>';
                } else if (tab === "Layers") {
                    const sections = pageCanvas.querySelectorAll(".section").length;
                    const columns = pageCanvas.querySelectorAll(".column").length;
                    const widgets = pageCanvas.querySelectorAll(".widget").length;
                    body = '<div class="left-list">' +
                        '<div class="left-list-item"><span>Sections</span><strong>' + sections + '</strong></div>' +
                        '<div class="left-list-item"><span>Columns</span><strong>' + columns + '</strong></div>' +
                        '<div class="left-list-item"><span>Widgets</span><strong>' + widgets + '</strong></div>' +
                        '</div><div class="left-panel-actions"><button class="left-panel-btn" data-left-action="refresh-layers">Refresh</button></div>';
                } else if (tab === "Styles") {
                    body = '<div class="left-list">' +
                        '<div class="left-list-item" style="display:block;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Accent</label><input id="globalAccent" type="color" value="#2d6cf6" style="width:100%;height:34px;border:1px solid #d7e3f5;border-radius:8px;"></div>' +
                        '<div class="left-list-item" style="display:block;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Background</label><input id="globalBg" type="color" value="#f3f6fb" style="width:100%;height:34px;border:1px solid #d7e3f5;border-radius:8px;"></div>' +
                        '</div>';
                } else if (tab === "Media") {
                    const media = Array.from(pageCanvas.querySelectorAll(".image-widget img[src]")).map((img, idx) => {
                        return '<div class="left-list-item"><span>Image ' + (idx + 1) + '</span><button data-left-action="focus-media" data-value="' + String(idx) + '">Select</button></div>';
                    }).join("");
                    body = '<div class="left-panel-actions"><button class="left-panel-btn" data-left-action="open-media-upload">Upload To Selected Image</button></div>' +
                        '<div class="left-list">' + (media || '<div class="left-list-item"><span>No media yet</span></div>') + '</div>';
                } else if (tab === "Templates") {
                    body = '<div class="left-list">' +
                        '<div class="left-list-item"><span>Hero Template</span><button data-left-action="insert-template" data-value="hero">Insert</button></div>' +
                        '<div class="left-list-item"><span>Pricing Template</span><button data-left-action="insert-template" data-value="pricing">Insert</button></div>' +
                        '<div class="left-list-item"><span>Contact Template</span><button data-left-action="insert-template" data-value="contact">Insert</button></div>' +
                        '</div>';
                } else if (tab === "Components") {
                    const componentItems = state.components.map((c, idx) => {
                        return '<div class="left-list-item"><span>' + escapeHtml(c.name) + '</span><button data-left-action="insert-component" data-value="' + idx + '">Insert</button></div>';
                    }).join("");
                    body = '<input id="componentName" class="left-panel-input" placeholder="Component name">' +
                        '<div class="left-panel-actions"><button class="left-panel-btn" data-left-action="save-component">Save Selected</button></div>' +
                        '<div class="left-list">' + (componentItems || '<div class="left-list-item"><span>No saved components</span></div>') + '</div>';
                } else if (tab === "History") {
                    const items = state.history.map((h, idx) => {
                        const current = idx === state.historyIndex ? ' style="border-color:#9dbcf6;background:#edf4ff;"' : "";
                        return '<div class="left-list-item"' + current + '><span>Snapshot ' + (idx + 1) + '</span><button data-left-action="history-go" data-value="' + idx + '">Go</button></div>';
                    }).join("");
                    body = '<div class="left-panel-actions"><button class="left-panel-btn" data-left-action="undo">Undo</button><button class="left-panel-btn" data-left-action="redo">Redo</button></div>' +
                        '<div class="left-list">' + (items || '<div class="left-list-item"><span>No history yet</span></div>') + '</div>';
                }

                leftContextPanel.innerHTML =
                    '<h3 class="left-context-title">' + tab + '</h3>' +
                    '<p class="left-context-sub">Interactive tools for ' + tab + '.</p>' +
                    body;
                leftContextPanel.classList.add("open");
            }

            function runLeftPanelAction(action, value) {
                if (action === "add-page") {
                    const input = document.getElementById("newPageName");
                    const name = input && input.value.trim() ? input.value.trim() : "Untitled Page";
                    const id = "page_" + Date.now();
                    state.pages.push({
                        id,
                        name
                    });
                    state.pageStore[id] = '<div class="empty-state" id="emptyState">' +
                        '<div class="empty-illustration"></div>' +
                        '<h2>Start Building Your Page</h2>' +
                        '<p>Create sections visually and compose your page in minutes.</p>' +
                        '<button class="action-btn primary" id="addSectionBtn">+ Add Section</button>' +
                        '</div>';
                    syncPageSelector();
                    switchToPage(id);
                    showToast("Page added");
                    renderLeftPanel("Pages");
                    return;
                }
                if (action === "switch-page") {
                    switchToPage(value);
                    showToast("Page switched");
                    return;
                }
                if (action === "refresh-layers") {
                    renderLeftPanel("Layers");
                    showToast("Layers refreshed");
                    return;
                }
                if (action === "open-media-upload") {
                    const selected = state.selected;
                    if (selected && selected.dataset.widgetType === "Image") {
                        const input = selected.querySelector(".image-upload-input");
                        if (input) input.click();
                    } else {
                        showToast("Select an Image widget first");
                    }
                    return;
                }
                if (action === "focus-media") {
                    const idx = Number(value);
                    const target = Array.from(pageCanvas.querySelectorAll(".widget[data-widget-type='Image']"))[idx];
                    if (target) {
                        selectNode(target);
                        showToast("Image selected");
                    }
                    return;
                }
                if (action === "insert-template") {
                    const layout = layoutCatalog.find((l) => l.id === "1-col") || layoutCatalog[0];
                    insertSection(layout);
                    const section = pageCanvas.querySelector(".section:last-child");
                    if (!section) return;
                    const col = section.querySelector(".column");
                    if (!col) return;
                    if (value === "hero") {
                        insertWidget("Heading", col);
                        insertWidget("Text", col);
                        insertWidget("Button", col);
                    } else if (value === "pricing") {
                        insertWidget("Pricing", col);
                    } else if (value === "contact") {
                        insertWidget("Contact Form", col);
                    }
                    showToast("Template inserted");
                    return;
                }
                if (action === "save-component") {
                    const selected = state.selected;
                    if (!selected) {
                        showToast("Select a block first");
                        return;
                    }
                    const nameInput = document.getElementById("componentName");
                    const name = nameInput && nameInput.value.trim() ? nameInput.value.trim() : "Component " + (state.components.length + 1);
                    state.components.push({
                        name,
                        html: selected.outerHTML
                    });
                    showToast("Component saved");
                    renderLeftPanel("Components");
                    return;
                }
                if (action === "insert-component") {
                    const index = Number(value);
                    const comp = state.components[index];
                    if (!comp) return;
                    const targetCol = state.selected?.closest(".column") || pageCanvas.querySelector(".column");
                    if (!targetCol) {
                        showToast("Add a section first");
                        return;
                    }
                    const temp = document.createElement("div");
                    temp.innerHTML = comp.html;
                    const node = temp.firstElementChild;
                    if (node) {
                        ensureNodeId(node);
                        targetCol.appendChild(node);
                        selectNode(node);
                        pushHistory();
                        showToast("Component inserted");
                    }
                    return;
                }
                if (action === "undo") {
                    undo();
                    renderLeftPanel("History");
                    return;
                }
                if (action === "redo") {
                    redo();
                    renderLeftPanel("History");
                    return;
                }
                if (action === "history-go") {
                    const idx = Number(value);
                    if (Number.isNaN(idx) || !state.history[idx]) return;
                    state.historyIndex = idx;
                    pageCanvas.innerHTML = state.history[idx];
                    syncEmptyState();
                    updateCustomCssStyles();
                    selectNode(null);
                    showToast("History restored");
                    renderLeftPanel("History");
                }
            }

            function openSectionPicker(anchor) {
                const cards = layoutCatalog.map((layout) => {
                    const total = layout.cols.reduce((sum, c) => sum + c, 0);
                    const preview = layout.cols.map((c) => {
                        const width = (c / total) * 100;
                        return '<div class="layout-col" style="width:' + width + '%"></div>';
                    }).join("");
                    return '<button class="layout-card" data-layout="' + layout.id + '">' +
                        '<div class="layout-visual">' + preview + '</div>' +
                        '<div class="layout-label">' + layout.label + '</div>' +
                        '</button>';
                }).join("");

                sectionPicker.innerHTML = '<h3>Choose a section layout</h3><div class="layout-grid">' + cards + '</div>';
                state.sectionPickerAnchor = anchor;
                repositionSectionPicker();
                sectionPicker.classList.add("open");

                sectionPicker.querySelectorAll(".layout-card").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const layout = layoutCatalog.find((l) => l.id === btn.dataset.layout);
                        if (layout) insertSection(layout);
                        sectionPicker.classList.remove("open");
                        state.sectionPickerAnchor = null;
                    });
                });
            }

            function repositionSectionPicker() {
                if (!state.sectionPickerAnchor || !sectionPicker.innerHTML) return;

                const gap = 12;
                const viewportPad = 12;
                const anchorRect = state.sectionPickerAnchor.getBoundingClientRect();

                sectionPicker.style.left = viewportPad + "px";
                sectionPicker.style.top = viewportPad + "px";

                const panelRect = sectionPicker.getBoundingClientRect();
                let left = anchorRect.left - 10;
                left = Math.max(viewportPad, Math.min(left, window.innerWidth - panelRect.width - viewportPad));

                const canOpenBelow = anchorRect.bottom + gap + panelRect.height <= window.innerHeight - viewportPad;
                const top = canOpenBelow ?
                    anchorRect.bottom + gap :
                    Math.max(viewportPad, anchorRect.top - panelRect.height - gap);

                sectionPicker.style.left = left + "px";
                sectionPicker.style.top = top + "px";
            }

            function insertSection(layout) {
                syncEmptyState();

                const section = document.createElement("div");
                section.className = "section";
                section.setAttribute("draggable", "true");
                section.dataset.nodeType = "section";
                ensureNodeId(section);
                section.innerHTML = getToolbar("section") + '<div class="columns"></div>';

                const total = layout.cols.reduce((sum, c) => sum + c, 0);
                const columnsWrap = section.querySelector(".columns");
                layout.cols.forEach((fraction) => {
                    const col = createColumn((fraction / total) * 100);
                    columnsWrap.appendChild(col);
                });

                pageCanvas.appendChild(section);
                syncEmptyState();
                selectNode(section);
                pushHistory();
                showToast("Section added");
            }

            function createColumn(widthPercent) {
                const column = document.createElement("div");
                column.className = "column";
                column.setAttribute("draggable", "true");
                column.dataset.nodeType = "column";
                ensureNodeId(column);
                column.dataset.width = widthPercent.toFixed(0);
                column.style.flex = "0 0 " + widthPercent + "%";
                column.innerHTML = getToolbar("column") + '<button class="add-widget-btn">+ Add Widget</button>';
                return column;
            }

            function openWidgetLibrary(column) {
                try {
                    state.currentInsertColumn = column;
                    state.widgetPicker.activeIndex = 0;
                    renderWidgetPicker();
                    widgetOverlay.classList.add("open");
                    widgetLibrary.classList.add("open");

                    const searchInput = document.getElementById("widgetSearch");
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                } catch (error) {
                    console.error("Widget picker failed to open", error);
                    showToast("Unable to open widget picker. Resetting cache...");
                    localStorage.removeItem("nova_builder_recent_widgets");
                    localStorage.removeItem("nova_builder_widget_usage");
                    localStorage.removeItem("nova_builder_favorite_widgets");
                }
            }

            function closeWidgetLibrary() {
                widgetOverlay.classList.remove("open");
                widgetLibrary.classList.remove("open");
            }

            function renderWidgetPicker() {
                const quick = state.widgetPicker.quick;
                const category = state.widgetPicker.category;
                const search = state.widgetPicker.search.trim().toLowerCase();
                const favorites = getWidgetFavorites();
                const frequent = getFrequentlyUsedWidgets();
                const recent = getRecentlyUsedWidgets();

                const filtered = widgetRegistry.filter((widget) => {
                    const categoryMatch = category === "All" || widget.category === category;
                    if (!categoryMatch) return false;

                    if (quick === "favorites" && !favorites.includes(widget.name)) return false;
                    if (quick === "frequent" && !frequent.includes(widget.name)) return false;
                    if (quick === "recent" && !recent.includes(widget.name)) return false;

                    if (!search) return true;
                    const haystack = [widget.name, widget.description, widget.category].concat(widget.aliases || []).join(" ").toLowerCase();
                    return haystack.includes(search);
                });

                state.widgetPicker.results = filtered;
                if (state.widgetPicker.activeIndex >= filtered.length) {
                    state.widgetPicker.activeIndex = Math.max(0, filtered.length - 1);
                }

                const quickButtons = [{
                        key: "all",
                        label: "All Widgets"
                    }, {
                        key: "recent",
                        label: "Recently Used"
                    }, {
                        key: "frequent",
                        label: "Frequently Used"
                    }, {
                        key: "favorites",
                        label: "Favorites"
                    }]
                    .map((item) => '<button class="widget-quick-btn ' + (quick === item.key ? 'active' : '') + '" data-quick="' + item.key + '">' + item.label + '</button>')
                    .join("");

                const tabs = widgetCategories
                    .map((name) => '<button class="widget-tab ' + (name === category ? 'active' : '') + '" data-category="' + name + '">' + name + '</button>')
                    .join("");

                const cards = filtered.map((widget, idx) => {
                    const isFavorite = favorites.includes(widget.name);
                    return '<article class="widget-card ' + (idx === state.widgetPicker.activeIndex ? 'active' : '') + '" data-widget="' + widget.name + '" tabindex="0" role="button" aria-label="Insert ' + escapeHtml(widget.name) + '" style="animation-delay:' + (idx * 16) + 'ms">' +
                        '<div class="widget-card-top">' +
                        '<div class="widget-card-icon">' + escapeHtml(widget.icon) + '</div>' +
                        '<button type="button" class="widget-fav-btn ' + (isFavorite ? 'active' : '') + '" data-favorite="' + widget.name + '" title="Favorite">★</button>' +
                        '</div>' +
                        '<div class="widget-preview">' + getWidgetPreview(widget.preview) + '</div>' +
                        '<div class="widget-card-name">' + escapeHtml(widget.name) + '</div>' +
                        '<div class="widget-card-desc">' + escapeHtml(widget.description) + '</div>' +
                        '<span class="widget-insert">Insert →</span>' +
                        '</article>';
                }).join("");

                const emptyMarkup =
                    '<div class="widget-empty">' +
                    '<div><div class="widget-empty-illus"></div><h4 style="font-size:20px;margin-bottom:6px;color:#0f172a;">No widgets found</h4><p>Try another keyword.</p></div>' +
                    '</div>';

                widgetLibrary.innerHTML =
                    '<div class="widget-modal-header">' +
                    '<h3 class="widget-modal-title">Choose Widget</h3>' +
                    '<p class="widget-modal-subtitle">Select a widget to insert into this column.</p>' +
                    '<div class="widget-search-wrap">' +
                    '<span class="widget-search-icon">⌕</span>' +
                    '<input class="widget-search" id="widgetSearch" placeholder="Search widgets..." value="' + escapeHtml(state.widgetPicker.search) + '">' +
                    '<span class="widget-shortcut">⌘K</span>' +
                    '</div>' +
                    '</div>' +
                    '<div class="widget-modal-body">' +
                    '<div class="widget-quick">' + quickButtons + '</div>' +
                    '<div class="widget-tabs">' + tabs + '</div>' +
                    '<div class="widget-cards" id="widgetCardGrid">' + (cards || emptyMarkup) + '</div>' +
                    '</div>';

                bindWidgetPickerEvents();
            }

            function bindWidgetPickerEvents() {
                const searchEl = document.getElementById("widgetSearch");
                if (searchEl) {
                    searchEl.addEventListener("input", (e) => {
                        state.widgetPicker.search = e.target.value;
                        state.widgetPicker.activeIndex = 0;
                        renderWidgetPicker();
                        const nextSearch = document.getElementById("widgetSearch");
                        if (nextSearch) {
                            nextSearch.focus();
                            nextSearch.setSelectionRange(nextSearch.value.length, nextSearch.value.length);
                        }
                    });
                }

                widgetLibrary.querySelectorAll("[data-quick]").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        state.widgetPicker.quick = btn.dataset.quick;
                        state.widgetPicker.activeIndex = 0;
                        renderWidgetPicker();
                    });
                });

                widgetLibrary.querySelectorAll("[data-category]").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        state.widgetPicker.category = btn.dataset.category;
                        state.widgetPicker.activeIndex = 0;
                        renderWidgetPicker();
                    });
                });

                widgetLibrary.querySelectorAll("[data-favorite]").forEach((btn) => {
                    btn.addEventListener("click", (e) => {
                        e.stopPropagation();
                        toggleWidgetFavorite(btn.dataset.favorite);
                        renderWidgetPicker();
                    });
                });

                widgetLibrary.querySelectorAll(".widget-card").forEach((card, idx) => {
                    card.addEventListener("mouseenter", () => {
                        state.widgetPicker.activeIndex = idx;
                        updateActiveWidgetCard();
                    });
                    card.addEventListener("click", (e) => {
                        if (e.target.closest("[data-favorite]")) return;
                        insertWidget(card.dataset.widget, state.currentInsertColumn);
                        closeWidgetLibrary();
                    });
                    card.addEventListener("keydown", (e) => {
                        if (e.key === "Enter" || e.key === " ") {
                            e.preventDefault();
                            insertWidget(card.dataset.widget, state.currentInsertColumn);
                            closeWidgetLibrary();
                        }
                    });
                });
            }

            function handleWidgetPickerKeys(e) {
                const modalOpen = widgetLibrary.classList.contains("open");
                if (!modalOpen) return false;

                if (e.key === "Escape") {
                    e.preventDefault();
                    closeWidgetLibrary();
                    return true;
                }

                if (e.key === "Enter" && state.widgetPicker.results[state.widgetPicker.activeIndex]) {
                    e.preventDefault();
                    insertWidget(state.widgetPicker.results[state.widgetPicker.activeIndex].name, state.currentInsertColumn);
                    closeWidgetLibrary();
                    return true;
                }

                const cardsCount = state.widgetPicker.results.length;
                if (!cardsCount) return false;

                const step = getWidgetGridColumns();
                if (e.key === "ArrowRight") {
                    e.preventDefault();
                    state.widgetPicker.activeIndex = Math.min(cardsCount - 1, state.widgetPicker.activeIndex + 1);
                    updateActiveWidgetCard();
                    return true;
                }
                if (e.key === "ArrowLeft") {
                    e.preventDefault();
                    state.widgetPicker.activeIndex = Math.max(0, state.widgetPicker.activeIndex - 1);
                    updateActiveWidgetCard();
                    return true;
                }
                if (e.key === "ArrowDown") {
                    e.preventDefault();
                    state.widgetPicker.activeIndex = Math.min(cardsCount - 1, state.widgetPicker.activeIndex + step);
                    updateActiveWidgetCard();
                    return true;
                }
                if (e.key === "ArrowUp") {
                    e.preventDefault();
                    state.widgetPicker.activeIndex = Math.max(0, state.widgetPicker.activeIndex - step);
                    updateActiveWidgetCard();
                    return true;
                }

                return false;
            }

            function updateActiveWidgetCard() {
                const cards = widgetLibrary.querySelectorAll(".widget-card");
                cards.forEach((card, idx) => {
                    card.classList.toggle("active", idx === state.widgetPicker.activeIndex);
                });
                const active = cards[state.widgetPicker.activeIndex];
                if (active) {
                    active.scrollIntoView({
                        block: "nearest"
                    });
                }
            }

            function syncPageSelector() {
                const selector = document.getElementById("pageSelector");
                selector.innerHTML = state.pages.map((p) => {
                    const selected = p.id === state.currentPageId ? " selected" : "";
                    return '<option value="' + escapeHtml(p.id) + '"' + selected + '>' + escapeHtml(p.name) + '</option>';
                }).join("");
            }

            function switchToPage(pageId) {
                if (!pageId || pageId === state.currentPageId) return;
                state.pageStore[state.currentPageId] = pageCanvas.innerHTML;
                state.currentPageId = pageId;
                pageCanvas.innerHTML = state.pageStore[pageId] || '<div class="empty-state" id="emptyState">' +
                    '<div class="empty-illustration"></div>' +
                    '<h2>Start Building Your Page</h2>' +
                    '<p>Create sections visually and compose your page in minutes.</p>' +
                    '<button class="action-btn primary" id="addSectionBtn">+ Add Section</button>' +
                    '</div>';
                syncEmptyState();
                updateCustomCssStyles();
                selectNode(null);
                pushHistory();
                syncPageSelector();
            }

            function getWidgetGridColumns() {
                const grid = document.getElementById("widgetCardGrid");
                if (!grid) return 4;
                const computed = window.getComputedStyle(grid).gridTemplateColumns;
                if (!computed) return 4;
                return computed.split(" ").filter(Boolean).length || 4;
            }

            function getWidgetPreview(type) {
                const p = {
                    heading: '<div class="sk-line" style="width:88%"></div><div class="sk-line" style="width:52%"></div>',
                    image: '<div class="sk-box">IMAGE</div>',
                    text: '<div class="sk-line" style="width:96%"></div><div class="sk-line" style="width:82%"></div><div class="sk-line" style="width:64%"></div>',
                    button: '<div class="sk-line" style="width:48%;height:14px;background:#bcd1f5"></div>',
                    gallery: '<div class="sk-grid"><span></span><span></span><span></span><span></span><span></span><span></span></div>',
                    faq: '<div class="sk-line" style="width:86%"></div><div class="sk-line" style="width:42%"></div><div class="sk-line" style="width:72%"></div>',
                    contact: '<div class="sk-line" style="width:68%"></div><div class="sk-line" style="width:100%"></div><div class="sk-line" style="width:100%"></div><div class="sk-line" style="width:54%;height:12px;background:#bcd1f5"></div>',
                    pricing: '<div class="sk-line" style="width:30%"></div><div class="sk-line" style="width:46%;height:13px;background:#bcd1f5"></div><div class="sk-line" style="width:74%"></div>',
                    hero: '<div class="sk-line" style="width:86%"></div><div class="sk-line" style="width:56%"></div><div class="sk-box" style="height:20px">BUTTON</div>',
                    testimonials: '<div class="sk-line" style="width:44%"></div><div class="sk-line" style="width:82%"></div><div class="sk-line" style="width:52%"></div>',
                    video: '<div class="sk-box">VIDEO</div>',
                    map: '<div class="sk-box">MAP</div>',
                    slider: '<div class="sk-box">SLIDER</div>',
                    code: '<div class="sk-line" style="width:94%"></div><div class="sk-line" style="width:84%"></div><div class="sk-line" style="width:64%"></div>',
                    embed: '<div class="sk-box">EMBED</div>',
                    divider: '<div class="sk-line" style="width:100%;height:2px"></div>',
                    spacer: '<div class="sk-box" style="height:26px">SPACE</div>',
                    features: '<div class="sk-line" style="width:86%"></div><div class="sk-line" style="width:72%"></div><div class="sk-line" style="width:64%"></div>',
                    services: '<div class="sk-grid"><span></span><span></span><span></span></div>',
                    statistics: '<div class="sk-line" style="width:34%;height:12px;background:#bcd1f5"></div><div class="sk-line" style="width:58%"></div>',
                    progress: '<div class="sk-line" style="width:100%;height:9px;background:#d0def4"></div><div class="sk-line" style="width:76%;height:9px;background:#b8cdf3"></div>',
                    team: '<div class="sk-grid"><span></span><span></span><span></span></div><div class="sk-line" style="width:78%"></div>',
                    newsletter: '<div class="sk-line" style="width:72%"></div><div class="sk-line" style="width:100%"></div><div class="sk-line" style="width:50%;height:12px;background:#bcd1f5"></div>',
                    icon: '<div class="sk-box" style="height:34px">ICON</div>'
                };
                return p[type] || p.text;
            }

            function getRecentlyUsedWidgets() {
                const stored = localStorage.getItem("nova_builder_recent_widgets");
                return parseStoredArray(stored);
            }

            function getFrequentlyUsedWidgets() {
                const stored = localStorage.getItem("nova_builder_widget_usage");
                const usage = parseStoredObject(stored);
                return Object.keys(usage).sort((a, b) => Number(usage[b]) - Number(usage[a])).slice(0, 8);
            }

            function getWidgetFavorites() {
                const stored = localStorage.getItem("nova_builder_favorite_widgets");
                return parseStoredArray(stored);
            }

            function toggleWidgetFavorite(name) {
                const current = getWidgetFavorites();
                const next = current.includes(name) ? current.filter((n) => n !== name) : current.concat(name);
                localStorage.setItem("nova_builder_favorite_widgets", JSON.stringify(next));
            }

            function recordWidgetUsage(name) {
                const usageStored = localStorage.getItem("nova_builder_widget_usage");
                const usage = parseStoredObject(usageStored);
                usage[name] = Number(usage[name] || 0) + 1;
                localStorage.setItem("nova_builder_widget_usage", JSON.stringify(usage));

                const recent = getRecentlyUsedWidgets().filter((item) => item !== name);
                recent.unshift(name);
                localStorage.setItem("nova_builder_recent_widgets", JSON.stringify(recent.slice(0, 12)));
            }

            function isTypingTarget(target) {
                if (!target) return false;
                return Boolean(target.closest("input, textarea, select, [contenteditable='true']"));
            }

            function insertWidget(type, column) {
                if (!column) return;
                const addBtn = column.querySelector(".add-widget-btn");

                const widget = document.createElement("div");
                widget.className = "widget";
                widget.setAttribute("draggable", "true");
                widget.dataset.nodeType = "widget";
                widget.dataset.widgetType = type;
                ensureNodeId(widget);
                widget.innerHTML = getToolbar("widget") + getWidgetMarkup(type);

                if (addBtn) {
                    column.insertBefore(widget, addBtn);
                } else {
                    column.appendChild(widget);
                    ensureAddWidgetButton(column);
                }
                selectNode(widget);
                recordWidgetUsage(type);
                pushHistory();
                showToast(type + " widget added");
            }

            function parseStoredArray(stored) {
                if (!stored) return [];
                try {
                    const parsed = JSON.parse(stored);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    return [];
                }
            }

            function parseStoredObject(stored) {
                if (!stored) return {};
                try {
                    const parsed = JSON.parse(stored);
                    return parsed && typeof parsed === "object" && !Array.isArray(parsed) ? parsed : {};
                } catch (error) {
                    return {};
                }
            }

            function getWidgetMarkup(type) {
                const map = {
                    Heading: '<h1 class="editable" contenteditable="true">Craft a premium headline</h1>',
                    Text: '<p class="editable" contenteditable="true">Click and type your story directly on canvas. No modal, no interruption.</p>',
                    Button: '<button class="editable" contenteditable="true">Get Started</button>',
                    Image: '<div class="image-widget" data-ratio="16/9"><div class="image-placeholder">Image Placeholder</div><img alt="" style="display:none;"><div class="image-widget-controls"><button type="button" class="image-upload-chip image-upload-trigger">Upload Image</button></div><input type="file" class="image-upload-input" accept="image/*" style="display:none;"></div>',
                    Video: '<div style="border:1px dashed #c8d9f3;border-radius:10px;background:#f4f8ff;min-height:180px;display:grid;place-items:center;color:#5b6a80;font-weight:600;">Paste video URL in properties</div>',
                    Gallery: '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">' +
                        '<div style="height:70px;border:1px dashed #c8d9f3;border-radius:8px;background:#f4f8ff"></div>' +
                        '<div style="height:70px;border:1px dashed #c8d9f3;border-radius:8px;background:#f4f8ff"></div>' +
                        '<div style="height:70px;border:1px dashed #c8d9f3;border-radius:8px;background:#f4f8ff"></div>' +
                        '</div>',
                    Slider: '<div style="border:1px dashed #c8d9f3;border-radius:10px;background:#f4f8ff;min-height:150px;padding:16px;"><p class="editable" contenteditable="true" style="font-weight:700;margin-bottom:8px;">Slider Slide 1</p><p class="editable" contenteditable="true">Add multiple slides from properties.</p></div>',
                    Icon: '<div style="font-size:40px;line-height:1;">★</div>',
                    Features: '<ul style="display:grid;gap:8px;padding-left:18px;color:#334155;"><li class="editable" contenteditable="true">Fast setup</li><li class="editable" contenteditable="true">Clean UI</li><li class="editable" contenteditable="true">Responsive design</li></ul>',
                    Services: '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">' +
                        '<div style="border:1px solid #d9e4f7;border-radius:10px;padding:10px;"><p class="editable" contenteditable="true"><strong>Service A</strong></p><p class="editable" contenteditable="true">Short description.</p></div>' +
                        '<div style="border:1px solid #d9e4f7;border-radius:10px;padding:10px;"><p class="editable" contenteditable="true"><strong>Service B</strong></p><p class="editable" contenteditable="true">Short description.</p></div>' +
                        '</div>',
                    Pricing: '<div style="border:1px solid #d9e4f7;border-radius:12px;padding:14px;"><p class="editable" contenteditable="true" style="font-weight:700;">Starter</p><p class="editable" contenteditable="true" style="font-size:28px;font-weight:800;">₹999</p><p class="editable" contenteditable="true">Monthly billing</p><button class="editable" contenteditable="true" style="margin-top:8px;">Choose Plan</button></div>',
                    FAQ: '<div style="display:grid;gap:8px;"><details open><summary>What is included?</summary><p class="editable" contenteditable="true">Everything you need to launch.</p></details><details><summary>Can I cancel anytime?</summary><p class="editable" contenteditable="true">Yes, no long-term lock-in.</p></details></div>',
                    Team: '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">' +
                        '<div style="text-align:center;border:1px solid #d9e4f7;border-radius:10px;padding:10px;"><div style="width:46px;height:46px;border-radius:50%;background:#dce8ff;margin:0 auto 8px;"></div><p class="editable" contenteditable="true">Member 1</p></div>' +
                        '<div style="text-align:center;border:1px solid #d9e4f7;border-radius:10px;padding:10px;"><div style="width:46px;height:46px;border-radius:50%;background:#dce8ff;margin:0 auto 8px;"></div><p class="editable" contenteditable="true">Member 2</p></div>' +
                        '<div style="text-align:center;border:1px solid #d9e4f7;border-radius:10px;padding:10px;"><div style="width:46px;height:46px;border-radius:50%;background:#dce8ff;margin:0 auto 8px;"></div><p class="editable" contenteditable="true">Member 3</p></div>' +
                        '</div>',
                    Testimonials: '<div style="border:1px solid #d9e4f7;border-radius:10px;padding:12px;"><p>★★★★★</p><p class="editable" contenteditable="true">This builder is incredibly easy to use.</p><p class="editable" contenteditable="true" style="font-weight:700;">Customer Name</p></div>',
                    Statistics: '<div style="display:flex;gap:16px;flex-wrap:wrap;"><div><p class="editable" contenteditable="true" style="font-size:24px;font-weight:800;">120+</p><p>Projects</p></div><div><p class="editable" contenteditable="true" style="font-size:24px;font-weight:800;">98%</p><p>Satisfaction</p></div></div>',
                    "Progress Bar": '<div style="display:grid;gap:8px;"><p class="editable" contenteditable="true">Onboarding Progress</p><div style="height:8px;border-radius:999px;background:#e3ebfa;"><div style="height:100%;width:68%;border-radius:999px;background:#2d6cf6;"></div></div></div>',
                    "Contact Form": '<form style="display:grid;gap:8px;"><input placeholder="Name" style="min-height:36px;border:1px solid #d1def3;border-radius:8px;padding:0 10px;"><input placeholder="Email" style="min-height:36px;border:1px solid #d1def3;border-radius:8px;padding:0 10px;"><textarea placeholder="Message" style="min-height:86px;border:1px solid #d1def3;border-radius:8px;padding:10px;"></textarea><button type="button" class="editable" contenteditable="true">Submit</button></form>',
                    Newsletter: '<div style="display:flex;gap:8px;flex-wrap:wrap;"><input placeholder="Enter email" style="flex:1;min-width:190px;min-height:36px;border:1px solid #d1def3;border-radius:8px;padding:0 10px;"><button type="button" class="editable" contenteditable="true">Subscribe</button></div>',
                    "Call To Action": '<div style="border:1px solid #d9e4f7;border-radius:12px;padding:14px;background:#f8fbff;"><h3 class="editable" contenteditable="true">Launch your project today</h3><p class="editable" contenteditable="true">Create landing pages in minutes.</p><button class="editable" contenteditable="true">Start Free</button></div>',
                    Instagram: '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">' +
                        '<div style="height:72px;background:#eef4ff;border-radius:8px;"></div><div style="height:72px;background:#eef4ff;border-radius:8px;"></div><div style="height:72px;background:#eef4ff;border-radius:8px;"></div></div>',
                    YouTube: '<div style="border:1px dashed #c8d9f3;border-radius:10px;background:#f4f8ff;min-height:180px;display:grid;place-items:center;color:#5b6a80;font-weight:600;">YouTube Embed</div>',
                    Map: '<div style="border:1px dashed #c8d9f3;border-radius:10px;background:#f4f8ff;min-height:180px;display:grid;place-items:center;color:#5b6a80;font-weight:600;">Map Placeholder</div>',
                    "Custom HTML": '<pre style="background:#0f172a;color:#e2e8f0;padding:10px;border-radius:10px;overflow:auto;">&lt;section&gt;Your HTML here&lt;/section&gt;</pre>',
                    Embed: '<div style="border:1px dashed #c8d9f3;border-radius:10px;background:#f4f8ff;min-height:120px;display:grid;place-items:center;color:#5b6a80;font-weight:600;">Embed URL or code snippet</div>',
                    Divider: '<hr style="border:none;border-top:1px solid #d5e2f5;">',
                    Spacer: '<div style="height:44px"></div>'
                };
                if (map[type]) return map[type];
                return '<p class="editable" contenteditable="true"><strong>' + type + '</strong> block. Click to customize content.</p>';
            }

            function getToolbar(type) {
                const iconSets = {
                    section: [
                        ["move", "↕"],
                        ["duplicate", "⎘"],
                        ["copy", "⧉"],
                        ["paste", "⤵"],
                        ["delete", "✕"],
                        ["settings", "⚙"],
                        ["collapse", "▾"]
                    ],
                    column: [
                        ["add-widget", "+"],
                        ["duplicate", "⎘"],
                        ["delete", "✕"],
                        ["resize", "↔"],
                        ["responsive", "R"]
                    ],
                    widget: [
                        ["move", "↕"],
                        ["duplicate", "⎘"],
                        ["copy", "⧉"],
                        ["paste", "⤵"],
                        ["delete", "✕"],
                        ["visibility", "◐"],
                        ["animation", "✦"]
                    ]
                };

                const buttons = iconSets[type].map((icon) => {
                    return '<button type="button" data-action="' + icon[0] + '" title="' + icon[0] + '">' + icon[1] + '</button>';
                }).join("");

                return '<div class="node-toolbar">' + buttons + '</div>';
            }

            function runAction(action, node) {
                if (action === "duplicate") {
                    const clone = node.cloneNode(true);
                    ensureNodeId(clone);
                    node.parentNode.insertBefore(clone, node.nextSibling);
                    selectNode(clone);
                    pushHistory();
                    return;
                }
                if (action === "copy") {
                    state.clipboard = node.outerHTML;
                    showToast("Copied");
                    return;
                }
                if (action === "paste") {
                    if (!state.clipboard) return;
                    const temp = document.createElement("div");
                    temp.innerHTML = state.clipboard;
                    const clone = temp.firstElementChild;
                    if (clone) {
                        ensureNodeId(clone);
                        node.parentNode.insertBefore(clone, node.nextSibling);
                        selectNode(clone);
                        pushHistory();
                    }
                    return;
                }
                if (action === "delete") {
                    const parent = node.parentElement;
                    node.remove();
                    selectNode(null);

                    parent?.querySelectorAll(":scope > .column").forEach((col) => ensureAddWidgetButton(col));
                    syncEmptyState();
                    pushHistory();
                    return;
                }
                if (action === "add-widget") {
                    openWidgetLibrary(node);
                    return;
                }
                if (action === "resize") {
                    const current = Number(node.dataset.width || 100);
                    const next = current >= 100 ? 25 : current + 25;
                    node.dataset.width = String(next);
                    node.style.flex = "0 0 " + next + "%";
                    pushHistory();
                    showToast("Column width: " + next + "%");
                    return;
                }
                if (action === "responsive") {
                    showToast("Responsive width controls available in panel");
                    selectNode(node);
                    return;
                }
                if (action === "collapse") {
                    const cols = node.querySelector(".columns");
                    if (!cols) return;
                    cols.style.display = cols.style.display === "none" ? "flex" : "none";
                    return;
                }
                if (action === "visibility" || action === "animation" || action === "settings") {
                    selectNode(node);
                    showToast("Settings ready in right panel");
                    return;
                }
            }

            function ensureAddWidgetButton(column) {
                if (!column.querySelector(".widget") && !column.querySelector(".add-widget-btn")) {
                    column.insertAdjacentHTML("beforeend", '<button class="add-widget-btn">+ Add Widget</button>');
                }
            }

            function renderEmptyState() {
                if (pageCanvas.querySelector(".section")) return;
                pageCanvas.innerHTML = '<div class="empty-state" id="emptyState">' +
                    '<div class="empty-illustration"></div>' +
                    '<h2>Start Building Your Page</h2>' +
                    '<p>Create sections visually and compose your page in minutes.</p>' +
                    '<button class="action-btn primary" id="addSectionBtn">+ Add Section</button>' +
                    '</div>';
                document.getElementById("addSectionBtn").addEventListener("click", (e) => openSectionPicker(e.currentTarget));
            }

            function syncEmptyState() {
                const hasSection = Boolean(pageCanvas.querySelector(".section"));
                const empty = pageCanvas.querySelector("#emptyState");
                if (hasSection && empty) {
                    empty.remove();
                }
                if (!hasSection && !empty) {
                    renderEmptyState();
                }
            }

            function selectNode(node) {
                pageCanvas.querySelectorAll(".selected").forEach((el) => el.classList.remove("selected"));
                state.selected = node;
                if (node) {
                    ensureNodeId(node);
                    node.classList.add("selected");
                }
                renderPropertyPanel(node);
            }

            function setPanelIntro() {
                renderPropertyPanel(null);
            }

            function renderPropertyPanel(node) {
                if (!node) {
                    propertyPanel.innerHTML =
                        '<h3 class="panel-title">Properties</h3>' +
                        '<p class="panel-subtitle">Select a section, column, or widget to edit settings.</p>' +
                        '<div class="prop-group"><h4>Editor Tips</h4>' +
                        '<p style="color:#5b667a;font-size:13px;line-height:1.55;">Use Cmd/Ctrl+Z to undo, drag elements to reorder, and click any text to edit inline.</p>' +
                        '</div>';
                    return;
                }

                const type = node.dataset.nodeType;
                if (type === "section") {
                    propertyPanel.innerHTML =
                        '<h3 class="panel-title">Section</h3><p class="panel-subtitle">Layout and visual style</p>' +
                        buildGroup("Layout", [
                            field("Width", "select", "data-width", "full", ["boxed", "full"]),
                            field("Min Height", "input", "style-minHeight", node.style.minHeight || "0px"),
                            field("Padding", "input", "style-padding", node.style.padding || "16px"),
                            field("Margin", "input", "style-margin", node.style.margin || "0px")
                        ]) +
                        buildGroup("Style", [
                            field("Background", "input", "style-background", node.style.background || "#ffffff"),
                            field("Text Color", "input", "style-color", node.style.color || "#0f172a"),
                            field("Border Color", "input", "style-borderColor", node.style.borderColor || "#dce7f8"),
                            field("Border Radius", "input", "style-borderRadius", node.style.borderRadius || "16px"),
                            field("Shadow", "select", "style-boxShadow", node.style.boxShadow || "soft", ["none", "soft", "medium"])
                        ]) +
                        buildGroup("Effects", [
                            field("Animation", "select", "data-animation", "fade", ["none", "fade", "slide", "scale"]),
                            field("Responsive Visibility", "select", "data-visibility", "all", ["all", "desktop", "tablet", "mobile"])
                        ]) +
                        buildCustomCssGroup(node);
                } else if (type === "column") {
                    propertyPanel.innerHTML =
                        '<h3 class="panel-title">Column</h3><p class="panel-subtitle">Spacing, alignment, and responsive width</p>' +
                        buildGroup("Layout", [
                            field("Width", "input", "data-width", node.dataset.width || "50"),
                            field("Vertical Align", "select", "style-justifyContent", node.style.justifyContent || "flex-start", ["flex-start", "center", "flex-end", "space-between"]),
                            field("Horizontal Align", "select", "style-alignItems", node.style.alignItems || "stretch", ["stretch", "flex-start", "center", "flex-end"])
                        ]) +
                        buildGroup("Spacing", [
                            field("Gap", "input", "style-gap", node.style.gap || "10px"),
                            field("Padding", "input", "style-padding", node.style.padding || "12px"),
                            field("Margin", "input", "style-margin", node.style.margin || "0px")
                        ]) +
                        buildGroup("Style", [
                            field("Background", "input", "style-background", node.style.background || "#fbfdff"),
                            field("Border", "input", "style-border", node.style.border || "1px dashed #c7d8f3"),
                            field("Border Radius", "input", "style-borderRadius", node.style.borderRadius || "12px"),
                            field("Text Color", "input", "style-color", node.style.color || "#334155"),
                            field("Responsive Width", "input", "data-responsiveWidth", node.dataset.responsiveWidth || "100")
                        ]) +
                        buildCustomCssGroup(node);
                } else {
                    const widgetType = node.dataset.widgetType || "Widget";
                    propertyPanel.innerHTML =
                        '<h3 class="panel-title">' + widgetType + '</h3><p class="panel-subtitle">Context-aware settings</p>' +
                        buildWidgetSettings(widgetType, node) +
                        buildCustomCssGroup(node);
                }

                bindPropertyInputs(node);
            }

            function buildWidgetSettings(type, node) {
                const shared = buildGroup("Spacing", [
                    field("Margin", "input", "style-margin", node.style.margin || "0px"),
                    field("Padding", "input", "style-padding", node.style.padding || "12px")
                ]) + buildGroup("Animation", [
                    field("Animation", "select", "data-animation", node.dataset.animation || "none", ["none", "fade", "slide-up", "zoom"])
                ]);

                if (type === "Heading") {
                    return buildGroup("Typography", [
                        field("Size", "input", "data-font-size", "30"),
                        field("Align", "select", "style-textAlign", node.style.textAlign || "left", ["left", "center", "right"]),
                        field("Color", "input", "style-color", node.style.color || "#0f172a")
                    ]) + shared;
                }
                if (type === "Button") {
                    return buildGroup("Button", [
                        field("Label", "input", "data-label", getWidgetEditableText(node)),
                        field("Link", "input", "data-link", "#"),
                        field("Size", "select", "data-size", "md", ["sm", "md", "lg"]),
                        field("Style", "select", "data-style", "solid", ["solid", "outline", "ghost"]),
                        field("Icon", "input", "data-icon", "")
                    ]) + buildGroup("States", [
                        field("Hover Color", "input", "data-hover-color", "#1f57d4")
                    ]) + shared;
                }
                if (type === "Image") {
                    const imageEl = node.querySelector(".image-widget img");
                    const hasImage = imageEl && imageEl.getAttribute("src");
                    return buildGroup("Image", [
                        '<div class="field"><label>Upload</label><button type="button" class="action-btn" data-action="panel-image-upload">Upload Image</button><input type="file" id="panelImageUpload" accept="image/*" style="display:none;"></div>',
                        field("Image URL", "input", "data-upload", imageEl ? (imageEl.getAttribute("src") || "") : ""),
                        field("Replace", "input", "data-replace", ""),
                        field("Crop", "select", "data-crop", "fill", ["fill", "fit"]),
                        field("Alt Text", "input", "data-alt", imageEl ? (imageEl.getAttribute("alt") || "") : ""),
                        field("Border Radius", "input", "style-borderRadius", node.querySelector(".image-widget")?.style.borderRadius || "12px"),
                        field("Aspect Ratio", "input", "data-ratio", node.querySelector(".image-widget")?.dataset.ratio || "16/9"),
                        field("Lazy Load", "select", "data-lazy", "true", ["true", "false"])
                    ]) + (hasImage ? buildGroup("Preview", [
                        '<p style="font-size:12px;color:#5e6a80;line-height:1.5;">Image uploaded and ready. You can replace it anytime.</p>'
                    ]) : "") + shared;
                }
                return buildGroup("Settings", [
                    field("Alignment", "select", "style-textAlign", node.style.textAlign || "left", ["left", "center", "right"]),
                    field("Color", "input", "style-color", node.style.color || "#334155"),
                    field("Background", "input", "style-background", node.style.background || "#ffffff"),
                    field("Font Size", "input", "data-font-size", node.querySelector(".editable")?.style.fontSize || "16px"),
                    field("Border Radius", "input", "style-borderRadius", node.style.borderRadius || "12px")
                ]) + shared;
            }

            function buildCustomCssGroup(node) {
                ensureNodeId(node);
                return buildGroup("Custom CSS", [
                    field("CSS", "textarea", "data-custom-css", node.dataset.customCss || ""),
                    '<p style="font-size:12px;color:#64748b;line-height:1.5;">Applied live to this selected element only.</p>'
                ]);
            }

            function getWidgetEditableText(widget) {
                const editable = widget.querySelector(".editable");
                return editable ? editable.innerText.trim() : "";
            }

            function buildGroup(title, fields) {
                return '<section class="prop-group"><h4>' + title + '</h4>' + fields.join("") + '</section>';
            }

            function field(label, type, prop, value, options) {
                if (type === "select") {
                    const opts = (options || []).map((opt) => {
                        const selected = String(opt) === String(value) ? "selected" : "";
                        return '<option ' + selected + '>' + opt + '</option>';
                    }).join("");
                    return '<div class="field"><label>' + label + '</label><select data-prop="' + prop + '">' + opts + '</select></div>';
                }
                if (type === "textarea") {
                    return '<div class="field"><label>' + label + '</label><textarea data-prop="' + prop + '" style="min-height:96px;border:1px solid #cfdbef;border-radius:10px;padding:8px 10px;background:#fdfefe;resize:vertical;">' + escapeHtml(String(value || "")) + '</textarea></div>';
                }
                return '<div class="field"><label>' + label + '</label><input data-prop="' + prop + '" value="' + escapeHtml(String(value || "")) + '"></div>';
            }

            function bindPropertyInputs(node) {
                propertyPanel.querySelectorAll("[data-prop]").forEach((input) => {
                    input.addEventListener("input", () => {
                        applyProperty(node, input.dataset.prop, input.value);
                        pushHistoryDebounced();
                    });
                });
            }

            function applyProperty(node, prop, value) {
                if (!node) return;
                if (prop.startsWith("style-")) {
                    const styleProp = prop.replace("style-", "");
                    if (styleProp === "boxShadow") {
                        node.style.boxShadow = value === "none" ? "none" : value === "medium" ? "0 12px 28px rgba(17,38,77,0.14)" : "0 8px 16px rgba(17,38,77,0.1)";
                    } else if (styleProp === "borderRadius" && node.dataset.widgetType === "Image") {
                        const imageWidget = node.querySelector(".image-widget");
                        if (imageWidget) {
                            imageWidget.style.borderRadius = value;
                        }
                    } else {
                        node.style[styleProp] = value;
                    }
                    return;
                }
                if (prop === "data-width") {
                    node.dataset.width = value;
                    if (node.classList.contains("column")) {
                        node.style.flex = "0 0 " + value + "%";
                    }
                    return;
                }
                if (prop === "data-label") {
                    const editable = node.querySelector(".editable");
                    if (editable) editable.innerText = value;
                    return;
                }
                if (prop === "data-font-size") {
                    const editable = node.querySelector(".editable");
                    if (editable) {
                        editable.style.fontSize = normalizeSizeValue(value);
                    }
                    return;
                }
                if (prop === "data-upload") {
                    setWidgetImageFromUrl(node, value);
                    return;
                }
                if (prop === "data-alt") {
                    const img = node.querySelector(".image-widget img");
                    if (img) img.alt = value;
                    return;
                }
                if (prop === "data-ratio") {
                    const imageWidget = node.querySelector(".image-widget");
                    if (imageWidget) {
                        imageWidget.dataset.ratio = value;
                        const img = imageWidget.querySelector("img");
                        if (img) img.style.aspectRatio = value;
                    }
                    return;
                }
                if (prop === "data-size") {
                    const editableBtn = node.querySelector("button.editable");
                    if (editableBtn) {
                        if (value === "sm") {
                            editableBtn.style.padding = "8px 12px";
                            editableBtn.style.fontSize = "12px";
                        } else if (value === "lg") {
                            editableBtn.style.padding = "12px 18px";
                            editableBtn.style.fontSize = "16px";
                        } else {
                            editableBtn.style.padding = "10px 14px";
                            editableBtn.style.fontSize = "14px";
                        }
                    }
                    return;
                }
                if (prop === "data-style") {
                    const editableBtn = node.querySelector("button.editable");
                    if (editableBtn) {
                        if (value === "outline") {
                            editableBtn.style.background = "transparent";
                            editableBtn.style.border = "1px solid #2d6cf6";
                            editableBtn.style.color = "#2d6cf6";
                        } else if (value === "ghost") {
                            editableBtn.style.background = "#eaf1ff";
                            editableBtn.style.border = "none";
                            editableBtn.style.color = "#1f4db0";
                        } else {
                            editableBtn.style.background = "#2d6cf6";
                            editableBtn.style.border = "none";
                            editableBtn.style.color = "#ffffff";
                        }
                    }
                    return;
                }
                if (prop === "data-custom-css") {
                    ensureNodeId(node);
                    node.dataset.customCss = value;
                    updateCustomCssStyles();
                    return;
                }
                node.dataset[prop.replace("data-", "")] = value;
            }

            function ensureNodeId(node) {
                if (!node || !node.dataset) return;
                if (!node.dataset.nodeId) {
                    node.dataset.nodeId = "node_" + Date.now() + "_" + Math.random().toString(36).slice(2, 8);
                }
            }

            function updateCustomCssStyles() {
                let styleTag = document.getElementById("customCssRuntime");
                if (!styleTag) {
                    styleTag = document.createElement("style");
                    styleTag.id = "customCssRuntime";
                    document.head.appendChild(styleTag);
                }

                const rules = [];
                pageCanvas.querySelectorAll(".section, .column, .widget").forEach((node) => {
                    ensureNodeId(node);
                    const css = (node.dataset.customCss || "").trim();
                    if (!css) return;
                    const safeCss = css.replaceAll("<", "").replaceAll(">", "");
                    rules.push('[data-node-id="' + node.dataset.nodeId + '"]{' + safeCss + '}');
                });
                styleTag.textContent = rules.join("\n");
            }

            function normalizeSizeValue(value) {
                const v = String(value || "").trim();
                if (!v) return "16px";
                if (/^\d+$/.test(v)) return v + "px";
                return v;
            }

            async function setWidgetImageFromFile(widget, file) {
                showToast("Uploading image...");
                try {
                    const uploadedUrl = await uploadImageToServer(file);
                    setWidgetImageFromUrl(widget, uploadedUrl);
                    pushHistoryDebounced();
                    if (state.selected === widget) {
                        renderPropertyPanel(widget);
                    }
                    showToast("Image uploaded");
                } catch (error) {
                    const localPreview = await readFileAsDataUrl(file);
                    setWidgetImageFromUrl(widget, localPreview);
                    pushHistoryDebounced();
                    if (state.selected === widget) {
                        renderPropertyPanel(widget);
                    }
                    showToast("Upload failed. Using local preview.");
                }
            }

            async function uploadImageToServer(file) {
                const endpoints = [
                    "/api/builder/upload.php",
                    "/api/singleUpload.php",
                    "/api/singleFileUpload.php"
                ];

                let lastError = null;

                for (const endpoint of endpoints) {
                    try {
                        const formData = new FormData();
                        formData.append("upload", file);

                        const response = await fetch(endpoint, {
                            method: "POST",
                            body: formData,
                            credentials: "same-origin"
                        });

                        const payload = await parseJsonSafe(response);
                        const url = extractUploadedUrl(payload);
                        if (response.ok && url) {
                            return normalizeUploadedUrl(url);
                        }

                        lastError = new Error("Upload endpoint responded without URL: " + endpoint);
                    } catch (error) {
                        lastError = error;
                    }
                }

                throw lastError || new Error("Image upload failed");
            }

            async function parseJsonSafe(response) {
                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (error) {
                    return {
                        raw: text
                    };
                }
            }

            function extractUploadedUrl(payload) {
                if (!payload || typeof payload !== "object") return "";
                if (typeof payload.url === "string" && payload.url.trim()) return payload.url.trim();
                if (typeof payload.msg === "string" && payload.msg.trim()) {
                    const msg = payload.msg.trim();
                    if (msg.startsWith("/upload/") || msg.startsWith("upload/") || msg.startsWith("http://") || msg.startsWith("https://") || msg.startsWith("/")) {
                        return msg;
                    }
                }
                return "";
            }

            function normalizeUploadedUrl(url) {
                const clean = String(url || "").trim();
                if (!clean) return "";
                if (clean.startsWith("http://") || clean.startsWith("https://") || clean.startsWith("data:")) {
                    return clean;
                }
                return clean.startsWith("/") ? clean : "/" + clean;
            }

            function readFileAsDataUrl(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(String(reader.result || ""));
                    reader.onerror = () => reject(new Error("File preview failed"));
                    reader.readAsDataURL(file);
                });
            }

            function setWidgetImageFromUrl(widget, url) {
                const imageWidget = widget.querySelector(".image-widget");
                if (!imageWidget) return;
                const img = imageWidget.querySelector("img");
                const placeholder = imageWidget.querySelector(".image-placeholder");

                if (!url || !String(url).trim()) {
                    if (img) {
                        img.removeAttribute("src");
                        img.style.display = "none";
                    }
                    if (placeholder) placeholder.style.display = "grid";
                    return;
                }

                if (img) {
                    img.src = String(url).trim();
                    img.style.display = "block";
                }
                if (placeholder) placeholder.style.display = "none";
            }

            function onDragStart(e) {
                const draggable = e.target.closest(".section, .column, .widget");
                if (!draggable) return;
                state.draggingEl = draggable;
                draggable.style.opacity = "0.55";
                e.dataTransfer.effectAllowed = "move";
                e.dataTransfer.setData("text/plain", draggable.dataset.nodeType || "node");
            }

            function onDragOver(e) {
                if (!state.draggingEl) return;
                e.preventDefault();

                const target = e.target.closest(".section, .column, .widget");
                if (!target || target === state.draggingEl) return;

                const sameType = target.dataset.nodeType === state.draggingEl.dataset.nodeType;
                if (!sameType) return;

                state.dropTarget = target;
                const rect = target.getBoundingClientRect();
                dropIndicator.classList.add("show");
                dropIndicator.style.width = rect.width + "px";
                dropIndicator.style.left = rect.left + "px";
                dropIndicator.style.top = rect.top - 3 + "px";

                const wrapRect = canvasWrap.getBoundingClientRect();
                if (e.clientY > wrapRect.bottom - 50) {
                    canvasWrap.scrollTop += 18;
                } else if (e.clientY < wrapRect.top + 50) {
                    canvasWrap.scrollTop -= 18;
                }
            }

            function onDrop(e) {
                if (!state.draggingEl || !state.dropTarget) return;
                e.preventDefault();
                const target = state.dropTarget;
                if (target === state.draggingEl) return;

                const rect = target.getBoundingClientRect();
                const before = e.clientY < rect.top + rect.height / 2;
                target.parentNode.insertBefore(state.draggingEl, before ? target : target.nextSibling);
                pushHistory();
                showToast("Moved");
            }

            function onDragEnd() {
                if (state.draggingEl) {
                    state.draggingEl.style.opacity = "";
                }
                state.draggingEl = null;
                state.dropTarget = null;
                dropIndicator.classList.remove("show");
            }

            function saveNow() {
                localStorage.setItem(state.autoSaveKey, pageCanvas.innerHTML);
                showToast("Saved");
            }

            function restoreAutosave() {
                const saved = localStorage.getItem(state.autoSaveKey);
                if (saved) {
                    pageCanvas.innerHTML = saved;
                    syncEmptyState();
                    updateCustomCssStyles();
                    showToast("Autosave restored");
                }
            }

            let historyTimer = null;

            function pushHistoryDebounced() {
                clearTimeout(historyTimer);
                historyTimer = setTimeout(() => pushHistory(), 250);
            }

            function pushHistory() {
                const snapshot = pageCanvas.innerHTML;
                if (state.history[state.historyIndex] === snapshot) return;
                state.history = state.history.slice(0, state.historyIndex + 1);
                state.history.push(snapshot);
                state.historyIndex = state.history.length - 1;
            }

            function undo() {
                if (state.historyIndex <= 0) return;
                state.historyIndex -= 1;
                pageCanvas.innerHTML = state.history[state.historyIndex];
                syncEmptyState();
                updateCustomCssStyles();
                selectNode(null);
                showToast("Undo");
            }

            function redo() {
                if (state.historyIndex >= state.history.length - 1) return;
                state.historyIndex += 1;
                pageCanvas.innerHTML = state.history[state.historyIndex];
                syncEmptyState();
                updateCustomCssStyles();
                selectNode(null);
                showToast("Redo");
            }

            function showToast(text) {
                toast.innerText = text;
                toast.classList.add("show");
                clearTimeout(showToast._timer);
                showToast._timer = setTimeout(() => toast.classList.remove("show"), 1600);
            }

            function escapeHtml(value) {
                return value
                    .replaceAll("&", "&amp;")
                    .replaceAll("<", "&lt;")
                    .replaceAll(">", "&gt;")
                    .replaceAll('"', "&quot;")
                    .replaceAll("'", "&#39;");
            }
        })();
    </script>
</body>

</html>