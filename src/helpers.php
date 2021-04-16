<?php

namespace Veronica;

use ArrayObject;

const ENV_TEST = 1;

const ENV_PRO = 2;

function arr_obj($entries, $options = ArrayObject::ARRAY_AS_PROPS) {
    if ($entries instanceof ArrayObject) {
        return $entries;
    }
    foreach ($entries as $key => $entry){
        if (is_array($entry)) {
            $entries[$key] = arr_obj($entry, $options);
        }
    }

    return new ArrayObject($entries, $options);
}
