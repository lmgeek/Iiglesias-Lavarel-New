@extends('layouts.app')

@section('title', 'Mi red')

@push('styles')
<style>
    .network-wrap {
        overflow-x: auto;
        padding: 6px 0 22px;
    }
    .tree, .tree ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
    }
    .tree {
        width: max-content;
        margin-inline: auto;
    }
    .tree ul {
        padding-top: 26px;
        position: relative;
    }
    .tree li {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        padding: 26px 8px 4px;
    }
    .tree > ul > li:first-child {
        padding-top: 0;
    }
    .tree li::before,
    .tree li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        width: 50%;
        height: 26px;
        border-top: 1.5px solid var(--border-medium);
    }
    .tree li::after {
        right: auto;
        left: 50%;
        border-left: 1.5px solid var(--border-medium);
    }
    .tree li:only-child::after,
    .tree li:only-child::before {
        display: none;
    }
    .tree li:first-child::before,
    .tree li:last-child::after {
        border-top: none;
    }
    .tree li:last-child::before {
        border-right: 1.5px solid var(--border-medium);
        border-radius: 0 6px 0 0;
    }
    .tree li:first-child::after {
        border-radius: 6px 0 0 0;
    }
    .tree ul ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        height: 26px;
        border-left: 1.5px solid var(--border-medium);
    }
    .tree-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-medium);
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(26, 23, 20, 0.05);
        width: max-content;
        max-width: 240px;
        transition: box-shadow .15s, border-color .15s;
    }
    .tree-card:hover {
        border-color: var(--cat-400);
        box-shadow: 0 4px 16px rgba(26, 23, 20, 0.09);
    }
    .tree-card.tree-me {
        background: var(--cat-50);
        border-color: var(--cat-300);
        box-shadow: 0 6px 20px rgba(212, 138, 48, 0.18);
    }
    .tree-info {
        min-width: 0;
    }
    .tree-name {
        font-weight: 500;
        font-size: 13.5px;
        line-height: 1.3;
    }
    .tree-meta {
        font-size: 11.5px;
        color: var(--text-tertiary);
        margin-top: 2px;
    }
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Mi red</h1>
        <p class="page-sub">{{ $total }} discípulos en toda tu línea de relacionamiento</p>
    </div>
    <span class="badge badge-cat">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg>
        Red completa
    </span>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Tu línea de relacionamiento</h2>
            <p class="card-sub">Vos, tus discípulos y cada discípulo de tu red</p>
        </div>
    </div>

    @if (count($tree) === 0)
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg>
            </div>
            <div class="empty-title">Tu red está vacía</div>
            <p>Asigna discípulos desde Mis discípulos para comenzar.</p>
        </div>
    @else
        <div class="network-wrap">
            <ul class="tree">
                <li>
                    <div class="tree-card tree-me">
                        <span class="avatar">{{ mb_substr($me->fullname ?? 'U', 0, 1) }}</span>
                        <div class="tree-info">
                            <div class="tree-name">{{ $me->fullname ?? 'Tú' }}</div>
                            <div class="tree-meta">{{ $total }} discípulo(s) en tu red</div>
                        </div>
                    </div>
                    <ul>
                        @foreach ($tree as $node)
                            @include('relacionamiento._node', ['node' => $node])
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    @endif
</div>
@endsection