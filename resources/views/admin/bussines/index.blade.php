@extends('layouts.admin')

@section('title', 'Verifikasi Bisnis')

@section('content')
    @if (session('status'))
        <x-alert variant="success">Perubahan status verifikasi disimpan.</x-alert>
    @endif

    @if ($profiles->isEmpty())
        <x-card padding="none">
            <x-empty-state icon="user-circle" title="Belum ada profil bisnis" description="Pengajuan profil bisnis akan muncul di sini." />
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
                        @foreach ($profiles as $profile)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-900">{{ $profile->company_name }}</p>
                                    <p class="text-xs text-surface-500">{{ $profile->phone }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-surface-900">{{ $profile->user->name }}</p>
                                    <p class="text-xs text-surface-500">{{ $profile->user->email }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($profile->verified_at)
                                        <x-badge variant="success">Terverifikasi</x-badge>
                                    @else
                                        <x-badge variant="warning">Menunggu</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($profile->verified_at)
                                        <form method="POST" action="{{ route('admin.business.unverify', $profile->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-red-600 hover:underline">Cabut Verifikasi</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.business.verify', $profile->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-primary-700 hover:underline font-medium">Verifikasi</button>
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