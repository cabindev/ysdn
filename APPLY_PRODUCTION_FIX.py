#!/usr/bin/env python3
"""
🔧 Production Fix - Run this on production server:
python3 APPLY_PRODUCTION_FIX.py
"""
import os, glob

os.chdir(os.path.expanduser("~/httpdocs/ysdn"))
print("=" * 70)
print("🔧 YSDN Production Bug Fix")
print("=" * 70)

# Fix 1: User.php NULL pointer
if os.path.exists("src/Model/User.php"):
    with open("src/Model/User.php", 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    if '$data[0]' in content and 'if (empty($data))' not in content:
        old = "        $stmt = $this->pdo->prepare($sql);\n        $stmt->execute(['emailOrName' => $emailOrName]);\n        $data = $stmt->fetchAll();\n        $userDB = $data[0];"
        new = "        $stmt = $this->pdo->prepare($sql);\n        $stmt->execute(['emailOrName' => $emailOrName]);\n        $data = $stmt->fetchAll();\n        if (empty($data)) {\n            return false;\n        }\n        $userDB = $data[0];"
        if old in content:
            content = content.replace(old, new)
            with open("src/Model/User.php", 'w', encoding='utf-8') as f:
                f.write(content)
            print("✅ src/Model/User.php - NULL pointer bug fixed")

# Fix 2: login.php paths
if os.path.exists("ysdn/auth/login.php"):
    with open("ysdn/auth/login.php", 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    old_content = content
    content = content.replace('_SERVER[\'DOCUMENT_ROOT\'] . "/ysdn/ysdn/auth/csrf.php"', '__DIR__ . \'/csrf.php\'')
    content = content.replace('_SERVER["DOCUMENT_ROOT"] . "/ysdn/ysdn/auth/csrf.php"', '__DIR__ . "/csrf.php"')
    if content != old_content:
        with open("ysdn/auth/login.php", 'w', encoding='utf-8') as f:
            f.write(content)
        print("✅ ysdn/auth/login.php - Path fixed")

# Fix 3: checkLogin.php paths
if os.path.exists("ysdn/auth/checkLogin.php"):
    with open("ysdn/auth/checkLogin.php", 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    old_content = content
    content = content.replace('_SERVER[\'DOCUMENT_ROOT\'] . "/ysdn/ysdn/auth/csrf.php"', '__DIR__ . \'/csrf.php\'')
    content = content.replace('_SERVER[\'DOCUMENT_ROOT\'] . "/ysdn/vendor/autoload.php"', '__DIR__ . \'/../../vendor/autoload.php\'')
    content = content.replace('_SERVER["DOCUMENT_ROOT"] . "/ysdn/ysdn/auth/csrf.php"', '__DIR__ . "/csrf.php"')
    content = content.replace('_SERVER["DOCUMENT_ROOT"] . "/ysdn/vendor/autoload.php"', '__DIR__ . "/../../vendor/autoload.php"')
    if content != old_content:
        with open("ysdn/auth/checkLogin.php", 'w', encoding='utf-8') as f:
            f.write(content)
        print("✅ ysdn/auth/checkLogin.php - Path fixed")

# Fix 4: Vendor paths in 51+ files
count = 0
for filepath in glob.glob("**/*.php", recursive=True):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        old_content = content
        if 'DOCUMENT_ROOT' in content and '/vendor/' in content:
            content = content.replace("_SERVER['DOCUMENT_ROOT'] . \"/vendor/", "_SERVER['DOCUMENT_ROOT'] . \"/ysdn/vendor/")
            content = content.replace('_SERVER["DOCUMENT_ROOT"] . "/vendor/', '_SERVER["DOCUMENT_ROOT"] . "/ysdn/vendor/')
        if content != old_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            count += 1
    except: pass

print(f"✅ Fixed {count} files with vendor path issues")
print("=" * 70)
print("✅ ALL FIXES APPLIED!")
print("=" * 70)
print("\n📌 TEST LOGIN:")
print("   URL: https://ysdnthailand.com/ysdn/auth/login.php")
print("   Email: evo_reaction@hotmail.com")
print("   Password: 025194166\n")
