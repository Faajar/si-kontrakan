@props(['status'])

@php
    $warna = match ($status) {
        \App\Enums\StatusUnit::Kosong => 'bg-gray-100 text-gray-700',
        \App\Enums\StatusUnit::Terisi => 'bg-green-100 text-green-800',
        \App\Enums\StatusUnit::Perbaikan => 'bg-amber-100 text-amber-800',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2 py-0.5 text-xs font-medium $warna"]) }}>{{ $status->label() }}</span>
