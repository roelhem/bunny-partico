<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

final class Color extends Enum
{
    const BLACK = 'black';
    const WHITE = 'white';
    const GRAY = 'gray';
    const RED = 'red';
    const ORANGE = 'orange';
    const YELLOW = 'yellow';
    const BROWN = 'brown';
    const GREEN = 'green';
    const TEAL = 'teal';
    const BLUE = 'blue';
    const INDIGO = 'indigo';
    const PURPLE = 'purple';
    const PINK = 'pink';

    static $default = Color::GRAY;
}
