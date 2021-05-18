<?php

declare(strict_types=1);

namespace Veronica;

use ArrayObject;

const ENV_TEST = 1;

const ENV_PRO = 2;

const TYPE_EMISSION = 1; // Es una constante revisar pagina 21 tabla 2

const DOC_INVOICE = '01';

const DOC_CREDIT_NOTE = '04';

const STATUS_CREATED = 'CREADO';

const STATUS_AUTHORIZED = 'AUTORIZADO';

const STATUS_NOT_AUTHORIZED = 'NO AUTORIZADO';

const STATUS_IN_PROCESSING = 'EN PROCESAMIENTO';

const STATUS_REJECTED = 'RECHAZADO';

const STATUS_BACK = 'DEVUELTA';

function arr_obj($entries, $options = ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST) {
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

function get_key(...$entries) {

    $key = implode('', $entries);

    $sum = 0;
    foreach (str_split(strrev($key)) as $i => $val) {
        if ($i % 6 === 0) {
            $factor = 2;
        }
        $sum += $val * $factor;
        $factor ++;
    }
    $result = 11 - ($sum % 11);
    if ($result > 9) {
        $result = 11 - $result;
    }

    return $key . $result;
}
