<?php

namespace {{ namespace }}\Controller{{ entity_namespace ? '\\' ~ entity_namespace : '' }};

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
{% if 'annotation' == format -%}
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
{%- endif %}


class {{ entity_class }}Controller extends Controller
{
    {%- set target = 'annotation' == format ? 'annotation' : 'others' -%}

    {%- include dir ~ '/actions/' ~ target ~ '/index.php' -%}
}
