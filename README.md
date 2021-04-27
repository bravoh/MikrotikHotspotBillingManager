# PHP Mikrotik Billing 
----
Features:
----
- Voucher Generator and Print
- Self registration, user must have voucher before registration
- Multi Router Mikrotik
- Hotspot & PPPOE
- Easy Installation
- Multi Language
- New Voucher Layout

TODOS:
----

- SMS Notification to user
- send receipt via SMS or EMAIL
- Social Media Login

Installation
----
Auto Installer
----
Unzip the contents of the zip file to a folder on your computer.
Upload the Entire phpmixbill folder to your website / server
Next you can rename the folder to whatever you like (billing, finance, manage etc..)
Now visit the uploaded location using your web browser to run the installer process.
Follow the instructions on screen to install PHPMixBill
For security, Delete the install directory inside system folder.
If you see blank page after installation, it might be your compiled folder permission is not writable. Please make permission 755 compiled directory inside ui folder to store the generated contents from theme.
if you get error after submit database configuration, create chmod 777 folder system, retry instalation, after finish, chmod 755 folder system


Login Details
----
username: admin
password: 123456

System Requirements
----
Most current web servers with PHP & MySQL installed will be capable of running PHPMixBill

Minimum Requirements
- Linux or Windows OS
- PHP Version 5.3+
- Both PDO & MySQLi Support
- GD2 Image Library
- MySQL Version 4.1.x and above

License
----

GNU General Public License version 2 or later
