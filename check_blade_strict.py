
import re

file_path = r'd:\App\skripsi\resources\views\livewire\transaction\rincian-pesanan.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

stack = []

# Regex for directives
start_pattern = re.compile(r'@(if|foreach|for|while|auth|guest|switch|section(?!\(\s*\'content\'\s*\))|push|php|script)\b')
end_pattern = re.compile(r'@end(if|foreach|for|while|auth|guest|switch|section|push|php|script)\b')
else_pattern = re.compile(r'@(else|elseif)\b')

for i, line in enumerate(lines):
    line_num = i + 1
    content = line.strip()
    
    # Check for closings first to handle single-line weirdness if any (rare in proper formatting)
    # But usually we encounter start then end.
    
    # Find all matches in line
    # We need to iterate tokens in order.
    # A simple approach: split by @ and analyze.
    
    # Let's simple scan line for directives.
    # Note: this ignores multiple directives on one line for simplicity unless we use finditer
    
    # Better: finditer
    matches = []
    for m in start_pattern.finditer(content):
        matches.append((m.start(), 'start', m.group(1)))
    for m in end_pattern.finditer(content):
        matches.append((m.start(), 'end', m.group(1)))
    for m in else_pattern.finditer(content):
        matches.append((m.start(), 'else', m.group(1)))
        
    matches.sort(key=lambda x: x[0])
    
    for _, type, name in matches:
        if type == 'start':
            stack.append((name, line_num))
            # print(f"Line {line_num}: Push {name}")
        elif type == 'end':
            if not stack:
                print(f"Error at Line {line_num}: Unexpected @end{name}. Stack is empty.")
            else:
                top_name, top_line = stack[-1]
                if top_name != name:
                    print(f"Error at Line {line_num}: Mismatch. Found @end{name} but expected @end{top_name} (opened at {top_line})")
                else:
                    stack.pop()
                    # print(f"Line {line_num}: Pop {name}")
        elif type == 'else':
             if not stack or stack[-1][0] not in ['if', 'switch', 'auth', 'guest', 'foreach']: # else can be in switch? no. standard is if/elseif. foreach has empty?
                 # actually @else can be used in @foreach (forelse) but blade uses @forelse. 
                 # strict @else is for if/auth/guest.
                 pass

if stack:
    print("Unclosed directives at end:")
    for item in stack:
        print(f"{item[0]} at line {item[1]}")
else:
    print("Directives balanced.")
