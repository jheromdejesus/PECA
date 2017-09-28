@echo off

cls



type get.txt > get.js
echo WScript.Echo(getText("http://localhost/Onlinetransaction_notification_service/sendEmailNotification")); >> get.js

cscript get.js >> temp_file

del get.js
del temp_file

exit





