@echo off

cls



type "C:\wamp\www\PECA\scheduler\get.txt" > "C:\wamp\www\PECA\scheduler\get.js"
echo WScript.Echo(getText("http://localhost/replicationClient/replicateMain")); >> "C:\wamp\www\PECA\scheduler\get.js"

C:\Windows\System32\cscript "C:\wamp\www\PECA\scheduler\get.js" >> "C:\wamp\www\PECA\scheduler\temp_file"

del "C:\wamp\www\PECA\scheduler\get.js"
del "C:\wamp\www\PECA\scheduler\temp_file"

exit









