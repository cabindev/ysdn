#!/usr/bin/env python3
"""
Production Fix Script - แก้ไขปัญหา path และ null pointer bug
รัน: python3 fix_production.py
"""
import os
import glob
import sys

# Colors for output
GREEN = '\033[92m'
RED = '\033[91m'
YELLOW = '\033[93m'
RESET = '\033[0m'

def fix_files(root_dir="/home/ysdn/httpdocs/ysdn"):
    """Fix all PHP files with path and bug issues"""
    
    if not os.path.exists(root_dir):
        print(f"{RED}❌ Directory not found: {root_dir}{RESET}")
        print(f"Please adjust root_dir in script or use: python3 fix_production.py /path/to/ysdn")
        return
    
    php_files = glob.glob(f"{root_dir}/**/*.php", recursive=True)
    total_files = 0
    fixed_files_set = set()
    
    print(f"{YELLOW}📂 Found {len(php_files)} PHP files{RESET}")
    
    for filepath in php_files:
        try:
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                original_content = f.read()
            
            content = original_content
            was_modified = False
            
            # Fix 1: NULL pointer bug in User.php
            if 'checkUserByEmailOrName' in content and '$data[0]' in content:
                if 'if (empty($data))' not in content:
                    # Add null check before $data[0] access
                    old_pattern = '''$stmt->execute(['emailOrName' => $emailOrName]);
        $data = $stmt->fetchAll();
        $userDB = $data[0];'''
                    
                    new_pattern = '''$stmt->execute(['emailOrName' => $emailOrName]);
        $data = $stmt->fetchAll();
        
        // ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือไม่
        if (empty($data)) {
            return false;
        }
        
        $userDB = $data[0];'''
                    
                    if old_pattern in content:
                        content = content.replace(old_pattern, new_pattern)
                        was_modified = True
            
            # Fix 2: Path issues - csrf.php require
            if 'DOCUMENT_ROOT' in content and 'csrf.php' in content:
                content = content.replace(
                    '_SERVER[\'DOCUMENT_ROOT\'] . "/ysdn/ysdn/auth/csrf.php"',
                    '__DIR__ . \'/csrf.php\''
                )
                content = content.replace(
                    '_SERVER["DOCUMENT_ROOT"] . "/ysdn/ysdn/auth/csrf.php"',
                    '__DIR__ . "/csrf.php"'
                )
                if content != original_content:
                    was_modified = True
            
            # Fix 3: Path issues - vendor autoload
            if 'DOCUMENT_ROOT' in content and 'vendor/autoload' in content:
                old_vendor = '_SERVER[\'DOCUMENT_ROOT\'] . "/ysdn/vendor/autoload.php"'
                new_vendor = '__DIR__ . "/../../vendor/autoload.php"'
                content = content.replace(old_vendor, new_vendor)
                
                old_vendor2 = '_SERVER["DOCUMENT_ROOT"] . "/ysdn/vendor/autoload.php"'
                new_vendor2 = '__DIR__ . "/../../vendor/autoload.php"'
                content = content.replace(old_vendor2, new_vendor2)
                
                if content != original_content:
                    was_modified = True
            
            # Fix 4: Generic vendor path in auth folder
            if '/ysdn/auth/' in filepath or '/ysdn/member/' in filepath or '/activity/' in filepath or '/Dashboard/' in filepath:
                if 'vendor/autoload' in content and 'DOCUMENT_ROOT' in content:
                    content = content.replace(
                        'DOCUMENT_ROOT . "/vendor/',
                        'DOCUMENT_ROOT . "/ysdn/vendor/'
                    )
                    if content != original_content:
                        was_modified = True
            
            # Write back if modified
            if was_modified:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                total_files += 1
                fixed_files_set.add(filepath.replace(root_dir, ''))
        
        except Exception as e:
            pass  # Silently skip errors
    
    # Print results
    print(f"\n{GREEN}✅ Fixed {total_files} files:{RESET}")
    for fp in sorted(fixed_files_set):
        print(f"   ✓ {fp}")
    
    print(f"\n{GREEN}✅ Production fixes complete!{RESET}")

if __name__ == '__main__':
    root = sys.argv[1] if len(sys.argv) > 1 else "/home/ysdn/httpdocs/ysdn"
    fix_files(root)
