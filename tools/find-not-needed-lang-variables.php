<?php
/*
  Detect language variables that aren't needed anymore.
  IMPORTANT: We only use heuristics to determine which variables are needed and which aren't.
  You should always manually check if a particular variable is really unused.
  
  This script should be run from the CLI.
*/

define('SRC_DIR', dirname(dirname(__FILE__)) . '/src/');

$ignoredPaths = [
    SRC_DIR . 'config/',
    SRC_DIR . 'data/',
    SRC_DIR . 'setup/',
    SRC_DIR . 'temp/',

    // This is very important - otherwise, our script does not detect any unused language.
    SRC_DIR . 'serverlib/languages/',
];

$fileContents = [];

function findFilesRec($folderPath): void {
    global $ignoredPaths;

    if (substr($folderPath, -1) !== '/') {
        exit(
            'Seems like the folder path does not end on "/". This should not happen.'
        );
    }
    if (!is_dir($folderPath)) {
        exit(
            'Seems like the folder path does not exist: " ' . $folderPath . ' "'
        );
    }

    if (in_array($folderPath, $ignoredPaths)) {
        return;
    }

    $children = glob($folderPath . '*');
    foreach ($children as $childPath) {
        if (is_file($childPath)) {
            processFile($childPath);
            continue;
        }
        findFilesRec($childPath . '/');
    }
}

function processFile($filePath): void {
    global $fileContents, $ignoredPaths;

    if (in_array($filePath, $ignoredPaths)) {
        return;
    }

    $fileContent = file_get_contents($filePath);

    if (isProbablyBinaryFile($fileContent)) {
        return;
    }

    $fileContents[] = $fileContent;
}

function isProbablyBinaryFile($fileContent): bool {
    if (str_contains($fileContent, "\0")) {
        return true;
    }
    return false;
}

findFilesRec(SRC_DIR);

require_once SRC_DIR . 'serverlib/languages/deutsch.lang.php';

// We ignore lang_custom, because that is e.g. used in the SystemMail function.
echo "Ignoring lang_custom\n";

$langArrayNames = ['lang_user', 'lang_admin', 'lang_client'];
foreach ($langArrayNames as $langArrayName) {
    foreach ($$langArrayName as $langKey => $langValue) {
        printLangIfNotNeeded($langArrayName, $langKey);
    }
}

function printLangIfNotNeeded($langType, $langKey): void {
    global $fileContents;

    // Some strings are loaded in a "dynamic" way, e.g. $lang_admin['text_' . $key]
    $dynamicPrefixes = ['text_', 'stat_', 'cert_err', 'cert_caerr'];
    foreach ($dynamicPrefixes as $prefix) {
        if (str_starts_with($langKey, $prefix)) {
            return;
        }
    }

    $templateItem = '{lng p="' . $langKey . '"}';
    $jsItem = "lang['{$langKey}']";
    $inlineUseItem = "\${$langType}['{$langKey}']";

    foreach ($fileContents as $fileContent) {
        if (
            str_contains($fileContent, $templateItem) ||
            str_contains($fileContent, $jsItem) ||
            str_contains($fileContent, $inlineUseItem)
        ) {
            return;
        }
    }

    echo "\${$langType}['{$langKey}']\n";
}
