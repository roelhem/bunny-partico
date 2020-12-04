<?php


namespace App\GraphQL\Fields;

use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Field;


class FormattableDateField extends Field
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
            'format' => [
                'type' => Type::string(),
                'defaultValue' => 'c',
                'description' => 'See the documentation on
                    (datetime::format)[https://www.php.net/manual/en/datetime.format.php] for more information.
                    Defaults to an *ISO 8601* date.',
            ],
            'relative' => [
                'type' => Type::boolean(),
                'defaultValue' => false,
                'description' => 'See the date relative to today.'
            ],
        ];
    }

    protected function resolve($root, $args): ?string
    {
        $date = $root->{$this->getProperty()};

        if(!$date instanceof Carbon) {
            return null;
        }

        if($args['relative']) {
            return $date->diffForHumans();
        }

        return $date->format($args['format']);
    }

    protected function getProperty(): string
    {
        return $this->attributes['alias'] ?? $this->attributes['name'];
    }
}
