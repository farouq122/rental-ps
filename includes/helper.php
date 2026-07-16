<?php

// Formats integer amount to Indonesian Rupiah currency format
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Helper to determine status based on completion time
// Returns: 'Sedang Disewa' or 'Tersedia'
function get_sewa_status($waktu_selesai) {
    if (!$waktu_selesai) {
        return 'Tersedia';
    }
    
    $selesai_timestamp = strtotime($waktu_selesai);
    $sekarang_timestamp = time(); // Server current time
    
    if ($sekarang_timestamp < $selesai_timestamp) {
        return 'Sedang Disewa';
    }
    
    return 'Tersedia';
}

// Calculates on-the-fly total cost of a rental
function hitung_total_biaya($tarif_per_jam, $durasi_jam) {
    return $tarif_per_jam * $durasi_jam;
}

// Returns human readable status badge
function get_status_badge($status) {
    if ($status === 'Tersedia') {
        return '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Tersedia</span>';
    } else {
        return '<span class="badge bg-danger"><i class="bi bi-play-circle"></i> Sedang Disewa</span>';
    }
}
?>
