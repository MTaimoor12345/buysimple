@echo off
:loop
:: /E - Sab sub-folders copy karega (khali folders bhi)
:: /XC - Changed files ko overwrite karega
:: /XN - Newer files ko overwrite karega
:: /XO - Older files ko overwrite karega
:: /XF - Specific Files ko exclude karega (config file)
:: /XD - Specific Folders ko exclude karega (.git, .github aur config folder)

robocopy "C:\xampp\htdocs\haris web" "E:\buysimple" /E /XF "database.php" /XD ".git" ".github" "config"

timeout /t 5
goto loop