<#
.SYNOPSIS
    Jalankan pengujian concurrent transaction dengan 3 skenario VU berbeda.

.DESCRIPTION
    Skrip ini menjalankan test-transaction.js tiga kali dengan konfigurasi
    VU yang berbeda: 10 VU, 20 VU, dan 50 VU. Hasil tiap run disimpan
    sebagai JSON di folder tests/performance/.

.PARAMETER Scenario
    Pilih skenario spesifik: 10, 20, 50 atau all (default: all)

.PARAMETER Stock
    Jumlah stok awal produk sebelum tiap skenario (default: 50)

.EXAMPLE
    .\tests\performance\run-scenarios.ps1
    .\tests\performance\run-scenarios.ps1 -Scenario 10
    .\tests\performance\run-scenarios.ps1 -Scenario all -Stock 100
#>
param(
    [ValidateSet('10', '20', '50', 'all')]
    [string]$Scenario = 'all',

    [int]$Stock = 50
)

$ScriptDir  = Split-Path -Parent $MyInvocation.MyCommand.Path
$TestScript = Join-Path $ScriptDir 'test-transaction.js'
$ResultsDir = $ScriptDir

# Pastikan folder results ada
if (-not (Test-Path $ResultsDir)) {
    New-Item -ItemType Directory -Path $ResultsDir -Force | Out-Null
}

function Run-Scenario {
    param(
        [int]$Vus,
        [int]$InitialStock
    )

    $outputFile = Join-Path $ResultsDir "results-${Vus}vu.json"
    $logFile    = Join-Path $ResultsDir "results-${Vus}vu.log"

    Write-Host ""
    Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Cyan
    Write-Host "  Skenario: $Vus Virtual Users  |  Stok Awal: $InitialStock" -ForegroundColor Cyan
    Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Cyan

    $cmd = "k6 run " +
           "--env VUS=$Vus " +
           "--env STOCK=$InitialStock " +
           "--summary-export=`"$outputFile`" " +
           "`"$TestScript`""

    Write-Host "Perintah: $cmd" -ForegroundColor DarkGray
    Write-Host ""

    $startTime = Get-Date
    $result    = Invoke-Expression $cmd 2>&1

    # Simpan output lengkap ke log
    $result | Out-File -FilePath $logFile -Encoding utf8

    # Tampilkan output di konsol
    $result | ForEach-Object { Write-Host $_ }

    $duration = (Get-Date) - $startTime
    Write-Host ""
    Write-Host "  Durasi run : $([math]::Round($duration.TotalSeconds, 1))s" -ForegroundColor Gray
    Write-Host "  JSON hasil : $outputFile" -ForegroundColor Green
    Write-Host "  Log output : $logFile"    -ForegroundColor Green

    # Baca ringkasan dari JSON
    if (Test-Path $outputFile) {
        try {
            $json        = Get-Content $outputFile -Raw | ConvertFrom-Json
            $reqTotal    = $json.metrics.http_reqs.values.count
            $failRate    = [math]::Round($json.metrics.http_req_failed.values.rate * 100, 2)
            $p95         = [math]::Round($json.metrics.http_req_duration.values.'p(95)', 2)
            $avgDur      = [math]::Round($json.metrics.http_req_duration.values.avg, 2)
            $rps         = [math]::Round($json.metrics.http_reqs.values.rate, 2)
            $txnSuccess  = $json.metrics.txn_successful_buys.values.count
            $txn409      = $json.metrics.txn_insufficient_stock_409.values.count
            $txnErrors   = $json.metrics.txn_server_errors_5xx.values.count
            $raceCount   = $json.metrics.txn_race_condition_detected.values.count

            Write-Host ""
            Write-Host "  ── Ringkasan HTTP ─────────────────────────────────" -ForegroundColor Yellow
            Write-Host ("  Total Request : {0}" -f $reqTotal)
            Write-Host ("  Req / detik   : {0}" -f $rps)
            Write-Host ("  Rate Gagal    : {0}%" -f $failRate)
            Write-Host ("  Avg Duration  : {0} ms" -f $avgDur)
            Write-Host ("  p(95) Duration: {0} ms" -f $p95)
            Write-Host ""
            Write-Host "  ── Konsistensi Transaksi ──────────────────────────" -ForegroundColor Yellow
            Write-Host ("  Pembelian Berhasil (200) : {0}" -f $txnSuccess)
            Write-Host ("  Stok Habis (409)         : {0}" -f $txn409)
            Write-Host ("  Error Server (5xx)       : {0}" -f $txnErrors)

            if ($raceCount -gt 0) {
                Write-Host ("  Race Condition Terdeteksi: {0} ❌" -f $raceCount) -ForegroundColor Red
            } else {
                Write-Host "  Race Condition Terdeteksi: 0 ✅" -ForegroundColor Green
            }
        } catch {
            Write-Host "  (Gagal mem-parse JSON hasil)" -ForegroundColor DarkYellow
        }
    }
}

# ── Jalankan skenario ───────────────────────────────────────────────────────
Write-Host ""
Write-Host "╔═══════════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║  Pengujian Concurrent Transaction – k6                ║" -ForegroundColor Magenta
Write-Host "║  Produk: Donat Gula (siap-beli)                        ║" -ForegroundColor Magenta
Write-Host "╚═══════════════════════════════════════════════════════╝" -ForegroundColor Magenta

switch ($Scenario) {
    '10'  { Run-Scenario -Vus 10 -InitialStock $Stock }
    '20'  { Run-Scenario -Vus 20 -InitialStock $Stock }
    '50'  { Run-Scenario -Vus 50 -InitialStock $Stock }
    'all' {
        Run-Scenario -Vus 10 -InitialStock $Stock
        Run-Scenario -Vus 20 -InitialStock $Stock
        Run-Scenario -Vus 50 -InitialStock $Stock
    }
}

Write-Host ""
Write-Host "Semua skenario selesai. Lihat hasil di: $ResultsDir" -ForegroundColor Cyan
