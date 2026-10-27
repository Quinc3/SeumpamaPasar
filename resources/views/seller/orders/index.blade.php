@extends('layouts.app')

@section('title', 'Pesanan Masuk')

@section('content')
    <x-page-wrapper>
        @if (session('status'))
            <x-alert variant="success">{{ session('status') }}</x-alert>
        @endif

        @if ($orders->isEmpty())
            <x-card padding="none">
                <x-empty-state icon="truck" title="Belum ada pesanan" description="Pesanan dari pembeli akan muncul di sini." />
            </x-card>
        @else
            <form method="POST" action="{{ route('seller.orders.bulk-status') }}">
                @csrf
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <select name="action" class="rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="process">Ubah ke: Diproses</option>
                        <option value="ship">Ubah ke: Dikirim</option>
                    </select>
                    <x-primary-button type="submit">Terapkan ke yang dipilih</x-primary-button>
                    <span id="bulk-count" class="text-sm text-surface-500">0 dipilih</span>
                    <span class="text-xs text-surface-400 ml-auto">Hanya pesanan yang bisa diproses dapat dipilih</span>
                </div>

                <div class="space-y-2">
                    @foreach ($orders as $order)
                        @php
                            $selectable = in_array($order->status, [\App\Enums\OrderStatus::PendingProcessing, \App\Enums\OrderStatus::Processing], true);
                        @endphp
                        <div class="bg-white border border-surface-200 rounded-lg p-3 flex items-start gap-3">
                            <div class="pt-1">
                                @if ($selectable)
                                    <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="bulk-checkbox rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                @else
                                    <span class="inline-block w-4 h-4 rounded border border-surface-200 bg-surface-50" title="Tidak dapat diubah massal"></span>
                                @endif
                            </div>
                            <a href="{{ route('seller.orders.show', $order->id) }}" class="flex-1 min-w-0 flex items-start justify-between gap-3 hover:text-primary-700">
                                <div class="min-w-0">
                                    <p class="text-xs text-surface-500 font-mono">{{ $order->checkout->reference }}</p>
                                    <p class="text-sm font-semibold text-surface-900 mt-0.5">{{ $order->buyer->name }}</p>
                                    <p class="text-xs text-surface-600 mt-0.5">{{ $order->items->count() }} item · {{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <x-price :amount="$order->subtotal" class="text-sm" />
                                    <div class="mt-1"><x-status-badge :status="$order->status->value" /></div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </form>
            <div class="pt-2">{{ $orders->links() }}</div>
        @endif
    </x-page-wrapper>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const boxes = document.querySelectorAll('.bulk-checkbox');
            const counter = document.getElementById('bulk-count');
            function updateCount() {
                const n = document.querySelectorAll('.bulk-checkbox:checked').length;
                if (counter) counter.textContent = n + ' dipilih';
            }
            boxes.forEach(cb => cb.addEventListener('change', updateCount));
        });
    </script>
@endsection