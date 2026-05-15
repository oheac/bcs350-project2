$envFile = Get-Content -Path ".\.env"

foreach ($line in $envFile) {
    if ($line -match '=' -and $line -notmatch '^\s*#') {
        $name, $value = $line.Split('=', 2)
        Set-Item -Path "Env:$name" -Value $value
    }
}

php -S 127.0.0.1:8080
