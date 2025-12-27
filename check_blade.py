
file_path = r'd:\App\skripsi\resources\views\livewire\transaction\rincian-pesanan.blade.php'

with open(file_path, 'r') as f:
    lines = f.readlines()

stack = []

for i, line in enumerate(lines):
    line = line.strip()
    # Simple check for directives. Note: this won't handle comments or complex inline blade perfectly but usually sufficient for structure debugging.
    if '@if' in line and '@endif' not in line: # simplistic check
        # check if it's really an opening if
        if line.startswith('@if'): 
            stack.append(('if', i + 1))
    elif '@foreach' in line and '@endforeach' not in line:
        if line.startswith('@foreach'):
            stack.append(('foreach', i + 1))
    elif '@for' in line and '@endfor' not in line:
        if line.startswith('@for'):
           stack.append(('for', i + 1))
    
    # Check for closing
    if line.startswith('@endif'):
        if stack and stack[-1][0] == 'if':
            stack.pop()
        else:
             print(f"Error: Unexpected @endif at line {i+1}")
    elif line.startswith('@endforeach'):
        if stack and stack[-1][0] == 'foreach':
            stack.pop()
        else:
             print(f"Error: Unexpected @endforeach at line {i+1}")
    elif line.startswith('@endfor'):
        if stack and stack[-1][0] == 'for':
            stack.pop()
        else:
             print(f"Error: Unexpected @endfor at line {i+1}")

if stack:
    print("Unclosed directives:")
    for item in stack:
        print(f"{item[0]} at line {item[1]}")
else:
    print("Directives seem balanced.")
