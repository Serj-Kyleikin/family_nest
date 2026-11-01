<?php

namespace Database\Seeders;

use App\Models\Expenses\{
    ExpenseTemplate,
    ExpenseTemplateGroup,
};
use Illuminate\Database\Seeder;
use Symfony\Component\Yaml\Yaml;

class ExpenseTemplateGroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        $files = glob(database_path('seeders/data/*.yaml'));

        sort($files);

        foreach ($files as $file) {

            $category = Yaml::parseFile($file);

            $group = ExpenseTemplateGroup::firstOrCreate(
                [
                    'user_id'   => null,
                    'name'      => $category['name'],
                ]
            );

            foreach ($category['templates'] as $name) {

                ExpenseTemplate::firstOrCreate(
                    [
                        'template_group_id' => $group->id,
                        'name'              => $name,
                    ],
                    [
                        'is_active'         => true,
                    ]
                );
            }
        }
    }
}
