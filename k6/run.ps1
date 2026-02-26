#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Runner script untuk pengujian reliability k6 – Pawon3D

.DESCRIPTION
    Jalankan salah satu dari 4 tipe pengujian reliability:
      smoke  – 1 VU, 2 menit  (sanity check)
      load   – 10 VU, 5 menit (beban normal)
      stress – ramp ke 50 VU  (beban tinggi, cari titik kegagalan)
      soak   – 10 VU, 30 menit (ketahanan jangka panjang)

.PARAMETER Test
    Tipe pengujian: smoke | load | stress | soak | all

.PARAMETER BaseUrl
    URL dasar aplikasi. Default: https://skripsi.test

.PARAMETER Out
    Format output tambahan: json | csv | influxdb (opsional)

.EXAMPLE
    .\run.ps1 -Test smoke
    .\run.ps1 -Test load -Out json
    .\run.ps1 -Test all
#>
param(
    [ValidateSet('smoke', 'load', 'stress', 'soak', 'all')]
    [string]$Test = 'smoke',
    [string]$BaseUrl = 'https://skripsi.test',
    [ValidateSet('', 'json', 'csv')]
    [string]$Out = ''
)

$ScriptDir = $PSScriptRoot
$Timestamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$ResultDir = Join-Path $ScriptDir "results"
New-Item -ItemType Directory -Force -Path $ResultDir | Out-Null

function Invoke-K6 {
    param([string]$TestName)

    $Script = Join-Path $ScriptDir "${TestName}.js"
    $ResultFile = Join-Path $ResultDir "${TestName}_${Timestamp}"
    $Args = @("run", "--env", "BASE_URL=$BaseUrl", $Script)

    if ($Out -eq 'json') {
        $Args += "--out", "json=${ResultFile}.json"
    } elseif ($Out -eq 'csv') {
        $Args += "--out", "csv=${ResultFile}.csv"
    }

    Write-Host ""
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "  Menjalankan: $TestName test" -ForegroundColor Cyan
    Write-Host "  URL Target : $BaseUrl" -ForegroundColor Cyan
    Write-Host "  Waktu      : $(Get-Date -Format 'HH:mm:ss')" -ForegroundColor Cyan
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""

    $StartTime = Get-Date
    & k6 @Args
    $ExitCode = $LASTEXITCODE
    $Duration = (Get-Date) - $StartTime

    Write-Host ""
    if ($ExitCode -eq 0) {
        Write-Host "✅ $TestName test LULUS (durasi: $([math]::Round($Duration.TotalMinutes, 1)) menit)" -ForegroundColor Green
    } else {
        Write-Host "❌ $TestName test GAGAL (exit: $ExitCode, durasi: $([math]::Round($Duration.TotalMinutes, 1)) menit)" -ForegroundColor Red
    }
    return $ExitCode
}

# Jalankan tes
$Results = @{}

if ($Test -eq 'all') {
    foreach ($T in @('smoke', 'load', 'stress', 'soak')) {
        $Results[$T] = Invoke-K6 -TestName $T
    }
} else {
    $Results[$Test] = Invoke-K6 -TestName $Test
}

# Ringkasan akhir
if ($Test -eq 'all') {
    Write-Host ""
    Write-Host "========================================"  -ForegroundColor Cyan
    Write-Host "  RINGKASAN PENGUJIAN RELIABILITY"        -ForegroundColor Cyan
    Write-Host "========================================"  -ForegroundColor Cyan
    foreach ($T in $Results.Keys) {
        $Status = if ($Results[$T] -eq 0) { "✅ LULUS" } else { "❌ GAGAL" }
        Write-Host "  $T : $Status" -ForegroundColor $(if ($Results[$T] -eq 0) { 'Green' } else { 'Red' })
    }
    Write-Host ""
}
