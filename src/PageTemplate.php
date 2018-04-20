<?php
namespace szywo\TinyTweet;

use szywo\TinyTweet\DataContainer;

abstract class PageTemplate
{
    protected $data;

    protected $basePath;

    abstract protected function getTitle();

    abstract protected function getAuxilaryCssFile();

    abstract protected function renderMenuBox();

    abstract protected function renderErrorBox();

    abstract protected function renderFormBox();

    abstract protected function renderContentBox();

    abstract protected function prepareData(DataContainer $dataContainer);

    protected function httpResponseCode()
    {
        http_response_code(200);
    }

    final public function __construct(DataContainer $dataContainer)
    {
        $this->data = $dataContainer;
        $this->basePath = $dataContainer->get('base_path');
        $this->prepareData($dataContainer);
    }

    final public function renderPage()
    {
        include 'views/pageTemplate.html.php';
    }



}
