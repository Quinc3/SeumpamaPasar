@extends('layouts.admin')

@section('title', 'Afiliasi')

@section('content')
    @if (session('status'))
        <x-alert variant="success">Status afiliasi diperbarui.</x-alert>
    @endif

    @if ($affiliates->isEmpty())
        <x-card padding="none">
            <x-empty-state icon="user-circle" title="Belum ada afiliasi" />
        </x-card>
    @else
        <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-surface-50 text-left text-xs uppercase text-surface-600">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Komisi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($affiliates as $a)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="text-surface-900">{{ $a->user->name }}</p>
                                    <p class="text-xs text-surface-500">{{ $a->user->email }}</p>
                                </td>
                                <td class="px-4 py-3 font-mono">{{ $a->referral_code }}</td>
                                <td class="px-4 py-3">
                                    <x-price :amount="$a->commissions->where('status', \App\Enums\CommissionStatus::Approved)->sum('amount')" class="text-sm" />
                                </td>
                                <td class="px-4 py-3">
                                    @if ($a->status === 'active')
                                        <x-badge variant="success">Aktif</x-badge>
                                    @else
                                        <x-badge variant="danger">Ditangguhkan</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($a->status === 'active')
                                        <form method="POST" action="{{ route('admin.affiliates.suspend', $a->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-600 hover:underline">Tangguhkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.affiliates.activate', $a->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-primary-700 hover:underline">Aktifkan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-2">{{ $affiliates->links() }}</div>
    @endif
@endsection