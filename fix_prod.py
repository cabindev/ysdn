#!/usr/bin/env python3
"""
YSDN Production Bug Fix Script
แก้ไข:
1. NULL pointer bug ใน User.php
2. Path issues ใน login.php, checkLogin.php
3. Vendor path issues ใน 51+ files
"""
import os
import glob

def fix_production(root_dir="/home/ysdn/httpdocs/ysdn"):
    if not os.path.exists(root_dir):
        print(f"❌ Directory not found: {root_dir}")
        return False
    
    fixed_files = []
    
    # 1. Fix NULL pointer bug ใน User.php
    user_php = os.path.join(root_dir, "src/Model/User.php")
    if os.path.exists(user_php):
        with open(user_php, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # ตรวจสอบว่ามี null check หรือยัง
        if '$data[0]' in content and 'if (empty($data))' not in content:
            old_code = """$stmt->execute(['emailOrName' => $emailOrName]);
        $data = $stmt->fetchAll();
        $userDB = $data[0];"""
            
            new_code = """$stmt->execute(['emailOrName' => $emailOrName]);
        $data = $stmt->fetchAll();
        
        // ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือไม่
        if (empty($data)) {
            return false;
        }
        
        $userDB = $data[0];"""
            
            if old_code in content:
                content = content.replace(old_code, new_code)
                with open(user_php, 'w', encoding='utf-8') as f:
                    f.write(content)
                fixed_files.append("✅ src/Model/User.php")
    
    # 2. Fix path ใน login.php
    login_php = os.path.join(root_dir, "ysdn/auth/login.php")
    if os.path.exists(login_php):
        with open(login_php, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        if 'DOCUMENT_ROOT' in content and 'csrf.php' in content:
            content = content.replace(
                '_SERVER['DOCUMENT_ROOT'] . "/ysdn/ysdn/auth/csrf.php"',
                '__DIR__ . '/csrf.php''
            )
            content = content.replace(
                '_SERVER["DOCUMENT_ROOT"] . "/ysdn/ysdn/auth/csrf.php"',
                '__DIR__ . "/csrf.php"'
            )
            with open(login_php, 'w', encoding='utf-8') as f:
                f.write(content)
            fixed_files.append("✅ ysdn/auth/login.php")
    
    # 3. Fix path ใน checkLogin.php
    check_php = os.path.join(root_dir, "ysdn/auth/checkLogin.php")
    if os.path.exists(check_php):
        with open(check_php, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        if 'DOCUMENT_ROOT' in content:
            # Fix csrf.php path
            content = content.replace(
                '_SERVER['DOCUMENT_ROOT'] . "/ysdn/ysdn/auth/csrf.php"',
                '__DIR__ . '/csrf.php''
            )
            content = content.replace(
                '_SERVER["DOCUMENT_ROOT"] . "/ysdn/ysdn/auth/csrf.php"',
                '__DIR__ . "/csrf.php"'
            )
            # Fix vendor path
            content = content.replace(
                '_SERVER['DOCUMENT_ROOT'] . "/ysdn/vendor/autoload.php"',
                '__DIR__ . '/../../vendor/autoload.php''
            )
            content = content.replace(
                '_SERVER["DOCUMENT_ROOT"] . "/ysdn/vendor/autoload.php"',
                '__DIR__ . "/../../vendor/autoload.php"'
            )
            
            with open(check_php, 'w', encoding='utf-8') as f:
                f.write(content)
            fixed_files.append("✅ ysdn/auth/checkLogin.php")
    
    # 4. Fix vendor paths ใน 51+ files ทั้งหมด
    php_files = glob.glob(f"{root_dir}/**/*.php", recursive=True)
    vendor_fixes = 0
    
    for filepath in php_files:
        try:
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                original = f.read()
            
            content = original
            
            # Fix vendor path - DOCUMENT_ROOT "/vendor/" → DOCUMENT_ROOT "/ysdn/vendor/"
            if 'DOCUMENT_ROOT' in content and '/vendor/' in content and '/ysdn/vendor/' not in content:
                content = content.replace(
                    "_SERVER['DOCUMENT_ROOT'] . "/vendor/",
                    "_SERVER['DOCUMENT_ROOT'] . "/ysdn/vendor/"
                )
                content = content.replace(
                    '_SERVER["DOCUMENT_ROOT"] . "/vendor/',
                    '_SERVER["DOCUMENT_ROOT"] . "/ysdn/vendor/'
                )
            
            # Fix csrf path ใน auth files
            if '/ysdn/auth/' in filepath and 'csrf.php' in content and 'DOCUMENT_ROOT' in content:
                content = content.replace(
                    '_SERVER['DOCUMENT_ROOT'] . "/ysdn/ysdn/auth/csrf.php"',
                    '__DIR__ . '/csrf.php''
                )
                content = content.replace(
                    '_SERVER["DOCUMENT_ROOT"] . "/ysdn/ysdn/auth/csrf.php"',
                    '__DIR__ . "/csrf.php"'
                )
            
            if content != original:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                vendor_fixes += 1
        except:
            pass
    
    if vendor_fixes > 0:
        fixed_files.append(f"✅ Fixed {vendor_fixes} files with vendor/path issues")
    
    return fixed_files

# Main execution
if __name__ == "__main__":
    print("\n🔧 เริ่มแก้ไข Production Server...")
    print("-" * 70)
    
    fixed = fix_production()
    
    if fixed:
        print("\n✅ FILES FIXED:")
        for item in fixed:
            print(f"   {item}")
        print(f"\n✅ เสร็จสิ้น! แก้ไข {len(fixed)} ชุด")
    else:
        print("\n❌ ไม่สามารถแก้ไขได้")
    
    print("-" * 70)
    print("🎉 Production fixes complete!")
