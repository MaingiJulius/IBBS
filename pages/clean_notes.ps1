Get-ChildItem -Path "." -Filter "*.php" | ForEach-Object {
    $filePath = $_.FullName
    $fileName = $_.Name
    $content = [System.IO.File]::ReadAllText($filePath)

    # Remove /* Note: ... was removed ... */ comment blocks (single and multi-line)
    $content = [regex]::Replace($content, '\s*/\* Note:[^*]*\*/', '', [System.Text.RegularExpressions.RegexOptions]::Singleline)

    # Replace OOP $conn->close() with procedural mysqli_close($conn)
    $content = $content -replace '\$conn->close\(\)', 'mysqli_close($conn)'

    [System.IO.File]::WriteAllText($filePath, $content)
    Write-Host "Cleaned: $fileName"
}
Write-Host "Done."
