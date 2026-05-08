@php
    $active = $active ?? '';
@endphp

<div class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10.5z"/></svg>
        </span>
        <span>الرئيسية</span>
    </a>
    <a href="{{ route('reports') }}" class="nav-item {{ $active === 'reports' ? 'active' : '' }}">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 21h3V10H5v11zm5 0h3V4h-3v17zm5 0h3v-7h-3v7z"/></svg>
        </span>
        <span>التقارير</span>
    </a>
    <a href="{{ route('data-entry') }}" class="nav-item {{ $active === 'data-entry' ? 'active' : '' }}">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        </span>
        <span>إدخال البيانات</span>
    </a>
    <a href="{{ route('seizures') }}" class="nav-item {{ $active === 'seizures' ? 'active' : '' }}">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 2h9a3 3 0 0 1 3 3v2h2a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2V5a3 3 0 0 1 3-3zm9 5V5a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v1h10zm-1 2H5v11h14V9h-1z"/></svg>
        </span>
        <span>السجل</span>
    </a>
    <a href="{{ route('settings') }}" class="nav-item {{ $active === 'settings' ? 'active' : '' }}">
        <span class="nav-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.4 12.9c.04-.3.06-.6.06-.9s-.02-.6-.06-.9l2.11-1.65a.5.5 0 0 0 .12-.64l-2-3.46a.5.5 0 0 0-.6-.22l-2.49 1a7.5 7.5 0 0 0-1.56-.9l-.38-2.65A.5.5 0 0 0 13.4 3h-4.8a.5.5 0 0 0-.49.42l-.38 2.65a7.5 7.5 0 0 0-1.56.9l-2.49-1a.5.5 0 0 0-.6.22l-2 3.46a.5.5 0 0 0 .12.64L4.6 11.1c-.04.3-.06.6-.06.9s.02.6.06.9L2.49 14.55a.5.5 0 0 0-.12.64l2 3.46c.14.24.43.34.68.22l2.49-1c.48.38 1.01.7 1.56.9l.38 2.65c.05.28.28.47.56.47h4.8c.28 0 .51-.19.56-.47l.38-2.65c.55-.2 1.08-.52 1.56-.9l2.49 1c.25.12.54.02.68-.22l2-3.46a.5.5 0 0 0-.12-.64l-2.11-1.65zM12 15.5A3.5 3.5 0 1 1 15.5 12 3.5 3.5 0 0 1 12 15.5z"/></svg>
        </span>
        <span>الإعدادات</span>
    </a>
</div>
