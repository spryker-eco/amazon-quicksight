<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Dependency\Facade;

class AmazonQuicksightToTranslatorFacadeBridge implements AmazonQuicksightToTranslatorFacadeInterface
{
    /**
     * @var \Spryker\Zed\Translator\Business\TranslatorFacadeInterface
     */
    protected $translatorFacade;

    /**
     * @param \Spryker\Zed\Translator\Business\TranslatorFacadeInterface $translatorFacade
     */
    public function __construct($translatorFacade)
    {
        $this->translatorFacade = $translatorFacade;
    }

    /**
     * @param string $id
     * @param array<string, mixed> $parameters
     * @param string|null $domain
     * @param string|null $locale
     *
     * @return string
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->translatorFacade->trans($id, $parameters, $domain, $locale);
    }
}
