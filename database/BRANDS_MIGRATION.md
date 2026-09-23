# Brands migration

Run before using Brands in the admin dashboard:

```sh
php database/migrate_brands.php
```

On XAMPP Windows:

```powershell
C:\xampp\php\php.exe database\migrate_brands.php
```

For hosting, run from the project directory with DB_HOST, DB_NAME, DB_USER and DB_PASS environment variables set for the target database. The CLI defaults to the local database. Back up the target database before deployment.

The migration is repeatable: it creates brands, adds nullable products.brand_id, an index, and a foreign key. Existing products retain NULL (no brand). No product data is rewritten or removed. Deploy the updated PHP and JavaScript files together. Before migration, existing listings and product forms continue to work without brands.

Admins with product-view permission can view Brands; product-create permission allows creating brands. A name is required, images are optional (JPG/PNG/WebP up to 5 MB). Assign one brand or No brand in Add/Edit Product. Customers can select multiple brands, combined with existing category/search/price filters; pagination retains the selection.
