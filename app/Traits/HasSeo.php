<?php

namespace App\Traits;

use App\Models\PageSetting;

trait HasSeo
{
    /**
     * Get SEO layout data for a static page by key.
     */
    protected function seo(string $key): array
    {
        $setting = PageSetting::where('page_key', $key)->first();
        
        if (!$setting) {
            return [];
        }

        return array_filter([
            'title' => $setting->meta_title,
            'description' => $setting->meta_description,
            'keywords' => $setting->meta_keywords,
            'canonical' => $setting->canonical_url,
            'jsonSchema' => $setting->json_schema,
        ]);
    }

    /**
     * Get SEO layout data for a specific Eloquent model (BlogPost, PolicyPage, etc.).
     */
    protected function seoForModel($model, ?string $defaultTitle = null, ?string $defaultDescription = null): array
    {
        return array_filter([
            'title' => $model->meta_title ?: $defaultTitle ?: $model->title ?: $model->name ?: null,
            'description' => $model->meta_description ?: $defaultDescription ?: $model->summary ?: $model->short_bio ?: null,
            'keywords' => $model->meta_keywords ?: null,
            'canonical' => $model->canonical_url ?: null,
            'jsonSchema' => $model->json_schema ?? null,
        ]);
    }
}
