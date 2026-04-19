$csv = 'c:\xampp\htdocs\projects\trackflow\fontawesome_material_500.csv'
$outFile = 'c:\xampp\htdocs\projects\trackflow\temp_icons_js.txt'
$d = Import-Csv $csv
$fa = $d | ForEach-Object { $_.font_awesome } | Where-Object { $_ -and $_ -ne '' } | Sort-Object -Unique
$mi = $d | ForEach-Object { $_.material_icon } | Where-Object { $_ -and $_ -ne '' } | Sort-Object -Unique
$out = @()
foreach ($v in $fa) { $out += "{ type: 'fa', value: '$v' }," }
foreach ($v in $mi) { $out += "{ type: 'mi', value: '$v' }," }
Set-Content -Path $outFile -Value ($out -join "`n")
Write-Host "WROTE $outFile"