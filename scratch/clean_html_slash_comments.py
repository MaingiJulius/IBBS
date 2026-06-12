import os

PAGES_DIR = r"c:\Users\Admin\Desktop\UDA\Project\IBBS_PROTOTYPE\pages"

def remove_html_slash_comments(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        
    new_lines = []
    in_php = False
    in_script = False
    modified = False
    
    for line in lines:
        stripped = line.strip()
        
        # Check transitions
        if '<?php' in line.lower() and '?>' not in line:
            in_php = True
        elif '?>' in line:
            in_php = False
            
        if '<script' in line.lower() and '</script>' not in line.lower():
            in_script = True
        elif '</script>' in line.lower():
            in_script = False
            
        # Also handle single-line <?php ... ?> which doesn't span lines but needs checking
        # If it's a single line script or php, the state resets correctly.
        
        # If we are NOT in PHP and NOT in Script, it's HTML
        if not in_php and not in_script:
            # Check if line starts with //
            if stripped.startswith('//'):
                modified = True
                continue # Skip this line
                
            # Check for inline // (less common but possible, though we must be careful with URLs like http://)
            # Actually, URLs have http://, so inline // is risky to just strip. 
            # Looking at the codebase, the main issue is whole lines starting with //
            
        new_lines.append(line)
        
    if modified:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.writelines(new_lines)
        print(f"Cleaned {os.path.basename(filepath)}")

for root, dirs, files in os.walk(PAGES_DIR):
    for file in files:
        if file.endswith('.php') or file.endswith('.html'):
            remove_html_slash_comments(os.path.join(root, file))

print("Done")
