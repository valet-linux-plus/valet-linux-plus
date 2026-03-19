# Pull Request / Issue Description

## English Version

### Title
**feat: Add ALTLinux distribution support**

### Summary
This PR adds full support for ALTLinux (Education, Workstation, and other editions) - a Russian Linux distribution based on ALT Linux.

### Changes

#### New Files
- `cli/Valet/PackageManagers/AltLinux.php` - New package manager class for ALTLinux
- `INSTALL.ALTLINUX.md` - Detailed installation instructions (bilingual: English/Russian)

#### Modified Files
- `cli/Valet/Contracts/PackageManager.php` - Added new interface methods
- `cli/Valet/PackageManagers/*.php` - Implemented new methods in all package managers
- `cli/Valet/PhpFpm.php` - Updated to use distro-specific methods
- `cli/Valet/Mysql.php` - Updated extension installation
- `cli/Valet/ServiceManagers/Systemd.php` - Fixed service detection
- `cli/Valet/Valet.php` - Added AltLinux to detection list
- `composer.json` - Added 'altlinux' keyword
- `README.md` - Added ALTLinux to supported distributions

### Technical Details

**ALTLinux Package Manager Features:**
- PHP FPM packages: `php8.x-fpm-fcgi` naming convention
- PHP extensions: `php8.x-extension` (without dash prefix)
- MariaDB as default MySQL implementation
- CA certificates path: `/etc/pki/ca-trust/extracted/pem`
- Systemd service names: `php8.x-fpm`

**New Interface Methods:**
- `getPhpExtensionName(string $version, string $extension): string` - Returns distro-specific PHP extension package name
- `getPhpFpmServiceName(string $version): string` - Returns systemd service name for PHP-FPM

### Testing
Tested successfully on **ALTLinux Education 11.0 (FalcoVespertinus)** with the following services:
- ✅ Nginx
- ✅ PHP 8.3 FPM
- ✅ Dnsmasq
- ✅ Redis
- ✅ MariaDB
- ✅ Mailpit

### Installation Commands for ALTLinux
```bash
sudo apt-get install php8.3 php8.3-fpm-fcgi php8.3-mysqlnd php8.3-gd php8.3-zip php8.3-xml php8.3-curl php8.3-mbstring php8.3-pgsql php8.3-intl php8.3-posix
composer global require genesisweb/valet-linux-plus
valet install
```

---

## Русская версия

### Заголовок
**feat: Добавлена поддержка дистрибутива ALTLinux**

### Краткое описание
Этот PR добавляет полную поддержку операционной системы ALTLinux (Education, Workstation и другие редакции) - российского Linux-дистрибутива на базе ALT Linux.

### Изменения

#### Новые файлы
- `cli/Valet/PackageManagers/AltLinux.php` - Новый класс менеджера пакетов для ALTLinux
- `INSTALL.ALTLINUX.md` - Подробная инструкция по установке (двуязычная: английский/русский)

#### Изменённые файлы
- `cli/Valet/Contracts/PackageManager.php` - Добавлены новые методы интерфейса
- `cli/Valet/PackageManagers/*.php` - Реализованы новые методы во всех менеджерах пакетов
- `cli/Valet/PhpFpm.php` - Обновлён для использования специфичных для дистрибутива методов
- `cli/Valet/Mysql.php` - Обновлена установка расширений
- `cli/Valet/ServiceManagers/Systemd.php` - Исправлено обнаружение служб
- `cli/Valet/Valet.php` - Добавлен AltLinux в список обнаружения
- `composer.json` - Добавлено ключевое слово 'altlinux'
- `README.md` - Добавлен ALTLinux в список поддерживаемых дистрибутивов

### Технические детали

**Особенности менеджера пакетов ALTLinux:**
- Пакеты PHP FPM: именование `php8.x-fpm-fcgi`
- Расширения PHP: `php8.x-extension` (без дефиса)
- MariaDB как реализация MySQL по умолчанию
- Путь к CA-сертификатам: `/etc/pki/ca-trust/extracted/pem`
- Имена служб systemd: `php8.x-fpm`

**Новые методы интерфейса:**
- `getPhpExtensionName(string $version, string $extension): string` - Возвращает специфичное для дистрибутива имя пакета расширения PHP
- `getPhpFpmServiceName(string $version): string` - Возвращает имя службы systemd для PHP-FPM

### Тестирование
Успешно протестировано на **ALTLinux Education 11.0 (FalcoVespertinus)** со следующими службами:
- ✅ Nginx
- ✅ PHP 8.3 FPM
- ✅ Dnsmasq
- ✅ Redis
- ✅ MariaDB
- ✅ Mailpit

### Команды установки для ALTLinux
```bash
sudo apt-get install php8.3 php8.3-fpm-fcgi php8.3-mysqlnd php8.3-gd php8.3-zip php8.3-xml php8.3-curl php8.3-mbstring php8.3-pgsql php8.3-intl php8.3-posix
composer global require genesisweb/valet-linux-plus
valet install
```

---

## Statistics / Статистика

| Metric / Метрика | Value |
|-----------------|-------|
| Files changed / Изменено файлов | 16 |
| Insertions / Добавлено строк | 618 |
| Deletions / Удалено строк | 12 |
| New package manager / Новый менеджер пакетов | AltLinux |
| Supported PHP versions / Поддерживаемые версии PHP | 8.1, 8.2, 8.3, 8.4 |

---

## Related Issues / Связанные вопросы

This PR addresses the request for ALTLinux support / Этот PR отвечает на запрос о поддержке ALTLinux.

## Checklist / Контрольный список

- [x] Code follows project conventions / Код следует соглашениям проекта
- [x] All package managers updated / Все менеджеры пакетов обновлены
- [x] Documentation added / Документация добавлена
- [x] Tested on real hardware / Протестировано на реальном оборудовании
- [x] Bilingual documentation / Двуязычная документация

---


