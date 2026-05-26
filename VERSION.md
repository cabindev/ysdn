# YSDN Thailand — Version Log

## v3.0 — พฤษภาคม 2569 (UI Overhaul)

### เป้าหมาย
ปรับ UI ทุกหน้าให้ใช้ design system เดียวกัน — minimal, flat, ไม่มี shadow/bg ที่ไม่จำเป็น

### Sidebar (ใหม่ทั้งระบบ)
- Dark sidebar `#1c1c1e` ใช้กับทุกหน้า Dashboard + Activity + Auth
- Active state: เส้น left border สีส้ม `#f58220` + text ขาว ไม่มี bg block
- Logout pin ล่างสุดทุกหน้า
- Admin เห็น Dashboard links ครบ, Member เห็นเฉพาะ "บัญชีของฉัน"

### หน้าที่ปรับปรุง
| หน้า | การเปลี่ยนแปลงหลัก |
|------|-------------------|
| `Dashboard/index.php` | Stat cards flat, recent table redesign (avatar initial, relative time, status badge), sidebar ใหม่ |
| `Dashboard/data.php` | เขียนใหม่ทั้งหมด — PHP ขึ้นบน, sidebar ใหม่, DataTables ภาษาไทย |
| `Dashboard/allUser.php` | เขียนใหม่ทั้งหมด — role badge, toast notification แทน alert |
| `Dashboard/chartjquery.php` | เขียนใหม่ — chart grid 2 คอลัมน์, ตัดสีสุ่มออกใช้ palette คงที่ |
| `Dashboard/approove_user_activity.php` | เขียนใหม่ — activity filter บน topbar, DataTables, toast |
| `activity/all_activity.php` | เขียนใหม่ — AdminLTE จาก `../Dashboard/`, activity cards, pagination จริง |
| `ysdn/auth/profile.php` | Member card + QR code + download, sidebar แยก admin/member |
| `ysdn/auth/editProfile.php` | เปลี่ยนจาก raw table → info grid 3 กลุ่ม + ปุ่มแก้ไข |
| `ysdn/auth/profile_activity.php` | เขียนใหม่ — tabs กำลังจะมาถึง/ผ่านมาแล้ว, status badge |

### Design System
- **Font**: Inter + Noto Sans Thai weight 300/400/500
- **Colors**: `#1a1a1a` text, `#f0f0f0` border, `#f58220` accent, `#bbb` muted
- **Table rows**: border-bottom only, hover `#fafafa`, no outer border
- **Status badges**: pill shape — approved `#15803d`, pending `#c2621a`, rejected `#dc2626`
- **Topbar**: สีขาว, border-bottom บาง, ไม่มี shadow
- **Content area**: `#fff` ล้วน ไม่มี gray background

### Bug fixes
- ทุกหน้าที่เขียนใหม่: PHP require ขึ้นก่อน HTML output (แก้ headers already sent)
- ลบ `<body>` tag ซ้ำออก (data.php, allUser.php)
- เพิ่ม `Input::e()` ทุก output ป้องกัน XSS
- `editProfile.php`: ลบ `session_start()` ซ้ำ ใช้ `auth.php` แทน

---

## v2.0 — พฤษภาคม 2569 (Security Overhaul)

### การเปลี่ยนแปลงทั้งหมด

#### Security
- **CSRF Protection** — ครอบทุก form (12 form, 13 processor) ด้วย `csrf.php`
- **MIME Validation** — ตรวจ file upload จาก content จริง ไม่ใช่นามสกุล (รองรับ jpg/png/gif/webp)
- **Credentials** — ย้าย DB + SMTP password ออกจากโค้ดไปไฟล์ `.env`
- **HTTP Status Codes** — processor ใช้ 403/405 แทน echo เมื่อ request ผิดพลาด

#### Database
- **PDO Singleton** — เปิด connection เดียวตลอด request
- **charset=utf8mb4** — รองรับ emoji และภาษาไทยเต็มรูปแบบ
- **Error Logging** — DB connection error บันทึกลง `logs/app.log`

#### Code Quality
- **ImageHelper** (`src/Helper/ImageHelper.php`) — รวม compress/resize/validate ไว้ที่เดียว ลบโค้ดซ้ำออก 5 ไฟล์
- **Logger** (`src/Helper/Logger.php`) — บันทึก INFO/ERROR ลง `logs/app.log`
- **Path** — แก้ DOCUMENT_ROOT path ทั้ง 56 ไฟล์ให้ตรงกับโครงสร้างจริง
- **session_start()** — แก้ bug ใน checkLogin.php ที่เรียกหลัง `$_SESSION` ถูก set

#### Cleanup
- ลบ Linux system directories (bin/dev/etc/lib/logs/tmp/usr/var) ที่ติดมาจาก server
- ลบ `httpdocs/` subfolder ที่ไม่ได้ใช้

---

## v1.0 — มิถุนายน 2567 (เวอร์ชันแรก)

### Tech Stack
| ชั้น | เทคโนโลยี |
|------|-----------|
| Backend | PHP 8.3, PDO (MySQL) |
| Frontend | Bootstrap 5.3, jQuery, AdminLTE 3.2 |
| Email | PHPMailer + Gmail SMTP |
| Image | GD Library + claviska/SimpleImage |
| QR Code | endroid/qr-code, bacon/bacon-qr-code |
| UI Components | DataTables, Chart.js, Summernote, FontAwesome |
| Package Manager | Composer |

### โมดูลหลัก
1. **Auth** — สมัครสมาชิก, Login, Reset Password, Profile
2. **Dashboard** — สถิติ, ตารางสมาชิก, Chart
3. **Activity** — สร้าง/แก้ไข/ลบกิจกรรม, ลงทะเบียน, อนุมัติ
4. **Member** — ข้อมูลละเอียด, QR Code ประจำตัว, รหัส YSDN

---

## แผนถัดไป (v4.0)

- [ ] อัปเกรด AdminLTE 3 → AdminLTE 4 (Bootstrap 5.3, no jQuery)
- [ ] Notification system (real-time แจ้งเตือนเมื่อมีการลงทะเบียนใหม่)
- [ ] API layer (JSON responses สำหรับ mobile)
- [ ] Export รายงานเป็น PDF
