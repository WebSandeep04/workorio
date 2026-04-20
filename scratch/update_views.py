import os

files = [
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\under-process.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-completed.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-pending.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-followups.blade.php"
]

header_target = '''              <th>Product</th>
              <th>Ticket</th>
            </tr>'''
header_replacement = '''              <th>Product</th>
              <th>Ticket</th>
              <th>Owner</th>
              <th>Creator</th>
            </tr>'''

body_target = '<td>${item.ticket_value ?? "-"}</td>'
body_replacement = '''<td>${item.ticket_value ?? "-"}</td>
            <td>${item.owner_name ?? "-"}</td>
            <td>${item.creator_name ?? "-"}</td>'''

colspan_target = 'colspan="15"'
colspan_replacement = 'colspan="17"'

for file_path in files:
    if not os.path.exists(file_path):
        print(f"Skipping {file_path} - not found")
        continue
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    content = content.replace(header_target, header_replacement)
    content = content.replace(body_target, body_replacement)
    content = content.replace(colspan_target, colspan_replacement)
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated {file_path}")
