<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Expander;

use Symfony\Component\Form\FormBuilderInterface;

interface QuicksightUserFormExpanderInterface
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface<string, \Symfony\Component\Form\FormBuilderInterface> $builder
     *
     * @return void
     */
    public function expandForm(FormBuilderInterface $builder): void;
}
