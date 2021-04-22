<?php

namespace Veronica\Document;

use XML\Support\Element;

class Contact extends Element
{
    protected $fillable = [
        'name' => 'string',
        'tradename' => 'string',
        'address' => Address::class,
        'identification' => Element::class,
    ];
}
