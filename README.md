# Mall Cleaner

![Mall Cleaner Plugin](https://github.com/hounddd/wn-mallcleaner-plugin/blob/main/.github/MallCleaner-plugin.png?raw=true)

This plugin offers the possibility to clean [OFFLINE.Mall](https://github.com/OFFLINE-GmbH/oc-mall-plugin) plugin data.  

The OFFLINE.Mall plugin generates shopping carts for each visitor to the site, and these can be present in very large numbers if your site is visited by robots.  
Visitors can also simulate orders without paying for them.  
This plugin will clean up your database of these superfluous elements by either :
 - **[Using a command](#mallcleanercommand)**
 - **[Register settings for the OFFLINE.Gdpr](#mallcleanergdprplugin) plugin**

## Installation
*Let assume you're in the root of your wintercms installation*

### Using composer
Just run this command
```bash
composer require hounddd/wn-mallcleaner
```

### Cloning repo
Clone this repo into your winter plugins folder.

```bash
cd plugins
mkdir hounddd && cd hounddd
git clone https://github.com/Hounddd/wn-mallcleaner mallcleaner
```


## Cleanning

### <a name="mallcleanercommand"></a>With command line

This plugin adds an artisan console command to clean up data.

#### **mall:cleaner all**

Run all available cleaners.
```bash
php artisan mall:cleaner all
```
#### **mall:cleaner empty-carts**

Cleans all carts with no products.
```bash
php artisan mall:cleaner empty-carts
```
#### **mall:cleaner unpaid-orders**

Cleans all unpaid orders.
```bash
php artisan mall:cleaner unpaid-orders
```

#### **Command line options**

 - **`--days`** (or `--d`) : The retention period in days to keep data. Default to 120 days. Pass option with a value to specify another retention period, eg: `--days=40` for 40 days. 
 - **`--dry-run`** : Simulate the cleaning process and get information about what is about to be deleted. No data will be deleted

### <a name="mallcleanergdprplugin"></a>With OFFLINE.Gdpr plugin

[OFFLINE.Gdpr](https://github.com/OFFLINE-GmbH/oc-gdpr-plugin) plugin offers data retention functionality enables you to delete old plugin data after a specified amount of days.  
You can specify the data retention policy for each plugin via Winter's backend settings.

#### Cleanup command
You can trigger the Gdpr plugin cleanup on demand via 
```bash
php artisan gdpr:cleanup
```

***
Make awesome sites with [WinterCMS](https://wintercms.com)!