<?php

namespace Contao\Framework;

interface DependentBundleInterface
{

    /**
     * Returns the bundle names that this bundle depends upon.
     *
     * @return array An array of bundle names
     */
    public function getDependencies();
}
