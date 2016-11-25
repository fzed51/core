Param(
    [switch]$coverage,
    [string]$reporter = "verbose"
)

$oldcomspec = $env:ComSpec
$oldEncoding = $OutputEncoding
$env:ComSpec = $null;
[System.Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$env:TERM = 'ANSI'

if($coverage){
    phpdbg.exe -qrr .\vendor\kahlan\kahlan\bin\kahlan --coverage --reporter=$reporter
}else{
    php.exe .\vendor\kahlan\kahlan\bin\kahlan --reporter=$reporter
}

$env:ComSpec = $oldcomspec
$env:TERM = $null
[System.Console]::OutputEncoding = $oldEncoding