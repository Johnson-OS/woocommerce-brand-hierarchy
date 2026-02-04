<?php

declare(strict_types=1);

namespace WCBH;

use WCBH\Domain\Brand\BrandTaxonomy;
use WCBH\Domain\Brand\BrandMeta;
use WCBH\Infrastructure\Admin\ElementorCompatibility;
use WCBH\Infrastructure\Admin\ProductBrandFilter;
use WCBH\Infrastructure\Admin\ProductBrandSortable;
use WCBH\Infrastructure\Assets\Assets;
use WCBH\Infrastructure\Rewrite\BrandRewrite;
use WCBH\Infrastructure\QueryVars\QueryVars;
use WCBH\Presentation\TemplateLoader;
use WCBH\Infrastructure\Query\BrandProductQuery;
use WCBH\Presentation\Breadcrumbs;
use WCBH\Infrastructure\Import\BrandCsvImport;
use WCBH\Infrastructure\Admin\ProductTableBrandColumn;

final class Plugin
{
    public static function init(): void
    {
        (new ElementorCompatibility())->register();
        (new BrandTaxonomy())->register();
        (new BrandMeta())->register();

        (new QueryVars())->register();
        (new BrandRewrite())->register();

        (new TemplateLoader())->register();
        (new BrandProductQuery())->register();
        (new Breadcrumbs())->register();
        (new BrandCsvImport())->register();
        (new Assets())->register();
        (new ProductTableBrandColumn())->register();
        (new ProductBrandFilter())->register();
        (new ProductBrandSortable())->register();

    }

    public static function activate(): void
    {
        self::init();
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}
