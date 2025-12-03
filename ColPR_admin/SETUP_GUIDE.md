# ColPR Admin Setup Guide for XAMPP

## Problem
You're getting "Not Found" error when accessing `http://localhost/ColPR_admin/index.php` because Apache's DocumentRoot doesn't point to your `E:\final` folder.

## Solution Options

### Option 1: Create Symbolic Link (Recommended - No File Copying)
This creates a link in htdocs that points to your actual folder.

1. Open PowerShell as Administrator
2. Run this command:
```powershell
New-Item -ItemType SymbolicLink -Path "C:\xampp\htdocs\ColPR_admin" -Target "E:\final\ColPR_admin"
```

3. Access your application at: `http://localhost/ColPR_admin/index.php`

### Option 2: Copy Folder to htdocs
1. Copy the entire `ColPR_admin` folder to `C:\xampp\htdocs\`
2. Access at: `http://localhost/ColPR_admin/index.php`

### Option 3: Configure Virtual Host (Advanced)
1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add this configuration:
```apache
<VirtualHost *:80>
    DocumentRoot "E:/final"
    ServerName localhost
    <Directory "E:/final">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
3. Make sure `httpd.conf` has this line uncommented:
```apache
Include conf/extra/httpd-vhosts.conf
```
4. Restart Apache

### Option 4: Change DocumentRoot (Not Recommended)
1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find the line: `DocumentRoot "C:/xampp/htdocs"`
3. Change it to: `DocumentRoot "E:/final"`
4. Find: `<Directory "C:/xampp/htdocs">`
5. Change it to: `<Directory "E:/final">`
6. Restart Apache

## After Setup
1. Make sure Apache and MySQL are running in XAMPP Control Panel
2. Ensure your database `glaxit_chatbot` is created and configured
3. Access: `http://localhost/ColPR_admin/index.php` or `http://localhost/ColPR_admin/login.php`

## Troubleshooting
- If you still get "Not Found", check Apache error logs: `C:\xampp\apache\logs\error.log`
- Make sure the folder name matches exactly: `ColPR_admin` (case-sensitive in URLs)
- Try accessing: `http://localhost/ColPR_admin/` (without index.php)


