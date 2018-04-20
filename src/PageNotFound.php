<?php

namespace szywo\TinyTweet;

use szywo\TinyTweet\DataContainer;

class PageNotFound extends PageAbstract
{
    private $requestUri;

    protected function getAuxilaryCssFile()
    {
        return "css/pageNotFound.css";
    }

    protected function httpResponseCode()
    {
        http_response_code(404);
    }

    protected function getTitle()
    {
        return "Error 404 - Oops!";
    }

    protected function prepareData(DataContainer $dataContainer)
    {
        $this->requestUri = htmlentities($dataContainer->get('request_uri'), ENT_QUOTES|ENT_HTML5);
    }

    protected function renderMenuBox()
    {
        // as for now we dont render any menu
    }

    protected function renderErrorBox()
    {
        include 'views/pageNotFound.html.php';
    }


    protected function renderFormBox()
    {
        // do not render any form also
    }

    protected function renderContentBox()
    {
        // nor content
    }

}
