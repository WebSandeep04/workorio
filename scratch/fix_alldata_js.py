import os
import re

file_path = r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata.blade.php"

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# We need to find all instances where ticket_value cell is followed by assignToColumn or just </tr>
# and insert owner_name and creator_name cells.

ticket_pattern = r"(<td>\$\{record\.ticket_value \?\? '0'\}</td>)(\s+\$\{assignToColumn\})"
replacement = r"\1\n                                <td>${record.owner_name ?? 'N/A'}</td>\n                                <td>${record.creator_name ?? 'N/A'}</td>\2"

# Apply to all instances in alldata.blade.php
new_content = re.sub(ticket_pattern, replacement, content)

# Also check for instances that don't have assignToColumn but close the row
# (though in this file they mostly seem to have it)

if new_content != content:
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print(f"Updated JS rendering in {file_path}")
else:
    print(f"No changes made to {file_path} - target pattern not found")
