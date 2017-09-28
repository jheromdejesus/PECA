@echo off

cls



type "C:\wamp\www\pecanuvem\scheduler\get.txt" > "C:\wamp\www\pecanuvem\scheduler\get.js"
echo WScript.Echo(getText("http://localhost/suspension_service")); >> "C:\wamp\www\pecanuvem\scheduler\get.js"

C:\Windows\System32\cscript "C:\wamp\www\pecanuvem\scheduler\get.js" >> "C:\wamp\www\pecanuvem\scheduler\temp_file"

del "C:\wamp\www\pecanuvem\scheduler\get.js"
del "C:\wamp\www\pecanuvem\scheduler\temp_file"

exit





