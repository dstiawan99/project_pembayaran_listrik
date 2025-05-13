<?php
function base_url() {
    $base_url = "http://".$_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
    return $base_url;
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function tanggal_indo($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

function getBulan($bln) {
    switch ($bln) {
        case "01": return "Januari"; break;
        case "02": return "Februari"; break;
        case "03": return "Maret"; break;
        case "04": return "April"; break;
        case "05": return "Mei"; break;
        case "06": return "Juni"; break;
        case "07": return "Juli"; break;
        case "08": return "Agustus"; break;
        case "09": return "September"; break;
        case "10": return "Oktober"; break;
        case "11": return "November"; break;
        case "12": return "Desember"; break;
    }
}
?>