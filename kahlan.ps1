Param(
    [switch]$coverage
)

$oldcomspec = $env:ComSpec
$oldEncoding = $OutputEncoding
$env:ComSpec = $null;
[System.Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$env:TERM = 'ANSI'

if($coverage){
    phpdbg.exe -qrr .\vendor\kahlan\kahlan\bin\kahlan --coverage --reporter=verbose
}else{
    php.exe -qrr .\vendor\kahlan\kahlan\bin\kahlan --reporter=verbose
}
 
pause

$env:ComSpec = $oldcomspec
$env:TERM = $null
[System.Console]::OutputEncoding = $oldEncoding