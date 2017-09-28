@echo off

cls



type get.txt > get.js
echo WScript.Echo(getText("http://localhost/Password_expiry_service")); >> get.js

cscript get.js >> temp_file

del get.js
del temp_file

exit





