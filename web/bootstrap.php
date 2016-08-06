<?php

require_once "environment.php";

loadIplLibraries([
    "Ipl.Core.Isf",
    "Ipl.Core.Isf2",
    "Ipl.Core.Install",
    "Ipl.Core.Log",
    "Ipl.Core.System",
    "Ipl.Core.Tools"
]);

date_default_timezone_set(TIMEZN);