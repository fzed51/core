$oldcomspec = $env:ComSpec
$oldEncoding = $OutputEncoding
$env:ComSpec = $null;
[System.Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$env:TERM = 'ANSI'

phpdbg.exe -qrr .\vendor\kahlan\kahlan\bin\kahlan --coverage --reporter=verbose
 
pause

$env:ComSpec = $oldcomspec
$env:TERM = $null
[System.Console]::OutputEncoding = $oldEncoding