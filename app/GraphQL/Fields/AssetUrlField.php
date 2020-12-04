<?php


namespace App\GraphQL\Fields;

use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Field;

class AssetUrlField extends Field
{
    protected $attributes = [
        'description' => 'A field that can output a date in any way you like.',
    ];

    public function __construct(array $settings = [])
    {
        $this->attributes = array_merge($this->attributes, $settings);
    }

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'relative' => [
                'type' => Type::boolean(),
                'defaultValue' => false,
                'description' => 'Show the relative URI-reference.'
            ],
        ];
    }

    protected function resolve($root, $args): ?string
    {
        $path = $root->{$this->getProperty()};

        if(!is_string($path)) {
            return null;
        }

        if($args['relative']) {
            return '/' . $path;
        }

        return config('app.url') . '/' . $path;
    }

    protected function getProperty(): string
    {
        return $this->attributes['alias'] ?? $this->attributes['name'];
    }
}
