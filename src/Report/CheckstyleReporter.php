<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Report;

use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author David Maciel <jdmaciel@gmail.com>
 *
 * @internal
 */
final class CheckstyleReporter implements ReporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'checkstyle';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportSummary $reportSummary)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('checkstyle');
        $root->setAttribute('version', '1.0.0');
        $dom->appendChild($root);

        foreach ($reportSummary->getChanged() as $file => $fixResult) {
            $fileXML = $dom->createElement('file');
            $fileXML->setAttribute('name', $file);
            $root->appendChild($fileXML);
            if ($reportSummary->shouldAddAppliedFixers()) {
                foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                    $appliedFixerXML = $dom->createElement('error');
                    $appliedFixerXML->setAttribute('severity', 'warning');
                    $appliedFixerXML->setAttribute('source', "PHP-CS-fixer.$appliedFixer");
                    $fileXML->appendChild($appliedFixerXML);
                }
            }
        }

        $dom->formatOutput = true;

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($dom->saveXML()) : $dom->saveXML();
    }
}
