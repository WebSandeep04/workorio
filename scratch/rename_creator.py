import os

files = [
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\under-process.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-completed.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-pending.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-followups.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-new.blade.php"
]

header_target = '<th>Creator</th>'
header_replacement = '<th>Assigned By</th>'

for file_path in files:
    if not os.path.exists(file_path):
        print(f"Skipping {file_path} - not found")
        continue
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if header_target in content:
        content = content.replace(header_target, header_replacement)
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {file_path}")
    else:
        print(f"Target not found in {file_path}")
