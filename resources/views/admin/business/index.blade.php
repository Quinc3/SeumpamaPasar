@extends('layouts.admin')

@section('title', 'Verifikasi Bisnis')

@section('content')
    @if (session('status'))
        <x-alert variant="success">Status verifikasi diperbarui.</x-alert>
    @endif

    @if ($profiles->isEmpty())
        <x-card padding="none">
            <x-empty-state icon="credit-card" title="Belum ada profil bisnis" description="Pengajuan profil bisnis akan muncul di sini." />
        </x-card>
    @else
        <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-surface-50 text-left text-xs uppercase text-surface-600">
                        <tr>
                            <th class="px-4 py-3">Perusahaan</th>
                            <th class="px-4 py-3">Pemilik</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($profiles as $p)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-900">{{ $p->company_name }}</p>
                                    <p class="text-xs text-surface-500">{{ $p->phone }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-surface-900">{{ $p->user->name }}</p>
                                    <p class="text-xs text-surface-500">{{ $p->user->email }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($p->verified_at)
                                        <x-badge variant="success">Terverifikasi</x-badge>
                                    @else
                                        <x-badge variant="warning">Menunggu</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($p->verified_at)
                                        <form method="POST" action="{{ route('admin.business.unverify', $p->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-600 hover:underline">Cabut</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.business.verify', $p->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-primary-700 hover:underline font-medium">Verifikasi</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-2">{{ $profiles->links() }}</div>
    @endif
@endsection