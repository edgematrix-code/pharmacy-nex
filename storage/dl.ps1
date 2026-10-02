$dir = 'C:\Users\HP\Herd\vitalis\storage\downloads'
New-Item -ItemType Directory -Force -Path $dir | Out-Null
$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36'
$urls = @(
 'https://nexuspharma.to/wp-content/uploads/2025/12/nexus-pharma-transparent-3-scaled-e1764957533768.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/nexus-pharma-transparent-2-scaled-e1764957830166.png',
 'https://nexuspharma.to/wp-content/uploads/2026/03/photo_2026-03-23_12-21-49-768x845.jpg',
 'https://nexuspharma.to/wp-content/uploads/2025/12/trenbolone-e-200-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/trenbolone-a-100-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/themes/motta/images/empty-bag.svg',
 'https://nexuspharma.to/wp-content/uploads/2025/12/web-image-scaled.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/testosterone-c-250-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/primobolan-e-100-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/masteron-e-200-3-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/masteron-e-200-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/HCG-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/GLOW-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/BPC_TB-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/nexus-labs-white-1-scaled-e1764932786804-768x365.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Test-Cyp-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/04/Test-Report-111771-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Primo-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97109-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Masteron-P-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97105-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2026/08/change-the-blue-top-of-all-the-vials-and-make-it-white-and-make-white-backgroun-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/02/Black-Tops-HGH-300x300.jpeg',
 'https://nexuspharma.to/wp-content/uploads/2026/02/Test-Report-109559-300x300.png',
 'https://nexuspharma.to/wp-content/plugins/motta-addons//assets/images/person.jpg',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Oxandrolone-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/02/oxandrolone-10-1-300x300.jpg'
)
$log = Join-Path $dir 'dl.log'
Set-Content -Path $log -Value 'start'
foreach ($u in $urls) {
  $n = [System.IO.Path]::GetFileName($u)
  $o = Join-Path $dir $n
  try {
    Invoke-WebRequest -Uri $u -OutFile $o -TimeoutSec 45 -UseBasicParsing -UserAgent $ua -Headers @{Referer='https://nexuspharma.to/'} | Out-Null
    Add-Content -Path $log -Value ('OK ' + $n + ' ' + (Get-Item $o).Length)
  } catch {
    Add-Content -Path $log -Value ('FAIL ' + $n + ' :: ' + $_.Exception.Message)
  }
}
Add-Content -Path $log -Value 'DONE'
