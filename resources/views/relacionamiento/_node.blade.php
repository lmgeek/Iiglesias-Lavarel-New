@php($depth ??= 0)
<li class="tree-leaf">
    <div class="tree-card">
        <span class="avatar avatar-sm">{{ mb_substr($node['user']->fullname ?? '?', 0, 1) }}</span>
        <div class="tree-info">
            <div class="tree-name">{{ $node['user']->fullname ?? 'Sin usuario' }}</div>
            <div class="tree-meta">
                @if (($node['user']->lider_celula ?? null) === 'Si')
                    <span class="badge badge-cat">Facilitador</span>
                @else
                    {{ $node['user']->email ?? 'Sin email' }}
                @endif
            </div>
        </div>
        @if ($node['totalBelow'] > 0)
            <span class="badge badge-gray" title="Discípulos que le corresponden">{{ $node['totalBelow'] }} en su red</span>
        @endif
    </div>

    @if (count($node['children']))
        <ul>
            @foreach ($node['children'] as $child)
                @include('relacionamiento._node', ['node' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>