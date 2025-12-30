@extends('layouts.app')

@section('title', 'SAL Template Designer')

@push('styles')
<style>
    /* Reset and Base */
    .word-editor-wrapper {
        margin: -1.5rem;
        height: calc(100vh - 64px);
        display: flex;
        flex-direction: column;
        background: #e3e3e3;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Title Bar */
    .title-bar {
        background: #217346;
        color: white;
        height: 32px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        font-size: 12px;
    }

    .title-bar-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .title-bar-icon {
        width: 16px;
        height: 16px;
    }

    .title-bar-title {
        font-weight: 400;
    }

    .title-bar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .title-bar-btn {
        width: 46px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
    }

    .title-bar-btn:hover {
        background: rgba(255,255,255,0.1);
    }

    /* Menu Bar */
    .menu-bar {
        background: #217346;
        display: flex;
        align-items: center;
        padding: 0 4px;
        height: 24px;
    }

    .menu-item {
        padding: 2px 10px;
        font-size: 12px;
        color: white;
        cursor: pointer;
        border-radius: 2px;
    }

    .menu-item:hover {
        background: rgba(255,255,255,0.1);
    }

    /* Ribbon */
    .ribbon {
        background: #f3f3f3;
        border-bottom: 1px solid #d4d4d4;
    }

    .ribbon-tabs {
        display: flex;
        background: #217346;
        padding: 0 4px;
    }

    .ribbon-tab {
        padding: 6px 14px;
        font-size: 12px;
        color: rgba(255,255,255,0.85);
        cursor: pointer;
        border: none;
        background: transparent;
        position: relative;
    }

    .ribbon-tab:hover {
        color: white;
        background: rgba(255,255,255,0.1);
    }

    .ribbon-tab.active {
        color: #217346;
        background: #f3f3f3;
        border-radius: 3px 3px 0 0;
    }

    .ribbon-content {
        display: none;
        padding: 6px 8px 8px;
        background: #f3f3f3;
    }

    .ribbon-content.active {
        display: flex;
        gap: 2px;
    }

    /* Ribbon Groups */
    .ribbon-group {
        display: flex;
        flex-direction: column;
        padding: 0 6px;
        border-right: 1px solid #d4d4d4;
        min-width: fit-content;
    }

    .ribbon-group:last-child {
        border-right: none;
    }

    .ribbon-group-content {
        display: flex;
        align-items: flex-start;
        gap: 1px;
        flex: 1;
    }

    .ribbon-group-label {
        text-align: center;
        font-size: 10px;
        color: #666;
        padding-top: 3px;
        white-space: nowrap;
    }

    /* Ribbon Buttons */
    .ribbon-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 4px 6px;
        min-width: 44px;
        height: 66px;
        border: 1px solid transparent;
        background: transparent;
        border-radius: 3px;
        cursor: pointer;
        gap: 2px;
    }

    .ribbon-btn:hover {
        background: #c5e1f5;
        border-color: #98cdf5;
    }

    .ribbon-btn:active {
        background: #98cdf5;
    }

    .ribbon-btn.active {
        background: #c5e1f5;
        border-color: #98cdf5;
    }

    .ribbon-btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ribbon-btn-icon svg {
        width: 24px;
        height: 24px;
        color: #217346;
    }

    .ribbon-btn-label {
        font-size: 10px;
        color: #333;
        text-align: center;
        line-height: 1.2;
    }

    /* Small Ribbon Buttons */
    .ribbon-btn-sm {
        width: 24px;
        height: 24px;
        min-width: 24px;
        padding: 2px;
        border: 1px solid transparent;
        background: transparent;
        border-radius: 2px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ribbon-btn-sm:hover {
        background: #c5e1f5;
        border-color: #98cdf5;
    }

    .ribbon-btn-sm:active,
    .ribbon-btn-sm.active {
        background: #98cdf5;
        border-color: #70b8f0;
    }

    .ribbon-btn-sm svg {
        width: 16px;
        height: 16px;
        color: #333;
    }

    /* Font Controls */
    .font-row {
        display: flex;
        gap: 2px;
        margin-bottom: 3px;
    }

    .font-select {
        height: 22px;
        border: 1px solid #ababab;
        border-radius: 2px;
        font-size: 11px;
        padding: 0 4px;
        background: white;
    }

    .font-family-select {
        width: 120px;
    }

    .font-size-select {
        width: 45px;
        text-align: center;
    }

    /* Separator */
    .ribbon-separator {
        width: 1px;
        background: #d4d4d4;
        margin: 4px 4px;
        align-self: stretch;
    }

    .ribbon-separator-h {
        height: 1px;
        background: #d4d4d4;
        margin: 2px 0;
    }

    /* Button Row */
    .btn-row {
        display: flex;
        gap: 1px;
    }

    /* Color Picker Button */
    .color-btn {
        position: relative;
        width: 32px;
        height: 24px;
        min-width: 32px;
        padding: 2px;
        border: 1px solid transparent;
        background: transparent;
        border-radius: 2px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .color-btn:hover {
        background: #c5e1f5;
        border-color: #98cdf5;
    }

    .color-btn svg {
        width: 14px;
        height: 14px;
        color: #333;
    }

    .color-bar {
        width: 14px;
        height: 3px;
        border-radius: 1px;
        margin-top: 1px;
    }

    /* Document Container */
    .document-container {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    /* Ruler */
    .ruler-container {
        background: #f3f3f3;
        border-bottom: 1px solid #d4d4d4;
        height: 25px;
        display: flex;
        justify-content: center;
        padding: 0 40px;
    }

    .ruler {
        width: 210mm;
        height: 100%;
        background: white;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Document Area */
    .document-area {
        flex: 1;
        overflow: auto;
        background: #e3e3e3;
        padding: 20px;
        display: flex;
        justify-content: center;
    }

    /* Page */
    .page {
        background: white;
        width: 210mm;
        min-height: 297mm;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        position: relative;
    }

    .page-content {
        padding: 25mm;
        min-height: calc(297mm - 50mm);
        outline: none;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.5;
    }

    .page-content:focus {
        outline: none;
    }

    /* Variables Panel */
    .variables-panel {
        width: 280px;
        background: #f8f8f8;
        border-left: 1px solid #d4d4d4;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .panel-header {
        background: #217346;
        color: white;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
    }

    .panel-close:hover {
        background: rgba(255,255,255,0.2);
    }

    .panel-body {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
    }

    .panel-section {
        margin-bottom: 16px;
    }

    .panel-section-title {
        font-size: 11px;
        font-weight: 600;
        color: #217346;
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .variable-btn {
        display: block;
        width: 100%;
        text-align: left;
        padding: 8px 10px;
        margin-bottom: 4px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .variable-btn:hover {
        background: #e8f5e9;
        border-color: #217346;
    }

    .variable-btn code {
        display: block;
        font-family: 'Consolas', 'Courier New', monospace;
        font-size: 11px;
        color: #217346;
        margin-bottom: 2px;
    }

    .variable-btn span {
        font-size: 10px;
        color: #666;
    }

    /* Status Bar */
    .status-bar {
        background: #217346;
        height: 22px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        font-size: 11px;
        color: white;
        gap: 20px;
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .status-bar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Zoom Controls */
    .zoom-control {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .zoom-btn {
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        padding: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
    }

    .zoom-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    .zoom-btn svg {
        width: 14px;
        height: 14px;
    }

    /* Toast */
    .toast-container {
        position: fixed;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
    }

    .toast {
        background: #323232;
        color: white;
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 13px;
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .toast.show {
        opacity: 1;
    }

    .toast.success {
        background: #217346;
    }

    .toast.error {
        background: #d32f2f;
    }

    .toast.info {
        background: #1976d2;
    }

    /* Dropdown */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border: 1px solid #d4d4d4;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 200px;
        z-index: 1000;
        display: none;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        font-size: 12px;
        color: #333;
        cursor: pointer;
        gap: 10px;
    }

    .dropdown-item:hover {
        background: #e8f5e9;
    }

    .dropdown-item svg {
        width: 16px;
        height: 16px;
        color: #666;
    }

    .dropdown-item-shortcut {
        margin-left: auto;
        color: #999;
        font-size: 11px;
    }

    .dropdown-divider {
        height: 1px;
        background: #e0e0e0;
        margin: 4px 0;
    }

    /* Hidden input for color picker */
    input[type="color"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* Quick Access Toolbar */
    .quick-access {
        display: flex;
        align-items: center;
        gap: 2px;
        margin-left: 10px;
    }

    .quick-access-btn {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        border-radius: 2px;
        cursor: pointer;
        color: white;
    }

    .quick-access-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    .quick-access-btn svg {
        width: 14px;
        height: 14px;
    }

    /* Save Status */
    .save-status {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
    }

    .save-status.saved {
        color: rgba(255,255,255,0.9);
    }

    .save-status.saving {
        color: #ffeb3b;
    }

    .save-status.unsaved {
        color: #ff9800;
    }
</style>
@endpush

@section('content')
<div class="word-editor-wrapper" id="editorWrapper">
    <!-- Title Bar -->
    <div class="title-bar">
        <div class="title-bar-left">
            <svg class="title-bar-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm2 2h8v2H8V6zm0 4h8v2H8v-2zm0 4h5v2H8v-2z"/>
            </svg>
            <span class="title-bar-title">SAL Template - Document Editor</span>
            <div class="quick-access">
                <button class="quick-access-btn" onclick="saveDocument()" title="Save (Ctrl+S)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                </button>
                <button class="quick-access-btn" onclick="execCmd('undo')" title="Undo (Ctrl+Z)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                </button>
                <button class="quick-access-btn" onclick="execCmd('redo')" title="Redo (Ctrl+Y)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
                </button>
            </div>
        </div>
        <div class="title-bar-right">
            <div class="save-status saved" id="saveStatus">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Saved</span>
            </div>
        </div>
    </div>

    <!-- Menu Bar -->
    <div class="menu-bar">
        <div class="menu-item dropdown" onclick="toggleMenu('fileMenu')">
            File
            <div class="dropdown-menu" id="fileMenu">
                <div class="dropdown-item" onclick="saveDocument()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Save
                    <span class="dropdown-item-shortcut">Ctrl+S</span>
                </div>
                <div class="dropdown-item" onclick="previewPDF()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview PDF
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="resetToDefault()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset to Default
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.documents.sal') }}" class="dropdown-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                    Close Editor
                </a>
            </div>
        </div>
        <div class="menu-item dropdown" onclick="toggleMenu('editMenu')">
            Edit
            <div class="dropdown-menu" id="editMenu">
                <div class="dropdown-item" onclick="execCmd('undo')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Undo
                    <span class="dropdown-item-shortcut">Ctrl+Z</span>
                </div>
                <div class="dropdown-item" onclick="execCmd('redo')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
                    Redo
                    <span class="dropdown-item-shortcut">Ctrl+Y</span>
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="doCut()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    Cut
                    <span class="dropdown-item-shortcut">Ctrl+X</span>
                </div>
                <div class="dropdown-item" onclick="doCopy()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Copy
                    <span class="dropdown-item-shortcut">Ctrl+C</span>
                </div>
                <div class="dropdown-item" onclick="doPaste()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Paste
                    <span class="dropdown-item-shortcut">Ctrl+V</span>
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="execCmd('selectAll')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/></svg>
                    Select All
                    <span class="dropdown-item-shortcut">Ctrl+A</span>
                </div>
            </div>
        </div>
        <div class="menu-item dropdown" onclick="toggleMenu('insertMenu')">
            Insert
            <div class="dropdown-menu" id="insertMenu">
                <div class="dropdown-item" onclick="insertLetterhead()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    Letterhead
                </div>
                <div class="dropdown-item" onclick="insertDateField()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Date
                </div>
                <div class="dropdown-item" onclick="insertSignature()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Signature Block
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item" onclick="execCmd('insertHorizontalRule')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16"/></svg>
                    Horizontal Line
                </div>
                <div class="dropdown-item" onclick="insertPageBreak()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Page Break
                </div>
            </div>
        </div>
        <div class="menu-item dropdown" onclick="toggleMenu('viewMenu')">
            View
            <div class="dropdown-menu" id="viewMenu">
                <div class="dropdown-item" onclick="togglePanel()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    Toggle Variables Panel
                </div>
            </div>
        </div>
    </div>

    <!-- Ribbon -->
    <div class="ribbon">
        <div class="ribbon-tabs">
            <button class="ribbon-tab active" data-tab="home">Home</button>
            <button class="ribbon-tab" data-tab="insert">Insert</button>
            <button class="ribbon-tab" data-tab="layout">Page Layout</button>
        </div>

        <!-- Home Tab -->
        <div class="ribbon-content active" id="tab-home">
            <!-- Clipboard -->
            <div class="ribbon-group">
                <div class="ribbon-group-content">
                    <button class="ribbon-btn" onclick="doPaste()" title="Paste (Ctrl+V)">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Paste</span>
                    </button>
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <button class="ribbon-btn-sm" onclick="doCut()" title="Cut (Ctrl+X)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" onclick="doCopy()" title="Copy (Ctrl+C)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="ribbon-group-label">Clipboard</div>
            </div>

            <!-- Font -->
            <div class="ribbon-group">
                <div class="ribbon-group-content" style="flex-direction: column; align-items: flex-start;">
                    <div class="font-row">
                        <select class="font-select font-family-select" id="fontFamily" onchange="applyFont(this.value)">
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Arial">Arial</option>
                            <option value="Calibri">Calibri</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Verdana">Verdana</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Courier New">Courier New</option>
                        </select>
                        <select class="font-select font-size-select" id="fontSize" onchange="applyFontSize(this.value)">
                            <option value="1">8</option>
                            <option value="2">10</option>
                            <option value="3" selected>12</option>
                            <option value="4">14</option>
                            <option value="5">18</option>
                            <option value="6">24</option>
                            <option value="7">36</option>
                        </select>
                    </div>
                    <div class="btn-row">
                        <button class="ribbon-btn-sm" id="btnBold" onclick="execCmd('bold')" title="Bold (Ctrl+B)">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6zm0 8h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" id="btnItalic" onclick="execCmd('italic')" title="Italic (Ctrl+I)">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 4h4l-2 16H8l2-16z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" id="btnUnderline" onclick="execCmd('underline')" title="Underline (Ctrl+U)">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 3v9a4 4 0 008 0V3h2v9a6 6 0 01-12 0V3h2zM4 20h16v2H4v-2z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" id="btnStrike" onclick="execCmd('strikeThrough')" title="Strikethrough">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 12h18v2H3v-2zm5-7h8v2H8V5zm0 14h8v-2H8v2z"/></svg>
                        </button>
                        <div class="ribbon-separator"></div>
                        <button class="color-btn" onclick="document.getElementById('textColor').click()" title="Font Color">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 2L5.5 16h2.25l1.12-3h6.25l1.12 3h2.25L13 2h-2zm-1.38 9L12 4.67 14.38 11H9.62z"/></svg>
                            <div class="color-bar" id="textColorBar" style="background: #000000;"></div>
                            <input type="color" id="textColor" value="#000000" onchange="applyTextColor(this.value)">
                        </button>
                        <button class="color-btn" onclick="document.getElementById('bgColor').click()" title="Highlight">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15.24 2c.2 0 .39.08.54.22l4.05 4.05c.3.3.3.78 0 1.08l-9.19 9.19a.75.75 0 01-.54.22H6.96c-.41 0-.75-.34-.75-.75V12.88c0-.2.08-.39.22-.54L15.24 2z"/></svg>
                            <div class="color-bar" id="bgColorBar" style="background: #ffff00;"></div>
                            <input type="color" id="bgColor" value="#ffff00" onchange="applyBgColor(this.value)">
                        </button>
                    </div>
                </div>
                <div class="ribbon-group-label">Font</div>
            </div>

            <!-- Paragraph -->
            <div class="ribbon-group">
                <div class="ribbon-group-content" style="flex-direction: column; align-items: flex-start;">
                    <div class="btn-row">
                        <button class="ribbon-btn-sm" onclick="execCmd('insertUnorderedList')" title="Bullets">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 6a2 2 0 100-4 2 2 0 000 4zm0 8a2 2 0 100-4 2 2 0 000 4zm0 8a2 2 0 100-4 2 2 0 000 4zM8 5h14v2H8V5zm0 8h14v2H8v-2zm0 8h14v2H8v-2z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" onclick="execCmd('insertOrderedList')" title="Numbering">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M2 5h2v2H2v1h3V4H2v1zm0 7h3v-1H3v-1h2V8H2v1h1v1H2v2zm0 5h2v1H2v1h3v-4H2v2zM8 5h14v2H8V5zm0 8h14v2H8v-2zm0 8h14v2H8v-2z"/></svg>
                        </button>
                        <div class="ribbon-separator"></div>
                        <button class="ribbon-btn-sm" onclick="execCmd('outdent')" title="Decrease Indent">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 6h10v2H11V6zm0 5h10v2H11v-2zm0 5h10v2H11v-2zM3 8l4 4-4 4V8z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" onclick="execCmd('indent')" title="Increase Indent">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h10v2H3V6zm0 5h10v2H3v-2zm0 5h10v2H3v-2zm18-6l-4-4v8l4-4z"/></svg>
                        </button>
                    </div>
                    <div class="btn-row">
                        <button class="ribbon-btn-sm" id="btnLeft" onclick="execCmd('justifyLeft')" title="Align Left">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h18v2H3V3zm0 5h12v2H3V8zm0 5h18v2H3v-2zm0 5h12v2H3v-2z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" id="btnCenter" onclick="execCmd('justifyCenter')" title="Center">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h18v2H3V3zm3 5h12v2H6V8zm-3 5h18v2H3v-2zm3 5h12v2H6v-2z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" id="btnRight" onclick="execCmd('justifyRight')" title="Align Right">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h18v2H3V3zm6 5h12v2H9V8zm-6 5h18v2H3v-2zm6 5h12v2H9v-2z"/></svg>
                        </button>
                        <button class="ribbon-btn-sm" id="btnJustify" onclick="execCmd('justifyFull')" title="Justify">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h18v2H3V3zm0 5h18v2H3V8zm0 5h18v2H3v-2zm0 5h18v2H3v-2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="ribbon-group-label">Paragraph</div>
            </div>

            <!-- Styles -->
            <div class="ribbon-group">
                <div class="ribbon-group-content">
                    <button class="ribbon-btn" onclick="applyHeading('h1')" title="Heading 1">
                        <div class="ribbon-btn-icon">
                            <span style="font-size: 18px; font-weight: bold; color: #217346;">H1</span>
                        </div>
                        <span class="ribbon-btn-label">Heading 1</span>
                    </button>
                    <button class="ribbon-btn" onclick="applyHeading('h2')" title="Heading 2">
                        <div class="ribbon-btn-icon">
                            <span style="font-size: 16px; font-weight: bold; color: #217346;">H2</span>
                        </div>
                        <span class="ribbon-btn-label">Heading 2</span>
                    </button>
                    <button class="ribbon-btn" onclick="applyHeading('p')" title="Normal">
                        <div class="ribbon-btn-icon">
                            <span style="font-size: 14px; color: #333;">P</span>
                        </div>
                        <span class="ribbon-btn-label">Normal</span>
                    </button>
                </div>
                <div class="ribbon-group-label">Styles</div>
            </div>
        </div>

        <!-- Insert Tab -->
        <div class="ribbon-content" id="tab-insert">
            <div class="ribbon-group">
                <div class="ribbon-group-content">
                    <button class="ribbon-btn" onclick="insertLetterhead()" title="Insert Letterhead">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Letterhead</span>
                    </button>
                    <button class="ribbon-btn" onclick="insertDateField()" title="Insert Date">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Date</span>
                    </button>
                    <button class="ribbon-btn" onclick="insertSignature()" title="Insert Signature">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Signature</span>
                    </button>
                </div>
                <div class="ribbon-group-label">Document Elements</div>
            </div>
            <div class="ribbon-group">
                <div class="ribbon-group-content">
                    <button class="ribbon-btn" onclick="execCmd('insertHorizontalRule')" title="Horizontal Line">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Line</span>
                    </button>
                    <button class="ribbon-btn" onclick="insertPageBreak()" title="Page Break">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Page Break</span>
                    </button>
                </div>
                <div class="ribbon-group-label">Breaks</div>
            </div>
        </div>

        <!-- Layout Tab -->
        <div class="ribbon-content" id="tab-layout">
            <div class="ribbon-group">
                <div class="ribbon-group-content">
                    <button class="ribbon-btn active" id="btnPortrait" onclick="setOrientation('portrait')" title="Portrait">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="6" y="3" width="12" height="18" rx="1" stroke-width="2"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Portrait</span>
                    </button>
                    <button class="ribbon-btn" id="btnLandscape" onclick="setOrientation('landscape')" title="Landscape">
                        <div class="ribbon-btn-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="1" stroke-width="2"/></svg>
                        </div>
                        <span class="ribbon-btn-label">Landscape</span>
                    </button>
                </div>
                <div class="ribbon-group-label">Orientation</div>
            </div>
            <div class="ribbon-group">
                <div class="ribbon-group-content" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px;">
                        <span style="width: 50px;">Top:</span>
                        <input type="number" value="25" min="0" max="100" style="width: 50px; height: 20px; border: 1px solid #ababab; border-radius: 2px; padding: 0 4px; font-size: 11px;" id="marginTop"> mm
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px;">
                        <span style="width: 50px;">Bottom:</span>
                        <input type="number" value="25" min="0" max="100" style="width: 50px; height: 20px; border: 1px solid #ababab; border-radius: 2px; padding: 0 4px; font-size: 11px;" id="marginBottom"> mm
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px;">
                        <span style="width: 50px;">Left:</span>
                        <input type="number" value="25" min="0" max="100" style="width: 50px; height: 20px; border: 1px solid #ababab; border-radius: 2px; padding: 0 4px; font-size: 11px;" id="marginLeft"> mm
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px;">
                        <span style="width: 50px;">Right:</span>
                        <input type="number" value="25" min="0" max="100" style="width: 50px; height: 20px; border: 1px solid #ababab; border-radius: 2px; padding: 0 4px; font-size: 11px;" id="marginRight"> mm
                    </div>
                </div>
                <div class="ribbon-group-label">Margins</div>
            </div>
        </div>
    </div>

    <!-- Document Container -->
    <div class="document-container">
        <!-- Document Area -->
        <div class="document-area">
            <div class="page" id="page">
                <div class="page-content" contenteditable="true" id="editor" spellcheck="true">{!! $template->body_content ?? '<p>Start typing your document here...</p>' !!}</div>
            </div>
        </div>

        <!-- Variables Panel -->
        <div class="variables-panel" id="variablesPanel">
            <div class="panel-header">
                <span>Template Variables</span>
                <button class="panel-close" onclick="togglePanel()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="panel-body">
                <p style="font-size: 11px; color: #666; margin-bottom: 12px;">Click a variable to insert it at the cursor position.</p>

                <div class="panel-section">
                    <div class="panel-section-title">Student Information</div>
                    @foreach($variables as $var => $desc)
                        @if(str_contains($var, 'student'))
                        <button class="variable-btn" onclick="insertVariable('{{ $var }}')">
                            <code>{{ $var }}</code>
                            <span>{{ $desc }}</span>
                        </button>
                        @endif
                    @endforeach
                </div>

                <div class="panel-section">
                    <div class="panel-section-title">Other Variables</div>
                    @foreach($variables as $var => $desc)
                        @if(!str_contains($var, 'student'))
                        <button class="variable-btn" onclick="insertVariable('{{ $var }}')">
                            <code>{{ $var }}</code>
                            <span>{{ $desc }}</span>
                        </button>
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
        <div class="status-bar-right">
            <span>Page 1 of 1</span>
            <div class="zoom-control">
                <button class="zoom-btn" onclick="zoomOut()" title="Zoom Out">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </button>
                <span id="zoomLevel">100%</span>
                <button class="zoom-btn" onclick="zoomIn()" title="Zoom In">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-container">
    <div class="toast" id="toast"></div>
</div>
@endsection

@push('scripts')
<script>
// Global variables
const editor = document.getElementById('editor');
let isDirty = false;
let currentZoom = 100;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Focus editor
    editor.focus();

    // Update counts
    updateCounts();

    // Track changes
    editor.addEventListener('input', function() {
        isDirty = true;
        updateSaveStatus('unsaved');
        updateCounts();
    });

    // Selection change - update button states
    document.addEventListener('selectionchange', updateButtonStates);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key.toLowerCase()) {
                case 's':
                    e.preventDefault();
                    saveDocument();
                    break;
                case 'b':
                    e.preventDefault();
                    execCmd('bold');
                    break;
                case 'i':
                    e.preventDefault();
                    execCmd('italic');
                    break;
                case 'u':
                    e.preventDefault();
                    execCmd('underline');
                    break;
            }
        }
    });

    // Auto-save every 30 seconds
    setInterval(function() {
        if (isDirty) {
            saveDocument();
        }
    }, 30000);

    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Close menus when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
        }
    });

    // Ribbon tab switching
    document.querySelectorAll('.ribbon-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            document.querySelectorAll('.ribbon-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.ribbon-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        });
    });
});

// Execute command
function execCmd(cmd, value = null) {
    editor.focus();
    document.execCommand(cmd, false, value);
    isDirty = true;
    updateSaveStatus('unsaved');
    updateButtonStates();
}

// Toggle menu
function toggleMenu(menuId) {
    event.stopPropagation();
    const menu = document.getElementById(menuId);
    const wasOpen = menu.classList.contains('show');
    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
    if (!wasOpen) {
        menu.classList.add('show');
    }
}

// Clipboard operations
async function doCopy() {
    const selection = window.getSelection();
    if (selection.toString()) {
        try {
            await navigator.clipboard.writeText(selection.toString());
            showToast('Copied to clipboard', 'success');
        } catch (err) {
            document.execCommand('copy');
        }
    }
    closeMenus();
}

async function doCut() {
    const selection = window.getSelection();
    if (selection.toString()) {
        try {
            await navigator.clipboard.writeText(selection.toString());
            document.execCommand('delete');
            isDirty = true;
            updateSaveStatus('unsaved');
            showToast('Cut to clipboard', 'success');
        } catch (err) {
            document.execCommand('cut');
        }
    }
    closeMenus();
}

async function doPaste() {
    editor.focus();
    try {
        const text = await navigator.clipboard.readText();
        document.execCommand('insertText', false, text);
        isDirty = true;
        updateSaveStatus('unsaved');
    } catch (err) {
        showToast('Use Ctrl+V to paste', 'info');
    }
    closeMenus();
}

// Font functions
function applyFont(font) {
    execCmd('fontName', font);
}

function applyFontSize(size) {
    execCmd('fontSize', size);
}

function applyTextColor(color) {
    execCmd('foreColor', color);
    document.getElementById('textColorBar').style.background = color;
}

function applyBgColor(color) {
    execCmd('hiliteColor', color);
    document.getElementById('bgColorBar').style.background = color;
}

function applyHeading(tag) {
    execCmd('formatBlock', tag);
}

// Update button states based on current selection
function updateButtonStates() {
    document.getElementById('btnBold').classList.toggle('active', document.queryCommandState('bold'));
    document.getElementById('btnItalic').classList.toggle('active', document.queryCommandState('italic'));
    document.getElementById('btnUnderline').classList.toggle('active', document.queryCommandState('underline'));
    document.getElementById('btnStrike').classList.toggle('active', document.queryCommandState('strikeThrough'));
    document.getElementById('btnLeft').classList.toggle('active', document.queryCommandState('justifyLeft'));
    document.getElementById('btnCenter').classList.toggle('active', document.queryCommandState('justifyCenter'));
    document.getElementById('btnRight').classList.toggle('active', document.queryCommandState('justifyRight'));
    document.getElementById('btnJustify').classList.toggle('active', document.queryCommandState('justifyFull'));
}

// Insert functions
function insertVariable(variable) {
    editor.focus();
    document.execCommand('insertText', false, variable);
    isDirty = true;
    updateSaveStatus('unsaved');
}

function insertLetterhead() {
    const html = `<div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 16pt; font-weight: bold; color: #003366;">UNIVERSITI MALAYSIA PAHANG AL-SULTAN ABDULLAH</div>
        <div style="font-size: 11pt; color: #666;">Faculty of Manufacturing and Mechatronic Engineering Technology</div>
        <div style="font-size: 10pt; color: #888; margin-top: 5px;">26600 Pekan, Pahang Darul Makmur, Malaysia</div>
    </div>`;
    editor.focus();
    document.execCommand('insertHTML', false, html);
    isDirty = true;
    updateSaveStatus('unsaved');
    closeMenus();
}

function insertDateField() {
    editor.focus();
    document.execCommand('insertHTML', false, '<p style="text-align: right;">@{{current_date}}</p>');
    isDirty = true;
    updateSaveStatus('unsaved');
    closeMenus();
}

function insertSignature() {
    const html = `<div style="margin-top: 50px;">
        <p>Yours sincerely,</p>
        <div style="margin-top: 60px;">
            <div style="width: 200px; border-bottom: 1px solid #000;"></div>
            <p style="margin-top: 5px; margin-bottom: 0;"><strong>@{{signatory_name}}</strong></p>
            <p style="margin: 0; color: #666; font-size: 11pt;">WBL Coordinator</p>
        </div>
    </div>`;
    editor.focus();
    document.execCommand('insertHTML', false, html);
    isDirty = true;
    updateSaveStatus('unsaved');
    closeMenus();
}

function insertPageBreak() {
    const html = `<div style="page-break-after: always; border-bottom: 2px dashed #ccc; margin: 20px 0; padding: 10px 0; text-align: center; color: #999; font-size: 10px;">Page Break</div>`;
    editor.focus();
    document.execCommand('insertHTML', false, html);
    isDirty = true;
    updateSaveStatus('unsaved');
    closeMenus();
}

// Layout functions
function setOrientation(orientation) {
    const page = document.getElementById('page');
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

// Zoom functions
function zoomIn() {
    if (currentZoom < 200) {
        currentZoom += 10;
        applyZoom();
    }
}

function zoomOut() {
    if (currentZoom > 50) {
        currentZoom -= 10;
        applyZoom();
    }
}

function applyZoom() {
    document.getElementById('page').style.transform = `scale(${currentZoom / 100})`;
    document.getElementById('page').style.transformOrigin = 'top center';
    document.getElementById('zoomLevel').textContent = currentZoom + '%';
}

// Toggle panel
function togglePanel() {
    const panel = document.getElementById('variablesPanel');
    panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
    closeMenus();
}

// Update word/character counts
function updateCounts() {
    const text = editor.innerText || '';
    const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
    const chars = text.length;
    document.getElementById('wordCount').textContent = 'Words: ' + words;
    document.getElementById('charCount').textContent = 'Characters: ' + chars;
}

// Save document
async function saveDocument() {
    updateSaveStatus('saving');

    try {
        const response = await fetch('{{ route("admin.documents.sal.update") }}', {
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
                    margin_top: document.getElementById('marginTop').value,
                    margin_bottom: document.getElementById('marginBottom').value,
                    margin_left: document.getElementById('marginLeft').value,
                    margin_right: document.getElementById('marginRight').value
                }
            })
        });

        if (response.ok) {
            isDirty = false;
            updateSaveStatus('saved');
            showToast('Document saved', 'success');
        } else {
            throw new Error('Save failed');
        }
    } catch (error) {
        updateSaveStatus('unsaved');
        showToast('Failed to save', 'error');
    }
}

function updateSaveStatus(status) {
    const el = document.getElementById('saveStatus');
    el.className = 'save-status ' + status;

    switch(status) {
        case 'saving':
            el.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" stroke-dasharray="30" stroke-dashoffset="10"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg><span>Saving...</span>';
            break;
        case 'saved':
            el.innerHTML = '<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Saved</span>';
            break;
        case 'unsaved':
            el.innerHTML = '<svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/></svg><span>Unsaved</span>';
            break;
    }
}

// Preview PDF
function previewPDF() {
    window.open('{{ route("admin.documents.sal.preview") }}', '_blank');
    closeMenus();
}

// Reset to default
function resetToDefault() {
    if (confirm('Reset to default template? This cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.documents.sal.reset") }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
    closeMenus();
}

// Close all menus
function closeMenus() {
    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
}

// Show toast
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}
</script>
@endpush
