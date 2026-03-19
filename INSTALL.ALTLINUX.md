# Installing Valet Linux+ on ALTLinux

This file contains instructions for installing Valet Linux+ on the ALTLinux operating system (Education, Workstation, and other editions).

## Requirements

- ALTLinux 10.x or newer
- PHP 8.2 or newer
- Composer

## Installation

### 1. Install PHP and Required Extensions

```bash
sudo apt-get install php8.3 php8.3-fpm-fcgi php8.3-mysqlnd php8.3-gd php8.3-zip php8.3-xml php8.3-curl php8.3-mbstring php8.3-pgsql php8.3-intl php8.3-posix
```

Or for PHP 8.2:

```bash
sudo apt-get install php8.2 php8.2-fpm-fcgi php8.2-mysqlnd php8.2-gd php8.2-zip php8.2-xml php8.2-curl php8.2-mbstring php8.2-pgsql php8.2-intl php8.2-posix
```

### 2. Install Composer

If Composer is not yet installed:

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Install Valet Linux+

```bash
composer global require genesisweb/valet-linux-plus
```

### 4. Add Composer Path to PATH

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

To add permanently to your profile:

```bash
echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

### 5. Install Valet

```bash
valet install
```

## Troubleshooting

### Error: SELinux in Enforcing Mode

If you receive an error about SELinux, you can temporarily set it to permissive mode:

```bash
sudo setenforce 0
```

Or add an exception for Valet in SELinux settings.

### Error: PHP Not Found

Make sure PHP is installed and available:

```bash
php -v
```

If multiple PHP versions are installed, you can switch to the desired one:

```bash
valet use php@8.3
```

### Error: Dnsmasq Not Starting

Make sure dnsmasq is installed:

```bash
sudo apt-get install dnsmasq
```

Restart the service:

```bash
valet restart
```

## Usage

After installation, you can use Valet as usual:

```bash
# Park a directory
cd ~/projects
valet park

# Link a project
valet link myproject

# List sites
valet links

# Use specific PHP version
valet use php@8.3
```

## Additional Commands

```bash
# Secure site (HTTPS)
valet secure

# Share local site
valet share

# Check status
valet status
```

## Uninstallation

```bash
valet uninstall
composer global remove genesisweb/valet-linux-plus
```

## Support

- Documentation: https://valetlinux.plus/
- Issues: https://github.com/genesisweb/valet-linux-plus/issues

---

# Установка Valet Linux+ на ALTLinux

Этот файл содержит инструкции по установке Valet Linux+ на операционной системе ALTLinux (Education, Workstation и другие редакции).

## Требования

- ALTLinux 10.x или новее
- PHP 8.2 или новее
- Composer

## Установка

### 1. Установка PHP и необходимых расширений

```bash
sudo apt-get install php8.3 php8.3-fpm-fcgi php8.3-mysqlnd php8.3-gd php8.3-zip php8.3-xml php8.3-curl php8.3-mbstring php8.3-pgsql php8.3-intl php8.3-posix
```

Или для PHP 8.2:

```bash
sudo apt-get install php8.2 php8.2-fpm-fcgi php8.2-mysqlnd php8.2-gd php8.2-zip php8.2-xml php8.2-curl php8.2-mbstring php8.2-pgsql php8.2-intl php8.2-posix
```

### 2. Установка Composer

Если Composer ещё не установлен:

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Установка Valet Linux+

```bash
composer global require genesisweb/valet-linux-plus
```

### 4. Добавление пути Composer в PATH

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

Для постоянного добавления в профиль:

```bash
echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

### 5. Установка Valet

```bash
valet install
```

## Решение проблем

### Ошибка: SELinux в режиме enforcing

Если вы получили ошибку о SELinux, можно временно перевести его в режим предупреждения:

```bash
sudo setenforce 0
```

Или добавить исключение для Valet в настройках SELinux.

### Ошибка: Не найден PHP

Убедитесь, что PHP установлен и доступен:

```bash
php -v
```

Если используется несколько версий PHP, можно переключиться на нужную:

```bash
valet use php@8.3
```

### Ошибка: Dnsmasq не запускается

Убедитесь, что dnsmasq установлен:

```bash
sudo apt-get install dnsmasq
```

Перезапустите службу:

```bash
valet restart
```

## Использование

После установки вы можете использовать Valet как обычно:

```bash
# Парковка директории
cd ~/projects
valet park

# Ссылка на проект
valet link myproject

# Просмотр списка сайтов
valet links

# Использование конкретной версии PHP
valet use php@8.3
```

## Дополнительные команды

```bash
# Защищённый сайт (HTTPS)
valet secure

# Шеринг локального сайта
valet share

# Проверка статуса
valet status
```

## Удаление

```bash
valet uninstall
composer global remove genesisweb/valet-linux-plus
```

## Поддержка

- Документация: https://valetlinux.plus/
- Issues: https://github.com/genesisweb/valet-linux-plus/issues
