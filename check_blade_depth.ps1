
$path = "d:\App\skripsi\resources\views\livewire\transaction\rincian-pesanan.blade.php"
$lines = Get-Content $path
$stack = @()
$lineNum = 0

foreach ($line in $lines) {
    $lineNum++
    $trimmed = $line.Trim()
    
    # Check openings
    if ($trimmed -match "^@if\b") { $stack += "if" }
    elseif ($trimmed -match "^@foreach\b") { $stack += "foreach" }
    elseif ($trimmed -match "^@for\b") { $stack += "for" }
    elseif ($trimmed -match "^@auth\b") { $stack += "auth" }
    elseif ($trimmed -match "^@guest\b") { $stack += "guest" }
    elseif ($trimmed -match "^@switch\b") { $stack += "switch" }
    elseif ($trimmed -match "^@push\b") { $stack += "push" }
    elseif ($trimmed -match "^@section\b" -and -not ($trimmed -match "^@section\('content'\)")) { $stack += "section" }
    elseif ($trimmed -match "^@php\b") { $stack += "php" }
    elseif ($trimmed -match "^@script\b") { $stack += "script" }
    
    # Check closings
    $closing = $null
    if ($trimmed -match "^@endif\b") { $closing = "if" }
    elseif ($trimmed -match "^@endforeach\b") { $closing = "foreach" }
    elseif ($trimmed -match "^@endfor\b") { $closing = "for" }
    elseif ($trimmed -match "^@endauth\b") { $closing = "auth" }
    elseif ($trimmed -match "^@endguest\b") { $closing = "guest" }
    elseif ($trimmed -match "^@endswitch\b") { $closing = "switch" }
    elseif ($trimmed -match "^@endpush\b") { $closing = "push" }
    elseif ($trimmed -match "^@endsection\b") { $closing = "section" }
    elseif ($trimmed -match "^@endphp\b") { $closing = "php" }
    elseif ($trimmed -match "^@endscript\b") { $closing = "script" }

    if ($closing) {
        if ($stack.Count -eq 0) {
            Write-Host "Error: Extra @end$closing at line $lineNum. Stack is empty."
        }
        else {
            $top = $stack[-1]
            if ($top -ne $closing) {
                Write-Host "Error: Mismatch at line $lineNum. Found @end$closing, expected @end$top"
            }
            # Pop
            $stack = $stack[0..($stack.Count - 2)]
        }
    }
}

if ($stack.Count -gt 0) {
    Write-Host "Unclosed directives at end of file:"
    foreach ($item in $stack) {
        Write-Host $item
    }
} else {
    Write-Host "File structure seems OK."
}
