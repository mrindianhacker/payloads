if ($env:PSExecutionPolicyPreference -ne 'Bypass') {
    Start-Process powershell -ArgumentList '-NoProfile', '-ExecutionPolicy', 'Bypass', "-File `"$PSCommandPath`"" -WindowStyle Hidden
    exit
}

# Define image path
$pic = [Environment]::GetFolderPath('MyPictures')
$img = "$pic\\joker.jpg"

# Ensure Pictures folder exists
if (-not (Test-Path $pic)) {
    New-Item -Path $pic -ItemType Directory | Out-Null
}

# Download image using .NET WebClient instead of Invoke-WebRequest
$wc = New-Object Net.WebClient
$wc.DownloadFile("https://wallpapercave.com/wp/wp4528934.jpg", $img)

# Set as wallpaper using WScript.Shell COM object
$code = @"
Set WshShell = CreateObject("WScript.Shell")
WshShell.RegWrite "HKCU\Control Panel\Desktop\Wallpaper", "$img", "REG_SZ"
WshShell.Run "RUNDLL32.EXE user32.dll,UpdatePerUserSystemParameters", 1, True
"@

# Save and run the VBScript
$vbPath = "$env:TEMP\\w.vbs"
$code | Out-File -FilePath $vbPath -Encoding ASCII
Start-Process "wscript.exe" -ArgumentList $vbPath -WindowStyle Hidden
