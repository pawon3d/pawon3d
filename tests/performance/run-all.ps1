<#
.SYNOPSIS
    Master runner untuk pengujian non-fungsional UMKM.

.DESCRIPTION
    Menjalankan seluruh skenario pengujian:

    BAGIAN A — Performance Testing (beban realistis UMKM):
      - 3 VU  → kondisi normal     (30 detik)
      - 5 VU  → kondisi sibuk      (30 detik)
      - 8 VU  → kondisi beban puncak (30 detik)

    BAGIAN B — Reliability Testing:
      - B.1 Concurrency Kasir    (5 VU, stok=5)
      - B.2 Concurrency Produksi (3 VU, bahan baku)
      - B.3 Atomicity Test       (DB transaction + rollback)
      - B.4 Boundary Conditions  (stok=1, minimum, command)
      - B.5 Command Reliability  (inventory:check-alerts)

.PARAMETER Part
    'A'  → hanya performance
    'B'  → hanya reliability
    'all'→ semua (default)

.EXAMPLE
    .\tests\performance\run-all.ps1
    .\tests\performance\run-all.ps1 -Part A
    .\tests\performance\run-all.ps1 -Part B
#>
param(
    [ValidateSet('A', 'B', 'all')]
    [string]$Part = 'all'
)

$Dir = Split-Path -Parent $MyInvocation.MyCommand.Path

function Run-K6 {
    param([string]$Label, [string]$Script, [string]$Export, [string[]]$Extras = @())

    Write-Host ""
    Write-Host "──────────────────────────────────────────────────────" -ForegroundColor Cyan
    Write-Host "  $Label" -ForegroundColor Cyan
    Write-Host "──────────────────────────────────────────────────────" -ForegroundColor Cyan

    $exportPath = Join-Path $Dir $Export
    $logPath    = $exportPath -replace '\.json$', '.log'
    $args       = @('run') + $Extras + @("--summary-export=$exportPath", "$Dir\$Script")

    $start  = Get-Date
    $output = & k6 @args 2>&1
    $exit   = $LASTEXITCODE
    $dur    = [math]::Round(((Get-Date) - $start).TotalSeconds, 1)

    $output | Out-File $logPath -Encoding utf8
    $output | ForEach-Object { Write-Host $_ }

    Write-Host ""
    if ($exit -eq 0) {
        Write-Host "  ✅ $Label — LULUS ($dur s)" -ForegroundColor Green
    } else {
        Write-Host "  ⚠  $Label — periksa log ($dur s)" -ForegroundColor Yellow
    }
    Write-Host "  JSON : $exportPath" -ForegroundColor DarkGray
    return $exit
}

$results = @{}

# ── BAGIAN A – Performance ─────────────────────────────────────────────────
if ($Part -in 'A', 'all') {
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════╗" -ForegroundColor Magenta
    Write-Host "║  BAGIAN A — Performance Testing                      ║" -ForegroundColor Magenta
    Write-Host "╚══════════════════════════════════════════════════════╝" -ForegroundColor Magenta

    $results['A_3vu'] = Run-K6 'Performance 3 VU (normal)'      'performance-test.js' 'perf-3vu.json'  @('--env', 'VUS=3',  '--env', 'DURATION=30s')
    $results['A_5vu'] = Run-K6 'Performance 5 VU (sibuk)'       'performance-test.js' 'perf-5vu.json'  @('--env', 'VUS=5',  '--env', 'DURATION=30s')
    $results['A_8vu'] = Run-K6 'Performance 8 VU (beban puncak)''performance-test.js' 'perf-8vu.json'  @('--env', 'VUS=8',  '--env', 'DURATION=30s')
}

# ── BAGIAN B – Reliability ─────────────────────────────────────────────────
if ($Part -in 'B', 'all') {
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════╗" -ForegroundColor Magenta
    Write-Host "║  BAGIAN B — Reliability Testing                      ║" -ForegroundColor Magenta
    Write-Host "╚══════════════════════════════════════════════════════╝" -ForegroundColor Magenta

    $results['B1_kasir']     = Run-K6 'B.1 Concurrency Kasir'        'reliability-kasir.js'      'rel-kasir.json'
    $results['B2_produksi']  = Run-K6 'B.2 Concurrency Produksi'     'reliability-produksi.js'   'rel-produksi.json'
    $results['B3_atomicity'] = Run-K6 'B.3 Atomicity Test'           'reliability-atomicity.js'  'rel-atomicity.json'
    $results['B4_boundary']  = Run-K6 'B.4 Boundary Conditions'      'reliability-boundary.js'   'rel-boundary.json'
    $results['B5_command']   = Run-K6 'B.5 Command Reliability'      'reliability-command.js'    'rel-command.json'
}

# ── Ringkasan ──────────────────────────────────────────────────────────────
Write-Host ""
Write-Host "╔══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Ringkasan Hasil Seluruh Pengujian                   ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════╝" -ForegroundColor Cyan

foreach ($key in $results.Keys | Sort-Object) {
    $status = if ($results[$key] -eq 0) { '✅ LULUS' } else { '⚠  CEK LOG' }
    Write-Host ("  {0,-30} {1}" -f $key, $status)
}

Write-Host ""
Write-Host "Hasil JSON & log ada di: $Dir" -ForegroundColor Cyan
