# Maturitní projekt 2020
Projekt byl vypracován pro maturitní obhajobu 2020.

### Zadání projektu
Cílem bylo vypracovat školní webovou stránku s redakčním systémem pomocí PHP, HTML5, JavaScriptu a Databáze MySQL. Předloha pro projekt byla dosavadní stránka školy http://nosch.cz

### Licence
Projekt byl vypracován za předpokladu, že dosavadní stránka http://nosch.cz vlastní všechna práva na obsah. Kód tohoto projektu je otevřený (Open Source) a škola jej může využít pro jakékoliv další účely. Na tento projekt se tedy nevztahuje žádná standartní licence.

### Obsah
Veškerý obsah byl vypracován z předlohy ze stránky http://nosch.cz nebo vytvořen tak, aby na ní navazoval.

### Dokumentace
Pro obsáhlejší dokumentaci si prohlédněte dokument README.docx

# Využité technologie
- Composer (https://getcomposer.org v1.10+)
- NodeJS (https://nodejs.org/ v12+)
- MySQL (https://www.mysql.com/)
- PHP 7+ (https://www.php.net/)
- Symfony (https://symfony.com/)

# Nástroje
Projekt byl vypracován pomocí nástroje XAMPP (https://www.apachefriends.org/index.html). V root složce je soubor .htaccess, který ukazuje základní nastavení Apache PHP, nicméně jakýkoliv další PHP a web provider je možné použít, jenom je nutné jakékoliv dotazy webu referovat na soubor index.php (web je stavěn na URL adresách, o které se stará index.php). Z výukových důvodů nebyl použit žádný PHP framework tak, aby bylo co nejvíce kódu napsáno "z hlavy".
Při použití XAMPP nejsou nutné žádné další nastavení, projekt by měl fungovat bez žádných problémů. Pro lokální vývoj je nutné mít nainstalované další aplikace.

# Lokální vývoj
Nainstalujte si aplikace:
- Composer (https://getcomposer.org v1.10+)
- NodeJS (https://nodejs.org/ v12+)
- XAMPP (https://www.apachefriends.org/index.html)

1. Otevřte příkazový řádek ve složce "vendor" a spusťte příkaz: 
- `composer install`
2. Otevřete příkazový řádek ve složce "webpack" a spusťte příkaz:
- `npm install`
- `npm start`
3. Otevřete XAMPP a zapněte:
- Apache
- MySQL
4. Zkopírujte konfigurační soubor "config.template.json" do stejné složky pod názvem "config.json"
- Pro lokální vývoj je konfigurační soubor již připraven, pro deploy na release je nutné změnit "mode" na "release" a upravit některé nastavení. Pro větší referenci použijte soubor README.docx

Na adrese http://localhost (default) se Vám otevře webová stránka a jste připraveni dělat lokální změny.

# Adresáře
- webpack/src
	- CSS (SCSS), JavaScript (TypeScript)
- vendor/src
	- PHP

# Chyby
Chyby při vývojí se zobrazují na URL adrese "/error" při lokálním vývoji se také ukládájí do adresáře "vendor/errors/last_development" a při release "vendor/errors/error_datum". Chyby v CSS a JS jsou zobrazovány v konzoli s příkazem "npm start"

# Podrobnější dokumentace
Náhledněte do souboru README.docx.

# Podpora
Na maturitním projektu budu pracovat do konce maturity (květen, červen) jakýkoliv další vývoj ještě nemůžu předpovědět. Projekt je momentálně ve fázi dokončení a byl odevzán vyučujícímu, nicméně pořád probíhají konzultace a opravy drobných chyb.

# TODO
- Docker (snad brzy)
- Podrobnější PHP dokumentace