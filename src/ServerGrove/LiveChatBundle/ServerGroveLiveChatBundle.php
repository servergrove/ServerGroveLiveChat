<?php

namespace ServerGrove\SGLiveChatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SGLiveChatBundle extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }

}
