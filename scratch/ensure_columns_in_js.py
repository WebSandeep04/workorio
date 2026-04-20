import os

files = [
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-new.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-pending.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-completed.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-followups.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\under-process.blade.php"
]

# We search for the ticket_value cell and if the next lines don't contain owner_name, we add it.
target_line_pattern = "<td>${item.ticket_value ?? '-'}</td>"
replacement_lines = """<td>${item.ticket_value ?? '-'}</td>
            <td>${item.owner_name ?? '-'}</td>
            <td>${item.creator_name ?? '-'}</td>""" # Note: creator_name is the property in JS

for file_path in files:
    if not os.path.exists(file_path):
        print(f"Skipping {file_path} - not found")
        continue
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check if we already have owner_name in the JS rendering parts
    if "item.owner_name" not in content:
        content = content.replace(target_line_pattern, replacement_lines)
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated JS rendering in {file_path}")
    else:
        # It might be partially updated. Let's do a more careful check.
        # If it's present but not everywhere, we might need a more robust replacement.
        # But my previous script for today-new might have worked.
        # Let's just try to replace all instances that DON'T have owner_name after them.
        # For simplicity, if it's already there once, I'll assume it's okay for now or I'll check manually.
        # Wait, today-followups clearly has it missing in loadFilteredFollowups.
        
        # Robust replacement: only replace if not followed by owner_name
        import re
        content = re.sub(r"<td>\$\{item\.ticket_value \?\? '-'\}</td>(?!\s*<td>\$\{item\.owner_name)", replacement_lines, content)
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Verified/Updated JS rendering in {file_path}")
