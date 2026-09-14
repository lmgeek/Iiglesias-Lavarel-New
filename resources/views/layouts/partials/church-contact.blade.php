@php
    $phone = !empty($config->phone) ? trim($config->phone) : null;
    $email = !empty($config->email) ? trim($config->email) : null;

    $socials = [];
    $networks = [
        'instagram' => ['label' => 'Instagram', 'handle' => !empty($config->instagram) ? trim($config->instagram) : null, 'base' => 'https://instagram.com/'],
        'facebook' => ['label' => 'Facebook', 'handle' => !empty($config->facebook) ? trim($config->facebook) : null, 'base' => 'https://facebook.com/'],
        'tiktok' => ['label' => 'TikTok', 'handle' => !empty($config->tiktok) ? trim($config->tiktok) : null, 'base' => 'https://tiktok.com/'],
        'youtube' => ['label' => 'YouTube', 'handle' => !empty($config->youtube) ? trim($config->youtube) : null, 'base' => 'https://youtube.com/'],
    ];

    foreach (['instagram', 'facebook', 'tiktok', 'youtube'] as $key) {
        $item = $networks[$key];
        $handle = $item['handle'];
        if (empty($handle)) continue;
        $handle = ltrim($handle, '@');
        $url = preg_match('~^https?://~i', $handle) ? $handle : $item['base'] . $handle;
        $socials[] = ['key' => $key, 'label' => $item['label'], 'url' => $url, 'handle' => $item['handle']];
    }

    $hasContact = $phone || $email || count($socials) > 0;
@endphp

@if ($hasContact)
<div class="church-contact {{ $compact ?? false ? 'church-contact--compact' : '' }}">
    @if ($phone)
        <a href="tel:{{ preg_replace('/[^+\d]/', '', $phone) }}" class="church-contact-item">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <span>{{ $phone }}</span>
        </a>
    @endif
    @if ($email)
        <a href="mailto:{{ $email }}" class="church-contact-item">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <span>{{ $email }}</span>
        </a>
    @endif
    @foreach ($socials as $s)
        <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer" class="church-contact-item" title="{{ $s['label'] }} · {{ $s['handle'] }}">
            @if ($s['key'] === 'instagram')
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
            @elseif ($s['key'] === 'facebook')
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7h2.4l.4-3h-2.8V9.1c0-.9.3-1.6 2-1.6h1V4.8c-.4-.1-1.6-.2-2.5-.2-2.6 0-4.4 1.6-4.4 4.6V11H7v3h2.6v7h3.9z"/></svg>
            @elseif ($s['key'] === 'tiktok')
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 1 1-2.59-2.59c.27 0 .53.04.77.12V9.77a5.76 5.76 0 0 0-.77-.05 5.68 5.68 0 1 0 5.68 5.68V9.36a7.35 7.35 0 0 0 4.3 1.38V7.65a4.3 4.3 0 0 1-3.24-1.83z"/></svg>
            @else
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M21.5 7.2a2.5 2.5 0 0 0-1.76-1.77C18.1 5 12 5 12 5s-6.1 0-7.74.43A2.5 2.5 0 0 0 2.5 7.2 26.2 26.2 0 0 0 2 12c0 1.6.17 3.2.5 4.8a2.5 2.5 0 0 0 1.76 1.77C5.9 19 12 19 12 19s6.1 0 7.74-.43a2.5 2.5 0 0 0 1.76-1.77c.33-1.6.5-3.2.5-4.8 0-1.6-.17-3.2-.5-4.8zM10 15.5v-7l6 3.5-6 3.5z"/></svg>
            @endif
        </a>
    @endforeach
</div>
@endif