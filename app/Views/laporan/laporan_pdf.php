<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: green; }
        .text-danger { color: red; }
        .bold { font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none; margin-bottom: 20px;">
            <tr style="border: none;">
                <td style="width: 20%; text-align: center; border: 1px solid black; padding: 10px;">
                    <?php
                    $path = FCPATH . 'img/logobex.png';
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        echo '<img src="' . $base64 . '" style="width: 100px; height: auto;" alt="Logo">';
                    } else {
                         echo 'Logo Not Found';
                    }
                    ?>
                </td>
                <td style="width: 80%; text-align: center; border: 1px solid black; border-left: none; padding: 5px;">
                    <h2 style="margin: 2px 0; font-family: Arial, sans-serif; font-size: 18px;">PT BEX INDO BERKAT</h2>
                    <p style="margin: 2px 0; font-size: 12px;">Meadow Green Residence 5, Jalan Mandor Samin No. 17,</p>
                    <p style="margin: 2px 0; font-size: 12px;">RT 2 / RW 5, Kali Baru, Cilodong (No.64) Kota Depok - Jawa Barat 16414.</p>
                </td>
            </tr>
        </table>

        <?php 
        $titleText = "Laporan Keuangan Rekapitulasi";
        if (!empty($filter_info)) {
            // Check if filter info contains "Tahun" or date range
            if (strpos($filter_info, 'Tahun') !== false) {
                // Example: Filter: Tahun 2025
                $titleText = "Laporan Keuangan Per Tahun";
            } else {
                // Example: Filter: 01-01-2025 s/d 31-01-2025
                $titleText = "Laporan Keuangan Periode Tanggal - Tanggal"; 
            }
        }
        ?>
        
        <table style="width: 100%; border: none; margin-bottom: 20px;">
            <tr style="border: none;">
               <td style="text-align: center; border: 1px solid black; padding: 5px;">
                   <h3 style="margin: 0; font-family: Arial, sans-serif;"><?= $titleText ?></h3>
                   <?php if(!empty($filter_info)): ?>
                       <p style="margin: 5px 0; font-size: 12px;"><?= $filter_info ?></p>
                   <?php endif; ?>
               </td>
            </tr>
        </table>
        
        <p style="text-align: right; font-size: 10px;">Dicetak pada: <?= $generated_at ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Bulan</th>
                <th width="10%">Tahun</th>
                <th>Pemasukan (Rp)</th>
                <th>Pengeluaran (Rp)</th>
                <th>Bersih (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalMasuk = 0;
            $totalKeluar = 0;
            $totalBersih = 0;
            foreach ($report_data as $i => $row): 
                $totalMasuk += $row['pemasukan'];
                $totalKeluar += $row['pengeluaran'];
                $totalBersih += $row['bersih'];
            ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= $row['nama_bulan'] ?></td>
                <td class="text-center"><?= $row['tahun'] ?></td>
                <td class="text-right text-success"><?= number_format($row['pemasukan'], 0, ',', '.') ?></td>
                <td class="text-right text-danger"><?= number_format($row['pengeluaran'], 0, ',', '.') ?></td>
                <td class="text-right bold <?= $row['bersih'] >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= number_format($row['bersih'], 0, ',', '.') ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="bold" style="background-color: #eee;">
                <td colspan="3" class="text-center">TOTAL</td>
                <td class="text-right"><?= number_format($totalMasuk, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($totalKeluar, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($totalBersih, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generated by System PT BEX INDO BERKAT.
    </div>
</body>
</html>
