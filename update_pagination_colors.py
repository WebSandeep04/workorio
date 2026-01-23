import re
import os

# List of files (extracted from the documentation I previously created)
# I will read the file I created: PAGINATION_IMPLEMENTATION_LIST.md

def get_file_list():
    files = []
    try:
        with open(r'd:\laravel\leadmanagement (akrati ui work)\PAGINATION_IMPLEMENTATION_LIST.md', 'r') as f:
            for line in f:
                # Extract path looking for something that looks like a path
                # The format is "NUMBER. `PATH`"
                match = re.search(r'`([^`]+)`', line)
                if match:
                    files.append(match.group(1))
    except Exception as e:
        print(f"Error reading list: {e}")
    return files

def process_files(files):
    # Replacements
    # We want to be specific to pagination CSS if possible, but regex replacement on file content works
    # if we are confident these distinct strings appear in the pagination section.
    # The gradient string is very specific to pagination active state in this codebase.
    
    # Target: .pagination .page-link color
    # Target: .pagination .page-item.active .page-link background/border
    # Target: .pagination .page-link:hover background/border
    
    for file_path in files:
        if not os.path.exists(file_path):
            print(f"Skipping {file_path} (not found)")
            continue
            
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            
            # We will use regex substitution with a lambda or comprehensive replace for the pagination block
            # But the CSS might be scattered.
            # Strategy: look for the .pagination blocks and replace colors inside them.
            
            # Simple approach: Replace the specific unique lines/values that are part of the pagination CSS.
            
            # 1. Text Color for links
            # .pagination .page-link { ... color: #667eea; ... }
            # Regex to find .pagination .page-link block and replace color
            
            def replace_pagination_color(match):
                block = match.group(0)
                # Replace color #667eea with #434afa
                return block.replace('#667eea', '#434afa')

            # Search for .pagination .page-link { ... }
            # Note: CSS might span multiple lines
            content = re.sub(r'(\.pagination \.page-link\s*\{[^}]+\})', replace_pagination_color, content, flags=re.DOTALL)
            
            # 2. Active State
            # .pagination .page-item.active .page-link { ... }
            # Replace gradient and border and shadow
            def replace_active_pagination(match):
                block = match.group(0)
                # Replace gradient with solid color
                block = re.sub(r'background:\s*linear-gradient\([^;]+\);', 'background: #434afa;', block)
                # Just in case it's already a solid color but wrong
                if 'background: #667eea' in block:
                     block = block.replace('background: #667eea', 'background: #434afa')
                     
                block = block.replace('#667eea', '#434afa') # Border color
                # Shadow rgba(102, 126, 234, ...) -> rgba(67, 74, 250, ...)
                block = block.replace('102, 126, 234', '67, 74, 250')
                return block

            content = re.sub(r'(\.pagination \.page-item\.active \.page-link\s*\{[^}]+\})', replace_active_pagination, content, flags=re.DOTALL)
            
            # 3. Hover State
            # .pagination .page-link:hover { ... }
            def replace_hover_pagination(match):
                block = match.group(0)
                block = block.replace('102, 126, 234', '67, 74, 250') # rgba for background
                block = block.replace('#667eea', '#434afa') # border
                return block

            content = re.sub(r'(\.pagination \.page-link:hover\s*\{[^}]+\})', replace_hover_pagination, content, flags=re.DOTALL)
            
            if content != original_content:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"Updated {file_path}")
            else:
                print(f"No changes for {file_path} (maybe already updated or pattern not found)")

        except Exception as e:
            print(f"Error processing {file_path}: {e}")

if __name__ == '__main__':
    files = get_file_list()
    print(f"Found {len(files)} files.")
    process_files(files)
