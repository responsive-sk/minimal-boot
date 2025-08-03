# Codecov Setup Instructions

## 1. Nastavení Codecov účtu

1. Jděte na https://codecov.io/
2. Přihlaste se pomocí GitHub účtu
3. Autorizujte Codecov přístup k vašim repositories

## 2. Přidání repository

1. V Codecov dashboardu klikněte na "Add new repository"
2. Najděte a vyberte `responsive-sk/minimal-boot`
3. Codecov automaticky vygeneruje upload token

## 3. Nastavení GitHub Secrets

1. Jděte na GitHub repository: https://github.com/responsive-sk/minimal-boot
2. Klikněte na Settings → Secrets and variables → Actions
3. Klikněte "New repository secret"
4. Přidejte secret:
   - Name: `CODECOV_TOKEN`
   - Value: [token z Codecov dashboardu]

## 4. Ověření nastavení

Po pushnutí kódu se automaticky spustí GitHub Actions workflow, který:
- Spustí testy s coverage
- Uploadne coverage data do Codecov
- Codecov zobrazí coverage report

## 5. Badge do README

Po prvním úspěšném uploadu můžete přidat coverage badge do README.md:

```markdown
[![codecov](https://codecov.io/gh/responsive-sk/minimal-boot/branch/main/graph/badge.svg)](https://codecov.io/gh/responsive-sk/minimal-boot)
```

## 6. Codecov konfigurace

Soubor `codecov.yml` je už nakonfigurován s:
- Target coverage: 80%
- Ignorované adresáře: tests/, var/, public/, bin/, config/
- Automatické komentáře na PR s coverage reporty

## Troubleshooting

Pokud upload nefunguje:
1. Zkontrolujte CODECOV_TOKEN v GitHub Secrets
2. Zkontrolujte logs v GitHub Actions
3. Ověřte, že coverage.xml se generuje správně
