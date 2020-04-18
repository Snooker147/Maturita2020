<?php

// no ErrorHandler required: Only interface definition

interface IResource
{

    /** A function called new build or after init */
    function finalizeBuild(bool $build): void;

    /**
     * Calls when the resource should been built
     */
    function build(): void;

    /**
     * Gets the unique name of this resource
     */
    function getResourceName(): string;


}


?>