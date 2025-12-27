
$path = "d:\App\skripsi\resources\views\livewire\transaction\rincian-pesanan.blade.php"
$lines = Get-Content $path
$stack = @()
$lineNum = 0

foreach ($line in $lines) {
    $lineNum++
    $trimmed = $line.Trim()
    
    # Check for openings
    if ($trimmed -match "^@if\b" -or $trimmed -match "^@foreach\b" -or $trimmed -match "^@for\b" -or $trimmed -match "^@auth\b" -or $trimmed -match "^@guest\b" -or $trimmed -match "^@switch\b" -or $trimmed -match "^@push\b" -or $trimmed -match "^@section\b") {
        # Check against false positives if needed, but simple regex is usually ok
        if (-not ($trimmed -match "^@section\('content'\)")) { # example exclusion if needed
             $stack += @{ Type = $trimmed.Split(' ')[0]; Line = $lineNum }
        }
    }

    # Check for closings
    if ($trimmed -match "^@endif\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@if") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endif at line $lineNum"
        }
    }
    elseif ($trimmed -match "^@endforeach\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@foreach") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endforeach at line $lineNum"
        }
    }
     elseif ($trimmed -match "^@endfor\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@for") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endfor at line $lineNum"
        }
    }
    elseif ($trimmed -match "^@endauth\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@auth") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endauth at line $lineNum"
        }
    }
    elseif ($trimmed -match "^@endguest\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@guest") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endguest at line $lineNum"
        }
    }
    elseif ($trimmed -match "^@endswitch\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@switch") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endswitch at line $lineNum"
        }
    }
    elseif ($trimmed -match "^@endpush\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@push") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endpush at line $lineNum"
        }
    }
    elseif ($trimmed -match "^@endsection\b") {
        if ($stack.Count -gt 0 -and $stack[-1].Type -eq "@section") {
            $stack = $stack[0..($stack.Count - 2)]
        } else {
            Write-Host "Error: Unexpected @endsection at line $lineNum"
        }
    }
}

if ($stack.Count -gt 0) {
    Write-Host "Unclosed directives:"
    foreach ($item in $stack) {
        Write-Host "$($item.Type) at line $($item.Line)"
    }
} else {
    Write-Host "Directives seem balanced."
}
