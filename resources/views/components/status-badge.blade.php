@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;

    $map = [
        // Order
        'pending_payment'    => ['label' => 'Menunggu Pembayaran', 'variant' => 'warning'],
        'pending_processing' => ['label' => 'Menunggu Diproses',   'variant' => 'info'],
        'processing'         => ['label' => 'Diproses',            'variant' => 'info'],
        'shipped'            => ['label' => 'Dikirim',             'variant' => 'primary'],
        'completed'          => ['label' => 'Selesai',             'variant' => 'success'],
        'cancelled'          => ['label' => 'Dibatalkan',          'variant' => 'danger'],

        // Payment
        'paid'   => ['label' => 'Dibayar', 'variant' => 'success'],
        'failed' => ['label' => 'Gagal',   'variant' => 'danger'],

        // Product
        'draft'          => ['label' => 'Draf',            'variant' => 'neutral'],
        'pending_review' => ['label' => 'Menunggu Review', 'variant' => 'warning'],
        'active'         => ['label' => 'Aktif',           'variant' => 'success'],
        'rejected'       => ['label' => 'Ditolak',         'variant' => 'danger'],
        'archived'       => ['label' => 'Diarsipkan',      'variant' => 'neutral'],

        // Seller / payment / digital — kolisi 'pending' & 'success' di-resolve setelah di bawah
        'pending'   => ['label' => 'Pending',  'variant' => 'warning'],
        'success'   => ['label' => 'Berhasil', 'variant' => 'success'],
        'suspended' => ['label' => 'Ditangguhkan', 'variant' => 'danger'],
    ];

    $config = $map[$value] ?? ['label' => ucfirst(str_replace('_', ' ', $value)), 'variant' => 'neutral'];
@endphp

<x-badge :variant="$config['variant']">{{ $config['label'] }}</x-badge>