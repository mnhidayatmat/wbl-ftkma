@extends('layouts.app')

@section('title', 'SAL Template Designer')

@push('styles')
<style>
    .designer-wrapper {
        margin: -1.5rem;
        height: calc(100vh - 64px);
        display: flex;
        flex-direction: column;
        background: #1e1e1e;
        font-family: 'Segoe UI', -apple-system, sans-serif;
        overflow: hidden;
    }

    /* Header */
    .designer-header {
        background: #2d2d2d;
        border-bottom: 1px solid #404040;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-title {
        color: #fff;
        font-size: 14px;
        font-weight: 500;
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .btn-header {
        padding: 6px 14px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        border: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }

    .btn-secondary {
        background: #404040;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #505050;
    }

    .btn-primary {
        background: #0066cc;
        color: #fff;
    }

    .btn-primary:hover {
        background: #0077ee;
    }

    .btn-success {
        background: #28a745;
        color: #fff;
    }

    .btn-success:hover {
        background: #32b350;
    }

    /* Main Layout */
    .designer-main {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* Left Toolbar */
    .toolbar-left {
        width: 60px;
        background: #2d2d2d;
        border-right: 1px solid #404040;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 12px 0;
        gap: 4px;
        flex-shrink: 0;
    }

    /* Left Panel (Pages & Layers) */
    .panel-left {
        width: 220px;
        background: #252525;
        border-right: 1px solid #404040;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        overflow: hidden;
    }

    .panel-left-section {
        display: flex;
        flex-direction: column;
    }

    .panel-left-section.layers-section {
        flex: 1;
        overflow: hidden;
        border-top: 1px solid #404040;
    }

    .panel-left-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        color: #888;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #2d2d2d;
    }

    .panel-left-header button {
        background: none;
        border: none;
        color: #888;
        cursor: pointer;
        padding: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
    }

    .panel-left-header button:hover {
        color: #fff;
        background: #404040;
    }

    .panel-left-list {
        list-style: none;
        margin: 0;
        padding: 0;
        overflow-y: auto;
    }

    .panel-left-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        color: #ccc;
        font-size: 12px;
        cursor: pointer;
        gap: 8px;
        border-left: 2px solid transparent;
    }

    .panel-left-item:hover {
        background: #333;
    }

    .panel-left-item.active {
        background: #3a3a3a;
        border-left-color: #0066cc;
        color: #fff;
    }

    .panel-left-item .item-icon {
        width: 16px;
        height: 16px;
        color: #888;
        flex-shrink: 0;
    }

    .panel-left-item .item-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .panel-left-item .item-actions {
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.15s;
    }

    .panel-left-item:hover .item-actions {
        opacity: 1;
    }

    .panel-left-item .item-action {
        background: none;
        border: none;
        color: #888;
        cursor: pointer;
        padding: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
    }

    .panel-left-item .item-action:hover {
        color: #fff;
        background: #505050;
    }

    .panel-left-item .item-action.hidden-layer {
        color: #555;
    }

    .layer-item-hidden {
        opacity: 0.5;
    }

    /* Layer drag and drop */
    .panel-left-item .drag-handle {
        cursor: grab;
        color: #555;
        padding: 2px;
        margin-right: 4px;
        display: flex;
        align-items: center;
    }

    .panel-left-item .drag-handle:hover {
        color: #888;
    }

    .panel-left-item.dragging {
        opacity: 0.5;
        background: #444;
    }

    .panel-left-item.drag-over {
        border-top: 2px solid #0066cc;
    }

    .panel-left-item.drag-over-bottom {
        border-bottom: 2px solid #0066cc;
    }

    .layers-empty {
        padding: 20px 12px;
        color: #666;
        font-size: 11px;
        text-align: center;
    }

    .tool-btn {
        width: 44px;
        height: 44px;
        border: none;
        background: transparent;
        color: #aaa;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        font-size: 9px;
        transition: all 0.15s;
    }

    .tool-btn:hover {
        background: #404040;
        color: #fff;
    }

    .tool-btn.active {
        background: #0066cc;
        color: #fff;
    }

    .tool-btn svg {
        width: 20px;
        height: 20px;
    }

    .tool-divider {
        width: 32px;
        height: 1px;
        background: #404040;
        margin: 8px 0;
    }

    /* Canvas Area */
    .canvas-container {
        flex: 1;
        background: #1e1e1e;
        overflow: auto;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 40px;
    }

    .canvas-wrapper {
        position: relative;
    }

    .canvas {
        width: 595px;
        height: 842px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        position: relative;
        overflow: hidden;
    }

    /* Canvas Elements */
    .canvas-element {
        position: absolute;
        cursor: move;
        min-width: 20px;
        min-height: 20px;
        user-select: none;
    }

    .canvas-element:hover {
        outline: 2px solid #0066cc;
    }

    .canvas-element.selected {
        outline: 2px solid #0066cc;
    }

    .canvas-element.multi-selected {
        outline: 2px solid #0066cc;
        outline-offset: 1px;
    }

    /* Marquee selection box */
    .marquee-selection {
        position: absolute;
        border: 1px dashed #0066cc;
        background: rgba(0, 102, 204, 0.1);
        pointer-events: none;
        z-index: 9999;
    }

    .canvas-element.editing {
        cursor: text;
    }

    /* Resize Handles */
    .resize-handle {
        position: absolute;
        width: 8px;
        height: 8px;
        background: #0066cc;
        border: 1px solid #fff;
        border-radius: 1px;
        z-index: 10;
    }

    .resize-handle.nw { top: -4px; left: -4px; cursor: nwse-resize; }
    .resize-handle.n { top: -4px; left: 50%; transform: translateX(-50%); cursor: ns-resize; }
    .resize-handle.ne { top: -4px; right: -4px; cursor: nesw-resize; }
    .resize-handle.e { top: 50%; right: -4px; transform: translateY(-50%); cursor: ew-resize; }
    .resize-handle.se { bottom: -4px; right: -4px; cursor: nwse-resize; }
    .resize-handle.s { bottom: -4px; left: 50%; transform: translateX(-50%); cursor: ns-resize; }
    .resize-handle.sw { bottom: -4px; left: -4px; cursor: nesw-resize; }
    .resize-handle.w { top: 50%; left: -4px; transform: translateY(-50%); cursor: ew-resize; }

    /* Rotate Handle */
    .rotate-handle {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #28a745;
        border: 2px solid #fff;
        border-radius: 50%;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        cursor: grab;
        z-index: 11;
        display: none;
    }

    .rotate-handle:hover {
        background: #32b350;
    }

    .rotate-handle:active {
        cursor: grabbing;
    }

    .rotate-handle::before {
        content: '';
        position: absolute;
        width: 1px;
        height: 20px;
        background: #28a745;
        left: 50%;
        top: 100%;
        transform: translateX(-50%);
    }

    .canvas-element.selected .rotate-handle {
        display: block;
    }

    /* Text Element */
    .text-element {
        padding: 8px;
        font-family: 'Times New Roman', serif;
        font-size: 12pt;
        line-height: 1.5;
        color: #000;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .text-element[contenteditable="true"] {
        outline: none;
    }

    /* Image Element */
    .image-element {
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
    }

    .image-element.empty {
        background: #f5f5f5;
        border: 1px dashed #ccc;
    }

    .image-element img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Line Element */
    .line-element {
        background: #000;
    }

    /* Table Element */
    .table-element {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .table-element table {
        width: 100%;
        height: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .table-element td {
        border: 1px solid #000;
        padding: 4px 6px;
        font-size: 10pt;
        font-family: 'Times New Roman', serif;
        vertical-align: top;
        overflow: hidden;
        word-wrap: break-word;
    }

    .table-element td:focus {
        outline: 2px solid #0066cc;
        outline-offset: -2px;
        background: #f0f8ff;
    }

    .table-element td[contenteditable="true"] {
        cursor: text;
    }

    /* ========================================
       TABLE COMPONENT STYLES
       Professional table editor with full editing capabilities
       ======================================== */

    /* Table Wrapper */
    .table-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: visible;
        box-sizing: border-box;
    }

    .table-wrapper table {
        width: 100%;
        height: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        box-sizing: border-box;
    }

    /* Table Cells */
    .table-wrapper td {
        position: relative;
        box-sizing: border-box;
        outline: none;
        cursor: text;
        user-select: text;
    }

    .table-wrapper td:focus {
        outline: 2px solid #0066cc;
        outline-offset: -2px;
        background: #f0f8ff !important;
    }

    .table-wrapper td.cell-selected {
        background: #e3f2fd !important;
    }

    /* Cell Formatting Toolbar */
    .cell-format-toolbar {
        position: fixed;
        display: none;
        background: #2d2d2d;
        border: 1px solid #404040;
        border-radius: 6px;
        padding: 4px;
        gap: 2px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 10000;
        flex-direction: row;
        align-items: center;
    }

    .cell-format-toolbar.show {
        display: flex;
    }

    .cell-format-toolbar button {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: #ccc;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        transition: all 0.15s;
    }

    .cell-format-toolbar button:hover {
        background: #404040;
        color: #fff;
    }

    .cell-format-toolbar button.active {
        background: #0066cc;
        color: #fff;
    }

    .cell-format-toolbar .separator {
        width: 1px;
        height: 20px;
        background: #404040;
        margin: 0 4px;
    }

    .cell-format-toolbar .color-picker-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .cell-format-toolbar .color-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: #ccc;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        position: relative;
    }

    .cell-format-toolbar .color-btn:hover {
        background: #404040;
    }

    .cell-format-toolbar .color-btn .color-indicator {
        position: absolute;
        bottom: 3px;
        left: 5px;
        right: 5px;
        height: 3px;
        background: #ff0000;
        border-radius: 1px;
    }

    .cell-format-toolbar input[type="color"] {
        position: absolute;
        width: 28px;
        height: 28px;
        opacity: 0;
        cursor: pointer;
        top: 0;
        left: 0;
    }

    /* Column Resize Handles */
    .col-resize-handle {
        position: absolute;
        top: 0;
        width: 8px;
        height: 100%;
        cursor: col-resize;
        background: transparent;
        z-index: 100;
        transform: translateX(-50%);
    }

    .col-resize-handle:hover,
    .col-resize-handle.active {
        background: rgba(0, 102, 204, 0.2);
    }

    .col-resize-handle::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        width: 2px;
        height: 100%;
        background: transparent;
        transform: translateX(-50%);
    }

    .col-resize-handle:hover::before,
    .col-resize-handle.active::before {
        background: #0066cc;
    }

    /* Row Resize Handles */
    .row-resize-handle {
        position: absolute;
        left: 0;
        width: 100%;
        height: 8px;
        cursor: row-resize;
        background: transparent;
        z-index: 100;
        transform: translateY(-50%);
    }

    .row-resize-handle:hover,
    .row-resize-handle.active {
        background: rgba(0, 102, 204, 0.2);
    }

    .row-resize-handle::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 2px;
        background: transparent;
        transform: translateY(-50%);
    }

    .row-resize-handle:hover::before,
    .row-resize-handle.active::before {
        background: #0066cc;
    }

    /* Table drag handle - minimal style */
    .table-drag-handle {
        position: absolute;
        top: -4px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 8px;
        background: transparent;
        border-radius: 4px;
        cursor: move;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.15s, background 0.15s;
        z-index: 10;
    }

    .canvas-element:hover .table-drag-handle,
    .canvas-element.selected .table-drag-handle {
        opacity: 1;
    }

    .table-drag-handle::before {
        content: '';
        width: 24px;
        height: 3px;
        background: #999;
        border-radius: 2px;
    }

    .table-drag-handle:hover {
        background: rgba(0, 102, 204, 0.1);
    }

    .table-drag-handle:hover::before {
        background: #0066cc;
    }

    /* Add Row/Column Buttons */
    .table-add-col-btn {
        position: absolute;
        top: 50%;
        right: -24px;
        width: 20px;
        height: 20px;
        background: #0066cc;
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.15s, transform 0.15s;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 110;
    }

    .table-add-row-btn {
        position: absolute;
        bottom: -24px;
        left: 50%;
        width: 20px;
        height: 20px;
        background: #0066cc;
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.15s, transform 0.15s;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 110;
    }

    .canvas-element:hover .table-add-col-btn,
    .canvas-element:hover .table-add-row-btn,
    .canvas-element.selected .table-add-col-btn,
    .canvas-element.selected .table-add-row-btn {
        opacity: 1;
    }

    .table-add-col-btn:hover,
    .table-add-row-btn:hover {
        background: #0077ee;
        transform: translateY(-50%) scale(1.1);
    }

    .table-add-row-btn:hover {
        transform: translateX(-50%) scale(1.1);
    }

    /* Show handles when table is selected */
    .canvas-element.selected .col-resize-handle::before,
    .canvas-element.selected .row-resize-handle::before {
        background: rgba(0, 102, 204, 0.3);
    }

    /* Cell right-click context menu */
    .table-context-menu {
        position: fixed;
        background: #2d2d2d;
        border: 1px solid #404040;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 10000;
        min-width: 160px;
        padding: 4px 0;
    }

    .table-context-menu-item {
        padding: 8px 16px;
        color: #ccc;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .table-context-menu-item:hover {
        background: #404040;
        color: #fff;
    }

    .table-context-menu-item.danger {
        color: #ff6b6b;
    }

    .table-context-menu-item.danger:hover {
        background: #5a2a2a;
    }

    .table-context-menu-divider {
        height: 1px;
        background: #404040;
        margin: 4px 0;
    }

    /* Table Dialog */
    .table-dialog {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .table-dialog.show {
        display: flex;
    }

    .table-dialog-content {
        background: #2d2d2d;
        border-radius: 8px;
        padding: 24px;
        min-width: 300px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }

    .table-dialog-title {
        color: #fff;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .table-dialog-row {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
        gap: 12px;
    }

    .table-dialog-row label {
        color: #aaa;
        font-size: 13px;
        width: 80px;
    }

    .table-dialog-row input {
        flex: 1;
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 4px;
        padding: 8px 12px;
        color: #fff;
        font-size: 13px;
    }

    .table-dialog-row input:focus {
        outline: none;
        border-color: #0066cc;
    }

    .table-dialog-preview {
        margin: 20px 0;
        display: flex;
        justify-content: center;
    }

    .table-dialog-preview table {
        border-collapse: collapse;
    }

    .table-dialog-preview td {
        width: 24px;
        height: 24px;
        border: 1px solid #555;
        background: #1e1e1e;
    }

    .table-dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .table-dialog-actions button {
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        border: none;
    }

    .table-dialog-actions .btn-cancel {
        background: #404040;
        color: #fff;
    }

    .table-dialog-actions .btn-cancel:hover {
        background: #505050;
    }

    .table-dialog-actions .btn-insert {
        background: #0066cc;
        color: #fff;
    }

    .table-dialog-actions .btn-insert:hover {
        background: #0077ee;
    }

    /* Right Panel */
    .panel-right {
        width: 280px;
        background: #2d2d2d;
        border-left: 1px solid #404040;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        overflow: hidden;
    }

    .panel-header {
        padding: 12px 16px;
        border-bottom: 1px solid #404040;
        color: #fff;
        font-size: 13px;
        font-weight: 500;
    }

    .panel-content {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
    }

    .panel-section {
        margin-bottom: 20px;
    }

    .panel-section-title {
        color: #888;
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    .prop-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        gap: 8px;
    }

    .prop-label {
        color: #aaa;
        font-size: 12px;
        width: 60px;
        flex-shrink: 0;
    }

    .prop-input {
        flex: 1;
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 4px;
        padding: 6px 10px;
        color: #fff;
        font-size: 12px;
    }

    .prop-input:focus {
        outline: none;
        border-color: #0066cc;
    }

    .prop-input-sm {
        width: 60px;
        text-align: center;
    }

    .prop-select {
        flex: 1;
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 4px;
        padding: 6px 10px;
        color: #fff;
        font-size: 12px;
        cursor: pointer;
    }

    .prop-color {
        width: 32px;
        height: 32px;
        border: 1px solid #404040;
        border-radius: 4px;
        cursor: pointer;
        padding: 2px;
    }

    .format-btns {
        display: flex;
        gap: 4px;
    }

    .format-btn {
        width: 32px;
        height: 32px;
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 4px;
        color: #aaa;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .format-btn:hover {
        background: #404040;
        color: #fff;
    }

    .format-btn.active {
        background: #0066cc;
        border-color: #0066cc;
        color: #fff;
    }

    /* Variables Panel */
    .variables-section {
        border-top: 1px solid #404040;
        padding-top: 16px;
    }

    .variable-item {
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 4px;
        padding: 8px 10px;
        margin-bottom: 6px;
        color: #aaa;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .variable-item:hover {
        background: #404040;
        color: #fff;
        border-color: #0066cc;
    }

    .variable-item code {
        background: #0066cc33;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 10px;
        color: #6bb8ff;
    }

    /* Empty State */
    .empty-state {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #666;
        text-align: center;
        padding: 20px;
    }

    .empty-state.show {
        display: flex;
    }

    .empty-state svg {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 13px;
        margin: 0;
    }

    /* Toast */
    .toast {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: #333;
        color: #fff;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 13px;
        z-index: 1000;
        transition: transform 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .toast.show {
        transform: translateX(-50%) translateY(0);
    }

    .toast.success {
        background: #28a745;
    }

    .toast.error {
        background: #dc3545;
    }

    /* Zoom Controls */
    .zoom-controls {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #2d2d2d;
        border: 1px solid #404040;
        border-radius: 6px;
        display: flex;
        align-items: center;
        padding: 4px;
        gap: 4px;
    }

    .zoom-btn {
        width: 28px;
        height: 28px;
        background: transparent;
        border: none;
        color: #aaa;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .zoom-btn:hover {
        background: #404040;
        color: #fff;
    }

    .zoom-level {
        color: #fff;
        font-size: 12px;
        padding: 0 8px;
        min-width: 50px;
        text-align: center;
    }

    /* Context Menu */
    .context-menu {
        position: fixed;
        background: #2d2d2d;
        border: 1px solid #404040;
        border-radius: 6px;
        padding: 4px 0;
        min-width: 160px;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .context-menu.show {
        display: block;
    }

    .context-item {
        padding: 8px 16px;
        color: #fff;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .context-item:hover {
        background: #0066cc;
    }

    .context-item.danger {
        color: #ff6b6b;
    }

    .context-item.danger:hover {
        background: #dc3545;
        color: #fff;
    }

    .context-divider {
        height: 1px;
        background: #404040;
        margin: 4px 0;
    }

    /* Orientation Buttons */
    .orientation-btns {
        display: flex;
        gap: 10px;
    }

    .orientation-btn {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 12px;
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 6px;
        color: #888;
        cursor: pointer;
        transition: all 0.15s;
    }

    .orientation-btn:hover {
        background: #333;
        color: #fff;
        border-color: #555;
    }

    .orientation-btn.active {
        background: #0066cc22;
        border-color: #0066cc;
        color: #fff;
    }

    .orientation-btn span {
        font-size: 11px;
    }

    /* Margin Grid */
    .margin-grid {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .margin-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .margin-row > span {
        width: 70px;
    }

    .margin-input-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .margin-input-wrapper label {
        font-size: 10px;
        color: #666;
    }

    .margin-input {
        width: 60px !important;
        text-align: center;
        padding: 6px 4px !important;
    }

    .margin-preview {
        width: 60px;
        height: 80px;
        background: #fff;
        border: 1px solid #404040;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
    }

    .margin-inner {
        width: 100%;
        height: 100%;
        border: 1px dashed #0066cc;
        background: #0066cc11;
    }

    /* Preset Buttons */
    .preset-btns {
        display: flex;
        gap: 8px;
    }

    .preset-btn {
        flex: 1;
        padding: 8px 12px;
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 4px;
        color: #aaa;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .preset-btn:hover {
        background: #333;
        color: #fff;
        border-color: #555;
    }

    /* Margin Guides on Canvas */
    .margin-guide {
        position: absolute;
        border: 1px dashed #ccc;
        pointer-events: none;
        z-index: 1;
    }

    /* Shape Buttons */
    .shape-btns {
        display: flex;
        gap: 12px;
    }

    .shape-btn {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 16px 12px;
        background: #1e1e1e;
        border: 1px solid #404040;
        border-radius: 8px;
        color: #aaa;
        cursor: pointer;
        transition: all 0.15s;
    }

    .shape-btn:hover {
        background: #333;
        color: #fff;
        border-color: #0066cc;
    }

    .shape-btn:active {
        transform: scale(0.95);
    }

    .shape-btn svg {
        stroke: currentColor;
    }

    .shape-btn span {
        font-size: 11px;
    }
</style>
@endpush

@section('content')
<div class="designer-wrapper">
    <!-- Header -->
    <div class="designer-header">
        <div class="header-left">
            <a href="{{ route('admin.documents.sal') }}" class="btn-header btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <span class="header-title">SAL Template Designer</span>
        </div>
        <div class="header-actions">
            <button class="btn-header btn-secondary" onclick="resetCanvas()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset
            </button>
            <button class="btn-header btn-secondary" onclick="previewPDF()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview
            </button>
            <button class="btn-header btn-success" onclick="saveTemplate()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Save Template
            </button>
        </div>
    </div>

    <!-- Main Area -->
    <div class="designer-main">
        <!-- Left Toolbar -->
        <div class="toolbar-left">
            <button class="tool-btn" onclick="undo()" title="Undo (Ctrl+Z)" id="btnUndo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 10h10a5 5 0 0 1 5 5v2"/>
                    <path d="M3 10l5-5"/>
                    <path d="M3 10l5 5"/>
                </svg>
                <span>Undo</span>
            </button>
            <button class="tool-btn" onclick="redo()" title="Redo (Ctrl+Y)" id="btnRedo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10H11a5 5 0 0 0-5 5v2"/>
                    <path d="M21 10l-5-5"/>
                    <path d="M21 10l-5 5"/>
                </svg>
                <span>Redo</span>
            </button>
            <div class="tool-divider"></div>
            <button class="tool-btn" onclick="showLayoutPanel()" title="Page Layout" id="btnLayout">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                <span>Layout</span>
            </button>
            <div class="tool-divider"></div>
            <button class="tool-btn" onclick="addText()" title="Add Text">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7V4h16v3M9 20h6M12 4v16"/>
                </svg>
                <span>Text</span>
            </button>
            <button class="tool-btn" onclick="addImage()" title="Add Image">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="M21 15l-5-5L5 21"/>
                </svg>
                <span>Image</span>
            </button>
            <button class="tool-btn" onclick="addLogo()" title="Add Logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span>Logo</span>
            </button>
            <div class="tool-divider"></div>
            <button class="tool-btn" onclick="showShapePanel()" title="Add Shape" id="btnShape">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                </svg>
                <span>Shape</span>
            </button>
            <button class="tool-btn" onclick="showTableDialog()" title="Add Table">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="3" y1="15" x2="21" y2="15"/>
                    <line x1="9" y1="3" x2="9" y2="21"/>
                    <line x1="15" y1="3" x2="15" y2="21"/>
                </svg>
                <span>Table</span>
            </button>
            <div class="tool-divider"></div>
            <button class="tool-btn" onclick="addSignature()" title="Signature Block">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                </svg>
                <span>Sign</span>
            </button>
        </div>

        <!-- Left Panel (Pages & Layers) -->
        <div class="panel-left">
            <!-- Pages Section -->
            <div class="panel-left-section">
                <div class="panel-left-header">
                    <span>Pages</span>
                    <button onclick="addPage()" title="Add Page">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </button>
                </div>
                <ul class="panel-left-list" id="pagesList">
                    <!-- Pages will be rendered here -->
                </ul>
            </div>

            <!-- Layers Section -->
            <div class="panel-left-section layers-section">
                <div class="panel-left-header">
                    <span>Layers</span>
                </div>
                <ul class="panel-left-list" id="layersList">
                    <!-- Layers will be rendered here -->
                </ul>
                <div class="layers-empty" id="layersEmpty">
                    No elements on this page
                </div>
            </div>
        </div>

        <!-- Canvas Area -->
        <div class="canvas-container" id="canvasContainer">
            <div class="canvas-wrapper">
                <div class="canvas" id="canvas" onclick="handleCanvasClick(event)" onmousedown="handleCanvasMouseDown(event)">
                    <!-- Elements will be added here dynamically -->
                </div>
            </div>

            <!-- Zoom Controls -->
            <div class="zoom-controls">
                <button class="zoom-btn" onclick="zoomOut()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <span class="zoom-level" id="zoomLevel">100%</span>
                <button class="zoom-btn" onclick="zoomIn()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <button class="zoom-btn" onclick="zoomFit()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="panel-right">
            <div class="panel-header" id="panelTitle">Properties</div>
            <div class="panel-content">
                <!-- Empty State -->
                <div class="empty-state show" id="emptyState">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                    </svg>
                    <p>Select an element to edit its properties</p>
                </div>

                <!-- Element Properties -->
                <div id="elementProps" style="display: none;">
                    <div class="panel-section">
                        <div class="panel-section-title">Position</div>
                        <div class="prop-row">
                            <span class="prop-label">X</span>
                            <input type="number" class="prop-input prop-input-sm" id="propX" onchange="updatePosition()">
                            <span class="prop-label">Y</span>
                            <input type="number" class="prop-input prop-input-sm" id="propY" onchange="updatePosition()">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Width</span>
                            <input type="number" class="prop-input prop-input-sm" id="propW" onchange="updateSize()">
                            <span class="prop-label">Height</span>
                            <input type="number" class="prop-input prop-input-sm" id="propH" onchange="updateSize()">
                        </div>
                    </div>

                    <div class="panel-section" id="textProps">
                        <div class="panel-section-title">Text</div>
                        <div class="prop-row">
                            <select class="prop-select" id="propFont" onchange="updateTextStyle()" onmousedown="saveSelection()" title="Fonts are mapped to PDF-compatible equivalents">
                                <option value="Arial">Arial (Sans)</option>
                                <option value="Times New Roman">Times New Roman (Serif)</option>
                                <option value="Courier New">Courier New (Mono)</option>
                                <option value="Georgia">Georgia (Serif)</option>
                                <option value="Helvetica">Helvetica (Sans)</option>
                            </select>
                        </div>
                        <div class="prop-row">
                            <input type="number" class="prop-input prop-input-sm" id="propFontSize" value="12" min="8" max="72" onchange="updateTextStyle()" onmousedown="saveSelection()">
                            <span class="prop-label">pt</span>
                            <input type="color" class="prop-color" id="propColor" value="#000000" onchange="updateTextStyle()" onmousedown="saveSelection()">
                        </div>
                        <div class="prop-row">
                            <div class="format-btns">
                                <button class="format-btn" id="btnBold" onmousedown="event.preventDefault(); toggleFormat('bold')" title="Bold">
                                    <strong>B</strong>
                                </button>
                                <button class="format-btn" id="btnItalic" onmousedown="event.preventDefault(); toggleFormat('italic')" title="Italic">
                                    <em>I</em>
                                </button>
                                <button class="format-btn" id="btnUnderline" onmousedown="event.preventDefault(); toggleFormat('underline')" title="Underline">
                                    <u>U</u>
                                </button>
                                <div class="format-btn" style="position: relative; overflow: visible;" title="Text Selection Color">
                                    <span style="border-bottom: 3px solid #ff0000; padding-bottom: 1px;" id="inlineColorIndicator">A</span>
                                    <input type="color" id="inlineTextColor" value="#ff0000"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;"
                                        onchange="applyInlineTextColor(this.value)">
                                </div>
                            </div>
                        </div>
                        <div class="prop-row" style="flex-direction: column; align-items: flex-start; gap: 6px;">
                            <span class="prop-label" style="margin-bottom: 2px;">Alignment</span>
                            <div class="format-btns" style="width: 100%; justify-content: flex-start;">
                                <button class="format-btn" id="btnLeft" onmousedown="event.preventDefault(); setAlign('left')" title="Align Left" style="flex: 1; gap: 4px;">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M3 5h18v2H3V5zm0 4h12v2H3V9zm0 4h18v2H3v-2zm0 4h12v2H3v-2z"/></svg>
                                    <span style="font-size: 10px;">Left</span>
                                </button>
                                <button class="format-btn" id="btnCenter" onmousedown="event.preventDefault(); setAlign('center')" title="Align Center" style="flex: 1; gap: 4px;">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M3 5h18v2H3V5zm3 4h12v2H6V9zm-3 4h18v2H3v-2zm3 4h12v2H6v-2z"/></svg>
                                    <span style="font-size: 10px;">Center</span>
                                </button>
                                <button class="format-btn" id="btnRight" onmousedown="event.preventDefault(); setAlign('right')" title="Align Right" style="flex: 1; gap: 4px;">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M3 5h18v2H3V5zm6 4h12v2H9V9zm-6 4h18v2H3v-2zm6 4h12v2H9v-2z"/></svg>
                                    <span style="font-size: 10px;">Right</span>
                                </button>
                                <button class="format-btn" id="btnJustify" onmousedown="event.preventDefault(); setAlign('justify')" title="Justify" style="flex: 1; gap: 4px;">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M3 5h18v2H3V5zm0 4h18v2H3V9zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"/></svg>
                                    <span style="font-size: 10px;">Justify</span>
                                </button>
                            </div>
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Line Spacing</span>
                            <select class="prop-select" id="propLineSpacing" onchange="updateTextStyle()" style="width: auto; flex: 1;">
                                <option value="1">Single (1.0)</option>
                                <option value="1.15">1.15</option>
                                <option value="1.5" selected>1.5</option>
                                <option value="2">Double (2.0)</option>
                                <option value="2.5">2.5</option>
                                <option value="3">Triple (3.0)</option>
                            </select>
                        </div>
                    </div>

                    <div class="panel-section" id="lineProps" style="display: none;">
                        <div class="panel-section-title">Line</div>
                        <div class="prop-row">
                            <span class="prop-label">Color</span>
                            <input type="color" class="prop-color" id="propLineColor" value="#000000" onchange="updateLineStyle()">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Thickness</span>
                            <input type="number" class="prop-input prop-input-sm" id="propLineWidth" value="1" min="0.1" max="20" step="0.1" oninput="updateLineStyle()" onchange="updateLineStyle()">
                            <span class="prop-label">px</span>
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Rotation</span>
                            <input type="number" class="prop-input prop-input-sm" id="propLineRotation" value="0" min="-360" max="360" onchange="updateLineRotation()">
                            <span class="prop-label">°</span>
                        </div>
                    </div>

                    <div class="panel-section" id="boxProps" style="display: none;">
                        <div class="panel-section-title">Box</div>
                        <div class="prop-row">
                            <span class="prop-label">Fill</span>
                            <input type="color" class="prop-color" id="propBoxFill" value="#ffffff" onchange="updateBoxStyle()">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Border</span>
                            <input type="color" class="prop-color" id="propBoxBorder" value="#000000" onchange="updateBoxStyle()">
                            <input type="number" class="prop-input prop-input-sm" id="propBoxBorderWidth" value="1" min="0" max="10" onchange="updateBoxStyle()">
                        </div>
                    </div>

                    <div class="panel-section" id="tableProps" style="display: none;">
                        <div class="panel-section-title">Table</div>
                        <div class="prop-row">
                            <span class="prop-label">Rows</span>
                            <input type="number" class="prop-input prop-input-sm" id="propTableRows" value="3" min="1" max="20" onchange="updateTableStructure()">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Columns</span>
                            <input type="number" class="prop-input prop-input-sm" id="propTableCols" value="3" min="1" max="10" onchange="updateTableStructure()">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Border</span>
                            <input type="color" class="prop-color" id="propTableBorder" value="#000000" onchange="updateTableStyle()">
                            <input type="number" class="prop-input prop-input-sm" id="propTableBorderWidth" value="1" min="0" max="5" onchange="updateTableStyle()">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Cell Pad</span>
                            <input type="number" class="prop-input prop-input-sm" id="propTablePadding" value="4" min="0" max="20" onchange="updateTableStyle()">
                            <span class="prop-label">px</span>
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Font Size</span>
                            <input type="number" class="prop-input prop-input-sm" id="propTableFontSize" value="10" min="6" max="24" onchange="updateTableStyle()">
                            <span class="prop-label">pt</span>
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Header BG</span>
                            <input type="color" class="prop-color" id="propTableHeaderBg" value="#f0f0f0" onchange="updateTableStyle()">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Text Align</span>
                            <select class="prop-select" id="propTableTextAlign" onchange="updateTableStyle()" style="flex: 1;">
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">V. Align</span>
                            <select class="prop-select" id="propTableVerticalAlign" onchange="updateTableStyle()" style="flex: 1;">
                                <option value="top">Top</option>
                                <option value="middle">Middle</option>
                                <option value="bottom">Bottom</option>
                            </select>
                        </div>
                        <div class="prop-row">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #ccc; font-size: 12px;">
                                <input type="checkbox" id="propTableHasHeader" onchange="updateTableStyle()" checked>
                                Has Header Row
                            </label>
                        </div>
                    </div>

                    <div class="panel-section">
                        <button class="btn-header btn-secondary" style="width: 100%;" onclick="deleteElement()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Element
                        </button>
                    </div>
                </div>

                <!-- Variables -->
                <div class="panel-section variables-section" id="variablesSection">
                    <div class="panel-section-title">Template Variables</div>
                    <p style="color: #666; font-size: 11px; margin-bottom: 10px;">Click to insert, use buttons for formatting</p>
                    @foreach($variables as $key => $label)
                    <div class="variable-item-container" style="margin-bottom: 8px;">
                        <div class="variable-item" onclick="insertVariable('{{ $key }}', 'normal')" style="margin-bottom: 4px;">
                            <code>{{ $key }}</code>
                            <span style="color: #888; font-size: 10px;">{{ $label }}</span>
                        </div>
                        <div style="display: flex; gap: 4px; padding-left: 4px;">
                            <button type="button" onclick="insertVariable('{{ $key }}', 'uppercase')" style="background: #333; border: 1px solid #555; color: #ccc; padding: 2px 6px; border-radius: 3px; font-size: 9px; cursor: pointer;" title="Insert as UPPERCASE">ABC</button>
                            <button type="button" onclick="insertVariable('{{ $key }}', 'bold')" style="background: #333; border: 1px solid #555; color: #ccc; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; cursor: pointer;" title="Insert as Bold">B</button>
                            <button type="button" onclick="insertVariable('{{ $key }}', 'bold-uppercase')" style="background: #333; border: 1px solid #555; color: #ccc; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; cursor: pointer;" title="Insert as BOLD UPPERCASE">B+ABC</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Layout Panel (shown when Layout button clicked) -->
                <div id="layoutPanel" style="display: none;">
                    <!-- Page Size -->
                    <div class="panel-section">
                        <div class="panel-section-title">Page Size</div>
                        <div class="prop-row">
                            <select class="prop-select" id="pageSize" onchange="updatePageSize()">
                                <option value="a4">A4 (210 × 297 mm)</option>
                                <option value="letter">Letter (8.5 × 11 in)</option>
                                <option value="legal">Legal (8.5 × 14 in)</option>
                                <option value="a3">A3 (297 × 420 mm)</option>
                                <option value="a5">A5 (148 × 210 mm)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Orientation -->
                    <div class="panel-section">
                        <div class="panel-section-title">Orientation</div>
                        <div class="orientation-btns">
                            <button class="orientation-btn active" id="btnPortrait" onclick="setOrientation('portrait')">
                                <svg width="24" height="32" viewBox="0 0 24 32" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="1" width="22" height="30" rx="2"/>
                                    <line x1="5" y1="6" x2="19" y2="6"/>
                                    <line x1="5" y1="10" x2="15" y2="10"/>
                                    <line x1="5" y1="14" x2="17" y2="14"/>
                                </svg>
                                <span>Portrait</span>
                            </button>
                            <button class="orientation-btn" id="btnLandscape" onclick="setOrientation('landscape')">
                                <svg width="32" height="24" viewBox="0 0 32 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="1" width="30" height="22" rx="2"/>
                                    <line x1="5" y1="6" x2="27" y2="6"/>
                                    <line x1="5" y1="10" x2="20" y2="10"/>
                                    <line x1="5" y1="14" x2="24" y2="14"/>
                                </svg>
                                <span>Landscape</span>
                            </button>
                        </div>
                    </div>

                    <!-- Margins -->
                    <div class="panel-section">
                        <div class="panel-section-title">Margins (mm)</div>
                        <div class="margin-grid">
                            <div class="margin-row">
                                <span></span>
                                <div class="margin-input-wrapper">
                                    <label>Top</label>
                                    <input type="number" class="prop-input margin-input" id="marginTop" value="25" min="0" max="100" onchange="updateMargins()">
                                </div>
                                <span></span>
                            </div>
                            <div class="margin-row">
                                <div class="margin-input-wrapper">
                                    <label>Left</label>
                                    <input type="number" class="prop-input margin-input" id="marginLeft" value="25" min="0" max="100" onchange="updateMargins()">
                                </div>
                                <div class="margin-preview" id="marginPreview">
                                    <div class="margin-inner"></div>
                                </div>
                                <div class="margin-input-wrapper">
                                    <label>Right</label>
                                    <input type="number" class="prop-input margin-input" id="marginRight" value="25" min="0" max="100" onchange="updateMargins()">
                                </div>
                            </div>
                            <div class="margin-row">
                                <span></span>
                                <div class="margin-input-wrapper">
                                    <label>Bottom</label>
                                    <input type="number" class="prop-input margin-input" id="marginBottom" value="25" min="0" max="100" onchange="updateMargins()">
                                </div>
                                <span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Preset Margins -->
                    <div class="panel-section">
                        <div class="panel-section-title">Margin Presets</div>
                        <div class="preset-btns">
                            <button class="preset-btn" onclick="applyMarginPreset('normal')">Normal</button>
                            <button class="preset-btn" onclick="applyMarginPreset('narrow')">Narrow</button>
                            <button class="preset-btn" onclick="applyMarginPreset('wide')">Wide</button>
                        </div>
                    </div>

                    <!-- Page Background -->
                    <div class="panel-section">
                        <div class="panel-section-title">Page Background</div>
                        <div class="prop-row">
                            <span class="prop-label">Color</span>
                            <input type="color" class="prop-color" id="pageBgColor" value="#ffffff" onchange="updatePageBackground()">
                        </div>
                    </div>
                </div>

                <!-- Shape Panel (shown when Shape button clicked) -->
                <div id="shapePanel" style="display: none;">
                    <div class="panel-section">
                        <div class="panel-section-title">Add Shape</div>
                        <p style="color: #888; font-size: 11px; margin-bottom: 12px;">Click a shape to add it to the canvas</p>
                        <div class="shape-btns">
                            <button class="shape-btn" onclick="addLineFromPanel()" title="Add Line">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="20" x2="35" y2="20"/>
                                </svg>
                                <span>Line</span>
                            </button>
                            <button class="shape-btn" onclick="addBoxFromPanel()" title="Add Box">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="5" y="8" width="30" height="24" rx="2"/>
                                </svg>
                                <span>Box</span>
                            </button>
                        </div>
                    </div>

                    <div class="panel-section">
                        <div class="panel-section-title">Line Defaults</div>
                        <div class="prop-row">
                            <span class="prop-label">Color</span>
                            <input type="color" class="prop-color" id="defaultLineColor" value="#000000">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Thickness</span>
                            <input type="number" class="prop-input prop-input-sm" id="defaultLineThickness" value="1" min="0.1" max="20" step="0.1">
                            <span class="prop-label">px</span>
                        </div>
                    </div>

                    <div class="panel-section">
                        <div class="panel-section-title">Box Defaults</div>
                        <div class="prop-row">
                            <span class="prop-label">Fill</span>
                            <input type="color" class="prop-color" id="defaultBoxFill" value="#ffffff">
                        </div>
                        <div class="prop-row">
                            <span class="prop-label">Border</span>
                            <input type="color" class="prop-color" id="defaultBoxBorder" value="#000000">
                            <input type="number" class="prop-input prop-input-sm" id="defaultBoxBorderWidth" value="1" min="0" max="10">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Insert Dialog -->
<div class="table-dialog" id="tableDialog">
    <div class="table-dialog-content">
        <div class="table-dialog-title">Insert Table</div>
        <div class="table-dialog-row">
            <label>Rows</label>
            <input type="number" id="tableRows" value="3" min="1" max="20" oninput="updateTablePreview()">
        </div>
        <div class="table-dialog-row">
            <label>Columns</label>
            <input type="number" id="tableCols" value="3" min="1" max="10" oninput="updateTablePreview()">
        </div>
        <div class="table-dialog-preview" id="tablePreview"></div>
        <div class="table-dialog-actions">
            <button class="btn-cancel" onclick="hideTableDialog()">Cancel</button>
            <button class="btn-insert" onclick="insertTable()">Insert Table</button>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div class="context-menu" id="contextMenu">
    <div class="context-item" onclick="duplicateElement()">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        Duplicate
    </div>
    <div class="context-item" onclick="bringToFront()">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
        Bring to Front
    </div>
    <div class="context-item" onclick="sendToBack()">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        Send to Back
    </div>
    <div class="context-divider"></div>
    <div class="context-item danger" onclick="deleteElement()">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Delete
    </div>
</div>

<!-- Cell Formatting Toolbar -->
<div class="cell-format-toolbar" id="cellFormatToolbar">
    <button type="button" onclick="formatCellText('bold')" title="Bold (Ctrl+B)" id="cellBoldBtn">
        <strong>B</strong>
    </button>
    <button type="button" onclick="formatCellText('italic')" title="Italic (Ctrl+I)" id="cellItalicBtn">
        <em>I</em>
    </button>
    <button type="button" onclick="formatCellText('underline')" title="Underline (Ctrl+U)" id="cellUnderlineBtn">
        <span style="text-decoration: underline;">U</span>
    </button>
    <div class="separator"></div>
    <div class="color-picker-wrapper" title="Text Color">
        <div class="color-btn">
            A
            <span class="color-indicator" id="textColorIndicator"></span>
        </div>
        <input type="color" id="cellTextColor" value="#ff0000" onchange="applyTextColor(this.value)">
    </div>
    <div class="separator"></div>
    <button type="button" onclick="formatCellText('uppercase')" title="Uppercase" id="cellUpperBtn">
        ABC
    </button>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<!-- Hidden file input for images -->
<input type="file" id="imageInput" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
@endsection

@push('scripts')
<script>
// State
let pages = [{ id: 'page_1', name: 'Page 1', elements: [] }];
let currentPageId = 'page_1';
let pageIdCounter = 1;
let elements = []; // Current page elements (reference to current page's elements array)
let selectedElement = null;
let selectedElements = []; // Array for multi-select

// Undo/Redo History
let undoHistory = [];
let redoHistory = [];
const MAX_HISTORY = 50; // Maximum number of undo states to keep
let isDragging = false;
let isResizing = false;
let isRotating = false;
let isMarqueeSelecting = false; // For marquee/box selection
let marqueeStart = { x: 0, y: 0 }; // Start point of marquee
let dragOffset = { x: 0, y: 0 };
let dragOffsets = {}; // Store offsets for each selected element during multi-drag
let resizeHandle = null;
let rotateStartAngle = 0;
let elementIdCounter = 0;
let currentZoom = 100;
let currentPanel = 'properties'; // 'properties' or 'layout' or 'shape'
let savedSelection = null; // Stored selection for rich text editing

// Page sizes in pixels (at 72 DPI)
const pageSizes = {
    a4: { width: 595, height: 842 },
    letter: { width: 612, height: 792 },
    legal: { width: 612, height: 1008 },
    a3: { width: 842, height: 1191 },
    a5: { width: 420, height: 595 }
};

let pageSettings = {
    size: 'a4',
    orientation: 'portrait',
    margins: { top: 25, bottom: 25, left: 25, right: 25 },
    background: '#ffffff'
};

const canvas = document.getElementById('canvas');

// Load saved elements
document.addEventListener('DOMContentLoaded', function() {
    loadTemplate();

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.context-menu')) {
            document.getElementById('contextMenu').classList.remove('show');
        }
    });

    // Track selection changes for rich text editing
    document.addEventListener('selectionchange', function() {
        const selection = window.getSelection();
        if (selection && selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const container = range.commonAncestorContainer;
            // Check if selection is within a text element
            const textEl = container.nodeType === 3
                ? container.parentElement?.closest('.text-element')
                : container.closest?.('.text-element');
            if (textEl && textEl.contentEditable === 'true') {
                savedSelection = range.cloneRange();
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        const activeTag = document.activeElement.tagName;
        const isEditing = document.querySelector('.canvas-element.editing');

        // Delete element with Delete or Backspace key (when not in input/editing)
        if ((e.key === 'Delete' || e.key === 'Backspace') && selectedElements.length > 0) {
            if (activeTag !== 'INPUT' && activeTag !== 'TEXTAREA' && !isEditing) {
                e.preventDefault();
                // Delete all selected elements
                deleteSelectedElements();
            }
        }
        if (e.key === 'Escape') {
            deselectAll();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveTemplate();
        }
        // Ctrl+A to select all elements
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            if (activeTag !== 'INPUT' && activeTag !== 'TEXTAREA' && !isEditing) {
                e.preventDefault();
                selectAllElements();
            }
        }
        // Ctrl+Z to undo
        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
            if (activeTag !== 'INPUT' && activeTag !== 'TEXTAREA' && !isEditing) {
                e.preventDefault();
                undo();
            }
        }
        // Ctrl+Y or Ctrl+Shift+Z to redo
        if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
            if (activeTag !== 'INPUT' && activeTag !== 'TEXTAREA' && !isEditing) {
                e.preventDefault();
                redo();
            }
        }
    });
});

// ==================== UNDO/REDO FUNCTIONS ====================

// Save current state to undo history
function saveState() {
    // Deep clone the current state
    const state = {
        pages: JSON.parse(JSON.stringify(pages)),
        currentPageId: currentPageId,
        pageSettings: JSON.parse(JSON.stringify(pageSettings)),
        pageIdCounter: pageIdCounter,
        elementIdCounter: elementIdCounter
    };

    undoHistory.push(state);

    // Limit history size
    if (undoHistory.length > MAX_HISTORY) {
        undoHistory.shift();
    }

    // Clear redo history when new action is performed
    redoHistory = [];

    // Update button states
    updateUndoRedoButtons();
}

// Undo last action
function undo() {
    if (undoHistory.length === 0) {
        showNotification('Nothing to undo', 'info');
        return;
    }

    // Save current state to redo history first
    const currentState = {
        pages: JSON.parse(JSON.stringify(pages)),
        currentPageId: currentPageId,
        pageSettings: JSON.parse(JSON.stringify(pageSettings)),
        pageIdCounter: pageIdCounter,
        elementIdCounter: elementIdCounter
    };
    redoHistory.push(currentState);

    // Restore previous state
    const previousState = undoHistory.pop();
    restoreState(previousState);

    showNotification('Undo successful', 'success');
    updateUndoRedoButtons();
}

// Redo last undone action
function redo() {
    if (redoHistory.length === 0) {
        showNotification('Nothing to redo', 'info');
        return;
    }

    // Save current state to undo history first
    const currentState = {
        pages: JSON.parse(JSON.stringify(pages)),
        currentPageId: currentPageId,
        pageSettings: JSON.parse(JSON.stringify(pageSettings)),
        pageIdCounter: pageIdCounter,
        elementIdCounter: elementIdCounter
    };
    undoHistory.push(currentState);

    // Restore redo state
    const redoState = redoHistory.pop();
    restoreState(redoState);

    showNotification('Redo successful', 'success');
    updateUndoRedoButtons();
}

// Restore a saved state
function restoreState(state) {
    pages = state.pages;
    currentPageId = state.currentPageId;
    pageSettings = state.pageSettings;
    pageIdCounter = state.pageIdCounter;
    elementIdCounter = state.elementIdCounter;

    // Update current elements reference
    const currentPage = pages.find(p => p.id === currentPageId);
    elements = currentPage ? currentPage.elements : [];

    // Deselect all
    deselectAll();

    // Clear existing canvas elements
    document.querySelectorAll('.canvas-element').forEach(el => el.remove());

    // Re-render all elements on the canvas
    elements.forEach(element => {
        renderElement(element);
    });

    // Re-render pages list and layers panel
    renderPages();
    renderLayers();
    applyPageSettings();
}

// Update undo/redo button states
function updateUndoRedoButtons() {
    const undoBtn = document.getElementById('btnUndo');
    const redoBtn = document.getElementById('btnRedo');

    if (undoBtn) {
        undoBtn.style.opacity = undoHistory.length > 0 ? '1' : '0.5';
        undoBtn.disabled = undoHistory.length === 0;
    }
    if (redoBtn) {
        redoBtn.style.opacity = redoHistory.length > 0 ? '1' : '0.5';
        redoBtn.disabled = redoHistory.length === 0;
    }
}

// Show notification helper
function showNotification(message, type = 'info') {
    // Create notification element if not exists
    let notification = document.getElementById('undoRedoNotification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'undoRedoNotification';
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            z-index: 10000;
            transition: opacity 0.3s;
            pointer-events: none;
        `;
        document.body.appendChild(notification);
    }

    // Set colors based on type
    if (type === 'success') {
        notification.style.background = '#217346';
        notification.style.color = '#fff';
    } else {
        notification.style.background = '#333';
        notification.style.color = '#fff';
    }

    notification.textContent = message;
    notification.style.opacity = '1';

    // Auto hide
    setTimeout(() => {
        notification.style.opacity = '0';
    }, 1500);
}

// ==================== END UNDO/REDO ====================

// Add Text Element
function addText() {
    saveState(); // Save state for undo
    const id = 'el_' + (++elementIdCounter);
    const element = {
        id: id,
        type: 'text',
        x: 50,
        y: 50,
        width: 200,
        height: 40,
        content: 'Double-click to edit text',
        fontFamily: 'Times New Roman',
        fontSize: 12,
        color: '#000000',
        bold: false,
        italic: false,
        underline: false,
        align: 'left',
        lineHeight: 1.5
    };
    elements.push(element);
    renderElement(element);
    selectElement(id);
}

// Add Image Element
function addImage() {
    document.getElementById('imageInput').click();
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        saveState(); // Save state for undo
        const reader = new FileReader();
        reader.onload = function(e) {
            const id = 'el_' + (++elementIdCounter);
            const element = {
                id: id,
                type: 'image',
                x: 50,
                y: 50,
                width: 150,
                height: 100,
                src: e.target.result
            };
            elements.push(element);
            renderElement(element);
            selectElement(id);
        };
        reader.readAsDataURL(file);
    }
    event.target.value = '';
}

// Add Logo (UMPSA letterhead)
function addLogo() {
    saveState(); // Save state for undo
    const id = 'el_' + (++elementIdCounter);
    const element = {
        id: id,
        type: 'text',
        x: 50,
        y: 30,
        width: 495,
        height: 80,
        content: 'UNIVERSITI MALAYSIA PAHANG AL-SULTAN ABDULLAH\nFaculty of Manufacturing and Mechatronic Engineering Technology\n26600 Pekan, Pahang Darul Makmur, Malaysia',
        fontFamily: 'Arial',
        fontSize: 14,
        color: '#003366',
        bold: true,
        italic: false,
        underline: false,
        align: 'center'
    };
    elements.push(element);
    renderElement(element);
    selectElement(id);
}

// Add Line
function addLine() {
    const id = 'el_' + (++elementIdCounter);
    const element = {
        id: id,
        type: 'line',
        x: 50,
        y: 100,
        width: 200,
        height: 20,
        color: '#000000',
        thickness: 1,
        rotation: 0
    };
    elements.push(element);
    renderElement(element);
    selectElement(id);
}

// Add Box
function addBox() {
    const id = 'el_' + (++elementIdCounter);
    const element = {
        id: id,
        type: 'box',
        x: 50,
        y: 50,
        width: 150,
        height: 100,
        fill: '#ffffff',
        borderColor: '#000000',
        borderWidth: 1
    };
    elements.push(element);
    renderElement(element);
    selectElement(id);
}

// Add Signature Block
function addSignature() {
    saveState(); // Save state for undo
    const id = 'el_' + (++elementIdCounter);
    const element = {
        id: id,
        type: 'text',
        x: 50,
        y: 650,
        width: 250,
        height: 120,
        content: 'Yours sincerely,\n\n\n\n______________________\n@{{signatory_name}}\nWBL Coordinator',
        fontFamily: 'Times New Roman',
        fontSize: 12,
        color: '#000000',
        bold: false,
        italic: false,
        underline: false,
        align: 'left'
    };
    elements.push(element);
    renderElement(element);
    selectElement(id);
}

// Render Element
function renderElement(element) {
    const div = document.createElement('div');
    div.id = element.id;
    div.className = 'canvas-element';
    div.style.left = element.x + 'px';
    div.style.top = element.y + 'px';
    div.style.width = element.width + 'px';
    div.style.height = element.height + 'px';
    div.dataset.type = element.type;

    // Set z-index based on position in elements array
    const elementIndex = elements.findIndex(e => e.id === element.id);
    div.style.zIndex = elementIndex + 1;

    // Apply rotation if exists
    if (element.rotation) {
        div.style.transform = `rotate(${element.rotation}deg)`;
    }

    if (element.type === 'text') {
        // Check if content is rich text (contains HTML) or plain text
        const isRichText = element.isRichText || /<[^>]+>/.test(element.content);
        const content = isRichText ? element.content : element.content.replace(/\n/g, '<br>');

        div.innerHTML = `<div class="text-element" style="
            font-family: ${element.fontFamily};
            font-size: ${element.fontSize}pt;
            color: ${element.color};
            font-weight: ${element.bold ? 'bold' : 'normal'};
            font-style: ${element.italic ? 'italic' : 'normal'};
            text-decoration: ${element.underline ? 'underline' : 'none'};
            text-align: ${element.align};
            line-height: ${element.lineHeight || 1.5};
            width: 100%;
            height: 100%;
        ">${content}</div>`;
    } else if (element.type === 'image') {
        div.innerHTML = `<div class="image-element" style="width: 100%; height: 100%;">
            <img src="${element.src}" alt="Image">
        </div>`;
    } else if (element.type === 'line') {
        // For sub-pixel lines, use scaleY transform for better rendering
        const thickness = element.thickness || 1;
        const useScale = thickness < 1;
        const actualHeight = useScale ? 1 : thickness;
        const scaleY = useScale ? thickness : 1;

        div.innerHTML = `<div class="line-element" style="
            background: ${element.color};
            height: ${actualHeight}px;
            width: 100%;
            position: absolute;
            top: 50%;
            transform: translateY(-50%) scaleY(${scaleY});
            transform-origin: center;
        "></div>`;
    } else if (element.type === 'box') {
        div.innerHTML = `<div style="
            width: 100%;
            height: 100%;
            background: ${element.fill};
            border: ${element.borderWidth}px solid ${element.borderColor};
            box-sizing: border-box;
        "></div>`;
    } else if (element.type === 'table') {
        div.innerHTML = renderTableHTML(element);
        // Bind table events after adding to DOM (done below)
    }

    // Add resize handles
    const handles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
    handles.forEach(pos => {
        const handle = document.createElement('div');
        handle.className = `resize-handle ${pos}`;
        handle.style.display = 'none';
        handle.dataset.handle = pos;
        div.appendChild(handle);
    });

    // Add rotate handle for line elements
    if (element.type === 'line') {
        const rotateHandle = document.createElement('div');
        rotateHandle.className = 'rotate-handle';
        rotateHandle.addEventListener('mousedown', startRotate);
        div.appendChild(rotateHandle);
    }

    // Event listeners
    div.addEventListener('mousedown', handleElementMouseDown);
    div.addEventListener('dblclick', handleElementDblClick);
    div.addEventListener('contextmenu', handleContextMenu);

    canvas.appendChild(div);

    // Bind table events after element is in DOM
    if (element.type === 'table') {
        bindTableEvents(element.id);
    }
}

// Handle Element Mouse Down
function handleElementMouseDown(e) {
    if (e.target.classList.contains('resize-handle')) {
        startResize(e);
        return;
    }

    if (e.target.classList.contains('rotate-handle')) {
        // Rotation is handled by startRotate attached directly to the handle
        return;
    }

    // Check for column resize handle in tables
    if (e.target.classList.contains('col-resize-handle')) {
        // Column resize is handled by onmousedown on the handle itself
        return;
    }

    const el = e.currentTarget;

    // Check if shift is held for multi-select
    const addToSelection = e.shiftKey;

    // If clicking on an already selected element (part of multi-selection), don't change selection
    if (selectedElements.includes(el.id) && selectedElements.length > 1 && !addToSelection) {
        // Keep the multi-selection, just start dragging
    } else {
        selectElement(el.id, addToSelection);
    }

    // Allow dragging from table drag handle
    if (e.target.classList.contains('table-drag-handle')) {
        startDrag(e, el);
        return;
    }

    // Don't start drag if clicking on table cells or column handles
    if (e.target.tagName === 'TD' || e.target.closest('.col-resize-handle')) {
        return;
    }

    if (!el.classList.contains('editing')) {
        startDrag(e, el);
    }
}

// Start Drag
function startDrag(e, el) {
    saveState(); // Save state for undo before drag
    isDragging = true;
    const canvasRect = canvas.getBoundingClientRect();

    // Store offsets for all selected elements (for multi-drag)
    dragOffsets = {};
    selectedElements.forEach(elemId => {
        const elemEl = document.getElementById(elemId);
        if (elemEl) {
            const rect = elemEl.getBoundingClientRect();
            dragOffsets[elemId] = {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top,
                startX: parseFloat(elemEl.style.left) || 0,
                startY: parseFloat(elemEl.style.top) || 0
            };
        }
    });

    // Also store the primary drag offset
    const rect = el.getBoundingClientRect();
    dragOffset.x = e.clientX - rect.left;
    dragOffset.y = e.clientY - rect.top;

    // Store starting mouse position for calculating delta
    dragOffset.startMouseX = e.clientX;
    dragOffset.startMouseY = e.clientY;

    document.addEventListener('mousemove', handleDrag);
    document.addEventListener('mouseup', stopDrag);
    e.preventDefault();
}

function handleDrag(e) {
    if (!isDragging || selectedElements.length === 0) return;

    const canvasRect = canvas.getBoundingClientRect();
    const zoom = currentZoom / 100;

    // Calculate mouse delta from start
    const deltaX = (e.clientX - dragOffset.startMouseX) / zoom;
    const deltaY = (e.clientY - dragOffset.startMouseY) / zoom;

    // Move all selected elements
    selectedElements.forEach(elemId => {
        const el = document.getElementById(elemId);
        const offset = dragOffsets[elemId];
        if (!el || !offset) return;

        let x = offset.startX + deltaX;
        let y = offset.startY + deltaY;

        // Constrain to canvas
        x = Math.max(0, Math.min(x, 595 - el.offsetWidth));
        y = Math.max(0, Math.min(y, 842 - el.offsetHeight));

        el.style.left = x + 'px';
        el.style.top = y + 'px';

        // Update element data
        const element = elements.find(elem => elem.id === elemId);
        if (element) {
            element.x = Math.round(x);
            element.y = Math.round(y);
        }
    });

    updatePropsPanel();
}

function stopDrag() {
    isDragging = false;
    dragOffsets = {};
    document.removeEventListener('mousemove', handleDrag);
    document.removeEventListener('mouseup', stopDrag);
}

// Resize
function startResize(e) {
    saveState(); // Save state for undo before resize
    isResizing = true;
    resizeHandle = e.target.dataset.handle;

    // Ensure element is selected
    const el = e.target.closest('.canvas-element');
    if (el) {
        selectElement(el.id);
    }

    document.addEventListener('mousemove', handleResize);
    document.addEventListener('mouseup', stopResize);
    e.preventDefault();
    e.stopPropagation();
}

function handleResize(e) {
    if (!isResizing || !selectedElement) return;

    const el = document.getElementById(selectedElement);
    if (!el) return;

    const canvasRect = canvas.getBoundingClientRect();
    const element = elements.find(elem => elem.id === selectedElement);
    if (!element) return;

    const mouseX = (e.clientX - canvasRect.left) / (currentZoom / 100);
    const mouseY = (e.clientY - canvasRect.top) / (currentZoom / 100);

    let newX = element.x;
    let newY = element.y;
    let newW = element.width;
    let newH = element.height;

    if (resizeHandle.includes('e')) {
        newW = Math.max(20, mouseX - element.x);
    }
    if (resizeHandle.includes('w')) {
        const dx = element.x - mouseX;
        newW = Math.max(20, element.width + dx);
        newX = mouseX;
    }
    if (resizeHandle.includes('s')) {
        newH = Math.max(20, mouseY - element.y);
    }
    if (resizeHandle.includes('n')) {
        const dy = element.y - mouseY;
        newH = Math.max(20, element.height + dy);
        newY = mouseY;
    }

    el.style.left = newX + 'px';
    el.style.top = newY + 'px';
    el.style.width = newW + 'px';
    el.style.height = newH + 'px';

    element.x = Math.round(newX);
    element.y = Math.round(newY);
    element.width = Math.round(newW);
    element.height = Math.round(newH);

    updatePropsPanel();
}

function stopResize() {
    isResizing = false;
    resizeHandle = null;
    document.removeEventListener('mousemove', handleResize);
    document.removeEventListener('mouseup', stopResize);
}

// Double Click to Edit Text
function handleElementDblClick(e) {
    const el = e.currentTarget;
    if (el.dataset.type !== 'text') return;

    const textEl = el.querySelector('.text-element');
    textEl.contentEditable = true;
    textEl.focus();
    el.classList.add('editing');

    // Select all text if it's the default placeholder
    const element = elements.find(elem => elem.id === el.id);
    if (element && (element.content === 'Double-click to edit text' || textEl.innerText === 'Double-click to edit text')) {
        // Select all text so user can immediately type to replace
        const range = document.createRange();
        range.selectNodeContents(textEl);
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
    }

    // Smart blur handler that checks if focus moved to toolbar
    function handleBlur(e) {
        // Use setTimeout to check where focus went after blur
        setTimeout(() => {
            const activeEl = document.activeElement;
            const propsPanel = document.getElementById('propsPanel');
            const textPropsSection = document.getElementById('textProps');

            // Check if focus moved to a toolbar element
            const focusInToolbar = propsPanel && (
                propsPanel.contains(activeEl) ||
                activeEl.closest('#textProps') ||
                activeEl.closest('.format-btns') ||
                activeEl.id === 'propFont' ||
                activeEl.id === 'propFontSize' ||
                activeEl.id === 'propColor' ||
                activeEl.id === 'propLineSpacing'
            );

            if (focusInToolbar) {
                // Focus went to toolbar, keep editing mode active
                // But don't re-focus - let the toolbar element work
                return;
            }

            // Focus went somewhere else, end editing mode
            textEl.contentEditable = false;
            el.classList.remove('editing');
            textEl.removeEventListener('blur', handleBlur);

            const element = elements.find(e => e.id === el.id);
            if (element) {
                // Preserve HTML formatting for rich text
                element.content = textEl.innerHTML;
                // Mark as rich text if it contains HTML tags
                element.isRichText = /<[^>]+>/.test(element.content);
            }
        }, 10);
    }

    textEl.addEventListener('blur', handleBlur);
}

// Context Menu
function handleContextMenu(e) {
    e.preventDefault();
    selectElement(e.currentTarget.id);

    const menu = document.getElementById('contextMenu');
    menu.style.left = e.clientX + 'px';
    menu.style.top = e.clientY + 'px';
    menu.classList.add('show');
}

// Select Element (with optional shift for multi-select)
function selectElement(id, addToSelection = false) {
    if (addToSelection) {
        // Multi-select mode (shift+click)
        const index = selectedElements.indexOf(id);
        if (index > -1) {
            // Already selected, remove from selection
            selectedElements.splice(index, 1);
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('selected', 'multi-selected');
                el.querySelectorAll('.resize-handle').forEach(h => h.style.display = 'none');
            }
        } else {
            // Add to selection
            selectedElements.push(id);
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('multi-selected');
            }
        }

        // Update primary selected element (last one added)
        if (selectedElements.length > 0) {
            selectedElement = selectedElements[selectedElements.length - 1];
            // Show resize handles only on primary selection
            selectedElements.forEach((elemId, idx) => {
                const el = document.getElementById(elemId);
                if (el) {
                    el.querySelectorAll('.resize-handle').forEach(h => {
                        h.style.display = idx === selectedElements.length - 1 ? 'block' : 'none';
                    });
                }
            });
        } else {
            selectedElement = null;
        }
    } else {
        // Single select mode - clear previous selections
        clearAllSelections();

        selectedElement = id;
        selectedElements = [id];

        const el = document.getElementById(id);
        if (el) {
            el.classList.add('selected');
            el.querySelectorAll('.resize-handle').forEach(h => h.style.display = 'block');
        }
    }

    // Switch to properties panel when element is selected
    showPropertiesPanel();
    updatePropsPanel();
}

// Select multiple elements (used by marquee selection)
function selectMultipleElements(ids) {
    clearAllSelections();

    selectedElements = [...ids];
    if (ids.length > 0) {
        selectedElement = ids[ids.length - 1];
    }

    ids.forEach((id, idx) => {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add(ids.length > 1 ? 'multi-selected' : 'selected');
            // Show resize handles only on last element
            el.querySelectorAll('.resize-handle').forEach(h => {
                h.style.display = idx === ids.length - 1 ? 'block' : 'none';
            });
        }
    });

    if (ids.length > 0) {
        showPropertiesPanel();
        updatePropsPanel();
    }
}

// Select all elements
function selectAllElements() {
    const ids = elements.map(e => e.id);
    selectMultipleElements(ids);
}

// Clear all selections
function clearAllSelections() {
    selectedElements.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('selected', 'multi-selected');
            el.querySelectorAll('.resize-handle').forEach(h => h.style.display = 'none');
        }
    });
    selectedElements = [];
    selectedElement = null;
}

function deselectAll() {
    clearAllSelections();

    // Only update empty state if we're in properties panel
    if (currentPanel === 'properties') {
        document.getElementById('emptyState').classList.add('show');
        document.getElementById('elementProps').style.display = 'none';
    }
}

function handleCanvasClick(e) {
    if (e.target === canvas || e.target.classList.contains('margin-guide')) {
        deselectAll();
        // Switch back to properties panel when clicking canvas
        if (currentPanel === 'layout') {
            showPropertiesPanel();
        }
    }
}

// Marquee Selection
function handleCanvasMouseDown(e) {
    // Only start marquee if clicking directly on canvas or margin guide
    if (e.target !== canvas && !e.target.classList.contains('margin-guide')) {
        return;
    }

    // Don't start if clicking on an element
    if (e.target.closest('.canvas-element')) {
        return;
    }

    isMarqueeSelecting = true;
    const canvasRect = canvas.getBoundingClientRect();
    const zoom = currentZoom / 100;

    marqueeStart.x = (e.clientX - canvasRect.left) / zoom;
    marqueeStart.y = (e.clientY - canvasRect.top) / zoom;

    // Create marquee element
    let marquee = document.getElementById('marqueeSelection');
    if (!marquee) {
        marquee = document.createElement('div');
        marquee.id = 'marqueeSelection';
        marquee.className = 'marquee-selection';
        canvas.appendChild(marquee);
    }

    marquee.style.left = marqueeStart.x + 'px';
    marquee.style.top = marqueeStart.y + 'px';
    marquee.style.width = '0px';
    marquee.style.height = '0px';
    marquee.style.display = 'block';

    // Clear current selection unless shift is held
    if (!e.shiftKey) {
        deselectAll();
    }

    document.addEventListener('mousemove', handleMarqueeMove);
    document.addEventListener('mouseup', handleMarqueeUp);
    e.preventDefault();
}

function handleMarqueeMove(e) {
    if (!isMarqueeSelecting) return;

    const canvasRect = canvas.getBoundingClientRect();
    const zoom = currentZoom / 100;

    const currentX = (e.clientX - canvasRect.left) / zoom;
    const currentY = (e.clientY - canvasRect.top) / zoom;

    // Calculate marquee dimensions
    const left = Math.min(marqueeStart.x, currentX);
    const top = Math.min(marqueeStart.y, currentY);
    const width = Math.abs(currentX - marqueeStart.x);
    const height = Math.abs(currentY - marqueeStart.y);

    const marquee = document.getElementById('marqueeSelection');
    if (marquee) {
        marquee.style.left = left + 'px';
        marquee.style.top = top + 'px';
        marquee.style.width = width + 'px';
        marquee.style.height = height + 'px';
    }

    // Highlight elements that intersect with marquee
    highlightIntersectingElements(left, top, width, height);
}

function handleMarqueeUp(e) {
    if (!isMarqueeSelecting) return;

    const marquee = document.getElementById('marqueeSelection');
    if (marquee) {
        const left = parseFloat(marquee.style.left);
        const top = parseFloat(marquee.style.top);
        const width = parseFloat(marquee.style.width);
        const height = parseFloat(marquee.style.height);

        // Select all elements that intersect with the marquee
        const intersectingIds = getIntersectingElementIds(left, top, width, height);

        if (intersectingIds.length > 0) {
            if (e.shiftKey) {
                // Add to existing selection
                intersectingIds.forEach(id => {
                    if (!selectedElements.includes(id)) {
                        selectedElements.push(id);
                        const el = document.getElementById(id);
                        if (el) {
                            el.classList.add('multi-selected');
                        }
                    }
                });
                if (selectedElements.length > 0) {
                    selectedElement = selectedElements[selectedElements.length - 1];
                    showPropertiesPanel();
                    updatePropsPanel();
                }
            } else {
                selectMultipleElements(intersectingIds);
            }
        }

        marquee.style.display = 'none';
    }

    isMarqueeSelecting = false;
    document.removeEventListener('mousemove', handleMarqueeMove);
    document.removeEventListener('mouseup', handleMarqueeUp);
}

function highlightIntersectingElements(left, top, width, height) {
    // Visual feedback during marquee selection
    elements.forEach(element => {
        const el = document.getElementById(element.id);
        if (!el) return;

        const intersects = elementsIntersect(
            left, top, width, height,
            element.x, element.y, element.width, element.height
        );

        if (intersects && !selectedElements.includes(element.id)) {
            el.style.outlineColor = '#0066cc';
            el.style.outlineStyle = 'dashed';
        } else if (!selectedElements.includes(element.id)) {
            el.style.outlineColor = '';
            el.style.outlineStyle = '';
        }
    });
}

function getIntersectingElementIds(left, top, width, height) {
    const ids = [];
    elements.forEach(element => {
        if (elementsIntersect(
            left, top, width, height,
            element.x, element.y, element.width, element.height
        )) {
            ids.push(element.id);
        }
    });
    return ids;
}

function elementsIntersect(x1, y1, w1, h1, x2, y2, w2, h2) {
    // Check if two rectangles intersect
    return !(x2 > x1 + w1 ||
             x2 + w2 < x1 ||
             y2 > y1 + h1 ||
             y2 + h2 < y1);
}

// Update Properties Panel
function updatePropsPanel() {
    if (!selectedElement) {
        document.getElementById('emptyState').classList.add('show');
        document.getElementById('elementProps').style.display = 'none';
        return;
    }

    document.getElementById('emptyState').classList.remove('show');
    document.getElementById('elementProps').style.display = 'block';

    const element = elements.find(e => e.id === selectedElement);
    if (!element) return;

    document.getElementById('propX').value = element.x;
    document.getElementById('propY').value = element.y;
    document.getElementById('propW').value = element.width;
    document.getElementById('propH').value = element.height;

    document.getElementById('textProps').style.display = element.type === 'text' ? 'block' : 'none';
    document.getElementById('lineProps').style.display = element.type === 'line' ? 'block' : 'none';
    document.getElementById('boxProps').style.display = element.type === 'box' ? 'block' : 'none';
    document.getElementById('tableProps').style.display = element.type === 'table' ? 'block' : 'none';

    if (element.type === 'text') {
        document.getElementById('propFont').value = element.fontFamily;
        document.getElementById('propFontSize').value = element.fontSize;
        document.getElementById('propColor').value = element.color;
        document.getElementById('propLineSpacing').value = element.lineHeight || 1.5;
        document.getElementById('btnBold').classList.toggle('active', element.bold);
        document.getElementById('btnItalic').classList.toggle('active', element.italic);
        document.getElementById('btnUnderline').classList.toggle('active', element.underline);
        document.getElementById('btnLeft').classList.toggle('active', element.align === 'left');
        document.getElementById('btnCenter').classList.toggle('active', element.align === 'center');
        document.getElementById('btnRight').classList.toggle('active', element.align === 'right');
        document.getElementById('btnJustify').classList.toggle('active', element.align === 'justify');
    } else if (element.type === 'line') {
        document.getElementById('propLineColor').value = element.color;
        document.getElementById('propLineWidth').value = element.thickness;
        document.getElementById('propLineRotation').value = element.rotation || 0;
    } else if (element.type === 'box') {
        document.getElementById('propBoxFill').value = element.fill;
        document.getElementById('propBoxBorder').value = element.borderColor;
        document.getElementById('propBoxBorderWidth').value = element.borderWidth;
    } else if (element.type === 'table') {
        document.getElementById('propTableRows').value = element.rows;
        document.getElementById('propTableCols').value = element.cols;
        document.getElementById('propTableBorder').value = element.borderColor || '#000000';
        document.getElementById('propTableBorderWidth').value = element.borderWidth || 1;
        document.getElementById('propTablePadding').value = element.cellPadding || 4;
        document.getElementById('propTableFontSize').value = element.fontSize || 10;
        document.getElementById('propTableHeaderBg').value = element.headerBg || '#f0f0f0';
        document.getElementById('propTableTextAlign').value = element.textAlign || 'left';
        document.getElementById('propTableVerticalAlign').value = element.verticalAlign || 'middle';
        document.getElementById('propTableHasHeader').checked = element.hasHeader !== false;
    }
}

// Update Functions
function updatePosition() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element) return;

    element.x = parseInt(document.getElementById('propX').value) || 0;
    element.y = parseInt(document.getElementById('propY').value) || 0;

    const el = document.getElementById(selectedElement);
    el.style.left = element.x + 'px';
    el.style.top = element.y + 'px';
}

function updateSize() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element) return;

    element.width = parseInt(document.getElementById('propW').value) || 20;
    element.height = parseInt(document.getElementById('propH').value) || 20;

    const el = document.getElementById(selectedElement);
    el.style.width = element.width + 'px';
    el.style.height = element.height + 'px';
}

function updateTextStyle() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'text') return;

    const textEl = document.querySelector(`#${selectedElement} .text-element`);
    const isEditing = textEl && textEl.contentEditable === 'true';

    // Check if we have a saved selection to restore
    const hasSavedSelection = savedSelection !== null;
    let hasSelection = false;

    // Try to restore saved selection first
    if (hasSavedSelection && isEditing) {
        textEl.focus();
        try {
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(savedSelection);
            hasSelection = savedSelection.toString().length > 0;
        } catch (e) {
            // Selection restoration failed, continue without selection
        }
    } else {
        const selection = window.getSelection();
        hasSelection = selection && selection.rangeCount > 0 && selection.toString().length > 0;
    }

    // If editing with selection, apply to selection only
    if (isEditing && hasSelection) {
        const fontFamily = document.getElementById('propFont').value;
        const fontSize = document.getElementById('propFontSize').value;
        const color = document.getElementById('propColor').value;

        // Focus to ensure execCommand works
        textEl.focus();

        // Apply font name
        document.execCommand('fontName', false, fontFamily);

        // Apply color
        document.execCommand('foreColor', false, color);

        // For font size, we need to use a span wrapper approach
        // since execCommand fontSize only supports 1-7
        document.execCommand('fontSize', false, '7');
        const fontElements = textEl.querySelectorAll('font[size="7"]');
        fontElements.forEach(el => {
            el.removeAttribute('size');
            el.style.fontSize = fontSize + 'pt';
        });

        // Save content
        element.content = textEl.innerHTML;
        element.isRichText = true;

        // Clear saved selection after use
        savedSelection = null;
    } else {
        // Apply to whole element
        element.fontFamily = document.getElementById('propFont').value;
        element.fontSize = parseInt(document.getElementById('propFontSize').value) || 12;
        element.color = document.getElementById('propColor').value;
        element.lineHeight = parseFloat(document.getElementById('propLineSpacing').value) || 1.5;

        textEl.style.fontFamily = element.fontFamily;
        textEl.style.fontSize = element.fontSize + 'pt';
        textEl.style.color = element.color;
        textEl.style.lineHeight = element.lineHeight;
    }
}

// Save current selection for rich text editing
function saveSelection() {
    const selection = window.getSelection();
    if (selection && selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        // Only save if selection is within a text element
        const textEl = range.commonAncestorContainer;
        if (textEl && (textEl.classList?.contains('text-element') || textEl.parentElement?.closest('.text-element'))) {
            savedSelection = range.cloneRange();
        }
    }
}

// Restore saved selection
function restoreSelection() {
    if (savedSelection) {
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(savedSelection);
        return true;
    }
    return false;
}

// Check if currently editing a text element
function isEditingText() {
    if (!selectedElement) return false;
    const textEl = document.querySelector(`#${selectedElement} .text-element`);
    return textEl && textEl.contentEditable === 'true';
}

function toggleFormat(format) {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'text') return;

    const textEl = document.querySelector(`#${selectedElement} .text-element`);
    const isEditing = textEl && textEl.contentEditable === 'true';
    const selection = window.getSelection();
    const hasSelection = selection && selection.rangeCount > 0 && selection.toString().length > 0;

    // If editing with selection, apply to selection only using execCommand
    if (isEditing && hasSelection) {
        // Focus the text element first to ensure execCommand works
        textEl.focus();

        // Apply the format
        if (format === 'bold') document.execCommand('bold', false, null);
        if (format === 'italic') document.execCommand('italic', false, null);
        if (format === 'underline') document.execCommand('underline', false, null);

        // Save content with formatting
        element.content = textEl.innerHTML;
        element.isRichText = true;
    } else if (isEditing && !hasSelection) {
        // Editing but no selection - apply to whole element and future typing
        element[format] = !element[format];

        // Apply inline style for current selection position
        if (format === 'bold') document.execCommand('bold', false, null);
        if (format === 'italic') document.execCommand('italic', false, null);
        if (format === 'underline') document.execCommand('underline', false, null);

        // Also update the element-level style
        if (format === 'bold') textEl.style.fontWeight = element.bold ? 'bold' : 'normal';
        if (format === 'italic') textEl.style.fontStyle = element.italic ? 'italic' : 'normal';
        if (format === 'underline') textEl.style.textDecoration = element.underline ? 'underline' : 'none';
    } else {
        // Not editing - apply to whole element
        element[format] = !element[format];

        if (format === 'bold') textEl.style.fontWeight = element.bold ? 'bold' : 'normal';
        if (format === 'italic') textEl.style.fontStyle = element.italic ? 'italic' : 'normal';
        if (format === 'underline') textEl.style.textDecoration = element.underline ? 'underline' : 'none';
    }

    updatePropsPanel();
}

// Apply inline text color to selected text in text elements
function applyInlineTextColor(color) {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'text') return;

    const textEl = document.querySelector(`#${selectedElement} .text-element`);
    if (!textEl) return;

    // Update color indicator
    const indicator = document.getElementById('inlineColorIndicator');
    if (indicator) {
        indicator.style.borderBottomColor = color;
    }

    const isEditing = textEl.contentEditable === 'true';
    const selection = window.getSelection();
    const hasSelection = selection && selection.rangeCount > 0 && selection.toString().length > 0;

    if (isEditing) {
        // Focus and apply color
        textEl.focus();

        if (hasSelection) {
            // Apply to selection
            document.execCommand('foreColor', false, color);
        } else {
            // No selection - select all and apply
            document.execCommand('selectAll', false, null);
            document.execCommand('foreColor', false, color);
        }

        // Save content with formatting
        element.content = textEl.innerHTML;
        element.isRichText = true;
    } else {
        // Not editing - start editing first
        textEl.contentEditable = 'true';
        textEl.focus();
        document.execCommand('selectAll', false, null);
        document.execCommand('foreColor', false, color);
        element.content = textEl.innerHTML;
        element.isRichText = true;
    }
}

function setAlign(align) {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'text') return;

    element.align = align;

    const textEl = document.querySelector(`#${selectedElement} .text-element`);
    textEl.style.textAlign = align;

    updatePropsPanel();
}

function updateLineStyle() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'line') return;

    element.color = document.getElementById('propLineColor').value;
    element.thickness = parseFloat(document.getElementById('propLineWidth').value) || 1;

    const el = document.getElementById(selectedElement);
    const lineEl = el ? el.querySelector('.line-element') : null;
    if (lineEl) {
        lineEl.style.background = element.color;

        // For sub-pixel lines, use scaleY transform for better rendering
        const thickness = element.thickness;
        const useScale = thickness < 1;
        const actualHeight = useScale ? 1 : thickness;
        const scaleY = useScale ? thickness : 1;

        lineEl.style.height = actualHeight + 'px';
        lineEl.style.transform = `translateY(-50%) scaleY(${scaleY})`;
    }
}

function updateLineRotation() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'line') return;

    element.rotation = parseInt(document.getElementById('propLineRotation').value) || 0;

    const el = document.getElementById(selectedElement);
    el.style.transform = `rotate(${element.rotation}deg)`;
}

// Rotation handling
function startRotate(e) {
    e.preventDefault();
    e.stopPropagation();

    isRotating = true;
    const el = e.target.closest('.canvas-element');
    selectElement(el.id);

    const element = elements.find(elem => elem.id === el.id);
    rotateStartAngle = element.rotation || 0;

    document.addEventListener('mousemove', handleRotate);
    document.addEventListener('mouseup', stopRotate);
}

function handleRotate(e) {
    if (!isRotating || !selectedElement) return;

    const el = document.getElementById(selectedElement);
    const element = elements.find(elem => elem.id === selectedElement);
    const rect = el.getBoundingClientRect();

    // Get center of element
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;

    // Calculate angle from center to mouse position
    const angle = Math.atan2(e.clientY - centerY, e.clientX - centerX) * (180 / Math.PI);

    // Adjust angle (add 90 to make top = 0 degrees)
    let rotation = angle + 90;

    // Snap to 15 degree increments when holding Shift
    if (e.shiftKey) {
        rotation = Math.round(rotation / 15) * 15;
    }

    // Normalize to -180 to 180
    while (rotation > 180) rotation -= 360;
    while (rotation < -180) rotation += 360;

    element.rotation = Math.round(rotation);
    el.style.transform = `rotate(${element.rotation}deg)`;

    // Update properties panel
    document.getElementById('propLineRotation').value = element.rotation;
}

function stopRotate() {
    isRotating = false;
    document.removeEventListener('mousemove', handleRotate);
    document.removeEventListener('mouseup', stopRotate);
}

function updateBoxStyle() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'box') return;

    element.fill = document.getElementById('propBoxFill').value;
    element.borderColor = document.getElementById('propBoxBorder').value;
    element.borderWidth = parseInt(document.getElementById('propBoxBorderWidth').value) || 0;

    const boxEl = document.querySelector(`#${selectedElement} > div`);
    boxEl.style.background = element.fill;
    boxEl.style.border = `${element.borderWidth}px solid ${element.borderColor}`;
}

// ==================== TABLE FUNCTIONS ====================

// Show table insert dialog
function showTableDialog() {
    document.getElementById('tableRows').value = 3;
    document.getElementById('tableCols').value = 3;
    updateTablePreview();
    document.getElementById('tableDialog').classList.add('show');
}

// Hide table insert dialog
function hideTableDialog() {
    document.getElementById('tableDialog').classList.remove('show');
}

// Update table preview in dialog
function updateTablePreview() {
    const rows = parseInt(document.getElementById('tableRows').value) || 3;
    const cols = parseInt(document.getElementById('tableCols').value) || 3;
    const preview = document.getElementById('tablePreview');

    let html = '<table>';
    for (let r = 0; r < Math.min(rows, 8); r++) {
        html += '<tr>';
        for (let c = 0; c < Math.min(cols, 8); c++) {
            html += '<td></td>';
        }
        html += '</tr>';
    }
    html += '</table>';
    preview.innerHTML = html;
}

// ========================================
// TABLE COMPONENT - Schema-Driven Implementation
// ========================================

// Generate unique ID
function generateId(prefix = 'id') {
    return `${prefix}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
}

// Insert table from dialog
function insertTable() {
    const rows = parseInt(document.getElementById('tableRows').value) || 3;
    const cols = parseInt(document.getElementById('tableCols').value) || 3;

    addTable(rows, cols);
    hideTableDialog();
}

// Add table element with new schema structure
function addTable(numRows = 3, numCols = 3) {
    const id = 'el_' + (++elementIdCounter);

    // Create column definitions with IDs
    const columns = [];
    const defaultColWidth = 100 / numCols;
    for (let c = 0; c < numCols; c++) {
        columns.push({
            id: generateId('col'),
            width: defaultColWidth  // Percentage width
        });
    }

    // Create row definitions with IDs and cell data
    const rows = [];
    const defaultRowHeight = 30; // Default row height in pixels
    for (let r = 0; r < numRows; r++) {
        const cells = {};
        columns.forEach(col => {
            cells[col.id] = '';
        });
        rows.push({
            id: generateId('row'),
            height: defaultRowHeight,
            cells: cells
        });
    }

    const element = {
        id: id,
        type: 'table',
        x: 50,
        y: 50,
        width: numCols * 100,
        height: numRows * defaultRowHeight,
        // New schema structure
        columns: columns,
        rows: rows,
        // Legacy support
        cols: numCols,
        // Styling
        borderColor: '#000000',
        borderWidth: 1,
        cellPadding: 6,
        fontSize: 10,
        fontFamily: 'Arial',
        headerBg: '#f0f0f0',
        hasHeader: true,
        textAlign: 'left',
        verticalAlign: 'middle'
    };

    elements.push(element);
    renderElement(element);
    selectElement(id);
    renderLayers();
}

// Render table HTML with new schema
function renderTableHTML(element) {
    const { columns, rows, borderColor, borderWidth, cellPadding, fontSize, fontFamily, headerBg, hasHeader, textAlign, verticalAlign } = element;

    // Handle legacy format conversion
    if (!columns || !rows) {
        return renderLegacyTableHTML(element);
    }

    const align = textAlign || 'left';
    const vAlign = verticalAlign || 'middle';
    const font = fontFamily || 'Arial';

    // Calculate total height for row percentages
    const totalHeight = element.height || rows.reduce((sum, r) => sum + (r.height || 30), 0);

    let html = `<div class="table-wrapper" data-element-id="${element.id}">`;

    // Drag handle
    html += `<div class="table-drag-handle" title="Drag to move table"></div>`;

    // Table
    html += `<table style="border-collapse: collapse; width: 100%; height: 100%; table-layout: fixed; font-family: ${font}, sans-serif;">`;

    // Colgroup for column widths
    html += '<colgroup>';
    columns.forEach(col => {
        html += `<col data-col-id="${col.id}" style="width: ${col.width}%;">`;
    });
    html += '</colgroup>';

    // Rows
    rows.forEach((row, rowIndex) => {
        const rowHeightPercent = ((row.height || 30) / totalHeight) * 100;
        html += `<tr data-row-id="${row.id}" style="height: ${rowHeightPercent}%;">`;

        columns.forEach((col, colIndex) => {
            const cellContent = row.cells[col.id] || '';
            const isHeader = hasHeader && rowIndex === 0;
            const bgStyle = isHeader ? `background: ${headerBg};` : '';
            const fontWeight = isHeader ? 'font-weight: bold;' : '';

            html += `<td contenteditable="true"
                data-row-id="${row.id}"
                data-col-id="${col.id}"
                data-row-index="${rowIndex}"
                data-col-index="${colIndex}"
                tabindex="0"
                style="border: ${borderWidth}px solid ${borderColor};
                       padding: ${cellPadding}px;
                       font-size: ${fontSize}pt;
                       text-align: ${align};
                       vertical-align: ${vAlign};
                       ${bgStyle}
                       ${fontWeight}
                       overflow: hidden;
                       word-wrap: break-word;
                       box-sizing: border-box;"
            >${cellContent}</td>`;
        });

        html += '</tr>';
    });

    html += '</table>';

    // Column resize handles
    let cumulativeWidth = 0;
    for (let c = 0; c < columns.length - 1; c++) {
        cumulativeWidth += columns[c].width;
        html += `<div class="col-resize-handle"
            data-col-index="${c}"
            data-element-id="${element.id}"
            style="left: ${cumulativeWidth}%;"></div>`;
    }

    // Row resize handles
    let cumulativeHeight = 0;
    for (let r = 0; r < rows.length - 1; r++) {
        cumulativeHeight += ((rows[r].height || 30) / totalHeight) * 100;
        html += `<div class="row-resize-handle"
            data-row-index="${r}"
            data-element-id="${element.id}"
            style="top: ${cumulativeHeight}%;"></div>`;
    }

    // Add row/column buttons
    html += `<button class="table-add-col-btn" onclick="tableAddColumn('${element.id}')" title="Add column">+</button>`;
    html += `<button class="table-add-row-btn" onclick="tableAddRow('${element.id}')" title="Add row">+</button>`;

    html += '</div>';
    return html;
}

// Legacy table rendering for backward compatibility
function renderLegacyTableHTML(element) {
    const { cols, cellData, borderColor, borderWidth, cellPadding, fontSize, headerBg, hasHeader, textAlign, verticalAlign } = element;
    const numRows = element.rows;
    const colWidths = element.colWidths || Array(cols).fill(100 / cols);
    const align = textAlign || 'left';
    const vAlign = verticalAlign || 'middle';

    let html = `<div class="table-wrapper" data-element-id="${element.id}">`;
    html += `<div class="table-drag-handle" title="Drag to move table"></div>`;
    html += `<table style="border-collapse: collapse; width: 100%; height: 100%; table-layout: fixed;">`;

    html += '<colgroup>';
    for (let c = 0; c < cols; c++) {
        html += `<col style="width: ${colWidths[c]}%;">`;
    }
    html += '</colgroup>';

    for (let r = 0; r < numRows; r++) {
        html += '<tr>';
        for (let c = 0; c < cols; c++) {
            const cellContent = cellData && cellData[r] && cellData[r][c] ? cellData[r][c] : '';
            const isHeader = hasHeader && r === 0;
            const bgStyle = isHeader ? `background: ${headerBg};` : '';
            const fontWeight = isHeader ? 'font-weight: bold;' : '';
            html += `<td contenteditable="true"
                data-row-index="${r}"
                data-col-index="${c}"
                tabindex="0"
                style="border: ${borderWidth}px solid ${borderColor};
                       padding: ${cellPadding}px;
                       font-size: ${fontSize}pt;
                       text-align: ${align};
                       vertical-align: ${vAlign};
                       ${bgStyle}
                       ${fontWeight}
                       overflow: hidden;
                       word-wrap: break-word;"
            >${cellContent}</td>`;
        }
        html += '</tr>';
    }

    html += '</table>';

    let cumulativeWidth = 0;
    for (let c = 0; c < cols - 1; c++) {
        cumulativeWidth += colWidths[c];
        html += `<div class="col-resize-handle"
            data-col-index="${c}"
            data-element-id="${element.id}"
            style="left: ${cumulativeWidth}%;"></div>`;
    }

    html += `<button class="table-add-col-btn" onclick="tableAddColumn('${element.id}')" title="Add column">+</button>`;
    html += `<button class="table-add-row-btn" onclick="tableAddRow('${element.id}')" title="Add row">+</button>`;

    html += '</div>';
    return html;
}

// Save cell content on blur
function saveTableCell(elementId, rowId, colId, content) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    if (element.rows && element.columns) {
        // New schema
        const row = element.rows.find(r => r.id === rowId);
        if (row) {
            row.cells[colId] = content;
        }
    } else if (element.cellData) {
        // Legacy schema
        const rowIndex = parseInt(rowId);
        const colIndex = parseInt(colId);
        if (!element.cellData[rowIndex]) {
            element.cellData[rowIndex] = [];
        }
        element.cellData[rowIndex][colIndex] = content;
    }
}

// Handle cell focus
function onTableCellFocus(e) {
    e.stopPropagation();
    const td = e.target;

    // Track active table cell for variable insertion
    activeTableCell = td;

    const canvasEl = td.closest('.canvas-element');
    if (canvasEl) {
        canvasEl.classList.add('editing');
    }

    // Show formatting toolbar
    showCellFormatToolbar(td);
}

// Handle cell blur
function onTableCellBlur(e) {
    const td = e.target;

    // Hide formatting toolbar after a short delay (allows button clicks to complete)
    setTimeout(() => {
        if (activeTableCell === td) {
            activeTableCell = null;
            hideCellFormatToolbar();
        }
    }, 200);

    const wrapper = td.closest('.table-wrapper');
    if (!wrapper) return;

    const elementId = wrapper.dataset.elementId;
    const rowId = td.dataset.rowId || td.dataset.rowIndex;
    const colId = td.dataset.colId || td.dataset.colIndex;

    // Save innerHTML to preserve formatting (bold, variables, etc.)
    saveTableCell(elementId, rowId, colId, td.innerHTML);

    const canvasEl = td.closest('.canvas-element');
    if (canvasEl) {
        canvasEl.classList.remove('editing');
    }
}

// Show cell formatting toolbar
function showCellFormatToolbar(cell) {
    const toolbar = document.getElementById('cellFormatToolbar');
    if (!toolbar || !cell) return;

    const rect = cell.getBoundingClientRect();

    // Position above the cell
    toolbar.style.left = rect.left + 'px';
    toolbar.style.top = (rect.top - 40) + 'px';

    // If toolbar would go above viewport, position below the cell
    if (rect.top - 40 < 10) {
        toolbar.style.top = (rect.bottom + 5) + 'px';
    }

    toolbar.classList.add('show');
}

// Hide cell formatting toolbar
function hideCellFormatToolbar() {
    const toolbar = document.getElementById('cellFormatToolbar');
    if (toolbar) {
        toolbar.classList.remove('show');
    }
}

// Format selected text in table cell
function formatCellText(format) {
    if (!activeTableCell) return;

    // Prevent blur from hiding toolbar
    event.preventDefault();
    event.stopPropagation();

    const selection = window.getSelection();
    if (!selection.rangeCount) {
        // No selection, select all content
        const range = document.createRange();
        range.selectNodeContents(activeTableCell);
        selection.removeAllRanges();
        selection.addRange(range);
    }

    // Apply formatting using execCommand
    switch (format) {
        case 'bold':
            document.execCommand('bold', false, null);
            break;
        case 'italic':
            document.execCommand('italic', false, null);
            break;
        case 'underline':
            document.execCommand('underline', false, null);
            break;
        case 'uppercase':
            // Get selected text and convert to uppercase
            const range = selection.getRangeAt(0);
            const selectedText = range.toString();
            if (selectedText) {
                const upperText = selectedText.toUpperCase();
                document.execCommand('insertText', false, upperText);
            } else {
                // No selection - convert entire cell content
                const content = activeTableCell.innerText;
                activeTableCell.innerText = content.toUpperCase();
            }
            break;
    }

    // Re-focus the cell and update data
    activeTableCell.focus();

    // Save the cell content
    const wrapper = activeTableCell.closest('.table-wrapper');
    if (wrapper) {
        const elementId = wrapper.dataset.elementId;
        const rowId = activeTableCell.dataset.rowId || activeTableCell.dataset.rowIndex;
        const colId = activeTableCell.dataset.colId || activeTableCell.dataset.colIndex;
        saveTableCell(elementId, rowId, colId, activeTableCell.innerHTML);
    }
}

// Apply text color to selected text
function applyTextColor(color) {
    if (!activeTableCell) return;

    // Update color indicator
    const indicator = document.getElementById('textColorIndicator');
    if (indicator) {
        indicator.style.background = color;
    }

    const selection = window.getSelection();
    if (!selection.rangeCount || selection.isCollapsed) {
        // No selection, select all content
        const range = document.createRange();
        range.selectNodeContents(activeTableCell);
        selection.removeAllRanges();
        selection.addRange(range);
    }

    // Apply color using execCommand
    document.execCommand('foreColor', false, color);

    // Re-focus the cell
    activeTableCell.focus();

    // Save the cell content
    const wrapper = activeTableCell.closest('.table-wrapper');
    if (wrapper) {
        const elementId = wrapper.dataset.elementId;
        const rowId = activeTableCell.dataset.rowId || activeTableCell.dataset.rowIndex;
        const colId = activeTableCell.dataset.colId || activeTableCell.dataset.colIndex;
        saveTableCell(elementId, rowId, colId, activeTableCell.innerHTML);
    }
}

// Add column to table
function tableAddColumn(elementId) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    if (element.columns && element.rows) {
        // New schema
        const newColId = generateId('col');
        const newWidth = 100 / (element.columns.length + 1);

        // Redistribute column widths
        element.columns.forEach(col => {
            col.width = col.width * (element.columns.length / (element.columns.length + 1));
        });

        element.columns.push({
            id: newColId,
            width: newWidth
        });

        // Add cells to all rows
        element.rows.forEach(row => {
            row.cells[newColId] = '';
        });

        element.cols = element.columns.length;
        element.width = element.columns.length * 100;
    } else {
        // Legacy schema
        element.cols = (element.cols || 1) + 1;
        const newColWidths = element.colWidths || [];
        const newWidth = 100 / element.cols;
        newColWidths.forEach((w, i) => newColWidths[i] = w * ((element.cols - 1) / element.cols));
        newColWidths.push(newWidth);
        element.colWidths = newColWidths;

        if (element.cellData) {
            element.cellData.forEach(row => row.push(''));
        }
        element.width = element.cols * 100;
    }

    rerenderTable(elementId);
}

// Add row to table
function tableAddRow(elementId) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    if (element.columns && element.rows) {
        // New schema
        const newRowId = generateId('row');
        const cells = {};
        element.columns.forEach(col => {
            cells[col.id] = '';
        });

        element.rows.push({
            id: newRowId,
            height: 30,
            cells: cells
        });

        element.height = element.rows.reduce((sum, r) => sum + (r.height || 30), 0);
    } else {
        // Legacy schema
        element.rows = (element.rows || 1) + 1;
        const newRow = Array(element.cols || 1).fill('');
        if (!element.cellData) element.cellData = [];
        element.cellData.push(newRow);
        element.height = element.rows * 30;
    }

    rerenderTable(elementId);
}

// Delete row from table
function tableDeleteRow(elementId, rowIndex) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    if (element.rows && element.rows.length > 1) {
        element.rows.splice(rowIndex, 1);
        element.height = element.rows.reduce((sum, r) => sum + (r.height || 30), 0);
    } else if (element.cellData && element.cellData.length > 1) {
        element.cellData.splice(rowIndex, 1);
        element.rows = element.cellData.length;
        element.height = element.rows * 30;
    }

    rerenderTable(elementId);
}

// Delete column from table
function tableDeleteColumn(elementId, colIndex) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    if (element.columns && element.columns.length > 1) {
        const colId = element.columns[colIndex].id;
        const removedWidth = element.columns[colIndex].width;
        element.columns.splice(colIndex, 1);

        // Redistribute width
        element.columns.forEach(col => {
            col.width += removedWidth / element.columns.length;
        });

        // Remove cells from all rows
        element.rows.forEach(row => {
            delete row.cells[colId];
        });

        element.cols = element.columns.length;
        element.width = element.columns.length * 100;
    } else if (element.colWidths && element.colWidths.length > 1) {
        element.colWidths.splice(colIndex, 1);
        const total = element.colWidths.reduce((a, b) => a + b, 0);
        element.colWidths = element.colWidths.map(w => (w / total) * 100);

        if (element.cellData) {
            element.cellData.forEach(row => row.splice(colIndex, 1));
        }
        element.cols--;
        element.width = element.cols * 100;
    }

    rerenderTable(elementId);
}

// Re-render table after changes
function rerenderTable(elementId) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    const el = document.getElementById(elementId);
    if (!el) return;

    el.style.width = element.width + 'px';
    el.style.height = element.height + 'px';
    el.innerHTML = renderTableHTML(element);

    // Re-add resize handles
    addResizeHandlesToElement(el);

    // Re-bind events
    bindTableEvents(elementId);
}

// Add resize handles to element
function addResizeHandlesToElement(el) {
    const handles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
    handles.forEach(pos => {
        if (!el.querySelector(`.resize-handle.${pos}`)) {
            const handle = document.createElement('div');
            handle.className = `resize-handle ${pos}`;
            handle.style.display = 'none';
            handle.dataset.handle = pos;
            el.appendChild(handle);
        }
    });
}

// Bind table events
function bindTableEvents(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;

    // Cell events
    el.querySelectorAll('td[contenteditable]').forEach(td => {
        td.addEventListener('focus', onTableCellFocus);
        td.addEventListener('blur', onTableCellBlur);
        td.addEventListener('keydown', handleTableCellKeydown);
        td.addEventListener('contextmenu', handleTableCellContextMenu);
    });

    // Column resize handles
    el.querySelectorAll('.col-resize-handle').forEach(handle => {
        handle.addEventListener('mousedown', (e) => {
            const colIndex = parseInt(handle.dataset.colIndex);
            startColResize(e, elementId, colIndex);
        });
    });

    // Row resize handles
    el.querySelectorAll('.row-resize-handle').forEach(handle => {
        handle.addEventListener('mousedown', (e) => {
            const rowIndex = parseInt(handle.dataset.rowIndex);
            startRowResize(e, elementId, rowIndex);
        });
    });
}

// Update table structure (add/remove rows/cols)
function updateTableStructure() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'table') return;

    const newRows = parseInt(document.getElementById('propTableRows').value) || element.rows;
    const newCols = parseInt(document.getElementById('propTableCols').value) || element.cols;

    // Adjust cellData array
    const oldCellData = element.cellData || [];
    const newCellData = [];

    for (let r = 0; r < newRows; r++) {
        const row = [];
        for (let c = 0; c < newCols; c++) {
            // Preserve existing data if available
            row.push(oldCellData[r] && oldCellData[r][c] ? oldCellData[r][c] : '');
        }
        newCellData.push(row);
    }

    // Adjust column widths
    const oldColWidths = element.colWidths || [];
    const newColWidths = [];
    const defaultWidth = 100 / newCols;

    for (let c = 0; c < newCols; c++) {
        if (c < oldColWidths.length) {
            newColWidths.push(oldColWidths[c]);
        } else {
            newColWidths.push(defaultWidth);
        }
    }

    // Normalize widths to sum to 100%
    const totalWidth = newColWidths.reduce((a, b) => a + b, 0);
    for (let c = 0; c < newCols; c++) {
        newColWidths[c] = (newColWidths[c] / totalWidth) * 100;
    }

    element.rows = newRows;
    element.cols = newCols;
    element.cellData = newCellData;
    element.colWidths = newColWidths;

    // Adjust element size
    element.width = newCols * 100;
    element.height = newRows * 30;

    // Re-render
    const el = document.getElementById(selectedElement);
    el.style.width = element.width + 'px';
    el.style.height = element.height + 'px';
    el.innerHTML = renderTableHTML(element);

    // Re-add resize handles
    const handles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
    handles.forEach(pos => {
        const handle = document.createElement('div');
        handle.className = `resize-handle ${pos}`;
        handle.style.display = 'block';
        handle.dataset.handle = pos;
        el.appendChild(handle);
    });

    updatePropsPanel();
}

// Update table style
function updateTableStyle() {
    if (!selectedElement) return;
    const element = elements.find(e => e.id === selectedElement);
    if (!element || element.type !== 'table') return;

    element.borderColor = document.getElementById('propTableBorder').value;
    element.borderWidth = parseInt(document.getElementById('propTableBorderWidth').value) || 1;
    element.cellPadding = parseInt(document.getElementById('propTablePadding').value) || 4;
    element.fontSize = parseInt(document.getElementById('propTableFontSize').value) || 10;
    element.headerBg = document.getElementById('propTableHeaderBg').value;
    element.textAlign = document.getElementById('propTableTextAlign').value;
    element.verticalAlign = document.getElementById('propTableVerticalAlign').value;
    element.hasHeader = document.getElementById('propTableHasHeader').checked;

    // Re-render table content
    const el = document.getElementById(selectedElement);
    el.innerHTML = renderTableHTML(element);

    // Re-add resize handles
    const handles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
    handles.forEach(pos => {
        const handle = document.createElement('div');
        handle.className = `resize-handle ${pos}`;
        handle.style.display = 'block';
        handle.dataset.handle = pos;
        el.appendChild(handle);
    });
}

// Column resize state
let isColResizing = false;
let colResizeData = {
    elementId: null,
    colIndex: null,
    startX: null,
    startWidths: null,
    tableWidth: null
};

// Start column resize
function startColResize(e, elementId, colIndex) {
    e.preventDefault();
    e.stopPropagation();

    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    const tableEl = document.getElementById(elementId);
    if (!tableEl) return;

    // Get column widths - support both new schema and legacy format
    let colWidths = [];
    if (element.columns && element.columns.length) {
        colWidths = element.columns.map(c => c.width);
    } else if (element.colWidths) {
        colWidths = [...element.colWidths];
    } else {
        const numCols = element.cols || 3;
        colWidths = Array(numCols).fill(100 / numCols);
    }

    isColResizing = true;
    colResizeData = {
        elementId: elementId,
        colIndex: colIndex,
        startX: e.clientX,
        startWidths: [...colWidths],
        tableWidth: tableEl.offsetWidth,
        useNewSchema: !!(element.columns && element.columns.length)
    };

    // Add active class to handle
    const handle = e.target;
    handle.classList.add('active');

    // Select the table element
    selectElement(elementId);

    document.addEventListener('mousemove', handleColResize);
    document.addEventListener('mouseup', stopColResize);
}

// Handle column resize
function handleColResize(e) {
    if (!isColResizing) return;

    const element = elements.find(el => el.id === colResizeData.elementId);
    if (!element) return;

    // Account for zoom level
    const deltaX = (e.clientX - colResizeData.startX) / (currentZoom / 100);
    const deltaPercent = (deltaX / colResizeData.tableWidth) * 100;

    const colIndex = colResizeData.colIndex;
    const startWidths = colResizeData.startWidths;
    const useNewSchema = colResizeData.useNewSchema;

    // Calculate new widths
    let newLeftWidth = startWidths[colIndex] + deltaPercent;
    let newRightWidth = startWidths[colIndex + 1] - deltaPercent;

    // Minimum column width (10%)
    const minWidth = 10;

    if (newLeftWidth < minWidth) {
        newLeftWidth = minWidth;
        newRightWidth = startWidths[colIndex] + startWidths[colIndex + 1] - minWidth;
    }

    if (newRightWidth < minWidth) {
        newRightWidth = minWidth;
        newLeftWidth = startWidths[colIndex] + startWidths[colIndex + 1] - minWidth;
    }

    // Update element data - support both schemas
    if (useNewSchema) {
        element.columns[colIndex].width = newLeftWidth;
        element.columns[colIndex + 1].width = newRightWidth;
    } else {
        if (!element.colWidths) {
            element.colWidths = [...startWidths];
        }
        element.colWidths[colIndex] = newLeftWidth;
        element.colWidths[colIndex + 1] = newRightWidth;
    }

    // Update visual
    const tableEl = document.getElementById(colResizeData.elementId);
    const cols = tableEl.querySelectorAll('col');
    if (cols[colIndex]) cols[colIndex].style.width = newLeftWidth + '%';
    if (cols[colIndex + 1]) cols[colIndex + 1].style.width = newRightWidth + '%';

    // Update resize handle position
    let cumulativeWidth = 0;
    for (let c = 0; c <= colIndex; c++) {
        const w = useNewSchema ? element.columns[c].width : element.colWidths[c];
        cumulativeWidth += w;
    }
    const handles = tableEl.querySelectorAll('.col-resize-handle');
    if (handles[colIndex]) {
        handles[colIndex].style.left = cumulativeWidth + '%';
    }
}

// Stop column resize
function stopColResize(e) {
    if (!isColResizing) return;

    isColResizing = false;

    // Remove active class from all handles
    document.querySelectorAll('.col-resize-handle.active').forEach(h => {
        h.classList.remove('active');
    });

    document.removeEventListener('mousemove', handleColResize);
    document.removeEventListener('mouseup', stopColResize);

    colResizeData = {
        elementId: null,
        colIndex: null,
        startX: null,
        startWidths: null,
        tableWidth: null
    };
}

// ========================================
// ROW RESIZE
// ========================================

let isRowResizing = false;
let rowResizeData = {
    elementId: null,
    rowIndex: null,
    startY: null,
    startHeights: null,
    tableHeight: null
};

function startRowResize(e, elementId, rowIndex) {
    e.preventDefault();
    e.stopPropagation();

    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    const tableEl = document.getElementById(elementId);
    if (!tableEl) return;

    // Get row heights
    let rowHeights = [];
    if (element.rows && element.rows.length) {
        rowHeights = element.rows.map(r => r.height || 30);
    } else {
        const numRows = element.rows || element.cellData?.length || 3;
        const defaultHeight = element.height / numRows;
        rowHeights = Array(numRows).fill(defaultHeight);
    }

    isRowResizing = true;
    rowResizeData = {
        elementId: elementId,
        rowIndex: rowIndex,
        startY: e.clientY,
        startHeights: [...rowHeights],
        tableHeight: tableEl.offsetHeight
    };

    const handle = e.target;
    handle.classList.add('active');

    selectElement(elementId);

    document.addEventListener('mousemove', handleRowResize);
    document.addEventListener('mouseup', stopRowResize);
}

function handleRowResize(e) {
    if (!isRowResizing) return;

    const element = elements.find(el => el.id === rowResizeData.elementId);
    if (!element) return;

    const deltaY = (e.clientY - rowResizeData.startY) / (currentZoom / 100);
    const rowIndex = rowResizeData.rowIndex;
    const startHeights = rowResizeData.startHeights;

    let newTopHeight = startHeights[rowIndex] + deltaY;
    let newBottomHeight = startHeights[rowIndex + 1] - deltaY;

    const minHeight = 20;

    if (newTopHeight < minHeight) {
        newTopHeight = minHeight;
        newBottomHeight = startHeights[rowIndex] + startHeights[rowIndex + 1] - minHeight;
    }

    if (newBottomHeight < minHeight) {
        newBottomHeight = minHeight;
        newTopHeight = startHeights[rowIndex] + startHeights[rowIndex + 1] - minHeight;
    }

    // Update element data
    if (element.rows && element.rows.length) {
        element.rows[rowIndex].height = newTopHeight;
        element.rows[rowIndex + 1].height = newBottomHeight;
    }

    // Update visual
    const tableEl = document.getElementById(rowResizeData.elementId);
    const trs = tableEl.querySelectorAll('tr');
    const totalHeight = element.height || startHeights.reduce((a, b) => a + b, 0);

    if (trs[rowIndex]) trs[rowIndex].style.height = ((newTopHeight / totalHeight) * 100) + '%';
    if (trs[rowIndex + 1]) trs[rowIndex + 1].style.height = ((newBottomHeight / totalHeight) * 100) + '%';

    // Update resize handle position
    let cumulativeHeight = 0;
    for (let r = 0; r <= rowIndex; r++) {
        const h = element.rows ? element.rows[r].height : startHeights[r];
        cumulativeHeight += (h / totalHeight) * 100;
    }
    const handles = tableEl.querySelectorAll('.row-resize-handle');
    if (handles[rowIndex]) {
        handles[rowIndex].style.top = cumulativeHeight + '%';
    }
}

function stopRowResize(e) {
    if (!isRowResizing) return;

    isRowResizing = false;

    document.querySelectorAll('.row-resize-handle.active').forEach(h => {
        h.classList.remove('active');
    });

    document.removeEventListener('mousemove', handleRowResize);
    document.removeEventListener('mouseup', stopRowResize);

    rowResizeData = {
        elementId: null,
        rowIndex: null,
        startY: null,
        startHeights: null,
        tableHeight: null
    };
}

// ========================================
// KEYBOARD NAVIGATION FOR TABLE CELLS
// ========================================

function handleTableCellKeydown(e) {
    const td = e.target;
    if (!td || td.tagName !== 'TD') return;

    const wrapper = td.closest('.table-wrapper');
    if (!wrapper) return;

    const elementId = wrapper.dataset.elementId;
    const rowIndex = parseInt(td.dataset.rowIndex);
    const colIndex = parseInt(td.dataset.colIndex);

    const table = wrapper.querySelector('table');
    const rows = table.querySelectorAll('tr');
    const numRows = rows.length;
    const numCols = rows[0] ? rows[0].querySelectorAll('td').length : 0;

    let targetRow = rowIndex;
    let targetCol = colIndex;
    let shouldNavigate = false;

    switch(e.key) {
        case 'Tab':
            e.preventDefault();
            if (e.shiftKey) {
                // Shift+Tab: Previous cell
                targetCol--;
                if (targetCol < 0) {
                    targetCol = numCols - 1;
                    targetRow--;
                }
            } else {
                // Tab: Next cell
                targetCol++;
                if (targetCol >= numCols) {
                    targetCol = 0;
                    targetRow++;
                }
            }
            shouldNavigate = true;
            break;

        case 'ArrowUp':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                targetRow--;
                shouldNavigate = true;
            }
            break;

        case 'ArrowDown':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                targetRow++;
                shouldNavigate = true;
            }
            break;

        case 'ArrowLeft':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                targetCol--;
                shouldNavigate = true;
            }
            break;

        case 'ArrowRight':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                targetCol++;
                shouldNavigate = true;
            }
            break;

        case 'Enter':
            if (e.shiftKey) {
                // Shift+Enter: Previous row
                e.preventDefault();
                targetRow--;
                shouldNavigate = true;
            } else if (e.ctrlKey || e.metaKey) {
                // Ctrl+Enter: Next row
                e.preventDefault();
                targetRow++;
                shouldNavigate = true;
            }
            // Regular Enter allows line break in cell
            break;

        case 'Escape':
            e.preventDefault();
            td.blur();
            break;

        case 'b':
        case 'B':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                formatCellText('bold');
            }
            break;

        case 'i':
        case 'I':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                formatCellText('italic');
            }
            break;

        case 'u':
        case 'U':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                formatCellText('underline');
            }
            break;
    }

    if (shouldNavigate) {
        // Wrap around
        if (targetRow < 0) targetRow = numRows - 1;
        if (targetRow >= numRows) targetRow = 0;
        if (targetCol < 0) targetCol = numCols - 1;
        if (targetCol >= numCols) targetCol = 0;

        // Save current cell content
        const rowId = td.dataset.rowId || td.dataset.rowIndex;
        const colId = td.dataset.colId || td.dataset.colIndex;
        saveTableCell(elementId, rowId, colId, td.innerText);

        // Navigate to target cell
        const targetTd = rows[targetRow]?.querySelectorAll('td')[targetCol];
        if (targetTd) {
            targetTd.focus();
            // Select all text in the cell
            const range = document.createRange();
            range.selectNodeContents(targetTd);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }
}

// ========================================
// TABLE CONTEXT MENU
// ========================================

let tableContextMenuTarget = null;

function handleTableCellContextMenu(e) {
    e.preventDefault();
    e.stopPropagation();

    const td = e.target.closest('td');
    if (!td) return;

    const wrapper = td.closest('.table-wrapper');
    if (!wrapper) return;

    tableContextMenuTarget = {
        td: td,
        elementId: wrapper.dataset.elementId,
        rowIndex: parseInt(td.dataset.rowIndex),
        colIndex: parseInt(td.dataset.colIndex)
    };

    // Remove existing context menu
    const existing = document.querySelector('.table-context-menu');
    if (existing) existing.remove();

    // Create context menu
    const menu = document.createElement('div');
    menu.className = 'table-context-menu';
    menu.innerHTML = `
        <div class="table-context-menu-item" onclick="tableInsertRowAbove()">
            <span>↑</span> Insert row above
        </div>
        <div class="table-context-menu-item" onclick="tableInsertRowBelow()">
            <span>↓</span> Insert row below
        </div>
        <div class="table-context-menu-divider"></div>
        <div class="table-context-menu-item" onclick="tableInsertColLeft()">
            <span>←</span> Insert column left
        </div>
        <div class="table-context-menu-item" onclick="tableInsertColRight()">
            <span>→</span> Insert column right
        </div>
        <div class="table-context-menu-divider"></div>
        <div class="table-context-menu-item danger" onclick="tableDeleteCurrentRow()">
            <span>✕</span> Delete row
        </div>
        <div class="table-context-menu-item danger" onclick="tableDeleteCurrentCol()">
            <span>✕</span> Delete column
        </div>
    `;

    menu.style.left = e.clientX + 'px';
    menu.style.top = e.clientY + 'px';

    document.body.appendChild(menu);

    // Close menu on click outside
    setTimeout(() => {
        document.addEventListener('click', closeTableContextMenu, { once: true });
    }, 0);
}

function closeTableContextMenu() {
    const menu = document.querySelector('.table-context-menu');
    if (menu) menu.remove();
    tableContextMenuTarget = null;
}

function tableInsertRowAbove() {
    if (!tableContextMenuTarget) return;
    const { elementId, rowIndex } = tableContextMenuTarget;
    insertTableRowAt(elementId, rowIndex);
    closeTableContextMenu();
}

function tableInsertRowBelow() {
    if (!tableContextMenuTarget) return;
    const { elementId, rowIndex } = tableContextMenuTarget;
    insertTableRowAt(elementId, rowIndex + 1);
    closeTableContextMenu();
}

function tableInsertColLeft() {
    if (!tableContextMenuTarget) return;
    const { elementId, colIndex } = tableContextMenuTarget;
    insertTableColAt(elementId, colIndex);
    closeTableContextMenu();
}

function tableInsertColRight() {
    if (!tableContextMenuTarget) return;
    const { elementId, colIndex } = tableContextMenuTarget;
    insertTableColAt(elementId, colIndex + 1);
    closeTableContextMenu();
}

function tableDeleteCurrentRow() {
    if (!tableContextMenuTarget) return;
    const { elementId, rowIndex } = tableContextMenuTarget;
    tableDeleteRow(elementId, rowIndex);
    closeTableContextMenu();
}

function tableDeleteCurrentCol() {
    if (!tableContextMenuTarget) return;
    const { elementId, colIndex } = tableContextMenuTarget;
    tableDeleteColumn(elementId, colIndex);
    closeTableContextMenu();
}

function insertTableRowAt(elementId, atIndex) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    if (element.columns && element.rows) {
        const newRowId = generateId('row');
        const cells = {};
        element.columns.forEach(col => {
            cells[col.id] = '';
        });

        element.rows.splice(atIndex, 0, {
            id: newRowId,
            height: 30,
            cells: cells
        });

        element.height = element.rows.reduce((sum, r) => sum + (r.height || 30), 0);
    } else if (element.cellData) {
        const newRow = Array(element.cols || 1).fill('');
        element.cellData.splice(atIndex, 0, newRow);
        element.rows = element.cellData.length;
        element.height = element.rows * 30;
    }

    rerenderTable(elementId);
}

function insertTableColAt(elementId, atIndex) {
    const element = elements.find(el => el.id === elementId);
    if (!element) return;

    if (element.columns && element.rows) {
        const newColId = generateId('col');
        const newWidth = 100 / (element.columns.length + 1);

        // Redistribute column widths
        element.columns.forEach(col => {
            col.width = col.width * (element.columns.length / (element.columns.length + 1));
        });

        element.columns.splice(atIndex, 0, {
            id: newColId,
            width: newWidth
        });

        // Add cells to all rows
        element.rows.forEach(row => {
            row.cells[newColId] = '';
        });

        element.cols = element.columns.length;
        element.width = element.columns.length * 100;
    } else if (element.colWidths) {
        element.cols = (element.cols || 1) + 1;
        const newWidth = 100 / element.cols;
        element.colWidths.forEach((w, i) => element.colWidths[i] = w * ((element.cols - 1) / element.cols));
        element.colWidths.splice(atIndex, 0, newWidth);

        if (element.cellData) {
            element.cellData.forEach(row => row.splice(atIndex, 0, ''));
        }
        element.width = element.cols * 100;
    }

    rerenderTable(elementId);
}

// ========================================
// INITIALIZE TABLE EVENTS ON LOAD
// ========================================

function initializeTableEvents() {
    document.querySelectorAll('.canvas-element').forEach(el => {
        const elementId = el.id;
        const element = elements.find(e => e.id === elementId);
        if (element && element.type === 'table') {
            bindTableEvents(elementId);
        }
    });
}

// Track active table cell for variable insertion
let activeTableCell = null;

// Insert Variable with formatting options
function insertVariable(variable, format = 'normal') {
    // Format the variable based on option
    let formattedVariable = variable;
    let htmlVariable = variable;

    switch (format) {
        case 'uppercase':
            // Use :upper suffix for PDF replacement - e.g. @{{student_name:upper@}}
            formattedVariable = variable.replace('@}}', ':upper@}}');
            // Use the :upper variable so PDF can replace it with uppercase value
            htmlVariable = '<span class="var-uppercase" style="text-transform: uppercase;">' + formattedVariable + '</span>';
            break;
        case 'bold':
            formattedVariable = '<strong>' + variable + '</strong>';
            htmlVariable = '<strong>' + variable + '</strong>';
            break;
        case 'bold-uppercase':
            // Use :upper suffix for PDF replacement
            formattedVariable = variable.replace('@}}', ':upper@}}');
            htmlVariable = '<strong class="var-bold-uppercase" style="text-transform: uppercase;">' + formattedVariable + '</strong>';
            break;
        default:
            formattedVariable = variable;
            htmlVariable = variable;
    }

    // Check if there's an active table cell
    if (activeTableCell) {
        insertVariableIntoTableCell(htmlVariable, formattedVariable);
        return;
    }

    // Check for focused table cell in the document
    const focusedCell = document.activeElement;
    if (focusedCell && focusedCell.classList.contains('table-cell')) {
        insertVariableIntoCell(focusedCell, htmlVariable, formattedVariable);
        return;
    }

    // Fall back to text element
    if (!selectedElement) {
        showToast('Select a text element or table cell first', 'error');
        return;
    }

    const element = elements.find(e => e.id === selectedElement);

    // Check if selected element is a table - find active cell within it
    if (element && element.type === 'table') {
        const tableWrapper = document.querySelector('#' + selectedElement + ' .table-wrapper');
        if (tableWrapper) {
            const firstCell = tableWrapper.querySelector('.table-cell');
            if (firstCell) {
                insertVariableIntoCell(firstCell, htmlVariable, formattedVariable);
                return;
            }
        }
        showToast('Click inside a table cell first', 'error');
        return;
    }

    if (!element || element.type !== 'text') {
        showToast('Select a text element or table cell first', 'error');
        return;
    }

    const textEl = document.querySelector('#' + selectedElement + ' .text-element');

    // Check if text element is being edited (has focus)
    if (textEl && textEl.isContentEditable && document.activeElement === textEl) {
        // Insert at cursor position using HTML
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();

            // Create a temporary element to parse HTML
            const temp = document.createElement('span');
            temp.innerHTML = htmlVariable;

            // Insert the nodes
            const frag = document.createDocumentFragment();
            while (temp.firstChild) {
                frag.appendChild(temp.firstChild);
            }
            range.insertNode(frag);
            range.collapse(false);

            // Update element content from the edited HTML
            element.content = textEl.innerHTML;
            element.isRichText = true;
        }
    } else {
        // Append to end
        element.content += htmlVariable;
        element.isRichText = true;
        textEl.innerHTML = element.content;
    }

    showToast('Variable inserted', 'success');
}

// Insert variable into a table cell
function insertVariableIntoCell(cell, htmlVariable, formattedVariable) {
    // Focus the cell if not already
    cell.focus();

    // Insert at cursor position or append
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);

        // Check if selection is within this cell
        if (cell.contains(range.commonAncestorContainer)) {
            range.deleteContents();

            const temp = document.createElement('span');
            temp.innerHTML = htmlVariable;

            const frag = document.createDocumentFragment();
            while (temp.firstChild) {
                frag.appendChild(temp.firstChild);
            }
            range.insertNode(frag);
            range.collapse(false);
        } else {
            // Append to cell
            cell.innerHTML += htmlVariable;
        }
    } else {
        // Append to cell
        cell.innerHTML += htmlVariable;
    }

    // Update element data
    updateTableCellData(cell);
    showToast('Variable inserted', 'success');
}

// Insert variable into the tracked active table cell
function insertVariableIntoTableCell(htmlVariable, formattedVariable) {
    if (!activeTableCell) return;

    insertVariableIntoCell(activeTableCell, htmlVariable, formattedVariable);
}

// Update table element data from cell
function updateTableCellData(cell) {
    const elementId = cell.closest('.table-wrapper')?.dataset.elementId;
    if (!elementId) return;

    const element = elements.find(e => e.id === elementId);
    if (!element) return;

    const rowId = cell.dataset.rowId;
    const colId = cell.dataset.colId;
    const rowIndex = parseInt(cell.dataset.rowIndex);
    const colIndex = parseInt(cell.dataset.colIndex);

    // Update based on schema type
    if (element.columns && element.rows && Array.isArray(element.rows)) {
        // New schema
        const row = element.rows.find(r => r.id === rowId);
        if (row && row.cells) {
            row.cells[colId] = cell.innerHTML;
        }
    } else {
        // Legacy schema
        if (!element.cellData) element.cellData = {};
        const key = rowIndex + '_' + colIndex;
        element.cellData[key] = cell.innerHTML;
    }
}

// Element Actions
function deleteElement() {
    if (!selectedElement) return;
    saveState(); // Save state for undo

    const el = document.getElementById(selectedElement);
    if (el) el.remove();

    elements = elements.filter(e => e.id !== selectedElement);
    selectedElements = selectedElements.filter(id => id !== selectedElement);
    selectedElement = null;

    document.getElementById('emptyState').classList.add('show');
    document.getElementById('elementProps').style.display = 'none';
    document.getElementById('contextMenu').classList.remove('show');

    renderLayers();
}

// Delete all selected elements
function deleteSelectedElements() {
    if (selectedElements.length === 0) return;
    saveState(); // Save state for undo

    // Remove all selected elements from DOM and data
    selectedElements.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.remove();
    });

    elements = elements.filter(e => !selectedElements.includes(e.id));

    // Clear selection
    selectedElements = [];
    selectedElement = null;

    document.getElementById('emptyState').classList.add('show');
    document.getElementById('elementProps').style.display = 'none';
    document.getElementById('contextMenu').classList.remove('show');

    renderLayers();
}

function duplicateElement() {
    if (!selectedElement) return;

    const original = elements.find(e => e.id === selectedElement);
    if (!original) return;

    const id = 'el_' + (++elementIdCounter);
    const clone = { ...original, id: id, x: original.x + 20, y: original.y + 20 };
    elements.push(clone);
    renderElement(clone);
    selectElement(id);

    document.getElementById('contextMenu').classList.remove('show');
    renderLayers();
}

function bringToFront() {
    if (!selectedElement) return;
    const el = document.getElementById(selectedElement);
    if (el) canvas.appendChild(el);
    document.getElementById('contextMenu').classList.remove('show');
}

function sendToBack() {
    if (!selectedElement) return;
    const el = document.getElementById(selectedElement);
    if (el) canvas.insertBefore(el, canvas.firstChild);
    document.getElementById('contextMenu').classList.remove('show');
}

// Zoom
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

function zoomFit() {
    const container = document.getElementById('canvasContainer');
    const containerRect = container.getBoundingClientRect();

    // Get available space (with some padding)
    const availableWidth = containerRect.width - 80;
    const availableHeight = containerRect.height - 80;

    // Get current canvas dimensions
    const canvasWidth = canvas.offsetWidth;
    const canvasHeight = canvas.offsetHeight;

    // Calculate zoom to fit both width and height
    const zoomWidth = (availableWidth / canvasWidth) * 100;
    const zoomHeight = (availableHeight / canvasHeight) * 100;

    // Use the smaller zoom to ensure it fits in both dimensions
    currentZoom = Math.min(zoomWidth, zoomHeight);

    // Clamp zoom between 25% and 200%
    currentZoom = Math.max(25, Math.min(200, Math.round(currentZoom)));

    applyZoom();
}

function applyZoom() {
    canvas.style.transform = `scale(${currentZoom / 100})`;
    canvas.style.transformOrigin = 'top center';
    document.getElementById('zoomLevel').textContent = currentZoom + '%';
}

// Save & Load
async function saveTemplate() {
    const size = pageSizes[pageSettings.size];
    let width, height;
    if (pageSettings.orientation === 'portrait') {
        width = size.width;
        height = size.height;
    } else {
        width = size.height;
        height = size.width;
    }

    try {
        const response = await fetch('{{ route("admin.documents.sal.canvas.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                canvas_elements: elements,
                canvas_width: width,
                canvas_height: height,
                settings: {
                    page_size: pageSettings.size,
                    orientation: pageSettings.orientation,
                    margins: pageSettings.margins,
                    background: pageSettings.background
                }
            })
        });

        if (response.ok) {
            showToast('Template saved successfully', 'success');
        } else {
            throw new Error('Save failed');
        }
    } catch (error) {
        showToast('Failed to save template', 'error');
    }
}

function loadTemplate() {
    @if($template->canvas_elements)
    const savedElements = @json($template->canvas_elements);
    if (Array.isArray(savedElements)) {
        savedElements.forEach(element => {
            elementIdCounter = Math.max(elementIdCounter, parseInt(element.id.replace('el_', '')) || 0);
            elements.push(element);
            renderElement(element);
        });
        // Sync loaded elements to the current page
        const currentPage = pages.find(p => p.id === currentPageId);
        if (currentPage) {
            currentPage.elements = elements;
        }
        // Update z-indices after loading all elements
        updateElementZIndices();
        renderLayers();
        // Initialize table events for any loaded tables
        initializeTableEvents();
    }
    @endif
}

function resetCanvas() {
    if (confirm('Are you sure you want to reset the canvas? This will remove all elements.')) {
        elements.forEach(el => {
            const dom = document.getElementById(el.id);
            if (dom) dom.remove();
        });
        elements = [];
        selectedElement = null;
        elementIdCounter = 0;
        document.getElementById('emptyState').classList.add('show');
        document.getElementById('elementProps').style.display = 'none';

        // Sync with current page
        const currentPage = pages.find(p => p.id === currentPageId);
        if (currentPage) {
            currentPage.elements = [];
        }
        renderLayers();
    }
}

function previewPDF() {
    window.open('{{ route("admin.documents.sal.preview") }}', '_blank');
}

// Toast
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// ==================== LAYOUT PANEL ====================

// Show layout panel
function showLayoutPanel() {
    currentPanel = 'layout';
    deselectAll();

    // Update panel title
    document.getElementById('panelTitle').textContent = 'Page Layout';

    // Hide properties elements and shape panel
    document.getElementById('emptyState').classList.remove('show');
    document.getElementById('elementProps').style.display = 'none';
    document.getElementById('variablesSection').style.display = 'none';
    document.getElementById('shapePanel').style.display = 'none';

    // Show layout panel
    document.getElementById('layoutPanel').style.display = 'block';

    // Highlight layout button
    document.querySelectorAll('.tool-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('btnLayout').classList.add('active');
}

// Show properties panel
function showPropertiesPanel() {
    currentPanel = 'properties';

    // Update panel title
    document.getElementById('panelTitle').textContent = 'Properties';

    // Hide layout and shape panels
    document.getElementById('layoutPanel').style.display = 'none';
    document.getElementById('shapePanel').style.display = 'none';

    // Show variables section
    document.getElementById('variablesSection').style.display = 'block';

    // Remove tool button highlights
    document.getElementById('btnLayout').classList.remove('active');
    document.getElementById('btnShape').classList.remove('active');

    // Show appropriate properties state
    if (selectedElement) {
        document.getElementById('emptyState').classList.remove('show');
        document.getElementById('elementProps').style.display = 'block';
    } else {
        document.getElementById('emptyState').classList.add('show');
        document.getElementById('elementProps').style.display = 'none';
    }
}

// Show shape panel
function showShapePanel() {
    currentPanel = 'shape';
    deselectAll();

    // Update panel title
    document.getElementById('panelTitle').textContent = 'Shapes';

    // Hide properties elements
    document.getElementById('emptyState').classList.remove('show');
    document.getElementById('elementProps').style.display = 'none';
    document.getElementById('variablesSection').style.display = 'none';
    document.getElementById('layoutPanel').style.display = 'none';

    // Show shape panel
    document.getElementById('shapePanel').style.display = 'block';

    // Highlight shape button
    document.querySelectorAll('.tool-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('btnShape').classList.add('active');
}

// Add line from shape panel (uses default settings)
function addLineFromPanel() {
    const color = document.getElementById('defaultLineColor').value;
    const thickness = parseFloat(document.getElementById('defaultLineThickness').value) || 1;

    const id = 'el_' + (++elementIdCounter);
    const element = {
        id: id,
        type: 'line',
        x: 50,
        y: 100,
        width: 200,
        height: 20,
        color: color,
        thickness: thickness,
        rotation: 0
    };
    elements.push(element);
    renderElement(element);
    selectElement(id);
}

// Add box from shape panel (uses default settings)
function addBoxFromPanel() {
    const fill = document.getElementById('defaultBoxFill').value;
    const borderColor = document.getElementById('defaultBoxBorder').value;
    const borderWidth = parseInt(document.getElementById('defaultBoxBorderWidth').value) || 1;

    const id = 'el_' + (++elementIdCounter);
    const element = {
        id: id,
        type: 'box',
        x: 50,
        y: 50,
        width: 150,
        height: 100,
        fill: fill,
        borderColor: borderColor,
        borderWidth: borderWidth
    };
    elements.push(element);
    renderElement(element);
    selectElement(id);
}

// Update page size
function updatePageSize() {
    const sizeKey = document.getElementById('pageSize').value;
    pageSettings.size = sizeKey;
    applyPageSettings();
}

// Set orientation
function setOrientation(orientation) {
    pageSettings.orientation = orientation;

    document.getElementById('btnPortrait').classList.toggle('active', orientation === 'portrait');
    document.getElementById('btnLandscape').classList.toggle('active', orientation === 'landscape');

    applyPageSettings();
}

// Update margins
function updateMargins() {
    pageSettings.margins = {
        top: parseInt(document.getElementById('marginTop').value) || 0,
        bottom: parseInt(document.getElementById('marginBottom').value) || 0,
        left: parseInt(document.getElementById('marginLeft').value) || 0,
        right: parseInt(document.getElementById('marginRight').value) || 0
    };
    updateMarginGuides();
}

// Apply margin presets
function applyMarginPreset(preset) {
    const presets = {
        normal: { top: 25, bottom: 25, left: 25, right: 25 },
        narrow: { top: 13, bottom: 13, left: 13, right: 13 },
        wide: { top: 25, bottom: 25, left: 50, right: 50 }
    };

    pageSettings.margins = { ...presets[preset] };

    document.getElementById('marginTop').value = pageSettings.margins.top;
    document.getElementById('marginBottom').value = pageSettings.margins.bottom;
    document.getElementById('marginLeft').value = pageSettings.margins.left;
    document.getElementById('marginRight').value = pageSettings.margins.right;

    updateMarginGuides();
    showToast(`Applied ${preset} margins`, 'success');
}

// Update page background
function updatePageBackground() {
    pageSettings.background = document.getElementById('pageBgColor').value;
    canvas.style.background = pageSettings.background;
}

// Apply page settings to canvas
function applyPageSettings() {
    const size = pageSizes[pageSettings.size];
    let width, height;

    if (pageSettings.orientation === 'portrait') {
        width = size.width;
        height = size.height;
    } else {
        width = size.height;
        height = size.width;
    }

    canvas.style.width = width + 'px';
    canvas.style.height = height + 'px';

    updateMarginGuides();
}

// Update margin guides on canvas
function updateMarginGuides() {
    // Remove existing guides
    document.querySelectorAll('.margin-guide').forEach(g => g.remove());

    const mmToPx = 2.83465; // 1mm = 2.83465px at 72 DPI
    const top = pageSettings.margins.top * mmToPx;
    const bottom = pageSettings.margins.bottom * mmToPx;
    const left = pageSettings.margins.left * mmToPx;
    const right = pageSettings.margins.right * mmToPx;

    const canvasWidth = parseInt(canvas.style.width) || 595;
    const canvasHeight = parseInt(canvas.style.height) || 842;

    const guide = document.createElement('div');
    guide.className = 'margin-guide';
    guide.style.top = top + 'px';
    guide.style.left = left + 'px';
    guide.style.width = (canvasWidth - left - right) + 'px';
    guide.style.height = (canvasHeight - top - bottom) + 'px';
    canvas.appendChild(guide);
}

// Load layout settings
function loadLayoutSettings() {
    @if($template->settings)
    const settings = @json($template->settings);
    if (settings) {
        if (settings.page_size) {
            pageSettings.size = settings.page_size;
            document.getElementById('pageSize').value = settings.page_size;
        }
        if (settings.orientation) {
            pageSettings.orientation = settings.orientation;
            setOrientation(settings.orientation);
        }
        if (settings.margins) {
            pageSettings.margins = settings.margins;
            document.getElementById('marginTop').value = settings.margins.top || 25;
            document.getElementById('marginBottom').value = settings.margins.bottom || 25;
            document.getElementById('marginLeft').value = settings.margins.left || 25;
            document.getElementById('marginRight').value = settings.margins.right || 25;
        }
        if (settings.background) {
            pageSettings.background = settings.background;
            document.getElementById('pageBgColor').value = settings.background;
            canvas.style.background = settings.background;
        }
        applyPageSettings();
    }
    @endif
}

// ==================== PAGES & LAYERS ====================

// Initialize pages and layers
function initPagesAndLayers() {
    // Set elements to reference current page's elements
    const currentPage = pages.find(p => p.id === currentPageId);
    if (currentPage) {
        elements = currentPage.elements;
    }
    renderPages();
    renderLayers();
}

// Add new page
function addPage() {
    saveState(); // Save state for undo
    pageIdCounter++;
    const newPage = {
        id: 'page_' + pageIdCounter,
        name: 'Page ' + pageIdCounter,
        elements: []
    };
    pages.push(newPage);
    switchPage(newPage.id);
    renderPages();
}

// Switch to a different page
function switchPage(pageId) {
    // Save current page's elements
    const currentPage = pages.find(p => p.id === currentPageId);
    if (currentPage) {
        currentPage.elements = [...elements];
    }

    // Clear canvas
    deselectAll();
    document.querySelectorAll('.canvas-element').forEach(el => el.remove());

    // Switch to new page
    currentPageId = pageId;
    const newPage = pages.find(p => p.id === pageId);
    if (newPage) {
        elements = newPage.elements;
        // Re-render all elements on the new page
        elements.forEach(element => {
            renderElement(element);
        });
    }

    renderPages();
    renderLayers();
}

// Delete a page
function deletePage(pageId) {
    if (pages.length <= 1) {
        showToast('Cannot delete the only page', 'error');
        return;
    }

    const index = pages.findIndex(p => p.id === pageId);
    if (index === -1) return;

    pages.splice(index, 1);

    // If deleting current page, switch to first available page
    if (pageId === currentPageId) {
        switchPage(pages[0].id);
    } else {
        renderPages();
    }
}

// Rename a page
function renamePage(pageId, newName) {
    const page = pages.find(p => p.id === pageId);
    if (page) {
        page.name = newName || page.name;
        renderPages();
    }
}

// Render pages list
function renderPages() {
    const pagesList = document.getElementById('pagesList');
    pagesList.innerHTML = '';

    pages.forEach(page => {
        const li = document.createElement('li');
        li.className = 'panel-left-item' + (page.id === currentPageId ? ' active' : '');
        li.innerHTML = `
            <svg class="item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <line x1="7" y1="8" x2="17" y2="8"/>
                <line x1="7" y1="12" x2="14" y2="12"/>
            </svg>
            <span class="item-name">${page.name}</span>
            <div class="item-actions">
                ${pages.length > 1 ? `
                <button class="item-action" onclick="event.stopPropagation(); deletePage('${page.id}')" title="Delete">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
                ` : ''}
            </div>
        `;
        li.onclick = () => switchPage(page.id);
        pagesList.appendChild(li);
    });
}

// Render layers list
function renderLayers() {
    const layersList = document.getElementById('layersList');
    const layersEmpty = document.getElementById('layersEmpty');

    layersList.innerHTML = '';

    if (elements.length === 0) {
        layersEmpty.style.display = 'block';
        return;
    }

    layersEmpty.style.display = 'none';

    // Render in reverse order (top layers first)
    [...elements].reverse().forEach((element, displayIndex) => {
        const li = document.createElement('li');
        li.className = 'panel-left-item' + (element.id === selectedElement ? ' active' : '');
        li.dataset.elementId = element.id;
        li.draggable = true;

        if (element.hidden) {
            li.classList.add('layer-item-hidden');
        }

        const icon = getLayerIcon(element.type);
        const name = getLayerName(element);

        li.innerHTML = `
            <div class="drag-handle" title="Drag to reorder">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="9" cy="6" r="1.5"/>
                    <circle cx="15" cy="6" r="1.5"/>
                    <circle cx="9" cy="12" r="1.5"/>
                    <circle cx="15" cy="12" r="1.5"/>
                    <circle cx="9" cy="18" r="1.5"/>
                    <circle cx="15" cy="18" r="1.5"/>
                </svg>
            </div>
            <svg class="item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                ${icon}
            </svg>
            <span class="item-name">${name}</span>
            <div class="item-actions">
                <button class="item-action ${element.hidden ? 'hidden-layer' : ''}" onclick="event.stopPropagation(); toggleLayerVisibility('${element.id}')" title="${element.hidden ? 'Show' : 'Hide'}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        ${element.hidden ?
                            '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>' :
                            '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
                        }
                    </svg>
                </button>
            </div>
        `;

        // Drag events
        li.addEventListener('dragstart', handleLayerDragStart);
        li.addEventListener('dragend', handleLayerDragEnd);
        li.addEventListener('dragover', handleLayerDragOver);
        li.addEventListener('dragleave', handleLayerDragLeave);
        li.addEventListener('drop', handleLayerDrop);

        li.onclick = (e) => {
            if (!e.target.closest('.drag-handle') && !e.target.closest('.item-action')) {
                selectElement(element.id);
            }
        };
        layersList.appendChild(li);
    });
}

// Layer drag and drop state
let draggedLayerId = null;

function handleLayerDragStart(e) {
    draggedLayerId = this.dataset.elementId;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', draggedLayerId);
}

function handleLayerDragEnd(e) {
    this.classList.remove('dragging');
    // Clear all drag-over classes
    document.querySelectorAll('.panel-left-item').forEach(item => {
        item.classList.remove('drag-over', 'drag-over-bottom');
    });
    draggedLayerId = null;
}

function handleLayerDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';

    if (this.dataset.elementId === draggedLayerId) return;

    // Clear other drag-over classes
    document.querySelectorAll('.panel-left-item').forEach(item => {
        if (item !== this) {
            item.classList.remove('drag-over', 'drag-over-bottom');
        }
    });

    // Determine if dropping above or below based on mouse position
    const rect = this.getBoundingClientRect();
    const midpoint = rect.top + rect.height / 2;

    if (e.clientY < midpoint) {
        this.classList.add('drag-over');
        this.classList.remove('drag-over-bottom');
    } else {
        this.classList.add('drag-over-bottom');
        this.classList.remove('drag-over');
    }
}

function handleLayerDragLeave(e) {
    this.classList.remove('drag-over', 'drag-over-bottom');
}

function handleLayerDrop(e) {
    e.preventDefault();
    e.stopPropagation();

    const targetId = this.dataset.elementId;
    if (!draggedLayerId || draggedLayerId === targetId) return;

    // Determine drop position (above or below)
    const rect = this.getBoundingClientRect();
    const midpoint = rect.top + rect.height / 2;
    const dropAbove = e.clientY < midpoint;

    // Reorder elements
    reorderLayer(draggedLayerId, targetId, dropAbove);

    // Clear drag-over classes
    this.classList.remove('drag-over', 'drag-over-bottom');
}

function reorderLayer(draggedId, targetId, dropAbove) {
    // Find indices in the elements array
    // Note: layers are displayed in reverse order, so we need to account for that
    const draggedIndex = elements.findIndex(e => e.id === draggedId);
    const targetIndex = elements.findIndex(e => e.id === targetId);

    if (draggedIndex === -1 || targetIndex === -1) return;

    // Remove the dragged element
    const [draggedElement] = elements.splice(draggedIndex, 1);

    // Calculate new index
    // Since layers are displayed in reverse (top layer = last in array shown first),
    // dropping "above" in the UI means placing after in the array (higher z-index)
    // dropping "below" in the UI means placing before in the array (lower z-index)
    let newIndex = elements.findIndex(e => e.id === targetId);

    if (dropAbove) {
        // Drop above in UI = insert after in array (higher z-index)
        newIndex = newIndex + 1;
    }

    // Insert at new position
    elements.splice(newIndex, 0, draggedElement);

    // Update z-index for all elements on the canvas
    updateElementZIndices();

    // Re-render layers
    renderLayers();

    // Keep the dragged element selected
    selectElement(draggedId);
}

function updateElementZIndices() {
    // Update z-index based on array position (later = higher z-index = on top)
    elements.forEach((element, index) => {
        const el = document.getElementById(element.id);
        if (el) {
            el.style.zIndex = index + 1;
        }
    });
}

// Get icon for layer type
function getLayerIcon(type) {
    switch (type) {
        case 'text':
            return '<path d="M4 7V4h16v3M9 20h6M12 4v16"/>';
        case 'image':
            return '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>';
        case 'line':
            return '<line x1="5" y1="12" x2="19" y2="12"/>';
        case 'box':
            return '<rect x="3" y="3" width="18" height="18" rx="2"/>';
        case 'table':
            return '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/>';
        default:
            return '<rect x="3" y="3" width="18" height="18"/>';
    }
}

// Get display name for layer
function getLayerName(element) {
    switch (element.type) {
        case 'text':
            // Show first few characters of text content
            const text = element.content || 'Text';
            return text.substring(0, 20) + (text.length > 20 ? '...' : '');
        case 'image':
            return 'Image';
        case 'line':
            return 'Line';
        case 'box':
            return 'Box';
        case 'table':
            return `Table (${element.rows}x${element.cols})`;
        default:
            return 'Element';
    }
}

// Toggle layer visibility
function toggleLayerVisibility(elementId) {
    const element = elements.find(e => e.id === elementId);
    if (!element) return;

    element.hidden = !element.hidden;

    const el = document.getElementById(elementId);
    if (el) {
        el.style.display = element.hidden ? 'none' : 'block';
    }

    renderLayers();
}

// Update layers when elements change
function updateLayers() {
    renderLayers();
}

// Override selectElement to also update layers
const originalSelectElement = selectElement;
selectElement = function(id) {
    originalSelectElement(id);
    renderLayers();
};

// Override deselectAll to also update layers
const originalDeselectAll = deselectAll;
deselectAll = function() {
    originalDeselectAll();
    renderLayers();
};

// Initialize layout on load
document.addEventListener('DOMContentLoaded', function() {
    loadLayoutSettings();
    updateMarginGuides();
    initPagesAndLayers();

    // Save initial state for undo functionality (after short delay to ensure everything is loaded)
    setTimeout(function() {
        saveState();
        // Clear undo history but keep the initial state as baseline
        // This ensures first undo goes back to initial state
        updateUndoRedoButtons();
    }, 100);
});
</script>
@endpush
