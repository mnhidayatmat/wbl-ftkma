@extends('layouts.app')

@section('title', 'SAL Template Designer')

@push('styles')
<style>
    /* Hide default layout padding for full-screen editor */
    .word-editor-wrapper {
        margin: -1.5rem;
        height: calc(100vh - 64px);
        display: flex;
        flex-direction: column;
        background: #f3f3f3;
    }

    /* Menu Bar */
    .menu-bar {
        background: #fff;
        border-bottom: 1px solid #d1d1d1;
        display: flex;
        align-items: center;
        padding: 0 8px;
        height: 32px;
        gap: 4px;
    }

    .menu-item {
        padding: 4px 12px;
        font-size: 13px;
        color: #333;
        cursor: pointer;
        border-radius: 4px;
        transition: background 0.15s;
    }

    .menu-item:hover {
        background: #e5e5e5;
    }

    .menu-item.active {
        background: #e1e1e1;
    }

    /* Ribbon Tabs */
    .ribbon-tabs {
        background: #fff;
        display: flex;
        padding: 0 8px;
        border-bottom: 1px solid #d1d1d1;
    }

    .ribbon-tab {
        padding: 8px 16px;
        font-size: 12px;
        color: #444;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.15s;
        font-weight: 500;
    }

    .ribbon-tab:hover {
        background: #f5f5f5;
    }

    .ribbon-tab.active {
        color: #0078d4;
        border-bottom-color: #0078d4;
    }

    /* Ribbon Content */
    .ribbon-content {
        background: #f8f8f8;
        border-bottom: 1px solid #d1d1d1;
        min-height: 90px;
        display: flex;
        align-items: flex-start;
        padding: 8px 12px;
        gap: 16px;
    }

    .ribbon-group {
        display: flex;
        flex-direction: column;
        padding: 0 12px;
        border-right: 1px solid #e0e0e0;
    }

    .ribbon-group:last-child {
        border-right: none;
    }

    .ribbon-group-label {
        font-size: 10px;
        color: #666;
        text-align: center;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .ribbon-group-content {
        display: flex;
        align-items: center;
        gap: 4px;
        flex: 1;
    }

    /* Ribbon Buttons */
    .ribbon-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border: none;
        background: transparent;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.15s;
        min-width: 50px;
    }

    .ribbon-btn:hover {
        background: #e5e5e5;
    }

    .ribbon-btn.active {
        background: #cce4f7;
    }

    .ribbon-btn svg {
        width: 20px;
        height: 20px;
        color: #444;
    }

    .ribbon-btn-label {
        font-size: 10px;
        color: #444;
        margin-top: 2px;
    }

    .ribbon-btn-small {
        width: 28px;
        height: 28px;
        min-width: unset;
        padding: 4px;
    }

    .ribbon-btn-small svg {
        width: 16px;
        height: 16px;
    }

    /* Font Controls */
    .font-select {
        height: 26px;
        border: 1px solid #c0c0c0;
        border-radius: 3px;
        font-size: 12px;
        padding: 0 8px;
        background: #fff;
        min-width: 130px;
    }

    .font-size-select {
        width: 55px;
        height: 26px;
        border: 1px solid #c0c0c0;
        border-radius: 3px;
        font-size: 12px;
        padding: 0 4px;
        text-align: center;
        background: #fff;
    }

    /* Format Buttons Row */
    .format-row {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .format-btn {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        background: transparent;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.1s;
    }

    .format-btn:hover {
        background: #e0e0e0;
        border-color: #c0c0c0;
    }

    .format-btn.active {
        background: #cce4f7;
        border-color: #0078d4;
    }

    .format-btn svg {
        width: 14px;
        height: 14px;
        color: #333;
    }

    .color-picker-btn {
        position: relative;
    }

    .color-indicator {
        position: absolute;
        bottom: 3px;
        left: 5px;
        right: 5px;
        height: 3px;
        background: #000;
        border-radius: 1px;
    }

    /* Document Area */
    .document-area {
        flex: 1;
        overflow: auto;
        padding: 20px 40px;
        display: flex;
        justify-content: center;
    }

    /* Page */
    .page {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        width: 210mm;
        min-height: 297mm;
        padding: 25mm;
        position: relative;
    }

    /* Editable Content */
    .editable-content {
        min-height: 100%;
        outline: none;
        font-family: 'Times New Roman', serif;
        font-size: 12pt;
        line-height: 1.6;
    }

    .editable-content:focus {
        outline: none;
    }

    /* Status Bar */
    .status-bar {
        background: #f0f0f0;
        border-top: 1px solid #d1d1d1;
        height: 24px;
        display: flex;
        align-items: center;
        padding: 0 12px;
        font-size: 11px;
        color: #666;
        gap: 20px;
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Right Panel - Variables */
    .variables-panel {
        width: 260px;
        background: #fff;
        border-left: 1px solid #d1d1d1;
        display: flex;
        flex-direction: column;
    }

    .panel-header {
        padding: 12px 16px;
        font-weight: 600;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #e0e0e0;
        background: #fafafa;
    }

    .panel-content {
        padding: 12px;
        overflow-y: auto;
        flex: 1;
    }

    .variable-category {
        margin-bottom: 16px;
    }

    .variable-category-title {
        font-size: 11px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .variable-item {
        display: flex;
        align-items: center;
        padding: 8px 10px;
        background: #f5f5f5;
        border-radius: 4px;
        margin-bottom: 6px;
        cursor: pointer;
        transition: all 0.15s;
        font-size: 12px;
    }

    .variable-item:hover {
        background: #e8f4fd;
        border-color: #0078d4;
    }

    .variable-item code {
        font-family: 'Consolas', monospace;
        font-size: 11px;
        color: #0078d4;
        background: #fff;
        padding: 2px 6px;
        border-radius: 3px;
        border: 1px solid #e0e0e0;
    }

    .variable-item-desc {
        font-size: 10px;
        color: #888;
        margin-left: 8px;
    }

    /* Main Container */
    .editor-main {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* Dropdown Menu */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: #fff;
        border: 1px solid #d1d1d1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 4px;
        padding: 4px 0;
        min-width: 180px;
        display: none;
        z-index: 1000;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        padding: 8px 16px;
        font-size: 13px;
        color: #333;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dropdown-item:hover {
        background: #f5f5f5;
    }

    .dropdown-item svg {
        width: 16px;
        height: 16px;
        color: #666;
    }

    .dropdown-divider {
        height: 1px;
        background: #e0e0e0;
        margin: 4px 0;
    }

    .dropdown-shortcut {
        margin-left: auto;
        font-size: 11px;
        color: #888;
    }

    /* Insert Element Styles */
    .inserted-logo {
        max-width: 200px;
        display: block;
        margin-bottom: 20px;
    }

    .letterhead {
        text-align: center;
        margin-bottom: 30px;
    }

    .letterhead-title {
        font-size: 16pt;
        font-weight: bold;
        color: #003366;
    }

    .letterhead-subtitle {
        font-size: 11pt;
        color: #666;
    }

    .signature-block {
        margin-top: 50px;
    }

    .signature-line {
        width: 200px;
        border-bottom: 1px solid #000;
        margin-top: 60px;
        margin-bottom: 5px;
    }

    /* Save indicator */
    .save-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
        padding-right: 12px;
    }

    .save-indicator.saving {
        color: #f59e0b;
    }

    .save-indicator.saved {
        color: #10b981;
    }

    .save-indicator.error {
        color: #ef4444;
    }

    /* Toast notification */
    .toast {
        position: fixed;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: #fff;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 13px;
        opacity: 0;
        transition: opacity 0.3s;
        z-index: 9999;
    }

    .toast.show {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="word-editor-wrapper">
    <!-- Menu Bar -->
    <div class="menu-bar">
        <a href="{{ route('admin.documents.sal') }}" class="menu-item" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
        <div style="width: 1px; height: 16px; background: #d1d1d1; margin: 0 8px;"></div>
        <div class="menu-item" onclick="toggleDropdown('fileMenu')" style="position: relative;">
            File
            <div id="fileMenu" class="dropdown-menu">
                <div class="dropdown-item" onclick="saveTemplate()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Save
                    <span class="dropdown-shortcut">Ctrl+S</span>
                </div>
                <div class="dropdown-item" onclick="window.open('{{ route('admin.documents.sal.preview') }}', '_blank')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview PDF
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="resetTemplate()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset to Default
                </div>
            </div>
        </div>
        <div class="menu-item" onclick="toggleDropdown('editMenu')" style="position: relative;">
            Edit
            <div id="editMenu" class="dropdown-menu">
                <div class="dropdown-item" onclick="document.execCommand('undo')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Undo
                    <span class="dropdown-shortcut">Ctrl+Z</span>
                </div>
                <div class="dropdown-item" onclick="document.execCommand('redo')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
                    Redo
                    <span class="dropdown-shortcut">Ctrl+Y</span>
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="document.execCommand('selectAll')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Select All
                    <span class="dropdown-shortcut">Ctrl+A</span>
                </div>
            </div>
        </div>
        <div class="menu-item" onclick="toggleDropdown('insertMenu')" style="position: relative;">
            Insert
            <div id="insertMenu" class="dropdown-menu">
                <div class="dropdown-item" onclick="insertLogoPlaceholder()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    University Logo
                </div>
                <div class="dropdown-item" onclick="insertLetterhead()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Letterhead
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="insertSignatureBlock()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Signature Block
                </div>
                <div class="dropdown-item" onclick="insertDate()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Date Field
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="insertPageBreak()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Page Break
                </div>
            </div>
        </div>
        <div class="menu-item" onclick="toggleDropdown('formatMenu')" style="position: relative;">
            Format
            <div id="formatMenu" class="dropdown-menu">
                <div class="dropdown-item" onclick="document.execCommand('bold')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                    Bold
                    <span class="dropdown-shortcut">Ctrl+B</span>
                </div>
                <div class="dropdown-item" onclick="document.execCommand('italic')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-2 0v16m-4 0h8"/></svg>
                    Italic
                    <span class="dropdown-shortcut">Ctrl+I</span>
                </div>
                <div class="dropdown-item" onclick="document.execCommand('underline')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v7a5 5 0 0010 0V4M5 20h14"/></svg>
                    Underline
                    <span class="dropdown-shortcut">Ctrl+U</span>
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="document.execCommand('justifyLeft')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
                    Align Left
                </div>
                <div class="dropdown-item" onclick="document.execCommand('justifyCenter')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
                    Align Center
                </div>
                <div class="dropdown-item" onclick="document.execCommand('justifyRight')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"/></svg>
                    Align Right
                </div>
            </div>
        </div>

        <!-- Save Indicator -->
        <div class="save-indicator saved" id="saveIndicator">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span id="saveStatus">Saved</span>
        </div>
    </div>

    <!-- Ribbon Tabs -->
    <div class="ribbon-tabs">
        <div class="ribbon-tab active" data-tab="home">Home</div>
        <div class="ribbon-tab" data-tab="insert">Insert</div>
        <div class="ribbon-tab" data-tab="layout">Layout</div>
    </div>

    <!-- Ribbon Content - Home -->
    <div class="ribbon-content" id="ribbon-home">
        <!-- Clipboard Group -->
        <div class="ribbon-group">
            <div class="ribbon-group-content">
                <button class="ribbon-btn" onclick="pasteFromClipboard()" title="Paste (Ctrl+V)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="ribbon-btn-label">Paste</span>
                </button>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <button class="ribbon-btn-small format-btn" onclick="cutToClipboard()" title="Cut (Ctrl+X)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    </button>
                    <button class="ribbon-btn-small format-btn" onclick="copyToClipboard()" title="Copy (Ctrl+C)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
            </div>
            <div class="ribbon-group-label">Clipboard</div>
        </div>

        <!-- Font Group -->
        <div class="ribbon-group">
            <div class="ribbon-group-content" style="flex-direction: column; gap: 4px; align-items: flex-start;">
                <div style="display: flex; gap: 4px;">
                    <select class="font-select" id="fontFamily" onchange="applyFont(this.value)">
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Arial">Arial</option>
                        <option value="Calibri">Calibri</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                        <option value="Courier New">Courier New</option>
                    </select>
                    <select class="font-size-select" id="fontSize" onchange="applyFontSize(this.value)">
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12" selected>12</option>
                        <option value="14">14</option>
                        <option value="16">16</option>
                        <option value="18">18</option>
                        <option value="20">20</option>
                        <option value="24">24</option>
                        <option value="28">28</option>
                        <option value="36">36</option>
                    </select>
                </div>
                <div class="format-row">
                    <button class="format-btn" onclick="document.execCommand('bold')" title="Bold (Ctrl+B)" id="btnBold">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6zm0 8h9a4 4 0 014 4 4 4 0 01-4 4H6z" stroke="currentColor" stroke-width="2"/></svg>
                    </button>
                    <button class="format-btn" onclick="document.execCommand('italic')" title="Italic (Ctrl+I)" id="btnItalic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="4" x2="14" y2="4"/><line x1="12" y1="4" x2="10" y2="20"/><line x1="8" y1="20" x2="12" y2="20"/></svg>
                    </button>
                    <button class="format-btn" onclick="document.execCommand('underline')" title="Underline (Ctrl+U)" id="btnUnderline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 4v7a5 5 0 0010 0V4"/><line x1="5" y1="20" x2="19" y2="20"/></svg>
                    </button>
                    <button class="format-btn" onclick="document.execCommand('strikethrough')" title="Strikethrough">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/><path d="M17.5 7.5c0-2-1.5-3.5-5.5-3.5s-5.5 1.5-5.5 3.5c0 4 11 4 11 8 0 2-1.5 3.5-5.5 3.5s-5.5-1.5-5.5-3.5"/></svg>
                    </button>
                    <div style="width: 1px; height: 20px; background: #d0d0d0; margin: 0 4px;"></div>
                    <button class="format-btn color-picker-btn" onclick="document.getElementById('textColorPicker').click()" title="Font Color">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16M6 16l6-12 6 12M8 12h8"/></svg>
                        <div class="color-indicator" id="textColorIndicator"></div>
                        <input type="color" id="textColorPicker" style="display:none;" onchange="applyTextColor(this.value)">
                    </button>
                    <button class="format-btn color-picker-btn" onclick="document.getElementById('highlightPicker').click()" title="Highlight">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <div class="color-indicator" id="highlightIndicator" style="background: yellow;"></div>
                        <input type="color" id="highlightPicker" value="#ffff00" style="display:none;" onchange="applyHighlight(this.value)">
                    </button>
                </div>
            </div>
            <div class="ribbon-group-label">Font</div>
        </div>

        <!-- Paragraph Group -->
        <div class="ribbon-group">
            <div class="ribbon-group-content" style="flex-direction: column; gap: 4px; align-items: flex-start;">
                <div class="format-row">
                    <button class="format-btn" onclick="insertList('unordered')" title="Bullets">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1.5" fill="currentColor"/><circle cx="4" cy="12" r="1.5" fill="currentColor"/><circle cx="4" cy="18" r="1.5" fill="currentColor"/></svg>
                    </button>
                    <button class="format-btn" onclick="insertList('ordered')" title="Numbering">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="20" y2="6"/><line x1="10" y1="12" x2="20" y2="12"/><line x1="10" y1="18" x2="20" y2="18"/><text x="4" y="8" font-size="8" fill="currentColor">1.</text><text x="4" y="14" font-size="8" fill="currentColor">2.</text><text x="4" y="20" font-size="8" fill="currentColor">3.</text></svg>
                    </button>
                    <div style="width: 1px; height: 20px; background: #d0d0d0; margin: 0 4px;"></div>
                    <button class="format-btn" onclick="changeIndent('outdent')" title="Decrease Indent">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 6h9M11 12h9M11 18h9M3 12l4-4v8l-4-4z"/></svg>
                    </button>
                    <button class="format-btn" onclick="changeIndent('indent')" title="Increase Indent">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 6h9M11 12h9M11 18h9M7 12l-4-4v8l4-4z"/></svg>
                    </button>
                </div>
                <div class="format-row">
                    <button class="format-btn" onclick="setAlignment('justifyLeft')" title="Align Left" id="btnAlignLeft">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>
                    </button>
                    <button class="format-btn" onclick="setAlignment('justifyCenter')" title="Align Center" id="btnAlignCenter">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                    </button>
                    <button class="format-btn" onclick="setAlignment('justifyRight')" title="Align Right" id="btnAlignRight">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <button class="format-btn" onclick="setAlignment('justifyFull')" title="Justify" id="btnJustify">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                </div>
            </div>
            <div class="ribbon-group-label">Paragraph</div>
        </div>

        <!-- Styles Group -->
        <div class="ribbon-group">
            <div class="ribbon-group-content">
                <button class="ribbon-btn" onclick="applyHeading('h1')" title="Heading 1">
                    <span style="font-size: 16px; font-weight: bold;">H1</span>
                    <span class="ribbon-btn-label">Heading 1</span>
                </button>
                <button class="ribbon-btn" onclick="applyHeading('h2')" title="Heading 2">
                    <span style="font-size: 14px; font-weight: bold;">H2</span>
                    <span class="ribbon-btn-label">Heading 2</span>
                </button>
                <button class="ribbon-btn" onclick="applyHeading('p')" title="Normal">
                    <span style="font-size: 12px;">P</span>
                    <span class="ribbon-btn-label">Normal</span>
                </button>
            </div>
            <div class="ribbon-group-label">Styles</div>
        </div>
    </div>

    <!-- Ribbon Content - Insert (Hidden by default) -->
    <div class="ribbon-content" id="ribbon-insert" style="display: none;">
        <div class="ribbon-group">
            <div class="ribbon-group-content">
                <button class="ribbon-btn" onclick="insertLogoPlaceholder()" title="Insert Logo">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="ribbon-btn-label">Logo</span>
                </button>
                <button class="ribbon-btn" onclick="insertLetterhead()" title="Insert Letterhead">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="ribbon-btn-label">Letterhead</span>
                </button>
            </div>
            <div class="ribbon-group-label">Header</div>
        </div>

        <div class="ribbon-group">
            <div class="ribbon-group-content">
                <button class="ribbon-btn" onclick="insertDate()" title="Insert Date">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="ribbon-btn-label">Date</span>
                </button>
                <button class="ribbon-btn" onclick="insertSignatureBlock()" title="Insert Signature">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span class="ribbon-btn-label">Signature</span>
                </button>
            </div>
            <div class="ribbon-group-label">Elements</div>
        </div>

        <div class="ribbon-group">
            <div class="ribbon-group-content">
                <button class="ribbon-btn" onclick="insertHorizontalLine()" title="Horizontal Line">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12" stroke-width="2"/></svg>
                    <span class="ribbon-btn-label">Line</span>
                </button>
                <button class="ribbon-btn" onclick="insertPageBreak()" title="Page Break">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="ribbon-btn-label">Page Break</span>
                </button>
            </div>
            <div class="ribbon-group-label">Breaks</div>
        </div>
    </div>

    <!-- Ribbon Content - Layout (Hidden by default) -->
    <div class="ribbon-content" id="ribbon-layout" style="display: none;">
        <div class="ribbon-group">
            <div class="ribbon-group-content" style="flex-direction: column; gap: 4px; align-items: flex-start;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; color: #666; width: 50px;">Top:</span>
                    <input type="number" value="25" min="0" max="100" style="width: 50px; height: 24px; border: 1px solid #c0c0c0; border-radius: 3px; padding: 0 4px; font-size: 11px;" id="marginTop"> mm
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; color: #666; width: 50px;">Bottom:</span>
                    <input type="number" value="25" min="0" max="100" style="width: 50px; height: 24px; border: 1px solid #c0c0c0; border-radius: 3px; padding: 0 4px; font-size: 11px;" id="marginBottom"> mm
                </div>
            </div>
            <div class="ribbon-group-label">Margins</div>
        </div>
        <div class="ribbon-group">
            <div class="ribbon-group-content" style="flex-direction: column; gap: 4px; align-items: flex-start;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; color: #666; width: 50px;">Left:</span>
                    <input type="number" value="25" min="0" max="100" style="width: 50px; height: 24px; border: 1px solid #c0c0c0; border-radius: 3px; padding: 0 4px; font-size: 11px;" id="marginLeft"> mm
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; color: #666; width: 50px;">Right:</span>
                    <input type="number" value="25" min="0" max="100" style="width: 50px; height: 24px; border: 1px solid #c0c0c0; border-radius: 3px; padding: 0 4px; font-size: 11px;" id="marginRight"> mm
                </div>
            </div>
            <div class="ribbon-group-label">&nbsp;</div>
        </div>
        <div class="ribbon-group">
            <div class="ribbon-group-content">
                <button class="ribbon-btn active" onclick="setOrientation('portrait')" id="btnPortrait">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24"><rect x="5" y="2" width="14" height="20" rx="1" stroke-width="2"/></svg>
                    <span class="ribbon-btn-label">Portrait</span>
                </button>
                <button class="ribbon-btn" onclick="setOrientation('landscape')" id="btnLandscape">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24"><rect x="2" y="5" width="20" height="14" rx="1" stroke-width="2"/></svg>
                    <span class="ribbon-btn-label">Landscape</span>
                </button>
            </div>
            <div class="ribbon-group-label">Orientation</div>
        </div>
    </div>

    <!-- Main Editor Area -->
    <div class="editor-main">
        <!-- Document Area -->
        <div class="document-area">
            <div class="page" id="documentPage">
                <div class="editable-content" contenteditable="true" id="editor" spellcheck="true">
                    {!! $template->body_content ?? '<p>Start typing your document here...</p>' !!}
                </div>
            </div>
        </div>

        <!-- Variables Panel -->
        <div class="variables-panel">
            <div class="panel-header">
                Template Variables
            </div>
            <div class="panel-content">
                <p style="font-size: 11px; color: #888; margin-bottom: 12px;">Click a variable to insert it at cursor position</p>

                <div class="variable-category">
                    <div class="variable-category-title">Student Information</div>
                    @foreach($variables as $var => $desc)
                        @if(str_contains($var, 'student'))
                        <div class="variable-item" onclick="insertVariable('{{ $var }}')">
                            <code>{{ $var }}</code>
                            <span class="variable-item-desc">{{ $desc }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>

                <div class="variable-category">
                    <div class="variable-category-title">Other Variables</div>
                    @foreach($variables as $var => $desc)
                        @if(!str_contains($var, 'student'))
                        <div class="variable-item" onclick="insertVariable('{{ $var }}')">
                            <code>{{ $var }}</code>
                            <span class="variable-item-desc">{{ $desc }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="status-bar">
        <div class="status-item">
            <span id="wordCount">Words: 0</span>
        </div>
        <div class="status-item">
            <span id="charCount">Characters: 0</span>
        </div>
        <div class="status-item" style="margin-left: auto;">
            Page 1 of 1
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
    const editor = document.getElementById('editor');
    let isDirty = false;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateWordCount();

        // Track changes
        editor.addEventListener('input', function() {
            isDirty = true;
            updateSaveIndicator('unsaved');
            updateWordCount();
        });

        // Auto-save every 30 seconds if dirty
        setInterval(function() {
            if (isDirty) {
                saveTemplate();
            }
        }, 30000);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key.toLowerCase()) {
                    case 's':
                        e.preventDefault();
                        saveTemplate();
                        break;
                }
            }
        });

        // Update format buttons on selection change
        document.addEventListener('selectionchange', updateFormatButtons);
    });

    // Ribbon Tab Switching
    document.querySelectorAll('.ribbon-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.ribbon-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.ribbon-content').forEach(c => c.style.display = 'none');

            this.classList.add('active');
            document.getElementById('ribbon-' + this.dataset.tab).style.display = 'flex';
        });
    });

    // Dropdown Menus
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const wasOpen = dropdown.classList.contains('show');

        // Close all dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.remove('show'));

        if (!wasOpen) {
            dropdown.classList.add('show');
        }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.menu-item')) {
            document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.remove('show'));
        }
    });

    // List and Indent Functions
    function insertList(type) {
        editor.focus();
        if (type === 'ordered') {
            document.execCommand('insertOrderedList', false, null);
        } else {
            document.execCommand('insertUnorderedList', false, null);
        }
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    function changeIndent(direction) {
        editor.focus();
        document.execCommand(direction, false, null);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    // Alignment Function
    function setAlignment(alignment) {
        editor.focus();
        document.execCommand(alignment, false, null);
        isDirty = true;
        updateSaveIndicator('unsaved');

        // Update button states
        document.getElementById('btnAlignLeft').classList.toggle('active', alignment === 'justifyLeft');
        document.getElementById('btnAlignCenter').classList.toggle('active', alignment === 'justifyCenter');
        document.getElementById('btnAlignRight').classList.toggle('active', alignment === 'justifyRight');
        document.getElementById('btnJustify').classList.toggle('active', alignment === 'justifyFull');
    }

    // Clipboard Functions
    async function pasteFromClipboard() {
        try {
            const text = await navigator.clipboard.readText();
            editor.focus();
            document.execCommand('insertText', false, text);
            isDirty = true;
            updateSaveIndicator('unsaved');
        } catch (err) {
            // Fallback - prompt user to use Ctrl+V
            showToast('Please use Ctrl+V to paste', 'info');
            editor.focus();
        }
    }

    async function copyToClipboard() {
        const selection = window.getSelection();
        if (selection.toString()) {
            try {
                await navigator.clipboard.writeText(selection.toString());
                showToast('Copied to clipboard');
            } catch (err) {
                document.execCommand('copy');
            }
        } else {
            showToast('Please select text to copy', 'info');
        }
    }

    async function cutToClipboard() {
        const selection = window.getSelection();
        if (selection.toString()) {
            try {
                await navigator.clipboard.writeText(selection.toString());
                document.execCommand('delete');
                isDirty = true;
                updateSaveIndicator('unsaved');
                showToast('Cut to clipboard');
            } catch (err) {
                document.execCommand('cut');
            }
        } else {
            showToast('Please select text to cut', 'info');
        }
    }

    // Font Functions
    function applyFont(font) {
        document.execCommand('fontName', false, font);
        editor.focus();
    }

    function applyFontSize(size) {
        // execCommand fontSize only accepts 1-7, so we use CSS
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const span = document.createElement('span');
            span.style.fontSize = size + 'pt';
            range.surroundContents(span);
        }
        editor.focus();
    }

    function applyTextColor(color) {
        document.execCommand('foreColor', false, color);
        document.getElementById('textColorIndicator').style.background = color;
        editor.focus();
    }

    function applyHighlight(color) {
        document.execCommand('hiliteColor', false, color);
        document.getElementById('highlightIndicator').style.background = color;
        editor.focus();
    }

    function applyHeading(tag) {
        document.execCommand('formatBlock', false, tag);
        editor.focus();
    }

    // Insert Functions
    function insertVariable(variable) {
        editor.focus();
        document.execCommand('insertText', false, variable);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    function insertLogoPlaceholder() {
        const html = `<div style="text-align: center; margin-bottom: 20px;">
            <img src="/images/umpsa-logo.png" alt="UMPSA Logo" style="max-width: 150px; height: auto;" onerror="this.style.display='none'">
        </div>`;
        document.execCommand('insertHTML', false, html);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    function insertLetterhead() {
        const html = `<div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 16pt; font-weight: bold; color: #003366;">UNIVERSITI MALAYSIA PAHANG AL-SULTAN ABDULLAH</div>
            <div style="font-size: 11pt; color: #666;">Faculty of Manufacturing and Mechatronic Engineering Technology</div>
            <div style="font-size: 10pt; color: #888; margin-top: 5px;">26600 Pekan, Pahang Darul Makmur, Malaysia</div>
        </div>`;
        document.execCommand('insertHTML', false, html);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    function insertSignatureBlock() {
        const html = `<div style="margin-top: 50px;">
            <p>Yours sincerely,</p>
            <div style="margin-top: 60px;">
                <div style="width: 200px; border-bottom: 1px solid #000;"></div>
                <p style="margin-top: 5px;"><strong>@{{signatory_name}}</strong></p>
                <p style="margin: 0; color: #666;">WBL Coordinator</p>
                <p style="margin: 0; color: #666;">Faculty of Manufacturing and Mechatronic Engineering Technology</p>
            </div>
        </div>`;
        document.execCommand('insertHTML', false, html);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    function insertDate() {
        const html = `<p style="text-align: right;">@{{current_date}}</p>`;
        document.execCommand('insertHTML', false, html);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    function insertHorizontalLine() {
        document.execCommand('insertHorizontalRule', false, null);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    function insertPageBreak() {
        const html = `<div style="page-break-after: always; border-bottom: 2px dashed #ccc; margin: 20px 0; text-align: center; color: #999; font-size: 10px;">--- Page Break ---</div>`;
        document.execCommand('insertHTML', false, html);
        isDirty = true;
        updateSaveIndicator('unsaved');
    }

    // Update format buttons state
    function updateFormatButtons() {
        document.getElementById('btnBold').classList.toggle('active', document.queryCommandState('bold'));
        document.getElementById('btnItalic').classList.toggle('active', document.queryCommandState('italic'));
        document.getElementById('btnUnderline').classList.toggle('active', document.queryCommandState('underline'));
    }

    // Word/Character Count
    function updateWordCount() {
        const text = editor.innerText || '';
        const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
        const chars = text.length;

        document.getElementById('wordCount').textContent = 'Words: ' + words;
        document.getElementById('charCount').textContent = 'Characters: ' + chars;
    }

    // Save Functions
    function updateSaveIndicator(status) {
        const indicator = document.getElementById('saveIndicator');
        const statusText = document.getElementById('saveStatus');

        indicator.classList.remove('saving', 'saved', 'error');

        switch(status) {
            case 'saving':
                indicator.classList.add('saving');
                statusText.textContent = 'Saving...';
                indicator.innerHTML = `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="animate-spin"><circle cx="12" cy="12" r="10" stroke-width="2" stroke-dasharray="32" stroke-dashoffset="8"/></svg><span id="saveStatus">Saving...</span>`;
                break;
            case 'saved':
                indicator.classList.add('saved');
                indicator.innerHTML = `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span id="saveStatus">Saved</span>`;
                break;
            case 'error':
                indicator.classList.add('error');
                indicator.innerHTML = `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg><span id="saveStatus">Error</span>`;
                break;
            case 'unsaved':
                indicator.innerHTML = `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" fill="currentColor"/></svg><span id="saveStatus">Unsaved</span>`;
                break;
        }
    }

    async function saveTemplate() {
        updateSaveIndicator('saving');

        try {
            const response = await fetch('{{ route('admin.documents.sal.update') }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: '{{ $template->title }}',
                    body_content: editor.innerHTML,
                    settings: {
                        margin_top: document.getElementById('marginTop')?.value || '25',
                        margin_bottom: document.getElementById('marginBottom')?.value || '25',
                        margin_left: document.getElementById('marginLeft')?.value || '25',
                        margin_right: document.getElementById('marginRight')?.value || '25'
                    }
                })
            });

            if (response.ok) {
                isDirty = false;
                updateSaveIndicator('saved');
                showToast('Template saved successfully');
            } else {
                throw new Error('Save failed');
            }
        } catch (error) {
            console.error('Save error:', error);
            updateSaveIndicator('error');
            showToast('Failed to save template', 'error');
        }
    }

    function resetTemplate() {
        if (confirm('Are you sure you want to reset to default template? This cannot be undone.')) {
            // Create a form and submit it as POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.documents.sal.reset') }}';
            form.innerHTML = '@csrf';
            document.body.appendChild(form);
            form.submit();
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;

        switch(type) {
            case 'error':
                toast.style.background = '#ef4444';
                break;
            case 'info':
                toast.style.background = '#3b82f6';
                break;
            default:
                toast.style.background = '#333';
        }

        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Prevent leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Layout functions
    function setOrientation(orientation) {
        const page = document.getElementById('documentPage');
        document.getElementById('btnPortrait').classList.remove('active');
        document.getElementById('btnLandscape').classList.remove('active');

        if (orientation === 'landscape') {
            page.style.width = '297mm';
            page.style.minHeight = '210mm';
            document.getElementById('btnLandscape').classList.add('active');
        } else {
            page.style.width = '210mm';
            page.style.minHeight = '297mm';
            document.getElementById('btnPortrait').classList.add('active');
        }
    }
</script>
@endpush
