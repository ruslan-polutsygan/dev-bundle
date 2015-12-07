<?php

namespace RP\DevBundle\Test\Phpunit\Extension;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

trait SonataAdminExtensionTrait
{
    public function getSonataAdminFormName(Form $form)
    {
        parse_str(parse_url($form->getUri(), PHP_URL_QUERY), $query);

        return $query['uniqid'];
    }

    public function tickSonataAdminFormCheckbox(Form $form, $field)
    {
        $formName = $this->getSonataAdminFormName($form);

        /* @var \Symfony\Component\DomCrawler\Field\ChoiceFormField[] $form */
        $form[$formName.$field]->tick();
    }

    public function selectSonataAdminFormOption(Form $form, $field, $value)
    {
        $formName = $this->getSonataAdminFormName($form);

        /* @var \Symfony\Component\DomCrawler\Field\ChoiceFormField[] $form */
        $form[$formName.$field]->select($value);
    }

    public function uploadSonataAdminFormFile(Form $form, $field, $file)
    {
        $formName = $this->getSonataAdminFormName($form);

        /* @var \Symfony\Component\DomCrawler\Field\FileFormField[] $form */
        $form[$formName.$field]->upload($file);
    }

    public function setSonataAdminFormFieldValues(Form $form, array $values)
    {
        $formName = $this->getSonataAdminFormName($form);
        $valuesWithFormName = array();
        foreach ($values as $name => $value) {
            $valuesWithFormName[$formName.'['.$name.']'] = $value;
        }

        $form->setValues($valuesWithFormName);
    }

    public function getSonataAdminFormFieldValue(Form $form, $field)
    {
        $formName = $this->getSonataAdminFormName($form);

        return $form->get($formName.'['.$field.']')->getValue();
    }

    public function assertSonataAdminFlashErrorMessageContains($text)
    {
        $crawler = new Crawler(static::$client->getResponse()->getContent());

        $content = '';
        if ($crawler->filter('.alert.alert-danger.alert-dismissable')->count() == 1) {
            $content = trim($crawler->filter('.alert.alert-danger.alert-dismissable')->text());
        }

        $this->assertContains($text, $content);
    }

    public function assertSonataAdminFlashSuccessMessageContains($text)
    {
        $crawler = new Crawler(static::$client->getResponse()->getContent());

        $content = '';
        if ($crawler->filter('.alert.alert-success.alert-dismissable')->count() == 1) {
            $content = trim($crawler->filter('.alert.alert-success.alert-dismissable')->text());
        }

        $this->assertContains($text, $content);
    }
}
